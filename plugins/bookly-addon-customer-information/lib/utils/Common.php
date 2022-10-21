<?php
namespace BooklyCustomerInformation\Lib\Utils;

use BooklyCustomerInformation\Lib;

/**
 * Class Common
 * @package BooklyCustomerInformation\Lib\Utils
 */
abstract class Common
{
    /**
     * Prepare customer information fields
     * @param array $fields_data ['id'=>'value']
     * @return array
     */
    public static function prepareInfoFields( array $fields_data )
    {
        $info_fields = array();

        foreach ( Lib\ProxyProviders\Local::getFieldsWhichMayHaveData() as $field ) {
            if ( array_key_exists( $field->id, $fields_data ) ) {
                $info_field = $fields_data[ $field->id ];
            } else {
                $info_field = array( 'id' => $field->id );
            }
            if ( ! isset ( $info_field['value'] ) ) {
                $info_field['value'] = $field->type == 'checkboxes' ? array() : '';
            }
            $info_fields[] = $info_field;
        }

        return $info_fields;
    }
}