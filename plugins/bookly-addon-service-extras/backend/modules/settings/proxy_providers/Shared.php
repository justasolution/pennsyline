<?php
namespace BooklyServiceExtras\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Backend\Components\Settings\Menu;
use Bookly\Backend\Components\Settings\Selects;

/**
 * Class Shared
 * @package BooklyServiceExtras\Backend\Modules\Settings\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderMenuItem()
    {
        Menu::renderItem( __( 'Service Extras', 'bookly' ), 'service_extras' );
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
    public static function prepareCalendarAppointmentCodes( array $codes, $participants )
    {
        if ( $participants == 'one' ) {
            $codes['extras'] = __( 'Extras titles', 'bookly' );
            $codes['extras_total_price'] = __( 'Extras total price', 'bookly' );
        }

        return $codes;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCodes( array $codes, $section )
    {
        switch ( $section ) {
            case 'calendar_one_participant' :
                $codes['extras'] = array( 'description' => __( 'Extras titles', 'bookly' ) );
                $codes['extras_total_price'] = array( 'description' => __( 'Extras total price', 'bookly' ) );
                break;
            case 'woocommerce':
                $codes['extras'] = array( 'description' => __( 'Extras titles', 'bookly' ) );
                break;
            case 'google_calendar':
            case 'outlook_calendar':
                $codes = array_merge_recursive( $codes, array(
                    'participants' => array(
                        'loop' => array(
                            'codes' => array(
                                'extras' => array( 'description' => __( 'Extras titles', 'bookly' ) ),
                                'extras_total_price' => array( 'description' => __( 'Extras total price', 'bookly' ) ),
                            ),
                        ),
                    ),
                ) );
                break;
        }

        return $codes;
    }

    /**
     * @inheritDoc
     */
    public static function saveSettings( array $alert, $tab, array $params )
    {
        if ( $tab == 'service_extras' ) {
            if ( ! array_key_exists( 'bookly_service_extras_show', $params ) ) {
                $params['bookly_service_extras_show'] = array();
            }
            $options = array( 'bookly_service_extras_multiply_nop', 'bookly_service_extras_show', 'bookly_service_extras_after_step_time' );
            foreach ( $options as $option_name ) {
                if ( array_key_exists( $option_name, $params ) ) {
                    update_option( $option_name, $params[ $option_name ] );
                }
            }
            $alert['success'][] = __( 'Settings saved.', 'bookly' );
        }

        return $alert;
    }
}