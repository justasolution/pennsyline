<?php
namespace BooklyLocations\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Backend\Components\Settings\Menu;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Settings\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderMenuItem()
    {
        Menu::renderItem( __( 'Locations', 'bookly' ), 'locations' );
    }

    /**
     * @inheritDoc
     */
    public static function renderTab()
    {
        self::renderTemplate( 'settings_tab' );
    }

    /**
     * @inheritDoc
     */
    public static function saveSettings( array $alert, $tab, array $params )
    {
        if ( $tab == 'locations' ) {
            $options = array( 'bookly_locations_allow_services_per_location' );
            foreach ( $options as $option_name ) {
                if ( array_key_exists( $option_name, $params ) ) {
                    update_option( $option_name, $params[ $option_name ] );
                }
            }
            $alert['success'][] = __( 'Settings saved.', 'bookly' );
        }

        return $alert;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCalendarAppointmentCodes( array $codes, $participants )
    {
        $codes['location_info'] = __( 'Location info', 'bookly' );
        $codes['location_name'] = __( 'Location name', 'bookly' );

        return $codes;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCodes( array $codes, $section )
    {
        switch ( $section ) {
            case 'calendar_one_participant' :
            case 'calendar_many_participants' :
            case 'woocommerce' :
            case 'google_calendar' :
            case 'outlook_calendar' :
                $codes['location_info'] = array( 'description' => __( 'Location info', 'bookly' ), 'if' => true );
                $codes['location_name'] = array( 'description' => __( 'Location name', 'bookly' ) );
                break;
        }

        return $codes;
    }
}