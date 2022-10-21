<?php
namespace BooklyCustomerGroups\Backend\Modules\CustomerGroups;

use Bookly\Lib as BooklyLib;

/**
 * Class Page
 * @package BooklyCustomerGroups\Backend\Modules\CustomerGroups
 */
class Page extends BooklyLib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/customer_groups.js' => array( 'bookly-backend-globals' ), ),
        ) );

        $datatables = BooklyLib\Utils\Tables::getSettings( 'customer_groups' );

        wp_localize_script( 'bookly-customer_groups.js', 'BooklyCustomerGroupsL10n', array(
            'new_group' => __( 'New Group', 'bookly' ),
            'edit_group' => __( 'Edit Group', 'bookly' ),
            'are_you_sure' => __( 'Are you sure?', 'bookly' ),
            'zeroRecords' => __( 'No customer groups yet.', 'bookly' ),
            'processing' => __( 'Processing...', 'bookly' ),
            'edit' => __( 'Edit', 'bookly' ),
            'no_result_found' => __( 'No result found', 'bookly' ),
            'all_selected' => __( 'All methods', 'bookly' ),
            'nothing_selected' => __( 'No methods selected', 'bookly' ),
            'default' => __( 'Default', 'bookly' ),
            'gateways' => BooklyLib\Utils\Common::getGateways(),
            'datatables' => $datatables,
        ) );

        $no_groups_count = BooklyLib\Entities\Customer::query( 'c' )
            ->select( 'c.id' )
            ->where( 'c.group_id', null )
            ->count();

        self::renderTemplate( 'index', compact( 'no_groups_count', 'datatables' ) );
    }
}