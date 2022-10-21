(function ($) {
    window.booklyStaffCalendar = function (Options) {
        let $container = $('.bookly-js-calendar.' + Options.calendar_id);
        if (!$container.length) {
            return;
        }
        let options = {
            calendar: {
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    end: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                view: 'dayGridMonth',
            },
            getCurrentStaffId: function () {
                return Options.staff_id;
            },
            viewChanged: function (view) {
                calendar.ec.setOption('height', heightEC(view.type));
            },
            l10n: BooklySCCalendarL10n
        },
        $footer = $('.bookly-js-calendar-footer', $container.closest('.bookly-staff-cabinet')),
        calendarTimer = null;

        // Init EventCalendar.
        let calendar = new BooklyCalendar($container, options);

        // Export to CSV modal
        let $exportDialog = $('#bookly-js-export-dialog'),
            $exportSelectAll = $('#bookly-js-export-select-all', $exportDialog);
        $('.bookly-js-export-btn').click(function () {
            $exportDialog.booklyModal('show');
        });

        $exportDialog.find('form').on('submit', function () {
            $exportDialog.booklyModal('hide');
        });

        $exportSelectAll
            .on('click', function () {
                let checked = this.checked;
                $('.bookly-js-columns input', $exportDialog).each(function () {
                    $(this).prop('checked', checked);
                });
            });

        $('.bookly-js-columns input', $exportDialog)
            .on('change', function () {
                $exportSelectAll.prop('checked', $('.bookly-js-columns input:checked', $exportDialog).length == $('.bookly-js-columns input', $exportDialog).length);
            });

        $exportDialog.on('show.bs.modal', function () {
            let calendar_view = calendar.ec.view;
            $('.bookly-js-export-start').val(moment(calendar_view.activeStart).format('YYYY-MM-DD'));
            $('.bookly-js-export-end').val(moment(calendar_view.activeEnd).format('YYYY-MM-DD'));
        });

        function heightEC(view_type) {
            let calendar_tools_height = 71,
                day_head_height = 28,
                slot_height = 17.85,
                weeks_rows = 5,
                day_slots_count = 5,
                height = (calendar_tools_height + (day_slots_count * slot_height + day_head_height) * weeks_rows)
            ;
            if (view_type != 'dayGridMonth') {
                if ($('.ec-content', $container).height() < height) {
                    height = 'auto';
                }
            }
            return height === 'auto' ? 'auto' : (calendar_tools_height + height) + 'px';
        }

        $(window).on('resize', function () {
            calendar.ec.setOption('height', heightEC(calendar.ec.getOption('view')));
        });

        function refreshBooklyCalendar() {
            let $refresh = $('input[name="bookly_calendar_refresh_rate"]:checked',$footer);
            clearTimeout(calendarTimer);
            if ($refresh.val() > 0) {
                calendarTimer = setInterval(function () {
                    calendar.ec.refetchEvents();
                }, $refresh.val() * 1000);
            }
        }

        $('input[name="bookly_calendar_refresh_rate"]', $footer).change(function () {
            $.post(
                ajaxurl,
                {action: 'bookly_update_calendar_refresh_rate', csrf_token: BooklyL10nGlobal.csrf_token, rate: this.value},
                function (response) {},
                'json'
            );
            if (this.value > 0) {
                $(this).closest('.btn-group').find('button').addClass('btn-success').removeClass('btn-default');
            } else {
                $(this).closest('.btn-group').find('button').addClass('btn-default').removeClass('btn-success');
            }
            refreshBooklyCalendar();
        });

        $('.bookly-js-calendar-refresh', $footer).on('click', function () {
            calendar.ec.refetchEvents();
        });

        refreshBooklyCalendar();
    }
})(jQuery);