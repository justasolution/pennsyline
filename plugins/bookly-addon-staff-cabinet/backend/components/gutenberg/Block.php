<?php
namespace BooklyStaffCabinet\Backend\Components\Gutenberg;

use Bookly\Lib as BooklyLib;

/**
 * Class Block
 * @package BooklyStaffCabinet\Backend\Components\Gutenberg\PackagesList
 */
class Block extends BooklyLib\Base\Block
{
    /**
     * @inheritDoc
     */
    public static function registerBlockType()
    {
        self::enqueueScripts( array(
            'module' => array(
                'js/staff-cabinet-calendar-block.js' => array( 'bookly-staff-cabinet-details-block.js' ),
                'js/staff-cabinet-details-block.js'  => array( 'bookly-staff-cabinet-services-block.js' ),
                'js/staff-cabinet-advanced-block.js' => array( 'bookly-staff-cabinet-services-block.js' ),
                'js/staff-cabinet-services-block.js' => array( 'bookly-staff-cabinet-schedule-block.js' ),
                'js/staff-cabinet-schedule-block.js' => array( 'bookly-staff-cabinet-days-off-block.js' ),
                'js/staff-cabinet-days-off-block.js' => array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-editor' ),
            ),
        ) );
        if ( BooklyLib\Config::specialDaysActive() ) {
            self::enqueueScripts( array(
                'module' => array(
                    'js/staff-cabinet-special-days-block.js' => array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-editor' ),
                ),
            ) );
        }

        wp_localize_script( 'bookly-staff-cabinet-days-off-block.js', 'BooklyStaffCabinetL10n', array(
            'blocks' => array(
                'calendar' => array(
                    'title'       => 'Bookly Staff Cabinet - ' . __( 'Calendar', 'bookly' ),
                    'description' => __( 'A custom block for displaying staff calendar', 'bookly' ),
                ),
                'details' => array(
                    'title'       => 'Bookly Staff Cabinet - ' . __( 'Details', 'bookly' ),
                    'description' => __( 'A custom block for displaying staff details', 'bookly' ),
                ),
                'advanced' => array(
                    'title'       => 'Bookly Staff Cabinet - ' . __( 'Advanced', 'bookly' ),
                    'description' => __( 'A custom block for displaying staff advanced settings', 'bookly' ),
                ),
                'services' => array(
                    'title'       => 'Bookly Staff Cabinet - ' . __( 'Services', 'bookly' ),
                    'description' => __( 'A custom block for displaying staff services', 'bookly' ),
                ),
                'schedule' => array(
                    'title'       => 'Bookly Staff Cabinet - ' . __( 'Schedule', 'bookly' ),
                    'description' => __( 'A custom block for displaying staff schedule', 'bookly' ),
                ),
                'specialDays' => array(
                    'title'       => 'Bookly Staff Cabinet - ' . __( 'Special days', 'bookly' ),
                    'description' => __( 'A custom block for displaying staff special days', 'bookly' ),
                ),
                'daysOff' => array(
                    'title'       => 'Bookly Staff Cabinet - ' . __( 'Days off', 'bookly' ),
                    'description' => __( 'A custom block for displaying staff days off', 'bookly' ),
                ),
            ),
            'addons' => array(
                'groupBooking' => BooklyLib\Config::groupBookingActive(),
                'depositPayments' => BooklyLib\Config::depositPaymentsActive(),
                'specialHours' => BooklyLib\Config::specialHoursActive(),
            ),
            'hideVisibilityField' => __( 'Hide visibility field', 'bookly' ),
            'capacity' => __( 'Capacity', 'bookly' ),
            'deposit'  => __( 'Deposit', 'bookly' ),
            'wp_users' => __( 'Hide WordPress users', 'bookly' ),
            'wp_users_help' => __( 'Hide this field if you want to hide the list of WP users from your staff members.' , 'bookly' ),
            'specialHours'  => __( 'Special hours', 'bookly' ),
            'fields'   => __( 'Fields', 'bookly' ),
            'readOnly' => __( 'read only', 'bookly' ),
            'services' => __( 'Services', 'bookly' ),
            'price'    => __( 'Price', 'bookly' ),
        ) );

        register_block_type( 'bookly/staff-cabinet-block', array(
            'editor_script' => 'bookly-staff-days-off-block.js',
        ) );
    }
}
