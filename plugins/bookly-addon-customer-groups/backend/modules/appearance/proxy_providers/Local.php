<?php
namespace BooklyCustomerGroups\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Local
 * @package BooklyCustomerGroups\Backend\Modules\Appearance\ProxyProviders
 */
class Local extends Proxy\CustomerGroups
{
    /**
     * @inheritDoc
     */
    public static function renderStepCompleteOption()
    {
        printf( '<option value="booking-skip-payment">%s</option>', esc_html__( 'Form view in case of payment has been skipped', 'bookly' ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderStepCompleteInfo()
    {
        self::renderTemplate( 'step_complete_info' );
    }
}