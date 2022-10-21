<?php

namespace BooklyInvoices\Backend\Components\Invoice;

use BooklyInvoices\Lib\Plugin;
use Bookly\Lib as BooklyLib;
use BooklyInvoices\Backend\Modules\Settings\Lib\Helper;

/**
 * Class Ajax
 *
 * @package BooklyInvoices\Backend\Components\Invoice
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * Render pdf invoice sample
     */
    public static function preview()
    {
        include_once Plugin::getDirectory() . '/lib/TCPDF/tcpdf.php';

        $pdf = new \TCPDF();
        $pdf->setPrintHeader( false );
        $pdf->setPrintFooter( false );
        $pdf->AddPage();
        $date = BooklyLib\Slots\DatePoint::now()->modify( - MONTH_IN_SECONDS );

        $payment = array(
            'id' => '',
            'status' => '',
            'target' => BooklyLib\Entities\Payment::TARGET_APPOINTMENTS,
            'type' => BooklyLib\Entities\Payment::TYPE_PAYPAL,
            'coupon' => null,
            'created_at' => '',
            'paid' => 1100.05,
            'total' => 1100.05,
            'customer' => '',
            'items' => array(
                array(
                    'ca_id' => '',
                    'appointment_date' => $date->format( 'Y-m-d 12:00:00' ),
                    'service_name' => 'Crown and Bridge',
                    'service_price' => 350,
                    'service_tax' => 140,
                    'deposit_format' => '$700.00 (100%)',
                    'number_of_persons' => '2',
                    'staff_name' => 'Nick Knight',
                    'extras' => array(),
                ),
                array(
                    'ca_id' => '',
                    'appointment_date' => BooklyLib\Utils\DateTime::formatDateTime( $date->modify( DAY_IN_SECONDS )->format( 'Y-m-d 18:00:00' ) ),
                    'service_name' => 'Invisalign (invisable braces)',
                    'service_price' => 375,
                    'service_tax' => 37.5,
                    'deposit_format' => '$375.00 (100%)',
                    'number_of_persons' => '1',
                    'staff_name' => 'Jane Howard',
                    'extras' => array(),
                ),
            ),
            'subtotal' => array(
                'price' => 1075,
                'deposit' => 1075,
            ),
            'tax_paid' => 177.50,
            'tax_total' => 177.50,
            'tax_in_price' => 'included',
            'price_correction' => 25.05,
            'group_discount' => false,
        );

        if ( ! BooklyLib\Config::taxesActive() ) {
            $payment['tax_total'] = 0;
        }

        $helper = new Helper();
        $show = array(
            'coupons' => BooklyLib\Config::couponsActive(),
            'customer_groups' => BooklyLib\Config::customerGroupsActive(),
            'deposit' => (int) BooklyLib\Config::depositPaymentsActive(),
            'gateway' => 1,
            'taxes' => (int) ( BooklyLib\Config::taxesActive() || $payment['tax_total'] > 0 ),
            'discounts' => (int) ( BooklyLib\Config::discountsActive() ),
        );

        $content = self::renderTemplate( 'invoice', array( 'helper' => $helper, 'codes' => '', 'payment' => $payment, 'show' => $show, 'adjustments' => array() ), false );

        $company_logo = wp_get_attachment_image_src( get_option( 'bookly_co_logo_attachment_id' ), 'full' );

        $now = BooklyLib\Slots\DatePoint::now();
        $codes = array(
            '{company_address}' => nl2br( get_option( 'bookly_co_address' ) ),
            '{company_logo}' => $company_logo ? sprintf( '<img src="%s"/>', esc_attr( $company_logo[0] ) ) : '',
            '{company_name}' => get_option( 'bookly_co_name' ),
            '{company_phone}' => get_option( 'bookly_co_phone' ),
            '{company_website}' => get_option( 'bookly_co_website' ),
            '{client_email}' => 'client@example.com',
            '{client_first_name}' => 'Client',
            '{client_last_name}' => 'Name',
            '{client_name}' => 'Client Name',
            '{client_phone}' => '+12025550107',
            '{client_address}' => 'Client address',
            '{invoice_number}' => '321',
            '{invoice_date}' => BooklyLib\Utils\DateTime::formatDate( $now->format( 'Y-m-d' ) ),
            '{invoice_due_date}' => BooklyLib\Utils\DateTime::formatDate( $now->modify( get_option( 'bookly_invoices_due_days' ) * DAY_IN_SECONDS )->format( 'Y-m-d' ) ),
            '{invoice_due_days}' => get_option( 'bookly_invoices_due_days' ),
            '{location_info}' => 'Location info',
            '{location_name}' => 'Location name',
        );
        $font_name = get_option( 'bookly_invoices_font_name' );
        $font_size = $font_name === 'freesans' ? 12 : 8;
        $pdf->setImageScale( 2.3 );
        $pdf->SetFont( $font_name, '', $font_size );
        $pdf->writeHTML( strtr( $content, $codes ) );
        $pdf->SetTextColor( 127 );
        $pdf->SetFont( $font_name, '', 100 );
        $pdf->Rotate( 60, 150, 150 );
        $pdf->Text( 70, 70, 'SAMPLE' );
        $pdf->SetTextColor( 207 );
        $pdf->SetFont( $font_name, '', 100 );
        $pdf->Text( 69, 69, 'SAMPLE' );
        $pdf->StopTransform();

        $pdf->Output( 'sample.pdf', 'D' );
        exit();
    }
}