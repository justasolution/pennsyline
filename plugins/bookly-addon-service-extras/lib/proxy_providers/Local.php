<?php
namespace BooklyServiceExtras\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyServiceExtras\Lib;

/**
 * Class Local
 * @package BooklyServiceExtras\Lib\ProxyProviders
 */
class Local extends BooklyLib\Proxy\ServiceExtras
{
    /**
     * Find extras by given ids.
     *
     * @param array $extras_ids
     * @return Lib\Entities\ServiceExtra[]
     */
    public static function findByIds( array $extras_ids )
    {
        return Lib\Entities\ServiceExtra::query()->whereIn( 'id', $extras_ids )->find();
    }

    /**
     * Get total duration of given extras.
     *
     * @param array $extras  [extras_id => quantity]
     * @return int
     */
    public static function getTotalDuration( array $extras )
    {
        $duration = 0;
        foreach ( self::findByIds( array_keys( $extras ) ) as $extra ) {
            $duration += $extra->getDuration() * $extras[ $extra->getId() ];
        }

        return $duration;
    }

    /**
     * Get total price if given extras.
     *
     * @param array $extras [extras_id => quantity]
     * @param int   $nop
     * @return float|int
     */
    public static function getTotalPrice( array $extras, $nop = 1 )
    {
        $price = 0.0;
        foreach ( self::findByIds( array_keys( $extras ) ) as $extra ) {
            $price += $extra->getPrice() * $extras[ $extra->getId() ];
        }

        return get_option( 'bookly_service_extras_multiply_nop', 1 )
            ? $price * $nop
            : $price;
    }

    /**
     * Prepare total price of a service with given original service price, number of persons and set of extras.
     *
     * @param float $default
     * @param float $service_price
     * @param int $nop
     * @param array $extras  [extras_id => quantity]
     * @return float
     */
    public static function prepareServicePrice( $default, $service_price, $nop, array $extras )
    {
        $extras_price = self::getTotalPrice( $extras, $nop );

        if ( $extras_price == 0 ) {
            return $default;
        }

        return $service_price * $nop + $extras_price;
    }

    /**
     * Find extras by service id.
     *
     * @param int   $service_id
     * @return Lib\Entities\ServiceExtra[]
     */
    public static function findByServiceId( $service_id )
    {
        return Lib\Entities\ServiceExtra::query()->where( 'service_id', $service_id )->sortBy( 'position, id' )->find();
    }

    /**
     * Find all extras.
     *
     * @return Lib\Entities\ServiceExtra[]
     */
    public static function findAll()
    {
        return Lib\Entities\ServiceExtra::query()->sortBy( 'title' )->find();
    }

    /**
     * @param array $ca_id
     * @param bool  $translate
     * @param null  $locale
     * @return array
     */
    public static function getCAInfo( $ca_id, $translate, $locale = null )
    {
        $result = array();
        $ca     = BooklyLib\Entities\CustomerAppointment::find( $ca_id );
        if ( $ca ) {
            $extras = array();
            if ( $token = $ca->getCompoundToken() ) {
                foreach ( BooklyLib\Entities\CustomerAppointment::query( 'ca' )->where( 'ca.compound_token', $token )->fetchArray() as $ca ) {
                    $extras = $extras + json_decode( $ca['extras'], true );
                }
            } elseif ( $token = $ca->getCollaborativeToken() ) {
                foreach ( BooklyLib\Entities\CustomerAppointment::query( 'ca' )->where( 'ca.collaborative_token', $token )->fetchArray() as $ca ) {
                    $extras = $extras + json_decode( $ca['extras'], true );
                }
            } else {
                $extras = json_decode( $ca->getExtras(), true );
            }
            foreach ( self::findByIds( array_keys( $extras ) ) as $extra ) {
                $quantity = $extras[ $extra->getId() ];
                $result[] = array(
                    'title' => Lib\Utils\Common::formatTitle(
                        $translate
                            ? $extra->getTranslatedTitle( $locale )
                            : ( $extra->getTitle() ?: __( 'Untitled', 'bookly' ) ),
                        $quantity
                    ),
                    'price' => $extra->getPrice() * $quantity,
                );
            }
        }

        return $result;
    }

    /**
     * Get extras data for given json data of appointment.
     *
     * @param array $extras
     * @param bool  $translate
     * @param null  $locale
     * @return array
     */
    public static function getInfo( $extras, $translate, $locale = null )
    {
        $result = array();
        foreach ( self::findByIds( array_keys( $extras ) ) as $extra ) {
            $quantity  = $extras[ $extra->getId() ];
            $result[] = array(
                'title' => Lib\Utils\Common::formatTitle(
                    $translate
                        ? $extra->getTranslatedTitle( $locale )
                        : ( $extra->getTitle() ?: __( 'Untitled', 'bookly' ) ),
                    $quantity
                ),
                'price' => $extra->getPrice() * $quantity,
            );
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function considerDuration( $default = null )
    {
        return get_option( 'bookly_service_extras_enabled' ) && get_option( 'bookly_service_extras_after_step_time' ) == '0';
    }
}