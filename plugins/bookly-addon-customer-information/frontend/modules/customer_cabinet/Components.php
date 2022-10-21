<?php
namespace BooklyCustomerInformation\Frontend\Modules\CustomerCabinet;

use Bookly\Lib as BooklyLib;

/**
 * Class Components
 * @package BooklyCustomerInformation\Frontend\Modules\CustomerCabinet
 */
class Components extends BooklyLib\Base\Component
{
    /**
     * Render customer information field for customer cabinet
     *
     * @param \stdClass[] $field
     */
    public static function render( $field )
    {
        self::renderTemplate( 'fields_customer_cabinet', compact( 'field' ) );
    }
}