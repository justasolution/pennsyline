<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
?>
<div class="bookly-customer-information-container">
    <?php foreach ( $fields as $field ) : ?>
        <div class="bookly-box bookly-js-info-field-row" data-id="<?php echo $field->id ?>" data-type="<?php echo $field->type ?>"<?php if ( $field->type != 'text-content' && $field->ask_once && isset( $data[ $field->id ] ) && $data[ $field->id ] !== '' && $data[ $field->id ] !== null && $data[ $field->id ] !== array() && $is_logged_in ) : ?> style="display: none;" <?php endif ?>>
            <div class="bookly-form-group">
                <?php if ( $field->type != 'text-content' ) : ?>
                    <label><?php echo Common::stripScripts( $field->label_translated ) ?></label>
                <?php endif ?>
                <div>
                    <?php if ( $field->type == 'text-field' ) : ?>
                        <input type="text" class="bookly-js-info-field" value="<?php echo isset( $data[ $field->id ] ) ? esc_attr( $data[ $field->id ] ) : '' ?>"/>
                    <?php elseif ( $field->type == 'textarea' ) : ?>
                        <textarea rows="3" class="bookly-js-info-field"><?php echo isset( $data[ $field->id ] ) ? esc_html( $data[ $field->id ] ) : '' ?></textarea>
                    <?php elseif ( $field->type == 'text-content' ) : ?>
                        <?php echo nl2br( Common::stripScripts( $field->label_translated ) ) ?>
                    <?php elseif ( $field->type == 'checkboxes' ) : ?>
                        <?php foreach ( $field->items_translated as $i => $item ) : ?>
                            <label>
                                <input type="checkbox" class="bookly-js-info-field" value="<?php echo esc_attr( $field->items[ $i ] ) ?>" <?php checked( isset( $data[ $field->id ] ) && in_array( $field->items[ $i ], $data[ $field->id ] ), true, true ) ?> />
                                <span><?php echo Common::stripScripts( $item ) ?></span>
                            </label><br/>
                        <?php endforeach ?>
                    <?php elseif ( $field->type == 'radio-buttons' ) : ?>
                        <?php foreach ( $field->items_translated as $i => $item ) : ?>
                            <label>
                                <input type="radio" class="bookly-js-info-field" name="bookly-js-info-field-<?php echo $field->id ?>"
                                       value="<?php echo esc_attr( $field->items[ $i ] ) ?>" <?php checked( isset( $data[ $field->id ] ) && $field->items[ $i ] == $data[ $field->id ], true, true ) ?> />
                                <span><?php echo Common::stripScripts( $item ) ?></span>
                            </label><br/>
                        <?php endforeach ?>
                    <?php elseif ( $field->type == 'drop-down' ) : ?>
                        <select class="bookly-js-info-field">
                            <option value=""></option>
                            <?php foreach ( $field->items_translated as $i => $item ) : ?>
                                <option value="<?php echo esc_attr( $field->items[ $i ] ) ?>" <?php selected( isset( $data[ $field->id ] ) && $field->items[ $i ] == $data[ $field->id ], true, true ) ?>><?php echo esc_html( $item ) ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php endif ?>
                </div>
                <?php if ( $field->type != 'text-content' ) : ?>
                    <div class="bookly-label-error bookly-js-info-field-error"></div>
                <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>
</div>