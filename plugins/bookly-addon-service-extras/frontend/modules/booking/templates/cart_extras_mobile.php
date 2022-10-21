<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php foreach ( $data as $extras ) : ?>
    <tr data-cart-key="<?php echo $cart_key ?>" class="bookly-cart-primary">
        <th><?php esc_html_e( 'Extras', 'bookly' ) ?></th>
        <td><?php echo $extras['title'] ?></td>
    </tr>
    <?php if ( isset( $positions['price'] ) ) : ?>
        <?php if ( $nop > 1 || $extras['quantity'] > 1 ) : ?>
        <tr data-cart-key="<?php echo $cart_key ?>" class="bookly-cart-sub-item">
            <th><?php esc_html_e( 'Quantity', 'bookly' ) ?></th>
            <td>
                <?php if ( $nop > 1 ) : ?>
                    <i class="bookly-icon-user"></i>
                    <?php echo $nop ?> &times;
                <?php endif ?>
                <?php if ( $extras['quantity'] > 1 ):  ?>
                    <?php echo $extras['quantity'] ?> &times;
                <?php endif ?>
                <?php echo $extras['price'] ?>
            </td>
        </tr>
        <?php endif ?>
        <?php if ( isset( $extras['tax'], $positions['tax'] ) ) : ?>
            <tr data-cart-key="<?php echo $cart_key ?>" class="bookly-cart-sub-item">
                <th><?php esc_html_e( 'Extras tax', 'bookly' ) ?></th>
                <td>
                    <?php echo $extras['tax'] ?>
                </td>
            </tr>
        <?php endif ?>
        <tr data-cart-key="<?php echo $cart_key ?>" class="bookly-cart-sub-item">
            <th><?php esc_html_e( 'Extras price', 'bookly' ) ?></th>
            <td>
                <?php echo $extras['total'] ?>
            </td>
        </tr>
    <?php endif ?>
<?php endforeach ?>