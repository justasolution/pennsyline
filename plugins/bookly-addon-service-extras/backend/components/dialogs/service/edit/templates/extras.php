<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\DateTime;
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Elements;
/** @var BooklyServiceExtras\Lib\Entities\ServiceExtra[] $extras */
?>
<div class="bookly-js-service bookly-js-service-simple">
    <div id="bookly_service_extras_container_<?php echo $service_id ?>">
        <ul class="list-group extras-container" data-service="<?php echo $service_id ?>" style="overflow: auto;">
            <?php foreach ( $extras as $extra ) : ?>
                <li class="list-group-item extra" data-extra-id="<?php echo $extra->getId() ?>">
                    <div class="row">
                        <div class="col-3">
                            <div class="row">
                                <div class="mr-2">
                                    <?php Elements::renderReorder() ?>
                                </div>
                                <input name="extras[<?php echo $extra->getId() ?>][id]"
                                       value="<?php echo $extra->getId() ?>" type="hidden">
                                <input name="extras[<?php echo $extra->getId() ?>][attachment_id]"
                                       value="<?php echo $extra->getAttachmentId() ?>" type="hidden">
                                <?php $img = wp_get_attachment_image_src( $extra->getAttachmentId(), 'thumbnail' ) ?>

                                <div class="bookly-mw-150 bookly-thumb<?php echo $img ? ' bookly-thumb-with-image' : '' ?>"
                                    <?php echo $img ? 'style="background-image: url(' . $img[0] . '); background-size: cover;"' : ''  ?>
                                >
                                    <i class="fas fa-fw fa-4x fa-camera mt-2 text-white w-100"></i>
                                    <a class="bookly-js-remove-attachment far fa-fw fa-trash-alt text-danger bookly-thumb-delete" href="javascript:void(0)" title="<?php esc_attr_e( 'Delete', 'bookly' ) ?>"
                                       <?php if ( !$img ) : ?>style="display: none;"<?php endif ?>>
                                    </a>
                                    <div class="bookly-thumb-edit">
                                        <label class="bookly-thumb-edit-btn"><?php esc_html_e( 'Image', 'bookly' ) ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-9">
                            <div class="form-group">
                                <label for="title_extras_<?php echo $extra->getId() ?>">
                                    <?php esc_html_e( 'Title', 'bookly' ) ?>
                                </label>
                                <input name="extras[<?php echo $extra->getId() ?>][title]" class="form-control" type="text" id="title_extras_<?php echo $extra->getId() ?>" value="<?php echo $extra->getTitle() ?>">
                            </div>

                            <div class="form-row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="price_extras_<?php echo $extra->getId() ?>">
                                            <?php esc_html_e( 'Price', 'bookly' ) ?>
                                        </label>
                                        <input name="extras[<?php echo $extra->getId() ?>][price]" class="form-control" type="number" step="1" id="price_extras_<?php echo $extra->getId() ?>" min="0.00" value="<?php echo $extra->getPrice() ?>">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="duration_extras_<?php echo $extra->getId() ?>">
                                            <?php esc_html_e( 'Duration', 'bookly' ) ?>
                                        </label>
                                        <select name="extras[<?php echo $extra->getId() ?>][duration]" id="duration_extras_<?php echo $extra->getId() ?>" class="form-control custom-select">
                                            <option value="0"><?php esc_html_e( 'OFF', 'bookly' ) ?></option>
                                            <?php for ( $j = $time_interval; $j <= 720; $j += $time_interval ) : ?><?php if ( $extra->getDuration() > 0 && $extra->getDuration() / 60 > $j - $time_interval && $extra->getDuration() / 60 < $j ) : ?><option value="<?php echo esc_attr( $extra->getDuration() ) ?>" selected><?php echo DateTime::secondsToInterval( $extra->getDuration() ) ?></option><?php endif ?><option value="<?php echo $j * 60 ?>" <?php selected( $extra->getDuration(), $j * 60 ) ?>><?php echo DateTime::secondsToInterval( $j * 60 ) ?></option><?php endfor ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="min_quantity_extras_<?php echo $extra->getId() ?>">
                                            <?php esc_html_e( 'Min quantity', 'bookly' ) ?>
                                        </label>
                                        <input name="extras[<?php echo $extra->getId() ?>][min_quantity]" class="form-control bookly-js-extras-quantity" type="number" step="1" id="min_quantity_extras_<?php echo $extra->getId() ?>" min="0" value="<?php echo $extra->getMinQuantity() ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="max_quantity_extras_<?php echo $extra->getId() ?>">
                                            <?php esc_html_e( 'Max quantity', 'bookly' ) ?>
                                        </label>
                                        <input name="extras[<?php echo $extra->getId() ?>][max_quantity]" class="form-control bookly-js-extras-quantity" type="number" step="1" id="max_quantity_extras_<?php echo $extra->getId() ?>" min="1" value="<?php echo $extra->getMaxQuantity() ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-right">
                                <?php Buttons::renderDelete( null, 'extra-delete' ) ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
    <div class="form-group mt-3 mb-0">
        <?php Buttons::render( null, 'bookly-js-add-extras btn-success', __( 'New Item', 'bookly' ), array(), '<i class="fas fa-fw fa-plus mt-1"></i>{caption}' ) ?>
    </div>
</div>