(function (wp) {
    var el = wp.element.createElement,
        components        = wp.components,
        blockControls     = wp.editor.BlockControls,
        inspectorControls = wp.editor.InspectorControls
    ;

    wp.blocks.registerBlockType('bookly/staff-cabinet-details', {
        title: BooklyStaffCabinetL10n.blocks.details.title,
        description: BooklyStaffCabinetL10n.blocks.details.description,
        icon: el('svg', { width: '20', height: '20', viewBox: "0 0 64 64" },
            el('path', {style: {fill: "rgb(0, 0, 0)"}, d: "M 8 0 H 56 A 8 8 0 0 1 64 8 V 22 H 0 V 8 A 8 8 0 0 1 8 0 Z"}),
            el('path', {style: {fill: "rgb(244, 102, 47)"}, d: "M 0 22 H 64 V 56 A 8 8 0 0 1 56 64 H 8 A 8 8 0 0 1 0 56 V 22 Z"}),
            el('rect', {style: {fill: "rgb(98, 86, 86)"}, x: 6, y: 6, width: 52, height: 10}),
            el('rect', {style: {fill: "rgb(242, 227, 227)"}, x: 12, y: 30, width: 40, height: 24}),
            el('path', {style: {fill: "rgb(199, 104, 56)", stroke: 'rgb(0, 0, 0)'}, d: "M 46.423 35.343 L 59.309 44.705 L 54.387 59.853 L 38.459 59.853 L 33.537 44.705 Z"}),
        ),
        category: 'bookly-blocks',
        keywords: [
            'bookly',
            'staff',
            'details',
        ],
        supports: {
            customClassName: false,
            html: false
        },
        attributes: {
            short_code: {
                type: 'string',
                default: '[bookly-staff-details]'
            },
            staff_visibility: {
                type: 'boolean',
                default: false
            }
        },
        edit: function (props) {
            var inspectorElements = [],
                attributes   = props.attributes
            ;

            function getShortCode(props, attributes) {
                var short_code = '[bookly-staff-details';

                if (attributes.staff_visibility) {
                    short_code += ' hide="visibility"';
                }
                short_code += ']';

                props.setAttributes({short_code: short_code});

                return short_code;
            }

            inspectorElements.push(el(components.PanelRow,
                {},
                el('label', {htmlFor: 'bookly-js-hide-visibility'}, BooklyStaffCabinetL10n.hideVisibilityField),
                el(components.FormToggle, {
                    id: 'bookly-js-hide-visibility',
                    checked: attributes.staff_visibility,
                    onChange: function () {
                        return props.setAttributes({staff_visibility: !props.attributes.staff_visibility});
                    },
                })
            ));

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