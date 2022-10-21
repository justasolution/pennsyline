<?php
namespace BooklyGoogleMapsAddress\Lib;

use Bookly\Lib as BooklyLib;
use BooklyGoogleMapsAddress\Backend\Modules as Backend;
use BooklyGoogleMapsAddress\Frontend\Modules as Frontend;

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
     * @inheritdoc
     */
    protected static function init()
    {
        // Init proxy.
        Backend\Appearance\ProxyProviders\Local::init();
        Backend\Appearance\ProxyProviders\Shared::init();
        Backend\Settings\ProxyProviders\Shared::init();
        if ( get_option( 'bookly_google_maps_address_enabled' ) ) {
            Frontend\Booking\ProxyProviders\Local::init();
            Frontend\Booking\ProxyProviders\Shared::init();
        }
    }
}