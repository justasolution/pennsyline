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
            <th style="border: 2px solid #eee;"><b><?php esc_html_e( 'Service', 'bookly' ) ?></b></th>
            <th style="border: 2px solid #eee;"><b><?php esc_html_e( 'Date', 'bookly' ) ?></b></th>
            <?php if ( $show['deposit'] ) : ?>
                <th style="text-align: right; border: 2px solid #eee;"><b><?php esc_html_e( 'Deposit', 'bookly' ) ?></b></th>
            <?php endif ?>
            <th style="text-align: right; border: 2px solid #eee;"><b><?php esc_html_e( 'Price', 'bookly' ) ?></b></th>
            <?php if ( $show['taxes'] ) : ?>
                <th style="text-align: right; border: 2px solid #eee;"><b><?php esc_html_e( 'Tax', 'bookly' ) ?></b></th>
            <?php endif ?>
        <?php elseif ( $payment['target'] === BooklyLib\Entities\Payment::TARGET_PACKAGES ) : ?>
            <th style="border: 2px solid #eee;"><b><?php esc_html_e( 'Package', 'bookly' ) ?></b></th>
            <th colspan="2" style="border: 2px solid #eee;"><b><?php esc_html_e( 'Provider', 'bookly' ) ?></b></th>
            <?php if ( $show['deposit'] ) : ?>
                <th style="text-align: right; border: 2px solid #eee;"><b><?php esc_html_e( 'Deposit', 'bookly' ) ?></b></th>
            <?php endif ?>
            <th style="text-align: right; border: 2px solid #eee;"><b><?php esc_html_e( 'Price', 'bookly' ) ?></b></th>
            <?php if ( $show['taxes'] ) : ?>
                <th style="text-align: right; border: 2px solid #eee;"><b><?php esc_html_e( 'Tax', 'bookly' ) ?></b></th>
            <?php endif ?>
        <?php endif ?>
    </tr>
    </thead>
    <tbody>

    <?php foreach ( $payments as $paymentsInfo ) : ?>
         <?php
            $paymentDataTemp = $paymentsInfo->getPaymentData();
            $paymentData = $paymentDataTemp['payment'];
            //print_r($paymentData);
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
            $paymentData['p_total'] = $paymentData['total']; // TODO add more logic if needed
            //echo "<pre>";
            //print_r($paymentData['subtotal']);
            //print_r($paymentData['p_subtotal']);
            //echo "</pre>";
        ?>
        <?php foreach ( $paymentData['items'] as $item ) : ?>
            <?php
//                 echo "<pre>";
//                  print_r($item);
//                 echo "</pre>";
            ?>
            <?php if ( $paymentData['target'] === BooklyLib\Entities\Payment::TARGET_APPOINTMENTS ) : ?>
                <tr>
                    <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;text-align:left;">
                        <?php if ( false && $item['service_category'] ) echo esc_html( $item['service_category'] . ' - ' ) ?>
                        <?php echo esc_html( $item['service_name'] ) ?>
                        <?php if ( isset( $item['units'], $item['duration'] ) && $item['units'] > 1 ) echo '&nbsp;(' . DateTime::secondsToInterval( $item['units'] * $item['duration'] ) . ')' ?>
                    </td>
                    <?php $appointment_date = $item['appointment_date'] ?>
                    <?php if ( $appointment_date !== null ) : ?>
                        <?php if ( $time_zone !== null ) : ?>
                            <?php $appointment_date = date_create( $appointment_date . ' ' . BooklyLib\Config::getWPTimeZone() ) ?>
                            <?php $appointment_date = date_format( date_timestamp_set( date_create( $time_zone ), $appointment_date->getTimestamp() ), 'Y-m-d H:i:s' ) ?>
                        <?php elseif ( $time_zone_offset !== null ) : ?>
                            <?php $appointment_date = DateTime::applyTimeZoneOffset( $appointment_date, $time_zone_offset ) ?>
                        <?php endif ?>
                        <?php $appointment_date = DateTime::formatDateTime( $appointment_date ) ?>
                    <?php else : ?>
                        <?php $appointment_date = __( 'N/A', 'bookly' ) ?>
                    <?php endif ?>
                    <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;"><?php echo $appointment_date ?></td>
                    <?php if ( $show['deposit'] ) : ?>
                        <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee; text-align: right;"><?php echo $item['deposit_format'] ?></td>
                    <?php endif ?>
                    <td style="text-align: right; border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;">
                        <?php if ( $paymentData->from_backend ) : ?>
                            <?php echo Price::format( $item['service_price'] ) ?>
                        <?php else : ?>
                            <?php if ( $item['number_of_persons'] > 1 )
                                echo $item['number_of_persons'] . '&nbsp;&times;&nbsp;' ?><?php echo Price::format( $item['service_price'] ) ?>
                        <?php endif ?>
                    </td>
                    <?php if ( $show['taxes'] ) : ?>
                        <?php if ( $item['service_tax'] !== null ) : ?>
                            <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee; text-align: right;"><?php printf( $paymentData->tax_in_price == 'included' ? '(%s)' : '%s', Price::format( $item['service_tax'] ) ) ?></td>
                        <?php else : ?>
                            <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee; text-align: right;">-</td>
                        <?php endif ?>
                    <?php endif ?>
                </tr>
                <?php if ( ! empty ( $item['extras'] ) ) : ?>
                    <?php $extras_price = 0 ?>
                    <?php foreach ( $item['extras'] as $extra ) : ?>
                        <tr>
                            <td style="border-left: 2px solid #eee; border-right: 2px solid #eee;"><?php if ( $extra['quantity'] > 1 ) echo $extra['quantity'] . '&nbsp;&times;&nbsp;' ?><?php echo esc_html( $extra['title'] ) ?></td>
                            <td style="border-right: 2px solid #eee;"></td>
                            <td style="border-right: 2px solid #eee;"></td>
                            <?php if ( $show['deposit'] ) : ?>
                                <td style="border-right: 2px solid #eee;"></td>
                            <?php endif ?>
                            <td style="border-right: 2px solid #eee; text-align: right;">
                                <?php printf( '%s%s%s',
                                    ( $item['number_of_persons'] > 1 && $paymentData->extras_multiply_nop ) ? $item['number_of_persons'] . '&nbsp;&times;&nbsp;' : '',
                                    ( $extra['quantity'] > 1 ) ? $extra['quantity'] . '&nbsp;&times;&nbsp;' : '',
                                    Price::format( $extra['price'] )
                                ) ?>
                            </td>
                            <?php if ( $show['taxes'] ) : ?>
                                <td style="text-align: right; border-right: 2px solid #eee;">
                                    <?php if ( isset( $extra['tax'] ) ) : ?>
                                        <?php echo Price::format( $extra['tax'] ) ?>
                                    <?php endif ?>
                                </td>
                            <?php endif ?>
                        </tr>
                        <?php $extras_price += $extra['price'] * $extra['quantity'] ?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php if ( isset ( $item['discounts'] ) ) : ?>
                    <?php foreach ( $item['discounts'] as $discount ) : ?>
                        <tr>
                            <td style="border-left: 2px solid #eee; border-right: 2px solid #eee;"><?php esc_html_e( 'Discount', 'bookly' ) ?> <small>(<?php echo esc_html( $discount['title'] ) ?>)</small></td>
                            <td style="border-right: 2px solid #eee;"></td>
                            <td style="border-right: 2px solid #eee;"></td>
                            <?php if ( $show['deposit'] ) : ?>
                                <td style="border-right: 2px solid #eee;"></td>
                            <?php endif ?>
                            <td style="border-right: 2px solid #eee; text-align: right;">
                                <?php if ( isset ( $discount['discount'] ) && $discount['discount'] > 0 ) : ?>
                                    <?php echo $discount['discount'] ?>%
                                <?php endif ?>
                                <?php if ( isset ( $discount['deduction'] ) && $discount['deduction'] > 0 ) : ?>
                                    <?php echo Price::format( $discount['deduction'] ) ?>
                                <?php endif ?>
                            </td>
                            <?php if ( $show['taxes'] ) : ?>
                                <td style="border-right: 2px solid #eee;"></td>
                            <?php endif ?>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            <?php elseif ( $paymentData->target === BooklyLib\Entities\Payment::TARGET_PACKAGES ) : ?>
                <tr>
                    <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;"><?php echo esc_html( $item['service_name'] ) ?></td>
                    <td colspan="2" style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;"><?php echo $item['staff_name'] ?></td>
                    <?php if ( $show['deposit'] ) : ?>
                        <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee; text-align: right;"><?php echo $item['deposit_format'] ?></td>
                    <?php endif ?>
                    <td style="text-align: right; border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee;">
                        <?php if ( $paymentData->from_backend ) : ?>
                            <?php echo Price::format( $item['service_price'] ) ?>
                        <?php endif ?>
                    </td>
                    <?php if ( $show['taxes'] ) : ?>
                        <?php if ( $item['service_tax'] !== null ) : ?>
                            <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee; text-align: right;"><?php printf( $paymentData->tax_in_price == 'included' ? '(%s)' : '%s', Price::format( $item['service_tax'] ) ) ?></td>
                        <?php else : ?>
                            <td style="border-top: 2px solid #eee; border-left: 2px solid #eee;border-right: 2px solid #eee; text-align: right;">-</td>
                        <?php endif ?>
                    <?php endif ?>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
    <?php endforeach ?>
    <tr>
        <td style="border: 2px solid #eee;text-align:right;" colspan="2"><?php esc_html_e( 'Subtotal', 'bookly' ) ?></td>
        <?php if ( $show['deposit'] ) : ?>
            <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $payment['subtotal']['deposit'] ) ?></td>
        <?php endif ?>
        <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $paymentData['p_subtotal'] ) ?></td>
        <?php if ( $show['taxes'] ) : ?>
            <td style="border: 2px solid #eee; text-align: right;"></td>
        <?php endif ?>
    </tr>
    <?php if ( $show['coupons'] || $payment['coupon'] ) : ?>
        <tr>
            <td></td>
            <td style="border: 2px solid #eee;text-align:right;" colspan="<?php echo 2 + $show['deposit'] ?>"><?php esc_html_e( 'Coupon discount', 'bookly' ) ?><?php if ( $payment['coupon'] ) : ?><br>(<?php echo $payment['coupon']['code'] ?>)<?php endif ?>
            </td>
            <td style="border: 2px solid #eee; text-align: right;">
                <?php if ( $payment['coupon'] ) : ?>
                    <?php if ( $payment['coupon']['discount'] ) : ?><?php echo $payment['coupon']['discount'] ?>%<br><?php endif ?>
                    <?php if ( $payment['coupon']['deduction'] ) : ?><?php echo Price::format( $payment['coupon']['deduction'] ) ?><?php endif ?>
                <?php else : ?>
                    <?php echo Price::format( 0 ) ?>
                <?php endif ?>
            </td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border: 2px solid #eee;"></td>
            <?php endif ?>
        </tr>
    <?php endif ?>
    <?php if ( $show['customer_groups'] || $payment['group_discount'] ) : ?>
        <tr>
            <td style="border: 2px solid #eee;text-align:right;" colspan="<?php echo 2 + $show['deposit'] ?>"><?php esc_html_e( 'Group discount', 'bookly' ) ?></td>
            <td style="border: 2px solid #eee; text-align: right;"><?php echo $payment['group_discount'] ?: Price::format( 0 ) ?></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border: 2px solid #eee;"></td>
            <?php endif ?>
        </tr>
    <?php endif ?>
    <?php if ( isset ( $payment['discounts'] ) ) : ?>
        <?php foreach ( $payment['discounts'] as $discount ) : ?>
            <tr>
                <td style="border: 2px solid #eee;text-align:right;" colspan="<?php echo 2 + $show['deposit'] ?>"><?php esc_html_e( 'Discount', 'bookly' ) ?> <small>(<?php echo esc_html( $discount['title'] ) ?>)</small></td>
                <td style="border: 2px solid #eee; text-align: right;">
                    <?php if ( isset ( $discount['discount'] ) && $discount['discount'] > 0 ) : ?>
                        <?php echo $discount['discount'] ?>%
                    <?php endif ?>
                    <?php if ( isset ( $discount['deduction'] ) && $discount['deduction'] > 0 ) : ?>
                        <?php echo Price::format( $discount['deduction'] ) ?>
                    <?php endif ?>
                </td>
                <?php if ( $show['taxes'] ) : ?>
                    <td style="border: 2px solid #eee;"></td>
                <?php endif ?>
            </tr>
        <?php endforeach ?>
    <?php endif ?>
    <?php foreach ( $adjustments as $adjustment ) : ?>
        <tr>
            <td style="border: 2px solid #eee;text-align:right;" colspan="<?php echo 2 + $show['deposit'] ?>"><?php echo esc_html( $adjustment['reason'] ) ?></td>
            <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $adjustment['amount'] ) ?></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $adjustment['tax'] ) ?></td>
            <?php endif ?>
        </tr>
    <?php endforeach ?>
    <?php if ( $show['price_correction'] && (float) $payment['price_correction'] ) : ?>
        <tr>
            <td style="border: 2px solid #eee;text-align:right;" colspan="<?php echo 2 + $show['deposit'] ?>"><?php echo \Bookly\Lib\Entities\Payment::typeToString( $payment['type'] ) ?></td>
            <td style="border: 2px solid #eee; text-align: right;"><?php echo Price::format( $payment['price_correction'] ) ?></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border: 2px solid #eee; text-align: right;">-</td>
            <?php endif ?>
        </tr>
    <?php endif ?>
    <tr>
        <td style="border: 2px solid #eee;text-align:right;" colspan="<?php echo 2 + $show['deposit'] ?>"><b><?php esc_html_e( 'Total', 'bookly' ) ?></b></td>
        <td style="border: 2px solid #eee; text-align: right;"><b><?php echo Price::format( $paymentData['p_total'] ) ?></b></td>
        <?php if ( $show['taxes'] ) : ?>
            <td style="border: 2px solid #eee; text-align: right;">(<?php echo Price::format( $payment['tax_total'] ) ?>)</td>
        <?php endif ?>
    </tr>
    <?php if ( false && $paymentData['p_total'] != $payment['paid'] ) : ?>
        <tr>
            <td style="border: 2px solid #eee;text-align:right;" colspan="<?php echo 2 + $show['deposit'] ?>"><b><?php esc_html_e( 'Paid', 'bookly' ) ?></b></td>
            <td style="border: 2px solid #eee; text-align: right;"><b><?php echo Price::format( $payment['paid'] ) ?></b></td>
            <?php if ( $show['taxes'] ) : ?>
                <td style="border: 2px solid #eee; text-align: right;">(<?php echo Price::format( $payment['tax_paid'] ) ?>)</td>
            <?php endif ?>
        </tr>
        <?php if ( ( $paymentData['p_total'] - $payment['paid'] ) > 0 || ( $show['taxes'] && ( $payment['tax_total'] - $payment['tax_paid'] ) > 0 ) ) : ?>
            <tr>
                <td style="border: 2px solid #eee;text-align:right;" colspan="<?php echo 2 + $show['deposit'] ?>"><b><?php esc_html_e( 'Due', 'bookly' ) ?></b></td>
                <td style="border: 2px solid #eee; text-align: right;"><b><?php echo Price::format( $paymentData['p_total'] - $payment['paid'] ) ?></b></td>
                <?php if ( $show['taxes'] ) : ?>
                    <td style="border: 2px solid #eee; text-align: right;">(<?php echo Price::format( $payment['tax_total'] - $payment['tax_paid'] ) ?>)</td>
                <?php endif ?>
            </tr>
        <?php endif ?>
    <?php endif ?>
    </tbody>
</table>