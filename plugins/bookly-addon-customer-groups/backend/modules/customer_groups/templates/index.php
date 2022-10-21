<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components as BooklyComponents;
use BooklyCustomerGroups\Backend\Components;

/** @var array $datatables */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Customer Groups', 'bookly' ) ?></h4>
        <?php BooklyComponents\Support\Buttons::render( $self::pageSlug() ) ?>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="form-row justify-content-end">
                <div class="col-12 col-sm-auto">
                    <?php BooklyComponents\Controls\Buttons::renderDefault( 'bookly-js-general-settings', 'w-100 mb-3', __( 'General settings', 'bookly' ), array(), true ) ?>
                </div>
                <div class="col-12 col-sm-auto">
                    <?php BooklyComponents\Controls\Buttons::renderAdd( 'bookly-js-add-group', 'w-100 mb-3', __( 'New group', 'bookly' ) ) ?>
                </div>
                <?php BooklyComponents\Dialogs\TableSettings\Dialog::renderButton( 'customer_groups', 'BooklyCustomerGroupsL10n' ) ?>
            </div>

            <table id="bookly-groups-list" class="table table-striped w-100">
                <thead>
                <tr>
                    <?php foreach ( $datatables['customer_groups']['settings']['columns'] as $column => $show ) : ?>
                        <?php if ( $show ) : ?>
                            <th><?php echo $datatables['customer_groups']['titles'][ $column ] ?></th>
                        <?php endif ?>
                    <?php endforeach ?>
                    <th></th>
                    <th width="16"><?php BooklyComponents\Controls\Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-check-all' ) ) ?></th>
                </tr>
                </thead>
            </table>

            <div class="text-right mt-3">
                <?php BooklyComponents\Controls\Buttons::renderDelete() ?>
            </div>
            <div>
                <?php esc_html_e( 'Customers without group', 'bookly' ) ?>: <span class="bookly-js-no-groups"><?php echo $no_groups_count ?></span>
            </div>
        </div>
        <?php Components\Dialogs\CustomerGroup\Edit::render() ?>
        <?php BooklyComponents\Dialogs\TableSettings\Dialog::render() ?>
    </div>
</div>