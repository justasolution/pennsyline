<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\Price;
?>
<tr class="bookly-js-extras-cart"<?php if ( ! get_option( 'bookly_service_extras_show_in_cart' ) ) : ?> style="display: none;"<?php endif ?>>
    <td class="bookly-cart-sub-item">Extras 1</td>
    <td colspan="3"></td>
    <td>4 × <?php echo Price::format( 10 ) ?> = <?php echo Price::format( 40 ) ?></td>
    <td></td>
</tr>