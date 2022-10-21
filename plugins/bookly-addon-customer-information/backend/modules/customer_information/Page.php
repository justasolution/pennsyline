<?php
namespace BooklyCustomerInformation\Backend\Modules\CustomerInformation;

use Bookly\Lib as BooklyLib;
use BooklyCustomerInformation\Lib;

/**
 * Class Page
 * @package BooklyCustomerInformation\Backend\Modules\CustomerInformation
 */
class Page extends BooklyLib\Base\Component
{
    /**
     *  Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'bookly' => array( 'backend/resources/css/fontawesome-all.min.css' => array ( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'bookly' => array( 'backend/resources/js/sortable.min.js' => array( 'bookly-backend-globals' ), ),
            'module' => array( 'js/customer_information.js' => array( 'bookly-sortable.min.js' ) ),
        ) );

        wp_localize_script( 'bookly-customer_information.js', 'BooklyCustomerInformationL10n', array(
            'csrfToken' => BooklyLib\Utils\Common::getCsrfToken(),
            'fields'    => Lib\ProxyProviders\Local::getFields(),
            'saved'     => __( 'Settings saved.', 'bookly' ),
        ) );

        self::renderTemplate( 'index' );
    }
}