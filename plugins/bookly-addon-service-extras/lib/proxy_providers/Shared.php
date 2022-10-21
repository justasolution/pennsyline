<?php
namespace BooklyServiceExtras\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyServiceExtras\Lib;

/**
 * Class Shared
 * @package BooklyPro\Lib\ProxyProviders
 */
class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareCustomerAppointmentCodes( $codes, $customer_appointment, $format )
    {
        $titles = array();
        $price = 0.0;
        $extras = json_decode( $customer_appointment->getExtras(), true );

        foreach ( Local::findByIds( array_keys( $extras ) ) as $extra ) {
            $quantity = $extras[ $extra->getId() ];
            $titles[] = ( $customer_appointment->getExtrasMultiplyNop() && $customer_appointment->getNumberOfPersons() > 1 ? $customer_appointment->getNumberOfPersons() . ' × ' : '' ) . Lib\Utils\Common::formatTitle( $extra->getTranslatedTitle(), $quantity );
            $price += $extra->getPrice() * $quantity * ( $customer_appointment->getExtrasMultiplyNop() ? $customer_appointment->getNumberOfPersons() : 1 );
        }
        $extras_titles = implode( ', ', $titles );
        if ( $format == 'text' ) {
            /** @see Lib\Utils\Common::formatTitle() */
            $extras_titles = str_replace( '&nbsp;&times;&nbsp;', ' x ', $extras_titles );
        }

        $codes['extras'] = $extras_titles;
        $codes['extras_total_price'] = BooklyLib\Utils\Price::format( $price );

        return $codes;
    }
}