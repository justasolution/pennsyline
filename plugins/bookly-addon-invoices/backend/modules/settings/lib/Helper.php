<?php
namespace BooklyInvoices\Backend\Modules\Settings\Lib;

use Bookly\Backend\Components\Editable\Elements;
use Bookly\Lib\Utils\Common;
use Bookly\Backend\Components\Settings;

class Helper
{
    /** @var string */
    public static $mode = 'preview';

    /**
     * Helper constructor.
     *
     * @param string $mode
     */
    public function __construct( $mode = 'preview' )
    {
        self::$mode = $mode;
    }

    /**
     * Render editable text (multi-line).
     *
     * @param string $option_name
     * @param string $codes
     */
    public static function renderString( $option_name, $codes = '' )
    {
        if ( self::$mode == 'preview' ) {
            echo nl2br( Common::getTranslatedOption( $option_name ) );
        } else {
            Elements::renderText( $option_name, $codes );
        }
    }

    /**
     * @param $option_name
     * @param $class
     */
    public static function renderImage( $option_name, $class )
    {
        self::$mode == 'preview'
            ? self::renderAttachmentImage( $option_name )
            : Settings\Image::render( $option_name, $class );
    }

    /**
     * @param $option_name
     */
    private static function renderAttachmentImage( $option_name )
    {
        $img = wp_get_attachment_image_src( get_option( $option_name ), 'full' );

        if ( $img ) {
            printf( '<img src="%s" />', $img[0] );
        }
    }

}