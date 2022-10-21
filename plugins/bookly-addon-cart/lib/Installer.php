<?php
namespace BooklyCart\Lib;

/**
 * Class Installer
 * @package BooklyCart\Lib
 */
class Installer extends Base\Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array(
            'bookly_cart_enabled' => 1,
            'bookly_cart_show_columns' => array(
                'service' => array( 'show' => 1 ),
                'date' => array( 'show' => 1 ),
                'time' => array( 'show' => 1 ),
                'employee' => array( 'show' => 1 ),
                'price' => array( 'show' => 1 ),
                'deposit' => array( 'show' => 1 ),
                'tax' => array( 'show' => 0 ),
            ),
            'bookly_app_button_book_more_near_next' => '0',
            'bookly_l10n_button_book_more' => __( 'Book More', 'bookly' ),
            'bookly_l10n_info_cart_step' => __( "Below you can find a list of services selected for booking.\nClick BOOK MORE if you want to add more services.", 'bookly' ),
            'bookly_l10n_step_cart' => __( 'Cart', 'bookly' ),
            'bookly_l10n_step_cart_button_next' => __( 'Next', 'bookly' ),
            'bookly_l10n_step_cart_slot_not_available' => __( 'The highlighted time is not available anymore. Please, choose another time slot.', 'bookly' ),
        );
    }
}