<?php
namespace BooklyStaffCabinet\Frontend\Modules\StaffDetails;

use Bookly\Lib as BooklyLib;
use BooklyStaffCabinet\Lib;
use BooklyStaffCabinet\Frontend\Components as FrontendComponents;

/**
 * Class ShortCode
 * @package BooklyStaffCabinet\Frontend\Modules\StaffDetails
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-staff-details', array( __CLASS__, 'render' ) );

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
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-details' )
        ) {
            self::enqueueStyles( array(
                'frontend' => array( 'css/staff-cabinet.css' => array( 'bookly-backend-globals' ), ),
            ) );
            if ( get_option( 'bookly_cst_phone_default_country' ) != 'disabled' ) {
                self::enqueueStyles( array(
                    'bookly' => array( 'frontend/resources/css/intlTelInput.css' ),
                ) );
            }
        }
    }

    /**
     * Link scripts.
     */
    public static function linkScripts()
    {
        if (
            get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ||
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-details' )
        ) {
            self::enqueueScripts( array(
                'bookly' => array( 'backend/components/dialogs/staff/edit/resources/js/staff-details.js' => array( 'bookly-backend-globals' ), ),
                'frontend' => array( 'js/staff-cabinet-native.js' ),
                'module' => array( 'js/staff-cabinet-details.js' => array( 'bookly-staff-details.js', 'bookly-staff-cabinet-native.js' ) ),
            ) );
            if ( get_option( 'bookly_cst_phone_default_country' ) != 'disabled' ) {
                self::enqueueScripts( array(
                    'bookly' => array( 'frontend/resources/js/intlTelInput.min.js' => array( 'jquery' ) ),
                ) );
            }

            $bookly_url = plugins_url( '', BooklyLib\Plugin::getMainFile() );
            wp_localize_script( 'bookly-staff-cabinet-details.js', 'BooklySCDetailsL10n', array(
                'selector'     => array( 'all_selected' => __( 'All locations', 'bookly' ), 'nothing_selected' => __( 'No locations selected', 'bookly' ), ),
                'saved'        => __( 'Settings saved.', 'bookly' ),
                'intlTelInput' => array(
                    'enabled' => get_option( 'bookly_cst_phone_default_country' ) != 'disabled',
                    'utils'   => $bookly_url . '/frontend/resources/js/intlTelInput.utils.js',
                    'country' => get_option( 'bookly_cst_phone_default_country' ),
                ),
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
                $form_id = uniqid( 'details-' );

                return self::renderTemplate( 'short_code', compact( 'form_id', 'attributes', 'staff' ), false );
            }
        }

        return FrontendComponents\Notice\Permission::generate();
    }
}