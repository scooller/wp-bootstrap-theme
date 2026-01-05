/**
 * Bootstrap Dropdown Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, InnerBlocks, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl, TextControl, RangeControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-dropdown', {
        title: __('Bootstrap Dropdown', 'bootstrap-theme'),
        description: __('Bootstrap dropdown menu component', 'bootstrap-theme'),
        icon: 'arrow-down-alt2',
        category: 'bootstrap',
        keywords: [__('dropdown'), __('menu'), __('bootstrap')],
        
        attributes: {
            buttonText: {
                type: 'string',
                default: 'Dropdown button'
            },
            buttonVariant: {
                type: 'string',
                default: 'secondary'
            },
            size: {
                type: 'string',
                default: ''
            },
            split: {
                type: 'boolean',
                default: false
            },
            direction: {
                type: 'string',
                default: 'down'
            },
            alignment: {
                type: 'string',
                default: ''
            },
            menuEnd: {
                type: 'boolean',
                default: false
            },
            autoClose: {
                type: 'string',
                default: 'true'
            },
            dark: {
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
            
            // Inserter preview image
            if (attributes.preview) {
                return createElement('img', {
                    src: '/wp-content/themes/bootstrap-theme/blocks/bs-dropdown/example.png',
                    alt: __('Dropdown preview', 'bootstrap-theme'),
                    style: { width: '100%', height: 'auto', display: 'block' }
                });
            }
            
            const variantOptions = [
                { label: 'Primary', value: 'primary' },
                { label: 'Secondary', value: 'secondary' },
                { label: 'Success', value: 'success' },
                { label: 'Danger', value: 'danger' },
                { label: 'Warning', value: 'warning' },
                { label: 'Info', value: 'info' },
                { label: 'Light', value: 'light' },
                { label: 'Dark', value: 'dark' },
                { label: 'Outline Primary', value: 'outline-primary' },
                { label: 'Outline Secondary', value: 'outline-secondary' }
            ];

            const sizeOptions = [
                { label: 'Default', value: '' },
                { label: 'Small', value: 'btn-sm' },
                { label: 'Large', value: 'btn-lg' }
            ];

            const directionOptions = [
                { label: 'Down', value: 'down' },
                { label: 'Up', value: 'up' },
                { label: 'Start', value: 'start' },
                { label: 'End', value: 'end' }
            ];

            const alignmentOptions = [
                { label: 'Default', value: '' },
                { label: 'End', value: 'dropdown-menu-end' }
            ];

            const autoCloseOptions = [
                { label: 'True (Default)', value: 'true' },
                { label: 'Outside', value: 'outside' },
                { label: 'Inside', value: 'inside' },
                { label: 'False', value: 'false' }
            ];

            const dropdownClasses = [
                attributes.direction === 'down' ? 'dropdown' : `drop${attributes.direction}`,
                attributes.direction === 'down' && 'btn-group'
            ].filter(Boolean).join(' ');

            const buttonClasses = [
                'btn',
                `btn-${attributes.buttonVariant}`,
                attributes.size,
                attributes.split ? '' : 'dropdown-toggle'
            ].filter(Boolean).join(' ');

            const menuClasses = [
                'dropdown-menu',
                attributes.alignment,
                attributes.dark ? 'dropdown-menu-dark' : ''
            ].filter(Boolean).join(' ');
            
            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Dropdown Settings', 'bootstrap-theme') },
                        createElement(TextControl, {
                            label: __('Button Text', 'bootstrap-theme'),
                            value: attributes.buttonText,
                            onChange: (value) => setAttributes({ buttonText: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Button Variant', 'bootstrap-theme'),
                            value: attributes.buttonVariant,
                            options: variantOptions,
                            onChange: (value) => setAttributes({ buttonVariant: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Size', 'bootstrap-theme'),
                            value: attributes.size,
                            options: sizeOptions,
                            onChange: (value) => setAttributes({ size: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Direction', 'bootstrap-theme'),
                            value: attributes.direction,
                            options: directionOptions,
                            onChange: (value) => setAttributes({ direction: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Menu Alignment', 'bootstrap-theme'),
                            value: attributes.alignment,
                            options: alignmentOptions,
                            onChange: (value) => setAttributes({ alignment: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Split Button', 'bootstrap-theme'),
                            help: __('Separate button and dropdown toggle', 'bootstrap-theme'),
                            checked: attributes.split,
                            onChange: (value) => setAttributes({ split: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Dark Menu', 'bootstrap-theme'),
                            help: __('Use dark theme for dropdown menu', 'bootstrap-theme'),
                            checked: attributes.dark,
                            onChange: (value) => setAttributes({ dark: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Auto Close', 'bootstrap-theme'),
                            help: __('Configure when dropdown closes', 'bootstrap-theme'),
                            value: attributes.autoClose,
                            options: autoCloseOptions,
                            onChange: (value) => setAttributes({ autoClose: value })
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
                        className: `${dropdownClasses} ${blockProps.className || ''}`.trim()
                    }),
                    // Button(s)
                    attributes.split ? 
                        createElement(Fragment, {},
                            createElement('button', {
                                type: 'button',
                                className: buttonClasses
                            }, attributes.buttonText),
                            createElement('button', {
                                type: 'button',
                                className: `btn btn-${attributes.buttonVariant} ${attributes.size} dropdown-toggle dropdown-toggle-split`.trim(),
                                'data-bs-toggle': 'dropdown',
                                'aria-expanded': 'false'
                            },
                                createElement('span', { className: 'visually-hidden' }, __('Toggle Dropdown', 'bootstrap-theme'))
                            )
                        ) :
                        createElement('button', {
                            className: buttonClasses,
                            type: 'button',
                            'data-bs-toggle': 'dropdown',
                            'aria-expanded': 'false'
                        }, attributes.buttonText),
                    
                    // Dropdown Menu
                    createElement('div', { className: 'mt-2' },
                        createElement('div', { className: 'mb-2' },
                            createElement('small', { className: 'text-muted' },
                                __('Dropdown Menu Preview:', 'bootstrap-theme')
                            )
                        ),
                        createElement('ul', {
                            className: `${menuClasses} show position-static`,
                            style: { display: 'block' }
                        },
                            createElement(InnerBlocks, {
                                allowedBlocks: ['bootstrap-theme/bs-dropdown-item', 'bootstrap-theme/bs-dropdown-divider'],
                                template: [
                                    ['bootstrap-theme/bs-dropdown-item', { text: 'Action' }],
                                    ['bootstrap-theme/bs-dropdown-item', { text: 'Another action' }],
                                    ['bootstrap-theme/bs-dropdown-divider'],
                                    ['bootstrap-theme/bs-dropdown-item', { text: 'Something else here' }]
                                ],
                                placeholder: __('Add dropdown items...', 'bootstrap-theme')
                            })
                        )
                    )
                )
            );
        },

        save: function() {
            return createElement(InnerBlocks.Content);
        }
    });

})(window.wp);