<?php
namespace BooklyStaffCabinet\Frontend\Components\Notice;

use Bookly\Lib as BooklyLib;

/**
 * Class Permission
 * @package BooklyStaffCabinet\Frontend\Components\Notice
 */
class Permission extends BooklyLib\Base\Component
{
    public static function generate()
    {
        // Checking if login form is 'rendered' in the current request
        if ( ! self::hasInCache( __FUNCTION__ ) ) {
            self::putInCache( __FUNCTION__, 'generated' );

            return self::renderTemplate( 'permission', array(), false );
        }
    }

    /**
     * @return string|void
     */
    public static function generateAccountDisabled()
    {
        // Checking if notice is 'rendered' in the current request
        if ( ! self::hasInCache( __FUNCTION__ ) ) {
            self::putInCache( __FUNCTION__, 'generated' );
            return self::renderTemplate( 'account_disabled', array(), false );
        }
    }
}