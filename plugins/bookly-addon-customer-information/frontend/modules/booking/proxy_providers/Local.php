<?php
namespace BooklyCustomerInformation\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Proxy\CustomerInformation as CustomerInformationProxy;
use BooklyCustomerInformation\Lib;

/**
 * Class Local
 * @package BooklyCustomerInformation\Frontend\Modules\Booking
 */
class Local extends CustomerInformationProxy
{
    /**
     * @inheritDoc
     */
    public static function renderDetailsStep( BooklyLib\UserBookingData $userData )
    {
        $fields = Lib\ProxyProviders\Local::getTranslatedFields();
        $data   = array();
        foreach ( $userData->getInfoFields() as $field ) {
            $data[ $field['id'] ] = $field['value'];
        }
        $is_logged_in = $userData->getCustomer() && get_current_user_id() > 0 && $userData->getCustomer()->getWpUserId() == get_current_user_id();

        self::renderTemplate( '6_details', compact( 'fields', 'data', 'is_logged_in' ) );
    }
}