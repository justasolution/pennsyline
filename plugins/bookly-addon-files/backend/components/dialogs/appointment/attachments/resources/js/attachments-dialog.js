jQuery(function($) {

    var
        $appointments_list = $('#bookly-appointments-list'),
        $dialog = $('#bookly-ca-attachments-dialog')
    ;

    $appointments_list
        .on('click', '[data-action=show-attachments]', function (e) {
            e.preventDefault();
            let dt = $appointments_list.DataTable(),
                $el = $(this).closest('td');
            if ($el.hasClass('child')) {
                $el = $el.closest('tr').prev();
            }
            let data = dt.row($el).data();
            $dialog.data('customer_appointment_id', data.ca_id).booklyModal('show',{});
        });

    $dialog
        .on('show.bs.modal', function (e) {
            var ca_id = $dialog.data('customer_appointment_id');
            $.ajax({
                url: ajaxurl,
                data: {action: 'bookly_files_get_attachments', ca_id: ca_id, csrf_token: BooklyL10nGlobal.csrf_token},
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('.modal-body', $dialog).html(response.data.html);
                    }
                }
            });

        })
        .on('hidden.bs.modal', function () {
            $('.modal-body', $dialog).html('<div class="bookly-loading"></div>');
        });
});