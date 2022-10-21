<?php
namespace BooklyStaffCabinet\Lib;

use Bookly\Lib as BooklyLib;
use BooklyStaffCabinet\Backend;
use BooklyStaffCabinet\Frontend;

/**
 * Class Plugin
 * @package BooklyStaffCabinet\Lib
 */
abstract class Plugin extends \Bookly\Lib\Base\Plugin
{
    protected static $prefix;
    protected static $title;
    protected static $version;
    protected static $slug;
    protected static $directory;
    protected static $main_file;
    protected static $basename;
    protected static $text_domain;
    protected static $root_namespace;
    protected static $embedded;

    /**
     * @inheritDoc
     */
    public static function init()
    {
        // Init ajax.
        Frontend\Modules\Calendar\Ajax::init();
        Frontend\Modules\StaffSchedule\Ajax::init();

        // Init proxy.
        Backend\Components\TinyMce\ProxyProviders\Shared::init();
        Backend\Components\Gutenberg\Block::init();

        if ( ! is_admin() ) {
            // Init short code.
            Frontend\Modules\Calendar\ShortCode::init();
            Frontend\Modules\StaffAdvanced\ShortCode::init();
            Frontend\Modules\StaffDaysOff\ShortCode::init();
            Frontend\Modules\StaffDetails\ShortCode::init();
            Frontend\Modules\StaffSchedule\ShortCode::init();
            Frontend\Modules\StaffServices\ShortCode::init();
            if ( BooklyLib\Config::specialDaysActive() ) {
                Frontend\Modules\StaffSpecialDays\ShortCode::init();
            }
        }
    }
}