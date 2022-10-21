<?php
namespace BooklyCustomerGroups\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyCustomerGroups\Backend\Modules\CustomerGroups\Page;
use BooklyCustomerGroups\Lib\Entities\CustomerGroups;

/**
 * Class Local
 * @package BooklyCustomerGroups\Lib
 */
class Local extends BooklyLib\Proxy\CustomerGroups
{
    /**
     * @inheritDoc
     */
    public static function addBooklyMenuItem()
    {
        add_submenu_page(
            'bookly-menu',
            __( 'Customer Groups', 'bookly' ),
            __( 'Customer Groups', 'bookly' ),
            BooklyLib\Utils\Common::getRequiredCapability(),
            Page::pageSlug(),
            function () { Page::render(); }
        );
    }

    /**
     * @inheritDoc
     */
    public static function prepareCartTotalPrice( $total, BooklyLib\UserBookingData $userData )
    {
        $customer = $userData->getCustomer();
        $group = self::getGroup( $customer->isLoaded() ? $customer->getGroupId() : 0 );
        $discount = $group->getDiscount();
        if ( $discount != 0 ) {
            if ( strpos( $discount, '%' ) === false ) {
                $total -= $discount;
            } else {
                $total = round( $total * ( 100 - rtrim( $discount, '%' ) ) / 100, 2 );
            }
        }

        return max( $total, 0 );
    }

    /**
     * @inheritDoc
     */
    public static function takeDefaultAppointmentStatus( $status, $group_id )
    {
        $key = $status . '-' . $group_id;
        if ( ! self::hasInCache( $key ) ) {
            $group = self::getGroup( $group_id );

            if ( in_array( $group->getAppointmentStatus(), BooklyLib\Entities\CustomerAppointment::getStatuses() ) ) {
                $status = $group->getAppointmentStatus();
            }

            self::putInCache( $key, $status );
        }

        return self::getFromCache( $key );
    }

    /**
     * @inheritDoc
     */
    public static function prepareDefaultAppointmentStatuses( array $statuses )
    {
        foreach ( CustomerGroups::query()->find() as $group ) {
            $statuses[ $group->getId() ] = self::takeDefaultAppointmentStatus( BooklyLib\Config::getDefaultAppointmentStatus(), $group->getId() );
        }
        $statuses[0] = self::takeDefaultAppointmentStatus( BooklyLib\Config::getDefaultAppointmentStatus(), 0 );

        return $statuses;
    }

    /**
     * @inheritDoc
     */
    public static function getSkipPayment( $customer )
    {
        $group = self::getGroup( $customer->getGroupId() );

        return (bool) $group->getSkipPayment();
    }

    /**
     * @param $group_id
     * @return CustomerGroups
     */
    public static function getGroup( $group_id )
    {
        $group = new CustomerGroups();
        if ( $group_id ) {
            $group->load( $group_id );
        }
        if ( ! $group->isLoaded() ) {
            $group_settings = get_option( 'bookly_customer_groups_general_settings', array() );
            $group
                ->setAppointmentStatus( isset( $group_settings['status'] ) ? $group_settings['status'] : '' )
                ->setDiscount( isset( $group_settings['discount'] ) ? $group_settings['discount'] : 0 )
                ->setSkipPayment( isset( $group_settings['skip_payment'] ) ? $group_settings['skip_payment'] : 0 );
        }

        return $group;
    }
}