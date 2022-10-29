<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
/** @var Bookly\Lib\Entities\Staff $staff */
$hide  = isset ( $attributes['hide'] ) ? explode( ',', $attributes['hide'] ) : array();
?>
<div class="row">
    <div class="col-md-auto">
        <div id="bookly-js-staff-avatar">
            <div class="form-group">
                <?php $img = $staff->getImageUrl( 'thumbnail' ) ?>

                <div class="bookly-js-image bookly-thumb<?php echo $img ? ' bookly-thumb-with-image' : '' ?>"
                    <?php echo $img ? 'style="background-image: url(' . $img . '); background-size: cover;"' : '' ?>
                >
                    <i class="fas fa-fw fa-4x fa-camera mt-2 text-white w-100"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <h3 class="bookly-js-staff-name"><?php echo esc_html( $staff->getFullName() ) ?></h3>
    </div>
</div>
<div id="bookly-details-container">
    <form class="bookly-js-staff-details">
        <div class="form-group">
            <label for="bookly-full-name"><?php esc_html_e( 'Full name', 'bookly' ) ?></label>
            <input type="text" class="form-control" id="bookly-full-name" name="full_name" value="<?php echo esc_attr( $staff->getFullName() ) ?>"/>
        </div>

        <div class="form-row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="bookly-email"><?php esc_html_e( 'Email', 'bookly' ) ?></label>
                    <input class="form-control" id="bookly-email" name="email"
                           value="<?php echo esc_attr( $staff->getEmail() ) ?>"
                           type="text"/>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="bookly-phone"><?php esc_html_e( 'Phone', 'bookly' ) ?></label>
                    <input class="form-control" id="bookly-phone"
                           value="<?php echo esc_attr( $staff->getPhone() ) ?>"
                           type="text"/>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="bookly-info"><?php esc_html_e( 'Info', 'bookly' ) ?></label>
            <textarea id="bookly-info" name="info" rows="3" class="form-control"><?php echo esc_textarea( $staff->getInfo() ) ?></textarea>
            <small class="form-text text-muted"><?php esc_html_e( 'This text can be inserted into notifications to customers by Administrator.', 'bookly' ) ?></small>
        </div>
        <?php if ( ! in_array( 'visibility', $hide ) ) : ?>
            <div class="form-group" id="bookly-visibility" data-default="<?php echo esc_attr( $staff->getVisibility() ) ?>">
                <?php
                Inputs::renderRadioGroup(
                    __( 'Visibility', 'bookly' ),
                    __( 'If you want to become invisible to your customers set the visibility to "Private".', 'bookly' ),
                    array( 'public' => array( 'title' => __( 'Public', 'bookly' ) ), 'private' => array( 'title' => __( 'Private', 'bookly' ) ) ),
                    $staff->getVisibility(),
                    array( 'name' => 'visibility' ) )
                ?>
            </div>
        <?php endif ?>
        <?php Proxy\Shared::renderStaffDetails( $staff ) ?>

        <input type="hidden" name="id" value="<?php echo $staff->getId() ?>">
        <input type="hidden" name="staff_cabinet" value="1">

        <div class="text-right">
            <hr/>
            <?php Buttons::renderSubmit( 'bookly-details-save' ) ?>
            <?php Buttons::renderReset() ?>
        </div>
    </form>
</div>
