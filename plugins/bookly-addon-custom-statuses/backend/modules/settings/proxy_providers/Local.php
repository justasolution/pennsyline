<?php
namespace BooklyCustomStatuses\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Lib as BooklyLib;

/**
 * Class Local
 * @package BooklyCustomStatuses\Backend\Modules\Settings\ProxyProviders
 */
class Local extends Proxy\CustomStatuses
{
    /**
     * @inheritDoc
     */
    public static function renderTab()
    {
        $datatables = BooklyLib\Utils\Tables::getSettings( 'custom_statuses' );

        self::renderTemplate( 'settings_tab', compact( 'datatables' ) );
    }
}