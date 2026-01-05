/**
 * Bootstrap Row Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, InnerBlocks, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl, RangeControl, ToggleControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-row', {
        title: __('Bootstrap Row', 'bootstrap-theme'),
        description: __('Bootstrap row for grid layout', 'bootstrap-theme'),
        icon: 'editor-table',
        category: 'bootstrap',
        keywords: [__('row'), __('bootstrap'), __('grid')],
        
        attributes: {
            gutters: {
                type: 'string',
                default: ''
            },
            justifyContent: {
                type: 'string',
                default: ''
            },
            alignItems: {
                type: 'string',
                default: ''
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
                    src: '/wp-content/themes/bootstrap-theme/blocks/bs-row/example.png',
                    alt: 'Preview',
                    style: { width: '100%', height: 'auto', display: 'block' }
                });
            }
            const gutterOptions = [
                { label: 'Default', value: '' },
                { label: 'No Gutters', value: 'g-0' },
                { label: 'Small Gutters', value: 'g-1' },
                { label: 'Medium Gutters', value: 'g-3' },
                { label: 'Large Gutters', value: 'g-5' }
            ];

            const justifyOptions = [
                { label: 'Default', value: '' },
                { label: 'Start', value: 'justify-content-start' },
                { label: 'Center', value: 'justify-content-center' },
                { label: 'End', value: 'justify-content-end' },
                { label: 'Around', value: 'justify-content-around' },
                { label: 'Between', value: 'justify-content-between' },
                { label: 'Evenly', value: 'justify-content-evenly' }
            ];

            const alignOptions = [
                { label: 'Default', value: '' },
                { label: 'Start', value: 'align-items-start' },
                { label: 'Center', value: 'align-items-center' },
                { label: 'End', value: 'align-items-end' },
                { label: 'Baseline', value: 'align-items-baseline' },
                { label: 'Stretch', value: 'align-items-stretch' }
            ];
            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Row Settings', 'bootstrap-theme') },
                        createElement(SelectControl, {
                            label: __('Gutters', 'bootstrap-theme'),
                            value: attributes.gutters,
                            options: gutterOptions,
                            onChange: (value) => setAttributes({ gutters: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Justify Content', 'bootstrap-theme'),
                            value: attributes.justifyContent,
                            options: justifyOptions,
                            onChange: (value) => setAttributes({ justifyContent: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Align Items', 'bootstrap-theme'),
                            value: attributes.alignItems,
                            options: alignOptions,
                            onChange: (value) => setAttributes({ alignItems: value })
                        })
                    ),
                    createElement(PanelBody, { title: __('AOS Animation', 'bootstrap-theme'), initialOpen: false },
                        createElement(SelectControl, {
                            label: __('Animation Type', 'bootstrap-theme'),
                            value: attributes.aosAnimation,
                            options: [
                                { label: __('None', 'bootstrap-theme'), value: '' },
                                { label: 'fade', value: 'fade' },
                                { label: 'fade-up', value: 'fade-up' },
                                { label: 'fade-down', value: 'fade-down' },
                                { label: 'fade-left', value: 'fade-left' },
                                { label: 'fade-right', value: 'fade-right' },
                                { label: 'fade-up-right', value: 'fade-up-right' },
                                { label: 'fade-up-left', value: 'fade-up-left' },
                                { label: 'fade-down-right', value: 'fade-down-right' },
                                { label: 'fade-down-left', value: 'fade-down-left' },
                                { label: 'flip-up', value: 'flip-up' },
                                { label: 'flip-down', value: 'flip-down' },
                                { label: 'flip-left', value: 'flip-left' },
                                { label: 'flip-right', value: 'flip-right' },
                                { label: 'slide-up', value: 'slide-up' },
                                { label: 'slide-down', value: 'slide-down' },
                                { label: 'slide-left', value: 'slide-left' },
                                { label: 'slide-right', value: 'slide-right' },
                                { label: 'zoom-in', value: 'zoom-in' },
                                { label: 'zoom-in-up', value: 'zoom-in-up' },
                                { label: 'zoom-in-down', value: 'zoom-in-down' },
                                { label: 'zoom-in-left', value: 'zoom-in-left' },
                                { label: 'zoom-in-right', value: 'zoom-in-right' },
                                { label: 'zoom-out', value: 'zoom-out' },
                                { label: 'zoom-out-up', value: 'zoom-out-up' },
                                { label: 'zoom-out-down', value: 'zoom-out-down' },
                                { label: 'zoom-out-left', value: 'zoom-out-left' },
                                { label: 'zoom-out-right', value: 'zoom-out-right' }
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
                                    { label: 'ease', value: 'ease' },
                                    { label: 'ease-in', value: 'ease-in' },
                                    { label: 'ease-out', value: 'ease-out' },
                                    { label: 'ease-in-out', value: 'ease-in-out' },
                                    { label: 'ease-in-back', value: 'ease-in-back' },
                                    { label: 'ease-out-back', value: 'ease-out-back' },
                                    { label: 'ease-in-out-back', value: 'ease-in-out-back' },
                                    { label: 'ease-in-sine', value: 'ease-in-sine' },
                                    { label: 'ease-out-sine', value: 'ease-out-sine' },
                                    { label: 'ease-in-out-sine', value: 'ease-in-out-sine' },
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
                        className: `row ${attributes.gutters} ${attributes.justifyContent} ${attributes.alignItems} ${blockProps.className || ''}`.trim()
                    }),
                    createElement(InnerBlocks, {
                        allowedBlocks: ['bootstrap-theme/bs-column'],
                        template: [
                            ['bootstrap-theme/bs-column'],
                            ['bootstrap-theme/bs-column']
                        ],
                        placeholder: __('Add columns to row...', 'bootstrap-theme')
                    })
                )
            );
        },

        save: function() {
            return createElement(InnerBlocks.Content);
        }
    });

})(window.wp);