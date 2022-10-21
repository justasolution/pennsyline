<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
?>
<div class="col-md-3 my-2">
    <div id="bookly-show-files-popover" data-container="#bookly-show-files-popover" data-toggle="bookly-popover" data-trigger="hover" data-placement="left" data-content="<?php esc_attr_e( 'Show custom fields required', 'bookly' ) ?>">
        <?php Inputs::renderCheckBox( __( 'Show files', 'bookly' ), null, get_option( 'bookly_files_enabled' ), array( 'id' => 'bookly-show-files' ) ) ?>
    </div>
</div>