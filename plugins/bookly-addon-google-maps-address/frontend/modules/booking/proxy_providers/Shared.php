<?php
namespace BooklyGoogleMapsAddress\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Frontend\Modules\Booking\Proxy;

/**
 * Class Shared
 * @package BooklyGoogleMapsAddress\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function booklyFormOptions( array $bookly_options )
    {
        $bookly_options['google_maps'] = array(
            'enabled' => (int) ( get_option( 'bookly_google_maps_address_enabled' ) && get_option( 'bookly_google_api_key' ) ),
            'api_key' => get_option( 'bookly_google_api_key' ),
        );

        return $bookly_options;
    }
}