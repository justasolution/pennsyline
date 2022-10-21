<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Editable\Elements;
?>
<div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'mr-2 bookly-left' : 'ml-2 bookly-right' ?>">
    <div class='bookly-next-step bookly-js-next-step bookly-btn'>
        <?php Elements::renderString( array( 'bookly_l10n_button_download_invoice' ) ) ?>
    </div>
</div>