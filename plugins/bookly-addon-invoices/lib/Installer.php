<?php
namespace BooklyInvoices\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Installer
 * @package BooklyInvoices\Lib
 */
class Installer extends Base\Installer
{
    /** @var array */
    protected $notifications = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Notifications email & sms.
        $default_settings = json_decode( '{"status":"any","option":2,"services":{"any":"any","ids":[]},"offset_hours":2,"perform":"before","at_hour":9,"before_at_hour":18,"offset_before_hours":-24,"offset_bidirectional_hours":0}', true );
        $default_settings['option'] = 1;
        $default_settings['status'] = 'pending';
        $settings = json_encode( $default_settings );
        $this->notifications[] = array(
            'gateway' => 'email',
            'type' => 'new_booking',
            'name' => __( 'Invoice #{invoice_number} for your appointment', 'bookly' ),
            'subject' => __( 'Invoice #{invoice_number} for your appointment', 'bookly' ),
            'message' => __( "Dear {client_name}.\n\nAttached please find invoice #{invoice_number} for your appointment.\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}", 'bookly' ),
            'to_customer' => 1,
            'settings' => $settings,
            'attach_invoice' => 1,
        );
        $this->notifications[] = array(
            'gateway' => 'sms',
            'type' => 'new_booking',
            'name' => __( 'New invoice', 'bookly' ),
            'subject' => __( 'New invoice', 'bookly' ),
            'message' => __( "Hello.\nYou have a new invoice #{invoice_number} for an appointment scheduled by {client_first_name} {client_last_name}.\nPlease download invoice here: {invoice_link}", 'bookly' ),
            'to_staff' => 1,
            'settings' => $settings,
        );

        $this->options = array(
            'bookly_invoices_due_days' => 30,
            'bookly_invoices_footer_attachment_id' => '',
            'bookly_invoices_header_attachment_id' => '',
            'bookly_invoices_font_name' => 'freesans',
            'bookly_invoices_show_download_invoice' => '0',
            'bookly_l10n_invoice_bill_to_label' => '',
            'bookly_l10n_invoice_bill_to_label_right' => '',
            'bookly_l10n_invoice_bill_to_data' => '<b>' . __( 'BILL TO', 'bookly' ) . "</b>\n{client_name}\n{client_address}\n{client_phone}",
            'bookly_l10n_invoice_bill_to_data_right' => __( 'Invoice#', 'bookly' ) . " {invoice_number}\n" . __( 'Date', 'bookly' ) . ": {invoice_date}\n" . __( 'Due date', 'bookly' ) . ': {invoice_due_date}',
            'bookly_l10n_invoice_company_label' => '',
            'bookly_l10n_invoice_company_label_right' => '',
            'bookly_l10n_invoice_company_data' => "<b>{company_name}</b>\n{company_address}\n{company_phone}\n{company_website}",
            'bookly_l10n_invoice_company_data_right' => '',
            'bookly_l10n_invoice_company_logo' => '{company_logo}',
            'bookly_l10n_invoice_info_data' => '',
            'bookly_l10n_invoice_info_data_right' => '',
            'bookly_l10n_invoice_label' => '',
            'bookly_l10n_invoice_label_top' => '',
            'bookly_l10n_invoice_label_bottom' => __( 'INVOICE', 'bookly' ),
            'bookly_l10n_invoice_thank_you' => "\n<b>" . __( 'Thank you for your business!', 'bookly' ) . "</b>\n" . __( 'We really appreciate it', 'bookly' ),
            'bookly_l10n_button_download_invoice' => __( 'Download invoice', 'bookly' ),
        );
    }

    public function loadData()
    {
        parent::loadData();

        // Insert notifications.
        foreach ( $this->notifications as $data ) {
            $notification = new BooklyLib\Entities\Notification();
            $notification->setFields( $data )->save();
        }
    }
}