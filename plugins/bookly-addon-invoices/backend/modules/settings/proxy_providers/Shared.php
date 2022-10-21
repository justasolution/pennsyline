<?php
namespace BooklyInvoices\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Components\Settings\Menu;
use Bookly\Backend\Modules\Settings\Proxy;
use BooklyInvoices\Lib;

/**
 * Class Shared
 * @package BooklyInvoices\Backend\Modules\Settings\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderMenuItem()
    {
        Menu::renderItem( __( 'Invoices', 'bookly' ), 'invoices' );
    }

    /**
     * @inheritDoc
     */
    public static function renderTab()
    {
        self::renderTemplate( 'settings_tab' );
    }

    /**
     * @inheritDoc
     */
    public static function saveSettings( array $alert, $tab, array $params )
    {
        if ( $tab == 'invoices' ) {
            $options = array(
                'bookly_invoices_due_days',
                'bookly_invoices_footer_attachment_id',
                'bookly_invoices_header_attachment_id',
                'bookly_invoices_font_name',
                'bookly_l10n_invoice_bill_to_label',
                'bookly_l10n_invoice_bill_to_data',
                'bookly_l10n_invoice_bill_to_label_right',
                'bookly_l10n_invoice_bill_to_data_right',
                'bookly_l10n_invoice_company_logo',
                'bookly_l10n_invoice_company_label',
                'bookly_l10n_invoice_company_data',
                'bookly_l10n_invoice_company_label_right',
                'bookly_l10n_invoice_company_data_right',
                'bookly_l10n_invoice_info_data',
                'bookly_l10n_invoice_info_data_right',
                'bookly_l10n_invoice_label',
                'bookly_l10n_invoice_label_top',
                'bookly_l10n_invoice_label_bottom',
                'bookly_l10n_invoice_thank_you',
            );
            foreach ( $options as $option_name ) {
                if ( array_key_exists( $option_name, $params ) ) {
                    update_option( $option_name, $params[ $option_name ] );
                    if ( strncmp( $option_name, 'bookly_l10n_', 12 ) === 0 ) {
                        do_action( 'wpml_register_single_string', 'bookly', $option_name, $params[ $option_name ] );
                    }
                }
            }
            $alert['success'][] = __( 'Settings saved.', 'bookly' );
        }

        return $alert;
    }
}