<?php
namespace BooklyGoogleMapsAddress\Lib;

use Bookly\Lib as BooklyLib;
use BooklyGoogleMapsAddress\Backend;
use BooklyGoogleMapsAddress\Frontend;

/**
 * Class Plugin
 * @package BooklyGoogleMapsAddress\Lib
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
        // Init proxy.
        Backend\Modules\Appearance\ProxyProviders\Local::init();
        Backend\Modules\Appearance\ProxyProviders\Shared::init();
        Backend\Modules\Settings\ProxyProviders\Shared::init();
        if ( get_option( 'bookly_google_maps_address_enabled' ) ) {
            Frontend\Modules\Booking\ProxyProviders\Local::init();
            Frontend\Modules\Booking\ProxyProviders\Shared::init();
        }
    }
}