<?php
namespace BooklyCustomerInformation\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;
use BooklyCustomerInformation\Lib;

/**
 * Class Shared
 * @package BooklyCustomerInformation\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        $options_to_save = array_merge( $options_to_save, array_intersect_key( $options, array_flip( array(
            'bookly_customer_information_enabled',
        ) ) ) );

        return $options_to_save;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCodes( array $codes )
    {
        $codes['info_fields'] = array( 'description' => __( 'Combined values of all customer information fields', 'bookly' ), 'if' => true );
        foreach ( Lib\ProxyProviders\Local::getFieldsWhichMayHaveData() as $info_field ) {
            $codes[ 'info_field#' . $info_field->id ] = array( 'description' => __( 'Info field', 'bookly' ) . ': ' . $info_field->label, 'if' => true );
        }

        return $codes;
    }
}