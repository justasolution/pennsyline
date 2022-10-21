<?php
namespace BooklyFiles\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyFiles\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['customer_appointment']['files_count'] = array( 'description' => __( 'Number of uploaded files', 'bookly' ), 'if' => true );

        return $codes;
    }
}