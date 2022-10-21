<?php
namespace BooklyServiceExtras\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Proxy;

/**
 * Class Local
 * @package BooklyServiceExtras\Frontend\Modules\Booking\ProxyProviders
 */
class Local extends Proxy\ServiceExtras
{
    /**
     * @inheritDoc
     */
    public static function getStepHtml( BooklyLib\UserBookingData $userData, $show_cart_btn, $info_text, $progress_tracker, $show_back_btn )
    {
        $chain = array();
        $chain_price = null;
        foreach ( $userData->chain->getItems() as $chain_item ) {
            $extras  = array();
            $service = $chain_item->getService();
            if ( $service->withSubServices() ) {
                // Price.
                $chain_price += $service->getPrice() * $chain_item->getNumberOfPersons();
                // Extras.
                $sub_services  = $service->getSubServices();
                $processed_ids = array();
                foreach ( $sub_services as $sub_service ) {
                    $service_id = $sub_service->getId();
                    if ( ! in_array( $service_id, $processed_ids ) ) {
                        $extras = array_merge( $extras, (array) BooklyLib\Proxy\ServiceExtras::findByServiceId( $service_id ) );
                        $processed_ids[] = $service_id;
                    }
                }
            } else {
                $service_id = $service->getId();
                // Price.
                if ( count( $chain_item->getStaffIds() ) == 1 ) {
                    $staff_service = BooklyLib\Entities\StaffService::query()
                        ->select( 'price' )
                        ->where( 'service_id', $service_id )
                        ->where( 'staff_id', current( $chain_item->getStaffIds() ) )
                        ->fetchRow();
                    $chain_price += $staff_service['price'] * $chain_item->getUnits() * $chain_item->getNumberOfPersons();
                } else {
                    $chain_price += $service->getPrice() * $chain_item->getUnits() * $chain_item->getNumberOfPersons();
                }
                // Extras.
                $extras = (array) BooklyLib\Proxy\ServiceExtras::findByServiceId( $service_id );
            }
            $chain[] = array(
                'service_title'  => $service->getTranslatedTitle(),
                'extras'         => $extras,
                'checked_extras' => $chain_item->getExtras(),
                'nop_multiplier' => get_option( 'bookly_service_extras_multiply_nop', 1 ) ? $chain_item->getNumberOfPersons() : 1,
            );
        }
        $show = get_option( 'bookly_service_extras_show' );

        return self::renderTemplate( 'step_extras', compact( 'chain', 'show', 'show_cart_btn', 'info_text', 'progress_tracker', 'chain_price', 'show_back_btn' ), false );
    }
}