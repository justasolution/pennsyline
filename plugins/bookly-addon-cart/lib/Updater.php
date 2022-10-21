<?php
namespace BooklyCart\Lib;

use Bookly\Lib;

/**
 * Class Updates
 * @package BooklyCart\Lib
 */
class Updater extends Lib\Base\Updater
{
    public function update_2_7()
    {
        if ( get_option( 'bookly_cart_enabled' ) && get_option( 'bookly_wc_enabled' ) && get_option( 'bookly_wc_product' ) && class_exists( 'WooCommerce', false ) ) {
            update_option( 'bookly_cart_enabled', '0' );
        }
    }

    public function update_2_3()
    {
        add_option( 'bookly_app_button_book_more_near_next', '0' );
    }
}