// Fancybox initialization for product images and galleries
jQuery(function($){
  // Configuración desde opciones del tema (localizada desde PHP)
  var fancyboxConfig = window.bootstrapThemeFancybox || {};
  var enabled = (typeof fancyboxConfig.enable === 'boolean') ? fancyboxConfig.enable : true;
  var autoImages = (typeof fancyboxConfig.autoImages === 'boolean') ? fancyboxConfig.autoImages : true;
  
  if (!enabled) return;

  var animationType = fancyboxConfig.animation || 'zoom';
  var showToolbar = (typeof fancyboxConfig.toolbar === 'boolean') ? fancyboxConfig.toolbar : true;
  var showThumbs = (typeof fancyboxConfig.thumbnails === 'boolean') ? fancyboxConfig.thumbnails : false;
  var loopEnabled = (typeof fancyboxConfig.loop === 'boolean') ? fancyboxConfig.loop : true;

  var options = {
    Toolbar: {
      display: {
        left: [],
        middle: [],
        right: showToolbar ? ['close'] : []
      }
    },
    Thumbs: showThumbs ? { autoStart: true } : false,
    infinite: loopEnabled
  };

  // Agregar clase de animación según configuración
  if (animationType && animationType !== 'none') {
    options.mainClass = 'fancybox-' + animationType;
  }

  // Bind estándar para elementos con data-fancybox
  if (typeof Fancybox !== 'undefined') {
    Fancybox.bind('[data-fancybox]', options);
  }

  // Autodetectar enlaces a imágenes si está habilitado
  if (autoImages && typeof Fancybox !== 'undefined') {
    var imageSelector = 'a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".gif"], a[href$=".webp"], a[href$=".JPG"], a[href$=".JPEG"], a[href$=".PNG"], a[href$=".GIF"], a[href$=".WEBP"]';
    
    // Excluir enlaces que ya tienen data-fancybox o clases específicas que no deberían abrirse en lightbox
    $(imageSelector).not('[data-fancybox]').not('.no-lightbox').not('.product-image-fancybox').each(function(){
      $(this).attr('data-fancybox', 'auto-gallery');
    });
    
    Fancybox.bind('[data-fancybox="auto-gallery"]', options);
  }

  // Compatibilidad con WooCommerce product images (legacy jQuery method)
  if ($.fn.fancybox) {
    $(document).on('click', '.woocommerce-product-single .product-image-fancybox', function(e){
      e.preventDefault();
      $.fancybox.open([
        {
          src: $(this).attr('href'),
          type: 'image',
          opts: {
            caption: $(this).find('img').attr('alt') || ''
          }
        }
      ]);
    });
  }
});
