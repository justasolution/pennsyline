<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;

$color = isset( $appearance['main_color'] ) ? $appearance['main_color'] : get_option( 'bookly_app_color', '#f4662f' );
?>
<style>
    .bookly-card-title {
        right: .5em;
        bottom: .5em;
        width: max-content;
        max-width: calc(100% - 1em);
        max-height: calc(100% - 1em);
    }

    .bookly-card-title > div {
        overflow: hidden;
    }

    .text-bookly {
        color: <?php echo esc_attr( $color ) ?>;
    }

    .bookly-bootstrap .card:hover {
        background-color: #FDFDFD !important;
    }

    .bookly-bootstrap .btn-outline-bookly {
        color: <?php echo esc_attr( $color ) ?>;
        border-color: <?php echo esc_attr( $color ) ?>;
    }

    .bookly-bootstrap .btn-check:focus + .btn-outline-bookly, .bookly-bootstrap .btn-outline-bookly:focus {
        background-color: <?php echo esc_attr( $color ) ?>;
        border-color: <?php echo esc_attr( $color ) ?>;
        color: #000000;
        box-shadow: 0 0 0 0.25rem rgba(128, 128, 128, 0.5);
    }

    .bookly-bootstrap .btn-check:checked + .btn-outline-bookly, .bookly-bootstrap .btn-check:active + .btn-outline-bookly, .bookly-bootstrap .btn-outline-bookly:active, .bookly-bootstrap .btn-outline-bookly.active, .bookly-bootstrap .btn-outline-bookly.dropdown-toggle.show {
        background-color: <?php echo esc_attr( $color ) ?>;
        border-color: <?php echo esc_attr( $color ) ?>;
    }

    .bookly-bootstrap .btn-check:checked + .btn-outline-bookly:focus, .bookly-bootstrap .btn-check:active + .btn-outline-bookly:focus, .bookly-bootstrap .btn-outline-bookly:active:focus, .bookly-bootstrap .btn-outline-bookly.active:focus, .bookly-bootstrap .btn-outline-bookly.dropdown-toggle.show:focus {
        background-color: <?php echo esc_attr( $color ) ?>;
        border-color: <?php echo esc_attr( $color ) ?>;
        box-shadow: 0 0 0 0.25rem rgba(128, 128, 128, 0.5);
    }

    .bookly-bootstrap .btn-outline-bookly:disabled, .bookly-bootstrap .btn-outline-bookly.disabled {
        color: <?php echo esc_attr( $color ) ?> !important;
        background-color: transparent;
    }

    .bookly-bootstrap .bg-bookly {
        background-color: <?php echo esc_attr( $color ) ?> !important;
    }

    .grid a.selected {
        background-color: <?php echo esc_attr( $color ) ?> !important;
    }

    @media (hover) {
        .bookly-bootstrap .btn-outline-bookly:hover {
            background-color: <?php echo esc_attr( $color ) ?>;
            border-color: <?php echo esc_attr( $color ) ?>;
        }
    }
</style>
<?php if ( isset( $appearance['custom_css'] ) && $appearance['custom_css'] != '' ) : ?>
    <style>
        <?php echo Common::css( $appearance['custom_css'] ) ?>
    </style>
<?php endif ?>
