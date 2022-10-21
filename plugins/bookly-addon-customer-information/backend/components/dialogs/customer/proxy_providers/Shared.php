<?php
namespace BooklyCustomerInformation\Backend\Components\Dialogs\Customer\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Customer\Edit\Proxy;
use Bookly\Lib as BooklyLib;
use BooklyCustomerInformation\Lib;

/**
 * Class Shared
 * @package BooklyCustomerInformation\Backend\Components\Dialogs\Customer\Edit\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareL10n( $localize )
    {
        $localize['infoFields'] = Lib\ProxyProviders\Local::getFieldsWhichMayHaveData();

        return $localize;
    }
}