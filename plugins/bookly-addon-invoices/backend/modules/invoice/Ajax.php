<?php
namespace BooklyInvoices\Backend\Modules\Invoice;

use Bookly\Lib as BooklyLib;
use BooklyInvoices\Lib;

/**
 * Class Ajax
 * @package BooklyInvoices\Backend\Modules\Invoice
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'supervisor' );
    }

    public static function downloadInvoices() // TODO
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $payment_ids = explode( ',', self::parameter( 'invoices' ) );
        /** @var BooklyLib\Entities\Payment[] $payments */
        $payments = BooklyLib\Entities\Payment::query()
            ->whereIn( 'id', $payment_ids )
            ->find();
        //echo "<pre>".print_r($payments,true)."</pre>";
        if ( count( $payments ) == 1 || class_exists( 'ZipArchive', false ) === false ) {
            BooklyLib\Proxy\Invoices::downloadInvoices( $payments );
        } elseif( $payments || true){

            BooklyLib\Proxy\Invoices::downloadInvoices( $payments); // M Mady
          }
          elseif ( $payments && false ) { // disable multople file download
            $files = array();
            $zip_archive = wp_tempnam();
            $zip   = new \ZipArchive();
            if ( $zip->open( $zip_archive ) === true ) {
                foreach ( $payments as $payment ) {
                    if ( $filename = BooklyLib\Proxy\Invoices::getInvoice( $payment ) ) {
                        $files[] = $filename;
                        $zip->addFile( $filename, 'Invoice_' . $payment->getId() . '.pdf' );
                    }
                }
                $zip->close();
                $files[] = $zip_archive;
                header( 'Pragma: public' );
                header( 'Expires: 0' );
                header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
                header( 'Cache-Control: public' );
                header( 'Content-Description: File Transfer' );
                header( 'Content-type: application/octet-stream' );
                header( 'Content-Disposition: attachment; filename="Invoices.zip"' );
                header( 'Content-Transfer-Encoding: binary' );
                header( 'Content-Length: ' . $fs->size( $zip_archive ) );
                print $fs->get_contents( $zip_archive );

                foreach ( $files as $path ) {
                    $fs->delete( $path, false, 'f' );
                }
            }
        }
        exit();
    }

    public static function downloadMasterInvoice() // TODO
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $payment_ids = explode( ',', self::parameter( 'invoices' ) );
        /** @var BooklyLib\Entities\Payment[] $payments */
        $payments = BooklyLib\Entities\Payment::query()
            ->whereIn( 'id', $payment_ids )
            ->find();
        //echo "<pre>".print_r('Download Master Invoice',true)."</pre>";
        //echo "<pre>".print_r($payments,true)."</pre>";
        BooklyLib\Proxy\Invoices::downloadMasterInvoice( $payments); // M Mady
        exit();
    }

    public static function editMasterInvoice() // TODO
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $payment_ids = explode( ',', self::parameter( 'invoices' ) );
        /** @var BooklyLib\Entities\Payment[] $payments */
        $payments = BooklyLib\Entities\Payment::query()
            ->whereIn( 'id', $payment_ids )
            ->find();
        //echo "<pre>".print_r('Download Master Invoice',true)."</pre>";
        //echo "<pre>".print_r($payments,true)."</pre>";
        BooklyLib\Proxy\Invoices::editMasterInvoice( $payments); // M Mady
        exit();
    }

    public static function paymentComplete() // Mady M TODO
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $payment_ids = explode( ',', self::parameter( 'invoices' ) );
        /** @var BooklyLib\Entities\Payment[] $payments */
        $payments = BooklyLib\Entities\Payment::query()
            ->whereIn( 'id', $payment_ids )
            ->find();
        //echo "<pre>".print_r('Payments Info',true)."</pre>";
        //echo "<pre>".print_r($payments,true)."</pre>";
        foreach($payments as $payment){
            //echo "<pre>".print_r('Master invoices',true)."</pre>";
            //echo "<pre>".print_r($payment->getPaymentData()['payment']['masterInvoiceInfo']['items'],true)."</pre>";
            $mPayment = $payment;
            //echo "<pre>".print_r($mPayment,true)."</pre>";
            $mDetails = json_decode( $mPayment->getDetails(), true );
            $mDetails['tax_paid'] = $mPayment->getTax();
            //echo "<pre>".print_r($details,true)."</pre>";
            $mPayment
                ->setPaid( $mPayment->getTotal() )
                ->setStatus( BooklyLib\Entities\Payment::STATUS_COMPLETED )
                ->setDetails( json_encode( $mDetails ) )
                ->save();
            foreach($payment->getPaymentData()['payment']['masterInvoiceInfo']['items'] as $gInvoice){
                //echo "<pre>".print_r('Generated invoices',true)."</pre>";
                //echo "<pre>".print_r($gInvoice,true)."</pre>";
                $gPayment = BooklyLib\Entities\Payment::find( $gInvoice['id'] );
                //echo "<pre>".print_r($gPayment,true)."</pre>";
                $gDetails = json_decode( $gPayment->getDetails(), true );
                $gDetails['tax_paid'] = $gPayment->getTax();
                //echo "<pre>".print_r($details,true)."</pre>";
                $gPayment
                    ->setPaid( $gPayment->getTotal() )
                    ->setStatus( BooklyLib\Entities\Payment::STATUS_COMPLETED )
                    ->setDetails( json_encode( $gDetails ) )
                    ->save();
                foreach($gInvoice['items'] as $eachAppt){
                    //echo "<pre>".print_r($eachAppt,true)."</pre>";
                    $ca  = BooklyLib\Entities\CustomerAppointment::find( $eachAppt['ca_id'] );
                    //echo "<pre>".print_r($ca ->getPaymentId(),true)."</pre>";
                    $paymentIds[] = $ca ->getPaymentId();
                    $payment = BooklyLib\Entities\Payment::find( $ca ->getPaymentId() );
                    //echo "<pre>".print_r($payment,true)."</pre>";
                    $details = json_decode( $payment->getDetails(), true );
                    $details['tax_paid'] = $payment->getTax();
                    //echo "<pre>".print_r($details,true)."</pre>";
                    $payment
                        ->setPaid( $payment->getTotal() )
                        ->setStatus( BooklyLib\Entities\Payment::STATUS_COMPLETED )
                        ->setDetails( json_encode( $details ) )
                        ->save();
                }
            }
        }
        echo "<h2>Successfully Marked payments to completed</h2>"; // TODO add all the payment id which were marked completed.
        echo "<h4>These Invoices are market as completed".print_r(json_encode($paymentIds),true)."</h4>";
        //BooklyLib\Proxy\Invoices::editMasterInvoice( $payments); // M Mady
        exit();
    }

    public static function editGenerateInvoice() // TODO
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $payment_ids = explode( ',', self::parameter( 'invoices' ) );
        /** @var BooklyLib\Entities\Payment[] $payments */
        $payments = BooklyLib\Entities\Payment::query()
            ->whereIn( 'id', $payment_ids )
            ->find();
        //echo "<pre>".print_r('Download Master Invoice',true)."</pre>";
        //echo "<pre>".print_r($payments,true)."</pre>";
        BooklyLib\Proxy\Invoices::editGenerateInvoice( $payments); // M Mady
        exit();
    }

    public static function generateInvoices() // TODO
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $payment_ids = explode( ',', self::parameter( 'invoices' ) );
        /** @var BooklyLib\Entities\Payment[] $payments */
        $payments = BooklyLib\Entities\Payment::query()
            ->whereIn( 'id', $payment_ids )
            ->find();

//         echo "<pre>".print_r($payments,true)."</pre>";

        BooklyLib\Proxy\Pro::createBackendInvoicePayment($payments ); // Create new backend Invoice

//         if ( count( $payments ) == 1 || class_exists( 'ZipArchive', false ) === false ) {
//             BooklyLib\Proxy\Invoices::downloadInvoice( $payments[0] );
//         } elseif( $payments || true){
//             BooklyLib\Proxy\Invoices::downloadInvoices( $payments); // M Mady
//           }
//           elseif ( $payments && false ) { // disable multople file download
//             $files = array();
//             $zip_archive = wp_tempnam();
//             $zip   = new \ZipArchive();
//             if ( $zip->open( $zip_archive ) === true ) {
//                 foreach ( $payments as $payment ) {
//                     if ( $filename = BooklyLib\Proxy\Invoices::getInvoice( $payment ) ) {
//                         $files[] = $filename;
//                         $zip->addFile( $filename, 'Invoice_' . $payment->getId() . '.pdf' );
//                     }
//                 }
//                 $zip->close();
//                 $files[] = $zip_archive;
//                 header( 'Pragma: public' );
//                 header( 'Expires: 0' );
//                 header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
//                 header( 'Cache-Control: public' );
//                 header( 'Content-Description: File Transfer' );
//                 header( 'Content-type: application/octet-stream' );
//                 header( 'Content-Disposition: attachment; filename="Invoices.zip"' );
//                 header( 'Content-Transfer-Encoding: binary' );
//                 header( 'Content-Length: ' . $fs->size( $zip_archive ) );
//                 print $fs->get_contents( $zip_archive );
//
//                 foreach ( $files as $path ) {
//                     $fs->delete( $path, false, 'f' );
//                 }
//             }
//         }
        exit();
    }

    public static function masterInvoices() // Master Invoice Generation
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $payment_ids = explode( ',', self::parameter( 'invoices' ) );
        /** @var BooklyLib\Entities\Payment[] $payments */
        $payments = BooklyLib\Entities\Payment::query()
            ->whereIn( 'id', $payment_ids )
            ->find();

//         echo "<pre>".print_r($payments,true)."</pre>";

        BooklyLib\Proxy\Pro::createBackendMasterInvoicePayment($payments );
//         if ( count( $payments ) == 1 || class_exists( 'ZipArchive', false ) === false ) {
//             BooklyLib\Proxy\Invoices::downloadInvoice( $payments[0] );
//         } elseif( $payments || true){
//             BooklyLib\Proxy\Invoices::downloadInvoices( $payments); // M Mady
//           }
//           elseif ( $payments && false ) { // disable multople file download
//             $files = array();
//             $zip_archive = wp_tempnam();
//             $zip   = new \ZipArchive();
//             if ( $zip->open( $zip_archive ) === true ) {
//                 foreach ( $payments as $payment ) {
//                     if ( $filename = BooklyLib\Proxy\Invoices::getInvoice( $payment ) ) {
//                         $files[] = $filename;
//                         $zip->addFile( $filename, 'Invoice_' . $payment->getId() . '.pdf' );
//                     }
//                 }
//                 $zip->close();
//                 $files[] = $zip_archive;
//                 header( 'Pragma: public' );
//                 header( 'Expires: 0' );
//                 header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
//                 header( 'Cache-Control: public' );
//                 header( 'Content-Description: File Transfer' );
//                 header( 'Content-type: application/octet-stream' );
//                 header( 'Content-Disposition: attachment; filename="Invoices.zip"' );
//                 header( 'Content-Transfer-Encoding: binary' );
//                 header( 'Content-Length: ' . $fs->size( $zip_archive ) );
//                 print $fs->get_contents( $zip_archive );
//
//                 foreach ( $files as $path ) {
//                     $fs->delete( $path, false, 'f' );
//                 }
//             }
//         }
        exit();
    }

}