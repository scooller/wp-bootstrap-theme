/**
 * Bootstrap Button Block Editor
 */

(function(wp) {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl, TextControl, RangeControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    registerBlockType('bootstrap-theme/bs-button', {
        title: __('Bootstrap Button', 'bootstrap-theme'),
        description: __('A customizable Bootstrap button component', 'bootstrap-theme'),
        icon: 'button',
        category: 'bootstrap',
        keywords: [ __('button'), __('bootstrap'), __('link') ],
        attributes: {
            text: { type: 'string', default: 'Button' },
            variant: { type: 'string', default: 'btn-primary' },
            outline: { type: 'boolean', default: false },
            link: { type: 'string', default: '' },
            target: { type: 'string', default: '_self' },
            size: { type: 'string', default: '' },
            disabled: { type: 'boolean', default: false },
            icon: { type: 'string', default: '' },
            iconPosition: { type: 'string', default: 'left' },
            preview: { type: 'boolean', default: false },
            className: { type: 'string', default: '' },
            aosAnimation: { type: 'string', default: '' },
            aosDelay: { type: 'number', default: 0 },
            aosDuration: { type: 'number', default: 800 },
            aosEasing: { type: 'string', default: 'ease-in-out-cubic' },
            aosOnce: { type: 'boolean', default: false },
            aosMirror: { type: 'boolean', default: true },
            aosAnchorPlacement: { type: 'string', default: 'top-bottom' }
        },
        example: { attributes: { preview: true } },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const blockProps = useBlockProps();

            if (attributes.preview) {
                return createElement('img', {
                    src: '/wp-content/themes/bootstrap-theme/blocks/bs-button/example.png',
                    alt: 'Preview',
                    style: { width: '100%', height: 'auto', display: 'block' }
                });
            }

            const buttonVariants = [
                { label: 'Primary', value: 'btn-primary' },
                { label: 'Secondary', value: 'btn-secondary' },
                { label: 'Success', value: 'btn-success' },
                { label: 'Danger', value: 'btn-danger' },
                { label: 'Warning', value: 'btn-warning' },
                { label: 'Info', value: 'btn-info' },
                { label: 'Light', value: 'btn-light' },
                { label: 'Dark', value: 'btn-dark' },
                { label: 'Link', value: 'btn-link' }
            ];

            const buttonSizes = [
                { label: 'Default', value: '' },
                { label: 'Small', value: 'btn-sm' },
                { label: 'Large', value: 'btn-lg' }
            ];

            const settingsPanel = createElement(PanelBody, { title: __('Button Settings', 'bootstrap-theme') },
                createElement(TextControl, {
                    label: __('Button Text', 'bootstrap-theme'),
                    value: attributes.text,
                    onChange: (value) => setAttributes({ text: value })
                }),
                createElement(SelectControl, {
                    label: __('Button Style', 'bootstrap-theme'),
                    value: attributes.variant,
                    options: buttonVariants,
                    onChange: (value) => setAttributes({ variant: value })
                }),
                createElement(SelectControl, {
                    label: __('Button Size', 'bootstrap-theme'),
                    value: attributes.size,
                    options: buttonSizes,
                    onChange: (value) => setAttributes({ size: value })
                }),
                createElement(ToggleControl, {
                    label: __('Outline Style', 'bootstrap-theme'),
                    checked: attributes.outline,
                    onChange: (value) => setAttributes({ outline: value })
                }),
                createElement(TextControl, {
                    label: __('Link URL', 'bootstrap-theme'),
                    value: attributes.link,
                    onChange: (value) => setAttributes({ link: value })
                }),
                createElement(ToggleControl, {
                    label: __('Open in new tab', 'bootstrap-theme'),
                    checked: attributes.target === '_blank',
                    onChange: (value) => setAttributes({ target: value ? '_blank' : '_self' })
                }),
                createElement(ToggleControl, {
                    label: __('Disabled', 'bootstrap-theme'),
                    checked: attributes.disabled,
                    onChange: (value) => setAttributes({ disabled: value })
                })
            );

            const aosPanel = createElement(PanelBody, { title: __('AOS Animation', 'bootstrap-theme'), initialOpen: false },
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
            );

            const iconPanel = createElement(PanelBody, { title: __('Icon', 'bootstrap-theme'), initialOpen: false },
                createElement(TextControl, {
                    label: __('Font Awesome classes', 'bootstrap-theme'),
                    help: __('Ejemplo: fa-solid fa-car (solo clases del icono)', 'bootstrap-theme'),
                    value: attributes.icon,
                    onChange: (value) => setAttributes({ icon: value })
                }),
                createElement(SelectControl, {
                    label: __('Icon position', 'bootstrap-theme'),
                    value: attributes.iconPosition,
                    options: [
                        { label: __('Before text', 'bootstrap-theme'), value: 'left' },
                        { label: __('After text', 'bootstrap-theme'), value: 'right' }
                    ],
                    onChange: (value) => setAttributes({ iconPosition: value })
                })
            );

            const icon = (attributes.icon || '').trim();
            const hasIcon = icon.length > 0;
            const iconEl = hasIcon ? createElement('i', { className: icon + (attributes.text ? (attributes.iconPosition === 'left' ? ' me-2' : ' ms-2') : '') }) : null;
            const inner = attributes.iconPosition === 'left' ? [iconEl, attributes.text] : [attributes.text, iconEl];

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

            return createElement(Fragment, {},
                createElement(InspectorControls, {}, settingsPanel, iconPanel, aosPanel),
                createElement('button',
                    Object.assign({}, blockProps, animationProps, {
                        className: `btn ${attributes.outline ? attributes.variant.replace('btn-', 'btn-outline-') : attributes.variant} ${attributes.size} ${blockProps.className || ''}`,
                        disabled: attributes.disabled
                    }),
                    inner
                )
            );
        },
        save: function() { return null; }
    });

})(window.wp);