<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
?>
<button class="bookly-nav-btn bookly-js-download-invoice bookly-btn ladda-button bookly-left" data-style="zoom-in" data-spinner-size="40">
    <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_download_invoice' ) ?></span>
</button>
