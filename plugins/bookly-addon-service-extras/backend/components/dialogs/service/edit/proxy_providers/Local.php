<?php
namespace BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;

/**
 * Class Local
 * @package BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit\ProxyProviders
 */
class Local extends Proxy\ServiceExtras
{
    /**
     * @inheritDoc
     */
    public static function renderTab()
    {
        self::renderTemplate( 'extras_tab' );
    }

    /**
     * @inheritDoc
     */
    public static function getTabHtml( $service_id )
    {
        $extras        = BooklyLib\Proxy\ServiceExtras::findByServiceId( $service_id );
        $time_interval = get_option( 'bookly_gen_time_slot_length' );

        return self::renderTemplate( 'extras', compact( 'service_id', 'extras', 'time_interval' ), false );
    }
}