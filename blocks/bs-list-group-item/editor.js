/**
 * Bootstrap List Group Item Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps, RichText, InnerBlocks } = wp.blockEditor;
    const { PanelBody, ToggleControl, SelectControl, TextControl, RangeControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-list-group-item', {
        title: __('Bootstrap List Group Item', 'bootstrap-theme'),
        description: __('Individual item within a list group', 'bootstrap-theme'),
        icon: 'minus',
        category: 'bootstrap',
        keywords: [__('list'), __('item'), __('bootstrap')],
        parent: ['bootstrap-theme/bs-list-group'],
        
        attributes: {
            text: {
                type: 'string',
                default: 'List item'
            },
            variant: {
                type: 'string',
                default: ''
            },
            active: {
                type: 'boolean',
                default: false
            },
            disabled: {
                type: 'boolean',
                default: false
            },
            actionable: {
                type: 'boolean',
                default: false
            },
            href: {
                type: 'string',
                default: '#'
            },
            openInNewTab: {
                type: 'boolean',
                default: false
            },
            hasContent: {
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
            
            // Inserter preview image
            if (attributes.preview) {
                return createElement('img', {
                    src: '/wp-content/themes/bootstrap-theme/blocks/bs-list-group-item/example.png',
                    alt: __('List group item preview', 'bootstrap-theme'),
                    style: { width: '100%', height: 'auto', display: 'block' }
                });
            }
            
            const variantOptions = [
                { label: 'Default', value: '' },
                { label: 'Primary', value: 'primary' },
                { label: 'Secondary', value: 'secondary' },
                { label: 'Success', value: 'success' },
                { label: 'Danger', value: 'danger' },
                { label: 'Warning', value: 'warning' },
                { label: 'Info', value: 'info' },
                { label: 'Light', value: 'light' },
                { label: 'Dark', value: 'dark' }
            ];

            const itemClasses = [
                'list-group-item',
                attributes.variant ? `list-group-item-${attributes.variant}` : '',
                attributes.active ? 'active' : '',
                attributes.disabled ? 'disabled' : '',
                attributes.actionable ? 'list-group-item-action' : ''
            ].filter(Boolean).join(' ');

            const TagName = attributes.actionable ? 'a' : 'li';
            
            const animationProps = {};
            if (attributes.aosAnimation) {
                animationProps['data-aos'] = attributes.aosAnimation;
                if (attributes.aosDelay) {
                    animationProps['data-aos-delay'] = attributes.aosDelay;
                }
                if (attributes.aosDuration) {
                    animationProps['data-aos-duration'] = attributes.aosDuration;
                }
                if (attributes.aosEasing) {
                    animationProps['data-aos-easing'] = attributes.aosEasing;
                }
                animationProps['data-aos-once'] = attributes.aosOnce ? 'true' : 'false';
                animationProps['data-aos-mirror'] = attributes.aosMirror ? 'true' : 'false';
                if (attributes.aosAnchorPlacement) {
                    animationProps['data-aos-anchor-placement'] = attributes.aosAnchorPlacement;
                }
            }

            const blockProps = useBlockProps(animationProps);

            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('List Item Settings', 'bootstrap-theme') },
                        createElement(SelectControl, {
                            label: __('Color Variant', 'bootstrap-theme'),
                            value: attributes.variant,
                            options: variantOptions,
                            onChange: (value) => setAttributes({ variant: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Active', 'bootstrap-theme'),
                            help: __('Mark as active/current item', 'bootstrap-theme'),
                            checked: attributes.active,
                            onChange: (value) => setAttributes({ active: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Disabled', 'bootstrap-theme'),
                            help: __('Make item appear disabled', 'bootstrap-theme'),
                            checked: attributes.disabled,
                            onChange: (value) => setAttributes({ disabled: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Actionable', 'bootstrap-theme'),
                            help: __('Make item clickable/hoverable', 'bootstrap-theme'),
                            checked: attributes.actionable,
                            onChange: (value) => setAttributes({ actionable: value })
                        }),
                        attributes.actionable && createElement(TextControl, {
                            label: __('Link URL', 'bootstrap-theme'),
                            value: attributes.href,
                            onChange: (value) => setAttributes({ href: value }),
                            placeholder: __('https://example.com', 'bootstrap-theme')
                        }),
                        attributes.actionable && createElement(ToggleControl, {
                            label: __('Open in New Tab', 'bootstrap-theme'),
                            checked: attributes.openInNewTab,
                            onChange: (value) => setAttributes({ openInNewTab: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Complex Content', 'bootstrap-theme'),
                            help: __('Enable rich content instead of simple text', 'bootstrap-theme'),
                            checked: attributes.hasContent,
                            onChange: (value) => setAttributes({ hasContent: value })
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
                                checked: attributes.aosOnce,
                                onChange: (value) => setAttributes({ aosOnce: value })
                            }),
                            createElement(ToggleControl, {
                                label: __('Mirror Animation', 'bootstrap-theme'),
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
                createElement(TagName, 
                    Object.assign({}, blockProps, { 
                        className: `${itemClasses} ${blockProps.className || ''}`.trim(),
                        href: attributes.actionable ? attributes.href : undefined,
                        target: (attributes.actionable && attributes.openInNewTab) ? '_blank' : undefined,
                        rel: (attributes.actionable && attributes.openInNewTab) ? 'noopener noreferrer' : undefined,
                        onClick: attributes.actionable ? (e) => e.preventDefault() : undefined
                    }),
                    attributes.hasContent ?
                        createElement(InnerBlocks, {
                            placeholder: __('Add list item content...', 'bootstrap-theme'),
                            template: [
                                ['core/heading', { 
                                    content: __('List item heading', 'bootstrap-theme'),
                                    level: 5
                                }],
                                ['core/paragraph', { 
                                    content: __('Some additional content for this list item.', 'bootstrap-theme')
                                }]
                            ]
                        }) :
                        createElement(RichText, {
                            tagName: 'span',
                            value: attributes.text,
                            onChange: (value) => setAttributes({ text: value }),
                            placeholder: __('List item text...', 'bootstrap-theme'),
                            allowedFormats: ['core/bold', 'core/italic']
                        })
                )
            );
        },

        save: function(props) {
            const { attributes } = props;
            const animationProps = {};
            if (attributes.aosAnimation) {
                animationProps['data-aos'] = attributes.aosAnimation;
                if (attributes.aosDelay) {
                    animationProps['data-aos-delay'] = attributes.aosDelay;
                }
                if (attributes.aosDuration) {
                    animationProps['data-aos-duration'] = attributes.aosDuration;
                }
                if (attributes.aosEasing) {
                    animationProps['data-aos-easing'] = attributes.aosEasing;
                }
                animationProps['data-aos-once'] = attributes.aosOnce ? 'true' : 'false';
                animationProps['data-aos-mirror'] = attributes.aosMirror ? 'true' : 'false';
                if (attributes.aosAnchorPlacement) {
                    animationProps['data-aos-anchor-placement'] = attributes.aosAnchorPlacement;
                }
            }
            const blockProps = useBlockProps.save(animationProps);
            
            const itemClasses = [
                'list-group-item',
                attributes.variant ? `list-group-item-${attributes.variant}` : '',
                attributes.active ? 'active' : '',
                attributes.disabled ? 'disabled' : '',
                attributes.actionable ? 'list-group-item-action' : ''
            ].filter(Boolean).join(' ');

            const TagName = attributes.actionable ? 'a' : 'li';

            return createElement(TagName, 
                Object.assign({}, blockProps, { 
                    className: itemClasses,
                    href: attributes.actionable ? attributes.href : undefined,
                    target: (attributes.actionable && attributes.openInNewTab) ? '_blank' : undefined,
                    rel: (attributes.actionable && attributes.openInNewTab) ? 'noopener noreferrer' : undefined
                }),
                attributes.hasContent ?
                    createElement(InnerBlocks.Content) :
                    createElement(RichText.Content, {
                        tagName: 'span',
                        value: attributes.text
                    })
            );
        }
    });

})(window.wp);