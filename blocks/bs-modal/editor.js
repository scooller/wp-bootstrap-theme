/**
 * Bootstrap Modal Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, InnerBlocks, useBlockProps, RichText } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl, TextControl, RangeControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-modal', {
        title: __('Bootstrap Modal', 'bootstrap-theme'),
        description: __('Bootstrap modal dialog component', 'bootstrap-theme'),
        icon: 'admin-page',
        category: 'bootstrap',
        keywords: [__('modal'), __('dialog'), __('bootstrap')],
        
        attributes: {
            modalId: {
                type: 'string',
                default: ''
            },
            modalTitle: {
                type: 'string',
                default: 'Modal Title'
            },
            size: {
                type: 'string',
                default: ''
            },
            centered: {
                type: 'boolean',
                default: false
            },
            scrollable: {
                type: 'boolean',
                default: false
            },
            staticBackdrop: {
                type: 'boolean',
                default: false
            },
            buttonText: {
                type: 'string',
                default: 'Open Modal'
            },
            buttonVariant: {
                type: 'string',
                default: 'primary'
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
            const { attributes, setAttributes, clientId } = props;
            const blockProps = useBlockProps();
            if (attributes.preview) {
                return createElement('img', {
                    src: '/wp-content/themes/bootstrap-theme/blocks/bs-modal/example.png',
                    alt: 'Preview',
                    style: { width: '100%', height: 'auto', display: 'block' }
                });
            }
            // Generate unique ID if not set
            if (!attributes.modalId) {
                setAttributes({ modalId: `modal-${clientId}` });
            }
            const sizeOptions = [
                { label: 'Default', value: '' },
                { label: 'Small', value: 'modal-sm' },
                { label: 'Large', value: 'modal-lg' },
                { label: 'Extra Large', value: 'modal-xl' },
                { label: 'Full Screen', value: 'modal-fullscreen' }
            ];

            const variantOptions = [
                { label: 'Primary', value: 'primary' },
                { label: 'Secondary', value: 'secondary' },
                { label: 'Success', value: 'success' },
                { label: 'Danger', value: 'danger' },
                { label: 'Warning', value: 'warning' },
                { label: 'Info', value: 'info' },
                { label: 'Light', value: 'light' },
                { label: 'Dark', value: 'dark' }
            ];

            const modalClasses = [
                'modal-dialog',
                attributes.size,
                attributes.centered ? 'modal-dialog-centered' : '',
                attributes.scrollable ? 'modal-dialog-scrollable' : ''
            ].filter(Boolean).join(' ');
            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Modal Settings', 'bootstrap-theme') },
                        createElement(TextControl, {
                            label: __('Modal ID', 'bootstrap-theme'),
                            value: attributes.modalId,
                            onChange: (value) => setAttributes({ modalId: value })
                        }),
                        createElement(SelectControl, {
                            label: __('Size', 'bootstrap-theme'),
                            value: attributes.size,
                            options: sizeOptions,
                            onChange: (value) => setAttributes({ size: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Centered', 'bootstrap-theme'),
                            checked: attributes.centered,
                            onChange: (value) => setAttributes({ centered: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Scrollable', 'bootstrap-theme'),
                            checked: attributes.scrollable,
                            onChange: (value) => setAttributes({ scrollable: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Static Backdrop', 'bootstrap-theme'),
                            help: __('Modal will not close when clicking outside', 'bootstrap-theme'),
                            checked: attributes.staticBackdrop,
                            onChange: (value) => setAttributes({ staticBackdrop: value })
                        })
                    ),
                    createElement(PanelBody, { title: __('Trigger Button', 'bootstrap-theme') },
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
                createElement('div', blockProps,
                    // Trigger Button
                    createElement('button', {
                        type: 'button',
                        className: `btn btn-${attributes.buttonVariant}`,
                        'data-bs-toggle': 'modal',
                        'data-bs-target': `#${attributes.modalId}`
                    }, attributes.buttonText),
                    
                    // Modal Preview (Editor Only)
                    createElement('div', {
                        className: 'modal-preview mt-3 p-3 border',
                        style: { backgroundColor: '#f8f9fa' }
                    },
                        createElement('h6', {}, __('Modal Preview:', 'bootstrap-theme')),
                        createElement('div', { className: 'modal-content' },
                            createElement('div', { className: 'modal-header' },
                                createElement(RichText, {
                                    tagName: 'h5',
                                    className: 'modal-title',
                                    value: attributes.modalTitle,
                                    onChange: (value) => setAttributes({ modalTitle: value }),
                                    placeholder: __('Modal title...', 'bootstrap-theme')
                                })
                            ),
                            createElement('div', { className: 'modal-body' },
                                createElement(InnerBlocks, {
                                    placeholder: __('Add modal content...', 'bootstrap-theme')
                                })
                            )
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