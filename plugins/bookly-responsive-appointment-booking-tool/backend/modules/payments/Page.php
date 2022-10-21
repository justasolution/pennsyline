<?php
namespace Bookly\Backend\Modules\Payments;

use Bookly\Lib;

/**
 * Class Page
 * @package Bookly\Backend\Modules\Payments
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias'  => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/payments.js' => array( 'bookly-backend-globals' ) ),
        ) );

//         $paymentsTable = Lib\Utils\Tables::getSettings( 'payments' );
//         $gaymentsTable =  Lib\Utils\Tables::getSettings( 'gayments' );
//         $maymentsTable =  Lib\Utils\Tables::getSettings( 'mayments' );

        //$object->setProperties(array('something' => 'someText', 'somethingElse' => 'more text', 'moreThings'=>'a lot more text'));


        $datatables['payments'] = Lib\Utils\Tables::getSettings( 'payments' )['payments'];
        $datatables['gayments'] = Lib\Utils\Tables::getSettings( 'gayments' )['gayments'];
        $datatables['mayments'] = Lib\Utils\Tables::getSettings( 'mayments' )['mayments'];

        //$object = (object) $datatables;

        //$datatables = json_decode(json_encode([$paymentsTable,$gaymentsTable,$maymentsTable]), FALSE);

        wp_localize_script( 'bookly-payments.js', 'BooklyL10n', array(
            'csrfToken'      => Lib\Utils\Common::getCsrfToken(),
            'datePicker'     => Lib\Utils\DateTime::datePickerOptions(),
            'dateRange'      => Lib\Utils\DateTime::dateRangeOptions( array( 'lastMonth' => __( 'Last month', 'bookly' ), ) ),
            'zeroRecords'    => __( 'No payments for selected period and criteria.', 'bookly' ),
            'processing'     => __( 'Processing...', 'bookly' ),
            'details'        => __( 'Details', 'bookly' ),
            'areYouSure'     => __( 'Are you sure?', 'bookly' ),
            'noResultFound'  => __( 'No result found', 'bookly' ),
            'searching'      => __( 'Searching', 'bookly' ),
            'multiple'       => __( 'See details for more items', 'bookly' ),
            'datatables'     => $datatables,
            'invoice'        => array(
                'enabled' => (int) Lib\Config::invoicesActive(),
                'button'  => __( 'Invoice', 'bookly' ),
            ),
        ) );

        $types = array(
            Lib\Entities\Payment::TYPE_LOCAL,
            Lib\Entities\Payment::TYPE_2CHECKOUT,
            Lib\Entities\Payment::TYPE_PAYPAL,
            Lib\Entities\Payment::TYPE_AUTHORIZENET,
            Lib\Entities\Payment::TYPE_STRIPE,
            Lib\Entities\Payment::TYPE_CLOUD_STRIPE,
            Lib\Entities\Payment::TYPE_PAYUBIZ,
            Lib\Entities\Payment::TYPE_PAYULATAM,
            Lib\Entities\Payment::TYPE_PAYSON,
            Lib\Entities\Payment::TYPE_MOLLIE,
            Lib\Entities\Payment::TYPE_FREE,
            Lib\Entities\Payment::TYPE_WOOCOMMERCE,
        );

        $providers = Lib\Entities\Staff::query()->select( 'id, full_name' )->sortBy( 'full_name' )->whereNot( 'visibility', 'archive' )->fetchArray();
        $categories = Lib\Entities\Category::query()->sortBy( 'position' )->fetchArray();
        //var_dump($categories);
        $category_name = '';

        //echo "<pre>".print_r($categories,true)."</pre>";
        $servicesTemp  = Lib\Entities\Service::query('id, title')->sortBy( 'title' )->fetchArray();
        //$category_name = '';
        $services = array();
        foreach($servicesTemp as $service){
            foreach($categories as $category){
                //$service = Lib\Entities\Service::find( $category['id'] );
                if($category['id'] === $service['category_id']){
                    //echo "<pre>".print_r($category,true)."</pre>";
                    $service['category_name'] = $category['name'];
                    $services[] = $service;
                    //echo "<pre>".print_r($service,true)."</pre>";
                }

//                 if ( $service->getCategoryId() ) {
//                     $category = Lib\Entities\Category::find( $service->getCategoryId() );
//                     if ( $category ) {
//                         $category_name = $category->getName();
//                     }
//                 }
                //$serviceInfo = Lib\Entities\Service::find( $service['category_id'] );
                //echo "<pre>".print_r($service,true)."</pre>";
                //$key = array_search($service['category_id'], array_column($categories, 'id'));
               // echo "<pre>".print_r($category_name,true)."</pre>";
                //$service['category_name'] = $category_name;
            }
        }
       //echo "<pre>".print_r($services,true)."</pre>";
        //var_dump($services);
       // $services['category_name'] = Lib\Proxy\Pro::getStaffCategoryName( $staff_member->getCategoryId() )
        //echo "<pre>".print_r($services,true)."</pre>";
        $customers = Lib\Entities\Customer::query()->count() < Lib\Entities\Customer::REMOTE_LIMIT
            ? array_map( function ( $row ) {
                unset( $row['id'] );

                return $row;
            }, Lib\Entities\Customer::query( 'c' )->select( 'c.id, c.full_name, c.email, c.phone' )->indexBy( 'id' )->fetchArray() )
            : false;

        self::renderTemplate( 'index', compact( 'types', 'providers','categories', 'services', 'customers', 'datatables' ) );
    }
}