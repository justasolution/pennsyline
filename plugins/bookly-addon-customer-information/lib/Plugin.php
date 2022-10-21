<?php
namespace BooklyCustomerInformation\Lib;

use Bookly\Lib as BooklyLib;
use BooklyCustomerInformation\Backend;
use BooklyCustomerInformation\Frontend;

/**
 * Class Plugin
 * @package BooklyCustomerInformation\Lib
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
        // Init ajax.
        Backend\Modules\CustomerInformation\Ajax::init();

        // Init proxy.
        Backend\Components\Dialogs\Customer\ProxyProviders\Local::init();
        Backend\Components\Dialogs\Customer\ProxyProviders\Shared::init();
        Backend\Modules\Appearance\ProxyProviders\Local::init();
        Backend\Modules\Appearance\ProxyProviders\Shared::init();
        Backend\Modules\Calendar\ProxyProviders\Shared::init();
        Backend\Modules\Customers\ProxyProviders\Local::init();
        Backend\Modules\Customers\ProxyProviders\Shared::init();
        Backend\Modules\Notifications\ProxyProviders\Shared::init();
        Backend\Modules\Settings\ProxyProviders\Shared::init();
        if ( get_option( 'bookly_customer_information_enabled' ) ) {
            Frontend\Modules\Booking\ProxyProviders\Local::init();
            Frontend\Modules\Booking\ProxyProviders\Shared::init();
        }
        Notifications\Assets\Item\ProxyProviders\Shared::init();
        ProxyProviders\Local::init();
        ProxyProviders\Shared::init();
    }
}