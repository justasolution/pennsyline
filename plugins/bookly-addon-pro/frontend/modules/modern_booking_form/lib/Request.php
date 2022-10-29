<?php
namespace BooklyPro\Frontend\Modules\ModernBookingForm\Lib;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Lib\Steps;
use Bookly\Frontend\Components\Booking;
use Bookly\Frontend\Modules\ModernBookingForm\Proxy;
use Bookly\Lib\Entities\Payment;

/**
 * Class Request
 *
 * @package BooklyPro\Frontend\Modules\ModernBookingForm\Lib
 */
class Request extends BooklyLib\Base\Component
{
    const BOOKING_STATUS_COMPLETED                  = 'completed';
    const BOOKING_STATUS_GROUP_SKIP_PAYMENT         = 'group_skip_payment';
    const BOOKING_STATUS_PAYMENT_IMPOSSIBLE         = 'payment_impossible';
    const BOOKING_STATUS_APPOINTMENTS_LIMIT_REACHED = 'appointments_limit_reached';

    /** @var array */
    protected $customer = array();
    /** @var array */
    protected $notices = array();
    /** @var string */
    protected $step = 'details';
    /** @var string self::BOOKING_STATUS_* */
    protected $booking_status;
    /** @var array */
    protected $data = array();
    /** @var BooklyLib\UserBookingData */
    protected $userData;
    /** @var BooklyLib\CartInfo */
    protected $cart_info;
    /** @var string */
    protected $type; // appointment || package
    /** @var Payment */
    protected $payment;
    /** @var string */
    protected $gateway;

    public function __construct()
    {
        $this->customer = self::parameter( 'customer' );
        $this->type = self::parameter( 'type' );
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $this->notices = array();

        if ( $this->customer['email'] === '' || ! is_email( $this->customer['email'] ) ) {
            $this->notices[] = 'email';
        }

        foreach ( array( 'phone', 'first_name', 'last_name' ) as $field ) {
            if ( $this->customer[ $field ] === '' ) {
                $this->notices[] = $field;
            }
        }
        if ( $this->notices ) {
            return false;
        }

        if ( $this->isAppointment() && $this->getUserData()->cart->getFailedKey() !== null ) {
            $this->step = 'slots';
            $this->notices[] = 'slot_not_available';

            return false;
        }

        if ( $this->getGateway() == '' ) {
            if ( BooklyLib\Config::paymentStepDisabled() ) {
                $this->step = 'done';
                $this->setBookingStatus( self::BOOKING_STATUS_COMPLETED );

                return true;
            }

            if ( $this->isReachedAppointmentsLimit() ) {
                $this->step = 'done';
                $this->setBookingStatus( self::BOOKING_STATUS_APPOINTMENTS_LIMIT_REACHED );

                return false;
            }

            if ( BooklyLib\Proxy\CustomerGroups::getSkipPayment( $this->getUserData()->getCustomer() ) ) {
                $this->step = 'done';
                $this->setBookingStatus( self::BOOKING_STATUS_GROUP_SKIP_PAYMENT );

                return false;
            }

            $pay_now = $this->getUserData()->cart->getInfo()->getPayNow();
            if ( $pay_now > 0 ) {
                $gateways = PaymentFlow::getAllowedGateways( $this->getUserData() );
                if ( $gateways ) {
                    $payment_gateways = array();
                    foreach ( PaymentFlow::orderGateways( $gateways ) as $type ) {
                        $payment_gateways[] = array( 'type' => $type, 'image' => Payment::typeToImage( $type ) );
                    }
                    if ( count( $payment_gateways ) === 1 && $payment_gateways[0]['type'] === Payment::TYPE_LOCAL ) {
                        $this->setGateway( Payment::TYPE_LOCAL );

                        return true;
                    } else {
                        $this->step = 'payment';
                        $this->data = compact( 'payment_gateways' );
                    }
                } else {
                    $this->step = 'done';
                    $this->setBookingStatus( self::BOOKING_STATUS_PAYMENT_IMPOSSIBLE );
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getError()
    {
        $result = array(
            'step' => $this->step,
            'data' => $this->data,
        );
        if ( $this->notices ) {
            $result['data']['notices'] = $this->notices;
        }
        if ( $this->booking_status ) {
            $result['status'] = $this->booking_status;
        }

        return $result;
    }

    /**
     * Get staff id
     *
     * @return int
     */
    public function getStaffId()
    {
        return self::parameter( 'staff_id' );
    }

    /**
     * Get location id
     *
     * @return int
     */
    public function getLocationId()
    {
        return self::parameter( 'location_id' );
    }

    /**
     * Get service id
     *
     * @return int
     */
    public function getServiceId()
    {
        return self::parameter( 'service_id' );
    }

    /**
     * Get customer data
     *
     * @return array
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Process payment
     *
     * @return array
     * @throws \Exception
     */
    public function processPayment()
    {
        if ( BooklyLib\Config::paymentStepDisabled() ) {
            return $this->createAppointment( false );
        }
        if ( $this->getGateway() === BooklyLib\Entities\Payment::TYPE_LOCAL
            || $this->getCartInfo()->getPayNow() == 0
        ) {
            return $this->createAppointment( true );
        }
        try {
            $response_url = add_query_arg( array( 'action' => 'bookly_pro_checkout_response' ), admin_url( 'admin-ajax.php' ) );
            switch ( $this->getGateway() ) {
                case BooklyLib\Entities\Payment::TYPE_CLOUD_STRIPE;
                    $redirect_url = $this->getCheckoutUrlForCloudStripe( $response_url );
                    break;
                default:
                    $redirect_url = '';
            }
        } catch ( \Exception $e ) {
            $this->step = 'payment';
            $this->data = array(
                'error' => $e->getMessage(),
            );
            throw $e;
        }

        $payment = $this->getPayment()->getToken();

        return compact( 'redirect_url', 'payment' );
    }

    /**
     * @return bool
     */
    protected function isAppointment()
    {
        return $this->type === 'appointment';
    }

    /**
     * @return BooklyLib\UserBookingData
     */
    protected function getUserData()
    {
        if ( $this->userData === null ) {
            $this->userData = new BooklyLib\UserBookingData( null );
            $service_id = $this->getServiceId();
            $staff_id = $this->getStaffId();
            $location_id = $this->getLocationId();
            $nop = self::parameter( 'nop' );
            $units = self::parameter( 'units', 1 );
            $extras = self::parameter( 'extras', array() );
            foreach ( array_keys( $extras, 0, false ) as $key ) {
                unset( $extras[ $key ] );
            }
            $slot = $this->isAppointment()
                ? self::parameter( 'slot' )
                : array( 'value' => sprintf( '[[%d,%d,null,0]]', $service_id, $staff_id ) );
            $slots = json_decode( $slot['value'], true );

            $this->userData
                ->setFirstName( $this->customer['first_name'] )
                ->setLastName( $this->customer['last_name'] )
                ->setEmail( $this->customer['email'] )
                ->setPhone( isset( $this->customer['phone_formatted'] ) ? $this->customer['phone_formatted'] : $this->customer['phone'] )
                ->setSlots( $slots );

            $cart_item = new BooklyLib\CartItem();
            $cart_item
                ->setStaffIds( array( $staff_id ) )
                ->setServiceId( $service_id )
                ->setNumberOfPersons( $nop )
                ->setLocationId( BooklyLib\Proxy\Locations::prepareStaffLocationId( $location_id, $staff_id ) )
                ->setUnits( $units )
                ->setExtras( $extras )
                ->setSlots( $slots );

            $this->userData->cart->add( $cart_item );
        }

        return $this->userData;
    }

    /**
     * @return BooklyLib\CartInfo
     */
    protected function getCartInfo()
    {
        if ( $this->cart_info === null ) {
            $this->cart_info = $this->getUserData()->cart->getInfo( $this->getGateway() );
        }

        return $this->cart_info;
    }

    /**
     * Get payment system
     *
     * @return string
     */
    protected function getGateway()
    {
        return $this->gateway ?: self::parameter( 'gateway' );
    }

    /**
     * @param string $gateway
     * @return void
     */
    protected function setGateway( $gateway )
    {
        $this->gateway = $gateway;
    }

    /**
     * Create payment
     *
     * @return Payment
     */
    protected function createPayment()
    {
        $this->payment = new Payment();
        $cart_info = $this->getCartInfo();
        if ( $cart_info->getPayNow() > 0 ) {
            $this->payment
                ->setType( $this->getGateway() )
                ->setStatus( Payment::STATUS_PENDING );
        } else {
            $this->payment
                ->setType( Payment::TYPE_FREE )
                ->setStatus( Payment::STATUS_COMPLETED );
        }
        if ( $this->payment->getType() === Payment::TYPE_LOCAL
            || $this->payment->getType() === Payment::TYPE_FREE
        ) {
            $this->payment
                ->setPaidType( Payment::PAY_IN_FULL )
                ->setTotal( $cart_info->getTotal() )
                ->setTax( $cart_info->getTotalTax() )
                ->setPaid( 0 );
        } else {
            $this->payment
                ->setCartInfo( $cart_info );
        }
        $this->payment->save();

        return $this->payment;
    }

    /**
     * Create appointment or package with local payment
     *
     * @return array
     */
    protected function createAppointment( $with_local_payment = true )
    {
        $payment = $with_local_payment
            ? $this->createPayment()
            : null;
        $status = PaymentFlow::STATUS_COMPLETED;
        $data = array();
        if ( $this->isAppointment() ) {
            $order = $this->getUserData()->save( $payment );
            $payment && $payment->setDetailsFromOrder( $order, $this->getCartInfo() )->save();
            $order_id = current( $order->getItems() )->getCA()->getOrderId();
            PaymentFlow::sendNotifications( $order_id );
            $data = PaymentFlow::getBookingResultFromOrderId( $status, $order_id );
        } else {
            Proxy\Packages::sendNotifications( Proxy\Packages::createPackage( $this, $payment ) );
        }

        return compact( 'status', 'data' );
    }

    /**
     * Get checkout url for Cloud Stripe
     *
     * @param string $response_url
     * @return string
     * @throws \Exception
     */
    protected function getCheckoutUrlForCloudStripe( $response_url )
    {
        $payment = $this->createPayment();
        $metadata = array();
        // Build custom metadata.
        if ( get_option( 'bookly_cloud_stripe_custom_metadata' ) ) {
            $codes = Booking\InfoText::getCodes( Steps::PAYMENT, $this->getUserData() );
            foreach ( get_option( 'bookly_cloud_stripe_metadata', array() ) as $meta ) {
                $metadata[ preg_replace( '/[^ \w]+/', '', $meta['name'] ) ] = BooklyLib\Utils\Codes::replace( $meta['value'], $codes, false );
            }
        }
        $metadata['payment_id'] = $payment->getId();
        $info = array(
            'total' => $payment->getPaid(),
            'description' => $this->getUserData()->cart->getItemsTitle(),
            'customer_email' => $this->customer['email'],
            'metadata' => $metadata,
        );
        $api = BooklyLib\Cloud\API::getInstance();
        $response = $api->stripe
            ->createSession(
                $info,
                add_query_arg( array( 'bookly_action' => 'cloud_stripe-success', 'payment' => $payment->getToken() ), $response_url ),
                add_query_arg( array( 'bookly_action' => 'cloud_stripe-cancel', 'payment' => $payment->getToken() ), $response_url )
            );
        if ( $response ) {
            if ( $this->isAppointment() ) {
                $order = $this->getUserData()->save( $payment );
                $payment
                    ->setDetailsFromOrder( $order, $this->getCartInfo() );
            } else {
                Proxy\Packages::sendNotifications( Proxy\Packages::createPackage( $this, $payment ) );
            }
            $payment
                ->setRefId( $response['payment_intent'] )
                ->save();

            return $response['redirect_url'];
        } else {
            $payment->delete();
        }

        throw new \Exception( current( $api->getErrors() ) );
    }

    /**
     * Get payment
     *
     * @return BooklyLib\Entities\Payment
     */
    protected function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set booking status
     *
     * @param string $status
     * @return void
     */
    protected function setBookingStatus( $status )
    {
        $this->booking_status = $status;
    }

    /**
     * Check if the client has reached the appointments limit
     *
     * @return bool
     */
    protected function isReachedAppointmentsLimit()
    {
        $data = array();
        foreach ( $this->getUserData()->cart->getItems() as $cart_item ) {
            if ( $cart_item->toBePutOnWaitingList() ) {
                // Skip waiting list items.
                continue;
            }
            $service = $cart_item->getService();
            if ( $service->getLimitPeriod() != 'off' ) {
                $slots = $cart_item->getSlots();
                $data[ $service->getId() ]['service'] = $service;
                $data[ $service->getId() ]['dates'][] = $slots[0][2];
            }
        }
        if ( $data ) {
            $customer = $this->getUserData()->getCustomer();
            foreach ( $data as $service_data ) {
                if ( $service_data['service']->appointmentsLimitReached( $customer->getId(), $service_data['dates'] ) ) {
                    return true;
                }
            }
        }

        return false;
    }
}