<?php
namespace BooklyAdvancedGoogleCalendar\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Updates
 * @package BooklyAdvancedGoogleCalendar\Lib
 */
class Updater extends BooklyLib\Base\Updater
{
    function update_1_8()
    {
        $this->renameOptions( array( 'bookly_gc_full_sync_offset_days' => 'bookly_gc_full_sync_offset_days_before' ) );
        add_option( 'bookly_gc_full_sync_offset_days_after', 365 );
    }

    function update_1_7()
    {
        add_option( 'bookly_gc_force_update_description', '0' );
    }

    function update_1_1()
    {
        delete_option( 'bookly_advanced_google_calendar_enabled' );
    }
}