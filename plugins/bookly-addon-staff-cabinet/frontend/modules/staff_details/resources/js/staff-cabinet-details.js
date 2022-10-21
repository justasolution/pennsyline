(function ($) {
    window.booklyStaffDetails = function (Options) {
        let $container = $('.' + Options.form_id),
            options = {
                intlTelInput: BooklySCDetailsL10n.intlTelInput,
                l10n: BooklySCDetailsL10n,
                saving: function (alerts) {
                    $.each(alerts, function (type, messages) {
                        if (messages.length > 0) {
                            window.booklyNativeAlert(messages.join('\n'));
                        }
                    });
                }
            };

        // Init Staff Details.
        new BooklyStaffDetails($container, options);
    };
})(jQuery);