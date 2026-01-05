(function(wp){
    const { __ } = wp.i18n;
    const { addFilter } = wp.hooks;
    const { createElement, Fragment } = wp.element;
    const { InspectorControls } = wp.blockEditor || wp.editor;
    const { PanelBody, SelectControl, RangeControl, ToggleControl } = wp.components;
    const { createHigherOrderComponent } = wp.compose;
    const { domReady } = wp;

    domReady(function(){

    const supportedBlocks = ['core/paragraph', 'core/heading'];

    const aosAttributes = {
        aosAnimation: { type: 'string', default: '' },
        aosDelay: { type: 'number', default: 0 },
        aosDuration: { type: 'number', default: 800 },
        aosEasing: { type: 'string', default: 'ease-in-out-cubic' },
        aosOnce: { type: 'boolean', default: false },
        aosMirror: { type: 'boolean', default: true },
        aosAnchorPlacement: { type: 'string', default: 'top-bottom' }
    };

    addFilter('blocks.registerBlockType', 'bootstrap-theme/aos-core-attrs', (settings, name) => {
        if (!supportedBlocks.includes(name)) return settings;
        settings.attributes = Object.assign({}, settings.attributes, aosAttributes);
        return settings;
    });

    // Patch already-registered core blocks (heading/paragraph) so attributes exist even if they were registered before this script ran.
    if (wp.blocks && typeof wp.blocks.getBlockType === 'function') {
        supportedBlocks.forEach((blockName) => {
            const block = wp.blocks.getBlockType(blockName);
            if (!block) return;
            const hasAttrs = block.attributes && Object.prototype.hasOwnProperty.call(block.attributes, 'aosAnimation');
            if (hasAttrs) return;
            // Mutate attributes in-place to avoid unregister/register issues with core blocks
            block.attributes = Object.assign({}, block.attributes || {}, aosAttributes);
        });
    }

    const withAOSControls = createHigherOrderComponent((BlockEdit) => {
        return (props) => {
            if (!supportedBlocks.includes(props.name)) {
                return createElement(BlockEdit, props);
            }

            const { attributes, setAttributes } = props;

            return createElement(Fragment, {},
                createElement(BlockEdit, props),
                createElement(InspectorControls, {},
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
                )
            );
        };
    }, 'withAOSControls');

    addFilter('editor.BlockEdit', 'bootstrap-theme/aos-core-edit', withAOSControls);

    addFilter('blocks.getSaveContent.extraProps', 'bootstrap-theme/aos-core-props', (extraProps, blockType, attributes) => {
        if (!supportedBlocks.includes(blockType.name)) return extraProps;
        if (!attributes || !attributes.aosAnimation) return extraProps;

        extraProps['data-aos'] = attributes.aosAnimation;
        if (attributes.aosDelay) {
            extraProps['data-aos-delay'] = attributes.aosDelay;
        }
        if (attributes.aosDuration) {
            extraProps['data-aos-duration'] = attributes.aosDuration;
        }
        if (attributes.aosEasing) {
            extraProps['data-aos-easing'] = attributes.aosEasing;
        }
        extraProps['data-aos-once'] = attributes.aosOnce ? 'true' : 'false';
        extraProps['data-aos-mirror'] = attributes.aosMirror ? 'true' : 'false';
        if (attributes.aosAnchorPlacement) {
            extraProps['data-aos-anchor-placement'] = attributes.aosAnchorPlacement;
        }
        return extraProps;
    });

    });

})(window.wp);
