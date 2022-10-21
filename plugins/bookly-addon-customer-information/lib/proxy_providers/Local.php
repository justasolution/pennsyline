<?php
namespace BooklyCustomerInformation\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyCustomerInformation\Lib;
use BooklyCustomerInformation\Backend\Modules\CustomerInformation\Page;
use BooklyCustomerInformation\Frontend\Modules\CustomerCabinet;

/**
 * Class Local
 * @package BooklyCustomerInformation\Lib\ProxyProviders
 */
class Local extends BooklyLib\Proxy\CustomerInformation
{
    /**
     * @inheritDoc
     */
    public static function addBooklyMenuItem()
    {
        $customer_information = __( 'Customer Information', 'bookly' );

        add_submenu_page( 'bookly-menu', $customer_information, $customer_information, BooklyLib\Utils\Common::getRequiredCapability(),
            Page::pageSlug(), function () { Page::render(); } );
    }

    /**
     * @inheritDoc
     */
    public static function getFields( $exclude = array() )
    {
        $result = json_decode( get_option( 'bookly_customer_information_data', '[]' ) );

        if ( ! empty ( $exclude ) ) {
            $result2 = array();
            foreach ( $result as $field ) {
                if ( ! in_array( $field->type, $exclude ) ) {
                    $result2[] = $field;
                }
            }
            $result = $result2;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldsWhichMayHaveData()
    {
        return self::getFields( array( 'text-content' ) );
    }

    /**
     * @inheritDoc
     */
    public static function getTranslatedFields( $language_code = null )
    {
        $fields = self::getFields();

        foreach ( $fields as $field ) {
            switch ( $field->type ) {
                case 'checkboxes':
                case 'radio-buttons':
                case 'drop-down':
                    $field->items_translated = array();
                    foreach ( $field->items as $i => $item ) {
                        $field->items_translated[] = BooklyLib\Utils\Common::getTranslatedString(
                            sprintf( 'customer_field_%d_%d', $field->id, $i ),
                            $item,
                            $language_code
                        );
                    }
                case 'textarea':
                case 'text-content':
                case 'text-field':
                    $field->label_translated = BooklyLib\Utils\Common::getTranslatedString(
                        sprintf( 'customer_field_%d', $field->id ),
                        $field->label,
                        $language_code
                    );
            }
        }

        return $fields;
    }

    /**
     * @inheritDoc
     */
    public static function validate( array $errors, array $values )
    {
        // Get data indexed by ID.
        $fields_data = array();
        foreach ( $values as $value ) {
            $fields_data[ $value['id'] ] = $value;
        }

        foreach ( self::getFieldsWhichMayHaveData() as $field ) {
            if ( $field->required && (
                ! array_key_exists( $field->id, $fields_data ) ||
                is_array( $fields_data[ $field->id ]['value'] ) && empty ( $fields_data[ $field->id ]['value'] ) ||
                $fields_data[ $field->id ]['value'] == ''
            ) ) {
                $errors['info_fields'][ $field->id ] = __( 'Required', 'bookly' );
            }
        }

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public static function renderCustomerCabinet( $field_id, BooklyLib\Entities\Customer $customer )
    {
        $fields = self::getFieldsWhichMayHaveData();
        $values = json_decode( $customer->getInfoFields() );
        foreach ( $fields as $field ) {
            if ( $field->id == $field_id ) {
                foreach ( $values as $value ) {
                    if ( $value->id == $field_id ) {
                        $field->value = $value->value;
                    }
                }
                CustomerCabinet\Components::render( $field );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function prepareInfoFields( array $info_fields )
    {
        return Lib\Utils\Common::prepareInfoFields( $info_fields );
    }
}