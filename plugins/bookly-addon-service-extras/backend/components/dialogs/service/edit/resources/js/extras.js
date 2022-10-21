jQuery(function ($) {
    $(document.body).on('service.initForm', {},
        // Bind an event handler to the components for service panel.
        function (event, $panel) {
            let $container = $('#bookly-services-extras', $panel),
                $modalContent = $panel.closest('.modal-content'),
                $serviceError = $('.bookly-js-service-error', $modalContent),
                $saveButton = $('#bookly-save', $modalContent);

            Sortable.create($('.extras-container', $container)[0], {
                handle: '.bookly-js-draghandle',
                onEnd : function () {
                    let positions = [];
                    $('.extras-container .extra', $container).each(function () {
                        positions.push($(this).data('extra-id'));
                    });
                    $.ajax({
                        type: 'POST',
                        url : ajaxurl,
                        data: {action: 'bookly_service_extras_update_extra_position', positions: positions, csrf_token: BooklyL10nGlobal.csrf_token}
                    });
                }
            });

            $container.off().on('click', '.bookly-js-add-extras', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var children = $('.extras-container li.new', $container);

                var id = 1;
                children.each(function (i, el) {
                    var elId = parseInt($(el).data('extra-id'));
                    id = (elId >= id) ? elId + 1 : id;
                });
                var template = $('.bookly-js-templates.extras').html();
                var $extras_container = $container.find('.extras-container');
                id += '-new';
                $extras_container.append(
                    template.replace(/%id%/g, id)
                );

                var substringMatcher = function (strs) {
                    return function findMatches(q, cb) {
                        var matches, substringRegex;

                        // an array that will be populated with substring matches
                        matches = [];

                        // regex used to determine if a string contains the substring `q`
                        substrRegex = new RegExp(q, 'i');

                        // iterate through the pool of strings and for any string that
                        // contains the substring `q`, add it to the `matches` array
                        $.each(strs, function (i, str) {
                            if (substrRegex.test(str.title_with_service)) {
                                matches.push(str);
                            }
                        });

                        cb(matches);
                    };
                };

                $('#title_extras_' + id).typeahead({
                        hint     : false,
                        highlight: true,
                        minLength: 0
                    },
                    {
                        name     : 'extras',
                        display  : 'title',
                        source   : substringMatcher(BooklyExtrasL10n.list),
                        templates: {
                            suggestion: function (data) {
                                return '<div>' + data.title_with_service + '</div>';
                            }
                        }
                    })
                    .bind('typeahead:select', function (ev, suggestion) {
                        let $extras = $(this).closest('.extra');
                        id = $extras.attr('data-extra-id');
                        $extras.find('#title_extras_' + id).val(suggestion.title);
                        $extras.find('#price_extras_' + id).val(suggestion.price);
                        $extras.find('#min_quantity_extras_' + id).val(suggestion.min_quantity);
                        $extras.find('#max_quantity_extras_' + id).val(suggestion.max_quantity);
                        $extras.find('#duration_extras_' + id).val(suggestion.duration);
                        if (suggestion.image != false) {
                            $extras.find("[name='extras[" + id + "][attachment_id]']").val(suggestion.attachment_id);
                            $extras.find('.bookly-thumb').css({'background-image': 'url(' + suggestion.image[0] + ')', 'background-size': 'cover'});
                            $extras.find('.bookly-js-remove-attachment').show();
                        }
                    });
                $('#title_extras_' + id).focus();
            }).on('click', '.bookly-thumb label', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var extra = $(this).parents('.extra');
                var frame = wp.media({
                    library : {type: 'image'},
                    multiple: false
                });
                frame.on('select', function () {
                    var selection = frame.state().get('selection').toJSON(),
                        img_src
                    ;
                    if (selection.length) {
                        if (selection[0].sizes['thumbnail'] !== undefined) {
                            img_src = selection[0].sizes['thumbnail'].url;
                        } else {
                            img_src = selection[0].url;
                        }
                        extra.find("[name='extras[" + extra.data('extra-id') + "][attachment_id]']").val(selection[0].id);
                        extra.find('.bookly-thumb').css({'background-image': 'url(' + img_src + ')', 'background-size': 'cover'});
                        extra.find('.bookly-js-remove-attachment').show();
                        extra.find('.bookly-thumb').addClass('bookly-thumb-with-image');
                        $(this).hide();
                    }
                });

                frame.open();
            }).on('click', '.bookly-js-remove-attachment', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).hide();
                var extra = $(this).parents('.extra');
                extra.find("[name='extras[" + extra.data('extra-id') + "][attachment_id]']").attr('value', '');
                extra.find('.bookly-thumb').attr('style', '');
                extra.find('.bookly-thumb').removeClass('bookly-thumb-with-image');
                extra.find('label').show();
            }).on('click', '.extra-delete', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm(BooklyL10n.are_you_sure)) {
                    $(this).parents('.extra').remove();
                    let $control = $('.bookly-js-extras-quantity:first',$container);
                    if ($control.length) {
                        $control.trigger('change');
                    } else {
                        validateQuantity();
                    }
                }
            }).on('click', 'button.bookly-js-reset', function (e) {
                $container.find('form').trigger('reset');
            }).on('click', 'button.bookly-js-save-extras', function (e) {
                e.preventDefault();
                var data  = $(this).closest('form').serializeArray(),
                    ladda = Ladda.create(this);
                ladda.start();

                data.push({name: 'action', value: 'bookly_service_extras_update_extras'});
                $.ajax({
                    type       : 'POST',
                    url        : ajaxurl,
                    data       : data,
                    dataType   : 'json',
                    xhrFields  : {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success    : function (response) {
                        ladda.stop();
                        if (response.data.new_extras_list) {
                            BooklyExtrasL10n.list = response.data.new_extras_list
                        }
                        $.each(response.data.new_extras_ids, function (front_id, real_id) {
                            var $li = $('li.extra.new[data-extra-id="' + front_id + '"]', $container);
                            $('[name^="extras"]', $li).each(function () {
                                $(this).attr('name', $(this).attr('name').replace('[' + front_id + ']', '[' + real_id + ']'));
                            });
                            $('[id*="_extras_"]', $li).each(function () {
                                $(this).attr('id', $(this).attr('id').replace(front_id, real_id));
                            });
                            $('label[for*="_extras_"]', $li).each(function () {
                                $(this).attr('for', $(this).attr('for').replace(front_id, real_id));
                            });
                            $li.data('extra-id', real_id).removeClass('new');
                            $li.append('<input type="hidden" value="' + real_id + '" name="extras[' + real_id + '][id]">');
                        });
                        booklyAlert(response.data.alert);
                    }
                });
            })
            .on('change', '.bookly-js-extras-quantity', function (e) {
                validateQuantity($(this).closest('.form-row'));
            });

            function validateQuantity($wrapper) {
                if (parseInt($('[name$="[min_quantity]"]', $wrapper).val()) > parseInt($('[name$="[max_quantity]"]', $wrapper).val())) {
                    if ($('.bookly-js-extras-quantity-error', $serviceError).length == 0) {
                        $serviceError.append('<div class="bookly-js-extras-quantity-error bookly-js-error">' + BooklyExtrasL10n.quantity_error + '</div>');
                    }
                    $('.bookly-js-extras-quantity', $wrapper).addClass('is-invalid');
                } else {
                    $('.bookly-js-extras-quantity', $wrapper).removeClass('is-invalid');
                    if ($('.bookly-js-extras-quantity.is-invalid', $container).length == 0) {
                        $('.bookly-js-extras-quantity-error', $serviceError).remove();
                    }
                }
                $saveButton.prop('disabled', $('.bookly-js-error', $serviceError).length > 0);
            }
        }
    );
});