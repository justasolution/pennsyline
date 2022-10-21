<?php
namespace BooklyLocations\Backend\Components\Dialogs\Staff\Edit\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use BooklyLocations\Lib;

/**
 * Class Local
 * @package BooklyLocations\Backend\Components\Dialogs\Staff\Edit\ProxyProviders
 */
class Local extends Proxy\Locations
{
    /**
     * @inheritDoc
     */
    public static function renderLocationSwitcher( $staff_id, $location_id, $type = 'custom_services' )
    {
        if ( get_option( 'bookly_locations_allow_services_per_location' ) ) {
            $staff_locations = BooklyLib\Entities\Staff::query( 's' )
                ->select( 'l.id, l.name' )
                ->leftJoin( 'StaffLocation', 'sl', 'sl.staff_id = s.id', '\BooklyLocations\Lib\Entities' )
                ->leftJoin( 'Location', 'l', 'sl.location_id = l.id', '\BooklyLocations\Lib\Entities' )
                ->where( 'id', $staff_id )
                ->fetchArray();
            if ( count( $staff_locations ) && $staff_locations[0]['id'] ) {
                $custom_settings = Lib\Entities\StaffLocation::query()
                    ->where( 'staff_id', $staff_id )
                    ->where( 'location_id', $location_id )
                    ->fetchVar( $type );

                self::renderTemplate( 'location_switcher', compact( 'staff_locations', 'location_id', 'custom_settings' ) );
            }
        }
    }
}