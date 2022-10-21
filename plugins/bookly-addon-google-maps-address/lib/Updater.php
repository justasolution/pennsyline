<?php
namespace BooklyGoogleMapsAddress\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Updater
 * @package BooklyGoogleMapsAddress\Lib
 */
class Updater extends BooklyLib\Base\Updater
{
    function update_1_4()
    {
        $this->addL10nOptions( array(
            'bookly_l10n_label_google_maps' => __( 'Address', 'bookly' ),
        ) );
    }
}