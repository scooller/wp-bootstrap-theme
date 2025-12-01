<?php
/**
 * Template part para mostrar información crítica de stock
 *
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product || !$product->managing_stock()) {
    return;
}

$stock_quantity = $product->get_stock_quantity();
$product_id = $product->get_id();

// Solo mostrar si el stock es crítico (5 o menos unidades)
if ($stock_quantity > 5) {
    return;
}
?>

<div class="critical-stock-info mt-3" data-product-id="<?php echo esc_attr($product_id); ?>">
    <?php if ($stock_quantity <= 0): ?>
        <div class="alert alert-danger d-flex align-items-center">
            <i class="fas fa-times-circle stock-icon-danger me-3"></i>
            <div>
                <strong><?php esc_html_e('Producto agotado', 'bootstrap-theme'); ?></strong>
                <p class="mb-0 small">
                    <?php esc_html_e('Este producto está temporalmente agotado. Te notificaremos cuando esté disponible.', 'bootstrap-theme'); ?>
                </p>
            </div>
        </div>
    <?php elseif ($stock_quantity == 1): ?>
        <div class="alert alert-warning stock-warning d-flex align-items-center">
            <i class="fas fa-exclamation-triangle stock-icon-warning me-3"></i>
            <div>
                <strong><?php esc_html_e('¡Última unidad disponible!', 'bootstrap-theme'); ?></strong>
                <p class="mb-0 small">
                    <?php esc_html_e('Solo queda 1 unidad en stock. ¡No dejes que se agote!', 'bootstrap-theme'); ?>
                </p>
            </div>
        </div>
    <?php elseif ($stock_quantity <= 3): ?>
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-info-circle stock-icon-warning me-3"></i>
            <div>
                <strong><?php printf(esc_html__('Solo quedan %d unidades', 'bootstrap-theme'), $stock_quantity); ?></strong>
                <p class="mb-0 small">
                    <?php esc_html_e('Stock limitado. Asegura tu compra ahora.', 'bootstrap-theme'); ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-light border d-flex align-items-center">
            <i class="fas fa-box stock-icon-success me-3"></i>
            <div>
                <small class="text-muted">
                    <?php printf(esc_html__('Pocas unidades disponibles (%d restantes)', 'bootstrap-theme'), $stock_quantity); ?>
                </small>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($stock_quantity > 0): ?>
        <!-- Contador dinámico en tiempo real -->
        <div class="stock-countdown mt-2" style="display: none;">
            <div class="alert alert-primary small">
                <i class="fas fa-clock me-2"></i>
                <span class="countdown-text">
                    <?php esc_html_e('Reserva tu producto ahora. Otros usuarios también están viendo este artículo.', 'bootstrap-theme'); ?>
                </span>
            </div>
        </div>
        
        <!-- Script para mostrar el contador de forma aleatoria -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stockCountdown = document.querySelector('.stock-countdown');
            const stockQuantity = <?php echo intval($stock_quantity); ?>;
            
            // Mostrar contador aleatorio para crear urgencia (solo si stock <= 3)
            if (stockQuantity <= 3 && Math.random() < 0.6) {
                setTimeout(function() {
                    if (stockCountdown) {
                        stockCountdown.style.display = 'block';
                        
                        // Ocultar después de 10 segundos
                        setTimeout(function() {
                            stockCountdown.style.display = 'none';
                        }, 10000);
                    }
                }, 3000 + Math.random() * 5000);
            }
        });
        </script>
    <?php endif; ?>
</div>

<?php if ($stock_quantity > 0 && $stock_quantity <= 3): ?>
    <!-- Añadir funcionalidad extra con JavaScript para productos críticos -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartBtn = document.querySelector('.single_add_to_cart_button');
        const productId = <?php echo intval($product_id); ?>;
        
        if (addToCartBtn) {
            // Añadir clase especial para productos con stock crítico
            addToCartBtn.classList.add('critical-stock-product');
            
            // Verificar stock cada 30 segundos
            const stockCheckInterval = setInterval(function() {
                if (typeof window.BootstrapThemeStockControl !== 'undefined') {
                    window.BootstrapThemeStockControl.validateProduct(productId, 1, 0)
                        .then(function(response) {
                            if (!response.success) {
                                // Stock agotado, deshabilitar botón
                                addToCartBtn.disabled = true;
                                addToCartBtn.innerHTML = '<i class="fas fa-times me-2"></i>Agotado';
                                addToCartBtn.classList.remove('btn-primary');
                                addToCartBtn.classList.add('btn-secondary');
                                
                                // Mostrar mensaje
                                const stockInfo = document.querySelector('.critical-stock-info');
                                if (stockInfo) {
                                    stockInfo.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i><strong>Producto agotado</strong><br><small>Este producto se agotó mientras navegabas. Actualiza la página para ver productos similares.</small></div>';
                                }
                                
                                clearInterval(stockCheckInterval);
                            }
                        })
                        .catch(function(error) {
                            console.warn('Error verificando stock:', error);
                        });
                }
            }, 30000);
            
            // Limpiar interval cuando el usuario sale de la página
            window.addEventListener('beforeunload', function() {
                clearInterval(stockCheckInterval);
            });
        }
    });
    </script>
<?php endif; ?>