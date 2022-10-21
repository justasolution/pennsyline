jQuery(function($) {

$('#tabs li a:not(:first)').addClass('inactive');
$('.container-fluid').hide();
$('.container-fluid:first').show();

$('#tabs li a').click(function(){
    $('#bookly-service-category').val("");
    $('#bookly-generate-service-category').val("");
    $('#bookly-master-service-category').val("");
    $('#bookly-filter-customer').val(null);
    $('#bookly-generate-filter-customer').val(null);
    $('#bookly-master-filter-customer').val(null);
    $('#bookly-payments-list').DataTable().ajax.reload();
    $('#bookly-generate-payments-list').DataTable().ajax.reload();
    $('#bookly-master-payments-list').DataTable().ajax.reload();
    var t = $(this).attr('id');
  if($(this).hasClass('inactive')){ //this is the start of our condition
    $('#tabs li a').addClass('inactive');
    $(this).removeClass('inactive');

    $('.container-fluid').hide();
    $('#'+ t + 'C').fadeIn('slow');
 }
});

});

//Payments
jQuery(function($) {

    var
        $payments_list    = $('#bookly-payments-list'),
        $check_all_button = $('#bookly-check-all'),
        $id_filter        = $('#bookly-filter-id'),
        $creationDateFilter = $('#bookly-filter-date'),
        $type_filter      = $('#bookly-filter-type'),
        $customer_filter  = $('#bookly-filter-customer'),
        $staff_filter     = $('#bookly-filter-staff'),
        $category_filter  = $('#bookly-service-category'),
        $service_filter   = $('#bookly-filter-service'),
        $status_filter    = $('#bookly-filter-status'),
        $payment_total    = $('#bookly-payment-total'),
        $delete_button    = $('#bookly-delete'),
        $download_invoice = $('#bookly-download-invoices'),
        $generate_invoice = $('#bookly-generate-invoices'),
        urlParts          = document.URL.split('#'),
        columns           = [],
        order             = [],
        pickers = {
            dateFormat:     'YYYY-MM-DD',
            creationDate: {
                startDate: moment().subtract(30, 'days'),
                endDate  : moment(),
            },
        };

    $('.bookly-js-select').val(null);

    if (urlParts.length > 1) {
        urlParts[1].split('&').forEach(function (part) {
            var params = part.split('=');
            if (params[0] == 'created-date') {
                pickers.creationDate.startDate = moment(params['1'].substring(0, 10));
                pickers.creationDate.endDate = moment(params['1'].substring(11));
                $creationDateFilter
                    .data('date', pickers.creationDate.startDate.format(pickers.dateFormat) + ' - ' + pickers.creationDate.endDate.format(pickers.dateFormat))
                    .find('span')
                    .html(pickers.creationDate.startDate.format(BooklyL10n.dateRange.format) + ' - ' + pickers.creationDate.endDate.format(BooklyL10n.dateRange.format));
            } else {
                $('#bookly-filter-' + params[0]).val(params[1]);
            }
        });
    } else {
        $.each(BooklyL10n.datatables.payments.settings.filter, function (field, value) {
            if (value != '') {
                let $elem = $('#bookly-filter-' + field);
                if ($elem.is(':checkbox')) {
                    $elem.prop('checked', value == '1');
                } else {
                    $elem.val(value);
                }
            }
            // check if select has correct values
            if ($('#bookly-filter-' + field).prop('type') == 'select-one') {
                if ($('#bookly-filter-' + field + ' option[value="' + value + '"]').length == 0) {
                    $('#bookly-filter-' + field).val(null);
                }
            }
        });
    }

    $('.bookly-js-select').select2({
        width: '100%',
        allowClear: true,
        placeholder: '',
        theme: 'bootstrap4',
        dropdownParent: '#bookly-tbs',
        language: {
            noResults: function() { return BooklyL10n.noResultFound; }
        },
        matcher: function (params, data) {
            const term = $.trim(params.term).toLowerCase();
            if (term === '' || data.text.toLowerCase().indexOf(term) !== -1) {
                return data;
            }

            let result = null;
            const search = $(data.element).data('search');
            search &&
            search.find(function(text) {
                if (result === null && text.toLowerCase().indexOf(term) !== -1) {
                    result = data;
                }
            });

            return result;
        }
    });

    $('.bookly-js-select-ajax')
        .select2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: '#bookly-tbs',
            allowClear: true,
            placeholder: '',
            language  : {
                noResults: function() { return BooklyL10n.no_result_found; },
                searching: function () { return BooklyL10n.searching; }
            },
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    params.page = params.page || 1;
                    return {
                        action: this.action === undefined ? $(this).data('ajax--action') : this.action,
                        filter: params.term,
                        page: params.page,
                        csrf_token: BooklyL10nGlobal.csrf_token
                    };
                }
            },
        });

    /**
     * Init Columns.
     */
    $.each(BooklyL10n.datatables.payments.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'created_at':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return type === 'sort' ? data : row.created_format;
                        }
                    });
                    break;
                case 'start_date':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return type === 'sort' ? data : row.start_date_format;
                        }
                    });
                    break;
                case 'customer':
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
                case 'category':
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
                case 'service':
                case 'provider':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return $.fn.dataTable.render.text().display(data) + (row.multiple ? '<i class="fas fa-shopping-cart ml-1" title="' + BooklyL10n.multiple + '"></i>' : '');
                        }
                    });
                    break;
                default:
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
            }
        }
    });
    columns.push({
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        width: BooklyL10n.invoice.enabled ? 180 : 90,
        render: function (data, type, row, meta) {
            var buttons = '<div class="d-inline-flex">';
            if (BooklyL10n.invoice.enabled) {
                buttons += '<button type="button" class="btn btn-default mr-1" data-action="view-invoice" data-payment_id="' + row.id + '"><i class="far fa-fw fa-file-pdf mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.invoice.button + '</span></button>';
            }
            return buttons + '<button type="button" class="btn btn-default" data-action="show-payment" data-payment_id="' + row.id + '"><i class="fas fa-fw fa-list-alt mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.details + '…</span></button></div>';
        }
    });
    columns.push({
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
            return '<div class="custom-control custom-checkbox">' +
                '<input value="' + row.id + '" id="bookly-dt-' + row.id + '" type="checkbox" class="custom-control-input">' +
                '<label for="bookly-dt-' + row.id + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    columns[0].responsivePriority = 0;

    $.each(BooklyL10n.datatables.payments.settings.order, function (_, value) {
        const index = columns.findIndex(function (c) { return c.data === value.column; });
        if (index !== -1) {
            order.push([index, value.order]);
        }
    });

    /**
     * Init DataTables.
     */
    var dt = $payments_list.DataTable({
        order     : order,
        paging    : false,
        info      : false,
        searching : false,
        processing: true,
        responsive: true,
        serverSide: false,
        ajax      : {
            url    : ajaxurl,
            type   : 'POST',
            data   : function (d) {
                return $.extend({}, d, {
                    action: 'bookly_get_payments',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    filter: {
                        id: $id_filter.val(),
                        created_at: $creationDateFilter.data('date'),
                        type: $type_filter.val(),
                        customer: $customer_filter.val(),
                        staff   : $staff_filter.val(),
                        category: $category_filter.val(),
                        service : $service_filter.val(),
                        status  : $status_filter.val()
                    }
                });
            },
            dataSrc: function (json) {
                $payment_total.html(json.total);

                return json.data;
            }
        },
        columns: columns,
        language: {
            zeroRecords: BooklyL10n.zeroRecords,
            processing: BooklyL10n.processing
        }
    });
    dt.on( 'order',  function () {
        let order = [];
        dt.order().forEach(function(data) {
            order.push({
                column: columns[data[0]].data,
                order: data[1]
            });
        });
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_update_table_order',
                table: 'payments',
                csrf_token : BooklyL10n.csrfToken,
                order : order
            },
            dataType : 'json'
        });
    });

    /**
     * Select all coupons.
     */
    $check_all_button.on('change', function () {
        $payments_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $payments_list
    // On coupon select.
        .on('change', 'tbody input:checkbox', function () {
            $check_all_button.prop('checked', $payments_list.find('tbody input:not(:checked)').length == 0);
        })
        // Show invoice
        .on('click', '[data-action=view-invoice]', function () {
            window.location = $download_invoice.data('action') + '&invoices=' + $(this).data('payment_id');
        })
//        // Generate invoice
//        .on('click', '[data-action=view-invoice]', function () {
//            window.location = $generate_invoice.data('action') + '&invoices=' + $(this).data('payment_id');
//        })
        // show payment details
        .on('click', '[data-action=show-payment]', function () {
            BooklyPaymentDetailsDialog.showDialog({
                payment_id: dt.row($(this).closest('td')).data().id,
                done: function (event) {
                    dt.ajax.reload();
                }
            });
        });

    /**
     * Init date range picker.
     */

    var picker_ranges = {};
    picker_ranges[BooklyL10n.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[BooklyL10n.dateRange.today]     = [moment(), moment()];
    picker_ranges[BooklyL10n.dateRange.last_7]    = [moment().subtract(7, 'days'), moment()];
    picker_ranges[BooklyL10n.dateRange.last_30]   = [moment().subtract(30, 'days'), moment()];
    picker_ranges[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[BooklyL10n.dateRange.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    $creationDateFilter.daterangepicker(
        {
            parentEl : $creationDateFilter.parent(),
            startDate: pickers.creationDate.startDate,
            endDate  : pickers.creationDate.endDate,
            ranges   : picker_ranges,
            showDropdowns  : true,
            linkedCalendars: false,
            autoUpdateInput: false,
            locale: $.extend({},BooklyL10n.dateRange, BooklyL10n.datePicker)
        },
        function(start, end) {
            $creationDateFilter
                .data('date', start.format(pickers.dateFormat) + ' - ' + end.format(pickers.dateFormat))
                .find('span')
                .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
        }
    );

    $id_filter.on('keyup', function () { dt.ajax.reload(); });
    $creationDateFilter.on('apply.daterangepicker', function () { dt.ajax.reload(); });
    $type_filter.on('change', function () { dt.ajax.reload(); });
    $customer_filter.on('change', function () { dt.ajax.reload(); });
    $staff_filter.on('change', function () { dt.ajax.reload(); });
    $category_filter.on('change', function () { dt.ajax.reload(); });
    $service_filter.on('change', function () { dt.ajax.reload(); });
    $status_filter.on('change', function () { dt.ajax.reload(); });

    /**
     * Delete payments.
     */
    $delete_button.on('click', function () {
        if (confirm(BooklyL10n.areYouSure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            var data = [];
            var $checkboxes = $payments_list.find('tbody input:checked');
            $checkboxes.each(function () {
                data.push(this.value);
            });

            $.ajax({
                url  : ajaxurl,
                type : 'POST',
                data : {
                    action : 'bookly_delete_payments',
                    csrf_token : BooklyL10n.csrfToken,
                    data : data
                },
                dataType : 'json',
                success  : function(response) {
                    if (response.success) {
                        dt.ajax.reload();
                    } else {
                        alert(response.data.message);
                    }
                    ladda.stop();
                }
            });
        }
    });

    $download_invoice.on('click', function () {
        var invoices = [];
        $payments_list.find('tbody input:checked').each(function () {
            invoices.push(this.value);
        });
        if (invoices.length) {
            window.location = $(this).data('action') + '&invoices=' + invoices.join(',');
        }
    });
    $generate_invoice.on('click', function () {
         if($customer_filter.val() !== null && $category_filter.val() !== "" && $payments_list.find('tbody input:checked').length>0){
             var invoices = [];
             $payments_list.find('tbody input:checked').each(function () {
                 invoices.push(this.value);
             });
             if (invoices.length) {
                 window.location = $(this).data('action') + '&invoices=' + invoices.join(',');
             }
         }else{
            alert("Please select Customer,Category & 2 checkboxes to generate invoice");
         }
    });
});

//Generate Payments
jQuery(function($) {

    var
        $payments_list    = $('#bookly-generate-payments-list'),
        $check_all_button = $('#bookly-generate-check-all'),
        $id_filter        = $('#bookly-generate-filter-id'),
        $creationDateFilter = $('#bookly-generate-filter-date'),
        $type_filter      = $('#bookly-generate-filter-type'),
        $customer_filter  = $('#bookly-generate-filter-customer'),
        $staff_filter     = $('#bookly-generate-filter-staff'),
        $category_filter  = $('#bookly-generate-service-category'),
        $service_filter   = $('#bookly-generate-filter-service'),
        $status_filter    = $('#bookly-generate-filter-status'),
        $payment_total    = $('#bookly-generate-payment-total'),
        $delete_button    = $('#bookly-generate-delete'),
        $download_invoice = $('#bookly-download-invoices'),
        $edit_invoice     = $('#bookly-edit-generate-invoice'),
        $generate_invoice = $('#bookly-generate-invoices'),
        $master_invoice = $('#bookly-master-invoices'),
        urlParts          = document.URL.split('#'),
        columns           = [],
        order             = [],
        pickers = {
            dateFormat:     'YYYY-MM-DD',
            creationDate: {
                startDate: moment().subtract(30, 'days'),
                endDate  : moment(),
            },
        };

    $('.bookly-generate-js-select').val(null);
    $category_filter.val("");

    if (urlParts.length > 1) {
        urlParts[1].split('&').forEach(function (part) {
            var params = part.split('=');
            if (params[0] == 'created-date') {
                pickers.creationDate.startDate = moment(params['1'].substring(0, 10));
                pickers.creationDate.endDate = moment(params['1'].substring(11));
                $creationDateFilter
                    .data('date', pickers.creationDate.startDate.format(pickers.dateFormat) + ' - ' + pickers.creationDate.endDate.format(pickers.dateFormat))
                    .find('span')
                    .html(pickers.creationDate.startDate.format(BooklyL10n.dateRange.format) + ' - ' + pickers.creationDate.endDate.format(BooklyL10n.dateRange.format));
            } else {
                $('#bookly-generate-filter-' + params[0]).val(params[1]);
            }
        });
    } else {
        $.each(BooklyL10n.datatables.gayments.settings.filter, function (field, value) {
            if (value != '') {
                let $elem = $('#bookly-generate-filter-' + field);
                if ($elem.is(':checkbox')) {
                    $elem.prop('checked', value == '1');
                } else {
                    $elem.val(value);
                }
            }
            // check if select has correct values
            if ($('#bookly-generate-filter-' + field).prop('type') == 'select-one') {
                if ($('#bookly-generate-filter-' + field + ' option[value="' + value + '"]').length == 0) {
                    $('#bookly-generate-filter-' + field).val(null);
                }
            }
        });
    }

    $('.bookly-generate-js-select').select2({
        width: '100%',
        allowClear: true,
        placeholder: '',
        theme: 'bootstrap4',
        dropdownParent: '#bookly-tbs',
        language: {
            noResults: function() { return BooklyL10n.noResultFound; }
        },
        matcher: function (params, data) {
            const term = $.trim(params.term).toLowerCase();
            if (term === '' || data.text.toLowerCase().indexOf(term) !== -1) {
                return data;
            }

            let result = null;
            const search = $(data.element).data('search');
            search &&
            search.find(function(text) {
                if (result === null && text.toLowerCase().indexOf(term) !== -1) {
                    result = data;
                }
            });

            return result;
        }
    });

    $('.bookly-generate-js-select-ajax')
        .select2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: '#bookly-tbs',
            allowClear: true,
            placeholder: '',
            language  : {
                noResults: function() { return BooklyL10n.no_result_found; },
                searching: function () { return BooklyL10n.searching; }
            },
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    params.page = params.page || 1;
                    return {
                        action: this.action === undefined ? $(this).data('ajax--action') : this.action,
                        filter: params.term,
                        page: params.page,
                        csrf_token : BooklyL10n.csrfToken
                    };
                }
            },
        });

    /**
     * Init Columns.
     */
    $.each(BooklyL10n.datatables.gayments.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'created_at':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return type === 'sort' ? data : row.created_format;
                        }
                    });
                    break;
                case 'customer':
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
                case 'category':
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
                case 'provider':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return $.fn.dataTable.render.text().display(data) + (row.multiple ? '<i class="fas fa-shopping-cart ml-1" title="' + BooklyL10n.multiple + '"></i>' : '');
                        }
                    });
                    break;
                default:
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
            }
        }
    });
    columns.push({
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        width: BooklyL10n.invoice.enabled ? 180 : 90,
        render: function (data, type, row, meta) {
            var buttons = '<div class="d-inline-flex">';
            if (BooklyL10n.invoice.enabled) {
                buttons += '<button type="button" class="btn btn-default mr-1" data-action="view-invoice" data-payment_id="' + row.id + '"><i class="far fa-fw fa-file-pdf mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.invoice.button + '</span></button>';
                buttons += '<button type="button" class="btn btn-default mr-1" data-action="edit-generate-invoice" data-payment_id="' + row.id + '"><i class="far fa-fw fa-file-pdf mr-lg-1"></i><span class="d-none d-lg-inline">Edit Invoice</span></button>';
            }
            return buttons + '<button type="button" class="btn btn-default" data-action="show-payment" data-payment_id="' + row.id + '"><i class="fas fa-fw fa-list-alt mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.details + '…</span></button></div>';
        }
    });
    columns.push({
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
            return '<div class="custom-control custom-checkbox">' +
                '<input value="' + row.id + '" id="bookly-generate-dt-' + row.id + '" type="checkbox" class="custom-control-input">' +
                '<label for="bookly-generate-dt-' + row.id + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    columns[0].responsivePriority = 0;

    $.each(BooklyL10n.datatables.gayments.settings.order, function (_, value) {
        const index = columns.findIndex(function (c) { return c.data === value.column; });
        if (index !== -1) {
            order.push([index, value.order]);
        }
    });

    /**
     * Init DataTables.
     */
    var dt = $payments_list.DataTable({
        order     : order,
        paging    : false,
        info      : false,
        searching : false,
        processing: true,
        responsive: true,
        serverSide: false,
        ajax      : {
            url    : ajaxurl,
            type   : 'POST',
            data   : function (d) {
                return $.extend({}, d, {
                    action    : 'bookly_get_gayments',
                    csrf_token: BooklyL10n.csrfToken,
                    filter    : {
                        id      : $id_filter.val(),
                        created_at: $creationDateFilter.data('date'),
                        type    : $type_filter.val(),
                        customer: $customer_filter.val(),
                        staff   : $staff_filter.val(),
                        category: $category_filter.val(),
                        status  : $status_filter.val()
                    }
                });
            },
            dataSrc: function (json) {
                $payment_total.html(json.total);

                return json.data;
            }
        },
        columns   : columns,
        language  : {
            zeroRecords: BooklyL10n.zeroRecords,
            processing : BooklyL10n.processing
        }
    });
    dt.on( 'order',  function () {
        let order = [];
        dt.order().forEach(function(data) {
            order.push({
                column: columns[data[0]].data,
                order: data[1]
            });
        });
        $.ajax({
            url  : ajaxurl,
            type : 'POST',
            data : {
                action : 'bookly_update_table_order',
                table: 'payments',
                csrf_token : BooklyL10n.csrfToken,
                order : order
            },
            dataType: 'json'
        });
    });

    /**
     * Select all coupons.
     */
    $check_all_button.on('change', function () {
        $payments_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $payments_list
        // On coupon select.
        .on('change', 'tbody input:checkbox', function () {
            $check_all_button.prop('checked', $payments_list.find('tbody input:not(:checked)').length == 0);
        })
        // Show invoice
        .on('click', '[data-action=view-invoice]', function () {
            window.location = $download_invoice.data('action') + '&invoices=' + $(this).data('payment_id');
        })
        // Edit invoice
        .on('click', '[data-action=edit-generate-invoice]', function () {
            window.location = $edit_invoice.data('action') + '&invoices=' + $(this).data('payment_id');
        })
        // show payment details
        .on('click', '[data-action=show-payment]', function () {
            BooklyPaymentDetailsDialog.showDialog({
                payment_id: dt.row($(this).closest('td')).data().id,
                done: function (event) {
                    dt.ajax.reload();
                }
            });
        });

    /**
     * Init date range picker.
     */

    var picker_ranges = {};
    picker_ranges[BooklyL10n.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[BooklyL10n.dateRange.today]     = [moment(), moment()];
    picker_ranges[BooklyL10n.dateRange.last_7]    = [moment().subtract(7, 'days'), moment()];
    picker_ranges[BooklyL10n.dateRange.last_30]   = [moment().subtract(30, 'days'), moment()];
    picker_ranges[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[BooklyL10n.dateRange.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    $creationDateFilter.daterangepicker(
        {
            parentEl : $creationDateFilter.parent(),
            startDate: pickers.creationDate.startDate,
            endDate  : pickers.creationDate.endDate,
            ranges   : picker_ranges,
            showDropdowns  : true,
            linkedCalendars: false,
            autoUpdateInput: false,
            locale: $.extend({},BooklyL10n.dateRange, BooklyL10n.datePicker)
        },
        function(start, end) {
            $creationDateFilter
                .data('date', start.format(pickers.dateFormat) + ' - ' + end.format(pickers.dateFormat))
                .find('span')
                .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
        }
    );

    $id_filter.on('keyup', function () { dt.ajax.reload(); });
    $creationDateFilter.on('apply.daterangepicker', function () { dt.ajax.reload(); });
    $type_filter.on('change', function () { dt.ajax.reload(); });
    $customer_filter.on('change', function () { dt.ajax.reload(); });
    $staff_filter.on('change', function () { dt.ajax.reload(); });
    $category_filter.on('change', function () { dt.ajax.reload(); });
    $service_filter.on('change', function () { dt.ajax.reload(); });
    $status_filter.on('change', function () { dt.ajax.reload(); });

    /**
     * Delete payments.
     */
    $delete_button.on('click', function () {
        if (confirm(BooklyL10n.areYouSure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            var data = [];
            var $checkboxes = $payments_list.find('tbody input:checked');
            $checkboxes.each(function () {
                data.push(this.value);
            });

            $.ajax({
                url  : ajaxurl,
                type : 'POST',
                data : {
                    action : 'bookly_delete_payments',
                    csrf_token : BooklyL10n.csrfToken,
                    data : data
                },
                dataType : 'json',
                success  : function(response) {
                    if (response.success) {
                        dt.ajax.reload();
                    } else {
                        alert(response.data.message);
                    }
                    ladda.stop();
                }
            });
        }
    });

    $download_invoice.on('click', function () {
        var invoices = [];
        $payments_list.find('tbody input:checked').each(function () {
            invoices.push(this.value);
        });
        if (invoices.length) {
            window.location = $(this).data('action') + '&invoices=' + invoices.join(',');
        }
    });
    $generate_invoice.on('click', function () {
         var invoices = [];
         $payments_list.find('tbody input:checked').each(function () {
             invoices.push(this.value);
         });
         if (invoices.length) {
             window.location = $(this).data('action') + '&invoices=' + invoices.join(',');
         }
    });
    $master_invoice.on('click', function () {
        if($customer_filter.val() === null && $category_filter.val() !== "" && $payments_list.find('tbody input:checked').length>1){
             var invoices = [];
             $payments_list.find('tbody input:checked').each(function () {
                 invoices.push(this.value);
             });
             if (invoices.length) {
                 window.location = $(this).data('action') + '&invoices=' + invoices.join(',');
             }
        }else if($customer_filter.val() !== null){
            alert("Please remove customer filter and generate master invoice only by category");
        }else if($category_filter.val() === ""){
            alert("Please select a category filter and generate master invoice only by category");
        }else{
            alert("Please select more than 2 checkboxes to generate master invoice by category");
        }
    });
});

//Master Invoice Payments
jQuery(function($) {

    var
        $payments_list    = $('#bookly-master-payments-list'),
        $check_all_button = $('#bookly-master-check-all'),
        $id_filter        = $('#bookly-master-filter-id'),
        $creationDateFilter = $('#bookly-master-filter-date'),
        $type_filter      = $('#bookly-master-filter-type'),
        $customer_filter  = $('#bookly-master-filter-customer'),
        $staff_filter     = $('#bookly-master-filter-staff'),
        $category_filter  = $('#bookly-master-service-category'),
        $service_filter   = $('#bookly-master-filter-service'),
        $status_filter    = $('#bookly-master-filter-status'),
        $payment_total    = $('#bookly-master-payment-total'),
        $delete_button    = $('#bookly-master-delete'),
        $download_invoice = $('#bookly-download-master-invoice'),
        $edit_invoice     = $('#bookly-edit-master-invoice'),
        $payment_complete = $('#bookly-master-payment-complete'),
        urlParts          = document.URL.split('#'),
        columns           = [],
        order             = [],
        pickers = {
            dateFormat:     'YYYY-MM-DD',
            creationDate: {
                startDate: moment().subtract(30, 'days'),
                endDate  : moment(),
            },
        };

    $('.bookly-master-js-select').val(null);
    $category_filter.val("");

    if (urlParts.length > 1) {
        urlParts[1].split('&').forEach(function (part) {
            var params = part.split('=');
            if (params[0] == 'created-date') {
                pickers.creationDate.startDate = moment(params['1'].substring(0, 10));
                pickers.creationDate.endDate = moment(params['1'].substring(11));
                $creationDateFilter
                    .data('date', pickers.creationDate.startDate.format(pickers.dateFormat) + ' - ' + pickers.creationDate.endDate.format(pickers.dateFormat))
                    .find('span')
                    .html(pickers.creationDate.startDate.format(BooklyL10n.dateRange.format) + ' - ' + pickers.creationDate.endDate.format(BooklyL10n.dateRange.format));
            } else {
                $('#bookly-master-filter-' + params[0]).val(params[1]);
            }
        });
    } else {
        $.each(BooklyL10n.datatables.mayments.settings.filter, function (field, value) {
            if (value != '') {
                let $elem = $('#bookly-master-filter-' + field);
                if ($elem.is(':checkbox')) {
                    $elem.prop('checked', value == '1');
                } else {
                    $elem.val(value);
                }
            }
            // check if select has correct values
            if ($('#bookly-master-filter-' + field).prop('type') == 'select-one') {
                if ($('#bookly-master-filter-' + field + ' option[value="' + value + '"]').length == 0) {
                    $('#bookly-master-filter-' + field).val(null);
                }
            }
        });
    }

    $('.bookly-master-js-select').select2({
        width: '100%',
        allowClear: true,
        placeholder: '',
        theme: 'bootstrap4',
        dropdownParent: '#bookly-tbs',
        language: {
            noResults: function() { return BooklyL10n.noResultFound; }
        },
        matcher: function (params, data) {
            const term = $.trim(params.term).toLowerCase();
            if (term === '' || data.text.toLowerCase().indexOf(term) !== -1) {
                return data;
            }

            let result = null;
            const search = $(data.element).data('search');
            search &&
            search.find(function(text) {
                if (result === null && text.toLowerCase().indexOf(term) !== -1) {
                    result = data;
                }
            });

            return result;
        }
    });

    $('.bookly-master-js-select-ajax')
        .select2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: '#bookly-tbs',
            allowClear: true,
            placeholder: '',
            language  : {
                noResults: function() { return BooklyL10n.no_result_found; },
                searching: function () { return BooklyL10n.searching; }
            },
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    params.page = params.page || 1;
                    return {
                        action: this.action === undefined ? $(this).data('ajax--action') : this.action,
                        filter: params.term,
                        page: params.page,
                        csrf_token : BooklyL10n.csrfToken
                    };
                }
            },
        });

    /**
     * Init Columns.
     */
    $.each(BooklyL10n.datatables.mayments.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'created_at':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return type === 'sort' ? data : row.created_format;
                        }
                    });
                    break;
                case 'start_date':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return type === 'sort' ? data : row.start_date_format;
                        }
                    });
                    break;
                case 'customer':
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
                case 'category':
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
                case 'service':
                case 'provider':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return $.fn.dataTable.render.text().display(data) + (row.multiple ? '<i class="fas fa-shopping-cart ml-1" title="' + BooklyL10n.multiple + '"></i>' : '');
                        }
                    });
                    break;
                default:
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
            }
        }
    });
    columns.push({
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        width: BooklyL10n.invoice.enabled ? 180 : 90,
        render: function (data, type, row, meta) {
            var buttons = '<div class="d-inline-flex">';
            if (BooklyL10n.invoice.enabled) {
                buttons += '<button type="button" class="btn btn-default mr-1" data-action="view-master-invoice" data-payment_id="' + row.id + '"><i class="far fa-fw fa-file-pdf mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.invoice.button + '</span></button>';
                buttons += '<button type="button" class="btn btn-default mr-1" data-action="edit-master-invoice" data-payment_id="' + row.id + '"><i class="far fa-fw fa-file-pdf mr-lg-1"></i><span class="d-none d-lg-inline">Edit Invoice</span></button>';
                buttons += '<button type="button" class="btn btn-default mr-1" data-action="master-payment-complete" data-payment_id="' + row.id + '"><i class="far fa-fw fa-file-pdf mr-lg-1"></i><span class="d-none d-lg-inline">Complete Payment</span></button>';
            }
            return buttons + '<button type="button" class="btn btn-default d-none" data-action="show-payment" data-payment_id="' + row.id + '"><i class="fas fa-fw fa-list-alt mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.details + '…</span></button></div>';
        }
    });
    columns.push({
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
            return '<div class="custom-control custom-checkbox">' +
                '<input value="' + row.id + '" id="bookly-master-dt-' + row.id + '" type="checkbox" class="custom-control-input">' +
                '<label for="bookly-master-dt-' + row.id + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    columns[0].responsivePriority = 0;

    $.each(BooklyL10n.datatables.mayments.settings.order, function (_, value) {
        const index = columns.findIndex(function (c) { return c.data === value.column; });
        if (index !== -1) {
            order.push([index, value.order]);
        }
    });

    /**
     * Init DataTables.
     */
    var dt = $payments_list.DataTable({
        order     : order,
        paging    : false,
        info      : false,
        searching : false,
        processing: true,
        responsive: true,
        serverSide: false,
        ajax      : {
            url    : ajaxurl,
            type   : 'POST',
            data   : function (d) {
                return $.extend({}, d, {
                    action    : 'bookly_get_mayments',
                    csrf_token: BooklyL10n.csrfToken,
                    filter    : {
                        id      : $id_filter.val(),
                        created_at: $creationDateFilter.data('date'),
                        type    : $type_filter.val(),
                        customer: $customer_filter.val(),
                        staff   : $staff_filter.val(),
                        category: $category_filter.val(),
                        service : $service_filter.val(),
                        status  : $status_filter.val()
                    }
                });
            },
            dataSrc: function (json) {
                $payment_total.html(json.total);

                return json.data;
            }
        },
        columns   : columns,
        language  : {
            zeroRecords: BooklyL10n.zeroRecords,
            processing : BooklyL10n.processing
        }
    });
    dt.on( 'order',  function () {
        let order = [];
        dt.order().forEach(function(data) {
            order.push({
                column: columns[data[0]].data,
                order: data[1]
            });
        });
        $.ajax({
            url  : ajaxurl,
            type : 'POST',
            data : {
                action : 'bookly_update_table_order',
                table: 'payments',
                csrf_token : BooklyL10n.csrfToken,
                order : order
            },
            dataType : 'json'
        });
    });

    /**
     * Select all coupons.
     */
    $check_all_button.on('change', function () {
        $payments_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $payments_list
    // On coupon select.
        .on('change', 'tbody input:checkbox', function () {
            $check_all_button.prop('checked', $payments_list.find('tbody input:not(:checked)').length == 0);
        })
        // Show invoice
        .on('click', '[data-action=view-master-invoice]', function () {
            window.location = $download_invoice.data('action') + '&invoices=' + $(this).data('payment_id');
        })
        // Edit invoice
        .on('click', '[data-action=edit-master-invoice]', function () {
            window.location = $edit_invoice.data('action') + '&invoices=' + $(this).data('payment_id');
        })
        // complete Master invoice payment
        .on('click', '[data-action=master-payment-complete]', function () {
            var answer = window.confirm("Are you sure you want to mark the master invoice payment to complete, Once done it cannot be undone");
            if (answer) {
                window.location = $payment_complete.data('action') + '&invoices=' + $(this).data('payment_id');
            }
        })
        // show payment details
        .on('click', '[data-action=show-payment]', function () {
            BooklyPaymentDetailsDialog.showDialog({
                payment_id: dt.row($(this).closest('td')).data().id,
                done: function (event) {
                    dt.ajax.reload();
                }
            });
        });

    /**
     * Init date range picker.
     */

    var picker_ranges = {};
    picker_ranges[BooklyL10n.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[BooklyL10n.dateRange.today]     = [moment(), moment()];
    picker_ranges[BooklyL10n.dateRange.last_7]    = [moment().subtract(7, 'days'), moment()];
    picker_ranges[BooklyL10n.dateRange.last_30]   = [moment().subtract(30, 'days'), moment()];
    picker_ranges[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[BooklyL10n.dateRange.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    $creationDateFilter.daterangepicker(
        {
            parentEl : $creationDateFilter.parent(),
            startDate: pickers.creationDate.startDate,
            endDate  : pickers.creationDate.endDate,
            ranges   : picker_ranges,
            showDropdowns  : true,
            linkedCalendars: false,
            autoUpdateInput: false,
            locale: $.extend({},BooklyL10n.dateRange, BooklyL10n.datePicker)
        },
        function(start, end) {
            $creationDateFilter
                .data('date', start.format(pickers.dateFormat) + ' - ' + end.format(pickers.dateFormat))
                .find('span')
                .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
        }
    );

    $id_filter.on('keyup', function () { dt.ajax.reload(); });
    $creationDateFilter.on('apply.daterangepicker', function () { dt.ajax.reload(); });
    $type_filter.on('change', function () { dt.ajax.reload(); });
    $customer_filter.on('change', function () { dt.ajax.reload(); });
    $staff_filter.on('change', function () { dt.ajax.reload(); });
    $category_filter.on('change', function () { dt.ajax.reload(); });
    $service_filter.on('change', function () { dt.ajax.reload(); });
    $status_filter.on('change', function () { dt.ajax.reload(); });

    /**
     * Delete payments.
     */
    $delete_button.on('click', function () {
        if (confirm(BooklyL10n.areYouSure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            var data = [];
            var $checkboxes = $payments_list.find('tbody input:checked');
            $checkboxes.each(function () {
                data.push(this.value);
            });

            $.ajax({
                url  : ajaxurl,
                type : 'POST',
                data : {
                    action : 'bookly_delete_payments',
                    csrf_token : BooklyL10n.csrfToken,
                    data : data
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        dt.ajax.reload();
                    } else {
                        alert(response.data.message);
                    }
                    ladda.stop();
                }
            });
        }
    });

    $download_invoice.on('click', function () {
        var invoices = [];
        $payments_list.find('tbody input:checked').each(function () {
            invoices.push(this.value);
        });
        if (invoices.length) {
            window.location = $(this).data('action') + '&invoices=' + invoices.join(',');
        }
    });
});