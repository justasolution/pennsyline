<?php
namespace BooklyInvoices\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;

/**
 * Class Shared
 * @package BooklyInvoices\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['invoice_date']     = '';
        $replace_codes['invoice_due_days'] = get_option( 'bookly_invoices_due_days' );
        $replace_codes['invoice_due_date'] = '';
        $replace_codes['invoice_link']     = '';
        $replace_codes['invoice_number']   = $codes->invoice_number;
        if ( $codes->invoice_number ) {
            $payment = BooklyLib\Entities\Payment::find( $codes->invoice_number );
            if ( $payment ) {
                $created_at = BooklyLib\Slots\DatePoint::fromStr( $payment->getCreatedAt() );
                $replace_codes['invoice_date']     = BooklyLib\Utils\DateTime::formatDate( $created_at->format( 'Y-m-d' ) );
                $replace_codes['invoice_due_date'] = BooklyLib\Utils\DateTime::formatDate( $created_at->modify( $replace_codes['invoice_due_days'] * DAY_IN_SECONDS )->format( 'Y-m-d' ) );
                $replace_codes['invoice_link']     = admin_url( 'admin-ajax.php?action=bookly_invoices_download&token=' . $payment->getToken() );
            }
        } else {
            // Test data
            $replace_codes['invoice_date']     = $codes->invoice_date;
            $replace_codes['invoice_due_date'] = $codes->invoice_due_date;
        }

        return $replace_codes;
    }
}