<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Editable\Elements;
?>
<div class="bookly-box bookly-js-google-maps bookly-table"<?php if ( ! get_option( 'bookly_google_maps_address_enabled' ) ) : ?> style="display: none;"<?php endif ?>>
    <div class="bookly-form-group">
        <?php
        Elements::renderLabel( array(
            'bookly_l10n_label_google_maps',
        ) )
        ?>
        <div class="bookly-form-field">
            <input class="bookly-form-element" type="text"/>
        </div>
    </div>
</div>