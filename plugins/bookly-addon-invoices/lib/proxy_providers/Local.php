<?php
namespace BooklyInvoices\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyInvoices\Lib\Plugin;
use BooklyInvoices\Backend\Components;

/**
 * Class Local
 * Provide local methods to be used in Bookly and other add-ons.
 *
 * @package BooklyInvoices\Lib\ProxyProviders
 */
abstract class Local extends BooklyLib\Proxy\Invoices
{
    /**
     * @inheritDoc
     */
    public static function getInvoice( BooklyLib\Entities\Payment $payment )
    {
        $pdf  = self::_getInvoicePdf( $payment );
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . wp_unique_filename( sys_get_temp_dir(), 'Invoice_' . $payment->getId() . '.pdf' );

        $pdf->Output( $path, 'F' );

        return $path;
    }

    /**
     * @inheritDoc
     */
    public static function getInvoices( $payments)
    {
        //echo "<pre>".print_r($payment,true)."</pre>";
        //print_r($payment);
        $pdf  = self::_getInvoicePdfMerge( $payments, false);
       // echo "<pre>".print_r($pdf,true)."</pre>";
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . wp_unique_filename( sys_get_temp_dir(), 'Invoice_' . 'test' . '.pdf' );

        $pdf->Output( $path, 'F' );

        return $path;
    }

    /**
     * @inheritDoc
     */
    public static function downloadInvoice( BooklyLib\Entities\Payment $payment )
    {
        $pdf = self::_getInvoicePdf( $payment );
        $pdf->Output( 'Invoice_' . $payment->getId() . '.pdf', 'D' );
        exit();
    }

    /**
     * @inheritDoc
     */
    public static function downloadInvoices($payments )
    {
        $pdf = self::_getInvoicePdfMerge( $payments, false);
        $pdf->Output( 'Invoice_' . $payments[0]->getId() . '.pdf', 'D' );
        exit();
    }

    /**
     * @inheritDoc
     */
    public static function downloadMasterInvoice($payments )
    {
        $pdf = self::_getMasterInvoice( $payments, false );
        $pdf->Output( 'Invoice_' . $payments[0]->getId() . '.pdf', 'D' );
        exit();
    }

    /**
     * @inheritDoc
     */
    public static function editMasterInvoice($payments )
    {
        $pdf = self::_getMasterInvoice( $payments, true );
        //$pdf->Output( 'Invoice_' . $payments[0]->getId() . '.pdf', 'D' );
        exit();
    }

    /**
     * @inheritDoc
     */
    public static function editGenerateInvoice($payments )
    {
        $pdf = self::_getInvoicePdfMerge( $payments, true );
        //$pdf->Output( 'Invoice_' . $payments[0]->getId() . '.pdf', 'D' );
        exit();
    }

    /**
     * @param BooklyLib\Entities\Payment $payment
     * @return \TCPDF
     */
    private static function _getMasterInvoice( $payments , $edit)
    {
        //echo "<pre>".print_r('Download Master Invoice',true)."</pre>";
        //echo "<pre>".print_r($payments,true)."</pre>";
        include Plugin::getDirectory() . '/lib/TCPDF/tcpdf.php';

        $font_name = get_option( 'bookly_invoices_font_name' );
        $font_size = $font_name === 'freesans' ? 12 : 8;
        $pdf = new \TCPDF();
        $pdf->setImageScale( 2.3 );
        $pdf->setPrintHeader( false );
        $pdf->setPrintFooter( false );
        $pdf->AddPage();
        $pdf->SetFont( $font_name, '', $font_size );
        //echo "<pre>".print_r($payment,true)."</pre>";
//         foreach ( $payments as $payment ) {
//             //echo "<pre>".print_r($payment,true)."</pre>";
//             $paymentData = $payment->getPaymentData();
//             //echo "<pre>".print_r($paymentData,true)."</pre>";
//             $data = Components\Invoice\Invoice::renderMerge( $payments );
//             $pdf->writeHTML( $data );
//         }
        $data = Components\Invoice\Invoice::renderMasterInvoice( $payments );
        if($edit){
            echo "<div contenteditable='true'>".print_r($data,true)."</div>";
        }else{
            $pdf->writeHTML( $data );
        }

        //$data = Components\Invoice\Invoice::render( $payment[0]->getPaymentData() );
       // $data1 = Components\Invoice\Invoice::render( $payment[1]->getPaymentData() );
        //echo "<pre>".print_r($data,true)."</pre>";
        //$data = Components\Invoice\Invoice::render( $payment->getPaymentData() );
        //$pdf->writeHTML( $data );

        return $pdf;
    }


    /**
     * @param BooklyLib\Entities\Payment $payment
     * @return \TCPDF
     */
    private static function _getInvoicePdfMerge( $payments , $edit)
    {   //echo "<pre>".print_r($payments,true)."</pre>";
        include Plugin::getDirectory() . '/lib/TCPDF/tcpdf.php';

        $font_name = get_option( 'bookly_invoices_font_name' );
        $font_size = $font_name === 'freesans' ? 12 : 8;
        $pdf = new \TCPDF();
        $pdf->setImageScale( 2.3 );
        $pdf->setPrintHeader( false );
        $pdf->setPrintFooter( false );
        $pdf->AddPage();
        $pdf->SetFont( $font_name, '', $font_size );
        //echo "<pre>".print_r($payment,true)."</pre>";
//         foreach ( $payments as $payment ) {
//             //echo "<pre>".print_r($payment,true)."</pre>";
//             $paymentData = $payment->getPaymentData();
//             //echo "<pre>".print_r($paymentData,true)."</pre>";
//             $data = Components\Invoice\Invoice::renderMerge( $payments );
//             $pdf->writeHTML( $data );
//         }
        $data = Components\Invoice\Invoice::renderMerge( $payments );
        //echo "<pre>".print_r($data,true)."</pre>";
        if($edit){
            echo "<div contenteditable='true'>".print_r($data,true)."</div>";
        }else{
            $pdf->writeHTML( $data );
        }

        //$data = Components\Invoice\Invoice::render( $payment[0]->getPaymentData() );
       // $data1 = Components\Invoice\Invoice::render( $payment[1]->getPaymentData() );
        //echo "<pre>".print_r($data,true)."</pre>";
        //$data = Components\Invoice\Invoice::render( $payment->getPaymentData() );
        //$pdf->writeHTML( $data );

        return $pdf;
    }
    /**
     * @param BooklyLib\Entities\Payment $payment
     * @return \TCPDF
     */
    private static function _getInvoicePdf( BooklyLib\Entities\Payment $payment )
    {
        include_once Plugin::getDirectory() . '/lib/TCPDF/tcpdf.php';

        $font_name = get_option( 'bookly_invoices_font_name' );
        $font_size = $font_name === 'freesans' ? 12 : 8;
        $pdf = new \TCPDF();
        $pdf->setImageScale( 2.3 );
        $pdf->setPrintHeader( false );
        $pdf->setPrintFooter( false );
        $pdf->AddPage();
        $pdf->SetFont( $font_name, '', $font_size );
        $data = Components\Invoice\Invoice::render( $payment->getPaymentData() );
        $pdf->writeHTML( $data );

        return $pdf;
    }

}