<?php
namespace BooklyStaffCabinet\Backend\Components\TinyMce\ProxyProviders;

use Bookly\Backend\Components\TinyMce\Proxy;

/**
 * Class ProxyProvider
 * @package BooklyStaffCabinet\Lib
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderMediaButtons( $version )
    {
        if ( $version < 3.5 ) {
            // show button for v 3.4 and below
            echo '<a href="#TB_inline?width=640&inlineId=bookly-editor-staff-cabinet-popup&height=650" title="' . esc_attr__( 'Add Staff Cabinet', 'bookly' ) . '">' . __( 'Add Staff Cabinet', 'bookly' ) . '</a>';
        } else {
            // display button matching new UI
            $img = '<span class="bookly-media-icon"></span> ';
            echo '<a href="#TB_inline?width=640&inlineId=bookly-editor-staff-cabinet-popup&height=650" class="thickbox button bookly-media-button" title="' . esc_attr__( 'Add Staff Cabinet', 'bookly' ) . '">' . $img . __( 'Add Staff Cabinet', 'bookly' ) . '</a>';
        }
    }

    /**
     * @inheritDoc
     */
    public static function renderPopup()
    {
        self::renderTemplate( 'popup' );
    }
}