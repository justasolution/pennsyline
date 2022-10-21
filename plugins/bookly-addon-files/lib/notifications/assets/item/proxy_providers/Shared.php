<?php
namespace BooklyFiles\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;
use BooklyFiles\Lib;

/**
 * Class Shared
 * @package BooklyFiles\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $custom_fields = (array) json_decode( $codes->getItem()->getCA()->getCustomFields(), true );
        $custom_fields_with_file = Lib\ProxyProviders\Local::getAllIds();

        $codes->files_count = 0;
        foreach ( $custom_fields as $field ) {
            if ( in_array( $field['id'], $custom_fields_with_file ) && $field['value'] != '' ) {
                $codes->files_count ++;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['files_count'] = $codes->files_count;

        return $replace_codes;
    }
}