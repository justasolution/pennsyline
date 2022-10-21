<?php
namespace BooklyStaffCabinet\Frontend\Modules\StaffSchedule;

use Bookly\Backend\Components\Schedule\Component as ScheduleComponent;
use Bookly\Lib as BooklyLib;
use BooklyStaffCabinet\Lib;
use BooklyStaffCabinet\Frontend\Components as FrontendComponents;

/**
 * Class Page
 * @package BooklyStaffCabinet\Frontend\Modules\StaffSchedule
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-staff-schedule', array( __CLASS__, 'render' ) );

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
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-schedule' )
        ) {
            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals' )
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
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-schedule' )
        ) {
            self::enqueueScripts( array(
                'bookly' => array(
                    'backend/resources/js/range-tools.js' => array( 'bookly-backend-globals' ),
                    'backend/components/dialogs/staff/edit/resources/js/staff-schedule.js' => array( 'bookly-range-tools.js' ),
                ),
                'frontend' => array( 'js/staff-cabinet-native.js' ),
                'module' => array( 'js/staff-cabinet-schedule.js' => array( 'bookly-staff-schedule.js', 'bookly-staff-cabinet-native.js' ) ),
            ) );

            wp_localize_script( 'bookly-staff-cabinet-schedule.js', 'BooklySCScheduleL10n', array(
                'saved'      => __( 'Settings saved.', 'bookly' ),
                'areYouSure' => __( 'Are you sure?', 'bookly' ),
            ) );
        }
    }

    /**
     * Render Staff Schedule shortcode.
     *
     * @param array $attributes
     * @return string
     */
    public static function render( $attributes )
    {
        if ( is_user_logged_in() && $staff = BooklyLib\Entities\Staff::query()->where( 'wp_user_id', get_current_user_id() )->findOne() ) {
            \BooklyPro\Backend\Components\License\Components::renderLicenseNotice( false );
            if ( $staff->isArchived() ) {
                return FrontendComponents\Notice\Permission::generateAccountDisabled();
            }
            if( \BooklyPro\Lib\Config::graceExpired() ) {
                return;
            } else {
                $location_id = null;
                $schedule = new ScheduleComponent( 'start_time[{index}]', 'end_time[{index}]' );
                $ss_ids = array();
                foreach ( $staff->getScheduleItems( $location_id ) as $item ) {
                    $id = $item->getId();
                    $schedule->addHours( $id, $item->getDayIndex(), $item->getStartTime(), $item->getEndTime() );
                    $ss_ids[ $id ] = $item->getDayIndex();
                }

                foreach (
                    BooklyLib\Entities\ScheduleItemBreak::query()
                        ->whereIn( 'staff_schedule_item_id', array_keys( $ss_ids) )
                        ->sortBy( 'start_time, end_time' )
                        ->fetchArray() as $break
                ) {
                    $schedule->addBreak( $break['staff_schedule_item_id'], $break['id'], $break['start_time'], $break['end_time'] );
                }
                $form_id = uniqid( 'schedule-' );

                return self::renderTemplate( 'short_code', compact( 'form_id', 'schedule', 'ss_ids' ), false );
            }
        }

        return FrontendComponents\Notice\Permission::generate();
    }
}