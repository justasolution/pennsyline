<?php
namespace BooklyCustomerInformation\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;

/**
 * Class Shared
 * @package BooklyCustomerInformation\Lib\ProxyProviders
 */
class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareTableColumns( $columns, $table )
    {
        if ( $table == BooklyLib\Utils\Tables::CUSTOMERS ) {
            foreach ( Local::getFieldsWhichMayHaveData() as $field ) {
                $columns[ 'info_fields_' . $field->id ] = BooklyLib\Utils\Common::stripScripts( $field->label );
            }
        }

        return $columns;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCustomerAppointmentCodes( $codes, $customer_appointment, $format )
    {
        $info_fields = Local::getFieldsWhichMayHaveData();
        $codes['info_fields'] = '';
        foreach ( json_decode( $customer_appointment->customer->getInfoFields() ) as $info_field ) {
            $label = '';
            foreach ( $info_fields as $field ) {
                if ( $field->id == $info_field->id ) {
                    $label = $field->label;
                    break;
                }
            }
            $value = nl2br( esc_html( is_array( $info_field->value ) ? implode( ',', $info_field->value ) : $info_field->value ) );
            $codes['info_fields'] .= sprintf( '<div>%s: %s</div>', wp_strip_all_tags( $label ), $value );
            $codes[ 'info_field#' . $info_field->id ] = $value;
        }

        return $codes;
    }
}