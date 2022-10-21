<?php
namespace BooklyInvoices\Lib;

use Bookly\Lib;

/**
 * Class Updates
 * @package BooklyInvoices\Lib
 */
class Updater extends Lib\Base\Updater
{
    function update_2_7()
    {
        $this->addL10nOptions( array(
            'bookly_l10n_button_download_invoice' => __( 'Download invoice', 'bookly' )
        ) );

        add_option( 'bookly_invoices_show_download_invoice', '0' );
    }

    function update_2_5()
    {
        add_option( 'bookly_invoices_font_name', 'freesans' );
    }

    function update_2_3()
    {
        $this->addL10nOptions( array(
            'bookly_l10n_invoice_bill_to_label_right' => '',
            'bookly_l10n_invoice_bill_to_data_right' => '',
            'bookly_l10n_invoice_company_label_right' => '',
            'bookly_l10n_invoice_company_data_right' => '',
            'bookly_l10n_invoice_info_data_right' => '',
            'bookly_l10n_invoice_label_top' => '',
            'bookly_l10n_invoice_label_bottom' => '',
        ) );
    }

    function update_1_2()
    {
        delete_option( 'bookly_invoices_enabled' );
    }
}