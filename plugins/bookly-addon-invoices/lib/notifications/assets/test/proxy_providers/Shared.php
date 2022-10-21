<?php
namespace BooklyInvoices\Lib\Notifications\Assets\Test\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\Notifications\Assets\Test\Codes;
use Bookly\Lib\Notifications\Assets\Test\Proxy;

/**
 * Class Shared
 * @package BooklyInvoices\Lib\Notifications\Assets\Test\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $created = BooklyLib\Slots\DatePoint::now();

        $codes->invoice_date     = BooklyLib\Utils\DateTime::formatDate( $created->format( 'Y-m-d' ) );
        $codes->invoice_due_date = BooklyLib\Utils\DateTime::formatDate( $created->modify( get_option( 'bookly_invoices_due_days' ) * DAY_IN_SECONDS )->format( 'Y-m-d' ) );
        $codes->invoice_number   = '0';

        return $codes;
    }
}