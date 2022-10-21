<?php
namespace BooklyStaffCabinet\Frontend\Modules\StaffSpecialDays;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Modules\Staff\Proxy;
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy as StaffEditProxy;
use BooklyStaffCabinet\Lib;
use BooklyStaffCabinet\Frontend\Components as FrontendComponents;

/**
 * Class Page
 * @package BooklyStaffCabinet\Frontend\Modules\StaffSpecialDays
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-staff-special-days', array( __CLASS__, 'render' ) );

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
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-special-days' )
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
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-special-days' )
        ) {
            $sp_days_ver = \BooklySpecialDays\Lib\Plugin::getVersion();
            $sp_days_url = plugins_url( '', \BooklySpecialDays\Lib\Plugin::getMainFile() );

            self::enqueueScripts( array(
                'bookly' => array( 'backend/resources/js/range-tools.js' => array( 'bookly-backend-globals' ), ),
                'frontend' => array( 'js/staff-cabinet-native.js' ),
                'module' => array( 'js/staff-cabinet-special-days.js' => array( 'bookly-special-days.js', 'bookly-staff-cabinet-native.js' ) )
            ) );
            wp_enqueue_script( 'bookly-special-days.js', $sp_days_url . '/backend/modules/staff/resources/js/special-days.js', array( 'bookly-range-tools.js' ), $sp_days_ver );

            Proxy\Shared::enqueueStaffProfileScripts();

            wp_localize_script( 'bookly-staff-cabinet-special-days.js', 'BooklySCSpecialDaysL10n', array(
                'datePicker'      => BooklyLib\Utils\DateTime::datePickerOptions(),
                'are_you_sure'    => __( 'Are you sure?', 'bookly' ),
                'start_time'      => '08:00:00',
                'end_time'        => '18:00:00',
                'saved'           => __( 'Settings saved.', 'bookly' ),
                'duplicate_error' => __( 'Duplicate dates are not permitted.', 'bookly' ),
                'past_date_error' => __( 'Date in the past.', 'bookly' ),
            ) );
        }
    }

    /**
     * Render Staff Special Days shortcode.
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
                $form_id = uniqid( 'special-days-' );
                $special_days = StaffEditProxy\SpecialDays::getStaffSpecialDays( $staff->getId(), null ) ?: array();

                return self::renderTemplate( 'short_code', compact( 'form_id', 'staff', 'special_days' ), false );
            }
        }

        return FrontendComponents\Notice\Permission::generate();
    }
}