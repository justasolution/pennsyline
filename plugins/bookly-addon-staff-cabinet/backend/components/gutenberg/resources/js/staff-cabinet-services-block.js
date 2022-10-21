(function (wp) {
    var el = wp.element.createElement,
        components        = wp.components,
        blockControls     = wp.editor.BlockControls,
        inspectorControls = wp.editor.InspectorControls
    ;

    wp.blocks.registerBlockType('bookly/staff-cabinet-services', {
        title: BooklyStaffCabinetL10n.blocks.services.title,
        description: BooklyStaffCabinetL10n.blocks.services.description,
        icon: el('svg', { width: '20', height: '20', viewBox: "0 0 64 64" },
            el('path', {style: {fill: "rgb(0, 0, 0)"}, d: "M 8 0 H 56 A 8 8 0 0 1 64 8 V 22 H 0 V 8 A 8 8 0 0 1 8 0 Z"}),
            el('path', {style: {fill: "rgb(244, 102, 47)"}, d: "M 0 22 H 64 V 56 A 8 8 0 0 1 56 64 H 8 A 8 8 0 0 1 0 56 V 22 Z"}),
            el('rect', {style: {fill: "rgb(98, 86, 86)"}, x: 6, y: 6, width: 52, height: 10}),
            el('rect', {style: {fill: "rgb(242, 227, 227)"}, x: 12, y: 30, width: 40, height: 24}),
            el('path', {style: {fill: "rgb(255, 255, 125)", stroke: 'rgb(0, 0, 0)'}, d: "M 46.423 35.343 L 59.309 44.705 L 54.387 59.853 L 38.459 59.853 L 33.537 44.705 Z"}),
        ),
        category: 'bookly-blocks',
        keywords: [
            'bookly',
            'staff',
            'services',
        ],
        supports: {
            customClassName: false,
            html: false
        },
        attributes: {
            short_code: {
                type: 'string',
                default: '[bookly-staff-services]'
            },
            readonly_services: {
                type: 'boolean',
                default: false
            },
            readonly_price: {
                type: 'boolean',
                default: false
            },
            readonly_capacity: {
                type: 'boolean',
                default: false
            },
            readonly_deposit: {
                type: 'boolean',
                default: false
            },
            readonly_special_hours: {
                type: 'boolean',
                default: false
            }
        },
        edit: function (props) {
            var inspectorElements = [],
                attributes   = props.attributes
            ;

            function getShortCode(props, attributes) {
                var short_code = '[bookly-staff-services',
                    readOnly = [];

                if (attributes.readonly_services) {
                    readOnly.push('services');
                }
                if (attributes.readonly_price) {
                    readOnly.push('price');
                }
                if (BooklyStaffCabinetL10n.addons.groupBooking) {
                    if (attributes.readonly_capacity) {
                        readOnly.push('capacity');
                    }
                }
                if (BooklyStaffCabinetL10n.addons.depositPayments) {
                    if (attributes.readonly_deposit) {
                        readOnly.push('deposit');
                    }
                }
                if (BooklyStaffCabinetL10n.addons.specialHours) {
                    if (attributes.readonly_special_hours) {
                        readOnly.push('special-hours');
                    }
                }
                if (readOnly.length > 0) {
                    short_code += ' read-only="' + readOnly.join() + '"';
                }

                short_code += ']';

                props.setAttributes({short_code: short_code});

                return short_code;
            }
            // Add row field     read only
            inspectorElements.push(el(components.PanelRow,
                {},
                el('b', {}, BooklyStaffCabinetL10n.fields),
                el('span', {}, BooklyStaffCabinetL10n.readOnly),
            ));

            inspectorElements.push(el(components.PanelRow,
                {},
                el('label', {htmlFor: 'bookly-js-read-only-services'}, BooklyStaffCabinetL10n.services),
                el(components.FormToggle, {
                    id: 'bookly-js-read-only-services',
                    checked: attributes.readonly_services,
                    onChange: function () {
                        return props.setAttributes({readonly_services: !props.attributes.readonly_services});
                    },
                })
            ));
            inspectorElements.push(el(components.PanelRow,
                {},
                el('label', {htmlFor: 'bookly-js-read-only-price'}, BooklyStaffCabinetL10n.price),
                el(components.FormToggle, {
                    id: 'bookly-js-read-only-price',
                    checked: attributes.readonly_price,
                    onChange: function () {
                        return props.setAttributes({readonly_price: !props.attributes.readonly_price});
                    },
                })
            ));

            if (BooklyStaffCabinetL10n.addons.groupBooking) {
                inspectorElements.push(el(components.PanelRow,
                    {},
                    el('label', {htmlFor: 'bookly-js-read-only-capacity'}, BooklyStaffCabinetL10n.capacity),
                    el(components.FormToggle, {
                        id: 'bookly-js-read-only-capacity',
                        checked: attributes.readonly_capacity,
                        onChange: function () {
                            return props.setAttributes({readonly_capacity: !props.attributes.readonly_capacity});
                        },
                    })
                ));
            }
            if (BooklyStaffCabinetL10n.addons.depositPayments) {
                inspectorElements.push(el(components.PanelRow,
                    {},
                    el('label', {htmlFor: 'bookly-js-read-only-deposit'}, BooklyStaffCabinetL10n.deposit),
                    el(components.FormToggle, {
                        id: 'bookly-js-read-only-deposit',
                        checked: attributes.readonly_deposit,
                        onChange: function () {
                            return props.setAttributes({readonly_deposit: !props.attributes.readonly_deposit});
                        },
                    })
                ));
            }
            if (BooklyStaffCabinetL10n.addons.specialHours) {
                inspectorElements.push(el(components.PanelRow,
                    {},
                    el('label', {htmlFor: 'bookly-js-read-only-special-hours'}, BooklyStaffCabinetL10n.specialHours),
                    el(components.FormToggle, {
                        id: 'bookly-js-read-only-special-hours',
                        checked: attributes.readonly_special_hours,
                        onChange: function () {
                            return props.setAttributes({readonly_special_hours: !props.attributes.readonly_special_hours});
                        },
                    })
                ));
            }

            return [
                el(blockControls, {key: 'controls'}),
                el(inspectorControls, {key: 'inspector'},
                    el(components.PanelBody, {initialOpen: true},
                        inspectorElements
                    )
                ),
                el('div', {},
                    getShortCode(props, props.attributes)
                )
            ]
        },

        save: function (props) {
            return (
                el('div', {},
                    props.attributes.short_code
                )
            )
        }
    })
})(
    window.wp
);