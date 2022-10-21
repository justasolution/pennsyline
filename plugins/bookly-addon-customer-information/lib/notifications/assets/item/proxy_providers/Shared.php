<?php

namespace BooklyCustomerInformation\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;
use BooklyCustomerInformation\Lib;

/**
 * Class Shared
 *
 * @package BooklyCustomerInformation\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $codes->info_fields = '';
        $codes->info_fields_data = array();
        $info_fields = Lib\ProxyProviders\Local::getFieldsWhichMayHaveData();
        $customer = Customer::find( $codes->getItem()->getCA()->getCustomerId() );
        if ( $customer ) {
            foreach ( (array) json_decode( $customer->getInfoFields(), true ) as $info_field ) {
                $label = '';
                foreach ( $info_fields as $field ) {
                    if ( $field->id == $info_field['id'] ) {
                        $label = $field->label;
                        break;
                    }
                }
                $value = is_array( $info_field['value'] ) ? implode( ',', $info_field['value'] ) : $info_field['value'];
                $codes->info_fields .= sprintf( '<div>%s: %s</div>', wp_strip_all_tags( $label ), $value );
                $codes->info_fields_data[ 'info_field#' . $info_field['id'] ] = is_array( $info_field['value'] ) ? implode( ',', $info_field['value'] ) : $info_field['value'];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['info_fields'] = $codes->info_fields;
        if ( $codes->info_fields_data !== null ) {
            foreach ( $codes->info_fields_data as $key => $value ) {
                $replace_codes[ $key ] = $value;
            }
        }

        return $replace_codes;
    }
}