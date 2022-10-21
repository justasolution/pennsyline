<?php
namespace BooklyCart\Lib;

use Bookly\Lib;
use BooklyCart\Backend;
use BooklyCart\Frontend;

/**
 * Class Plugin
 * @package BooklyCart\Lib
 */
abstract class Plugin extends Lib\Base\Plugin
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
        if ( get_option( 'bookly_cart_enabled' ) ) {
            Frontend\Modules\Booking\ProxyProviders\Local::init();
            Frontend\Modules\Booking\Ajax::init();
        }
    }
}