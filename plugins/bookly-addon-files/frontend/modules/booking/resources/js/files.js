jQuery(function($) {
    $('body')
        // Bind actions when rendered step details.
        .on('bookly.render.step_detail', {}, function (event, $container) {
            var ladda;
            $('.bookly-js-file', $container).trigger('change');
            $('.bookly-js-upload', $container)
                .on('click', function () {
                    $(this).siblings('.bookly-js-file-upload').trigger('click');
                    ladda = Ladda.create(this);
                });
            $('.bookly-js-file-upload', $container).fileupload({
                url: BooklyFilesL10n.ajaxurl,
                dataType: 'json',
                done: function (e, data) {
                    let $errors = $(this).closest('.bookly-custom-field-row').find('.bookly-custom-field-error');
                    if (data.result.success) {
                        $errors.html('');
                        var $div = $(e.target).closest('div.bookly-row');
                        $('.bookly-js-file', $div).val(data.result.data.slug)
                            .trigger('change');
                        $('[data-action=drop]', $div).attr('data-file', 'new');
                        $('span.bookly-js-file-name', $div).html(data.result.data.name);
                    } else {
                        $errors.html(data.result.data.error);
                    }
                    if (typeof ladda != 'undefined') {
                        ladda.stop();
                    }
                }
            }).bind('fileuploadsubmit', function (e, data) {
                data.formData = {
                    action: 'bookly_files_upload',
                    custom_field_id: $(e.delegateTarget).closest('div.bookly-custom-field-row').attr('data-id'),
                    csrf_token: BooklyL10n.csrf_token
                };
                if (typeof ladda != 'undefined') {
                    ladda.start();
                }
            }).prop('disabled', !$.support.fileInput);

            var $back_button = $container.closest('.bookly-form').find('.bookly-js-back-step');
            $back_button.on('click', function () {
                // Remove all new uploaded files.
                $('button[data-file=new]', $container).trigger('click');
            });
        })
        // Hide Browse button when uploaded file and show remove button,
        // and inverse when file removed
        .on('change','.bookly-form .bookly-js-file', function () {
            var $container = $(this).closest('div.bookly-row'),
                slug = $('.bookly-js-file', $container).val();
            $('.bookly-js-file-menu', $container).toggle(slug != '');
            $('.bookly-js-upload', $container).toggle(slug == '');
            if (slug == '') {
                $('span.bookly-js-file-name', $container).html('');
            }
        })
        // Removing uploaded file.
        .on('click', '.bookly-form .bookly-js-file-menu [data-action=drop]', function (e) {
            var $container = $(this).closest('div.bookly-row'),
                $file = $('.bookly-js-file', $container),
                slug  = $file.val();
            if (slug != '') {
                if (typeof e.isTrigger == 'undefined') {
                    var ladda = Ladda.create(this);
                    ladda.start();
                }
                var form_id = $container.closest('.bookly-form').data('form_id');
                $.ajax({
                    url: BooklyFilesL10n.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_files_delete',
                        form_id: form_id,
                        slug: slug,
                        csrf_token: BooklyL10n.csrf_token
                    },
                    dataType: 'json',
                    success: function () {
                        if (typeof e.isTrigger == 'undefined') {
                            $file.val('').trigger('change');
                            ladda.stop();
                        }
                    }
                });
            }
        });
});