<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\TinyMce\Proxy;
?>
<div id="bookly-editor-staff-cabinet-popup" style="display: none">
    <form>
        <table>
            <tr>
                <td>
                    <label for="bookly-select-shortcode"><?php esc_html_e( 'Short Codes', 'bookly' ) ?></label>
                </td>
                <td>
                    <select id="bookly-select-shortcode">
                        <option value="bookly-staff-calendar"><?php esc_html_e( 'Add Staff Calendar', 'bookly' ) ?></option>
                        <option value="bookly-staff-details"><?php esc_html_e( 'Add Staff Details', 'bookly' ) ?></option>
                        <option value="bookly-staff-advanced"><?php esc_html_e( 'Add Staff Advanced', 'bookly' ) ?></option>
                        <option value="bookly-staff-services"><?php esc_html_e( 'Add Staff Services', 'bookly' ) ?></option>
                        <option value="bookly-staff-schedule"><?php esc_html_e( 'Add Staff Schedule', 'bookly' ) ?></option>
                        <?php Proxy\SpecialDays::renderStaffCabinetSettings() ?>
                        <option value="bookly-staff-days-off"><?php esc_html_e( 'Add Staff Days Off', 'bookly' ) ?></option>
                    </select>
                </td>
            </tr>
            <tr class="bookly-js bookly-staff-calendar">
                <td></td>
                <td>
                    <table>
                        <tr>
                            <td>
                                <label><input type="checkbox" data-hide="wp_users"> <?php esc_html_e( 'Hide this field if you want to hide the list of WP users from your staff members.', 'bookly' ) ?></label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="bookly-js bookly-staff-details">
                <td></td>
                <td>
                    <table>
                        <tr>
                            <td>
                                <label><input type="checkbox" data-hide="visibility"> <?php esc_html_e( 'Hide visibility field', 'bookly' ) ?></label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="bookly-js bookly-staff-services">
                <td></td>
                <td>
                    <table>
                        <tr>
                            <td>
                                <label><input type="checkbox" data-read-only="services"> <?php esc_html_e( 'Disable services update', 'bookly' ) ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><input type="checkbox" data-read-only="price"> <?php esc_html_e( 'Disable price update', 'bookly' ) ?></label>
                            </td>
                        </tr>
                        <?php Proxy\GroupBooking::renderStaffCabinetSettings() ?>
                        <?php Proxy\DepositPayments::renderStaffCabinetSettings() ?>
                        <?php Proxy\SpecialHours::renderStaffCabinetSettings() ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button class="button button-primary bookly-js-insert-shortcode" type="button"><?php esc_html_e( 'Insert', 'bookly' ) ?></button>
                </td>
            </tr>
        </table>
    </form>
</div>
<style type="text/css">
    #bookly-editor-staff-cabinet-popup form { margin-top: 15px; }
    #bookly-editor-staff-cabinet-popup form table { width: 100%; }
    #bookly-editor-staff-cabinet-popup form table td select { width: 100%; margin-bottom: 5px; }
</style>
<script type="text/javascript">
    jQuery(function ($) {
        var $form = $('#bookly-editor-staff-cabinet-popup form'),
            $select_shortcode = $('#bookly-select-shortcode', $form),
            $settings = $('.bookly-js', $form),
            $insert = $('button.bookly-js-insert-shortcode', $form)
        ;

        $select_shortcode.on('change', function () {
            $settings.hide();
            $('.' + $(this).val()).show();
        }).trigger('change');

        $insert.on('click', function (e) {
            e.preventDefault();

            var code = $select_shortcode.val();

            if (code) {
                var insert = '[' + code,
                    attributes = {
                        'hide': [],
                        'read-only': []
                    },
                    $container = $('.' + code);

                switch (code) {
                    case 'bookly-staff-calendar':
                    case 'bookly-staff-details':
                    case 'bookly-staff-services':
                        $('[data-read-only]:checked', $container).each(function () {
                            attributes['read-only'].push($(this).data('read-only'));
                        });
                        $('[data-hide]:checked', $container).each(function () {
                            attributes['hide'].push($(this).data('hide'));
                        });
                        break;
                }
                $.each(attributes, function (item, arr) {
                    if (arr.length > 0) {
                        insert += ' ' + item + '="' + arr.join() + '"';
                    }
                });

                insert += ']';

                window.send_to_editor(insert);
            }

            window.parent.tb_remove();
            return false;
        });
    });
</script>