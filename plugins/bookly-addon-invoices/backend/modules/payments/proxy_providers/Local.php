<?php
namespace BooklyInvoices\Backend\Modules\Payments\ProxyProviders;

use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Modules\Payments\Proxy;
use Bookly\Lib as BooklyLib;

/**
 * Class Local
 * @package BooklyInvoices\Backend\Modules\Payments\ProxyProviders
 */
abstract class Local extends Proxy\Invoices
{
    /**
     * @inheritDoc
     */
    public static function renderDownloadButton()
    {
        $action = admin_url( 'admin-ajax.php?action=bookly_invoices_download_invoices&csrf_token=' . BooklyLib\Utils\Common::getCsrfToken() );

        Buttons::render( 'bookly-download-invoices', 'btn-default d-none', __( 'View/Download invoices', 'bookly' ), array( 'data-spinner-color' => '#333', 'data-action' => $action ) );
    }
    /**
     * @inheritDoc
     */
    public static function renderMasterInvoiceDownloadButton()
    {
        $action = admin_url( 'admin-ajax.php?action=bookly_invoices_download_master_invoice&csrf_token=' . BooklyLib\Utils\Common::getCsrfToken() );

        Buttons::render( 'bookly-download-master-invoice', 'btn-default d-none', __( 'View/Download invoices', 'bookly' ), array( 'data-spinner-color' => '#333', 'data-action' => $action ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderMasterInvoiceEditButton()
    {
        $action = admin_url( 'admin-ajax.php?action=bookly_invoices_edit_master_invoice&csrf_token=' . BooklyLib\Utils\Common::getCsrfToken() );

        Buttons::render( 'bookly-edit-master-invoice', 'btn-default d-none', __( 'Edit invoice', 'bookly' ), array( 'data-spinner-color' => '#333', 'data-action' => $action ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderMasterInvoicePaymentButton()
    {
        $action = admin_url( 'admin-ajax.php?action=bookly_invoices_payment_complete&csrf_token=' . BooklyLib\Utils\Common::getCsrfToken() );

        Buttons::render( 'bookly-master-payment-complete', 'btn-default d-none', __( 'Complete invoice', 'bookly' ), array( 'data-spinner-color' => '#333', 'data-action' => $action ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderGenerateInvoiceEditButton()
    {
        $action = admin_url( 'admin-ajax.php?action=bookly_invoices_edit_generate_invoice&csrf_token=' . BooklyLib\Utils\Common::getCsrfToken() );

        Buttons::render( 'bookly-edit-generate-invoice', 'btn-default d-none', __( 'Edit invoice', 'bookly' ), array( 'data-spinner-color' => '#333', 'data-action' => $action ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderGenerateInvoiceButton()
    {
        $action = admin_url( 'admin-ajax.php?action=bookly_invoices_generate_invoices&csrf_token=' . BooklyLib\Utils\Common::getCsrfToken() );

        Buttons::render( 'bookly-generate-invoices', 'btn-default', __( 'Generate Invoice', 'bookly' ), array( 'data-spinner-color' => '#333', 'data-action' => $action ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderGenerateMasterInvoiceButton()
    {
        $action = admin_url( 'admin-ajax.php?action=bookly_invoices_master_invoices&csrf_token=' . BooklyLib\Utils\Common::getCsrfToken() );

        Buttons::render( 'bookly-master-invoices', 'btn-default', __( 'Master Invoice', 'bookly' ), array( 'data-spinner-color' => '#333', 'data-action' => $action ) );
    }
}