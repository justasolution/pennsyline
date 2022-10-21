<?php
namespace BooklyFiles\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Frontend\Modules\Booking\Proxy;

/**
 * Class Local
 * @package BooklyFiles\Frontend\Modules\Booking\ProxyProviders
 */
class Local extends Proxy\Files
{
    /**
     * Render file browser control on step details.
     *
     * @param \stdClass $custom_field
     * @param array     $cf_item
     */
    public static function renderCustomField( \stdClass $custom_field, array $cf_item )
    {
        if ( $custom_field->type == 'file' ) {
            $names = \BooklyFiles\Lib\ProxyProviders\Local::getFileNamesForCustomFields( array(
                array(
                    'id' => $custom_field->id,
                    'value' => isset( $cf_item['data'][ $custom_field->id ] ) ? $cf_item['data'][ $custom_field->id ] : null,
                ),
            ) );
            $name = $names ? $names[ $custom_field->id ] : '';

            self::renderTemplate( '_6_details', compact( 'custom_field', 'cf_item', 'name' ) );
        }
    }
}