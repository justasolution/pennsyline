<?php
namespace BooklyInvoices\Frontend\Modules\Invoice;

use Bookly\Lib as BooklyLib;
use BooklyInvoices\Lib;

/**
 * Class Ajax
 * @package BooklyInvoices\Frontend\Modules\Invoice
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * Generate Content-Type: application/pdf data
     */
    public static function download()
    {
        $token   = self::parameter( 'token' );
        $payment = BooklyLib\Entities\Payment::query( 'p' )
            ->where( 'token', $token )
            ->findOne();
        if ( $payment ) {
            Lib\ProxyProviders\Local::downloadInvoice( $payment );
        }

        exit();
    }

    /**
     * Override parent method to exclude actions from CSRF token verification.
     *
     * @param string $action
     * @return bool
     */
    protected static function csrfTokenValid( $action = null )
    {
        $excluded_actions = array(
            'download',
        );

        return in_array( $action, $excluded_actions ) || parent::csrfTokenValid( $action );
    }
}