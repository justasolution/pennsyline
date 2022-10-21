<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;

/** @var array $show */
?>
<div class="bookly-js-extras-settings collapse">
    <div class="row">
        <div class="col-md-3 my-2">
            <?php Inputs::renderCheckBox( __( 'Show title', 'bookly' ), 'title', in_array( 'title', $show, true ), array( 'name' => 'bookly_service_extras_show[]' ) ) ?>
        </div>
        <div class="col-md-3 my-2">
            <?php Inputs::renderCheckBox( __( 'Show price', 'bookly' ), 'price', in_array( 'price', $show, true ), array( 'name' => 'bookly_service_extras_show[]' ) ) ?>
        </div>
        <div class="col-md-3 my-2">
            <?php Inputs::renderCheckBox( __( 'Show image', 'bookly' ), 'image', in_array( 'image', $show, true ), array( 'name' => 'bookly_service_extras_show[]' ) ) ?>
        </div>
        <div class="col-md-3 my-2">
            <?php Inputs::renderCheckBox( __( 'Show duration', 'bookly' ), 'duration', in_array( 'duration', $show, true ), array( 'name' => 'bookly_service_extras_show[]' ) ) ?>
        </div>
        <div class="col-md-3 my-2">
            <?php Inputs::renderCheckBox( __( 'Show summary', 'bookly' ), 'summary', in_array( 'summary', $show, true ), array( 'name' => 'bookly_service_extras_show[]' ) ) ?>
        </div>
    </div>
</div>