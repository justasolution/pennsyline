<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Dialogs;
/** @var array $datatables */
?>
<div class="tab-pane" id="bookly_settings_custom_statuses">
    <div class="card-body">
        <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'custom-statuses' ) ) ?>">
            <div class="form-row">
                <h5 class="col mb-3 align-self-center"><?php esc_html_e( 'Custom statuses', 'bookly' ) ?></h5>
                <div class="col-12 col-sm-auto">
                    <?php Buttons::renderAdd( null, 'w-100 mb-3', __( 'Add status', 'bookly' ), array( 'data-toggle' => 'bookly-modal', 'data-target' => '#bookly-custom-statuses-modal' ) ) ?>
                </div>
                <?php Dialogs\TableSettings\Dialog::renderButton( 'custom_statuses', 'BooklyCustomStatusesL10n', esc_attr( add_query_arg( 'tab', 'custom_statuses' ) ) ) ?>
            </div>
            <table class="table table-striped w-100" id="bookly-custom-statuses">
                <thead>
                <tr>
                    <th></th>
                    <th width="24"></th>
                    <?php foreach ( $datatables['custom_statuses']['settings']['columns'] as $column => $show ) : ?>
                        <?php if ( $show ) : ?>
                            <th><?php echo $datatables['custom_statuses']['titles'][ $column ] ?></th>
                        <?php endif ?>
                    <?php endforeach ?>
                    <th width="75"></th>
                    <th width="16"><?php Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-custom-statuses-check-all' ) ) ?></th>
                </tr>
                </thead>
            </table>

            <div class="text-right mt-3">
                <?php Buttons::renderDelete( 'bookly-custom-statuses-delete' ) ?>
            </div>
        </form>
    </div>

    <?php include '_modal.php' ?>
    <?php Dialogs\TableSettings\Dialog::render() ?>
</div>