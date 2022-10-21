<?php
namespace BooklyLocations\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['appointment']['location_info'] = array( 'description' => __( 'Location info', 'bookly' ), 'if' => true );
        $codes['appointment']['location_name'] = array( 'description' => __( 'Location name', 'bookly' ) );

        $codes['location']['location_info'] = array( 'description' => __( 'Location info', 'bookly' ), 'if' => true );
        $codes['location']['location_name'] = array( 'description' => __( 'Location name', 'bookly' ) );

        return $codes;
    }
}