<?php
namespace BooklyGoogleMapsAddress\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Frontend\Modules\Booking\Proxy;
use BooklyGoogleMapsAddress\Frontend\Components;

/**
 * Class Local
 * @package BooklyGoogleMapsAddress\Frontend\Modules\Booking
 */
class Local extends Proxy\GoogleMapsAddress
{
    /**
     * @inheritDoc
     */
    public static function renderAutocompleter()
    {
        Components\Booking\Address::render();
    }
}