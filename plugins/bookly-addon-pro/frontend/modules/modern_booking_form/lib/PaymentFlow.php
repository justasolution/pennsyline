<?php
namespace BooklyPro\Frontend\Modules\ModernBookingForm\Lib;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\ModernBookingForm\Proxy;
use Bookly\Frontend\Modules\Booking;
use Bookly\Lib\Entities\Payment;

/**
 * Class PaymentFlow
 *
 * @package BooklyPro\Frontend\Modules\ModernBookingForm\Lib
 */
class PaymentFlow extends BooklyLib\Base\Component
{
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /** @var array */
    protected static $support_gateways = array(
        Payment::TYPE_LOCAL,
        Payment::TYPE_CLOUD_STRIPE,
    );

    /**
     * Set payment flow completed
     *
     * @param BooklyLib\Entities\Payment $payment
     * @return void
     */
    public static function setCompleted( BooklyLib\Entities\Payment $payment )
    {
        $payment->setStatus( BooklyLib\Entities\Payment::STATUS_COMPLETED )->save();
        $order_id = BooklyLib\Entities\CustomerAppointment::query()
            ->where( 'payment_id', $payment->getId() )
            ->fetchVar( 'order_id' );

        self::sendNotifications( $order_id );
    }

    /**
     * Get data for step done
     *
     * @param string $status
     * @param BooklyLib\Entities\Payment $payment
     * @return array
     */
    public static function getBookingResultFromPayment( $status, BooklyLib\Entities\Payment $payment )
    {
        $data = array();
        switch ( $status ) {
            case self::STATUS_PROCESSING:
            case self::STATUS_COMPLETED:
                if ( $payment->getTarget() === BooklyLib\Entities\Payment::TARGET_APPOINTMENTS ) {
                    /** @var BooklyLib\Entities\Appointment $appointment */
                    $appointment = BooklyLib\Entities\Appointment::query( 'a' )
                        ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
                        ->where( 'ca.payment_id', $payment->getId() )
                        ->findOne();

                    $data['qr'] = self::getQr( $appointment );
                }
                break;
        }

        return $data;
    }

    /**
     * Get data for step done
     *
     * @param string $status
     * @param int $order_id
     * @return array
     */
    public static function getBookingResultFromOrderId( $status, $order_id )
    {
        $data = array();
        switch ( $status ) {
            case self::STATUS_PROCESSING:
            case self::STATUS_COMPLETED:
                /** @var BooklyLib\Entities\Appointment $appointment */
                $appointment = BooklyLib\Entities\Appointment::query( 'a' )
                    ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
                    ->where( 'ca.order_id', $order_id )
                    ->findOne();
                if ( $appointment ) {
                    $data['qr'] = self::getQr( $appointment );
                }
        }

        return $data;
    }

    /**
     * @param BooklyLib\Entities\Appointment $appointment
     * @return string
     */
    private static function getQr( $appointment )
    {
        $service = BooklyLib\Entities\Service::find( $appointment->getServiceId() );
        $ics = new BooklyLib\Utils\Ics\Feed();
        $description = BooklyLib\Utils\Codes::replace( BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_ics_customer_template' ), BooklyLib\Utils\Codes::getAppointmentCodes( $appointment ), false );
        $ics->addEvent( $appointment->getStartDate(), $appointment->getEndDate(), $service->getTranslatedTitle(), $description, self::parameter( 'location_id' ) );

        return add_query_arg(
            array(
                'cht' => 'qr',
                'chs' => '298x298',
                'chl' => urlencode( $ics->render() ),
            ),
            'https://chart.googleapis.com/chart'
        );
    }

    /**
     * Delete cascade related items
     *
     * @param BooklyLib\Entities\Payment $payment
     * @return void
     */
    public static function removeCascade( BooklyLib\Entities\Payment $payment )
    {
        if ( $payment->getTarget() === BooklyLib\Entities\Payment::TARGET_APPOINTMENTS ) {
            foreach ( BooklyLib\Entities\CustomerAppointment::query()->where( 'payment_id', $payment->getId() )->find() as $ca ) {
                BooklyLib\Utils\Log::deleteEntity( $ca, __METHOD__ );
                $ca->deleteCascade();
            }
        } else {
            Proxy\Packages::deleteCascade( $payment );
        }
        $payment->delete();
    }

    /**
     * Retrieve payment system status
     *
     * @param BooklyLib\Entities\Payment $payment
     * @return string|void self::STATUS_*
     * @throws \Exception
     */
    public static function retrieveStatus( BooklyLib\Entities\Payment $payment )
    {
        if ( $payment->getType() === BooklyLib\Entities\Payment::TYPE_CLOUD_STRIPE ) {
            $payment_intent = $payment->getRefId();
            $data = BooklyLib\Cloud\API::getInstance()->stripe->retrievePaymentIntent( $payment_intent );
            if ( $data['status'] === 'canceled' ) {
                throw new \Exception();
            }
            if ( strtoupper( $data['currency'] ) == BooklyLib\Config::getCurrency() ) {
                $paid = $payment->getPaid();
                if ( ! BooklyLib\Config::isZeroDecimalsCurrency() ) {
                    $paid *= 100;
                }
                if ( (int) $paid == $data['amount'] ) {
                    $pi_status = $data['status'];
                    $good_statuses = array(
                        'succeeded' => self::STATUS_COMPLETED,
                        'processing' => self::STATUS_PROCESSING,
                    );
                    if ( array_key_exists( $pi_status, $good_statuses ) ) {
                        return $good_statuses[ $pi_status ];
                    }
                    throw new \Exception();
                }
            }
        }
    }

    /**
     * Send notifications
     *
     * @param int $order_id
     * @return void
     */
    public static function sendNotifications( $order_id )
    {
        $order = BooklyLib\DataHolders\Booking\Order::createFromOrderId( $order_id );
        if ( $order ) {
            current( $order->getItems() )->getCA()->setJustCreated( true );
            BooklyLib\Notifications\Cart\Sender::send( $order );
        }
        foreach (
            BooklyLib\Entities\Appointment::query( 'a' )
                ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
                ->where( 'ca.order_id', $order_id )->find() as $appointment
        ) {
            BooklyLib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
            BooklyLib\Proxy\OutlookCalendar::syncEvent( $appointment );
        }
    }

    /**
     * @param BooklyLib\UserBookingData $userData
     * @return array
     */
    public static function getAllowedGateways( $userData )
    {
        $gateways = array();
        $gateway = BooklyLib\Entities\Payment::TYPE_LOCAL;
        if ( BooklyLib\Config::payLocallyEnabled() && Booking\Proxy\CustomerGroups::allowedGateway( $gateway, $userData ) !== false ) {
            $gateways[ $gateway ] = true;
        }
        $gateway = BooklyLib\Entities\Payment::TYPE_CLOUD_STRIPE;
        if ( Booking\Proxy\CustomerGroups::allowedGateway( $gateway, $userData ) !== false ) {
            $pay_cloud_stripe = BooklyLib\Cloud\API::getInstance()->account->productActive( 'stripe' ) && get_option( 'bookly_cloud_stripe_enabled' );
            if ( $pay_cloud_stripe ) {
                $gateways[ $gateway ] = true;
            }
        }

        return array_keys( Booking\Proxy\Pro::filterGateways( $gateways, $userData ) );
    }

    /**
     * @param array $gateways
     * @return array
     */
    public static function orderGateways( array $gateways )
    {
        $order = explode( ',', get_option( 'bookly_pmt_order' ) );
        $ordered = array();
        if ( $order ) {
            foreach ( $order as $payment_system ) {
                if ( in_array( $payment_system, $gateways ) ) {
                    $ordered[] = $payment_system;
                }
            }
        }
        foreach ( $gateways as $payment_system ) {
            if ( ! in_array( $payment_system, $ordered ) ) {
                $ordered[] = $payment_system;
            }
        }

        $list = array();
        foreach ( $ordered as $gateway ) {
            if ( self::isSupported( $gateway ) ) {
                $list[] = $gateway;
            }
        }

        return $list;
    }

    /**
     * @param string $gateway
     * @return bool
     */
    protected static function isSupported( $gateway )
    {
        return in_array( $gateway, self::$support_gateways );
    }
}