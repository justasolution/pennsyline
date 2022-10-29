<?php

namespace BooklyFiles\Backend\Components\Dialogs\Appointment\CustomerDetails\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails\Proxy;

/**
 * Class Shared
 *
 * @package BooklyFiles\Backend\Components\Dialogs\Appointment\CustomerDetails\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareL10n( $localize )
    {
        $localize['l10n']['incorrect_file_type'] = __( 'File\'s extension is not allowed', 'bookly' );

        return $localize;
    }
}