<?php
namespace BooklyLocations\Backend\Modules\Appearance\ProxyProviders;

use BooklyLocations\Lib;
use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderServiceStepSettings( $col )
    {
        self::renderTemplate( 'appearance_settings' );
    }

    /**
     * @inheritDoc
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        return array_merge( $options_to_save, array_intersect_key( $options, array_flip( array (
            'bookly_app_required_location',
            'bookly_l10n_label_location',
            'bookly_l10n_option_location',
            'bookly_l10n_required_location',
        ) ) ) );
    }

    /**
     * @inheritDoc
     */
    public static function prepareCodes( array $codes )
    {
        return array_merge( $codes, array(
            'location_info' => array( 'description' => __( 'Location info', 'bookly' ), 'if' => true ),
            'location_name' => array( 'description' => __( 'Location name', 'bookly' ), 'if' => true ),
        ) );
    }
}