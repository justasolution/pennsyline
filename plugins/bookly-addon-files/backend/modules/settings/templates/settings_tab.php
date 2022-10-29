<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Settings\Inputs;

?>
<div class="tab-pane" id="bookly_settings_files">
    <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'files' ) ) ?>">
        <div class="card-body">
            <?php Inputs::renderText( 'bookly_files_directory', __( 'Upload directory', 'bookly' ), __( 'Enter the network folder path where the files will be stored. If necessary, make sure that there is no free web access to the folder materials.', 'bookly' ) ) ?>
            <?php if ( ! wp_is_writable( get_option( 'bookly_files_directory' ) ?: ABSPATH . 'wp-admin' ) ): ?>
                <div class='alert alert-danger form-group mt-n2 p-1'><i class='fas pl-1 fa-times'></i> <?php esc_html_e( 'The specified folder is not writable', 'bookly' ) ?></div>
            <?php endif ?>
            <?php Inputs::renderText( 'bookly_files_extensions', __( 'Allowed file extensions', 'bookly' ), __( 'Enter file extensions that can be uploaded. For example: "png,jpg,bmp".', 'bookly' ) ) ?>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-end">
            <?php ControlsInputs::renderCsrf() ?>
            <?php Buttons::renderSubmit() ?>
            <?php Buttons::renderReset( null, 'ml-2' ) ?>
        </div>
    </form>
</div>