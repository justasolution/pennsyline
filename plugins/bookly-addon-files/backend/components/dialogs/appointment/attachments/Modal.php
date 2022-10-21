<?php
namespace BooklyFiles\Backend\Components\Dialogs\Appointment\Attachments;

use Bookly\Lib as BooklyLib;

class Modal extends BooklyLib\Base\Component
{
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/attachments-dialog.js' => array( 'bookly-backend-globals' ), ),
        ) );

        self::renderTemplate( 'attachments_dialog' );
    }
}