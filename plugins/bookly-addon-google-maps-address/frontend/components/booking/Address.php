<?php
namespace BooklyGoogleMapsAddress\Frontend\Components\Booking;

use Bookly\Lib as BooklyLib;

/**
 * Class Address
 * @package BooklyGoogleMapsAddress\Frontend\Components\Booking
 */
class Address extends BooklyLib\Base\Component
{
    public static function render()
    {
        self::renderTemplate( 'address' );
    }
}