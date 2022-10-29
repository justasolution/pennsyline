<?php
namespace BooklyFiles\Lib;

use Bookly\Lib as BooklyLib;
use BooklyFiles\Backend;
use BooklyFiles\Frontend;

/**
 * Class Plugin
 * @package BooklyFiles\Lib
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
     * @inheritDoc
     */
    protected static function init()
    {
        if ( BooklyLib\Config::customFieldsActive() ) {
            // Init ajax.
            Backend\Components\Dialogs\Appointment\Attachments\Ajax::init();
            Backend\Components\Dialogs\Appointment\Edit\Ajax::init();
            Frontend\Modules\Booking\Ajax::init();

            // Init proxy.
            Backend\Components\Dialogs\Appointment\CustomerDetails\ProxyProviders\Shared::init();
            Backend\Modules\Appearance\ProxyProviders\Local::init();
            Backend\Modules\Appearance\ProxyProviders\Shared::init();
            Backend\Modules\Appointments\ProxyProviders\Local::init();
            Backend\Modules\Appointments\ProxyProviders\Shared::init();
            Backend\Modules\CustomFields\ProxyProviders\Local::init();
            Backend\Modules\Notifications\ProxyProviders\Shared::init();
            Backend\Modules\Settings\ProxyProviders\Shared::init();
            if ( get_option( 'bookly_files_enabled' ) ) {
                Frontend\Modules\Booking\ProxyProviders\Local::init();
                Frontend\Modules\Booking\ProxyProviders\Shared::init();
            }
            Notifications\Assets\Item\ProxyProviders\Shared::init();
            Notifications\Assets\Test\ProxyProviders\Shared::init();
            ProxyProviders\Local::init();
            ProxyProviders\Shared::init();
        }
    }
}