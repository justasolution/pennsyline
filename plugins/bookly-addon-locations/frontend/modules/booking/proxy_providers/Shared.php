<?php
namespace BooklyLocations\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyLocations\Lib\Entities;
use Bookly\Frontend\Modules\Booking\Proxy;

/**
 * Class Shared
 * @package BooklyLocations\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function stepOptions( $options, $step, $userData )
    {
        if ( $step == 'service' ) {
            $options['l10n']['location_label'] = BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_location' );
            $options['l10n']['location_option'] = BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_option_location' );
            $options['l10n']['location_error'] = BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_required_location' );
            $options['services_per_location'] = (bool) BooklyLib\Proxy\Locations::servicesPerLocationAllowed();
            $options['required']['location'] = ( get_option( 'bookly_app_required_location' ) || get_option( 'bookly_locations_allow_services_per_location' ) );
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public static function prepareChainItemInfoText( $data, BooklyLib\ChainItem $chain_item )
    {
        $location = Entities\Location::find( $chain_item->getLocationId() );
        $data['location_info'][]  = $location ? $location->getTranslatedInfo() : '';
        $data['location_names'][] = $location ? $location->getTranslatedName() : '';

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCartItemInfoText( $data, BooklyLib\CartItem $cart_item )
    {
        $location = Entities\Location::find( $cart_item->getLocationId() );
        $data['location_info'][]  = $location ? $location->getTranslatedInfo() : '';
        $data['location_names'][] = $location ? $location->getTranslatedName() : '';

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function prepareInfoTextCodes( array $codes, array $data )
    {
        $codes['location_info'] = implode( ', ', $data['location_info'] );
        $codes['location_name'] = implode( ', ', $data['location_names'] );

        return $codes;
    }
}