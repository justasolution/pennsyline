<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Price;
use Bookly\Lib as BooklyLib;

/** @var array $payment */
/** @var array $show */
?>
<table width="100%" cellpadding="10">
    <thead>
    <tr>
        <?php if ( $payment['target'] === BooklyLib\Entities\Payment::TARGET_APPOINTMENTS ) : ?>
            <th style="border: 2px solid #eee;text-align:left;"><b><?php esc_html_e( 'Name', 'bookly' ) ?></b></th>
            <th style="border: 2px solid #eee;text-align:left;"><b><?php esc_html_e( 'Client File Number', 'bookly' ) ?></b></th>
            <th style="text-align: right; border: 2px solid #eee;text-align:right;"><b><?php esc_html_e( 'Invoice Number', 'bookly' ) ?></b></th>
            <th style="text-align: right; border: 2px solid #eee;text-align:right;"><b><?php esc_html_e( 'Amount', 'bookly' ) ?></b></th>
        <?php endif ?>
    </tr>
    </thead>
    <tbody>

    <?php foreach ( $payments as $paymentsInfo ) : ?>
         <?php
            //echo "<pre>".print_r(($payments),true)."</pre>";
            $paymentDataTemp = $paymentsInfo->getPaymentData();
            $masterInvoiceInfo = $paymentDataTemp['payment']['masterInvoiceInfo'];
            //echo "<pre>".print_r('Master Invoice Order',true)."</pre>";
            //echo "<pre>".print_r(($masterInvoiceInfo),true)."</pre>";
            foreach($paymentData['subtotal'] as $key => $value){
                //echo "<pre>";
                if($key === 'price'){
                    //print_r($paymentData['subtotal'][$key]);
                    $p_subtotal += intval($paymentData['subtotal'][$key]);
                }
                //print_r($paymentData['p_subtotal']);
                //echo "</pre>";
            }
            $paymentData['p_subtotal'] = $p_subtotal;
            $paymentData['p_total'] = $p_subtotal; // TODO add more logic if needed
            //echo "<pre>";
             //print_r($paymentData['subtotal']);
            //print_r($paymentData['p_subtotal']);
            //echo "</pre>";
        ?>
        <?php foreach ( $masterInvoiceInfo['items'] as $item ) : ?>
            <?php
                foreach(json_decode($item['customer_fields'],true) as $cf){
                   if($cf['id'] == '80472'){
                        $cf_number = $cf['value'];
                        //echo "<pre>".print_r($cf,true)."</pre>";
                   }
                }
            ?>
            <tr>
                <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;text-align:left;">
                    <?php if ( $item['customer']) echo $item['customer'] ?>
                </td>
                <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;text-align:left;">
                    <?php if ( $item['customer_fields'] ) echo $cf_number ?>
                </td>
                <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;text-align:right;">
                    <?php if ( $item['id'] ) echo esc_html( $item['id'] ) ?>
                </td>
                <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;text-align:right;">
                    <?php if ( $item['total'] ) echo esc_html( $item['total'] ) ?>
                </td>
            </tr>
        <?php endforeach ?>
    <?php endforeach ?>
    <tr>
        <td style="border: 2px solid #eee;text-align:right;" colspan="3"><?php esc_html_e( 'Subtotal', 'bookly' ) ?></td>
        <?php if ( $show['deposit'] ) : ?>
            <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $masterInvoiceInfo['subtotal']['deposit'] ) ?></td>
        <?php endif ?>
        <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $masterInvoiceInfo['grandTotal'] ) ?></td>
        <?php if ( $show['taxes'] ) : ?>
            <td style="border: 2px solid #eee; text-align: right;"></td>
        <?php endif ?>
    </tr>
    <?php foreach ( $adjustments as $adjustment ) : ?>
        <tr>
            <td style="border: 2px solid #eee;text-align:right;" colspan="3"><?php echo esc_html( $adjustment['reason'] ) ?></td>
            <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $adjustment['amount'] ) ?></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $adjustment['tax'] ) ?></td>
            <?php endif ?>
        </tr>
    <?php endforeach ?>
    <?php if ( $show['price_correction'] && (float) $masterInvoiceInfo['price_correction'] ) : ?>
        <tr>
            <td style="border: 2px solid #eee;text-align:right;" colspan="3"><?php echo \Bookly\Lib\Entities\Payment::typeToString( $masterInvoiceInfo['type'] ) ?></td>
            <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $masterInvoiceInfo['price_correction'] ) ?></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border: 2px solid #eee; text-align: right;">-</td>
            <?php endif ?>
        </tr>
    <?php endif ?>
    <tr>
        <td style="border: 2px solid #eee;text-align:right;" colspan="3>"><b><?php esc_html_e( 'Total', 'bookly' ) ?></b></td>
        <td style="border: 2px solid #eee; text-align: right;"><b><?php echo Price::format( $masterInvoiceInfo['grandTotal'] ) ?></b></td>
        <?php if ( $show['taxes'] ) : ?>
            <td style="border: 2px solid #eee; text-align: right;">(<?php echo Price::format( $masterInvoiceInfo['tax_total'] ) ?>)</td>
        <?php endif ?>
    </tr>
    <?php if ( $masterInvoiceInfoData['grandTotal'] != $masterInvoiceInfo['paid'] ) : ?>
        <tr>
            <td style="border: 2px solid #eee;text-align:right;" colspan="3>"><b><?php esc_html_e( 'Paid', 'bookly' ) ?></b></td>
            <td style="border: 2px solid #eee; text-align: right;"><b><?php echo Price::format( $masterInvoiceInfo['paid'] ) ?></b></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border: 2px solid #eee; text-align: right;">(<?php echo Price::format( $masterInvoiceInfo['tax_paid'] ) ?>)</td>
            <?php endif ?>
        </tr>
        <?php if ( ( $masterInvoiceInfoData['grandTotal'] - $masterInvoiceInfo['paid'] ) > 0 || ( $show['taxes'] && ( $masterInvoiceInfo['tax_total'] - $masterInvoiceInfo['tax_paid'] ) > 0 ) ) : ?>
            <tr>
                <td style="border: 2px solid #eee;text-align:right;" colspan="3>"><b><?php esc_html_e( 'Due', 'bookly' ) ?></b></td>
                <td style="border: 2px solid #eee; text-align: right;"><b><?php echo Price::format( $masterInvoiceInfo['grandTotal'] - $masterInvoiceInfo['paid'] ) ?></b></td>
                <?php if ( $show['taxes'] ) : ?>
                    <td style="border: 2px solid #eee; text-align: right;">(<?php echo Price::format( $masterInvoiceInfo['tax_total'] - $masterInvoiceInfo['tax_paid'] ) ?>)</td>
                <?php endif ?>
            </tr>
        <?php endif ?>
    <?php endif ?>
    </tbody>
</table>