<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Settings\Inputs;
?>
<div class="tab-pane" id="bookly_settings_google_maps">
    <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'google_maps' ) ) ?>">
        <div class="card-body">
            <div class="form-group">
                <h4><?php esc_html_e( 'Instructions', 'bookly' ) ?></h4>
                <p><?php esc_html_e( 'Follow these steps to get an API key:', 'bookly' ) ?></p>
                <ol>
                    <li><?php _e( 'Go to the <a href="https://console.developers.google.com/flows/enableapi?apiid=places_backend&reusekey=true" target="_blank">Google API Console</a>.', 'bookly' ) ?></li>
                    <li><?php _e( 'Create or select a project. Click <b>Continue</b> to enable the API.', 'bookly' ) ?></li>
                    <li><?php _e( 'On the <b>Credentials</b> page, get an <b>API key</b> (and set the API key restrictions). Note: If you have an existing unrestricted API key, or a key with server restrictions, you may use that key.', 'bookly' ) ?></li>
                    <li><?php _e( 'Click <b>Library</b> on the left sidebar menu. Select Google Maps JavaScript API and make sure it\'s enabled.', 'bookly' ) ?></li>
                    <li><?php _e( 'Use your <b>API key</b> in the form below.', 'bookly' ) ?></li>
                </ol>
            </div>
            <?php Inputs::renderText(
                'bookly_google_api_key',
                __( 'API Key', 'bookly' ),
                __( 'Enter a Google API key that you got after registering your app project on the Google API Console.', 'bookly' )
            ) ?>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-end">
            <?php ControlsInputs::renderCsrf() ?>
            <?php Buttons::renderSubmit() ?>
            <?php Buttons::renderReset( null, 'ml-2' ) ?>
        </div>
    </form>
</div>