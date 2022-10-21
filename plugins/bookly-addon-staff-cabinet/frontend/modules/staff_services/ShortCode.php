<?php
namespace BooklyStaffCabinet\Frontend\Modules\StaffServices;

use Bookly\Backend\Components\Dialogs\Staff\Edit\Forms\StaffServices;
use Bookly\Lib as BooklyLib;
use Bookly\Backend\Modules\Staff\Proxy;
use BooklyStaffCabinet\Lib;
use BooklyStaffCabinet\Frontend\Components as FrontendComponents;

/**
 * Class Page
 * @package BooklyStaffCabinet\Frontend\Modules\StaffServices
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-staff-services', array( __CLASS__, 'render' ) );

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
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-services' )
        ) {
            self::enqueueStyles( array(
                'frontend' => array( 'css/staff-cabinet.css' => array( 'bookly-backend-globals' ), ),
            ) );
            Proxy\Shared::enqueueStaffProfileStyles();
        }
    }

    /**
     * Link scripts.
     */
    public static function linkScripts()
    {
        if (
            get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ||
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-services' )
        ) {
            self::enqueueScripts( array(
                'bookly' => array(
                    'backend/resources/js/range-tools.js' => array( 'bookly-backend-globals' ),
                    'backend/components/dialogs/staff/edit/resources/js/staff-services.js' => array( 'bookly-range-tools.js' ),
                ),
                'frontend' => array( 'js/staff-cabinet-native.js' ),
                'module' => array( 'js/staff-cabinet-services.js' => array( 'bookly-staff-services.js', 'bookly-staff-cabinet-native.js' ) ),
            ) );
            Proxy\Shared::enqueueStaffProfileScripts();

            wp_localize_script( 'bookly-staff-cabinet-services.js', 'BooklySCServicesL10n', array(
                'capacity_error'    => __( 'Min capacity should not be greater than max capacity.', 'bookly' ),
                'saved'             => __( 'Settings saved.', 'bookly' ),
            ) );
        }
    }

    /**
     * Render Staff Services shortcode.
     *
     * @param array $attributes
     * @return string
     */
    public static function render( $attributes )
    {
        // Disable caching.
        BooklyLib\Utils\Common::noCache();

        if ( is_user_logged_in() && $staff = BooklyLib\Entities\Staff::query()->select( 'id, visibility' )->where( 'wp_user_id', get_current_user_id() )->fetchRow() ) {
            \BooklyPro\Backend\Components\License\Components::renderLicenseNotice( false );
            if ( $staff['visibility'] == 'archive' ) {
                return FrontendComponents\Notice\Permission::generateAccountDisabled();
            }
            if ( \BooklyPro\Lib\Config::graceExpired() ) {
                return;
            } else {
                $attrs = array();
                if ( $attributes ) {
                    foreach ( (array) $attributes as $key => $value ) {
                        $attrs[ $key ] = array_fill_keys( explode( ',', $value ), true );
                    }
                }
                $form = new StaffServices();
                $form->load( $staff['id'] );

                return self::renderTemplate( 'short_code', array( 'form_id' => uniqid( 'sevices-' ), 'staff_id' => $staff['id'], 'attributes' => $attrs, 'form' => $form ), false );
            }
        }

        return FrontendComponents\Notice\Permission::generate();
    }
}