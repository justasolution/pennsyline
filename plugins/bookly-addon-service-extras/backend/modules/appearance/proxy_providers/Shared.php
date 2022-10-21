<?php
namespace BooklyServiceExtras\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;
use BooklyServiceExtras\Lib\Plugin;

/**
 * Class Shared
 * @package BooklyServiceExtras\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        $options_to_save = array_merge( $options_to_save, array_intersect_key( $options, array_flip( array (
            'bookly_l10n_info_extras_step',
            'bookly_l10n_step_extras',
            'bookly_l10n_step_extras_button_next',
            'bookly_service_extras_enabled',
            'bookly_service_extras_show',
            'bookly_service_extras_show_in_cart',
        ) ) ) );

        if ( ! array_key_exists( 'bookly_service_extras_show', $options_to_save ) ) {
            $options_to_save['bookly_service_extras_show'] = array();
        }

        return $options_to_save;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCodes( array $codes )
    {
        $codes['appointments']['loop']['codes']['extras'] = array(
            'description' => array(
                __( 'Loop over extras', 'bookly' ),
                __( 'Loop over extras with delimiter', 'bookly' )
            ),
            'loop' => array(
                'item' => 'extra',
                'codes' => array(
                    'title'    => array( 'description' => __( 'Extras title', 'bookly' ) ),
                    'quantity' => array( 'description' => __( 'Extras quantity', 'bookly' ) ),
                    'price'    => array( 'description' => __( 'Extras price', 'bookly' ), 'if' => true ),
                ),
            ),
            'if' => true
        );

        return array_merge( $codes, array(
            'extras' => array( 'description' => __( 'Extras titles', 'bookly' ), 'if' => true, 'flags' => array( 'step' => '>2' ) ),
        ) );
    }
}