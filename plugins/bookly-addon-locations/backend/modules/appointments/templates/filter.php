<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="col-md-1">
    <div class='form-group'>
        <select class='form-control bookly-js-select' id='bookly-filter-location' data-placeholder="<?php esc_attr_e( 'Location', 'bookly' ) ?>">
            <option value="w/o"><?php esc_html_e( 'W/o location', 'bookly' ) ?></option>
            <?php foreach ( $locations as $location ): ?>
                <option value="<?php echo $location['id'] ?>"><?php echo esc_html( $location['name'] ) ?></option>
            <?php endforeach ?>
        </select>
    </div>
</div>