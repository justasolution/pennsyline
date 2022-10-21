<?php
namespace BooklyServiceExtras\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;
use Bookly\Lib\Utils\Price;
use BooklyServiceExtras\Lib;
use BooklyServiceExtras\Lib\ProxyProviders\Local;

/**
 * Class Shared
 * @package BooklyServiceExtras\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $titles = array();
        $price  = 0.0;
        $item   = $codes->getItem();
        $extras = $item->getExtras();

        /** @var Lib\Entities\ServiceExtra $extra */
        foreach ( Local::findByIds( array_keys( $extras ) ) as $extra ) {
            $quantity = $extras[ $extra->getId() ];
            $titles[] = ( $item->getCA()->getExtrasMultiplyNop() && $item->getCA()->getNumberOfPersons() > 1 ? $item->getCA()->getNumberOfPersons() . ' × ' : '' ) . Lib\Utils\Common::formatTitle( $extra->getTranslatedTitle(), $quantity );
            $price += $extra->getPrice() * $quantity * ( $item->getCA()->getExtrasMultiplyNop() ? $item->getCA()->getNumberOfPersons() : 1 );
        }
        $codes->extras             = implode( ', ', $titles );
        $codes->extras_total_price = $price;
    }

    /**
     * @inheritDoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $extras = $codes->extras;
        if ( $format == 'text' ) {
            /** @see Lib\Utils\Common::formatTitle() */
            $extras = str_replace( '&nbsp;&times;&nbsp;', ' x ', $extras );
        }
        $replace_codes['extras']             = $extras;
        $replace_codes['extras_total_price'] = Price::format( $codes->extras_total_price );

        return $replace_codes;
    }
}