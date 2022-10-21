<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Settings\Inputs;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Backend\Components;
use Bookly\Lib\Utils\Common;
use BooklyInvoices\Backend\Components\Invoice\Invoice;
?>
<div class="tab-pane" id="bookly_settings_invoices">
    <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'invoices' ) ) ?>">
        <div class="card-body">
            <div class="form-group">
                <p><?php echo esc_html( __( 'Invoices add-on requires your customers address information. So options "Make address field mandatory" in Settings/Customers and "Show address field" in Appearance/Details are activated automatically and can be disabled after Invoices add-on is deactivated.', 'bookly' ) ) ?></p>
                <?php Inputs::renderNumber( 'bookly_invoices_due_days', __( 'Invoice due days', 'bookly' ), __( 'This setting specifies the due period for the invoice (from 1 to 365 days)', 'bookly' ), 1, 1, 365 ) ?>
            </div>
            <div class="form-group">
                <?php Selects::renderSingle( 'bookly_invoices_font_name', __( 'Font', 'bookly' ), __( 'Choose the font for your invoice template', 'bookly' ), array( array( 'freesans', 'Freesans' ), array( 'freeserif', 'Freeserif' ), array( 'nanumgothic', 'Nanum Gothic' ), array( 'mplus1p', 'M PLUS 1p' ), ) ) ?>
            </div>
            <div class="form-group">
                <label class="mb-0"><?php esc_html_e( 'Invoice template', 'bookly' ) ?></label>
                <small class="form-text text-muted mb-1"><?php esc_html_e( 'Specify the template for the invoice.', 'bookly' ) ?></small>
                <?php echo Invoice::appearance() ?>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end">
            <a class="btn btn-default" href="<?php echo admin_url( 'admin-ajax.php?action=bookly_invoices_preview&csrf_token=' . Common::getCsrfToken() ) ?>"><?php esc_html_e( 'Preview', 'bookly' ) ?></a>
            <?php ControlsInputs::renderCsrf() ?>
            <?php Buttons::renderSubmit( null, 'mx-2' ) ?>
            <?php Buttons::renderReset() ?>
        </div>
    </form>
</div>
<?php Components\Editable\Elements::renderModals( 'bookly-settings-invoices' ) ?>