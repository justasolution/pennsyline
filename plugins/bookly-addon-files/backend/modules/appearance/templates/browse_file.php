<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Editable\Elements;
?>
<div class="bookly-box bookly-js-files"<?php if ( ! get_option( 'bookly_files_enabled' ) ) : ?> style="display: none;"<?php endif ?>>
    <div class="bookly-form-group">
        <label><?php esc_html_e( 'File', 'bookly' ) ?></label>
        <div>
            <div class="bookly-row">
                <div class="bookly-box bookly-table">
                    <div class="bookly-btn bookly-inline-block">
                        <?php Elements::renderString( array( 'bookly_l10n_browse' ) ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>