<?php
namespace BooklyFiles\Lib\Notifications\Assets\Test\ProxyProviders;

use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Test\Proxy;

/**
 * Class Shared
 * @package BooklyFiles\Lib\Notifications\Assets\Test\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $codes->files_count = 2;
    }
}