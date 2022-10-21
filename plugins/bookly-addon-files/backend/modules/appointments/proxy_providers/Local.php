<?php
namespace BooklyFiles\Backend\Modules\Appointments\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyFiles\Lib\Entities;

class Local extends BooklyLib\Proxy\Files
{
    /**
     * @inheritDoc
     */
    public static function getSubQueryAttachmentExists()
    {
        return Entities\CustomerAppointmentFiles::query()
            ->select('1')
            ->whereRaw( 'customer_appointment_id = ca.id', array() )
            ->limit( 1 );
    }
}