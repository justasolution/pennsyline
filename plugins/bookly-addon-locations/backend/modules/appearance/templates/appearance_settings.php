<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;

$attrs = array( 'id' => 'bookly-required-location' );
if ( get_option( 'bookly_locations_allow_services_per_location' ) ) {
    $attrs['disabled'] = 'disabled';
}
?>
<div class="col-md-3 my-2">
    <div data-toggle="bookly-popover" data-trigger="hover" data-placement="auto" class="bookly-js-simple-popover" data-content="<?php get_option( 'bookly_locations_allow_services_per_location' ) ? esc_attr_e( 'Custom settings for location enabled', 'bookly' ) : esc_attr_e( 'Show location required', 'bookly' ) ?>">
        <?php Inputs::renderCheckBox( __( 'Make selecting location required', 'bookly' ), null, get_option( 'bookly_app_required_location' ) || get_option( 'bookly_locations_allow_services_per_location' ), $attrs ) ?>
    </div>
</div>