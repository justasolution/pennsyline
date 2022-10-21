<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
?>
<div class="col-md-3 my-2">
    <div id="bookly-show-google-maps-popover" data-toggle="bookly-popover" data-trigger="hover" data-container="#bookly-show-google-maps-popover" data-placement="auto" data-content="<?php esc_attr_e( 'Show address required', 'bookly' ) ?>">
        <?php Inputs::renderCheckBox( __( 'Show google maps field', 'bookly' ), null, get_option( 'bookly_google_maps_address_enabled' ) && get_option( 'bookly_app_show_address' ), array( 'id' => 'bookly-show-google-maps' ) ) ?>
    </div>
</div>