<?php
namespace BooklyFiles\Backend\Modules\Appointments\ProxyProviders;

use BooklyFiles\Backend\Components;
use Bookly\Backend\Modules\Appointments\Proxy;

/**
 * Class Shared
 * @package BooklyFiles\Backend\Modules\Appointments\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderAddOnsComponents()
    {
        Components\Dialogs\Appointment\Attachments\Modal::render();
    }
}