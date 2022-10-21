<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Lib as BooklyLib;
?>
<div class="tab-pane" id="bookly_settings_service_extras">
    <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'service_extras' ) ) ?>">
        <div class="card-body">
            <?php if ( BooklyLib\Config::groupBookingActive() ) : ?>
                <?php Selects::renderSingle( 'bookly_service_extras_multiply_nop', __( 'Multiply extras by number of persons', 'bookly' ), __( 'If enabled, all extras will be multiplied by number of persons.', 'bookly' ) ) ?>
            <?php endif ?>
            <?php Selects::renderSingle( 'bookly_service_extras_after_step_time', __( 'Extras Step', 'bookly' ), null, array(
                    array( 0, __( 'After Service step', 'bookly' ) ),
                    array( 1, __( 'After Time step (Extras duration settings will be ignored)', 'bookly' ) ),
                ) ) ?>
            <?php Selects::renderMultiple( 'bookly_service_extras_show', __( 'Show', 'bookly' ), null, array( array( 'title', __( 'Title', 'bookly' ) ), array( 'price', __( 'Price', 'bookly' ) ), 'image' => array( 'image', __( 'Image', 'bookly' ) ), 'duration' => array( 'duration', __( 'Duration', 'bookly' ) ), 'summary' => array( 'summary', __( 'Summary', 'bookly' ) ) ) ) ?>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-end">
            <?php ControlsInputs::renderCsrf() ?>
            <?php Buttons::renderSubmit() ?>
            <?php Buttons::renderReset( null, 'ml-2' ) ?>
        </div>
    </form>
</div>