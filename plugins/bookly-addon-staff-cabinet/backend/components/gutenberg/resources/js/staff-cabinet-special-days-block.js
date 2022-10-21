(function (wp) {
    var el = wp.element.createElement;

    wp.blocks.registerBlockType('bookly/staff-cabinet-spacial-days', {
        title: BooklyStaffCabinetL10n.blocks.specialDays.title,
        description: BooklyStaffCabinetL10n.blocks.specialDays.description,
        icon: el('svg', { width: '20', height: '20', viewBox: "0 0 64 64" },
            el('path', {style: {fill: "rgb(0, 0, 0)"}, d: "M 8 0 H 56 A 8 8 0 0 1 64 8 V 22 H 0 V 8 A 8 8 0 0 1 8 0 Z"}),
            el('path', {style: {fill: "rgb(244, 102, 47)"}, d: "M 0 22 H 64 V 56 A 8 8 0 0 1 56 64 H 8 A 8 8 0 0 1 0 56 V 22 Z"}),
            el('rect', {style: {fill: "rgb(98, 86, 86)"}, x: 6, y: 6, width: 52, height: 10}),
            el('rect', {style: {fill: "rgb(242, 227, 227)"}, x: 12, y: 30, width: 40, height: 24}),
            el('path', {style: {fill: "rgb(175, 152, 114)", stroke: 'rgb(0, 0, 0)'}, d: "M 46.423 35.343 L 59.309 44.705 L 54.387 59.853 L 38.459 59.853 L 33.537 44.705 Z"}),
        ),
        category: 'bookly-blocks',
        keywords: [
            'bookly',
            'staff',
            'special',
        ],
        supports: {
            customClassName: false,
            html: false
        },
        attributes: {},
        edit: function (props) {
            return [
                el('div', {},
                    '[bookly-staff-special-days]'
                )
            ]
        },

        save: function (props) {
            return (
                el('div', {},
                    '[bookly-staff-special-days]'
                )
            )
        }
    })
})(
    window.wp
);