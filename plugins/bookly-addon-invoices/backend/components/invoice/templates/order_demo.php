<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<table width="100%" style="border:0.1px solid #666;">
    <thead>
    <tr>
        <th style="border-right:0.1px solid #666; text-align: center"><?php esc_html_e( 'Service', 'bookly' ) ?></th>
        <th style="border-right:0.1px solid #666; text-align: center"><?php esc_html_e( 'Date', 'bookly' ) ?></th>
        <th style="border-right:0.1px solid #666; text-align: center"><?php esc_html_e( 'Provider', 'bookly' ) ?></th>
        <th style="text-align: right; padding-right: 2px"><?php esc_html_e( 'Price', 'bookly' ) ?> </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px">-</td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px">-</td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px">-</td>
        <td style="border-top:0.1px solid #666; padding:2px; text-align: right">-</td>
    </tr>
    <tr>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding: 2px">-</td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px">-</td>
        <td style="border-top:0.1px solid #666; border-right:0.1px solid #666; padding:2px">-</td>
        <td style="border-top:0.1px solid #666; padding:2px; text-align: right">-</td>
    </tr>
    <tr>
        <td style="border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-top:0.1px solid #666; padding:2px"><?php esc_html_e( 'Subtotal', 'bookly' ) ?></td>
        <td style="border-right:0.1px solid #666; border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-right:0.1px solid #666; border-top:0.1px solid #666; padding:2px; text-align: right">-</td>
    </tr>
    <tr>
        <td style="border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-top:0.1px solid #666; padding:2px"><?php esc_html_e( 'Discount', 'bookly' ) ?></td>
        <td style="border-right:0.1px solid #666; border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-right:0.1px solid #666; border-top:0.1px solid #666; padding:2px; text-align: right">-</td>
    </tr>
    <tr>
        <td style="border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-top:0.1px solid #666; padding:2px"><?php esc_html_e( 'Total', 'bookly' ) ?></td>
        <td style="border-right:0.1px solid #666; border-top:0.1px solid #666; padding:2px"></td>
        <td style="border-right:0.1px solid #666; border-top:0.1px solid #666; padding:2px; text-align: right">-</td>
    </tr>
    </tbody>
</table>