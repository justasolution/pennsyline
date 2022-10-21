<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
?>
<div class="col-md-3 my-2">
    <?php Inputs::renderCheckBox( __( 'Show customer information', 'bookly' ), null, get_option( 'bookly_customer_information_enabled' ), array( 'id' => 'bookly-show-customer-information' ) ) ?>
</div>