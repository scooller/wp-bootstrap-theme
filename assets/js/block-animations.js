(function(wp){
  if (!wp || !wp.hooks) return;
  const { addFilter } = wp.hooks;
  const { createHigherOrderComponent } = wp.compose;
  const { Fragment } = wp.element;
  const { InspectorControls } = wp.blockEditor || wp.editor;
  const { PanelBody, SelectControl, TextControl } = wp.components;
  const { __ } = wp.i18n;

  // List of common Animate.css v4 animations (without prefix)
  const animationChoices = [
    { label: __('(Sin animación)', 'bootstrap-theme'), value: '' },
    { label: 'fadeIn', value: 'fadeIn' },
    { label: 'bounce', value: 'bounce' },
    { label: 'flash', value: 'flash' },
    { label: 'pulse', value: 'pulse' },
    { label: 'rubberBand', value: 'rubberBand' },
    { label: 'shakeX', value: 'shakeX' },
    { label: 'shakeY', value: 'shakeY' },
    { label: 'headShake', value: 'headShake' },
    { label: 'swing', value: 'swing' },
    { label: 'tada', value: 'tada' },
    { label: 'wobble', value: 'wobble' },
    { label: 'jello', value: 'jello' },
    { label: 'heartBeat', value: 'heartBeat' },
    { label: 'fadeInUp', value: 'fadeInUp' },
    { label: 'fadeInDown', value: 'fadeInDown' },
    { label: 'fadeInLeft', value: 'fadeInLeft' },
    { label: 'fadeInRight', value: 'fadeInRight' },
    { label: 'zoomIn', value: 'zoomIn' },
    { label: 'bounceIn', value: 'bounceIn' },
    { label: 'slideInUp', value: 'slideInUp' },
    { label: 'flipInX', value: 'flipInX' },
    { label: 'flipInY', value: 'flipInY' },
    { label: 'jackInTheBox', value: 'jackInTheBox' }   
  ];

  // 1) Extend attributes for all blocks
  addFilter('blocks.registerBlockType','bootstrap-theme/animations/attributes', (settings, name) => {
    settings.attributes = Object.assign({}, settings.attributes, {
      wowAnimation: { type: 'string', default: '' },
      wowDelay: { type: 'string', default: '' }, // e.g., '0.3s'
      wowDuration: { type: 'string', default: '' } // e.g., '0.8s'
    });
    return settings;
  });

  // 2) Add controls to Inspector
  const withAnimationControls = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
      const { attributes, setAttributes, isSelected } = props;
      const { wowAnimation, wowDelay, wowDuration } = attributes;

      return (
        wp.element.createElement(Fragment, null,
          wp.element.createElement(BlockEdit, props),
          isSelected && wp.element.createElement(
            InspectorControls, null,
            wp.element.createElement(
              PanelBody,
              { title: __('Animaciones (WOW + Animate.css)', 'bootstrap-theme'), initialOpen: false },
              wp.element.createElement(SelectControl, {
                label: __('Efecto', 'bootstrap-theme'),
                value: wowAnimation,
                options: animationChoices,
                onChange: (val) => setAttributes({ wowAnimation: val })
              }),
              wp.element.createElement(TextControl, {
                label: __('Delay (ej: 0.3s)', 'bootstrap-theme'),
                value: wowDelay,
                onChange: (val) => setAttributes({ wowDelay: val })
              }),
              wp.element.createElement(TextControl, {
                label: __('Duración (ej: 0.8s)', 'bootstrap-theme'),
                value: wowDuration,
                onChange: (val) => setAttributes({ wowDuration: val })
              })
            )
          )
        )
      );
    };
  }, 'withAnimationControls');

  addFilter('editor.BlockEdit', 'bootstrap-theme/animations/controls', withAnimationControls);

  // 3) Add classes/data attributes on save (funciona con bloques normales)
  addFilter('blocks.getSaveContent.extraProps','bootstrap-theme/animations/save-props', (extraProps, blockType, attributes) => {
    if (!attributes) return extraProps;
    const { wowAnimation, wowDelay, wowDuration } = attributes;
    if (!wowAnimation) return extraProps;

    // Class names for Animate.css v4
    const animateClass = 'animate__' + wowAnimation;
    const baseClasses = 'wow animate__animated ' + animateClass;

    extraProps = extraProps || {};
    extraProps.className = (extraProps.className ? extraProps.className + ' ' : '') + baseClasses;

    if (wowDelay) {
      extraProps['data-wow-delay'] = wowDelay+'s';
    }
    if (wowDuration) {
      extraProps['data-wow-duration'] = wowDuration+'s';
    }
    return extraProps;
  });

  // 4) Modificar el wrapper del bloque en el editor para bloques con InnerBlocks
  const withAnimationWrapper = createHigherOrderComponent((BlockListBlock) => {
    return (props) => {
      const { attributes, name } = props;
      const { wowAnimation, wowDelay, wowDuration } = attributes || {};

      if (!wowAnimation) {
        return wp.element.createElement(BlockListBlock, props);
      }

      // Aplicar clases de animación al wrapper
      const animateClass = 'animate__' + wowAnimation;
      const animationClasses = 'wow animate__animated ' + animateClass;
      
      const wrapperProps = {
        ...props,
        className: props.className ? `${props.className} ${animationClasses}` : animationClasses
      };

      // Añadir data attributes si existen
      if (wowDelay) {
        wrapperProps['data-wow-delay'] = wowDelay+'s';
      }
      if (wowDuration) {
        wrapperProps['data-wow-duration'] = wowDuration+'s';
      }

      return wp.element.createElement(BlockListBlock, wrapperProps);
    };
  }, 'withAnimationWrapper');

  addFilter('editor.BlockListBlock', 'bootstrap-theme/animations/wrapper', withAnimationWrapper);

  // 5) Para bloques server-side rendered, añadir clases al className
  const addAnimationClassName = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
      const { attributes, setAttributes, name } = props;
      const { wowAnimation, wowDelay, wowDuration, className } = attributes || {};

      // Si tiene animación, asegurar que className incluye las clases
      if (wowAnimation && !props.isSelected) {
        const animateClass = 'animate__' + wowAnimation;
        const animationClasses = 'wow animate__animated ' + animateClass;
        
        // Verificar si className ya contiene las clases de animación
        if (!className || !className.includes('wow')) {
          const newClassName = className ? `${className} ${animationClasses}` : animationClasses;
          
          // Actualizar className solo si es necesario
          if (className !== newClassName) {
            setTimeout(() => {
              setAttributes({ className: newClassName });
            }, 0);
          }
        }
      }

      return wp.element.createElement(BlockEdit, props);
    };
  }, 'addAnimationClassName');

  addFilter('editor.BlockEdit', 'bootstrap-theme/animations/classname', addAnimationClassName, 25);

})(window.wp || {});
