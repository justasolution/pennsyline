jQuery(function ($) {
    'use strict';

    let $groups_list = $('#bookly-groups-list'),
        $check_all_button = $('#bookly-check-all'),
        $add_button = $('#bookly-js-add-group'),
        $settings_button = $('#bookly-js-general-settings'),
        $delete_button = $('#bookly-delete'),
        $no_groups = $('.bookly-js-no-groups'),
        columns = [],
        order = [],
        edit_btn = $('<button type="button" class="btn btn-default" data-action="edit">').append($('<i class="far fa-fw fa-edit mr-lg-1" />'), '<span class="d-none d-lg-inline">' + BooklyCustomerGroupsL10n.edit + '…</span>').get(0).outerHTML,
        gateways_count = Object.keys(BooklyCustomerGroupsL10n.gateways).length
    ;

    /**
     * Init Columns.
     */
    $.each(BooklyCustomerGroupsL10n.datatables.customer_groups.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'gateways':
                    columns.push({
                        data: column,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            if(data === null) {
                                return BooklyCustomerGroupsL10n.default;
                            }
                            let length = data.length;
                            if (length === 0) {
                                return BooklyCustomerGroupsL10n.nothing_selected;
                            } else if (length === 1) {
                                return $.fn.dataTable.render.text().display(BooklyCustomerGroupsL10n.gateways[data[0]]);
                            } else {
                                return length === gateways_count
                                    ? BooklyCustomerGroupsL10n.all_selected
                                    : length + '/' + gateways_count;
                            }
                        }
                    });
                    break;
                default:
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
            }
        }
    });

    columns.push({
        responsivePriority: 1,
        orderable: false,
        width: 180,
        render: function (data, type, row, meta) {
            return edit_btn;
        }
    });

    columns.push(
        {
            responsivePriority: 1,
            orderable: false,
            render: function (data, type, row, meta) {
                return '<div class="custom-control custom-checkbox">' +
                    '<input value="' + row.id + '" id="bookly-dt-' + row.id + '" type="checkbox" class="custom-control-input">' +
                    '<label for="bookly-dt-' + row.id + '" class="custom-control-label"></label>' +
                    '</div>';
            }
        }
    );

    columns[0].responsivePriority = 0;

    $.each(BooklyCustomerGroupsL10n.datatables.customer_groups.settings.order, function (_, value) {
        const index = columns.findIndex(function (c) {return c.data === value.column;});
        if (index !== -1) {
            order.push([index, value.order]);
        }
    });

    /**
     * Init DataTables.
     */
    var dt = $groups_list.DataTable({
        order: order,
        info: false,
        paging: false,
        searching: false,
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: ajaxurl,
            type: 'POST',
            data: function (d) {
                return $.extend({action: 'bookly_customer_groups_get_groups', csrf_token : BooklyL10nGlobal.csrf_token}, {}, d);
            }
        },
        columns: columns,
        language: {
            zeroRecords: BooklyCustomerGroupsL10n.zeroRecords,
            processing: BooklyCustomerGroupsL10n.processing
        }
    });

    /**
     * Add group.
     */
    $add_button.on('click', function () {
        jQuery(document.body).trigger('bookly_customer_groups.groups_dialog', [{gateways: null}, function () {
            dt.ajax.reload();
        }]);
    });

    $groups_list.on('click', 'button', function () {
        jQuery(document.body).trigger('bookly_customer_groups.groups_dialog', [dt.row($(this).closest('td')).data(), function () {
            dt.ajax.reload();
        }]);
    });

    /**
     * General settings.
     */
    $settings_button.on('click', function () {
        jQuery(document.body).trigger('bookly_customer_groups.groups_dialog', [{
            is_general_settings: 1
        }]);
    });

    /**
     * Select all groups.
     */
    $check_all_button.on('change', function () {
        $groups_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    /**
     * On group select.
     */
    $groups_list.on('change', 'tbody input:checkbox', function () {
        $check_all_button.prop('checked', $groups_list.find('tbody input:not(:checked)').length == 0);
    });

    /**
     * Delete groups.
     */
    $delete_button.on('click', function () {
        if (confirm(BooklyCustomerGroupsL10n.are_you_sure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            var group_ids = [];
            var $checkboxes = $groups_list.find('tbody input[type="checkbox"]:checked');
            $checkboxes.each(function () {
                group_ids.push(this.value);
            });

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_customer_groups_delete_groups',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    group_ids: group_ids
                },
                dataType: 'json',
                success: function (response) {
                    ladda.stop();
                    if (response.success) {
                        $no_groups.text(response.data.no_groups_count);
                        dt.draw(false);
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        }
    });
});