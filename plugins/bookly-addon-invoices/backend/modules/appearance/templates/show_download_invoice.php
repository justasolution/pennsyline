<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
?>
<div class="col-md-3 my-2">
    <?php Inputs::renderCheckBox( __( 'Show \'Download invoice\' button', 'bookly' ), null, get_option( 'bookly_invoices_show_download_invoice' ), array( 'id' => 'bookly-show-download-invoice' ) ) ?>
</div>