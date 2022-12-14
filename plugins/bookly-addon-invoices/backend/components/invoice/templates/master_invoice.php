<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var $helper BooklyInvoices\Backend\Modules\Settings\Lib\Helper */
?>
<div>
    <table cellpadding="8" cellspacing="0" width="100%">
        <?php
            $bill_to_s = explode(":",$codes['{bill_to}']);
        ?>
        <tbody>
        <tr>
            <td colspan="2"><?php $helper::renderImage( 'bookly_invoices_header_attachment_id', 'w-100' ) ?></td>
        </tr>
        <?php if ( $helper::$mode == 'editable' || get_option( 'bookly_l10n_invoice_company_logo', false ) || get_option( 'bookly_l10n_invoice_label', false ) ) : ?>
            <tr>
                <td style="width: 50%; vertical-align: top;"><?php $helper::renderString( 'bookly_l10n_invoice_company_logo', $codes ) ?></td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                <?php $helper::renderString( 'bookly_l10n_invoice_label', $codes ) ?><br/>
                <?php if ( $bill_to_s[0] ) echo esc_html( 'Invoice Category: ' . $bill_to_s[0] ) ?><br/>
                <?php #if ( $codes['{po_number}'] ) echo esc_html( 'Account Number: ' . $codes['{po_number}'] ) ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ( true ) : ?>
        <tr>
            <td colspan="2" style="text-align: center; vertical-align: top;">
            <?php if ( $bill_to_s[1] ) echo esc_html( 'Bill To:  ' . $bill_to_s[1] ) ?>
            </td>
        </tr>
        <?php endif ?>
        <?php if ( $helper::$mode == 'editable' || get_option( 'bookly_l10n_invoice_company_label', false ) || get_option( 'bookly_l10n_invoice_company_label_right', false ) ) : ?>
        <tr>
            <td style="font-size: large; font-weight: bold; width: 50%; vertical-align: top;"><?php $helper::renderString( 'bookly_l10n_invoice_company_label', $codes ) ?></td>
            <td style="font-size: large; font-weight: bold; width: 50%; text-align: right; vertical-align: top;"><?php $helper::renderString( 'bookly_l10n_invoice_company_label_right', $codes ) ?></td>
        </tr>
        <?php endif ?>

        <tr>
            <?php if ( isset( $payments ) ) : ?>
                <td colspan="2"><?php $self::renderTemplate( 'master_invoice_order', array( 'translate' => true, 'payment' => $payment, 'payments' => $payments,'adjustments' => $adjustments, 'show' => $show, 'time_zone_offset' => $time_zone_offset, 'time_zone' => $time_zone ) ) ?></td>
            <?php endif ?>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center"><?php $helper::renderString( 'bookly_l10n_invoice_thank_you', $codes ) ?></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><?php $helper::renderImage( 'bookly_invoices_footer_attachment_id', 'w-100' ) ?></td>
        </tr>
        </tbody>
    </table>
</div>