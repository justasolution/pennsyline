<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Modules\Appearance\Codes;
use Bookly\Backend\Components\Editable\Elements;
use Bookly\Backend\Modules\Appearance\Proxy;
use Bookly\Lib\Config;
use Bookly\Lib\Utils\Price;
?>
<div class="bookly-form">

    <!-- Progress Tracker-->
    <?php echo $progress_tracker ?>

    <div class="bookly-box">
        <?php Elements::renderText( 'bookly_l10n_info_extras_step', Codes::getJson( 2 ) ) ?>
    </div>
    <div class="bookly-extra-step">
        <div class="bookly-box">
            <div class="bookly-extras-item">
                <div class="bookly-extras-thumb bookly-extras-selected">
                    <div class="bookly-js-extras-image"><img style="margin-bottom: 8px" src="<?php echo plugins_url( 'bookly-addon-service-extras/backend/modules/appearance/resources/images/medical.png' ) ?>" /></div>
                    <span class="bookly-js-extras-title extra-widget-title">Dental Care Pack</span>
                    <span class="bookly-js-extras-duration extra-widget-duration"><?php echo \Bookly\Lib\Utils\DateTime::secondsToInterval( 900 ) ?></span>
                    <strong class="bookly-js-extras-price extra-widget-price"><?php echo Price::format( 90 ) ?></strong>
                </div>
            </div>
            <div class="bookly-extras-item">
                <div class="bookly-extras-thumb">
                    <div class="bookly-js-extras-image"><img style="margin-bottom: 8px" src="<?php echo plugins_url( 'bookly-addon-service-extras/backend/modules/appearance/resources/images/teeth.png' ) ?>" /></div>
                    <span class="bookly-js-extras-title extra-widget-title">Special Toothbrush</span>
                    <span class="bookly-js-extras-duration extra-widget-duration"></span>
                    <strong class="bookly-js-extras-price extra-widget-price"><?php echo Price::format( 15 ) ?></strong>
                </div>
            </div>
            <div class="bookly-extras-item">
                <div class="bookly-extras-thumb">
                    <div class="bookly-js-extras-image"><img style="margin-bottom: 8px" src="<?php echo plugins_url( 'bookly-addon-service-extras/backend/modules/appearance/resources/images/tool.png' ) ?>" /></div>
                    <span class="bookly-js-extras-title extra-widget-title">Natural Toothpaste</span>
                    <span class="bookly-js-extras-duration extra-widget-duration"></span>
                    <strong class="bookly-js-extras-price extra-widget-price"><?php echo Price::format( 10 ) ?></strong>
                </div>
            </div>
        </div>

        <div class="bookly-extras-summary bookly-js-extras-summary bookly-box"><?php esc_html_e( 'Summary', 'bookly' ) ?>: <?php echo Price::format( 350 ) ?> + <?php echo Price::format( 90 ) ?><span></span></div>
    </div>
    <div class="bookly-box bookly-nav-steps">
        <div class="bookly-back-step bookly-js-back-step bookly-btn">
            <?php Elements::renderString( array( 'bookly_l10n_button_back' ) ) ?>
        </div>
        <?php Proxy\Cart::renderButton() ?>
        <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
            <div class="bookly-next-step bookly-js-next-step bookly-btn">
                <?php if ( Config::customJavaScriptActive() ): ?>
                    <?php Proxy\CustomJavaScript::renderNextButton( 'extras' ) ?>
                <?php else: ?>
                    <?php Elements::renderString( array( 'bookly_l10n_step_extras_button_next' ) ) ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>