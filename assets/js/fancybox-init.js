// Fancybox initialization for product images
jQuery(function($){
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
  Fancybox.bind('[data-fancybox]', {}); 
});
