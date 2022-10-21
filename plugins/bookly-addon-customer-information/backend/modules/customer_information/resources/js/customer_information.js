jQuery(function($) {

    var $fields = $("#bookly-customer-information-fields");

    Sortable.create($fields[0], {
        handle : '.bookly-js-draghandle.bookly-js-reorder-cf'
    });

    /**
     * Build initial fields.
     */
    restoreFields();

    /**
     * On "Add new field" button click.
     */
    $('#bookly-js-add-fields').on('click', 'button', function() {
        addField($(this).data('type'));
    });

    /**
     * On "Add new item" button click.
     */
    $fields.on('click', 'button', function() {
        addItem($(this).prev('ul'), $(this).data('type'));
    });

    /**
     * Delete field or checkbox/radio button/drop-down option.
     */
    $fields.on('click', '.bookly-js-delete', function(e) {
        e.preventDefault();
        $(this).closest('li').fadeOut('fast', function() { $(this).remove(); });
    });

    /**
     * Submit fields form.
     */
    $('#ajax-send-fields').on('click', function(e) {
        e.preventDefault();
        var ladda = Ladda.create(this),
            data = [];
        ladda.start();
        $fields.children('li').each(function() {
            var $this = $(this),
                field = {};
            switch ($this.data('type')) {
                case 'checkboxes':
                case 'radio-buttons':
                case 'drop-down':
                    field.items = [];
                    $this.find('ul.bookly-js-items li').each(function() {
                        field.items.push($(this).find('input').val());
                    });
                case 'textarea':
                case 'text-field':
                case 'text-content':
                    field.type     = $this.data('type');
                    field.label    = $this.find('.bookly-js-label').val();
                    field.required = $this.find('.bookly-js-required').prop('checked');
                    field.ask_once = $this.find('.bookly-js-ask-once').prop('checked');
                    field.id       = $this.data('bookly-field-id');
                case 'file':
                    field.type     = $this.data('type');
                    field.label    = $this.find('.bookly-js-label').val();
                    field.required = $this.find('.bookly-js-required').prop('checked');
                    field.ask_once = $this.find('.bookly-js-ask-once').prop('checked');
                    field.id       = $this.data('bookly-field-id');
//                      custom_fields_data.push({
//                        id: $this.data('id'),
//                        value: find($this).call($this, 'input.bookly-custom-field').val()
//                      });

            }
            data.push(field);
        });
        $.ajax({
            type      : 'POST',
            url       : ajaxurl,
            xhrFields : { withCredentials: true },
            data      : {
                action: 'bookly_customer_information_save_fields',
                csrf_token: BooklyCustomerInformationL10n.csrfToken,
                fields: JSON.stringify(data)
            },
            complete  : function() {
                ladda.stop();
                booklyAlert({success : [BooklyCustomerInformationL10n.saved]});
            }
        });
    });

    /**
     * On 'Reset' click.
     */
    $('button[type=reset]').on('click', function() {
        $fields.empty();
        restoreFields();
    });

    /**
     * Add new field.
     *
     * @param type
     * @param id
     * @param label
     * @param required
     * @param ask_once
     * @returns {*|jQuery}
     */
    function addField(type, id, label, required, ask_once) {
        let $new_field = $('ul#bookly-templates > li[data-type=' + type + ']').clone();
        // Set id, label and required.
        if (typeof id === 'undefined') {
            id = Math.floor((Math.random() * 100000) + 1);
        }
        if (typeof label === 'undefined') {
            label = '';
        }
        if (typeof required === 'undefined') {
            required = false;
        }
        if (typeof ask_once === 'undefined') {
            ask_once = false;
        }
        $new_field
            .hide()
            .data('bookly-field-id', id)
            .find('.bookly-js-required').prop({
            id: 'required-' + id,
            checked: required
        })
            .next('label').attr('for', 'required-' + id).end()
            .end()
            .find('.bookly-js-replace-code').text(' - {info_field#' + id + '}').end()
            .find('.bookly-js-ask-once').prop({
                id: 'ask-once-' + id,
                checked: ask_once
            })
            .next('label').attr('for', 'ask-once-' + id).end()
            .end()
            .find('.bookly-js-label').val(label);

        // Add new field to the list.
        $fields.append($new_field);
        $new_field.fadeIn('fast');
        let $items = $new_field.find('ul.bookly-js-items');
        if ($items.length > 0 ) {
            Sortable.create($items[0], {
                handle: '.bookly-js-draghandle.bookly-js-reorder-cf-item'
            });
        }
        // Set focus to label field.
        $new_field.find('.bookly-js-label').focus();

        return $new_field;
    }

    /**
     * Add new checkbox/radio button/drop-down option.
     *
     * @param $ul
     * @param type
     * @param value
     * @return {*|jQuery}
     */
    function addItem($ul, type, value) {
        var $new_item = $('ul#bookly-templates > li[data-type=' + type + ']').clone();
        if (typeof value !== 'undefined') {
            $new_item.find('input').val(value);
        }
        $new_item.hide().appendTo($ul).fadeIn('fast').find('input').focus();

        return $new_item;
    }

    /**
     * Restore fields from BooklyCustomerInformationL10n.custom_fields.
     */
    function restoreFields() {
        $.each(BooklyCustomerInformationL10n.fields, function (i, field) {
            var $new_field = addField(field.type, field.id, field.label, field.required, field.ask_once);
            // add children
            if (field.items) {
                $.each(field.items, function (i, value) {
                    addItem($new_field.find('ul.bookly-js-items'), field.type + '-item', value);
                });
            }
        });

        $(':focus').blur();
    }
});