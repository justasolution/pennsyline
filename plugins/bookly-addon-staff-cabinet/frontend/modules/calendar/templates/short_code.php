<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Dialogs\Appointment;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Config;

/**
 * @var string $calendar_id
 * @var int $staff_id
 */
?>
<script type="text/javascript">
    (function(win, fn) {
        var done = false, top = true,
            doc = win.document,
            root = doc.documentElement,
            modern = doc.addEventListener,
            add = modern ? 'addEventListener' : 'attachEvent',
            rem = modern ? 'removeEventListener' : 'detachEvent',
            pre = modern ? '' : 'on',
            init = function(e) {
                if (e.type == 'readystatechange') if (doc.readyState != 'complete') return;
                (e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
                if (!done) {
                    done = true;
                    fn.call(win, e.type || e);
                }
            },
            poll = function() {
                try {
                    root.doScroll('left');
                } catch (e) {
                    setTimeout(poll, 50);
                    return;
                }
                init('poll');
            };
        if (doc.readyState == 'complete') fn.call(win, 'lazy');
        else {
            if (!modern) if (root.doScroll) {
                try {
                    top = !win.frameElement;
                } catch (e) {
                }
                if (top) poll();
            }
            doc[add](pre + 'DOMContentLoaded', init, false);
            doc[add](pre + 'readystatechange', init, false);
            win[add](pre + 'load', init, false);
        }
    })(window, function() {
        var a = document.getElementsByClassName("bookly-staff-cabinet")[0];
        while (a) {
            try {
                if (getComputedStyle(a).zIndex !== 'auto') {
                    a.style.zIndex = "auto";
                }
            } catch (e) {
            }
            a = a.parentNode;
        }
        window.booklyStaffCalendar({
            calendar_id:  <?php echo json_encode( $calendar_id ) ?>,
            staff_id: <?php echo (int) $staff_id ?>
        });
    });
    var ajaxurl = <?php echo json_encode( admin_url( 'admin-ajax.php' ) ) ?>;
</script>
<div id="bookly-tbs" class="bookly-staff-cabinet">
    <div class="mb-2 position-relative">
        <div class="bookly-ec-loading" style="display: none">
            <div class="bookly-ec-loading-icon"></div>
        </div>
        <div class="bookly-js-calendar <?php echo $calendar_id ?>"></div>
        <?php if ( ! $read_only ): ?>
            <?php Appointment\Edit\Dialog::render( ! in_array( 'wp_users', $hide ) ) ?>
            <?php Bookly\Backend\Modules\Calendar\Proxy\Shared::renderAddOnsComponents() ?>
            <?php Bookly\Backend\Components\Dialogs\Queue\Dialog::render() ?>
            <?php Appointment\Delete\Dialog::render() ?>
        <?php endif ?>
    </div>
    <div class="form-row justify-content-between bookly-js-calendar-footer">
        <div class="col-auto">
            <button type="button" class="btn btn-default bookly-js-export-btn"><i class="far fa-fw fa-share-square"></i><?php esc_html_e( 'Export to CSV', 'bookly' ) ?></button>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button type="button" class="bookly-js-calendar-refresh btn <?php echo $refresh_rate > 0 ? 'btn-success' : 'btn-default' ?>"><i class="fas fa-sync-alt"></i></button>
                <button type="button" class="btn <?php echo $refresh_rate > 0 ? 'btn-success' : 'btn-default' ?> bookly-dropdown-toggle bookly-dropdown-toggle-split" data-toggle="bookly-dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="bookly-dropdown-menu pb-0 dropdown-menu-right">
                    <h6 class="bookly-dropdown-header"><?php esc_html_e( 'Auto-refresh Calendar', 'bookly' ) ?></h6>
                    <div class="bookly-dropdown-divider"></div>
                    <?php Inputs::renderRadioGroup( null, null,
                        array(
                            '60' => array( 'title' => __( 'Every 1 minute', 'bookly' ) ),
                            '300' => array( 'title' => __( 'Every 5 minutes', 'bookly' ) ),
                            '900' => array( 'title' => __( 'Every 15 minutes', 'bookly' ) ),
                            '0' => array( 'title' => __( 'Disable', 'bookly' ) ),
                        ),
                        $refresh_rate,
                        array( 'name' => 'bookly_calendar_refresh_rate', 'parent-class' => 'mx-3 my-2 w-100' ) ) ?>
                </div>
            </div>
        </div>
    </div>
    <div id="bookly-js-export-dialog" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
        <div class="modal-dialog">
            <form action="<?php echo admin_url( 'admin-ajax.php?action=bookly_staff_cabinet_export_appointments' ) ?>" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php esc_html_e( 'Export to CSV', 'bookly' ) ?></h5>
                        <button type="button" class="close" data-dismiss="bookly-modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <?php Inputs::renderRadioGroup( null, null, array(
                                'displayed' => array( 'title' => __( 'Displayed appointments', 'bookly' ) ),
                                'upcoming' => array( 'title' => __( 'Upcoming appointments', 'bookly' ) ),
                                'all' => array( 'title' => __( 'All appointments', 'bookly' ) ),
                            ), 'displayed', array( 'name' => 'export_type' ) ) ?>
                        </div>
                        <div class="form-group">
                            <label for="bookly-csv-delimiter"><?php esc_html_e( 'Delimiter', 'bookly' ) ?></label>
                            <select name="delimiter" id="bookly-csv-delimiter" class="form-control custom-select">
                                <option value=","><?php esc_html_e( 'Comma (,)', 'bookly' ) ?></option>
                                <option value=";"><?php esc_html_e( 'Semicolon (;)', 'bookly' ) ?></option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input id="bookly-js-export-select-all" class="bookly-js-required custom-control-input" type="checkbox" checked/>
                                <label class="custom-control-label" for="bookly-js-export-select-all"><?php esc_html_e( 'Select all', 'bookly' ) ?></label>
                            </div>
                        </div>
                        <div class="form-group ml-3 bookly-js-columns">
                            <?php Inputs::renderCheckBox( __( 'Booking Time', 'bookly' ), null, true, array( 'name' => 'exp[start_date]' ) ) ?>
                            <?php Inputs::renderCheckBox( __( 'Customer Name', 'bookly' ), null, true, array( 'name' => 'exp[customer_name]' ) ) ?>
                            <?php Inputs::renderCheckBox( __( 'Customer Phone', 'bookly' ), null, true, array( 'name' => 'exp[customer_phone]' ) ) ?>
                            <?php Inputs::renderCheckBox( __( 'Customer Email', 'bookly' ), null, true, array( 'name' => 'exp[customer_email]' ) ) ?>
                            <?php Inputs::renderCheckBox( __( 'Service', 'bookly' ), null, true, array( 'name' => 'exp[service_title]' ) ) ?>
                            <?php Inputs::renderCheckBox( __( 'Duration', 'bookly' ), null, true, array( 'name' => 'exp[service_duration]' ) ) ?>
                            <?php Inputs::renderCheckBox( __( 'Status', 'bookly' ), null, true, array( 'name' => 'exp[status]' ) ) ?>
                            <?php Inputs::renderCheckBox( __( 'Payment', 'bookly' ), null, true, array( 'name' => 'exp[payment]' ) ) ?>
                            <?php if ( Config::showNotes() ): ?>
                                <?php Inputs::renderCheckBox( Common::getTranslatedOption( 'bookly_l10n_label_notes' ), null, true, array( 'name' => 'exp[notes]' ) ) ?>
                            <?php endif ?>
                            <?php foreach ( $custom_fields as $field ) : ?>
                                <?php if ( $field->type != 'file' ) : ?>
                                    <?php Inputs::renderCheckBox( $field->label, null, true, array( 'name' => 'custom-field[' . $field->id . ']' ) ) ?>
                                <?php endif ?>
                            <?php endforeach ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" class="bookly-js-export-start" name="start_date" value=""/>
                        <input type="hidden" class="bookly-js-export-end" name="end_date" value=""/>
                        <?php Inputs::renderCsrf() ?>
                        <?php Buttons::renderSubmit( null, null, __( 'Export to CSV', 'bookly' ) ) ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>