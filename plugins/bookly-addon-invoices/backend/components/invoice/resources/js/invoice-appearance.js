jQuery(function($) {
    var
        $editableElements = $('#bookly_settings_invoices .bookly-js-editable'),
        $reset_button     = $('button[type=reset]'),
        $save_button      = $('#bookly_settings_invoices  button[type=submit]'),
        $due_days         = $('#bookly_invoices_due_days')
    ;

    $editableElements.booklyEditable({
        empty: BooklyInvoicesL10n.empty,
        container: '#bookly_settings_invoices',
    });

    $save_button.on('click', function(e) {
        $due_days.removeClass('is-invalid');
        if ( $due_days.val() > 365
            || $due_days.val() < 1
        ) {
            Ladda.stopAll();
            $due_days.addClass('is-invalid');
            booklyAlert({error: [BooklyInvoicesL10n.invalid_due_days]});
            return false;
        }

        var form = $save_button.closest('form').get(0);

        $editableElements.each(function () {
            // Add data from editable elements.
            var input = document.createElement("input"),
                value = $(this).booklyEditable('getValue', true),
                name  = Object.keys($(this).data('values'))[0]
            ;
            input.type  = 'hidden';
            input.value = value[name];
            input.name  = name;
            form.appendChild(input);
        });
    });

    // Reset options to defaults.
    $reset_button.on('click', function() {
        // Reset editable texts.
        $editableElements.each(function () {
            $(this).booklyEditable('reset');
        });

        // Reset header and footer image.
        var $form = $(this).parents('form');
        $('.bookly-js-image', $form).each(function () {
            var $input = $(this).parents('td').find('[type=hidden]');
            $(this).attr('style', $(this).data('style'));
            $input.val($input.data('default'));
        });
    });
});