<?php
namespace BooklyInvoices\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyInvoices\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['invoice']['invoice_date'] = array( 'description' => __( 'Invoice creation date', 'bookly' ) );
        $codes['invoice']['invoice_due_date'] = array( 'description' => __( 'Due date of invoice', 'bookly' ) );
        $codes['invoice']['invoice_due_days'] = array( 'description' => __( 'Number of days to submit payment', 'bookly' ) );
        $codes['invoice']['invoice_link'] = array( 'description' => __( 'Invoice link', 'bookly' ) );
        $codes['invoice']['invoice_number'] = array( 'description' => __( 'Invoice number', 'bookly' ) );

        return $codes;
    }
}