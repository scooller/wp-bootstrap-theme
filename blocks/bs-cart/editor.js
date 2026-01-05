/**
 * Bootstrap Cart Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, ToggleControl, SelectControl, RangeControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-cart', {
        title: __('Bootstrap Shopping Cart', 'bootstrap-theme'),
        description: __('Display WooCommerce shopping cart with automatic checkout updates', 'bootstrap-theme'),
        icon: 'cart',
        category: 'bootstrap',
        keywords: [__('cart'), __('woocommerce'), __('shopping')],
        
        attributes: {
            showEmptyMessage: {
                type: 'boolean',
                default: true
            },
            showTotals: {
                type: 'boolean',
                default: true
            },
            showButtons: {
                type: 'boolean',
                default: true
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
        
        supports: {
            align: true,
            className: true,
            anchor: true
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const blockProps = useBlockProps();
            
            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Cart Settings', 'bootstrap-theme') },
                        createElement(ToggleControl, {
                            label: __('Show Empty Cart Message', 'bootstrap-theme'),
                            checked: attributes.showEmptyMessage,
                            onChange: (value) => setAttributes({ showEmptyMessage: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Show Totals', 'bootstrap-theme'),
                            checked: attributes.showTotals,
                            onChange: (value) => setAttributes({ showTotals: value })
                        }),
                        createElement(ToggleControl, {
                            label: __('Show Cart & Checkout Buttons', 'bootstrap-theme'),
                            checked: attributes.showButtons,
                            onChange: (value) => setAttributes({ showButtons: value })
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
                        className: `bs-cart-editor ${blockProps.className || ''}`
                    }),
                    createElement('div', { className: 'p-3 bg-light border rounded' },
                        createElement('div', { className: 'mb-2' },
                            createElement('strong', null, '🛒 ' + __('Shopping Cart Block', 'bootstrap-theme'))
                        ),
                        createElement('p', { style: { margin: '0.5rem 0', fontSize: '0.9rem', color: '#666' } },
                            __('This block displays the WooCommerce shopping cart on the frontend.', 'bootstrap-theme')
                        ),
                        createElement('p', { style: { margin: '0.5rem 0', fontSize: '0.85rem', color: '#999' } },
                            __('The cart will appear here when visitors add products.', 'bootstrap-theme')
                        ),
                        createElement('div', { className: 'mt-3 pt-3 border-top' },
                            createElement('div', { style: { fontSize: '0.85rem' } },
                                createElement('div', { style: { margin: '0.25rem 0' } },
                                    (attributes.showEmptyMessage ? '✓' : '✗') + ' ' + __('Empty message', 'bootstrap-theme')
                                ),
                                createElement('div', { style: { margin: '0.25rem 0' } },
                                    (attributes.showTotals ? '✓' : '✗') + ' ' + __('Show totals', 'bootstrap-theme')
                                ),
                                createElement('div', { style: { margin: '0.25rem 0' } },
                                    (attributes.showButtons ? '✓' : '✗') + ' ' + __('Show buttons', 'bootstrap-theme')
                                )
                            )
                        )
                    )
                )
            );
        },

        save: function() {
            return null;
        }
    });

})(window.wp);
