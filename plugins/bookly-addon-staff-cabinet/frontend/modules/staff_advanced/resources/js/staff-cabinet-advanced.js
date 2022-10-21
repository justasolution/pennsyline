(function ($) {
    window.booklyStaffAdvanced = function (Options) {
        var $container = $('.' + Options.form_id);
        if (!$container.length) {
            return;
        }
        var options = {
            get_staff_advanced: {
                action: 'bookly_staff_cabinet_get_advanced',
                staff_id: Options.staff_id,
                layout: Options.layout,
            },
            booklyAlert: function (alerts) {
                $.each(alerts, function (type, messages) {
                    if(messages.length > 0){
                        window.booklyNativeAlert(messages.join('\n'));
                    }
                });
            },
            validation: function (has_error, info) {
                $('#bookly-advanced-save', $container).prop('disabled', has_error);
                let $error = $('.bookly-js-advanced-error',$container);
                if (has_error) {
                    $error.html(info);
                } else {
                    $error.html('');
                }
            },
            l10n: BooklySCAdvancedL10n,
            saving: function (alerts) {
                $.each(alerts, function (type, messages) {
                    if(messages.length > 0){
                        window.booklyNativeAlert(messages.join('\n'));
                    }
                });
            }
        };

        // Init Staff Advanced.
        new BooklyStaffAdvanced($container, options);
    };
})(jQuery);