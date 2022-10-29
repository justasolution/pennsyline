<?php
namespace BooklyGoogleMapsAddress\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Shared
 * @package BooklyGoogleMapsAddress\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{

    /**
     * Prepare appearance options.
     *
     * @param array $options_to_save
     * @param array $options
     * @return array
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        return array_merge( $options_to_save, array_intersect_key( $options, array_flip( array (
            'bookly_l10n_label_google_maps',
            'bookly_google_maps_address_enabled',
        ) ) ) );
    }
}