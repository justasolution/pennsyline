<?php
namespace BooklyGoogleMapsAddress\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Installer
 * @package BooklyGoogleMapsAddress\Lib
 */
class Installer extends Base\Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array(
            'bookly_google_maps_address_enabled' => '0',
            'bookly_l10n_label_google_maps'      => __( 'Address', 'bookly' ),
            'bookly_google_api_key'              => '',
        );
    }

}