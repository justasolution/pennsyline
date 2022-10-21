<?php
namespace BooklyStaffCabinet\Frontend\Modules\StaffSchedule;

use Bookly\Backend\Components\Schedule\BreakItem;
use Bookly\Lib as BooklyLib;

/**
 * Class Ajax
 * @package BooklyStaffCabinet\Frontend\Modules\StaffSchedule
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /** @var BooklyLib\Entities\Staff */
    protected static $staff;

    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'staff', );
    }

    /**
     * Reset breaks.
     */
    public static function resetBreaks()
    {
        // Remove all breaks for staff member.
        $item_break = new BooklyLib\Entities\ScheduleItemBreak();
        $item_break->removeBreaksByStaffId( self::$staff->getId() );

        // Restore previous breaks.
        $breaks = array();
        foreach ( (array) self::parameter( 'breaks' ) as $break ) {
            $item_break = new BooklyLib\Entities\ScheduleItemBreak();
            $item_break
                ->setStaffScheduleItemId( $break['index'] )
                ->setStartTime( $break['start'] )
                ->setEndTime( $break['end'] )
                ->save();
            $break = new BreakItem( $item_break->getId(), $item_break->getStartTime(), $item_break->getEndTime() );
            $breaks[ $item_break->getStaffScheduleItemId() ][] = $break->render( false );
        }

        wp_send_json_success( compact( 'breaks' ) );
    }

    /**
     * @inheritDoc
     */
    protected static function hasAccess( $action )
    {
        if ( parent::hasAccess( $action ) ) {
            self::$staff = BooklyLib\Entities\Staff::query()->where( 'wp_user_id', get_current_user_id() )->findOne();

            return self::$staff->isLoaded();
        }

        return false;
    }
}