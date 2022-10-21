<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
?>
<div class="col-md-3 my-2">
    <?php Inputs::renderCheckBox( __( 'Show extras', 'bookly' ), null, get_option( 'bookly_service_extras_show_in_cart' ), array( 'id' => 'bookly-show-cart-extras' ) ) ?>
</div>