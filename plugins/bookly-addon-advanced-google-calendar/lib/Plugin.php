<?php
namespace BooklyAdvancedGoogleCalendar\Lib;

use Bookly\Lib as BooklyLib;
use BooklyAdvancedGoogleCalendar\Frontend as Frontend;
use BooklyAdvancedGoogleCalendar\Backend as Backend;

/**
 * Class Plugin
 * @package BooklyAdvancedGoogleCalendar\Lib
 */
abstract class Plugin extends BooklyLib\Base\Plugin
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
     * @inheritdoc
     */
    protected static function init()
    {
        // Init ajax.
        Backend\Modules\Calendar\Ajax::init();
        Frontend\Modules\Google\Ajax::init();

        // Init proxy.
        Backend\Components\Dialogs\Staff\Edit\ProxyProviders\Shared::init();
        Backend\Modules\Calendar\ProxyProviders\Local::init();
        Backend\Modules\Settings\ProxyProviders\Local::init();
        Backend\Modules\Settings\ProxyProviders\Shared::init();
        ProxyProviders\Local::init();
        ProxyProviders\Shared::init();
    }

    /**
     * @inheritdoc
     */
    public static function deactivate( $network_wide )
    {
        if ( get_option( 'bookly_gc_sync_mode' ) == '2-way' ) {
            update_option( 'bookly_gc_sync_mode', '1.5-way' );
        }

        parent::deactivate( $network_wide );
    }
}