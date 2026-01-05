(function(wp){
  if (!wp || !wp.hooks) return;
  const { addFilter } = wp.hooks;
  const { createHigherOrderComponent } = wp.compose;
  const { Fragment } = wp.element;
  const { InspectorControls } = wp.blockEditor || wp.editor;
  const { PanelBody, SelectControl, RangeControl, ToggleControl } = wp.components;
  const { __ } = wp.i18n;

  // List of AOS animations
  const animationChoices = [
    { label: __('(Sin animación)', 'bootstrap-theme'), value: '' },
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
  ];

  // List of easing functions
  const easingChoices = [
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
  ];

  // List of anchor placements
  const anchorChoices = [
    { label: 'top-bottom', value: 'top-bottom' },
    { label: 'top-center', value: 'top-center' },
    { label: 'top-top', value: 'top-top' },
    { label: 'center-bottom', value: 'center-bottom' },
    { label: 'center-center', value: 'center-center' },
    { label: 'center-top', value: 'center-top' },
    { label: 'bottom-bottom', value: 'bottom-bottom' },
    { label: 'bottom-center', value: 'bottom-center' },
    { label: 'bottom-top', value: 'bottom-top' }
  ];

  // 1) Extend attributes for all blocks
  addFilter('blocks.registerBlockType','bootstrap-theme/animations/attributes', (settings, name) => {
    settings.attributes = Object.assign({}, settings.attributes, {
      aosAnimation: { type: 'string', default: '' },
      aosDelay: { type: 'number', default: 0 },
      aosDuration: { type: 'number', default: 800 },
      aosEasing: { type: 'string', default: 'ease-in-out-cubic' },
      aosOnce: { type: 'boolean', default: false },
      aosMirror: { type: 'boolean', default: true },
      aosAnchorPlacement: { type: 'string', default: 'top-bottom' }
    });
    return settings;
  });

  // 2) Add controls to Inspector
  const withAnimationControls = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
      const { attributes, setAttributes, isSelected } = props;
      const { aosAnimation, aosDelay, aosDuration, aosEasing, aosOnce, aosMirror, aosAnchorPlacement } = attributes;

      return (
        wp.element.createElement(Fragment, null,
          wp.element.createElement(BlockEdit, props),
          isSelected && wp.element.createElement(
            InspectorControls, null,
            wp.element.createElement(
              PanelBody,
              { title: __('Animaciones (AOS)', 'bootstrap-theme'), initialOpen: false },
              wp.element.createElement(SelectControl, {
                label: __('Tipo de Animación', 'bootstrap-theme'),
                value: aosAnimation,
                options: animationChoices,
                onChange: (val) => setAttributes({ aosAnimation: val })
              }),
              aosAnimation && wp.element.createElement(Fragment, null,
                wp.element.createElement(RangeControl, {
                  label: __('Delay (ms)', 'bootstrap-theme'),
                  value: aosDelay,
                  onChange: (val) => setAttributes({ aosDelay: val }),
                  min: 0,
                  max: 3000,
                  step: 100
                }),
                wp.element.createElement(RangeControl, {
                  label: __('Duración (ms)', 'bootstrap-theme'),
                  value: aosDuration,
                  onChange: (val) => setAttributes({ aosDuration: val }),
                  min: 100,
                  max: 3000,
                  step: 100
                }),
                wp.element.createElement(SelectControl, {
                  label: __('Easing', 'bootstrap-theme'),
                  value: aosEasing,
                  options: easingChoices,
                  onChange: (val) => setAttributes({ aosEasing: val })
                }),
                wp.element.createElement(ToggleControl, {
                  label: __('Animar una sola vez', 'bootstrap-theme'),
                  checked: aosOnce,
                  onChange: (val) => setAttributes({ aosOnce: val })
                }),
                wp.element.createElement(ToggleControl, {
                  label: __('Mirror (repetir en scroll up)', 'bootstrap-theme'),
                  checked: aosMirror,
                  onChange: (val) => setAttributes({ aosMirror: val })
                }),
                wp.element.createElement(SelectControl, {
                  label: __('Anchor Placement', 'bootstrap-theme'),
                  value: aosAnchorPlacement,
                  options: anchorChoices,
                  onChange: (val) => setAttributes({ aosAnchorPlacement: val })
                })
              )
            )
          )
        )
      );
    };
  }, 'withAnimationControls');

  addFilter('editor.BlockEdit', 'bootstrap-theme/animations/controls', withAnimationControls);

  // 3) Add data attributes on save
  addFilter('blocks.getSaveContent.extraProps','bootstrap-theme/animations/save-props', (extraProps, blockType, attributes) => {
    if (!attributes) return extraProps;
    const { aosAnimation, aosDelay, aosDuration, aosEasing, aosOnce, aosMirror, aosAnchorPlacement } = attributes;
    if (!aosAnimation) return extraProps;

    extraProps = extraProps || {};

    // Add AOS data attributes
    extraProps['data-aos'] = aosAnimation;

    if (aosDelay > 0) {
      extraProps['data-aos-delay'] = aosDelay;
    }
    if (aosDuration > 0) {
      extraProps['data-aos-duration'] = aosDuration;
    }
    if (aosEasing) {
      extraProps['data-aos-easing'] = aosEasing;
    }
    if (aosOnce) {
      extraProps['data-aos-once'] = 'true';
    }
    if (aosMirror) {
      extraProps['data-aos-mirror'] = 'true';
    } else {
      extraProps['data-aos-mirror'] = 'false';
    }
    if (aosAnchorPlacement) {
      extraProps['data-aos-anchor-placement'] = aosAnchorPlacement;
    }

    return extraProps;
  });

  // 4) Modify block wrapper in editor to show animations
  const withAnimationWrapper = createHigherOrderComponent((BlockListBlock) => {
    return (props) => {
      const { attributes, name } = props;
      const { aosAnimation, aosDelay, aosDuration, aosEasing, aosOnce, aosMirror, aosAnchorPlacement } = attributes || {};

      if (!aosAnimation) {
        return wp.element.createElement(BlockListBlock, props);
      }

      // Build data attributes for preview
      const wrapperProps = {
        ...props,
        'data-aos': aosAnimation
      };

      if (aosDelay > 0) {
        wrapperProps['data-aos-delay'] = aosDelay;
      }
      if (aosDuration > 0) {
        wrapperProps['data-aos-duration'] = aosDuration;
      }
      if (aosEasing) {
        wrapperProps['data-aos-easing'] = aosEasing;
      }
      if (aosOnce) {
        wrapperProps['data-aos-once'] = 'true';
      }
      if (aosMirror !== false) {
        wrapperProps['data-aos-mirror'] = 'true';
      }
      if (aosAnchorPlacement) {
        wrapperProps['data-aos-anchor-placement'] = aosAnchorPlacement;
      }

      return wp.element.createElement(BlockListBlock, wrapperProps);
    };
  }, 'withAnimationWrapper');

  addFilter('editor.BlockListBlock', 'bootstrap-theme/animations/wrapper', withAnimationWrapper);

})(window.wp || {});
