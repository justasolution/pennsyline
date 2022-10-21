<?php
namespace BooklyServiceExtras\Backend\Modules\Calendar\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Modules\Calendar\Proxy;
use BooklyServiceExtras\Lib;

/**
 * Class Shared
 * @package BooklyServiceExtras\Backend\Modules\Calendar\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareAppointmentCodesData( array $codes, $appointment_data, $participants )
    {
        if ( $participants == 'one' ) {
            $ca_extras = $appointment_data['extras'];
            if ( $ca_extras != '[]' ) {
                $extras = (array) json_decode( $ca_extras, true );
                $items  = Lib\ProxyProviders\Local::findByIds( array_keys( $extras ) );
                if ( ! empty( $items ) ) {
                    $price = 0;
                    $codes['extras'] =
                        implode( ', ', array_map( function ( $extra ) use ( $extras, $appointment_data, &$price ) {
                                /** @var Lib\Entities\ServiceExtra $extra */
                                $id    = $extra->getId();
                                $title = $extra->getTitle();
                                if ( $extras[ $id ] > 1 ) {
                                    $title = $extras[ $id ] . '&nbsp;&times;&nbsp;' . $title;
                                }
                                if ( $appointment_data['extras_multiply_nop'] && $appointment_data['number_of_persons'] > 1 ) {
                                    $title = '<i class="far fa-fw fa-user"></i>&nbsp;' . $appointment_data['number_of_persons'] . '&nbsp;&times;&nbsp;' . $title;
                                }
                                $price += $extra->getPrice() * $extras[ $id ];

                                return $title;
                            }, $items )
                        );
                    $codes['extras_total_price'] = BooklyLib\Utils\Price::format( $price * ( $appointment_data['extras_multiply_nop'] ? $appointment_data['number_of_persons'] : 1 ) );
                }
            }
        }

        return $codes;
    }
}