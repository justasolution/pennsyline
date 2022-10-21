<?php
namespace BooklyInvoices\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/***
 * Class Shared
 * @package BooklyInvoices\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        return array_merge( $options_to_save, array_intersect_key( $options, array_flip( array (
            'bookly_invoices_show_download_invoice',
            'bookly_l10n_button_download_invoice',
        ) ) ) );
    }
}