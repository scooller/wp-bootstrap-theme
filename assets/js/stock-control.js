/**
 * Control de stock del lado del cliente
 * Validación adicional antes de añadir productos al carrito
 */
(function($) {
    'use strict';

    class StockControl {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
            this.checkStockOnPageLoad();
            this.fixQuantityInputs();
        }

        bindEvents() {
            // NO interceptar el botón - dejar que el formulario se envíe normalmente
            // La validación se hace en el servidor con woocommerce_add_to_cart_validation
            
            // Monitorear cambios en cantidad
            $(document).on('change', 'input.qty', () => {
                this.validateQuantityChange();
            });

            // Cuando se selecciona una variación, WooCommerce re-renderiza el input de cantidad
            $(document).on('found_variation woocommerce_variation_has_changed', 'form.variations_form', () => {
                this.fixQuantityInputs();
            });
        }

        async validateStockBeforeAdd(button) {
            const $button = $(button);
            const $form = $button.closest('form.cart, form.variations_form, .product');
            
            // Obtener datos del producto
            const productData = this.getProductData($form, $button);
            
            if (!productData.product_id) {
                console.warn('No se pudo obtener el ID del producto');
                return this.proceedWithOriginalAction($button);
            }

            // Mostrar indicador de carga
            this.showLoadingState($button);

            try {
                const isAvailable = await this.checkStockAvailability(productData);
                
                if (isAvailable.success) {
                    // Stock disponible, proceder con la adición
                    this.proceedWithOriginalAction($button);
                } else {
                    // Stock no disponible, mostrar error
                    this.showStockError($button, isAvailable.data.message);
                }
            } catch (error) {
                console.error('Error al verificar stock:', error);
                // En caso de error, permitir la acción original para no bloquear completamente
                this.proceedWithOriginalAction($button);
            } finally {
                this.hideLoadingState($button);
            }
        }

        // Fallback: eliminar max negativo para evitar el error de navegador (p. ej. max="-1")
        fixQuantityInputs() {
            $('input.qty').each(function() {
                const $input = $(this);
                const maxAttr = $input.attr('max');
                const minAttr = $input.attr('min');
                // Si max es numérico y menor a 0, removerlo
                if (typeof maxAttr !== 'undefined' && maxAttr !== '') {
                    const maxVal = parseFloat(maxAttr);
                    if (!isNaN(maxVal) && maxVal < 0) {
                        $input.removeAttr('max');
                    }
                }
                // Asegurar mínimo 1
                if (typeof minAttr === 'undefined' || parseFloat(minAttr) < 1) {
                    $input.attr('min', '1');
                }
                // Paso por defecto 1
                if (!$input.attr('step')) {
                    $input.attr('step', '1');
                }
            });
        }

        getProductData($form, $button) {
            let productId = 0;
            let variationId = 0;
            let quantity = 1;

            // Intentar obtener desde el formulario
            if ($form.length) {
                productId = $form.find('[name="add-to-cart"]').val() || 
                           $form.find('[name="product_id"]').val() ||
                           $form.data('product_id');
                
                variationId = $form.find('[name="variation_id"]').val() || 0;
                quantity = $form.find('[name="quantity"]').val() || 
                          $form.find('input.qty').val() || 1;
            }

            // Intentar obtener desde el botón
            if (!productId) {
                productId = $button.data('product_id') || 
                           $button.attr('data-product_id') ||
                           $button.data('product-id');
            }

            if (!variationId) {
                variationId = $button.data('variation_id') || 
                             $button.attr('data-variation_id') || 0;
            }

            return {
                product_id: parseInt(productId) || 0,
                variation_id: parseInt(variationId) || 0,
                quantity: parseInt(quantity) || 1
            };
        }

        async checkStockAvailability(productData) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: stockControlAjax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'validate_stock_before_cart',
                        nonce: stockControlAjax.nonce,
                        product_id: productData.product_id,
                        variation_id: productData.variation_id,
                        quantity: productData.quantity
                    },
                    success: (response) => {
                        resolve(response);
                    },
                    error: (xhr, status, error) => {
                        reject(error);
                    }
                });
            });
        }

        proceedWithOriginalAction($button) {
            // Temporarily disable our event handler
            $button.off('click.stock-control');
            
            // Trigger the original click
            $button[0].click();
            
            // Re-enable our handler after a short delay
            setTimeout(() => {
                $button.on('click.stock-control', (e) => {
                    e.preventDefault();
                    this.validateStockBeforeAdd(e.target);
                });
            }, 100);
        }

        showLoadingState($button) {
            $button.addClass('loading disabled')
                   .prop('disabled', true);
            
            // Guardar texto original si no existe
            if (!$button.data('original-text')) {
                $button.data('original-text', $button.text());
            }
            
            $button.html('<i class="fas fa-spinner fa-spin me-2"></i>' + stockControlAjax.messages.checking_stock);
        }

        hideLoadingState($button) {
            $button.removeClass('loading disabled')
                   .prop('disabled', false);
            
            const originalText = $button.data('original-text');
            if (originalText) {
                $button.html(originalText);
            }
        }

        showStockError($button, message) {
            // Mostrar mensaje de error
            this.showNotification(message, 'error');
            
            // Cambiar temporalmente el botón
            const originalText = $button.data('original-text') || $button.text();
            $button.addClass('btn-danger')
                   .removeClass('btn-primary btn-success')
                   .html('<i class="fas fa-exclamation-triangle me-2"></i>' + stockControlAjax.messages.stock_unavailable);
            
            // Restaurar después de 3 segundos
            setTimeout(() => {
                $button.removeClass('btn-danger')
                       .addClass('btn-primary')
                       .html(originalText);
            }, 3000);
        }

        showNotification(message, type = 'error') {
            // Crear o actualizar notificación
            let $notification = $('.stock-notification');
            
            if (!$notification.length) {
                $notification = $('<div class="stock-notification alert alert-dismissible fade show" role="alert">' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '<div class="message"></div>' +
                    '</div>');
                
                // Insertar en la parte superior de la página
                $('main, .main-content, .content, body').first().prepend($notification);
            }
            
            // Configurar tipo y mensaje
            $notification.removeClass('alert-danger alert-warning alert-success')
                        .addClass(type === 'error' ? 'alert-danger' : 'alert-warning')
                        .find('.message')
                        .html('<i class="fas fa-exclamation-triangle me-2"></i>' + message);
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                $notification.alert('close');
            }, 5000);
        }

        validateQuantityChange() {
            const $qtyInput = $('input.qty');
            const $addButton = $('.single_add_to_cart_button');
            
            if ($qtyInput.length && $addButton.length) {
                const quantity = parseInt($qtyInput.val()) || 1;
                
                // Si la cantidad es mayor a 1, mostrar advertencia
                if (quantity > 1) {
                    this.showQuantityWarning($qtyInput, quantity);
                } else {
                    this.hideQuantityWarning();
                }
            }
        }

        showQuantityWarning($input, quantity) {
            let $warning = $('.quantity-warning');
            
            if (!$warning.length) {
                $warning = $('<div class="quantity-warning alert alert-info mt-2 mb-0">' +
                    '<i class="fas fa-info-circle me-2"></i>' +
                    '<span class="message"></span>' +
                    '</div>');
                
                $input.closest('.quantity').after($warning);
            }
            
            $warning.find('.message').text(
                `Solicitando ${quantity} unidades. Verifica la disponibilidad antes de añadir al carrito.`
            );
            
            $warning.show();
        }

        hideQuantityWarning() {
            $('.quantity-warning').hide();
        }

        checkStockOnPageLoad() {
            // Verificar si estamos en una página de producto individual
            if ($('.single-product').length) {
                this.checkCurrentProductStock();
            }
        }

        async checkCurrentProductStock() {
            const $form = $('form.cart, form.variations_form').first();
            const $button = $('.single_add_to_cart_button').first();
            
            if (!$form.length || !$button.length) return;
            
            // Excepción: No validar stock para productos rental_car
            // Detectar por campos específicos de rental_car
            if ($('.rentcar-fields').length || $('.rentcar-check-availability').length) {
                return;
            }
            
            const productData = this.getProductData($form, $button);
            
            if (!productData.product_id) return;
            
            try {
                const stockInfo = await this.checkStockAvailability(productData);

                console.log('Stock inicial verificado:', stockInfo);
                
                if (!stockInfo.success) {
                    // Deshabilitar botón si no hay stock
                    $button.prop('disabled', true)
                           .addClass('btn-secondary')
                           .removeClass('btn-primary')
                           .html('<i class="fas fa-times me-2"></i>No disponible');
                }else{
                    $button.prop('disabled', false);
                }
            } catch (error) {
                console.warn('No se pudo verificar el stock inicial:', error);
            }
        }

        // Método público para validación manual
        static validateProduct(productId, quantity = 1, variationId = 0) {
            const instance = new StockControl();
            return instance.checkStockAvailability({
                product_id: productId,
                variation_id: variationId,
                quantity: quantity
            });
        }
    }

    // Inicializar cuando el documento esté listo
    $(document).ready(() => {
        new StockControl();
    });

    // Exponer la clase globalmente para uso externo
    window.BootstrapThemeStockControl = StockControl;

})(jQuery);