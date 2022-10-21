<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="bookly-js-range-row ssd_id-<?php echo $day['id'] ?>">
    <div class="form-row py-1 rounded-lg mb-1">
        <div class="col-lg-3">
            <div>
                <input class="form-control bookly-js-date mb-2 mb-lg-0" type="text" name="list[<?php echo $day['id'] ?>][date]" value="<?php echo $day['date'] !== null ? \Bookly\Lib\Utils\DateTime::formatDate( $day['date'] ) : '' ?>" data-date="<?php echo $day['date'] ?>" autocomplete="off">
                <input type="hidden" name="list[<?php echo $day['id'] ?>][id]" value="<?php echo $day['id'] ?>">
                <div class="col-xs-12 bookly-js-staff-special-days-error"></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="form-row">
                <div class="col">
                    <?php
                    $bound = array( $day['start_time'], $day['end_time'] );
                    echo $working_start->render(
                        'list[' . $day['id'] . '][start_time]',
                        $day['start_time'],
                        array( 'class' => 'bookly-js-parent-range-start form-control' )
                    );
                    ?>
                </div>
                <div class="mt-2">
                    <?php esc_html_e( 'to', 'bookly' ) ?>
                </div>
                <div class="col">
                    <?php
                    echo $working_end->render(
                        'list[' . $day['id'] . '][end_time]',
                        $day['end_time'],
                        array( 'class' => 'bookly-js-parent-range-end form-control' )
                    );
                    ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 bookly-js-special-days-breaks-list">
            <div class="bookly-js-breaks-wrapper">
                <button type="button" data-ssd_id="<?php echo $day['id'] ?>"
                        class="bookly-js-special-days-toggle-popover btn btn-link pl-0">
                    <?php esc_html_e( 'add break', 'bookly' ) ?>
                </button>
                <div class="custom-control custom-checkbox float-right mt-2">
                    <input class="custom-control-input" type="checkbox" id="bookly-delete-ssd-<?php echo $day['id'] ?>" data-ssd_id="<?php echo $day['id'] ?>"/>
                    <label class="custom-control-label" for="bookly-delete-ssd-<?php echo $day['id'] ?>"></label>
                </div>
            </div>
            <div class="bookly-js-breaks-list">
                <?php $self::renderTemplate( '_breaks', array( 'breaks' => isset( $day['breaks'] ) ? $day['breaks'] : null, 'staff_special_day_id' => $day['id'] ) ) ?>
            </div>
        </div>
    </div>
</div>