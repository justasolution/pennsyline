<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Elements;
use Bookly\Backend\Components\Support;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Customer Information', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>

    <div class="card">
        <div class="card-body">
            <ul id="bookly-customer-information-fields" class="list-unstyled"></ul>

            <div id="bookly-js-add-fields">
                <button class="btn btn-default mb-2 mr-1" data-type="text-field"><i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Text Field', 'bookly' ) ?></button>
                <button class="btn btn-default mb-2 mr-1" data-type="textarea"><i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Text Area', 'bookly' ) ?></button>
                <button class="btn btn-default mb-2 mr-1" data-type="text-content"><i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Text Content', 'bookly' ) ?></button>
                <button class="btn btn-default mb-2 mr-1" data-type="checkboxes"><i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Checkbox Group', 'bookly' ) ?></button>
                <button class="btn btn-default mb-2 mr-1" data-type="radio-buttons"><i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Radio Button Group', 'bookly' ) ?></button>
                <button class="btn btn-default mb-2 mr-1" data-type="drop-down"><i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Drop Down', 'bookly' ) ?></button>
                <?php Bookly\Lib\Proxy\Files::renderCustomFieldButton() ?>
            </div>

            <small class="d-block d-sm-none text-muted mb-1">
                <i class="fas fa-asterisk fa-fw mr-1"></i>&ndash; <?php esc_html_e( 'Required field', 'bookly' ) ?><br/>
                <i class="fas fa-user-check fa-fw mr-1"></i>&ndash; <?php esc_html_e( 'Ask once', 'bookly' ) ?>
            </small>

            <small class="text-muted form-text"><?php esc_html_e( 'HTML allowed in all texts and labels.', 'bookly' ) ?></small>

            <ul id="bookly-templates" style="display:none">

                <li data-type="textarea">
                    <div class="form-row">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf' ) ?>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <?php esc_html_e( 'Text Area', 'bookly' ) ?><span class="bookly-js-replace-code text-muted"></span>
                                <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                                   title="<?php esc_attr_e( 'Remove field', 'bookly' ) ?>"></a>
                            </div>
                            <div class="input-group">
                                <input class="bookly-js-label form-control" type="text" placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>"/>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-required custom-control-input" type="checkbox"/>
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Required field', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-asterisk"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-ask-once custom-control-input" type="checkbox" />
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Ask once', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-user-check"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </li>

                <li data-type="text-content">
                    <div class="form-row">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf' ) ?>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <?php esc_html_e( 'Text Content', 'bookly' ) ?>
                                <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                                   title="<?php esc_attr_e( 'Remove field', 'bookly' ) ?>"></a>
                            </div>
                            <textarea class="bookly-js-label form-control" type="text" rows="3"
                                      placeholder="<?php esc_attr_e( 'Enter a content', 'bookly' ) ?>"></textarea>
                        </div>
                    </div>
                    <hr>
                </li>

                <li data-type="text-field">
                    <div class="form-row">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf' ) ?>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <?php esc_html_e( 'Text Field', 'bookly' ) ?><span class="bookly-js-replace-code text-muted"></span>
                                <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                                   title="<?php esc_attr_e( 'Remove field', 'bookly' ) ?>"></a>
                            </div>
                            <div class="input-group">
                                <input class="bookly-js-label form-control" type="text"
                                       placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>"/>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-required custom-control-input" type="checkbox"/>
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Required field', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-asterisk"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-ask-once custom-control-input" type="checkbox" />
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Ask once', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-user-check"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </li>

                <li data-type="checkboxes">
                    <div class="form-row">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf' ) ?>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <?php esc_html_e( 'Checkbox Group', 'bookly' ) ?><span class="bookly-js-replace-code text-muted"></span>
                                <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                                   title="<?php esc_attr_e( 'Remove field', 'bookly' ) ?>"></a>
                            </div>
                            <div class="input-group">
                                <input class="bookly-js-label form-control" type="text"
                                       placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>"/>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-required custom-control-input" type="checkbox"/>
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Required field', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-asterisk"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-ask-once custom-control-input" type="checkbox" />
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Ask once', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-user-check"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="bookly-js-items list-unstyled mt-2"></ul>
                            <button class="btn btn-sm btn-default mt-1" data-type="checkboxes-item">
                                <i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Checkbox', 'bookly' ) ?>
                            </button>
                        </div>
                    </div>
                    <hr>
                </li>

                <li data-type="radio-buttons">
                    <div class="form-row">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf' ) ?>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <?php esc_html_e( 'Radio Button Group', 'bookly' ) ?><span class="bookly-js-replace-code text-muted"></span>
                                <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                                   title="<?php esc_attr_e( 'Remove field', 'bookly' ) ?>"></a>
                            </div>
                            <div class="input-group">
                                <input class="bookly-js-label form-control" type="text"
                                       placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>"/>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-required custom-control-input" type="checkbox"/>
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Required field', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-asterisk"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-ask-once custom-control-input" type="checkbox" />
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Ask once', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-user-check"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="bookly-js-items list-unstyled mt-2"></ul>
                            <button class="btn btn-sm btn-default mt-1" data-type="radio-buttons-item">
                                <i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Radio Button', 'bookly' ) ?>
                            </button>
                        </div>
                    </div>
                    <hr>
                </li>

                <li data-type="drop-down">
                    <div class="form-row">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf' ) ?>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <?php esc_html_e( 'Drop Down', 'bookly' ) ?><span class="bookly-js-replace-code text-muted"></span>
                                <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                                   title="<?php esc_attr_e( 'Remove field', 'bookly' ) ?>"></a>
                            </div>
                            <div class="input-group">
                                <input class="bookly-js-label form-control" type="text"
                                       placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>"/>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-required custom-control-input" type="checkbox"/>
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Required field', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-asterisk"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            <input class="bookly-js-ask-once custom-control-input" type="checkbox" />
                                            <label class="custom-control-label">
                                                <span class="d-none d-sm-inline"><?php esc_html_e( 'Ask once', 'bookly' ) ?></span>
                                                <i class="d-sm-none fas fa-fw fa-user-check"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="bookly-js-items list-unstyled mt-2"></ul>
                            <button class="btn btn-sm btn-default mt-1" data-type="drop-down-item">
                                <i class="fas fa-fw fa-plus mr-1"></i><?php esc_html_e( 'Option', 'bookly' ) ?>
                            </button>
                        </div>
                    </div>
                    <hr>
                </li>

                <li data-type="checkboxes-item">
                    <div class="form-row align-items-center mb-2">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf-item' ) ?>
                        </div>
                        <div class="col">
                            <input class="form-control" type="text"
                                   placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>" />
                        </div>
                        <div class="col-auto">
                            <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                               title="<?php esc_attr_e( 'Remove item', 'bookly' ) ?>"></a>
                        </div>
                    </div>
                </li>

                <li data-type="radio-buttons-item">
                    <div class="form-row align-items-center mb-2">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf-item' ) ?>
                        </div>
                        <div class="col">
                            <input class="form-control" type="text"
                                   placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>" />
                        </div>
                        <div class="col-auto">
                            <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                               title="<?php esc_attr_e( 'Remove item', 'bookly' ) ?>"></a>
                        </div>
                    </div>
                </li>

                <li data-type="drop-down-item">
                    <div class="form-row align-items-center mb-2">
                        <div class="col-auto">
                            <?php Elements::renderReorder( 'bookly-js-reorder-cf-item' ) ?>
                        </div>
                        <div class="col">
                            <input class="form-control" type="text"
                                   placeholder="<?php esc_attr_e( 'Enter a label', 'bookly' ) ?>" />
                        </div>
                        <div class="col-auto">
                            <a href="#" class="bookly-js-delete far fa-fw fa-trash-alt text-danger"
                               title="<?php esc_attr_e( 'Remove item', 'bookly' ) ?>"></a>
                        </div>
                    </div>
                </li>
                <?php Bookly\Lib\Proxy\Files::renderCustomFieldTemplate( $services_html, $description_html ) ?>
            </ul>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-end">
            <?php Buttons::renderSubmit( 'ajax-send-fields' ) ?>
            <?php Buttons::renderReset( null, 'ml-2' ) ?>
        </div>
    </div>
</div>