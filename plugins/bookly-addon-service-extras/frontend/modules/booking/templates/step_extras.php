<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var \BooklyServiceExtras\Lib\Entities\ServiceExtra $extra */
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\Price;
use Bookly\Frontend\Modules\Booking\Proxy;

echo $progress_tracker;
$amount = 0;
?>
<div class="bookly-box"><?php echo $info_text ?></div>

<div class="bookly-extra-step">
    <?php foreach ( $chain as $chain_id => $chain_item ) : ?>
    <div class="bookly-js-extras-container<?php if ( ! empty( $chain_item['extras'] ) ) : ?> bookly-box<?php endif ?>" data-chain="<?php echo $chain_id ?>" data-multiplier="<?php echo $chain_item['nop_multiplier'] ?>">
        <?php if ( ! empty( $chain_item['extras'] ) ) : ?>
            <?php if ( count( $chain ) > 1 ) : ?>
                <div class="bookly-box"><b><?php echo $chain_item['service_title'] ?></b></div>
            <?php endif ?>

            <?php foreach ( $chain_item['extras'] as $extra ) :
                $extra_id = $extra->getId();
                $extra_price = $extra->getPrice();
                $extra_count = empty( $chain_item['checked_extras'][ $extra_id ] ) ? $extra->getMinQuantity() : $chain_item['checked_extras'][ $extra_id ];
                ?>
                <div class="bookly-extras-item bookly-js-extras-item" data-id="<?php echo $extra_id ?>" data-price="<?php echo $extra_price ?>" data-max_quantity="<?php echo $extra->getMaxQuantity() ?>" data-min_quantity="<?php echo $extra->getMinQuantity() ?>">
                    <div class="bookly-extras-thumb bookly-js-extras-thumb<?php if ( $extra_count ): ?> bookly-extras-selected<?php endif ?>">
                        <?php if ( in_array( 'image', $show ) ) : ?>
                            <?php if ( $extra->getAttachmentId() &&
                                       $image_attributes = wp_get_attachment_image_src( $extra->getAttachmentId(), 'thumbnail' )
                            ) : ?>
                                <img style="margin-bottom: 8px" src="<?php echo $image_attributes[0] ?>"/>
                            <?php endif ?>
                        <?php endif ?>
                        <div>
                        <?php if ( in_array( 'title', $show ) ) : ?>
                            <span class='extra-widget-title'><?php echo $extra->getTranslatedTitle() ?></span>
                        <?php endif ?>
                        <?php if ( $extra->getDuration() && in_array( 'duration', $show ) ) : ?>
                            <span class='extra-widget-duration'><?php echo \Bookly\Lib\Utils\DateTime::secondsToInterval( $extra->getDuration() ) ?></span>
                        <?php endif ?>
                        <?php if ( in_array( 'price', $show ) ) : ?>
                            <span class='extra-widget-price'><?php echo Price::format( $extra_price ) ?></span>
                        <?php endif ?>
                        </div>
                    </div>
                    <div<?php if ( $extra->getMaxQuantity() <= 1 ): ?> style="display:none"<?php endif ?>>
                        <div class="bookly-extras-count-controls">
                            <button class="bookly-round bookly-js-count-control" type="button" style="margin-right: 5px"><i class="bookly-icon-sm bookly-icon-minus"></i></button><input type="text" name="extra[]" value="<?php echo $extra_count ?>"/><button class="bookly-round bookly-js-count-control bookly-extras-increment bookly-js-extras-increment" type="button"><i class="bookly-icon-sm bookly-icon-plus"></i></button>
                        </div>
                        <div class="bookly-extras-total-price bookly-js-extras-total-price">
                            <?php echo Price::format( $extra_price * $extra_count ) ?>
                        </div>
                    </div>
                </div>
                <?php if ( isset( $chain_item['checked_extras'][ $extra_id ] ) ) :
                    $amount += $extra_price * $chain_item['checked_extras'][ $extra_id ];
                endif ?>
            <?php endforeach ?>
        <?php endif ?>
    </div>
    <?php endforeach ?>

    <?php if ( in_array( 'summary', $show ) ) : ?>
        <div class="bookly-box bookly-extras-summary bookly-js-extras-summary"><?php esc_html_e( 'Summary', 'bookly' ) ?>:<?php if ( $chain_price !== null ): ?> <?php echo Price::format( $chain_price ) ?><?php endif ?><span><?php echo $amount ? ' + ' . Price::format( $amount ) : '' ?></span></div>
    <?php endif ?>
</div>

<div class="bookly-box bookly-nav-steps">
    <?php if ( $show_back_btn ) : ?>
        <button class="bookly-back-step bookly-js-back-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
            <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_back' ) ?></span>
        </button>
    <?php endif ?>
    <?php if ( $show_cart_btn ) : ?>
        <?php Proxy\Cart::renderButton() ?>
    <?php endif ?>
    <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
        <button class="bookly-next-step bookly-js-next-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
            <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_extras_button_next' ) ?></span>
        </button>
    </div>
</div>