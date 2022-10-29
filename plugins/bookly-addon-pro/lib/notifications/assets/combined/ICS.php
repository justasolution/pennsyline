<?php
namespace BooklyPro\Lib\Notifications\Assets\Combined;

use Bookly\Lib\Notifications\Assets\Item\ICS as ItemICS;
use Bookly\Lib\Utils;

/**
 * Class ICS
 *
 * @package Bookly\Lib\Notifications\Assets\Item
 */
class ICS extends ItemICS
{
    protected $data;

    /**
     * Constructor.
     *
     * @param Codes $codes
     * @param string $recipient
     */
    public function __construct( Codes $codes, $recipient = 'client' )
    {
        $description_template = $this->getDescriptionTemplate( $recipient );
        $this->data =
            "BEGIN:VCALENDAR\n"
            . "VERSION:2.0\n"
            . "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\n"
            . "CALSCALE:GREGORIAN\n";
        foreach ( $codes->cart_info as $item ) {
            $description_codes = Utils\Codes::getICSCodes( $item['item'] );
            $this->data .= sprintf(
                "BEGIN:VEVENT\n"
                . "DTSTART:%s\n"
                . "DTEND:%s\n"
                . "SUMMARY:%s\n"
                . "DESCRIPTION:%s\n"
                . "LOCATION:%s\n"
                . "END:VEVENT\n",
                $this->_formatDateTime( $item['appointment_start'] ),
                $this->_formatDateTime( $item['appointment_end'] ),
                $this->_escape( $item['service_name'] ),
                $this->_escape( Utils\Codes::replace( $description_template, $description_codes, false ) ),
                $this->_escape( sprintf( "%s", $item['location'] ) )
            );
        }
        $this->data .= 'END:VCALENDAR';
    }
}