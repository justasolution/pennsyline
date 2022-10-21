<?php
namespace BooklyCustomerInformation\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Proxy;
use BooklyCustomerInformation\Lib;

/**
 * Class Shared
 *
 * @package BooklyCustomerInformation\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareInfoTextCodes( array $codes, array $data )
    {
        if ( get_option( 'bookly_customer_information_enabled' ) ) {
            $codes['info_fields'] = isset( $data['info_fields'] ) ? implode( '<br>', $data['info_fields'] ) : '';
            foreach ( $data as $key => $value ) {
                if ( strpos( $key, 'info_field#' ) === 0 ) {
                    $codes[ $key ] = $value;
                }
            }
        }

        return $codes;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCartItemInfoText( $data, BooklyLib\CartItem $cart_item, BooklyLib\UserBookingData $userData )
    {
        if ( get_option( 'bookly_customer_information_enabled' ) ) {
            $info_fields = Lib\ProxyProviders\Local::getFieldsWhichMayHaveData();
            $data['info_fields'] = array();
            foreach ( $userData->getInfoFields() as $info_field ) {
                $label = '';
                //echo "<pre>".print_r(($info_fields),true)."</pre>";
                foreach ( $info_fields as $field ) {
                    if ( $field->id == $info_field['id'] ) {
                        $label = $field->label;
                        break;
                    }
                }
                $value = nl2br( esc_html( is_array( $info_field['value'] ) ? implode( ',', $info_field['value'] ) : $info_field['value'] ) );
                $data['info_fields'][] = sprintf( '<div>%s: %s</div>', wp_strip_all_tags( $label ), $value );
                $data[ 'info_field#' . $info_field['id'] ] = $value;
            }
        }

        return $data;
    }
}