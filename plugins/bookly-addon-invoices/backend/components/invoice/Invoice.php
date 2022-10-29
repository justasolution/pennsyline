<?php
namespace BooklyInvoices\Backend\Components\Invoice;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Modules\Payments\Proxy;
use BooklyInvoices\Backend\Modules\Settings\Lib\Helper;
use Bookly\Backend\Modules\Notifications\Lib\Codes;

/**
 * Class Invoice
 * @package BooklyInvoices\Backend\Components\Invoice
 */
class Invoice extends BooklyLib\Base\Component
{
    /**
     * Render invoice content.
     *
     * @param array $payment_data
     * @return string
     */
    public static function render( array $payment_data )
    {
        $company_logo = wp_get_attachment_image_src( get_option( 'bookly_co_logo_attachment_id' ), 'full' );

        $payment = $payment_data['payment'];
        $created_at = BooklyLib\Slots\DatePoint::fromStr( $payment['created_at'] );
        $helper = new Helper();
        $codes = array(
            '{company_address}' => nl2br( get_option( 'bookly_co_address' ) ),
            '{company_logo}' => $company_logo ? sprintf( '<img src="%s"/>', esc_attr( $company_logo[0] ) ) : '',
            '{company_name}' => get_option( 'bookly_co_name' ),
            '{company_phone}' => get_option( 'bookly_co_phone' ),
            '{company_website}' => get_option( 'bookly_co_website' ),
            '{client_email}' => '',
            '{client_first_name}' => strtok( $payment['customer'], ' ' ),
            '{client_last_name}' => strtok( '' ),
            '{client_name}' => $payment['customer'],
            '{client_phone}' => '',
            '{client_note}' => '',
            '{client_address}' => '',
            '{invoice_number}' => $payment['id'],
            '{invoice_link}' => $payment['id'] ? admin_url( 'admin-ajax.php?action=bookly_invoices_download&token=' . $payment['token'] ) : '',
            '{invoice_date}' => BooklyLib\Utils\DateTime::formatDate( $created_at->format( 'Y-m-d' ) ),
            '{invoice_due_date}' => BooklyLib\Utils\DateTime::formatDate( $created_at->modify( get_option( 'bookly_invoices_due_days' ) * DAY_IN_SECONDS )->format( 'Y-m-d' ) ),
            '{invoice_due_days}' => get_option( 'bookly_invoices_due_days' ),
        );

        $time_zone_offset = null;
        $time_zone        = null;
        /** @var BooklyLib\Entities\CustomerAppointment $ca */
        $ca = BooklyLib\Entities\CustomerAppointment::query( 'ca' )
            ->where( 'ca.payment_id', $payment['id'] )
            ->findOne();
        if ( $ca ) {
            $time_zone_offset = $ca->getTimeZoneOffset();
            $time_zone        = $ca->getTimeZone();
            /** @var BooklyLib\Entities\Customer $customer */
            $customer = BooklyLib\Entities\Customer::find( $ca->getCustomerId() );
            if ( $customer ) {
                // Mady M
                //$codes['{client_info}'] = print_r($customer->getInfoFields()); // uncomment to find the custom field ids.
                foreach ( json_decode($customer->getInfoFields()) as $value ) {
                    if($value->id){
                        $codes['{client_info_'+ $value->id +'}'] = $value->value;
                    }
                }
                foreach ( json_decode($ca->getCustomFields()) as $value ) {
                    if($value->id){
                        $codes['{custom_info_'+ $value->id +'}'] = $value->value;
                    }
                }

                // Mady M
                $codes['{client_email}'] = $customer->getEmail();
                $codes['{client_first_name}'] = $customer->getFirstName();
                $codes['{client_last_name}'] = $customer->getLastName();
                $codes['{client_name}'] = $customer->getFullName();
                $codes['{client_phone}'] = $customer->getPhone();
                $codes['{client_address}'] = $customer->getAddress();
                $codes['{client_birthday}'] = $customer->getBirthday() ? BooklyLib\Utils\DateTime::formatDate( $customer->getBirthday() ) : '';
                $codes['{client_note}'] = $customer->getNotes();
                $codes['{po_number}'] = $codes['42021']; // saved on customer profile
                $codes['{bill_to}'] = $category_name; // service category

                $codes['{client_file_number}'] = $codes['80472'];
                $codes['{secondary_email}'] = $codes['86719'];
                $codes['{secondary_phone}'] = $codes['31143'];
                $codes['{international_phone}'] = $codes['27012'];
                $codes['{additional_billinfo}'] = $codes['63135'];
                $codes['{client_file}'] = $codes['16602'];

                //echo "<pre>".print_r($codes,true)."</pre>";
            }
        }

        $show_deposit = BooklyLib\Config::depositPaymentsActive();
        if ( ! $show_deposit ) {
            foreach ( $payment['items'] as $item ) {
                if ( array_key_exists( 'deposit_format', $item ) && $item['deposit_format'] ) {
                    $show_deposit = true;
                    break;
                }
            }
        }

        switch ( $payment_data['payment']['type'] ) {
            case BooklyLib\Entities\Payment::TYPE_PAYPAL:
                $price_correction =  get_option( 'bookly_paypal_increase' ) != 0
                    || get_option( 'bookly_paypal_addition' ) != 0;
                break;
            case BooklyLib\Entities\Payment::TYPE_CLOUD_STRIPE:
                $price_correction =  get_option( 'bookly_cloud_stripe_increase' ) != 0
                    || get_option( 'bookly_cloud_stripe_addition' ) != 0;
                break;
            default:
                $price_correction = Proxy\Shared::paymentSpecificPriceExists( $payment_data['payment']['type'] ) === true;
                break;
        }

        $show = array(
            'coupons' => BooklyLib\Config::couponsActive(),
            'customer_groups' => BooklyLib\Config::customerGroupsActive(),
            'deposit' => (int) $show_deposit,
            'price_correction' => $price_correction,
            'taxes' => (int) ( BooklyLib\Config::taxesActive() || $payment['tax_total'] > 0 ),
        );
        $adjustments = isset( $payment_data['adjustments'] ) ? $payment_data['adjustments'] : array();

        $content = self::renderTemplate( 'invoice', array(
            'helper'           => $helper,
            'codes'            => '',
            'payment'          => $payment_data['payment'],
            'adjustments'      => $adjustments,
            'show'             => $show,
            'time_zone_offset' => $time_zone_offset,
            'time_zone'        => $time_zone,
        ), false );

        return strtr( $content, $codes );
    }

    /**
         * Render invoice content.
         *
         * @param array $payment_data
         * @return string
         * @param array $payment_data_arr
         * @return string
         */
    public static function renderMerge( array $payment_data_arr )
    {
        //echo "<pre>".print_r($payment_data_arr,true)."</pre>";
        //$payment_data = $payment_data ->getPaymentData();
        //var_dump($payment_data);
        //$payment->getPaymentData()
        $company_logo = wp_get_attachment_image_src( get_option( 'bookly_co_logo_attachment_id' ), 'full' );
        $payment_data = $payment_data_arr[0]->getPaymentData();
        $payment = $payment_data['payment'];
        $created_at = BooklyLib\Slots\DatePoint::fromStr( $payment['created_at'] );
        $helper  = new Helper();
        $codes   = array(
            '{company_address}'   => nl2br( get_option( 'bookly_co_address' ) ),
            '{company_logo}'      => $company_logo ? sprintf( '<img src="%s"/>', esc_attr( $company_logo[0] ) ) : '',
            '{company_name}'      => get_option( 'bookly_co_name' ),
            '{company_phone}'     => get_option( 'bookly_co_phone' ),
            '{company_website}'   => get_option( 'bookly_co_website' ),
            '{client_email}'      => '',
            '{client_first_name}' => strtok( $payment['customer'], ' ' ),
            '{client_last_name}'  => strtok( '' ),
            '{client_name}'       => $payment['customer'],
            '{client_phone}'      => '',
            '{client_address}'    => '',
            '{invoice_number}'    => $payment['id'],
            '{invoice_link}'      => $payment['id'] ? admin_url( 'admin-ajax.php?action=bookly_invoices_download&token=' . $payment['token'] ) : '',
            '{invoice_date}'      => BooklyLib\Utils\DateTime::formatDate( $created_at->format( 'Y-m-d' ) ),
            '{invoice_due_date}'  => BooklyLib\Utils\DateTime::formatDate( $created_at->modify( get_option( 'bookly_invoices_due_days' ) * DAY_IN_SECONDS )->format( 'Y-m-d' ) ),
            '{invoice_due_days}'  => get_option( 'bookly_invoices_due_days' ),
        );

        $time_zone_offset = null;
        $time_zone        = null;
        /** @var BooklyLib\Entities\CustomerAppointment $ca */
        $catemp = $payment['items'][0]; // TODO just consider first appt as customer info.
        //echo "<pre>".print_r($catemp,true)."</pre>";
        $ca = BooklyLib\Entities\CustomerAppointment::find( $catemp['ca_id'] );
        //echo "Test";
        //echo "<pre>".print_r($ca,true)."</pre>";
        if($ca){
        	$custom_fields = json_decode( $ca->getCustomFields(), true );

        //echo "<pre>".print_r($catemp,true)."</pre>";
        //echo "<pre>".print_r('Custom Fields',true)."</pre>";
        //echo "<pre>".print_r($custom_fields,true)."</pre>";

//         $ca = BooklyLib\Entities\CustomerAppointment::query( 'ca' )
//             ->where( 'ca.payment_id', $payment['id'] )
//             ->findOne();
        //echo "<pre>".print_r($ca,true)."</pre>";

        $category_name = '';
        $appointment = BooklyLib\Entities\Appointment::find( $ca->getAppointmentId() );
        if ( $appointment->getServiceId() ) {
            $service = BooklyLib\Entities\Service::find( $appointment->getServiceId() );
            if ( $service->getCategoryId() ) {
                $category = BooklyLib\Entities\Category::find( $service->getCategoryId() );
                if ( $category ) {
                    $category_name = $category->getName();
                }
            }
        }
        }
        //echo "<pre>".print_r($category_name,true)."</pre>";
        if ( $ca ) {
            $time_zone_offset = $ca->getTimeZoneOffset();
            $time_zone        = $ca->getTimeZone();
            /** @var BooklyLib\Entities\Customer $customer */
            $customer = BooklyLib\Entities\Customer::find( $ca->getCustomerId() );
            //echo "<pre>".print_r('Customer Info',true)."</pre>";
           // echo "<pre>".print_r($customer,true)."</pre>";
            if ( $customer ) {
                // Mady M
                //$codes['{client_info}'] = print_r($customer->getInfoFields()); // uncomment to find the custom field ids.
                foreach ( json_decode($customer->getInfoFields()) as $value ) {
                    if($value->id){
                        $codes['{client_info_'+ $value->id +'}'] = $value->value;
                    }
                }
                foreach ( json_decode($ca->getCustomFields()) as $value ) {
                    if($value->id){
                        $codes['{custom_info_'+ $value->id +'}'] = $value->value;
                    }
                }

                // Mady M
                $codes['{client_email}'] = $customer->getEmail();
                $codes['{client_first_name}'] = $customer->getFirstName();
                $codes['{client_last_name}'] = $customer->getLastName();
                $codes['{client_name}'] = $customer->getFullName();
                $codes['{client_phone}'] = $customer->getPhone();
                $codes['{client_address}'] = $customer->getAddress();
                $codes['{client_birthday}'] = $customer->getBirthday() ? BooklyLib\Utils\DateTime::formatDate( $customer->getBirthday() ) : '';
                $codes['{client_note}'] = $customer->getNotes();
                $codes['{po_number}'] = $codes['42021']; // saved on customer profile
                $codes['{bill_to}'] = $category_name; // service category

                $codes['{client_file_number}'] = $codes['80472'];
                $codes['{secondary_email}'] = $codes['86719'];
                $codes['{secondary_phone}'] = $codes['31143'];
                $codes['{international_phone}'] = $codes['27012'];
                $codes['{additional_billinfo}'] = $codes['63135'];
                $codes['{client_file}'] = $codes['16602'];

                //echo "<pre>".print_r($codes,true)."</pre>";

            }
        }

        $show_deposit = BooklyLib\Config::depositPaymentsActive();
        if ( ! $show_deposit ) {
            foreach ( $payment['items'] as $item ) {
                if ( array_key_exists( 'deposit_format', $item ) && $item['deposit_format'] ) {
                    $show_deposit = true;
                    break;
                }
            }
        }

        switch ( $payment_data['payment']['type'] ) {
            case BooklyLib\Entities\Payment::TYPE_PAYPAL:
                $price_correction =  get_option( 'bookly_paypal_increase' ) != 0
                    || get_option( 'bookly_paypal_addition' ) != 0;
                break;
            case BooklyLib\Entities\Payment::TYPE_CLOUD_STRIPE:
                $price_correction =  get_option( 'bookly_cloud_stripe_increase' ) != 0
                    || get_option( 'bookly_cloud_stripe_addition' ) != 0;
                break;
            default:
                $price_correction = Proxy\Shared::paymentSpecificPriceExists( $payment_data['payment']['type'] ) === true;
                break;
        }

        $show = array(
            'coupons' => BooklyLib\Config::couponsActive(),
            'customer_groups' => BooklyLib\Config::customerGroupsActive(),
            'deposit' => (int) $show_deposit,
            'price_correction' => $price_correction,
            'taxes' => (int) ( BooklyLib\Config::taxesActive() || $payment['tax_total'] > 0 ),
        );
        $adjustments = isset( $payment_data['adjustments'] ) ? $payment_data['adjustments'] : array();

        $content = self::renderTemplate( 'invoice_merge', array(
            'helper'           => $helper,
            'codes'            => $codes,
            'payment'          => $payment_data['payment'],
            'payments'         => $payment_data_arr,
            'adjustments'      => $adjustments,
            'show'             => $show,
            'time_zone_offset' => $time_zone_offset,
            'time_zone'        => $time_zone,
        ), false );
        //$mergeContent = array();
        //echo "<pre>".print_r($mergeContent,true)."</pre>";

//         $mergeContent[] = $content;
//         $mergeCodes[] = $codes;
//         foreach ( $payment_data_arr as $payment_data ) {
//             //echo "<pre>".print_r($payment_data->getPaymentData(),true)."</pre>";
//
//             //echo "<pre>".print_r($mergeContent,true)."</pre>";
//         }
        //echo "<pre>".print_r($content,true)."</pre>";
//         strtr( $content, $codes );
        //echo "<pre>".print_r($content,true)."</pre>";
        return strtr( $content, $codes );
    }

    /**
         * Render invoice content.
         *
         * @param array $payment_data
         * @return string
         * @param array $payment_data_arr
         * @return string
         */
    public static function renderMasterInvoice( array $payment_data_arr )
    {
//         echo "<pre>".print_r('render Master Invoice',true)."</pre>";
//         echo "<pre>".print_r($edit)."</pre>";
        //$payment_data = $payment_data ->getPaymentData();
        //var_dump($payment_data);
        //$payment->getPaymentData()
        $company_logo = wp_get_attachment_image_src( get_option( 'bookly_co_logo_attachment_id' ), 'full' );
        $payment_data = $payment_data_arr[0]->getPaymentData();
        $payment = $payment_data['payment'];
        $created_at = BooklyLib\Slots\DatePoint::fromStr( $payment['created_at'] );
        $helper  = new Helper();
        $codes   = array(
            '{company_address}'   => nl2br( get_option( 'bookly_co_address' ) ),
            '{company_logo}'      => $company_logo ? sprintf( '<img src="%s"/>', esc_attr( $company_logo[0] ) ) : '',
            '{company_name}'      => get_option( 'bookly_co_name' ),
            '{company_phone}'     => get_option( 'bookly_co_phone' ),
            '{company_website}'   => get_option( 'bookly_co_website' ),
            '{client_email}'      => '',
            '{client_first_name}' => strtok( $payment['customer'], ' ' ),
            '{client_last_name}'  => strtok( '' ),
            '{client_name}'       => $payment['customer'],
            '{client_phone}'      => '',
            '{client_address}'    => '',
            '{invoice_number}'    => $payment['id'],
            '{invoice_link}'      => $payment['id'] ? admin_url( 'admin-ajax.php?action=bookly_invoices_download&token=' . $payment['token'] ) : '',
            '{invoice_date}'      => BooklyLib\Utils\DateTime::formatDate( $created_at->format( 'Y-m-d' ) ),
            '{invoice_due_date}'  => BooklyLib\Utils\DateTime::formatDate( $created_at->modify( get_option( 'bookly_invoices_due_days' ) * DAY_IN_SECONDS )->format( 'Y-m-d' ) ),
            '{invoice_due_days}'  => get_option( 'bookly_invoices_due_days' ),
        );

        $time_zone_offset = null;
        $time_zone        = null;
        /** @var BooklyLib\Entities\CustomerAppointment $ca */
        $catemp = $payment['items'][0]; // TODO just consider first appt as customer info.
        //echo "<pre>".print_r($catemp,true)."</pre>";
        $ca = BooklyLib\Entities\CustomerAppointment::find( $catemp['ca_id'] );
//         $ca = BooklyLib\Entities\CustomerAppointment::query( 'ca' )
//             ->where( 'ca.payment_id', $payment['id'] )
//             ->findOne();
            //echo "<pre>".print_r($ca,true)."</pre>";
        $category_name = '';
        $appointment = BooklyLib\Entities\Appointment::find( $ca->getAppointmentId() );
        if ( $appointment->getServiceId() ) {
            $service = BooklyLib\Entities\Service::find( $appointment->getServiceId() );
            if ( $service->getCategoryId() ) {
                $category = BooklyLib\Entities\Category::find( $service->getCategoryId() );
                if ( $category ) {
                    $category_name = $category->getName();
                }
            }
        }
        if ( $ca ) {
            $time_zone_offset = $ca->getTimeZoneOffset();
            $time_zone        = $ca->getTimeZone();
            /** @var BooklyLib\Entities\Customer $customer */
            $customer = BooklyLib\Entities\Customer::find( $ca->getCustomerId() );
//             echo "<pre>".print_r('Customer Info',true)."</pre>";
//             echo "<pre>".print_r($customer,true)."</pre>";
                //echo "<pre>".print_r(json_decode($ca->getCustomFields()),true)."</pre>";
            if ( $customer ) {
                // Mady M
                //$codes['{client_info}'] = print_r($customer->getInfoFields()); // uncomment to find the custom field ids.
                foreach ( json_decode($customer->getInfoFields()) as $value ) {
                    if($value->id){
                        $codes['{client_info_'+ $value->id +'}'] = $value->value;
                    }
                }
                foreach ( json_decode($ca->getCustomFields()) as $value ) {
                    if($value->id){
                        $codes['{custom_info_'+ $value->id +'}'] = $value->value;
                    }
                }

                // Mady M
                $codes['{client_email}'] = $customer->getEmail();
                $codes['{client_first_name}'] = $customer->getFirstName();
                $codes['{client_last_name}'] = $customer->getLastName();
                $codes['{client_name}'] = $customer->getFullName();
                $codes['{client_phone}'] = $customer->getPhone();
                $codes['{client_address}'] = $customer->getAddress();
                $codes['{client_birthday}'] = $customer->getBirthday() ? BooklyLib\Utils\DateTime::formatDate( $customer->getBirthday() ) : '';
                $codes['{client_note}'] = $customer->getNotes();
                $codes['{po_number}'] = $codes['42021']; // saved on customer profile
                $codes['{bill_to}'] = $category_name; // service category

                $codes['{client_file_number}'] = $codes['80472'];
                $codes['{secondary_email}'] = $codes['86719'];
                $codes['{secondary_phone}'] = $codes['31143'];
                $codes['{international_phone}'] = $codes['27012'];
                $codes['{additional_billinfo}'] = $codes['63135'];
                $codes['{client_file}'] = $codes['16602'];

                //echo "<pre>".print_r($codes,true)."</pre>";
            }
        }

        $show_deposit = BooklyLib\Config::depositPaymentsActive();
        if ( ! $show_deposit ) {
            foreach ( $payment['items'] as $item ) {
                if ( array_key_exists( 'deposit_format', $item ) && $item['deposit_format'] ) {
                    $show_deposit = true;
                    break;
                }
            }
        }

        switch ( $payment_data['payment']['type'] ) {
            case BooklyLib\Entities\Payment::TYPE_PAYPAL:
                $price_correction =  get_option( 'bookly_paypal_increase' ) != 0
                    || get_option( 'bookly_paypal_addition' ) != 0;
                break;
            case BooklyLib\Entities\Payment::TYPE_CLOUD_STRIPE:
                $price_correction =  get_option( 'bookly_cloud_stripe_increase' ) != 0
                    || get_option( 'bookly_cloud_stripe_addition' ) != 0;
                break;
            default:
                $price_correction = Proxy\Shared::paymentSpecificPriceExists( $payment_data['payment']['type'] ) === true;
                break;
        }

        $show = array(
            'coupons' => BooklyLib\Config::couponsActive(),
            'customer_groups' => BooklyLib\Config::customerGroupsActive(),
            'deposit' => (int) $show_deposit,
            'price_correction' => $price_correction,
            'taxes' => (int) ( BooklyLib\Config::taxesActive() || $payment['tax_total'] > 0 ),
        );
        $adjustments = isset( $payment_data['adjustments'] ) ? $payment_data['adjustments'] : array();

        $content = self::renderTemplate( 'master_invoice', array(
            'helper'           => $helper,
            'codes'            => $codes,
            'payment'          => $payment_data['payment'],
            'payments'         => $payment_data_arr,
            'adjustments'      => $adjustments,
            'show'             => $show,
            'time_zone_offset' => $time_zone_offset,
            'time_zone'        => $time_zone,
        ), false );
        //$mergeContent = array();
        //echo "<pre>".print_r($mergeContent,true)."</pre>";

//         $mergeContent[] = $content;
//         $mergeCodes[] = $codes;
//         foreach ( $payment_data_arr as $payment_data ) {
//             //echo "<pre>".print_r($payment_data->getPaymentData(),true)."</pre>";
//
//             //echo "<pre>".print_r($mergeContent,true)."</pre>";
//         }
//         echo "<pre>".print_r($content,true)."</pre>";
//         echo "<pre>".print_r($codes,true)."</pre>";
//         strtr( $content, $codes );


         return strtr( $content, $codes );

    }

    /**
     * Render editable template for invoice.
     *
     * @return string|void
     */
    public static function appearance()
    {
        wp_enqueue_media();

        self::enqueueScripts( array(
            'module' => array(
                'js/invoice-appearance.js' => array( 'jquery' ),
            ),
        ) );

        wp_localize_script( 'bookly-invoice-appearance.js', 'BooklyInvoicesL10n', array(
            'invalid_due_days' => __( 'Invoice due days: Please enter value in days from 1 to 365', 'bookly' ),
        ) );

        $helper = new Helper( 'editable' );
        $codes_list = new Codes();
        $codes = json_encode( $codes_list->getGroups( array( 'customer', 'invoice', 'company' ) ) );

        return self::renderTemplate( 'invoice', compact( 'helper', 'codes' ), false );
    }
}