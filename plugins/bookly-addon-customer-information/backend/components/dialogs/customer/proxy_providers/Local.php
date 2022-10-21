<?php
namespace BooklyCustomerInformation\Backend\Components\Dialogs\Customer\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Customer\Edit\Proxy;
use BooklyCustomerInformation\Lib;

/**
 * Class Local
 * @package BooklyCustomerInformation\Backend\Components\Dialogs\Customer\ProxyProviders
 */
class Local extends Proxy\CustomerInformation
{
    /**
     * @inheritDoc
     */
    public static function prepareCustomerFormData( array $params )
    {
        // Get data indexed by ID.
        $fields_data = array();
        if ( isset( $params['info_fields'] ) ) {
            foreach ( $params['info_fields'] as $field_data ) {
                $fields_data[ $field_data['id'] ] = $field_data;
            }
        }

        $params['info_fields'] = Lib\Utils\Common::prepareInfoFields( $fields_data );

        return $params;
    }
}