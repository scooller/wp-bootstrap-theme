/**
 * Bootstrap Container Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, InnerBlocks, useBlockProps, PanelColorSettings } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-container', {
        title: __('Bootstrap Container', 'bootstrap-theme'),
        description: __('Bootstrap container for layout structure', 'bootstrap-theme'),
        icon: 'editor-table',
        category: 'bootstrap',
        keywords: [__('container'), __('bootstrap'), __('layout')],
        
        attributes: {
            type: {
                type: 'string',
                default: 'container'
            },
            fluid: {
                type: 'boolean',
                default: false
            },
            bgType: {
                type: 'string', // 'none' | 'solid' | 'gradient'
                default: 'none'
            },
            bgColor: {
                type: 'string',
                default: ''
            },
            bgGradientFrom: {
                type: 'string',
                default: ''
            },
            bgGradientTo: {
                type: 'string',
                default: ''
            },
            bgGradientDirection: {
                type: 'string',
                default: 'to right'
            },
            preview: {
                type: 'boolean',
                default: false
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
                    src: '/wp-content/themes/bootstrap-theme/blocks/bs-container/example.png',
                    alt: 'Preview',
                    style: { width: '100%', height: 'auto', display: 'block' }
                });
            }
            const containerTypes = [
                { label: 'Standard Container', value: 'container' },
                { label: 'Small Container', value: 'container-sm' },
                { label: 'Medium Container', value: 'container-md' },
                { label: 'Large Container', value: 'container-lg' },
                { label: 'Extra Large Container', value: 'container-xl' },
                { label: 'XXL Container', value: 'container-xxl' }
            ];

            const bgTypeOptions = [
                { label: __('None', 'bootstrap-theme'), value: 'none' },
                { label: __('Solid color', 'bootstrap-theme'), value: 'solid' },
                { label: __('Gradient', 'bootstrap-theme'), value: 'gradient' }
            ];

            const gradientDirections = [
                { label: __('To right', 'bootstrap-theme'), value: 'to right' },
                { label: __('To left', 'bootstrap-theme'), value: 'to left' },
                { label: __('To bottom', 'bootstrap-theme'), value: 'to bottom' },
                { label: __('To top', 'bootstrap-theme'), value: 'to top' },
                { label: __('45°', 'bootstrap-theme'), value: '45deg' },
                { label: __('135°', 'bootstrap-theme'), value: '135deg' }
            ];

            // Build preview style for editor
            const buildStyle = () => {
                const style = {};
                if (attributes.bgType === 'solid' && attributes.bgColor) {
                    style.backgroundColor = attributes.bgColor;
                } else if (
                    attributes.bgType === 'gradient' &&
                    attributes.bgGradientFrom && attributes.bgGradientTo
                ) {
                    style.backgroundImage = `linear-gradient(${attributes.bgGradientDirection || 'to right'}, ${attributes.bgGradientFrom}, ${attributes.bgGradientTo})`;
                }
                return style;
            };
            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Container Settings', 'bootstrap-theme') },
                        createElement(ToggleControl, {
                            label: __('Fluid Container', 'bootstrap-theme'),
                            checked: attributes.fluid,
                            onChange: (value) => setAttributes({ fluid: value })
                        }),
                        !attributes.fluid && createElement(SelectControl, {
                            label: __('Container Type', 'bootstrap-theme'),
                            value: attributes.type,
                            options: containerTypes,
                            onChange: (value) => setAttributes({ type: value })
                        })
                    ),
                    createElement(PanelBody, { title: __('Background', 'bootstrap-theme'), initialOpen: false },
                        createElement(SelectControl, {
                            label: __('Background Type', 'bootstrap-theme'),
                            value: attributes.bgType,
                            options: bgTypeOptions,
                            onChange: (value) => setAttributes({ bgType: value })
                        }),
                        attributes.bgType === 'solid' && createElement(PanelColorSettings, {
                            title: __('Solid color', 'bootstrap-theme'),
                            colorSettings: [
                                {
                                    value: attributes.bgColor,
                                    onChange: (value) => setAttributes({ bgColor: value || '' }),
                                    label: __('Background color', 'bootstrap-theme')
                                }
                            ]
                        }),
                        attributes.bgType === 'gradient' && createElement(Fragment, {},
                            createElement(SelectControl, {
                                label: __('Direction', 'bootstrap-theme'),
                                value: attributes.bgGradientDirection,
                                options: gradientDirections,
                                onChange: (value) => setAttributes({ bgGradientDirection: value })
                            }),
                            createElement(PanelColorSettings, {
                                title: __('Gradient colors', 'bootstrap-theme'),
                                colorSettings: [
                                    {
                                        value: attributes.bgGradientFrom,
                                        onChange: (value) => setAttributes({ bgGradientFrom: value || '' }),
                                        label: __('From', 'bootstrap-theme')
                                    },
                                    {
                                        value: attributes.bgGradientTo,
                                        onChange: (value) => setAttributes({ bgGradientTo: value || '' }),
                                        label: __('To', 'bootstrap-theme')
                                    }
                                ]
                            })
                        )
                    )
                ),
                createElement('div', 
                    Object.assign({}, blockProps, { 
                        className: `${attributes.fluid ? 'container-fluid' : attributes.type} ${blockProps.className || ''}`,
                        style: Object.assign({}, blockProps.style || {}, buildStyle())
                    }),
                    createElement(InnerBlocks, {
                        placeholder: __('Add content to container...', 'bootstrap-theme')
                    })
                )
            );
        },

        save: function() {
            return createElement(InnerBlocks.Content);
        }
    });

})(window.wp);