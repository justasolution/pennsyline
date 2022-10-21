<?php
namespace BooklyStaffCabinet\Lib;

/**
 * Class Updates
 * @package BooklyStaffCabinet\Lib
 */
class Updater extends \Bookly\Lib\Base\Updater
{
    function update_1_8()
    {
        delete_option( 'bookly_staff_cabinet_enabled' );
    }
}