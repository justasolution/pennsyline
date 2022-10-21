(function ($) {
    window.booklyStaffServices = function (Options) {
        let $container = $('.' + Options.form_id);
        if (!$container.length) {
            return;
        }
        let options = {
            get_staff_services: {
                action: 'bookly_staff_cabinet_get_services',
                staff_id: Options.staff_id
            },
            saving: function (alerts) {
                $.each(alerts, function (type, messages) {
                    if (messages.length > 0) {
                        window.booklyNativeAlert(messages.join('\n'));
                    }
                });
            },
            booklyAlert: function (alerts) {
                options.saving(alerts);
            },
            validation: function () {},
            l10n: BooklySCServicesL10n
        };

        // Init Staff Services.
        new BooklyStaffServices($container, options);
    };
})(jQuery);