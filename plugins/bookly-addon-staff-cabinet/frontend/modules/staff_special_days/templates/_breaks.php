<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="breaks-list">
    <?php if ( $breaks ) : ?>
        <small class="d-block d-lg-none">
            <?php esc_html_e( 'Breaks', 'bookly' ) ?>
        </small>
    <?php endif ?>
    <div class="breaks-list-content">
        <?php if ( $breaks ) : ?>
            <?php foreach ( $breaks as $break ) :
                $self::renderTemplate( '_break', array(
                    'start_time'                  => $break['start_time'],
                    'end_time'                    => $break['end_time'],
                    'staff_special_days_break_id' => $break['id'],
                    'staff_special_day_id'        => $staff_special_day_id,
                ) );
            endforeach ?>
        <?php endif ?>
    </div>
</div>