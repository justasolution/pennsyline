<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib as BooklyLib;
?>
<div class="bookly-box">
    <div class="bookly-form-group">
        <label><?php echo BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_google_maps' ) ?></label>
        <div>
            <input class="bookly-js-cst-address-autocomplete" type="text" placeholder=""/>
        </div>
    </div>
</div>