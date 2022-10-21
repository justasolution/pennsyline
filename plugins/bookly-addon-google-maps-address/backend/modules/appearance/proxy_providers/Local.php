<?php
namespace BooklyGoogleMapsAddress\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Local
 * @package BooklyGoogleMapsAddress\Backend\Modules\Appearance\ProxyProviders
 */
class Local extends Proxy\GoogleMapsAddress
{
    /**
     * @inheritdoc
     */
    public static function renderGoogleMaps()
    {
        self::renderTemplate('google_maps');
    }

    /**
     * @inheritdoc
     */
    public static function renderShowGoogleMaps()
    {
        self::renderTemplate('show_google_maps');
    }
}