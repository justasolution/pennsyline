<?php
namespace BooklyInvoices\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Frontend\Modules\Booking\Proxy;

/**
 * Class Local
 * @package BooklyInvoices\Frontend\Modules\Booking\ProxyProviders
 */
class Local extends Proxy\Invoices
{
    /**
     * @inheritDoc
     */
    public static function getDownloadButton()
    {
        return get_option( 'bookly_invoices_show_download_invoice' )
            ? self::renderTemplate( 'button', array(), false )
            : null;
    }
}