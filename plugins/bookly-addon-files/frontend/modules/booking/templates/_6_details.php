<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
/** @var \stdClass $custom_field */
?>
<div class="bookly-row">
    <button class="bookly-btn ladda-button bookly-js-upload" data-style="zoom-in" data-spinner-size="40" type="button">
        <span class="ladda-label">
            <?php echo Common::getTranslatedOption( 'bookly_l10n_browse' ) ?>
            <input type="hidden" class="bookly-custom-field bookly-js-file" value="<?php echo esc_attr( isset( $cf_item['data'][ $custom_field->id ] ) ? $cf_item['data'][ $custom_field->id ] : '' ) ?>">
        </span>
    </button>
    <input type="file" class="bookly-js-file-upload">
    <div class="bookly-js-file-menu">
        <span class="bookly-js-file-name"><?php echo $name ?></span>
        <button class="bookly-round bookly-right" data-action="drop" title="<?php esc_attr_e( 'Remove', 'bookly' ) ?>" data-style="zoom-in" data-spinner-size="30"><i class="bookly-icon-sm bookly-icon-drop"></i></button>
    </div>
</div>