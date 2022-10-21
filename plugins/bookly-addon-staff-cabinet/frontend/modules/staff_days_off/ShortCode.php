<?php
namespace BooklyStaffCabinet\Frontend\Modules\StaffDaysOff;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Modules\Staff\Proxy;
use BooklyStaffCabinet\Lib;
use BooklyStaffCabinet\Frontend\Components as FrontendComponents;

/**
 * Class ShortCode
 * @package BooklyStaffCabinet\Frontend\Modules\StaffDaysOff
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-staff-days-off', array( __CLASS__, 'render' ) );

        // Assets.
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'linkStyles' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'linkScripts' ) );
    }

    /**
     * Link styles.
     */
    public static function linkStyles()
    {
        if (
            get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ||
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-days-off' )
        ) {
            self::enqueueStyles( array(
                'bookly' => array( 'backend/resources/css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
            ) );
            Proxy\Shared::enqueueStaffProfileStyles();
        }
    }

    /**
     * Link scripts.
     */
    public static function linkScripts()
    {
        /** @var \WP_Locale $wp_locale */
        global $wp_locale;

        if (
            get_current_user_id()
            && (
                get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ||
                BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-days-off' )
            )
        ) {
            $staff = BooklyLib\Entities\Staff::query()->select( 'id, visibility' )->where( 'wp_user_id', get_current_user_id() )->findOne();
            if ( $staff ) {
                self::enqueueScripts( array(
                    'bookly' => array(
                        'backend/resources/js/jCal.js' => array( 'jquery' ),
                        'backend/components/dialogs/staff/edit/resources/js/staff-days-off.js' => array( 'bookly-backend-globals' )
                    ),
                    'module' => array( 'js/staff-cabinet-days-off.js' => array( 'bookly-staff-days-off.js' ) ),
                ) );

                Proxy\Shared::enqueueStaffProfileScripts();

                wp_localize_script( 'bookly-staff-cabinet-days-off.js', 'BooklySCDaysOffL10n', array(
                    'holidays'    => $staff->getHolidays(),
                    'loading_img' => plugins_url( 'bookly-responsive-appointment-booking-tool/backend/resources/images/loading.gif' ),
                    'firstDay'    => (int) get_option( 'start_of_week' ),
                    'days'        => array_values( $wp_locale->weekday_abbrev ),
                    'months'      => array_values( $wp_locale->month ),
                    'close'       => __( 'Close', 'bookly' ),
                    'repeat'      => __( 'Repeat every year', 'bookly' ),
                    'we_are_not_working' => __( 'We are not working on this day', 'bookly' ),
                ) );
            }
        }
    }

    /**
     * Render Staff Days Off shortcode.
     *
     * @param array $attributes
     * @return string
     */
    public static function render( $attributes )
    {
        BooklyLib\Utils\Common::noCache();

        if ( is_user_logged_in() && $staff = BooklyLib\Entities\Staff::query()->select( 'id, visibility' )->where( 'wp_user_id', get_current_user_id() )->fetchRow() ) {
            \BooklyPro\Backend\Components\License\Components::renderLicenseNotice( false );
            if ( $staff['visibility'] == 'archive' ) {
                return FrontendComponents\Notice\Permission::generateAccountDisabled();
            }
            return \BooklyPro\Lib\Config::graceExpired()
                ? null
                : self::renderTemplate( 'short_code', array( 'form_id' => uniqid( 'days-off-' ), 'staff_id' => $staff['id'], 'attributes' => $attributes ), false );
        }

        return FrontendComponents\Notice\Permission::generate();
    }
}