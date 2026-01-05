/**
 * Bootstrap List Group Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, InnerBlocks, useBlockProps } = wp.blockEditor;
    const { PanelBody, ToggleControl, SelectControl, RangeControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-list-group', {
        title: __('Bootstrap List Group', 'bootstrap-theme'),
        description: __('Bootstrap list group component', 'bootstrap-theme'),
        icon: 'editor-ul',
        category: 'bootstrap',
        keywords: [__('list'), __('group'), __('bootstrap')],
        
        attributes: {
            flush: {
                type: 'boolean',
                default: false
            },
            horizontal: {
                type: 'string',
                default: ''
            },
            numbered: {
                type: 'boolean',
                default: false
            },
            preview: {
                type: 'boolean',
                default: false
            },
            aosAnimation: {
                type: 'string',
                default: ''
            },
            aosDelay: {
                type: 'number',
                default: 0
            },
            aosDuration: {
                type: 'number',
                default: 800
            },
            aosEasing: {
                type: 'string',
                default: 'ease-in-out-cubic'
            },
            aosOnce: {
                type: 'boolean',
                default: false
            },
            aosMirror: {
                type: 'boolean',
                default: true
            },
            aosAnchorPlacement: {
                type: 'string',
                default: 'top-bottom'
            }
        },
        example: {
            attributes: {
                preview: true
            }
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const blockProps = useBlockProps();
            if (attributes.preview) {
                return createElement('img', {
                    src: '/wp-content/themes/bootstrap-theme/blocks/bs-list-group/example.png',
                    alt: 'Preview',
                    style: { width: '100%', height: 'auto', display: 'block' }
                });
            }
            const horizontalOptions = [
                { label: 'Vertical', value: '' },
                { label: 'Always Horizontal', value: 'horizontal' },
                { label: 'Horizontal SM+', value: 'horizontal-sm' },
                { label: 'Horizontal MD+', value: 'horizontal-md' },
                { label: 'Horizontal LG+', value: 'horizontal-lg' },
                { label: 'Horizontal XL+', value: 'horizontal-xl' },
                { label: 'Horizontal XXL+', value: 'horizontal-xxl' }
            ];

            const listClasses = [
                'list-group',
                attributes.flush ? 'list-group-flush' : '',
                attributes.horizontal ? `list-group-${attributes.horizontal}` : '',
                attributes.numbered ? 'list-group-numbered' : ''
            ].filter(Boolean).join(' ');

            const tagName = attributes.numbered ? 'ol' : 'ul';
            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('List Group Settings', 'bootstrap-theme') },
                        createElement(ToggleControl, {
                            label: __('Flush', 'bootstrap-theme'),
                            help: __('Remove borders and rounded corners', 'bootstrap-theme'),
                            checked: attributes.flush,
                            onChange: (value) => setAttributes({ flush: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Layout', 'bootstrap-theme'),
                            value: attributes.horizontal,
                            options: horizontalOptions,
                            onChange: (value) => setAttributes({ horizontal: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Numbered', 'bootstrap-theme'),
                            help: __('Add automatic numbering to list items', 'bootstrap-theme'),
                            checked: attributes.numbered,
                            onChange: (value) => setAttributes({ numbered: value })
                        })
                    ),
                    createElement(PanelBody, { title: __('AOS Animation', 'bootstrap-theme'), initialOpen: false },
                        createElement(SelectControl, {
                            label: __('Animation Type', 'bootstrap-theme'),
                            value: attributes.aosAnimation,
                            options: [
                                { label: __('None', 'bootstrap-theme'), value: '' },
                                { label: 'fade-up', value: 'fade-up' },
                                { label: 'fade-down', value: 'fade-down' },
                                { label: 'fade-left', value: 'fade-left' },
                                { label: 'fade-right', value: 'fade-right' },
                                { label: 'flip-up', value: 'flip-up' },
                                { label: 'flip-down', value: 'flip-down' },
                                { label: 'flip-left', value: 'flip-left' },
                                { label: 'flip-right', value: 'flip-right' },
                                { label: 'zoom-in', value: 'zoom-in' },
                                { label: 'zoom-out', value: 'zoom-out' },
                                { label: 'slide-up', value: 'slide-up' },
                                { label: 'slide-down', value: 'slide-down' },
                                { label: 'bounce-in', value: 'bounce-in' }
                            ],
                            onChange: (value) => setAttributes({ aosAnimation: value })
                        }),
                        attributes.aosAnimation && createElement(Fragment, {},
                            createElement(RangeControl, {
                                label: __('Delay (ms)', 'bootstrap-theme'),
                                value: attributes.aosDelay,
                                onChange: (value) => setAttributes({ aosDelay: value }),
                                min: 0,
                                max: 3000,
                                step: 100
                            }),
                            createElement(RangeControl, {
                                label: __('Duration (ms)', 'bootstrap-theme'),
                                value: attributes.aosDuration,
                                onChange: (value) => setAttributes({ aosDuration: value }),
                                min: 100,
                                max: 3000,
                                step: 100
                            }),
                            createElement(SelectControl, {
                                label: __('Easing', 'bootstrap-theme'),
                                value: attributes.aosEasing,
                                options: [
                                    { label: 'linear', value: 'linear' },
                                    { label: 'ease-in-quad', value: 'ease-in-quad' },
                                    { label: 'ease-out-quad', value: 'ease-out-quad' },
                                    { label: 'ease-in-out-quad', value: 'ease-in-out-quad' },
                                    { label: 'ease-in-cubic', value: 'ease-in-cubic' },
                                    { label: 'ease-out-cubic', value: 'ease-out-cubic' },
                                    { label: 'ease-in-out-cubic', value: 'ease-in-out-cubic' },
                                    { label: 'ease-in-quart', value: 'ease-in-quart' },
                                    { label: 'ease-out-quart', value: 'ease-out-quart' },
                                    { label: 'ease-in-out-quart', value: 'ease-in-out-quart' }
                                ],
                                onChange: (value) => setAttributes({ aosEasing: value })
                            }),
                            createElement(ToggleControl, {
                                label: __('Animate Once', 'bootstrap-theme'),
                                help: __('Only animate once when element is first scrolled into view', 'bootstrap-theme'),
                                checked: attributes.aosOnce,
                                onChange: (value) => setAttributes({ aosOnce: value })
                            }),
                            createElement(ToggleControl, {
                                label: __('Mirror Animation', 'bootstrap-theme'),
                                help: __('Repeat animation when scrolling up', 'bootstrap-theme'),
                                checked: attributes.aosMirror,
                                onChange: (value) => setAttributes({ aosMirror: value })
                            }),
                            createElement(SelectControl, {
                                label: __('Anchor Placement', 'bootstrap-theme'),
                                value: attributes.aosAnchorPlacement,
                                options: [
                                    { label: 'top-bottom', value: 'top-bottom' },
                                    { label: 'top-center', value: 'top-center' },
                                    { label: 'top-top', value: 'top-top' },
                                    { label: 'center-bottom', value: 'center-bottom' },
                                    { label: 'center-center', value: 'center-center' },
                                    { label: 'center-top', value: 'center-top' },
                                    { label: 'bottom-bottom', value: 'bottom-bottom' },
                                    { label: 'bottom-center', value: 'bottom-center' },
                                    { label: 'bottom-top', value: 'bottom-top' }
                                ],
                                onChange: (value) => setAttributes({ aosAnchorPlacement: value })
                            })
                        )
                    )
                ),
                createElement(tagName, 
                    Object.assign({}, blockProps, { 
                        className: `${listClasses} ${blockProps.className || ''}`.trim()
                    }),
                    createElement(InnerBlocks, {
                        allowedBlocks: ['bootstrap-theme/bs-list-group-item'],
                        template: [
                            ['bootstrap-theme/bs-list-group-item', { text: 'First item' }],
                            ['bootstrap-theme/bs-list-group-item', { text: 'Second item' }],
                            ['bootstrap-theme/bs-list-group-item', { text: 'Third item' }]
                        ],
                        placeholder: __('Add list group items...', 'bootstrap-theme')
                    })
                )
            );
        },

        save: function() {
            return createElement(InnerBlocks.Content);
        }
    });

})(window.wp);