<?php
namespace BooklyPro\Lib\ProxyProviders;

use Bookly\Backend\Modules\Settings;
use Bookly\Lib as BooklyLib;
use Bookly\Lib\Entities\Appointment;
use Bookly\Lib\Entities\Staff;
use Bookly\Lib\Entities\Service;
use Bookly\Lib\Entities\Payment;
use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Slots\DatePoint;
use Bookly\Lib\Slots\Range;
use Bookly\Lib\Slots\RangeCollection;
use BooklyPro\Backend\Modules;
use BooklyPro\Lib;
use BooklyPro\Lib\Config;
use BooklyPro\Lib\Google;
use BooklyPro\Lib\Zoom;
use Bookly\Lib\DataHolders\Booking\Order;

/**
 * Class Local
 * @package BooklyPro\Lib\ProxyProviders
 */
class Local extends BooklyLib\Proxy\Pro
{
    /**
     * @inheritDoc
     */
    public static function addLicenseBooklyMenuItem()
    {
        add_submenu_page(
            'bookly-menu',
            __( 'License verification', 'bookly' ),
            __( 'License verification', 'bookly' ),
            'read',
            Settings\Page::pageSlug(),
            function () { Modules\License\Page::render(); }
        );
    }

    /**
     * @inheritDoc
     */
    public static function deleteGoogleCalendarEvent( Appointment $appointment )
    {
        if ( $appointment->hasGoogleCalendarEvent() ) {
            $google = new Google\Client();
            if ( $google->authWithStaffId( $appointment->getStaffId() ) ) {
                // Delete existing event in Google Calendar.
                $google->calendar()->deleteEvent( $appointment->getGoogleEventId() );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function deleteOnlineMeeting( Appointment $appointment )
    {
        // Zoom.
        if ( $appointment->getOnlineMeetingProvider() == 'zoom' ) {
            $zoom = new Zoom\Meetings( Staff::find( $appointment->getStaffId() ) );
            $zoom->remove( $appointment->getOnlineMeetingId() );
        }
    }

    /**
     * @inheritDoc
     */
    public static function getLastCustomerTimezone( $customer_id )
    {
        $timezone = CustomerAppointment::query( 'ca' )
            ->select( 'ca.time_zone, ca.time_zone_offset' )
            ->where( 'ca.customer_id', $customer_id )
            ->whereNot( 'ca.time_zone_offset', null )
            ->sortBy( 'created_at' )
            ->order( 'DESC' )
            ->limit( 1 )
            ->fetchArray();

        if ( ! empty( $timezone ) ) {
            $timezone = current( $timezone );

            return self::getCustomerTimezone( $timezone['time_zone'], $timezone['time_zone_offset'] );
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public static function getCustomerTimezone( $time_zone, $time_zone_offset )
    {
        if ( $time_zone ) {
            return $time_zone;
        } elseif ( $time_zone_offset !== null ) {
            return sprintf( 'UTC%s%s', $time_zone_offset > 0 ? '-' : '+', abs( $time_zone_offset ) / 60 );
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function getTimeZoneOffset( $time_zone_value )
    {
        $time_zone        = null;
        $time_zone_offset = null;  // in minutes

        // WordPress value.
        if ( $time_zone_value ) {
            if ( preg_match( '/^UTC[+-]/', $time_zone_value ) ) {
                $offset           = preg_replace( '/UTC\+?/', '', $time_zone_value );
                $time_zone        = null;
                $time_zone_offset = - $offset * 60;
            } else {
                $time_zone        = $time_zone_value;
                $time_zone_offset = - timezone_offset_get( timezone_open( $time_zone_value ), new \DateTime() ) / 60;
            }
        }

        return compact( 'time_zone', 'time_zone_offset' );
    }

    /**
     * @inheritDoc
     */
    public static function getGoogleCalendarBookings( array $staff_ids, DatePoint $dp )
    {
        $result = array();

        if ( Config::getGoogleCalendarSyncMode() == '1.5-way' ) {
            $query = Staff::query()
                ->whereIn( 'id', $staff_ids )
                ->whereNot( 'google_data', null );
            /** @var Staff $staff */
            foreach ( $query->find() as $staff ) {
                $google = new Google\Client();
                if ( $google->auth( $staff ) ) {
                    $bookings = $google->calendar()->getBookings( $dp );
                    if ( $bookings ) {
                        $result[ $staff->getId() ] = $bookings;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function getGoogleCalendarSyncMode()
    {
        return Config::getGoogleCalendarSyncMode();
    }

    /**
     * @inheritDoc
     */
    public static function graceExpired()
    {
        return Config::graceExpired();
    }

    /**
     * @inheritDoc
     */
    public static function prepareNotificationMessage( $message, $recipient, $gateway )
    {
        $remaining_days = Config::graceRemainingDays();

        if ( $remaining_days !== false ) {
            if ( $remaining_days === 0 ) {
                if ( $recipient == 'staff' ) {
                    return $gateway == 'email'
                        ? __( 'A new appointment has been created. To view the details of this appointment, please contact your website administrator in order to verify Bookly Pro license.', 'bookly' )
                        : __( 'You have a new appointment. To view it, contact your admin to verify Bookly Pro license.', 'bookly' );
                } else {
                    return $gateway == 'email'
                        ? __( 'A new appointment has been created. To view the details of this appointment, please verify Bookly Pro license in the administrative panel.', 'bookly' )
                        : __( 'You have a new appointment. To view it, please verify Bookly Pro license.', 'bookly' );
                }
            } else {
                $days_text = sprintf( _n( '%d day', '%d days', $remaining_days, 'bookly' ), $remaining_days );
                $replace   = array( '{days}' => $days_text );
                if ( $recipient == 'staff' ) {
                    return $message . PHP_EOL . ( $gateway == 'email'
                        ? strtr( __( 'Please contact your website administrator in order to verify the license for Bookly add-ons. If you do not verify the license within {days}, the respective add-ons will be disabled.', 'bookly' ), $replace )
                        : strtr( __( 'Contact your admin to verify Bookly add-ons license; {days} remaining.', 'bookly' ), $replace ) );
                } else {
                    return $message . PHP_EOL . ( $gateway == 'email'
                        ? strtr( __( 'Please verify the license for Bookly add-ons in the administrative panel. If you do not verify the license within {days}, the respective add-ons will be disabled.', 'bookly' ), $replace )
                        : strtr( __( 'Please verify Bookly add-ons license; {days} remaining.', 'bookly' ), $replace ) );
                }
            }
        }

        return $message;
    }

    /**
     * @inheritDoc
     */
    public static function revokeGoogleCalendarToken( Staff $staff )
    {
        $google = new Google\Client();
        if ( $google->auth( $staff ) ) {
            if ( BooklyLib\Config::advancedGoogleCalendarActive() ) {
                $google->calendar()->stopWatching( false );
            }
            $google->revokeToken();
        }
    }

    /**
     * @inheritDoc
     */
    public static function syncGoogleCalendarEvent( Appointment $appointment )
    {
        if ( ! Config::graceExpired() && $appointment->getStartDate() ) {
            $google = new Google\Client();
            if ( $google->authWithStaffId( $appointment->getStaffId() ) ) {
                $google->calendar()->syncAppointment( $appointment );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function createWPUser( Customer $customer )
    {
        if ( get_option( 'bookly_cst_create_account', 0 ) && ! $customer->getWpUserId() ) {
            $wp_user_id = 0;//get_current_user_id(); // Mady M
            if ( BooklyLib\Config::wooCommerceEnabled() && is_admin() ) {
                // If WC administrator manually changes the order status,
                // it is not allowed for new client to tie administrator's ID,
                // but create a new wp_user
                $wp_user_id = 0;
            }

            if ( $wp_user_id == 0 ) {
                $params = array(
                    'first_name' => $customer->getFirstName(),
                    'last_name' => $customer->getLastName(),
                    'full_name' => $customer->getFullName(),
                    'email' => $customer->getEmail(),
                );
                // Create new WP user and send email notification.
                try {
                    $wp_user = Lib\Utils\Common::createWPUser( $params, $password, 'client' );
                    $wp_user->set_role( get_option( 'bookly_cst_new_account_role' ) );
                    $wp_user_id = $wp_user->ID;

                    // Save entity for fill name, first_name, last_name
                    $customer->setWpUserId( $wp_user_id )->save();

                    // Send email/sms notification.
                    Lib\Notifications\NewWpUser\Sender::sendAuthToClient( $customer, $wp_user->display_name, $password );
                } catch ( \Exception $e ) {
                    $wp_user_id = null;
                }
            }

            $customer->setWpUserId( $wp_user_id );
        }
    }

    /**
     * @inheritDoc
     */
    public static function getMinimumTimePriorBooking( $service_id = null )
    {
        return Config::getMinimumTimePriorBooking( $service_id );
    }

    /**
     * @inheritDoc
     */
    public static function getMinimumTimePriorCancel( $service_id = null )
    {
        return Config::getMinimumTimePriorCancel( $service_id );
    }

    /**
     * @inheritDoc
     */
    public static function getStaffCategoryName( $category_id )
    {
        $has_categories = (bool) Lib\Entities\StaffCategory::query()->findOne();
        $category       = Lib\Entities\StaffCategory::find( $category_id );

        return $category ? $category->getName() : ( $has_categories ? __( 'Uncategorized', 'bookly' ) : '' );
    }

    /**
     * @inheritDoc
     */
    public static function getStaffDataForDropDown()
    {
        $result = array();

        $rows = BooklyLib\Entities\Staff::query( 's' )
            ->select( 'sc.id AS category_id, sc.name, s.id, s.full_name' )
            ->leftJoin( 'StaffCategory', 'sc', 'sc.id = s.category_id', '\BooklyPro\Lib\Entities' )
            ->whereNot( 's.visibility', 'archive' )
            ->sortBy( 'COALESCE(sc.position,99999), s.position, s.id' )
            ->fetchArray()
        ;
        foreach ( $rows as $row ) {
            $category_id = (int) $row['category_id'];
            if ( ! isset ( $result[ $category_id ] ) ) {
                $result[ $category_id ] = array(
                    'name'  => $category_id ? $row['name'] : __( 'Uncategorized', 'bookly' ),
                    'items' => array(),
                );
            }
            $result[ $category_id ]['items'][] = array(
                'id'        => $row['id'],
                'full_name' => $row['full_name'],
            );
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function showFacebookLoginButton()
    {
        return Config::showFacebookLoginButton();
    }

    /**
     * @inheritDoc
     */
    public static function getCustomerByFacebookId( $facebook_id )
    {
        if ( $facebook_id && Config::showFacebookLoginButton() ) {
            // Try to find customer by Facebook ID.
            return Customer::query()
                ->where( 'facebook_id', $facebook_id )
                ->findOne() ?: false;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public static function getFullAddressByCustomerData( array $data )
    {
        return Lib\Utils\Common::getFullAddressByCustomerData( $data );
    }

    /**
     * @inheritDoc
     */
    public static function createBackendPayment( array $data, CustomerAppointment $ca )
    {
        if ( true || isset( $data['payment_action'], $data['payment_for'] ) && $data['payment_action'] === 'create' && $data['payment_for'] === 'current' ) { // Mady To Look can we remove true make sure a duplicate payment is not created
            $appointment = Appointment::find( $ca->getAppointmentId() );
            //echo "<pre>".print_r($appointment,true)."</pre>";
             //echo "<pre>".print_r($ca ->getPaymentId(),true)."</pre>"; // paymet Id

            if($ca ->getPaymentId()) {
                return;
            }
            $payment = new Payment();
            $service = null;
            if ( $appointment->getCustomServiceName() === null ) {
                $service  = Service::find( $appointment->getServiceId() );
                $title    = $service->getTitle();
                $duration = $service->getDuration();
            } else {
                $title    = $appointment->getCustomServiceName();
                $duration = strtotime( $appointment->getEndDate() ) - strtotime( $appointment->getStartDate() );
            }

            $staff  = Staff::find( $appointment->getStaffId() ); // Mady MM check
            $extras = array();
            if ( $ca->getExtras() != '[]' ) {
                $_extras = json_decode( $ca->getExtras(), true );
                /** @var \BooklyServiceExtras\Lib\Entities\ServiceExtra $extra */
                foreach ( BooklyLib\Proxy\ServiceExtras::findByIds( array_keys( $_extras ) ) ?: array() as $extra ) { // Mady MM check
                    $quantity = $_extras[ $extra->getId() ];
                    $extras[] = array(
                        'title'    => $extra->getTitle(),
                        'price'    => $extra->getPrice(),
                        'quantity' => $quantity,
                    );
                }
            }
            $category_name = '';
            if ( $appointment->getServiceId() ) {
                $service = Service::find( $appointment->getServiceId() );
                if ( $service->getCategoryId() ) {
                    $category = BooklyLib\Entities\Category::find( $service->getCategoryId() );
                    if ( $category ) {
                        $category_name = $category->getName();
                    }
                }
            }
            $price = (int) $service->getPrice() + (int) $data['payment_price']; // Mady M // TODO test
            $tax   = $data['payment_tax'] ?: 0;
            $item = array(
                'ca_id'             => $ca->getId(),
                'appointment_date'  => $appointment->getStartDate(),
                'app_start_info'    => ( $service && $duration >= DAY_IN_SECONDS ) ? $service->getStartTimeInfo(): null,
                'service_name'      => $title,
                'service_category'  => $service ? $service->getTranslatedCategoryName() : '',
                'service_price'     => $price,
                'service_tax'       => $tax,
                'wait_listed'       => $ca->getStatus() == $ca::STATUS_WAITLISTED,
                'number_of_persons' => $ca->getNumberOfPersons(),
                'units'             => $ca->getUnits() ?: 1,
                'duration'          => $duration,
                'staff_name'        => $staff->getFullName(),
                'extras'            => $extras,
            );
            if ( BooklyLib\Config::depositPaymentsActive() ) {
                $item['deposit_format'] = BooklyLib\Proxy\DepositPayments::formatDeposit( 0, 0 );
            }

            $payment
                ->setType( Payment::TYPE_LOCAL )
                ->setStatus( Payment::STATUS_PENDING )
                ->setTotal( get_option( 'bookly_taxes_in_price' ) == 'excluded' ? $price + $tax : $price )
                ->setTax( $tax )
                ->setDetails( json_encode( array(
                'items' => array( $item ),
                'coupon' => null,
                'subtotal' => array( 'price' => $price, 'deposit' => 0 ),
                    'customer'     => Customer::find( $ca->getCustomerId() )->getFullName(),
                    'customer_appt'=> json_encode($ca),
                'tax_in_price' => get_option( 'bookly_taxes_in_price' ) ?: 'excluded',
                'tax_paid' => null,
                'from_backend' => true,
                ) ) )
                ->setPaid( 0 )
                ->save();
            $ca->setPaymentId( $payment->getId() )->save();
        }
    }

    /**
     * @inheritDoc
     */
    public static function createBackendInvoicePayment( $payments ) // Create Generate new payment Invoices
    {
        //echo "<pre>".print_r($payments,true)."</pre>";
        if ( true) { // Mady M TODO make sure we generate invoice for single customer
            $item = array();
            foreach($payments as $paymentTxns){
                $txnTemp = $paymentTxns->getPaymentData();
            $payment = new Payment();
                $catemp = $txnTemp['payment']['items'][0];
                //echo "<pre>".print_r($catemp,true)."</pre>";
                $ca = CustomerAppointment::find( $catemp['ca_id'] );
                //echo "<pre>".print_r('Customer Appts already',true)."</pre>";
                //echo "<pre>".print_r($ca,true)."</pre>";

                $appointmentTemp = Appointment::find($ca ->getAppointmentId() );

                $anyService = $appointmentTemp -> getServiceId();

                //echo "<pre>".print_r('Customer Appts Info',true)."</pre>";
                //echo "<pre>".print_r($anyService,true)."</pre>";


                //echo "<pre>".print_r($ca,true)."</pre>";
                //$appointment = Appointment::find($ca ->getAppointmentId() );
                //echo "<pre>".print_r($txnTemp['payment']['items'][0]['ca_id'],true)."</pre>";
                $category_name = '';
                //echo "<pre>".print_r($appointment,true)."</pre>";

//                 if ( $appointment->getCustomServiceName() === null ) {
//                     $service  = Service::find( $appointment->getServiceId() );
//                     $title    = $service->getTitle();
//                     $duration = $service->getDuration();
//                 } else {
//                     $title    = $appointment->getCustomServiceName();
//                     $duration = strtotime( $appointment->getEndDate() ) - strtotime( $appointment->getStartDate() );
//                 }
//
//                 //$staff  = Staff::find( Appointment::find( $ca->getAppointmentId() )->getStaffId() );
//                 $extras = array();
//                 if ( $ca->getExtras() != '[]' ) {
//                     $_extras = json_decode( $ca->getExtras(), true );
//                     /** @var \BooklyServiceExtras\Lib\Entities\ServiceExtra $extra */
//                     foreach ( (array) BooklyLib\Proxy\ServiceExtras::findByIds( array_keys( $_extras ) ) as $extra ) {
//                         $quantity = $_extras[ $extra->getId() ];
//                         $extras[] = array(
//                             'title'    => $extra->getTitle(),
//                             'price'    => $extra->getPrice(),
//                             'quantity' => $quantity,
//                         );
//                     }
//                 }
//
//                 if ( $appointment->getServiceId() ) {
//                     $service = Service::find( $appointment->getServiceId() );
//                     if ( $service->getCategoryId() ) {
//                         $category = BooklyLib\Entities\Category::find( $service->getCategoryId() );
//                         if ( $category ) {
//                             $category_name = $category->getName();
//                         }
//                     }
//                 }
                //$price = (int) $service->getPrice();//$data['payment_price']; // Mady M
                $tax   = $data['payment_tax'] ?: 0;


                //echo "<pre>".print_r($paymentTxn,true)."</pre>";


                //echo "<pre>".print_r($appointment,true)."</pre>";
                //$txnTemp['id'] = 'Invoice_GenerateTest';
                //echo "<pre>".print_r($txnTemp['payment'],true)."</pre>";
                //$customerName = $txnTemp['payment']['customer'];
                $paymentTxnTemp = $txnTemp['payment']['items'];
                //echo "<pre>".print_r($paymentTxnTemp['payment']['items'],true)."</pre>";
                $item[] = $paymentTxnTemp[0];
                //echo "<pre>".print_r($item,true)."</pre>";
                foreach($paymentTxnTemp as $paymentTxn){
                    //echo "<pre>".print_r(json_encode($paymentTxn),true)."</pre>";
                    $subtotal += $paymentTxn['service_price'];
                    $total += $paymentTxn['service_price'];
                }
                //echo "<pre>".print_r($item,true)."</pre>";

            }
            //echo "<pre>".print_r($item,true)."</pre>";
            $customer = Customer::find( $ca->getCustomerId() ); // get customer Info to create a new appointment
            //echo "<pre>".print_r($customer,true)."</pre>";
            $appointment = new Appointment();
            $appointment
                ->setStaffId( 1 ) // set Admin as staff who created apppt and generated invoice
                ->setServiceId( $anyService ) // set any serviceId in main category
                ->setCreatedFrom( 'backend' )
                ->setCustomServicePrice( 0 )
                ->setStartDate( null )
                ->setEndDate( null )
                ->setInternalNote( 'Dummy' )
                ->save();

            //Lib\Utils\Log::createEntity( $appointment, __METHOD__ );
//             echo "<pre>".print_r('Dummy Appt',true)."</pre>";
//             echo "<pre>".print_r($appointment,true)."</pre>";
//             echo "<pre>".print_r($ca,true)."</pre>";

//                             //$order = new Order( $customer );
//                             /** @var Service $service */
//                             $service = Service::query()->findOne();
//                             $service->setTitle( 'Service Name' );
//                             /** @var Staff $staff */
//                             $staff = Staff::query()->findOne();
//                             $staff->setFullName( 'Staff Name' );
//                             $appointment = new Appointment();
//                             echo "<pre>".print_r($appointment,true)."</pre>";
//                             $start = date_create()->modify( '-7 days' )->setTime( 8, 0, 0 );
//                             $appointment
//                                 ->setService( $service )
//                                 ->setInternalNote( 'Internal note' )
//                                 ->setCreatedAt( $start->format( 'Y-m-d H:i:s' ) )
//                                 ->setCreatedFrom( 'backend' )
//                                 ->setStaff( $staff )
//                                 ->setService( $service )
//                                 ->setStartDate( $start->format( 'Y-m-d H:i:s' ) )
//                                 ->setEndDate( $start->modify( $service->getDuration() . ' sec' )->format( 'Y-m-d H:i:s' ) );
//                             $ca = new CustomerAppointment();
//                             echo "<pre>".print_r($ca,true)."</pre>";
//                             $ca->setNotes( 'Notes' )
//                                 ->setNumberOfPersons( '1' )
//                                 ->setCustomerId($ca->getCustomerId())
//                                 ->setStatus( CustomerAppointment::STATUS_APPROVED )
//                                 ->setToken( 'token' )
//                                 ->setAppointment( $appointment )
//                                 ->save();

//             if ( BooklyLib\Config::depositPaymentsActive() ) {
//                 $item['deposit_format'] = BooklyLib\Proxy\DepositPayments::formatDeposit( 0, 0 );
//             }
            //$payment->id = 'Invoice_GenerateTest';
            //$customer = Customer::find( $ca->getCustomerId() );
//             echo "<pre>".print_r($customer,true)."</pre>";
//             echo "<pre>".print_r($payment,true)."</pre>";

                $payment
                ->setType( Payment::TYPE_PAYSON ) // TODO set unique payment type for generated invoice
                    ->setStatus( Payment::STATUS_PENDING )
                ->setTotal( $total )
                ->setTax( 0 )
                ->setDetails( json_encode( array(
                    'items'        =>  $item,
                    'coupon'       => null,
                    'subtotal'     => array( 'price' => $subtotal, 'deposit' => 0 ),
                    'customer'     => $customer->getFullName(),
                    'tax_in_price' => get_option( 'bookly_taxes_in_price' ) ?: 'excluded',
                    'tax_paid'     => null,
                    'from_backend' => true,
                ) ) )
                ->setPaid( 0 )
                ->save();
                //echo "<pre>".print_r($payment,true)."</pre>";
                $ca = new CustomerAppointment();
                $ca->setNotes( 'Notes' )
                    ->setCustomer( $customer )
                    ->setCreatedFrom( 'backend' )
                    ->setCreatedAt( current_time( 'mysql' ) )
                    ->setNumberOfPersons( '1' )
                    ->setStatus( CustomerAppointment::STATUS_REJECTED )
                    ->setAppointment( $appointment );
            $ca->setPaymentId( $payment->getId() )->save();  // If i save search works but the last payment is overridden
            //echo "<pre>".print_r($payment,true)."</pre>";
            BooklyLib\Proxy\Invoices::downloadInvoices( [$payment]); // M Mady // Download the created Invoice
//             echo "<pre>".print_r('CA Appt',true)."</pre>";
//             echo "<pre>".print_r($ca,true)."</pre>";
        }
    }

    /**
     * @inheritDoc
     */
    public static function createBackendMasterInvoicePayment( $payments ) // Create Generate new payment Invoices
    {
        //echo "<pre>".print_r($payments,true)."</pre>";
        if ( true) { // Mady M TODO make sure we generate invoice for single customer
            $item = array();
            $key=0;
            foreach($payments as $paymentTxns){
                $txnTemp = $paymentTxns->getPaymentData();
                $payment = new Payment();
                $catemp = $txnTemp['payment']['items'][0];
                //echo "<pre>".print_r($catemp,true)."</pre>";
                $ca = BooklyLib\Entities\CustomerAppointment::find( $catemp['ca_id'] );
                //echo "<pre>".print_r($ca,true)."</pre>";
                $customer[] = Customer::find( $ca->getCustomerId() );
//                 echo "<pre>".print_r('Customer Details',true)."</pre>";
//                 echo "<pre>".print_r($customer,true)."</pre>";
                $appointment = Appointment::find($ca ->getAppointmentId() );
                $anyService = $appointment -> getServiceId();
                //echo "<pre>".print_r($txnTemp['payment']['items'][0]['ca_id'],true)."</pre>";
                $category_name = '';
                //echo "<pre>".print_r($appointment,true)."</pre>";

//                 if ( $appointment->getCustomServiceName() === null ) {
//                     $service  = Service::find( $appointment->getServiceId() );
//                     $title    = $service->getTitle();
//                     $duration = $service->getDuration();
//                 } else {
//                     $title    = $appointment->getCustomServiceName();
//                     $duration = strtotime( $appointment->getEndDate() ) - strtotime( $appointment->getStartDate() );
//                 }
//
//                 $staff  = Staff::find( Appointment::find( $ca->getAppointmentId() )->getStaffId() );
//                 $extras = array();
//                 if ( $ca->getExtras() != '[]' ) {
//                     $_extras = json_decode( $ca->getExtras(), true );
//                     /** @var \BooklyServiceExtras\Lib\Entities\ServiceExtra $extra */
//                     foreach ( (array) BooklyLib\Proxy\ServiceExtras::findByIds( array_keys( $_extras ) ) as $extra ) {
//                         $quantity = $_extras[ $extra->getId() ];
//                         $extras[] = array(
//                             'title'    => $extra->getTitle(),
//                             'price'    => $extra->getPrice(),
//                             'quantity' => $quantity,
//                         );
//                     }
//                 }
//
//                 if ( $appointment->getServiceId() ) {
//                     $service = Service::find( $appointment->getServiceId() );
//                     if ( $service->getCategoryId() ) {
//                         $category = BooklyLib\Entities\Category::find( $service->getCategoryId() );
//                         if ( $category ) {
//                             $category_name = $category->getName();
//                         }
//                     }
//                 }
//                 $price = (int) $service->getPrice();//$data['payment_price']; // Mady M
//                 $tax   = $data['payment_tax'] ?: 0;


                //echo "<pre>".print_r($paymentTxn,true)."</pre>";


                //echo "<pre>".print_r($appointment,true)."</pre>";
                //$txnTemp['id'] = 'Invoice_GenerateTest';
                //echo "<pre>".print_r('TempObj',true)."</pre>";
                //echo "<pre>".print_r($customer,true)."</pre>";
                $customerName = $txnTemp['payment']['customer'];
                $txnTemp['payment']['customer_fields'] = ($customer[$key]->getInfoFields());
                $txnTemp['payment']['custom_fields'] = ($ca->getCustomFields());

//                 echo "<pre>".print_r(($txnTemp['payment']['customer_fields']),true)."</pre>";
//                 echo "<pre>".print_r(($txnTemp['payment']['custom_fields']),true)."</pre>";
                $masterInvoiceDetails['items'][] = $txnTemp['payment'];



                //echo "<pre>".print_r($masterInvoiceDetails,true)."</pre>";

                $paymentTxnTemp = $txnTemp['payment']['items'];
                //echo "<pre>".print_r($paymentTxnTemp['payment']['items'],true)."</pre>";
                $item[] = $paymentTxnTemp[0];
                //echo "<pre>".print_r($item,true)."</pre>";
                foreach($paymentTxnTemp as $paymentTxn){
                    //echo "<pre>".print_r(json_encode($paymentTxn),true)."</pre>";
                    $subtotal += $paymentTxn['service_price'];
                    $total += $paymentTxn['service_price'];
                }
//                 foreach($masterInvoiceDetails as $paymentTxn){
//                     echo "<pre>".print_r(json_encode($paymentTxn),true)."</pre>";
//                     $grandTotal += $paymentTxn['total'];
//                     //$total += $paymentTxn['service_price'];
//                 }
                //echo "<pre>".print_r($item,true)."</pre>";
                $key++;
            }
            foreach($masterInvoiceDetails['items'] as $mdi){
                $grandTotal += $mdi['total'];
            }

            $masterInvoiceDetails['grandTotal'] = $grandTotal;


            //$grandTotal += $masterInvoiceDetails['items']['total'];
            //echo "<pre>".print_r('Grand Total',true)."</pre>";
            //echo "<pre>".print_r($grandTotal,true)."</pre>";
//             echo "<pre>".print_r('Customer Details',true)."</pre>";
//             echo "<pre>".print_r($customer,true)."</pre>";
            //echo "<pre>".print_r('Master Details',true)."</pre>";
            //echo "<pre>".print_r($item,true)."</pre>";

//             if ( BooklyLib\Config::depositPaymentsActive() ) {
//                 $item['deposit_format'] = BooklyLib\Proxy\DepositPayments::formatDeposit( 0, 0 );
//             }
            //$payment->id = 'Invoice_GenerateTest';
            //$customer = Customer::find( $ca->getCustomerId() );
            //echo "<pre>".print_r($customer,true)."</pre>";
            //echo "<pre>".print_r('Master Invoice Object',true)."</pre>";
            //echo "<pre>".print_r($masterInvoiceDetails,true)."</pre>";

            $customer = Customer::find( $ca->getCustomerId() ); // get customer Info to create a new appointment
            //echo "<pre>".print_r($customer,true)."</pre>";
            $appointment = new Appointment();
            $appointment
                ->setStaffId( 1 ) // set Admin as staff who created apppt and generated invoice
                ->setServiceId( $anyService ) // set any serviceId in main category
                ->setCreatedFrom( 'backend' )
                ->setCustomServicePrice( 0 )
                ->setStartDate( null )
                ->setEndDate( null )
                ->setInternalNote( 'Dummy' )
                ->save();

            //echo "<pre>".print_r($customer,true)."</pre>";


            $ca = new CustomerAppointment();
            $ca->setNotes( 'Notes' )
                ->setCustomer( $customer )
                ->setCreatedFrom( 'backend' )
                ->setCreatedAt( current_time( 'mysql' ) )
                ->setNumberOfPersons( '1' )
                ->setStatus( CustomerAppointment::STATUS_REJECTED )
                ->setAppointment( $appointment );

                 //echo "<pre>".print_r($ca,true)."</pre>";

            $payment
                ->setType( Payment::TYPE_MOLLIE ) // TODO set unique payment type for generated invoice
                ->setStatus( Payment::STATUS_PENDING )
                ->setTotal( $total )
                ->setTax( 0 )
                ->setDetails( json_encode( array(
                    'items'        =>  $item,
                    'masterInvoiceDetails'        =>  $masterInvoiceDetails,
                    'coupon'       => null,
                    'subtotal'     => array( 'price' => $grandTotal, 'deposit' => 0 ),
                    'customer'     => 'Master Invoice',
                    'tax_in_price' => get_option( 'bookly_taxes_in_price' ) ?: 'excluded',
                    'tax_paid'     => null,
                    'from_backend' => true,
                ) ) )
                ->setPaid( 0 )
                ->save();


//              echo "<pre>".print_r('PAYment Details',true)."</pre>";
//              echo "<pre>".print_r($payment,true)."</pre>";
//              echo "<pre>".print_r($customer,true)."</pre>";
//              echo "<pre>".print_r($ca,true)."</pre>";
            $ca->setPaymentId( $payment->getId() )->save();
            BooklyLib\Proxy\Invoices::downloadMasterInvoice( [$payment]); // M Mady
        }
    }

    /**
     * @inheritDoc
     */
    public static function prepareGeneratorRanges( $ranges, $staff, $duration )
    {
        $limit = $staff->getWorkingTimeLimit();
        if ( $limit !== null ) {
            $result_ranges = new RangeCollection();
            foreach ( $ranges->all() as $range ) {
                $new_ranges = new RangeCollection();
                if ( $range->state() == Range::AVAILABLE ) {
                    $start_date = $range->start()->value()->format( 'Y-m-d' );
                    $end_date   = $range->end()->value()->format( 'Y-m-d' );
                    if ( $start_date == $end_date ) {
                        if ( $staff->getWorkload( $start_date ) + $duration > $limit ) {
                            $range = $range->replaceState( Range::FULLY_BOOKED );
                        }
                    } else {
                        if ( $staff->getWorkload( $start_date ) + $staff->getWorkload( $end_date ) + $duration > $limit * 2 ) {
                            $range = $range->replaceState( Range::FULLY_BOOKED );
                        } else {
                            if ( $staff->getWorkload( $start_date ) + $duration > $limit ) {
                                $new_ranges = $range->subtract( Range::fromDates( $range->start()->value()->format( 'Y-m-d H:i:s' ), date_create( $end_date )->modify( sprintf( '-%d seconds', $limit - $staff->getWorkload( $start_date ) ) )
                                    ->format( 'Y-m-d H:i:s' ) ) );
                                if ( $new_ranges->has( 1 ) ) {
                                    $new_ranges->get( 1 )->replaceState( Range::FULLY_BOOKED );
                                }
                            }
                            if ( $staff->getWorkload( $end_date ) + $duration > $limit ) {
                                $new_ranges = $range->subtract( Range::fromDates( date_create( $end_date )->modify( sprintf( '+%d seconds', $limit - $staff->getWorkload( $end_date ) ) )->format( 'Y-m-d H:i:s' ), $range->end()->value()
                                    ->format( 'Y-m-d H:i:s' ) ) );
                                if ( $new_ranges->has( 1 ) ) {
                                    $new_ranges->get( 0 )->replaceState( Range::FULLY_BOOKED );
                                }
                            }
                        }
                    }
                }
                if ( $new_ranges->isEmpty() ) {
                    $result_ranges->push( $range );
                } else {
                    $result_ranges = $result_ranges->merge( $new_ranges );
                }
            }
            $ranges = $result_ranges;
        }

        return $ranges;
    }

    /**
     * @inheritDoc
     */
    public static function getWorkingTimeLimitError( $staff, $start_date, $end_date, $duration, $appointment_id )
    {
        $limit = $staff->getWorkingTimeLimit();
        if ( $limit !== null ) {
            $start_day = date_create( $start_date )->format( 'Y-m-d' );
            $end_day   = date_create( $end_date )->format( 'Y-m-d' );
            if ( $start_day == $end_day ) {
                if ( $staff->getWorkload( $start_day, $appointment_id ? array( $appointment_id ) : array() ) + $duration > $limit ) {
                    return true;
                }
            } else {
                $start_day_workload = $staff->getWorkload( $start_day, $appointment_id ? array( $appointment_id ) : array() );
                $end_day_workload   = $staff->getWorkload( $end_day, $appointment_id ? array( $appointment_id ) : array() );

                if ( $start_day_workload + $end_day_workload + $duration > $limit * 2 ) {
                    return true;
                } else {
                    if ( $start_day_workload + $duration - strtotime( $end_date ) + strtotime( $end_day ) > $limit ) {
                        return true;
                    }
                    if ( $end_day_workload + $duration - strtotime( $end_day ) + strtotime( $start_date ) > $limit ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public static function getDisplayedAddressFields()
    {
        return Lib\Utils\Common::getDisplayedAddressFields();
    }

    /**
     * @inheritDoc
     */
    public static function logEmail( $to, $subject, $body, $headers, $attachments, $type_id )
    {
        if ( get_option( 'bookly_save_email_logs' ) ) {
            $log = new Lib\Entities\EmailLog();
            $log->setTo( $to )
                ->setSubject( $subject )
                ->setBody( $body )
                ->setHeaders( json_encode( $headers ) )
                ->setAttach( json_encode( $attachments ) )
                ->setType( BooklyLib\Entities\Notification::getTypeString( $type_id ) )
                ->setCreatedAt( current_time( 'mysql' ) )
                ->save();
        }
    }
}