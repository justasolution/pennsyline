<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\DateTime;
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Elements;
$time_interval = get_option( 'bookly_gen_time_slot_length' );
?>
<ul class="bookly-js-templates extras">
    <li class="list-group-item extra new" data-extra-id="%id%">
        <div class="row">
            <div class="col-3">
                <div class="row">
                    <div class="mr-2">
                        <?php Elements::renderReorder() ?>
                    </div>
                    <input type="hidden" name="extras[%id%][position]" value="9999">
                    <input type="hidden" name="extras[%id%][attachment_id]" value="">
                    <div class="bookly-mw-150 bookly-thumb">
                        <i class="fas fa-fw fa-4x fa-camera mt-2 text-white w-100"></i>
                        <a class="bookly-js-remove-attachment far fa-fw fa-trash-alt text-danger bookly-thumb-delete"
                           href="javascript:void(0)"
                           title="<?php esc_attr_e( 'Delete', 'bookly' ) ?>"
                           style="display: none">
                        </a>
                        <div class="bookly-thumb-edit">
                            <label class="bookly-thumb-edit-btn"><?php esc_html_e( 'Image', 'bookly' ) ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-9">
                <div class="form-group">
                    <label for="title_extras_%id%"><?php esc_html_e( 'Title', 'bookly' ) ?></label>
                    <input name="extras[%id%][title]" class="form-control bookly-js-extras-title typeahead" type="text" value="" id="title_extras_%id%" style="width:100%">
                </div>

                <div class="form-row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="price_extras_%id%"><?php esc_html_e( 'Price', 'bookly' ) ?></label>
                            <input class="form-control" type="number" step="1" name="extras[%id%][price]" value="0.00" id="price_extras_%id%">
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="duration_extras_%id%"><?php esc_html_e( 'Duration', 'bookly' ) ?></label>
                            <select name="extras[%id%][duration]" id="duration_extras_%id%" class="form-control custom-select">
                                <option value="0"><?php esc_html_e( 'OFF', 'bookly' ) ?></option>
                                <?php for ( $j = $time_interval; $j <= 720; $j += $time_interval ) : ?>
                                    <option value="<?php echo $j * 60 ?>"><?php echo DateTime::secondsToInterval( $j * 60 ) ?></option><?php endfor ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="min_quantity_extras_%id%">
                                <?php esc_html_e( 'Min quantity', 'bookly' ) ?>
                            </label>
                            <input name="extras[%id%][min_quantity]" class="form-control bookly-js-extras-quantity" type="number" step="1" id="min_quantity_extras_%id%" min="0" value="0">
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="max_quantity_extras_%id%">
                                <?php esc_html_e( 'Max quantity', 'bookly' ) ?>
                            </label>
                            <input name="extras[%id%][max_quantity]" class="form-control bookly-js-extras-quantity" type="number" step="1" id="max_quantity_extras_%id%" min="1" value="1">
                        </div>
                    </div>
                </div>

                <div class="form-group text-right">
                    <?php Buttons::renderDelete( null, 'extra-delete' ) ?>
                </div>
            </div>
        </div>
    </li>
</ul>