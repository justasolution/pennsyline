<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Elements;
/** @var string $services_html */
/** @var string $description_html */
?>
<li data-type="file">
    <div class="form-row">
        <div class="col-auto">
            <?php Elements::renderReorder( 'bookly-js-reorder-cf' ) ?>
        </div>
        <div class="col">
            <div class="mb-2">
                <?php esc_html_e( 'File Upload Field', 'bookly' ) ?>
                <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                   title="<?php esc_attr_e( 'Remove field', 'bookly' ) ?>"></a>
            </div>
            <div class="form-row">
                <div class="col-md-8 mb-3">
                    <div class="input-group">
                        <input class="bookly-js-label form-control" type="text"
                               placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <div class="custom-control custom-checkbox">
                                    <input class="bookly-js-required custom-control-input" type="checkbox" />
                                    <label class="custom-control-label">
                                        <span class="d-none d-sm-inline"><?php esc_html_e( 'Required field', 'bookly' ) ?></span>
                                        <i class="d-sm-none fas fa-fw fa-asterisk"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <?php echo $services_html ?>
                </div>
            </div>
            <?php echo $description_html ?>
        </div>
    </div>
    <hr class="mt-0" />
</li>