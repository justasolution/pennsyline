<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php foreach ( $data as $extras ) : ?>
    <tr data-cart-key="<?php echo $cart_key ?>" class="bookly-cart-sub-item">
        <?php foreach ( $positions as $position ) : ?>
            <?php if ( isset( $positions['price'] ) && $position == $positions['price'] ) : ?>
                <td class='bookly-rtext'>
                    <?php if ( $nop > 1 && get_option( 'bookly_service_extras_multiply_nop' ) ) : ?>
                        <i class="bookly-icon-user"></i><?php echo $nop ?> &times;
                    <?php endif ?>
                    <?php if ( $extras['quantity'] > 1 ): ?>
                        <?php echo $extras['quantity'] ?> &times;
                    <?php endif ?>
                    <?php echo $extras['price'] ?>
                    <?php if ( ( $nop > 1 && get_option( 'bookly_service_extras_multiply_nop' ) ) || $extras['quantity'] > 1 ) {
                        echo '= ' . $extras['total'];
                    } ?>
                </td>
            <?php elseif ( isset( $positions['service'] ) && $position == $positions['service'] ) : ?>
                <td>
                    <?php echo $extras['title'] ?>
                </td>
            <?php elseif ( isset( $positions['tax'] ) && $position == $positions['tax'] ) : ?>
                <td>
                    <?php echo $extras['tax'] ?>
                </td>
            <?php else: ?>
                <td>
                </td>
            <?php endif ?>
        <?php endforeach ?>
        <td></td>
    </tr>
<?php endforeach ?>
