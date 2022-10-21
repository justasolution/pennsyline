<?php
namespace BooklyServiceExtras\Backend\Components\Dialogs\Appointment\CustomerDetails\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails\Proxy;
use BooklyServiceExtras\Lib;
use Bookly\Lib as BooklyLib;

/**
 * Class Shared
 * @package BooklyServiceExtras\Backend\Components\Dialogs\Appointment\CustomerDetails\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareL10n( $localize )
    {
        $extras = Lib\Entities\ServiceExtra::query()->sortBy( 'title' )->fetchArray();

        foreach ( $extras as &$extra ) {
            $extra['id'] = (int) $extra['id'];
            $extra['service_id'] = (int) $extra['service_id'];
            $extra['price_format'] = BooklyLib\Utils\Price::format( $extra['price'] );
        }

        $localize['extras'] = $extras;
        $localize['l10n']['extras'] = __( 'Extras', 'bookly' );

        return $localize;
    }
}