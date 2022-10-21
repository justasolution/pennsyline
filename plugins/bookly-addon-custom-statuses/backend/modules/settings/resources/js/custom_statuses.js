jQuery(function($) {

    let
        $list            = $('#bookly-custom-statuses'),
        $checkAllButton  = $('#bookly-custom-statuses-check-all'),
        $modal           = $('#bookly-custom-statuses-modal'),
        $newStatusTitle  = $('#bookly-custom-statuses-new-title'),
        $editStatusTitle = $('#bookly-custom-statuses-edit-title'),
        $statusName      = $('#bookly-custom-statuses-name'),
        $statusBusy      = $('#bookly-custom-statuses-busy'),
        $saveButton      = $('#bookly-custom-statuses-save'),
        $deleteButton    = $('#bookly-custom-statuses-delete'),
        $defaultStatus   = $('#bookly_gen_default_appointment_status'),
        columns,
        row
    ;

    /**
     * Init Columns.
     */
    columns = [
        {
            data: 'position'
        },
        {
            render: function (data, type, row, meta) {
                var $i = $('<i class="fas fa-fw fa-bars text-muted bookly-cursor-move bookly-js-draghandle" />');
                $i.attr('title', BooklyCustomStatusesL10n.reorder);

                return $i.get(0).outerHTML;
            },
            responsivePriority: 0
        }
    ];

    $.each(BooklyCustomStatusesL10n.datatables.custom_statuses.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'busy':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return data === '1' ? BooklyCustomStatusesL10n.busy : BooklyCustomStatusesL10n.free;
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
        render: function (data, type, row, meta) {
            var $btn = $('<button type="button" class="btn btn-default" data-action="edit" />');
            $btn.html('<span class="d-none d-lg-inline">' + BooklyCustomStatusesL10n.edit + '…</span>');
            $btn.prepend('<i class="far fa-fw fa-edit mr-lg-1"/> ');

            return $btn.get(0).outerHTML;
        }
    });
    columns.push({
        responsivePriority: 1,
        render: function (data, type, row, meta) {
            return '<div class="custom-control custom-checkbox">' +
                '<input value="' + row.id + '" id="bookly-cs-' + row.id + '" type="checkbox" class="custom-control-input">' +
                '<label for="bookly-cs-' + row.id + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    /**
     * Init DataTable.
     */
    let dt = $list.DataTable({
        paging: false,
        info: false,
        searching: false,
        processing: true,
        responsive: true,
        ajax: {
            url: ajaxurl,
            data: {
                action: 'bookly_custom_statuses_get_statuses',
                csrf_token: BooklyL10nGlobal.csrf_token
            }
        },
        rowReorder: {
            dataSrc: 'position',
            snapX: true,
            selector: '.bookly-js-draghandle'
        },
        order: [0, 'asc'],
        columnDefs: [
            { visible: false, targets: 0 },
            { orderable: false, targets: '_all' }
        ],
        columns: columns,
        language: {
            zeroRecords: BooklyCustomStatusesL10n.zeroRecords,
            processing:  BooklyCustomStatusesL10n.processing
        }
    }).on( 'row-reordered', function ( e, diff, edit ) {
        let positions = [];
        dt.data().each(function (item) {
            positions.push({position: parseInt(item.position), id: item.id});
        });
        $.ajax({
            url  : ajaxurl,
            type : 'POST',
            data : {
                action: 'bookly_custom_statuses_update_statuses_position',
                csrf_token: BooklyL10nGlobal.csrf_token,
                positions: (positions.sort(function (a, b) {
                    return a.position - b.position
                }))
                .map(function (value) {
                    return value.id;
                })
            },
            dataType: 'json',
            success: function (response) {}
        });
    });

    /**
     * Fix datatables layout.
     */
    $('a[href="#bookly_settings_custom_statuses"]').on('shown.bs.tab', function (e) {
        dt.columns.adjust().responsive.recalc();
    });

    /**
     * Select all statuses.
     */
    $checkAllButton.on('change', function () {
        $list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $list
        // On status select.
        .on('change', 'tbody input:checkbox', function () {
            $checkAllButton.prop('checked', $list.find('tbody input:not(:checked)').length === 0);
        })
        // Edit status.
        .on('click', '[data-action=edit]', function () {
            row = dt.row($(this).closest('td'));
            $modal.booklyModal('show');
        });

    /**
     * On show modal.
     */
    $modal
        .on('show.bs.modal', function (e) {
            var data;
            if (row) {
                data = row.data();
                $newStatusTitle.hide();
                $editStatusTitle.show();
            } else {
                data = {name: '', busy: 1};
                $newStatusTitle.show();
                $editStatusTitle.hide();
            }
            $statusName.val(data.name);
            $statusBusy.val(data.busy);
        })
        .on('hidden.bs.modal', function() { row = null });

    /**
     * Save status.
     */
    $saveButton.on('click', function (e) {
        e.preventDefault();
        var $form = $(this).closest('form');
        var data = $form.serializeArray();
        data.push({name: 'action', value: 'bookly_custom_statuses_save_status'});
        if (row){
            data.push({name: 'id', value: row.data().id});
        }
        var ladda = Ladda.create(this, {timeout: 2000});
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (row) {
                        row.data(response.data).draw();
                    } else {
                        dt.row.add(response.data).draw();
                    }
                    $modal.booklyModal('hide');
                    if ($defaultStatus.length > 0 && $('option[value=' + response.data.slug + ']',$defaultStatus).length === 0) {
                        $defaultStatus.append($('<option/>', {value: response.data.slug, text: response.data.name}));
                    }
                } else {
                    alert(response.data.message);
                }
                ladda.stop();
            }
        });

    });

    /**
     * Delete statuses.
     */
    $deleteButton.on('click', function () {
        if (confirm(BooklyCustomStatusesL10n.areYouSure)) {
            let ladda = Ladda.create(this),
                ids = [];
            ladda.start();

            $('tbody input:checked', $list).each(function () {
                ids.push(this.value);
            });

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_custom_statuses_delete_statuses',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    ids: ids
                },
                dataType: 'json',
                success: function(response) {
                    ladda.stop();
                    if (response.success) {
                        $('tbody input:checked', $list).closest('td').each(function () {
                            if ($defaultStatus.length > 0) {
                                let slug = dt.row(this).data().slug
                                $('option[value="' + slug + '"]', $defaultStatus).remove();
                            }
                            dt.row(this).remove().draw();
                        });
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        }
    });

});