(function ($) {
    window.booklyStaffSpecialDays = function (Options) {
        let $container = $('.' + Options.form_id);
        if (!$container.length) {
            return;
        }
        let options = {
            container: '.bookly-js-staff-cabinet-special-days-wrap',
            staff_id: Options.staff_id,
            location_id: null,
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
            onLoad: function () {},
            l10n: BooklySCSpecialDaysL10n
        };
        // Init Staff Special Days.
        new BooklyStaffSpecialDays($container, options);
    };
})(jQuery);