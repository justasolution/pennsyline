<?php
namespace BooklyLocations\Frontend\Modules\ModernBookingForm\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\ModernBookingForm\Proxy;
use BooklyPackages\Lib;

/**
 * Class Shared
 *
 * @package BooklyLocations\Frontend\Modules\ModernBookingForm\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inerhitDoc
     */
    public static function prepareAppearance( array $bookly_options )
    {
        $bookly_options['l10n']['location'] = __( 'Location', 'bookly' );
        $bookly_options['l10n']['select_location'] = __( 'Any', 'bookly' );

        return $bookly_options;
    }
}