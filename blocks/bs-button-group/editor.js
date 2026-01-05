/**
 * Bootstrap Button Group Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, InnerBlocks, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl, TextControl, RangeControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-button-group', {
        title: __('Bootstrap Button Group', 'bootstrap-theme'),
        description: __('Group multiple buttons together', 'bootstrap-theme'),
        icon: 'editor-justify',
        category: 'bootstrap',
        keywords: [__('button'), __('group'), __('toolbar')],
        
        attributes: {
            size: {
                type: 'string',
                default: ''
            },
            vertical: {
                type: 'boolean',
                default: false
            },
            toolbar: {
                type: 'boolean',
                default: false
            },
            ariaLabel: {
                type: 'string',
                default: 'Button group'
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

            // Show preview image if this is an example
            if (attributes.preview) {
                return createElement('div', {
                    className: 'bootstrap-button-group-preview',
                    style: { textAlign: 'center', padding: '20px' }
                },
                    createElement('img', {
                        src: '/wp-content/themes/bootstrap-theme/blocks/bs-button-group/example.png',
                        alt: __('Bootstrap Button Group Preview', 'bootstrap-theme'),
                        style: { width: '100%', height: 'auto', maxWidth: '600px' }
                    })
                );
            }

            const sizeOptions = [
                { label: 'Default', value: '' },
                { label: 'Small', value: 'btn-group-sm' },
                { label: 'Large', value: 'btn-group-lg' }
            ];

            const groupClasses = [
                attributes.toolbar ? 'btn-toolbar' : (attributes.vertical ? 'btn-group-vertical' : 'btn-group'),
                attributes.size && !attributes.toolbar ? attributes.size : ''
            ].filter(Boolean).join(' ');

            const roleAttribute = attributes.toolbar ? 'toolbar' : 'group';

            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Button Group Settings', 'bootstrap-theme') },
                        createElement(ToggleControl, {
                            label: __('Toolbar', 'bootstrap-theme'),
                            help: __('Combine multiple button groups into a toolbar', 'bootstrap-theme'),
                            checked: attributes.toolbar,
                            onChange: (value) => setAttributes({ toolbar: value })
                        }),
                        !attributes.toolbar && createElement(ToggleControl, {
                            label: __('Vertical', 'bootstrap-theme'),
                            help: __('Stack buttons vertically', 'bootstrap-theme'),
                            checked: attributes.vertical,
                            onChange: (value) => setAttributes({ vertical: value })
                        }),
                        !attributes.toolbar && createElement(SelectControl, {
                            label: __('Size', 'bootstrap-theme'),
                            value: attributes.size,
                            options: sizeOptions,
                            onChange: (value) => setAttributes({ size: value })
                        }),
                        createElement(TextControl, {
                            label: __('ARIA Label', 'bootstrap-theme'),
                            help: __('Accessibility label for the button group', 'bootstrap-theme'),
                            value: attributes.ariaLabel,
                            onChange: (value) => setAttributes({ ariaLabel: value })
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
                createElement('div', 
                    Object.assign({}, blockProps, { 
                        className: `${groupClasses} ${blockProps.className || ''}`.trim(),
                        role: roleAttribute,
                        'aria-label': attributes.ariaLabel
                    }),
                    createElement(InnerBlocks, {
                        allowedBlocks: attributes.toolbar ? 
                            ['bootstrap-theme/bs-button-group', 'bootstrap-theme/bs-button'] : 
                            ['bootstrap-theme/bs-button'],
                        template: attributes.toolbar ? [
                            ['bootstrap-theme/bs-button-group'],
                            ['bootstrap-theme/bs-button-group']
                        ] : [
                            ['bootstrap-theme/bs-button', { text: 'Left', variant: 'outline-primary' }],
                            ['bootstrap-theme/bs-button', { text: 'Middle', variant: 'outline-primary' }],
                            ['bootstrap-theme/bs-button', { text: 'Right', variant: 'outline-primary' }]
                        ],
                        placeholder: attributes.toolbar ? 
                            __('Add button groups...', 'bootstrap-theme') :
                            __('Add buttons to group...', 'bootstrap-theme')
                    })
                )
            );
        },

        save: function() {
            return createElement(InnerBlocks.Content);
        }
    });

})(window.wp);