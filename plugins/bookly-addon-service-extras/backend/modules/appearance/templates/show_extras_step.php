<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
?>
<div class="col-lg-4 col-xl-3 mb-2">
    <?php Inputs::renderCheckBox( __( 'Show Extras step', 'bookly' ), null, get_option( 'bookly_service_extras_enabled' ), array( 'id' => 'bookly-show-step-extras', 'data-target' => 'bookly-step-2', 'data-type' => 'bookly-show-step-checkbox' ) ) ?>
</div>