/**
 * Bootstrap Container Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, InnerBlocks, useBlockProps, PanelColorSettings, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl, Button, RangeControl } = wp.components;
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
            backgroundImage: {
                type: 'string',
                default: ''
            },
            bgSize: {
                type: 'string',
                default: 'cover'
            },
            bgPosition: {
                type: 'string',
                default: 'center'
            },
            bgRepeat: {
                type: 'string',
                default: 'no-repeat'
            },
            bgAttachment: {
                type: 'string',
                default: 'scroll'
            },
            anchor: {
                type: 'string',
                default: ''
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
                { label: __('Gradient', 'bootstrap-theme'), value: 'gradient' },
                { label: __('Image', 'bootstrap-theme'), value: 'image' }
            ];

            const bgSizeOptions = [
                { label: __('Cover', 'bootstrap-theme'), value: 'cover' },
                { label: __('Contain', 'bootstrap-theme'), value: 'contain' },
                { label: __('Auto', 'bootstrap-theme'), value: 'auto' }
            ];

            const bgRepeatOptions = [
                { label: __('No Repeat', 'bootstrap-theme'), value: 'no-repeat' },
                { label: __('Repeat', 'bootstrap-theme'), value: 'repeat' },
                { label: __('Repeat X', 'bootstrap-theme'), value: 'repeat-x' },
                { label: __('Repeat Y', 'bootstrap-theme'), value: 'repeat-y' }
            ];

            const bgAttachmentOptions = [
                { label: __('Scroll', 'bootstrap-theme'), value: 'scroll' },
                { label: __('Fixed (Parallax)', 'bootstrap-theme'), value: 'fixed' },
                { label: __('Local', 'bootstrap-theme'), value: 'local' }
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
                } else if (attributes.bgType === 'image' && attributes.backgroundImage) {
                    style.backgroundImage = `url(${attributes.backgroundImage})`;
                    style.backgroundSize = attributes.bgSize || 'cover';
                    style.backgroundPosition = attributes.bgPosition || 'center';
                    style.backgroundRepeat = attributes.bgRepeat || 'no-repeat';
                    style.backgroundAttachment = attributes.bgAttachment || 'scroll';
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
                        }),
                        createElement('div', { className: 'components-base-control' },
                            createElement('label', { className: 'components-base-control__label' },
                                __('ID de Anclaje', 'bootstrap-theme')
                            ),
                            createElement('input', {
                                type: 'text',
                                className: 'components-text-control__input',
                                value: attributes.anchor,
                                onChange: (e) => setAttributes({ anchor: e.target.value }),
                                placeholder: 'nombre-sección',
                                style: { width: '100%', marginTop: '5px' }
                            }),
                            createElement('p', { style: { fontSize: '12px', color: '#666', marginTop: '5px' } },
                                __('Para crear links internos: #nombre-sección', 'bootstrap-theme')
                            )
                        )
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
                        ),
                        attributes.bgType === 'image' && createElement(Fragment, {},
                            createElement('div', { className: 'components-base-control' },
                                createElement('label', { className: 'components-base-control__label' },
                                    __('Background Image', 'bootstrap-theme')
                                ),
                                createElement(MediaUploadCheck, {},
                                    createElement(MediaUpload, {
                                        onSelect: (media) => setAttributes({ backgroundImage: media.url }),
                                        allowedTypes: ['image'],
                                        value: attributes.backgroundImage,
                                        render: ({ open }) => createElement(Fragment, {},
                                            attributes.backgroundImage ?
                                                createElement('div', {},
                                                    createElement('img', {
                                                        src: attributes.backgroundImage,
                                                        alt: '',
                                                        style: { maxWidth: '100%', height: 'auto', marginBottom: '10px' }
                                                    }),
                                                    createElement(Button, {
                                                        onClick: open,
                                                        variant: 'secondary',
                                                        style: { marginRight: '10px' }
                                                    }, __('Replace Image', 'bootstrap-theme')),
                                                    createElement(Button, {
                                                        onClick: () => setAttributes({ backgroundImage: '' }),
                                                        variant: 'link',
                                                        isDestructive: true
                                                    }, __('Remove Image', 'bootstrap-theme'))
                                                ) :
                                                createElement(Button, {
                                                    onClick: open,
                                                    variant: 'secondary'
                                                }, __('Select Image', 'bootstrap-theme'))
                                        )
                                    })
                                )
                            ),
                            createElement(SelectControl, {
                                label: __('Background Size', 'bootstrap-theme'),
                                value: attributes.bgSize,
                                options: bgSizeOptions,
                                onChange: (value) => setAttributes({ bgSize: value })
                            }),
                            createElement('div', { className: 'components-base-control' },
                                createElement('label', { className: 'components-base-control__label' },
                                    __('Background Position', 'bootstrap-theme')
                                ),
                                createElement('input', {
                                    type: 'text',
                                    className: 'components-text-control__input',
                                    value: attributes.bgPosition,
                                    onChange: (e) => setAttributes({ bgPosition: e.target.value }),
                                    placeholder: 'center'
                                })
                            ),
                            createElement(SelectControl, {
                                label: __('Background Repeat', 'bootstrap-theme'),
                                value: attributes.bgRepeat,
                                options: bgRepeatOptions,
                                onChange: (value) => setAttributes({ bgRepeat: value })
                            }),
                            createElement(SelectControl, {
                                label: __('Background Attachment', 'bootstrap-theme'),
                                value: attributes.bgAttachment,
                                options: bgAttachmentOptions,
                                onChange: (value) => setAttributes({ bgAttachment: value })
                            })
                        )
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