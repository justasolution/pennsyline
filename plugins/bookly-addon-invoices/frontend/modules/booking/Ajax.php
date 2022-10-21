<?php
namespace BooklyInvoices\Frontend\Modules\Booking;

use Bookly\Lib as BooklyLib;

/**
 * Class Ajax
 * @package BooklyInvoices\Frontend\Modules\Booking
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * Drop cart item.
     */
    public static function downloadInvoice()
    {
        $userData = new BooklyLib\UserBookingData( self::parameter( 'form_id' ) );
        if ( $userData->load() ) {
            $payment = BooklyLib\Entities\Payment::query()
                ->where( 'id', $userData->getPaymentId() )
                ->findOne();
            if ( $payment ) {
                BooklyLib\Proxy\Invoices::downloadInvoice( $payment );
            }
        }
    }
}