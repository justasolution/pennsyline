<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
?>
<div class="bookly-modal bookly-fade" id="bookly-location-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="bookly-new-locations-title"><?php esc_html_e( 'New Location', 'bookly' ) ?></h5>
                    <h5 class="modal-title" id="bookly-edit-locations-title"><?php esc_html_e( 'Edit Location', 'bookly' ) ?></h5>
                    <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class=form-group>
                                <label for="bookly-location-name"><?php esc_html_e( 'Name', 'bookly' ) ?></label>
                                <input type="text" id="bookly-location-name" class="form-control" name="name" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class=form-group>
                                <label for="bookly-location-info"><?php esc_html_e( 'Info', 'bookly' ) ?></label>
                                <textarea id="bookly-location-info" class="form-control" name="info"></textarea>
                                <small class="text-muted">
                                    <?php printf( __( 'This text can be inserted into notifications with %s code.', 'bookly' ), '{location_info}' ) ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-sm-12" >
                            <ul id="bookly-js-staff"
                                data-txt-select-all="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                                data-txt-all-selected="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                                data-txt-nothing-selected="<?php esc_attr_e( 'No staff selected', 'bookly' ) ?>"
                            >
                                <?php foreach ( $staff_dropdown_data as $category_id => $category ): ?>
                                    <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                                        <ul>
                                            <?php foreach ( $category['items'] as $staff ) : ?>
                                                <li data-input-name="staff_ids[]" data-value="<?php echo $staff['id'] ?>">
                                                    <?php echo esc_html( $staff['full_name'] ) ?>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php Inputs::renderCsrf() ?>
                    <?php Buttons::renderSubmit( 'bookly-location-save' ) ?>
                    <?php Buttons::renderCancel() ?>
                </div>
            </form>
        </div>
    </div>
</div>