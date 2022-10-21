<?php
namespace BooklyCustomerGroups\Backend\Components\Dialogs\Customer\Edit\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Customer\Edit\Proxy;
use Bookly\Lib as BooklyLib;
use BooklyCustomerGroups\Lib;

/**
 * Class Shared
 * @package BooklyCustomerGroups\Backend\Components\Dialogs\Customer\Edit\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareL10n( $localize )
    {
        $groups = array();
        $rows = Lib\Entities\CustomerGroups::query( 'cg' )
            ->select( 'cg.id, cg.name' )
            ->fetchArray();
        foreach ( $rows as $group ) {
            $groups[ $group['id'] ] = $group['name'];
        }

        $localize['groups'] = $groups;
        $localize['l10n']['group'] = __( 'Group', 'bookly' );
        $localize['l10n']['noGroup'] = __( 'No group', 'bookly' );

        return $localize;
    }
}