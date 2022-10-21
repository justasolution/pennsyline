<?php
namespace BooklyCustomerGroups\Lib;

use Bookly\Lib as BooklyLib;
use BooklyCustomerGroups\Backend;
use BooklyCustomerGroups\Frontend;

/**
 * Class Plugin
 * @package BooklyCustomerGroups\Lib
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
        Backend\Components\Dialogs\CustomerGroup\Ajax::init();
        Backend\Modules\CustomerGroups\Ajax::init();

        // Init proxy.
        Backend\Components\Dialogs\Customer\Edit\ProxyProviders\Shared::init();
        Backend\Components\Dialogs\Service\Edit\ProxyProviders\Local::init();
        Backend\Components\Dialogs\Service\Edit\ProxyProviders\Shared::init();
        Backend\Components\Dialogs\Staff\Edit\ProxyProviders\Local::init();
        Backend\Modules\Appearance\ProxyProviders\Local::init();
        Backend\Modules\Appearance\ProxyProviders\Shared::init();
        Backend\Modules\Customers\ProxyProviders\Local::init();
        Frontend\Modules\Booking\ProxyProviders\Local::init();
        ProxyProviders\Local::init();
        ProxyProviders\Shared::init();
    }
}