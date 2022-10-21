<?php
namespace BooklyServiceExtras\Lib\Utils;

use BooklyServiceExtras\Lib\Entities;

/**
 * Class Common
 * @package BooklyServiceExtras\Lib\Utils
 */
abstract class Common
{
    /**
     * Format title to "Q x TITLE" when Q > 1.
     *
     * @param string $title
     * @param integer $quantity
     * @return string
     */
    public static function formatTitle( $title, $quantity )
    {
        return ( $quantity > 1 ) ? $quantity . '&nbsp;&times;&nbsp;' . $title : $title;
    }

    /**
     * @return array
     */
    public static function getExtrasList()
    {
        $extras = Entities\ServiceExtra::query( 'e' )
            ->select( 'e.*, s.title as service_title' )
            ->leftJoin( 'Service', 's', 's.id = e.service_id', 'Bookly\Lib\Entities' )
            ->sortBy( 'title' )
            ->fetchArray();
        $list   = array();

        foreach ( $extras as $extra ) {
            $list[ $extra['id'] ] = array(
                'attachment_id'      => $extra['attachment_id'],
                'image'              => wp_get_attachment_image_src( $extra['attachment_id'], 'thumbnail' ),
                'title'              => $extra['title'],
                'title_with_service' => sprintf( '%s (%s)', $extra['title'], $extra['service_title'] ),
                'duration'           => $extra['duration'],
                'price'              => $extra['price'],
                'min_quantity'       => $extra['min_quantity'],
                'max_quantity'       => $extra['max_quantity'],
            );
        }

        return $list;
    }

    /**
     * Calculate price
     *
     * @param Entities\ServiceExtra $extras
     * @param int                   $quantity
     * @param int                   $nop
     * @return float|int
     */
    public static function getExtrasPrice( Entities\ServiceExtra $extras, $quantity, $nop )
    {
        return $quantity * (
            get_option( 'bookly_service_extras_multiply_nop', 1 )
                ? $extras->getPrice() * $nop
                : $extras->getPrice()
            );
    }
}