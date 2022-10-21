<?php
namespace BooklyInvoices\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Local
 * @package BooklyInvoices\Backend\Modules\Appearance\ProxyProviders
 */
class Local extends Proxy\Invoices
{
    /**
     * @inheritDoc
     */
    public static function renderDownloadInvoice()
    {
        self::renderTemplate( 'button' );
    }

    /**
     * @inheritDoc
     */
    public static function renderShowDownloadInvoice()
    {
        self::renderTemplate( 'show_download_invoice' );
    }
}