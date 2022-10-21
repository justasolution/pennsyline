<?php
namespace BooklyStaffCabinet\Frontend\Modules\StaffAdvanced;

use Bookly\Lib as BooklyLib;
use BooklyPro\Lib as BooklyProLib;
use BooklyStaffCabinet\Lib;
use BooklyStaffCabinet\Frontend\Components as FrontendComponents;

/**
 * Class ShortCode
 * @package BooklyStaffCabinet\Frontend\Modules\StaffAdvanced
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-staff-advanced', array( __CLASS__, 'render' ) );

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
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-advanced' )
        ) {
            self::enqueueStyles( array(
                'frontend' => array( 'css/staff-cabinet.css' => array( 'bookly-backend-globals' ), ),
            ) );
        }
    }

    /**
     * Link scripts.
     */
    public static function linkScripts()
    {
        if (
            get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ||
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-advanced' )
        ) {
            $src = plugins_url( 'backend/components/dialogs/staff/edit/resources/js/staff-advanced.js', BooklyProLib\Plugin::getBasename() );
            wp_register_script( 'bookly-staff-advanced.js', $src, array( 'bookly-backend-globals' ), BooklyProLib\Plugin::getVersion() );

            self::enqueueScripts( array(
                'frontend' => array( 'js/staff-cabinet-native.js' ),
                'module' => array( 'js/staff-cabinet-advanced.js' => array( 'bookly-staff-advanced.js' ), ),
            ) );

            wp_localize_script( 'bookly-staff-cabinet-advanced.js', 'BooklySCAdvancedL10n', array(
                'saved' => __( 'Settings saved.', 'bookly' ),
                'zoomFailed' => __( 'Zoom connection failed', 'bookly' ),
                'zoomOAuthConnectRequired' => __( 'Zoom: OAuth2.0 connection needed', 'bookly' )
            ) );
        }
    }

    /**
     * Render Staff Details shortcode.
     *
     * @param array $attributes
     * @return string
     */
    public static function render( $attributes )
    {
        // Disable caching.
        BooklyLib\Utils\Common::noCache();

        if ( is_user_logged_in() && $staff = BooklyLib\Entities\Staff::query()->where( 'wp_user_id', get_current_user_id() )->findOne() ) {
            \BooklyPro\Backend\Components\License\Components::renderLicenseNotice( false );
            if ( $staff->isArchived() ) {
                return FrontendComponents\Notice\Permission::generateAccountDisabled();
            }
            if ( \BooklyPro\Lib\Config::graceExpired() ) {
                return;
            } else {
                $form_id = uniqid( 'advanced-' );
                $calendars = array();
                $selected_calendar_id = null;
                if ( $staff->getGoogleData() != '' ) {
                    $google = new BooklyProLib\Google\Client();
                    if ( $google->auth( $staff, true ) && ( $list = $google->getCalendarList() ) !== false ) {
                        $calendars = $list;
                        $selected_calendar_id = $google->data()->calendar->id;
                    }
                }

                return self::renderTemplate( 'short_code', compact( 'form_id', 'staff', 'calendars', 'selected_calendar_id' ), false );
            }
        }

        return FrontendComponents\Notice\Permission::generate();
    }
}