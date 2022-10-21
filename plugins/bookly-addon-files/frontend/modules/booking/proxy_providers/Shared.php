<?php
namespace BooklyFiles\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Frontend\Modules\Booking\Proxy;

/**
 * Class Shared
 * @package BooklyFiles\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function enqueueBookingScripts( array $depends )
    {
        self::enqueueScripts( array(
            'module' => array(
                'js/jquery.iframe-transport.js' => array( 'bookly-frontend-globals' ),
                'js/jquery.fileupload.js' => array( 'bookly-jquery.iframe-transport.js', 'jquery-ui-widget' ),
                'js/files.js' => array( 'bookly-jquery.fileupload.js' ),
            ),
        ) );

        wp_localize_script( 'bookly-files.js', 'BooklyFilesL10n', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) );

        return $depends;
    }
}