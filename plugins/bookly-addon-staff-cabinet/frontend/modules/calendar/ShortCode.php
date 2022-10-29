<?php
namespace BooklyStaffCabinet\Frontend\Modules\Calendar;

use Bookly\Lib as BooklyLib;
use BooklyStaffCabinet\Lib;
use BooklyStaffCabinet\Frontend\Components as FrontendComponents;

/**
 * Class Page
 * @package BooklyStaffCabinet\Frontend\Modules\Calendar
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-staff-calendar', array( __CLASS__, 'render' ) );

        // Assets.
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'linkStyles' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'linkScripts' ) );
    }

    /**
     * Link styles.
     */
    public static function linkStyles()
    {
        if ( get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' || self::postsHaveShortCode() ) {
            self::enqueueStyles( array(
                'bookly' => array( 'backend/modules/calendar/resources/css/event-calendar.min.css' => array( 'bookly-backend-globals' ) ),
                'frontend' => array( 'css/staff-cabinet.css' => array( 'bookly-event-calendar.min.css' ), ),
                'module' => array( 'css/staff-cabinet-calendar.css' => array( 'bookly-staff-cabinet.css' ), )
            ) );
        }
    }

    /**
     * Link scripts.
     */
    public static function linkScripts()
    {
        // Disable emoji in IE11
        if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/7.0' ) !== false ) {
            if ( self::postsHaveShortCode() ) {
                BooklyLib\Utils\Common::disableEmoji();
            }
        }

        if ( get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' || self::postsHaveShortCode() ) {
            self::enqueueScripts( array(
                'bookly' => array(
                    'backend/modules/calendar/resources/js/event-calendar.min.js' => array( 'bookly-backend-globals' ),
                    'backend/modules/calendar/resources/js/calendar-common.js' => array( 'bookly-event-calendar.min.js' ),
                ),
                'module' => array( 'js/staff-cabinet-calendar.js' => array( 'bookly-calendar-common.js' ) ),
            ) );
            wp_localize_script( 'bookly-staff-cabinet-calendar.js', 'BooklySCCalendarL10n', array_merge(
                BooklyLib\Utils\Common::getCalendarSettings(),
                array(
                    'mjsDateFormat' => BooklyLib\Utils\DateTime::convertFormat( 'date', BooklyLib\Utils\DateTime::FORMAT_MOMENT_JS ),
                    'delete' => __( 'Delete', 'bookly' ),
                    'areYouSure' => __( 'Are you sure?', 'bookly' ),
                    'recurring_appointments' => array(
                        'active' => (int) BooklyLib\Config::recurringAppointmentsActive(),
                        'title' => __( 'Recurring appointments', 'bookly' ),
                    ),
                    'waiting_list' => array(
                        'active' => (int) BooklyLib\Config::waitingListActive(),
                        'title' => __( 'On waiting list', 'bookly' ),
                    ),
                    'packages' => array(
                        'active' => (int) BooklyLib\Config::packagesActive(),
                        'title' => __( 'Package', 'bookly' ),
                    ),
                ) ) );
        }
    }

    /**
     * Render Calendar shortcode.
     *
     * @param array $attributes
     * @return string
     */
    public static function render( $attributes )
    {
        // Disable caching.
        BooklyLib\Utils\Common::noCache();

        if ( is_user_logged_in() && $staff = BooklyLib\Entities\Staff::query()->select( 'id, visibility' )->where( 'wp_user_id', get_current_user_id() )->fetchRow() ) {
            $custom_fields = array();
            foreach ( (array) BooklyLib\Proxy\CustomFields::getTranslated() as $field ) {
                if ( ! in_array( $field->type, array( 'captcha', 'text-content' ) ) ) {
                    $custom_fields[] = $field;
                }
            }

            \BooklyPro\Backend\Components\License\Components::renderLicenseNotice( false );
            if ( $staff['visibility'] == 'archive' ) {
                return FrontendComponents\Notice\Permission::generateAccountDisabled();
            }
            return \BooklyPro\Lib\Config::graceExpired()
                ? null
                : self::renderTemplate( 'short_code', array(
                    'calendar_id' => uniqid( 'calendar-' ),
                    'staff_id' => $staff['id'],
                    'custom_fields' => $custom_fields,
                    'refresh_rate' => get_user_meta( get_current_user_id(), 'bookly_calendar_refresh_rate', true ),
                    'hide' => array_key_exists( 'hide', (array) $attributes ) ? (array) $attributes['hide'] : array(),
                    'read_only' => (bool) ( array_key_exists( 'read-only', (array) $attributes ) ? $attributes['read-only'] : false ),
                ), false );
        }

        return FrontendComponents\Notice\Permission::generate();
    }

    /**
     * Check whether current posts have shortcode 'bookly-staff-calendar'
     *
     * @return bool
     */
    protected static function postsHaveShortCode()
    {
        static $result;

        if ( $result === null ) {
            $result = BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-calendar' );
        }

        return $result;
    }
}