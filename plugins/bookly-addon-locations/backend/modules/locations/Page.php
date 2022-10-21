<?php
namespace BooklyLocations\Backend\Modules\Locations;

use Bookly\Lib as BooklyLib;

/**
 * Class Page
 * @package BooklyLocations\Backend\Modules\Locations
 */
class Page extends BooklyLib\Base\Component
{
    public static function render()
    {
        self::enqueueStyles( array(
            'bookly' => array( 'backend/resources/css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/locations.js' => array( 'bookly-backend-globals' ), ),
        ) );

        $staff_collection = BooklyLib\Entities\Staff::query()->select( 'id, full_name' )->indexBy( 'id' )->fetchArray();

        $datatables = BooklyLib\Utils\Tables::getSettings( 'locations' );

        wp_localize_script( 'bookly-locations.js', 'BooklyL10n', array(
            'csrfToken'   => BooklyLib\Utils\Common::getCsrfToken(),
            'edit'        => esc_attr__( 'Edit', 'bookly' ),
            'areYouSure'  => esc_attr__( 'Are you sure?', 'bookly' ),
            'zeroRecords' => esc_attr__( 'No locations found.', 'bookly' ),
            'processing'  => esc_attr__( 'Processing...', 'bookly' ),
            'reorder'     => esc_attr__( 'Reorder', 'bookly' ),
            'staff'       => array(
                'allSelected'     => esc_attr__( 'All staff', 'bookly' ),
                'nothingSelected' => esc_attr__( 'No staff selected', 'bookly' ),
                'collection'      => $staff_collection,
            ),
            'datatables'  => $datatables,
        ) );

        $staff_dropdown_data = BooklyLib\Proxy\Pro::getStaffDataForDropDown();

        self::renderTemplate( 'index', compact( 'staff_dropdown_data', 'datatables' ) );
    }

}