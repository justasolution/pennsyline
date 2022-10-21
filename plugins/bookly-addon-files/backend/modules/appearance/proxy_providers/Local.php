<?php
namespace BooklyFiles\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Local
 * @package BooklyFiles\Backend\Modules\Appearance\ProxyProviders
 */
class Local extends Proxy\Files
{
    /**
     * Render button browse in Appearance
     *
     * @throws \Exception
     */
    public static function renderAppearance()
    {
        self::renderTemplate( 'browse_file' );
    }

    /**
     * Render button browse in Appearance
     *
     * @throws \Exception
     */
    public static function renderShowFiles()
    {
        self::renderTemplate( 'show_files' );
    }

}