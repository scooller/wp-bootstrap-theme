<?php
/**
 * Gestión de productos de prueba para WooCommerce
 * 
 * @package Bootstrap_Theme
 * @since 1.1.4
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Agregar botones de acción en la página de opciones WooCommerce
 */
add_action('acf/input/admin_footer', 'bootstrap_theme_add_test_products_buttons');

/**
 * Inicializar sistema de productos de prueba
 */
add_action('init', 'bootstrap_theme_init_test_products_system');
function bootstrap_theme_init_test_products_system() {
    // Verificar que WordPress esté completamente cargado
    if (!did_action('init')) {
        return;
    }
    
    // Solo ejecutar si WooCommerce está activo
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Hooks para productos de prueba
    add_action('acf/input/admin_footer', 'bootstrap_theme_add_test_products_buttons');
    add_action('wp_ajax_bootstrap_theme_create_test_products', 'bootstrap_theme_ajax_create_test_products');
    add_action('wp_ajax_bootstrap_theme_delete_test_products', 'bootstrap_theme_ajax_delete_test_products');
}

/**
 * Agregar botones de acción en la página de opciones WooCommerce
 */
function bootstrap_theme_add_test_products_buttons() {
    $screen = get_current_screen();
    if (!$screen || strpos($screen->id, 'bootstrap-theme-options-woocommerce') === false) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Agregar botones después del tab de productos de prueba
        var testTab = $('[data-key="field_wc_test_products_tab"]');
        if (testTab.length) {
            var buttonsHtml = '<div class="bootstrap-theme-test-products-actions" style="margin: 20px 0; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">' +
                '<h4><?php esc_html_e("Acciones para productos de prueba", "bootstrap-theme"); ?></h4>' +
                '<p style="margin-bottom: 15px;"><?php esc_html_e("Usa estos botones para crear o eliminar productos de prueba según la configuración anterior.", "bootstrap-theme"); ?></p>' +
                '<button type="button" id="create-test-products" class="button button-primary" style="margin-right: 10px;">' +
                    '<span class="dashicons dashicons-plus-alt" style="margin-right: 5px;"></span>' +
                    '<?php esc_html_e("Crear productos de prueba", "bootstrap-theme"); ?>' +
                '</button>' +
                '<button type="button" id="delete-test-products" class="button button-secondary">' +
                    '<span class="dashicons dashicons-trash" style="margin-right: 5px;"></span>' +
                    '<?php esc_html_e("Eliminar productos de prueba", "bootstrap-theme"); ?>' +
                '</button>' +
                '<div id="test-products-status" style="margin-top: 15px; padding: 10px; display: none;"></div>' +
                '</div>';
            
            // Insertar después de todos los campos del tab
            var lastField = testTab.nextAll('.acf-field').last();
            if (lastField.length) {
                lastField.after(buttonsHtml);
            } else {
                testTab.after(buttonsHtml);
            }
        }
        
        // Manejar clic en crear productos
        $(document).on('click', '#create-test-products', function(e) {
            e.preventDefault();
            var button = $(this);
            var status = $('#test-products-status');
            
            button.prop('disabled', true).html('<span class="dashicons dashicons-update spin" style="margin-right: 5px;"></span><?php esc_html_e("Creando...", "bootstrap-theme"); ?>');
            status.removeClass('notice-error notice-success').addClass('notice notice-info').html('<?php esc_html_e("Creando productos de prueba...", "bootstrap-theme"); ?>').show();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bootstrap_theme_create_test_products',
                    nonce: '<?php echo wp_create_nonce("bootstrap_theme_test_products"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        status.removeClass('notice-info').addClass('notice-success').html(response.data.message);
                    } else {
                        status.removeClass('notice-info').addClass('notice-error').html(response.data || '<?php esc_html_e("Error al crear productos", "bootstrap-theme"); ?>');
                    }
                },
                error: function() {
                    status.removeClass('notice-info').addClass('notice-error').html('<?php esc_html_e("Error de conexión", "bootstrap-theme"); ?>');
                },
                complete: function() {
                    button.prop('disabled', false).html('<span class="dashicons dashicons-plus-alt" style="margin-right: 5px;"></span><?php esc_html_e("Crear productos de prueba", "bootstrap-theme"); ?>');
                }
            });
        });
        
        // Manejar clic en eliminar productos
        $(document).on('click', '#delete-test-products', function(e) {
            e.preventDefault();
            
            if (!confirm('<?php esc_html_e("¿Estás seguro de que quieres eliminar todos los productos de prueba? Esta acción no se puede deshacer.", "bootstrap-theme"); ?>')) {
                return;
            }
            
            var button = $(this);
            var status = $('#test-products-status');
            
            button.prop('disabled', true).html('<span class="dashicons dashicons-update spin" style="margin-right: 5px;"></span><?php esc_html_e("Eliminando...", "bootstrap-theme"); ?>');
            status.removeClass('notice-error notice-success').addClass('notice notice-info').html('<?php esc_html_e("Eliminando productos de prueba...", "bootstrap-theme"); ?>').show();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bootstrap_theme_delete_test_products',
                    nonce: '<?php echo wp_create_nonce("bootstrap_theme_test_products"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        status.removeClass('notice-info').addClass('notice-success').html(response.data.message);
                    } else {
                        status.removeClass('notice-info').addClass('notice-error').html(response.data || '<?php esc_html_e("Error al eliminar productos", "bootstrap-theme"); ?>');
                    }
                },
                error: function() {
                    status.removeClass('notice-info').addClass('notice-error').html('<?php esc_html_e("Error de conexión", "bootstrap-theme"); ?>');
                },
                complete: function() {
                    button.prop('disabled', false).html('<span class="dashicons dashicons-trash" style="margin-right: 5px;"></span><?php esc_html_e("Eliminar productos de prueba", "bootstrap-theme"); ?>');
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * AJAX: Crear productos de prueba
 */
function bootstrap_theme_ajax_create_test_products() {
    // Verificar nonce
    if (!wp_verify_nonce($_POST['nonce'], 'bootstrap_theme_test_products')) {
        wp_die(__('Acceso denegado', 'bootstrap-theme'));
    }
    
    // Verificar permisos
    if (!current_user_can('manage_woocommerce')) {
        wp_send_json_error(__('No tienes permisos para realizar esta acción', 'bootstrap-theme'));
    }
    
    // Verificar que WooCommerce esté activo
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(__('WooCommerce no está activo', 'bootstrap-theme'));
    }
    
    try {
        $result = bootstrap_theme_create_test_products();
        
        if ($result['success']) {
            wp_send_json_success(array(
                'message' => sprintf(
                    __('Se crearon %d productos de prueba exitosamente en %d categorías.', 'bootstrap-theme'),
                    $result['products_created'],
                    $result['categories_created']
                )
            ));
        } else {
            wp_send_json_error($result['message']);
        }
    } catch (Exception $e) {
        wp_send_json_error(__('Error inesperado: ', 'bootstrap-theme') . $e->getMessage());
    }
}

/**
 * AJAX: Eliminar productos de prueba
 */
function bootstrap_theme_ajax_delete_test_products() {
    // Verificar nonce
    if (!wp_verify_nonce($_POST['nonce'], 'bootstrap_theme_test_products')) {
        wp_die(__('Acceso denegado', 'bootstrap-theme'));
    }
    
    // Verificar permisos
    if (!current_user_can('manage_woocommerce')) {
        wp_send_json_error(__('No tienes permisos para realizar esta acción', 'bootstrap-theme'));
    }
    
    try {
        $result = bootstrap_theme_delete_test_products();
        
        if ($result['success']) {
            wp_send_json_success(array(
                'message' => sprintf(
                    __('Se eliminaron %d productos de prueba exitosamente.', 'bootstrap-theme'),
                    $result['products_deleted']
                )
            ));
        } else {
            wp_send_json_error($result['message']);
        }
    } catch (Exception $e) {
        wp_send_json_error(__('Error inesperado: ', 'bootstrap-theme') . $e->getMessage());
    }
}

/**
 * Crear productos de prueba
 */
function bootstrap_theme_create_test_products() {
    if (!function_exists('bootstrap_theme_get_woocommerce_option')) {
        return array('success' => false, 'message' => __('Función de opciones no disponible', 'bootstrap-theme'));
    }
    
    // Obtener configuración
    $count = (int) bootstrap_theme_get_woocommerce_option('test_products_count') ?: 10;
    $types = bootstrap_theme_get_woocommerce_option('test_product_types') ?: array('simple');
    $categories = bootstrap_theme_get_woocommerce_option('test_categories') ?: array();
    $generate_images = bootstrap_theme_get_woocommerce_option('test_generate_images') ?: true;
    
    // Crear categoría "Prueba" si no existe
    $test_category = wp_insert_term('Prueba', 'product_cat', array(
        'description' => __('Categoría para productos de prueba', 'bootstrap-theme'),
        'slug' => 'prueba-' . time()
    ));
    
    if (!is_wp_error($test_category)) {
        $categories[] = $test_category['term_id'];
    } elseif ($test_category->get_error_code() === 'term_exists') {
        // La categoría ya existe, obtener su ID
        $existing = get_term_by('name', 'Prueba', 'product_cat');
        if ($existing) {
            $categories[] = $existing->term_id;
        }
    }
    
    $products_created = 0;
    $categories_used = count(array_unique($categories));
    
    // Productos de ejemplo
    $sample_products = array(
        array('name' => 'Camiseta Premium', 'price' => 25.99, 'sale_price' => 19.99),
        array('name' => 'Pantalón Deportivo', 'price' => 45.00, 'sale_price' => null),
        array('name' => 'Zapatillas Running', 'price' => 89.99, 'sale_price' => 69.99),
        array('name' => 'Chaqueta Impermeable', 'price' => 120.00, 'sale_price' => null),
        array('name' => 'Gorra Deportiva', 'price' => 15.50, 'sale_price' => 12.99),
        array('name' => 'Mochila Urbana', 'price' => 55.00, 'sale_price' => null),
        array('name' => 'Reloj Deportivo', 'price' => 199.99, 'sale_price' => 149.99),
        array('name' => 'Auriculares Bluetooth', 'price' => 79.99, 'sale_price' => null),
        array('name' => 'Botella Térmica', 'price' => 22.50, 'sale_price' => 18.99),
        array('name' => 'Toalla de Microfibra', 'price' => 12.99, 'sale_price' => null)
    );
    
    for ($i = 0; $i < $count; $i++) {
        $sample = $sample_products[$i % count($sample_products)];
        $product_type = $types[array_rand($types)];
        
        // Crear producto
        $product = new WC_Product_Simple();
        
        // Configuración básica
        $product->set_name($sample['name'] . ' #' . ($i + 1));
        $product->set_slug(sanitize_title($sample['name'] . '-' . ($i + 1) . '-prueba'));
        $product->set_description(__('Este es un producto de prueba generado automáticamente. ', 'bootstrap-theme') . 
            __('Incluye características completas para testing de la tienda.', 'bootstrap-theme'));
        $product->set_short_description(__('Producto de prueba con todas las características.', 'bootstrap-theme'));
        
        // Precios
        $product->set_regular_price($sample['price']);
        if ($sample['sale_price']) {
            $product->set_sale_price($sample['sale_price']);
        }
        
        // Stock
        $product->set_manage_stock(true);
        $product->set_stock_quantity(rand(5, 100));
        $product->set_stock_status('instock');
        
        // SKU único
        $product->set_sku('TEST-' . strtoupper(substr(md5(uniqid()), 0, 8)));
        
        // Peso y dimensiones
        $product->set_weight(rand(100, 2000) / 100); // 1.00 - 20.00 kg
        $product->set_length(rand(10, 50));
        $product->set_width(rand(10, 50));
        $product->set_height(rand(5, 30));
        
        // Configurar tipo específico
        switch ($product_type) {
            case 'virtual':
                $product->set_virtual(true);
                break;
            case 'downloadable':
                $product->set_downloadable(true);
                $product->set_virtual(true);
                break;
        }
        
        // Guardar producto
        $product_id = $product->save();
        
        if ($product_id) {
            // Asignar categorías
            if (!empty($categories)) {
                wp_set_object_terms($product_id, $categories, 'product_cat');
            }
            
            // Generar imagen si está habilitado
            if ($generate_images) {
                bootstrap_theme_generate_product_image($product_id, $sample['name']);
            }
            
            // Marcar como producto de prueba
            update_post_meta($product_id, '_bootstrap_theme_test_product', 'yes');
            
            $products_created++;
        }
    }
    
    return array(
        'success' => true,
        'products_created' => $products_created,
        'categories_created' => $categories_used
    );
}

/**
 * Eliminar productos de prueba
 */
function bootstrap_theme_delete_test_products() {
    // Buscar productos marcados como de prueba
    $test_products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_key' => '_bootstrap_theme_test_product',
        'meta_value' => 'yes',
        'post_status' => 'any'
    ));
    
    $products_deleted = 0;
    
    foreach ($test_products as $product) {
        // Eliminar imágenes asociadas
        $thumbnail_id = get_post_thumbnail_id($product->ID);
        if ($thumbnail_id) {
            wp_delete_attachment($thumbnail_id, true);
        }
        
        // Eliminar producto
        if (wp_delete_post($product->ID, true)) {
            $products_deleted++;
        }
    }
    
    return array(
        'success' => true,
        'products_deleted' => $products_deleted
    );
}

/**
 * Generar imagen placeholder para producto
 */
function bootstrap_theme_generate_product_image($product_id, $product_name) {
    // Crear imagen placeholder usando GD
    $width = 600;
    $height = 600;
    
    // Verificar si GD está disponible
    if (!extension_loaded('gd')) {
        return false;
    }
    
    // Crear imagen
    $image = imagecreate($width, $height);
    
    // Colores
    $colors = array(
        array(52, 144, 220),   // Azul
        array(92, 184, 92),    // Verde
        array(240, 173, 78),   // Naranja
        array(217, 83, 79),    // Rojo
        array(91, 192, 222),   // Celeste
        array(138, 109, 59)    // Marrón
    );
    
    $color_set = $colors[array_rand($colors)];
    $bg_color = imagecolorallocate($image, $color_set[0], $color_set[1], $color_set[2]);
    $text_color = imagecolorallocate($image, 255, 255, 255);
    
    // Llenar fondo
    imagefill($image, 0, 0, $bg_color);
    
    // Agregar texto
    $font_size = 5;
    $text = strtoupper(substr($product_name, 0, 15));
    $text_width = imagefontwidth($font_size) * strlen($text);
    $text_height = imagefontheight($font_size);
    
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $text, $text_color);
    
    // Agregar marca de agua "PRUEBA"
    $watermark = "PRUEBA";
    $wm_width = imagefontwidth(3) * strlen($watermark);
    $wm_x = ($width - $wm_width) / 2;
    $wm_y = $height - 50;
    
    imagestring($image, 3, $wm_x, $wm_y, $watermark, $text_color);
    
    // Crear directorio si no existe
    $upload_dir = wp_upload_dir();
    $test_dir = $upload_dir['basedir'] . '/bootstrap-theme-test-images';
    
    if (!file_exists($test_dir)) {
        wp_mkdir_p($test_dir);
    }
    
    // Guardar imagen
    $filename = 'product-' . $product_id . '-' . time() . '.png';
    $filepath = $test_dir . '/' . $filename;
    
    if (imagepng($image, $filepath)) {
        imagedestroy($image);
        
        // Crear attachment en WordPress
        $attachment = array(
            'guid' => $upload_dir['baseurl'] . '/bootstrap-theme-test-images/' . $filename,
            'post_mime_type' => 'image/png',
            'post_title' => __('Imagen de producto de prueba', 'bootstrap-theme') . ' - ' . $product_name,
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attach_id = wp_insert_attachment($attachment, $filepath);
        
        if ($attach_id) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
            wp_update_attachment_metadata($attach_id, $attach_data);
            
            // Asignar como imagen destacada del producto
            set_post_thumbnail($product_id, $attach_id);
            
            // Marcar como imagen de prueba
            update_post_meta($attach_id, '_bootstrap_theme_test_image', 'yes');
        }
        
        return $attach_id;
    }
    
    imagedestroy($image);
    return false;
}