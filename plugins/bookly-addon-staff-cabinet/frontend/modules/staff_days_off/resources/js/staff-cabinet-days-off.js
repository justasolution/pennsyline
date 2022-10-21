(function ($) {
    window.booklyStaffDaysOff = function (Options) {
        var $container = $('.' + Options.form_id);
        if (!$container.length) {
            return;
        }
        var options = {
            staff_id: Options.staff_id,
            l10n: BooklySCDaysOffL10n
        };

        // Init Staff Days Off.
        new BooklyStaffDaysOff($container, options);
    };
})(jQuery);