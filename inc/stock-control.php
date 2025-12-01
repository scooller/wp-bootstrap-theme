<?php
/**
 * Control avanzado de stock para WooCommerce
 * Previene overselling cuando el stock es limitado (especialmente stock = 1)
 *
 * @package Bootstrap_Theme
 * @since 1.1.6
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase principal para control de stock
 */
class Bootstrap_Theme_Stock_Control {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Solo ejecutar si WooCommerce está activo
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        $this->init_hooks();
    }
    
    /**
     * Inicializar hooks
     */
    private function init_hooks() {
        
        // Validación antes de añadir al carrito - PRIORIDAD 5 para ejecutar ANTES que sold_individually check
        add_filter('woocommerce_add_to_cart_validation', array($this, 'validate_stock_before_add_to_cart'), 5, 3);
        
        // Validación durante el checkout
        add_action('woocommerce_checkout_process', array($this, 'validate_stock_during_checkout'));
        
        // Reservar stock temporalmente cuando se añade al carrito
        add_action('woocommerce_add_to_cart', array($this, 'reserve_stock_temporarily'), 10, 6);
        
        // Liberar stock reservado cuando se remueve del carrito
        add_action('woocommerce_cart_item_removed', array($this, 'release_reserved_stock'));
        
        // Liberar stock reservado cuando expira la sesión
        add_action('woocommerce_cleanup_sessions', array($this, 'cleanup_expired_reservations'));
        
        // Validación AJAX para botón "Añadir al carrito"
        add_action('wp_ajax_validate_stock_before_cart', array($this, 'ajax_validate_stock'));
        add_action('wp_ajax_nopriv_validate_stock_before_cart', array($this, 'ajax_validate_stock'));
        
        // Enqueue scripts para validación del lado del cliente
        add_action('wp_enqueue_scripts', array($this, 'enqueue_stock_control_scripts'));
        
        // Mostrar advertencias de stock bajo en productos
        add_action('woocommerce_single_product_summary', array($this, 'display_stock_warning'), 25);
        
        // Limpiar reservas al finalizar pedido
        add_action('woocommerce_thankyou', array($this, 'clear_cart_reservations'));
    }
    
    /**
     * Validar stock antes de añadir al carrito
     * Para productos variables, obtiene el variation_id del request
     */
    public function validate_stock_before_add_to_cart($passed, $product_id, $quantity) {
        // Para productos variables, obtener el variation_id del request
        $variation_id = isset($_REQUEST['variation_id']) ? absint($_REQUEST['variation_id']) : 0;
        $actual_product_id = $variation_id ? $variation_id : $product_id;
        
        $product = wc_get_product($actual_product_id);
        
        if (!$product) {
            return $passed;
        }
        
        $product_type = $product->get_type();
        
        // Skip validation for variable products (parent) - only validate variations
        if ($product_type === 'variable') {
            return $passed;
        }
        
        // Always skip virtual, composite, bundle, rental_car products or products not managing stock
        if ($product->is_virtual() || 
            $product_type === 'composite' || 
            $product_type === 'bundle' || 
            $product_type === 'rental_car' ||
            !$product->managing_stock()) {
            return $passed;
        }
        
        // Check if strict stock control is enabled for this product's categories
        $is_strict = $this->is_strict_stock_enabled_for_product($actual_product_id);
        
        if (!$is_strict) {
            return $passed;
        }
        
        $available_stock = $this->get_available_stock($product);
        $reserved_stock = $this->get_reserved_stock($actual_product_id);
        $actual_available = $available_stock - $reserved_stock;
        
        // Si el stock disponible es 1 o menos, hacer verificaciones adicionales
        if ($actual_available <= 1) {
            // Verificar si hay otros usuarios con este producto en el carrito
            $in_other_carts = $this->is_product_in_other_carts($product->get_id());
            
            if ($in_other_carts) {
                $message = get_field('stock_msg_product_in_other_carts', 'option');
                if (empty($message)) {
                    $message = __('Lo sentimos, "%s" está siendo procesado por otro usuario. Por favor, intenta de nuevo en unos minutos.', 'bootstrap-theme');
                }
                wc_add_notice(
                    sprintf($message, $product->get_name()),
                    'error'
                );
                return false;
            }
            
            // Verificar stock real en tiempo real
            $current_stock = $product->get_stock_quantity();
            if ($current_stock < $quantity) {
                $message = get_field('stock_msg_insufficient_stock', 'option');
                if (empty($message)) {
                    $message = __('Lo sentimos, no hay suficiente stock de "%s". Solo quedan %d unidades disponibles.', 'bootstrap-theme');
                }
                wc_add_notice(
                    sprintf($message, $product->get_name(), $current_stock),
                    'error'
                );
                return false;
            }
        }
        
        return $passed;
    }
    
    /**
     * Validar stock durante el proceso de checkout
     */
    public function validate_stock_during_checkout() {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $quantity = $cart_item['quantity'];
            
            // Skip virtual, composite, bundle and products not managing stock
            if (!$product) {
                continue;
            }
            
            $product_type = $product->get_type();
            
            if ($product->is_virtual() || 
                $product_type === 'composite' || 
                $product_type === 'bundle' || 
                !$product->managing_stock()) {
                continue;
            }
            
            $current_stock = $product->get_stock_quantity();
            
            if ($current_stock < $quantity) {
                $message = get_field('stock_msg_checkout_insufficient', 'option');
                if (empty($message)) {
                    $message = __('Lo sentimos, "%s" ya no tiene suficiente stock. Stock disponible: %d. Por favor, actualiza tu carrito.', 'bootstrap-theme');
                }
                wc_add_notice(
                    sprintf($message, $product->get_name(), $current_stock),
                    'error'
                );
            }
        }
    }
    
    /**
     * Reservar stock temporalmente
     */
    public function reserve_stock_temporarily($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        $product_id_to_reserve = $variation_id ? $variation_id : $product_id;
        $product = wc_get_product($product_id_to_reserve);
        
        // Do not reserve for virtual, composite, bundle, or when not managing stock
        if (!$product) {
            return;
        }
        
        $product_type = $product->get_type();
        
        if ($product->is_virtual() || 
            $product_type === 'composite' || 
            $product_type === 'bundle' || 
            !$product->managing_stock()) {
            return;
        }
        
        $this->add_stock_reservation($product_id_to_reserve, $quantity);
    }
    
    /**
     * Liberar stock reservado cuando se remueve del carrito
     */
    public function release_reserved_stock($cart_item_key) {
        $cart_item = WC()->cart->get_removed_cart_contents()[$cart_item_key] ?? null;
        
        if ($cart_item) {
            $product_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
            $quantity = $cart_item['quantity'];
            $product = wc_get_product($product_id);
            
            // Only remove reservations for products we reserved previously
            if (!$product) {
                return;
            }
            
            $product_type = $product->get_type();
            
            if ($product->is_virtual() || 
                $product_type === 'composite' || 
                $product_type === 'bundle' || 
                !$product->managing_stock()) {
                return;
            }
            
            $this->remove_stock_reservation($product_id, $quantity);
        }
    }
    
    /**
     * Obtener stock disponible real
     */
    private function get_available_stock($product) {
        return $product->get_stock_quantity();
    }
    
    /**
     * Obtener stock reservado para un producto
     */
    private function get_reserved_stock($product_id) {
        $reservations = get_transient('bootstrap_theme_stock_reservations') ?: array();
        $total_reserved = 0;
        
        foreach ($reservations as $session_id => $reserved_items) {
            if (isset($reserved_items[$product_id])) {
                $total_reserved += $reserved_items[$product_id]['quantity'];
            }
        }
        
        return $total_reserved;
    }
    
    /**
     * Verificar si un producto está en otros carritos
     */
    private function is_product_in_other_carts($product_id) {
        $current_session = WC()->session->get_customer_id();
        $reservations = get_transient('bootstrap_theme_stock_reservations') ?: array();
        
        foreach ($reservations as $session_id => $reserved_items) {
            if ($session_id !== $current_session && isset($reserved_items[$product_id])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Añadir reserva de stock
     */
    private function add_stock_reservation($product_id, $quantity) {
        $session_id = WC()->session->get_customer_id();
        $reservations = get_transient('bootstrap_theme_stock_reservations') ?: array();
        
        if (!isset($reservations[$session_id])) {
            $reservations[$session_id] = array();
        }
        
        $reservations[$session_id][$product_id] = array(
            'quantity' => $quantity,
            'timestamp' => time()
        );
        
        // Guardar por 30 minutos
        set_transient('bootstrap_theme_stock_reservations', $reservations, 30 * MINUTE_IN_SECONDS);
    }
    
    /**
     * Remover reserva de stock
     */
    private function remove_stock_reservation($product_id, $quantity) {
        $session_id = WC()->session->get_customer_id();
        $reservations = get_transient('bootstrap_theme_stock_reservations') ?: array();
        
        if (isset($reservations[$session_id][$product_id])) {
            unset($reservations[$session_id][$product_id]);
            
            if (empty($reservations[$session_id])) {
                unset($reservations[$session_id]);
            }
            
            set_transient('bootstrap_theme_stock_reservations', $reservations, 30 * MINUTE_IN_SECONDS);
        }
    }
    
    /**
     * Limpiar reservas expiradas
     */
    public function cleanup_expired_reservations() {
        $reservations = get_transient('bootstrap_theme_stock_reservations') ?: array();
        $current_time = time();
        $expiry_time = 30 * MINUTE_IN_SECONDS; // 30 minutos
        
        foreach ($reservations as $session_id => $reserved_items) {
            foreach ($reserved_items as $product_id => $reservation) {
                if (($current_time - $reservation['timestamp']) > $expiry_time) {
                    unset($reservations[$session_id][$product_id]);
                }
            }
            
            if (empty($reservations[$session_id])) {
                unset($reservations[$session_id]);
            }
        }
        
        set_transient('bootstrap_theme_stock_reservations', $reservations, 30 * MINUTE_IN_SECONDS);
    }
    
    /**
     * Verificar si el control de stock estricto está habilitado para un producto
     * basado en sus categorías
     */
    private function is_strict_stock_enabled_for_product($product_id) {
        // Obtener las categorías configuradas para control estricto
        $strict_categories = array();
        if (function_exists('get_field')) {
            $strict_categories = get_field('woocommerce_strict_stock_categories', 'option');
        }
        
        // Si no hay categorías configuradas, no aplicar control estricto a ningún producto
        if (empty($strict_categories) || !is_array($strict_categories)) {
            return false;
        }
        
        // Obtener las categorías del producto
        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
        
        if (is_wp_error($product_categories) || empty($product_categories)) {
            return false;
        }
        
        // Verificar si el producto tiene alguna categoría en la lista de control estricto
        foreach ($product_categories as $cat_id) {
            if (in_array($cat_id, $strict_categories)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * AJAX para validar stock antes de añadir al carrito
     */
    public function ajax_validate_stock() {
        check_ajax_referer('bootstrap_theme_nonce', 'nonce');
        
        $product_id = absint($_POST['product_id']);
        $quantity = absint($_POST['quantity']) ?: 1;
        $variation_id = absint($_POST['variation_id']) ?: 0;
        
        $product = wc_get_product($variation_id ? $variation_id : $product_id);
        
        if (!$product) {
            wp_send_json_error(array('message' => __('Producto no encontrado.', 'bootstrap-theme')));
        }
        
        $product_type = $product->get_type();
        
        // Skip validation for virtual, composite, bundle, or non-managed stock
        if ($product->is_virtual() || 
            $product_type === 'composite' || 
            $product_type === 'bundle' || 
            !$product->managing_stock()) {
            wp_send_json_success(array('available' => true));
        }
        
        $available_stock = $this->get_available_stock($product);
        $reserved_stock = $this->get_reserved_stock($product->get_id());
        $actual_available = $available_stock - $reserved_stock;
        
        if ($actual_available < $quantity) {
            wp_send_json_error(array(
                'message' => sprintf(
                    __('Stock insuficiente. Solo quedan %d unidades disponibles.', 'bootstrap-theme'),
                    $actual_available
                )
            ));
        }
        
        if ($actual_available <= 1 && $this->is_product_in_other_carts($product->get_id())) {
            wp_send_json_error(array(
                'message' => __('Este producto está siendo procesado por otro usuario. Intenta de nuevo en unos minutos.', 'bootstrap-theme')
            ));
        }
        
        wp_send_json_success(array('available' => true));
    }
    
    /**
     * Enqueue scripts para control de stock
     */
    public function enqueue_stock_control_scripts() {
        if (is_product() || is_shop() || is_product_category()) {
            // Enqueue CSS
            wp_enqueue_style(
                'bootstrap-theme-stock-control',
                get_template_directory_uri() . '/assets/css/stock-control.css',
                array(),
                BOOTSTRAP_THEME_VERSION
            );
            
            // Enqueue JavaScript
            wp_enqueue_script(
                'bootstrap-theme-stock-control',
                get_template_directory_uri() . '/assets/js/stock-control.js',
                array('jquery'),
                BOOTSTRAP_THEME_VERSION,
                true
            );
            
            wp_localize_script('bootstrap-theme-stock-control', 'stockControlAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bootstrap_theme_nonce'),
                'messages' => array(
                    'checking_stock' => __('Verificando disponibilidad...', 'bootstrap-theme'),
                    'stock_unavailable' => __('Producto no disponible', 'bootstrap-theme'),
                    'try_again' => __('Intentar de nuevo', 'bootstrap-theme')
                )
            ));
        }
    }
    
    /**
     * Mostrar advertencia de stock bajo usando template part
     */
    public function display_stock_warning() {
        // Do not show critical stock warning for virtual, composite, bundle products
        global $product;
        if ($product instanceof WC_Product) {
            $product_type = $product->get_type();
            if ($product->is_virtual() || 
                $product_type === 'composite' || 
                $product_type === 'bundle' || 
                !$product->managing_stock()) {
                return;
            }
        }
        // Usar el template part personalizado para mejor presentación
        get_template_part('template-parts/woocommerce/critical-stock-info');
    }
    
    /**
     * Limpiar reservas del carrito al finalizar pedido
     */
    public function clear_cart_reservations($order_id) {
        $session_id = WC()->session->get_customer_id();
        $reservations = get_transient('bootstrap_theme_stock_reservations') ?: array();
        
        if (isset($reservations[$session_id])) {
            unset($reservations[$session_id]);
            set_transient('bootstrap_theme_stock_reservations', $reservations, 30 * MINUTE_IN_SECONDS);
        }
    }
}

// Inicializar la clase INMEDIATAMENTE ya que el archivo solo se carga si WooCommerce existe
if (class_exists('WooCommerce')) {
    new Bootstrap_Theme_Stock_Control();
}