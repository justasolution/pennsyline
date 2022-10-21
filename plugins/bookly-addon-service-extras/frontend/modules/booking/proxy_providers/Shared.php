<?php
namespace BooklyServiceExtras\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Proxy;
use BooklyServiceExtras\Lib;

/**
 * Class Shared
 * @package BooklyServiceExtras\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareChainItemInfoText( $data, BooklyLib\ChainItem $chain_item )
    {
        return self::prepareInfoTextCodesData( $data, $chain_item );
    }

    /**
     * @inheritDoc
     */
    public static function prepareCartItemInfoText( $data, BooklyLib\CartItem $cart_item )
    {
        return self::prepareInfoTextCodesData( $data, $cart_item );
    }

    /**
     * Prepare info text codes for chain or cart item
     *
     * @param array $data
     * @param BooklyLib\ChainItem|BooklyLib\CartItem $item
     * @return array
     */
    protected static function prepareInfoTextCodesData( $data, $item )
    {
        $extras_data = array();
        $extras = $item->getExtras();
        if ( ! empty ( $extras ) ) {
            foreach ( Lib\ProxyProviders\Local::findByIds( array_keys( $extras ) ) as $extra ) {
                $extras_data[] = array(
                    'title'    => (
                        get_option( 'bookly_service_extras_multiply_nop', 1 ) && $item->getNumberOfPersons() > 1
                            ? $item->getNumberOfPersons() . ' × '
                            : ''
                        ) . Lib\Utils\Common::formatTitle( $extra->getTranslatedTitle(), $extras[ $extra->getId() ] ),
                    'quantity' => $extras[ $extra->getId() ],
                    'price'    => $extra->getPrice(),
                );
            }
        }
        $last = count( $data['appointments'] ) - 1;
        $data['appointments'][ $last ]['extras'] = $extras_data;
        $data['extras'][] = implode( ', ', array_map( function ( $item ) { return $item['title']; }, $extras_data ) );

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function prepareInfoTextCodes( array $codes, array $data )
    {
        $codes['extras'] = implode( ', ', $data['extras'] );

        return $codes;
    }

    /**
     * @inheritDoc
     */
    public static function renderCartItemInfo( BooklyLib\UserBookingData $userData, $cart_key, $positions, $desktop )
    {
        if ( get_option( 'bookly_service_extras_show_in_cart' ) ) {
            $cart_items = $userData->cart->getItems();
            $cart_item  = $cart_items[ $cart_key ];
            $template   = $desktop ? 'cart_extras' : 'cart_extras_mobile';
            $nop        = get_option( 'bookly_service_extras_multiply_nop', 1 ) ? $cart_item->getNumberOfPersons() : 1;
            $extras_qty = $cart_item->getExtras();
            $item_tax   = '';
            $data       = array();
            $cart_item_price = $cart_item->getServicePrice( $nop );
            if ( ! $cart_item->toBePutOnWaitingList() ) {
                $item_tax = BooklyLib\Proxy\Taxes::getItemTaxAmount( $cart_item );
            }

            foreach ( BooklyLib\Proxy\ServiceExtras::findByIds( array_keys( $extras_qty ) ) as $extras ) {
                $quantity = $extras_qty[ $extras->getId() ];
                $total = Lib\Utils\Common::getExtrasPrice( $extras, $quantity, $nop );
                $data[]   = array(
                    'title'    => $extras->getTranslatedTitle(),
                    'quantity' => $quantity,
                    'price'    => BooklyLib\Utils\Price::format( $extras->getPrice() ),
                    'total'    => BooklyLib\Utils\Price::format( $total ),
                    'tax'      => $item_tax == ''
                        ? null
                        : BooklyLib\Utils\Price::format( $cart_item_price > 0 ? ( $total / $cart_item_price * $item_tax ) : 0 )
                );
            }

            self::renderTemplate( $template, compact( 'data', 'nop', 'positions', 'cart_key' ) );
        }
    }

    /**
     * @inheritDoc
     */
    public static function booklyFormOptions( array $bookly_options )
    {
        $bookly_options['skip_steps']['extras'] = ! get_option( 'bookly_service_extras_enabled' );
        $bookly_options['step_extras'] = get_option( 'bookly_service_extras_after_step_time' )
            ? 'after_step_time'
            : 'before_step_time';

        if ( $bookly_options['form_attributes']['hide_services'] ) {
            // Case when service is hidden on bookly form
            // if all sub services in Compound or Collaborate services without extras
            // we need to skip step extras.
            $defaults = BooklyLib\Session::getFormVar( $bookly_options['form_id'], 'defaults' );
            if ( $defaults['service_id'] ) {
                $service = BooklyLib\Entities\Service::find( $defaults['service_id'] );
                switch ( $service->getType() ) {
                    case BooklyLib\Entities\Service::TYPE_COMPOUND:
                    case BooklyLib\Entities\Service::TYPE_COLLABORATIVE:
                        foreach ( $service->getSubServices() as $item ) {
                            if ( count( $item->getExtras() ) == 0 ) {
                                $bookly_options['skip_steps']['extras'] = 1;
                                break;
                            }
                        }
                        break;
                    case BooklyLib\Entities\Service::TYPE_SIMPLE:
                        $bookly_options['skip_steps']['extras'] = count( $service->getExtras() ) == 0;
                        break;
                    case BooklyLib\Entities\Service::TYPE_PACKAGE:
                    default:
                }
            }
        }
        $bookly_options['skip_steps']['extras'] = (int) $bookly_options['skip_steps']['extras'];

        return $bookly_options;
    }
}