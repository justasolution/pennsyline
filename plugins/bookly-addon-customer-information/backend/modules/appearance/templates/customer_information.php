<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="bookly-box bookly-js-customer-information bookly-table"<?php if ( ! get_option( 'bookly_customer_information_enabled' ) ) : ?> style="display: none;"<?php endif ?>>
    <div class="bookly-form-group">
        <label for="bookly-customer-information"><?php esc_html_e( 'Customer Information', 'bookly' ) ?></label>
        <div class="bookly-form-field">
            <input class="bookly-form-element" id="bookly-customer-information" type="text"/>
        </div>
    </div>
</div>