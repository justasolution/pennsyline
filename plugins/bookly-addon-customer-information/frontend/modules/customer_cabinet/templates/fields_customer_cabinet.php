<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;

/** @var \stdClass[] $field */
?>

<div class="form-group" data-type="<?php echo esc_attr( $field->type )?>" data-id="customer_information_<?php echo esc_attr( $field->id ) ?>">
    <label for="info_field_<?php echo esc_attr( $field->id ) ?>"><?php echo $field->label ?></label>
    <div>
        <?php if ( $field->type == 'text-field' ) : ?>
            <input id="info_field_<?php echo esc_attr( $field->id ) ?>" name="info_field_<?php echo esc_attr( $field->id ) ?>" value="<?php echo isset( $field->value ) ? esc_attr( $field->value ) : '' ?>" type="text" class="form-control bookly-js-control-input" />

        <?php elseif ( $field->type == 'textarea' ) : ?>
            <textarea id="info_field_<?php echo esc_attr( $field->id ) ?>" name="info_field_<?php echo esc_attr( $field->id ) ?>" rows="3" class="form-control bookly-js-control-input"><?php echo isset( $field->value ) ? esc_attr( $field->value ) : '' ?></textarea>

        <?php elseif ( $field->type == 'checkboxes' ) : ?>
            <?php foreach ( $field->items as $i => $item ) : ?>
                <?php Inputs::renderCheckBox( $item, esc_attr( $item ), isset( $field->value ) && in_array( $item, $field->value ), array( 'name' => 'info_field_checkbox_' . esc_attr( $field->id ), 'class' => ' bookly-js-control-input' ) ); ?>
            <?php endforeach ?>

        <?php elseif ( $field->type == 'radio-buttons' ) : ?>
            <?php foreach ( $field->items as $item ) : ?>
                <div class="radio">
                    <?php Inputs::renderRadio( $item, esc_attr( $item ), isset( $field->value ) && $field->value == $item, array( 'name' => 'info_field_' . esc_attr( $field->id ), 'class' => ' bookly-js-control-input' ) ); ?>
                </div>
            <?php endforeach ?>

        <?php elseif ( $field->type == 'drop-down' ) : ?>
            <select id="info_field_<?php echo esc_attr( $field->id ) ?>" name="info_field_<?php echo esc_attr( $field->id ) ?>" class="form-control custom-select bookly-js-control-input">
                <option value=""></option>
                <?php foreach ( $field->items as $item ) : ?>
                    <option value="<?php echo esc_attr( $item ) ?>"<?php selected( isset( $field->value ) && $field->value == $item ) ?>><?php echo $item ?></option>
                <?php endforeach ?>
            </select>
        <?php endif ?>
    </div>
</div>
