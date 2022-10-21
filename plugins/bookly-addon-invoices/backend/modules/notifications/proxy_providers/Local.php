<?php
namespace BooklyInvoices\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Local
 * @package BooklyInvoices\Backend\Modules\Notifications\ProxyProviders
 */
abstract class Local extends Proxy\Invoices
{
    /**
     * @inheritDoc
     */
    public static function renderAttach()
    {
        echo '<input type="hidden" name="notification[attach_invoice]" value="0">';
        Inputs::renderCheckBox( __( 'Attach invoice', 'bookly' ), 1, null, array( 'name' => 'notification[attach_invoice]' ) );
    }
}