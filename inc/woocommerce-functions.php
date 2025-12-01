<?php
/**
 * WooCommerce functions and integrations
 *
 * @package Bootstrap_Theme
 * @since 1.1.4
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inicializar funciones de WooCommerce después de que WordPress y WooCommerce estén cargados
 */
add_action('init', 'bootstrap_theme_init_woocommerce_functions');
function bootstrap_theme_init_woocommerce_functions() {
    // Verificar que WordPress esté completamente cargado
    if (!did_action('init')) {
        return;
    }
    
    // Solo ejecutar si WooCommerce está activo
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Bloquear acceso directo a categorías ocultas con error 403
    add_action('template_redirect', 'bootstrap_theme_block_hidden_categories');
    
    // Ajustar query principal (per page) con alta prioridad para sobreescribir otros ajustes
    add_action('pre_get_posts', 'bootstrap_theme_adjust_shop_query', 999);
    // Asegurar per_page también en consultas de productos de WooCommerce
    add_action('woocommerce_product_query', 'bootstrap_theme_adjust_wc_product_query', 999);
    // Hook adicional para forzar la paginación en WooCommerce
    add_action('woocommerce_shortcode_products_query', 'bootstrap_theme_adjust_wc_product_query', 999);

        // AJAX handlers para carrito
        add_action('wp_ajax_add_to_cart', 'bootstrap_theme_ajax_add_to_cart');
        add_action('wp_ajax_nopriv_add_to_cart', 'bootstrap_theme_ajax_add_to_cart');
        add_action('wp_ajax_get_cart_count', 'bootstrap_theme_get_cart_count');
        add_action('wp_ajax_nopriv_get_cart_count', 'bootstrap_theme_get_cart_count');
        add_action('wp_ajax_get_cart_offcanvas_content', 'bootstrap_theme_get_cart_offcanvas_content');
        add_action('wp_ajax_nopriv_get_cart_offcanvas_content', 'bootstrap_theme_get_cart_offcanvas_content');
    
        // Widgets y sidebar de la tienda
        add_action('widgets_init', 'bootstrap_theme_shop_widgets_init');
    
        // Badges de productos
        add_action('woocommerce_before_shop_loop_item', 'bootstrap_theme_product_badges', 5);
    
        // Filtros de productos
        add_filter('woocommerce_product_tabs', 'bootstrap_theme_product_tabs', 98);
        // Compatibilidad adicional para columnas
        add_filter('woocommerce_loop_columns', 'bootstrap_theme_loop_columns', 999);
    
        // Remover sidebar por defecto de WooCommerce
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
}

/**
 * Bloquear acceso directo a categorías ocultas con error 403
 */
function bootstrap_theme_block_hidden_categories() {
    if (is_tax('product_cat')) {
        $term = get_queried_object();
        if ($term && isset($term->term_id)) {
            $hidden = bootstrap_theme_get_hidden_product_cat_ids();
            if (in_array($term->term_id, $hidden, true)) {
                status_header(403);
                nocache_headers();
                $msg = function_exists('bootstrap_theme_get_option') ? bootstrap_theme_get_option('403_message') : '';
                echo '<div class="container py-5"><div class="alert alert-danger text-center">';
                if (!empty($msg)) {
                    echo wp_kses_post($msg);
                } else {
                    echo '<strong>';
                    esc_html_e('Acceso prohibido: esta categoría no está disponible.', 'bootstrap-theme');
                    echo '</strong>';
                }
                echo '</div></div>';
                exit;
            }
        }
    }
}
/**
 * Obtener IDs de categorías ocultas desde opciones ACF
 */
function bootstrap_theme_get_hidden_product_cat_ids() {
    if (function_exists('bootstrap_theme_get_woocommerce_option')) {
        $ids = bootstrap_theme_get_woocommerce_option('hidden_categories');
        if (is_array($ids)) {
            return array_map('intval', $ids);
        }
    }
    return array();
}

/**
 * Excluir categorías ocultas en el buscador de productos
 */
add_filter('woocommerce_product_categories_widget_args', function($args) {
    $exclude = bootstrap_theme_get_hidden_product_cat_ids();
    if (!empty($exclude)) {
        $args['exclude'] = isset($args['exclude']) ? array_merge((array)$args['exclude'], $exclude) : $exclude;
    }
    return $args;
});

/**
 * Excluir categorías ocultas en el widget de categorías de producto
 */
// Nota: filtro anterior ya maneja la exclusión en el widget; evitamos duplicados.

/**
 * Excluir categorías ocultas en menús y listados de categorías
 */
add_filter('get_terms_args', function($args, $taxonomies) {
    if (in_array('product_cat', (array)$taxonomies, true)) {
        $exclude = bootstrap_theme_get_hidden_product_cat_ids();
        if (!empty($exclude)) {
            $args['exclude'] = isset($args['exclude']) ? array_merge((array)$args['exclude'], $exclude) : $exclude;
        }
    }
    return $args;
}, 10, 2);

/**
 * WooCommerce setup
 */
function bootstrap_theme_woocommerce_setup()
{
    // Conservar estilos por defecto de WooCommerce.
    // Si en el futuro añadimos un CSS propio, podemos condicionar su activación aquí.
    add_filter('woocommerce_enqueue_styles', function($styles) {
        return $styles; // no deshabilitar los estilos nativos
    });

    // Add custom WooCommerce styles
    add_action('wp_enqueue_scripts', 'bootstrap_theme_woocommerce_styles');

    // Modify WooCommerce hooks
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    // Reemplazar paginación WooCommerce por Bootstrap personalizada
    remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
    add_action('woocommerce_after_shop_loop', 'bootstrap_theme_woocommerce_bootstrap_pagination', 10);

    add_action('woocommerce_before_main_content', 'bootstrap_theme_woocommerce_wrapper_start', 10);
    add_action('woocommerce_after_main_content', 'bootstrap_theme_woocommerce_wrapper_end', 10);

    // Modify product loop
    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
    add_action('woocommerce_shop_loop_item_title', 'bootstrap_theme_product_title', 10);

    // Change number of products per row
    add_filter('loop_shop_columns', 'bootstrap_theme_loop_columns', 999);

    // Change number of products per page
    add_filter('loop_shop_per_page', 'bootstrap_theme_products_per_page', 20);
    
    // Control related products and product navigation display through template logic
    // See template-parts/woocommerce/single-product.php and single.php for implementation
}
add_action('after_setup_theme', 'bootstrap_theme_woocommerce_setup');

/**
 * Prioritize featured products first in ALL product queries (shop, categories, related, etc.).
 *
 * Implementation: LEFT JOIN product_visibility term 'featured' and prepend ORDER BY clause
 * so featured appear first while preserving the existing order afterwards.
 *
 * Scope: frontend only, post_type=product, non-admin.
 */
add_filter('posts_clauses', 'bootstrap_theme_order_featured_first', 20, 2);
function bootstrap_theme_order_featured_first($clauses, $query) {
    if (is_admin()) {
        return $clauses;
    }
    $post_type = $query->get('post_type');
    // Afectar únicamente consultas de productos (incluye relacionadas/upsells que usan WP_Query)
    if ($post_type && $post_type !== 'product') {
        return $clauses;
    }
    // Muchas consultas no definen post_type explícito; si estamos en shop/tax/productos, continuar
    if (!$post_type) {
        if (!(function_exists('is_shop') && is_shop())
            && !(function_exists('is_product_category') && is_product_category())
            && !(function_exists('is_product_tag') && is_product_tag())
        ) {
            // Aún así, si la consulta incluye tax_query de product visibility/ category, podría ser productos
            // Para no sobre-optimizar, salir si no es explícito
        }
    }

    global $wpdb;
    // Evitar duplicados por múltiples term_relationships: usar subconsulta filtrada a 'featured'
    if (strpos($clauses['join'], 'bt_feat') === false) {
        $sub = "SELECT tr.object_id FROM {$wpdb->term_relationships} tr "
             . "INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'product_visibility' "
             . "INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id AND t.slug = 'featured'";
        $clauses['join'] .= " \nLEFT JOIN ( $sub ) bt_feat ON bt_feat.object_id = {$wpdb->posts}.ID\n";
    }

    // Asegurar GROUP BY para evitar filas duplicadas en singles
    if (empty($clauses['groupby'])) {
        $clauses['groupby'] = "{$wpdb->posts}.ID";
    } elseif (strpos($clauses['groupby'], "{$wpdb->posts}.ID") === false) {
        $clauses['groupby'] .= ", {$wpdb->posts}.ID";
    }

    // Prepend featured first ordering
    $featured_order = ' (bt_feat.object_id IS NOT NULL) DESC ';
    if (!empty($clauses['orderby'])) {
        // Evitar duplicar si ya lo agregamos
        if (strpos($clauses['orderby'], 'bt_feat.object_id') === false) {
            $clauses['orderby'] = $featured_order . ', ' . $clauses['orderby'];
        }
    } else {
        $clauses['orderby'] = $featured_order;
    }

    return $clauses;
}

/**
 * Enqueue WooCommerce styles
 */
function bootstrap_theme_woocommerce_styles()
{
    $css = get_template_directory() . '/assets/css/woocommerce.css';
    if (file_exists($css)) {
        wp_enqueue_style(
            'bootstrap-theme-woocommerce',
            get_template_directory_uri() . '/assets/css/woocommerce.css',
            array('woocommerce-general'),
            BOOTSTRAP_THEME_VERSION
        );
    }
}

/**
 * Mostrar buscador en el catálogo (opcional desde opciones del tema)
 * Se renderiza en los archivos de tienda, categorías y tags de producto.
 */
// Insertar el buscador entre el contador de resultados (20) y el orden (30)
add_action('woocommerce_before_shop_loop', 'bootstrap_theme_catalog_search', 25);
function bootstrap_theme_catalog_search() {
    if (!function_exists('bootstrap_theme_get_woocommerce_option')) return;
    // Mostrar solo en vistas de catálogo
    if (!(function_exists('is_shop') && is_shop()) && !(function_exists('is_product_category') && is_product_category()) && !(function_exists('is_product_tag') && is_product_tag())) {
        return;
    }
    $show = bootstrap_theme_get_woocommerce_option('catalog_show_search'); // busca con prefijo
    $show_bool = ($show === true || $show === 1 || $show === '1');
    if (!$show_bool) return;

    // Usar URL actual para mantener contexto (categoría/tag); GET limpia con s y post_type=product
    $action = esc_url(add_query_arg(array()));
    $query = get_search_query();
    ob_start();
    ?>
    <div class="woocommerce-catalog-search mb-3">
        <form role="search" method="get" action="<?php echo $action; ?>" class="d-flex gap-2">
            <input type="search" name="s" value="<?php echo esc_attr($query); ?>" class="form-control" placeholder="<?php echo esc_attr__('Buscar productos…', 'bootstrap-theme'); ?>" aria-label="<?php echo esc_attr__('Buscar productos', 'bootstrap-theme'); ?>" />
            <input type="hidden" name="post_type" value="product" />
            <button type="submit" class="btn btn-primary" aria-label="<?php echo esc_html__('Buscar', 'bootstrap-theme'); ?>">
                <svg class="icon me-2">
                    <use xlink:href="#fa-magnifying-glass"></use>
                </svg>
                <span class="visually-hidden"><?php echo esc_html__('Buscar', 'bootstrap-theme'); ?></span>
            </button>
        </form>
    </div>
    <?php
    echo ob_get_clean();
}

/**
 * For virtual, composite, bundle, or non-managed stock products, remove max attribute on quantity input
 * to avoid browser validation errors like "no sea mayor de -1".
 */
add_filter('woocommerce_quantity_input_args', function($args, $product) {
    if (!$product instanceof WC_Product) {
        return $args;
    }
    
    // Get product type
    $product_type = $product->get_type();
    
    // Relajar validaciones para productos virtuales, compuestos, bundles, o que no gestionan stock
    if ($product->is_virtual() || 
        $product_type === 'composite' || 
        $product_type === 'bundle' || 
        !$product->managing_stock()) {
        // Definir explícitamente la clave para evitar Notice en plantillas de WooCommerce
        $args['max_value'] = '';
        $args['min_value'] = isset($args['min_value']) ? max(1, (int) $args['min_value']) : 1;
        $args['step'] = isset($args['step']) ? $args['step'] : 1;
    }
    return $args;
}, 10, 2);

// Filtro de respaldo: asegura que el max quede vacío para productos virtuales, compuestos o bundles en cualquier contexto
add_filter('woocommerce_quantity_input_max', function($max, $product) {
    if ($product instanceof WC_Product) {
        $product_type = $product->get_type();
        if ($product->is_virtual() || 
            $product_type === 'composite' || 
            $product_type === 'bundle' || 
            !$product->managing_stock()) {
            return '';
        }
    }
    return $max;
}, 10, 2);

/**
 * Para variaciones virtuales, compuestas o que no gestionan stock, eliminar max_qty para evitar max=-1 en el input.
 */
add_filter('woocommerce_available_variation', function($variation_data, $product, $variation) {
    if ($variation instanceof WC_Product) {
        $product_type = $product->get_type();
        $variation_type = $variation->get_type();
        
        if ($variation->is_virtual() || 
            $product_type === 'composite' || 
            $product_type === 'bundle' ||
            $variation_type === 'composite' ||
            $variation_type === 'bundle' ||
            !$variation->managing_stock()) {
            // Quitar límite máximo en el selector de cantidad
            $variation_data['max_qty'] = '';
            // Asegurar mínimos razonables
            if (!isset($variation_data['min_qty']) || (int)$variation_data['min_qty'] < 1) {
                $variation_data['min_qty'] = 1;
            }
            if (!isset($variation_data['step']) || (int)$variation_data['step'] < 1) {
                $variation_data['step'] = 1;
            }
        }
    }
    return $variation_data;
}, 10, 3);

/**
 * Prevent stock HTML from showing warnings for virtual, composite, bundle products or products not managing stock.
 */
add_filter('woocommerce_get_stock_html', function($html, $product) {
    if ($product instanceof WC_Product) {
        $product_type = $product->get_type();
        if ($product->is_virtual() || 
            $product_type === 'composite' || 
            $product_type === 'bundle' || 
            !$product->managing_stock()) {
            return '';
        }
    }
    return $html;
}, 10, 2);

/**
 * WooCommerce content wrapper start
 */
function bootstrap_theme_woocommerce_wrapper_start()
{
    $container_class = function_exists('bootstrap_theme_get_option') ? bootstrap_theme_get_option('container_width') : 'container';
    if (empty($container_class)) { $container_class = 'container'; }
    // Determinar si mostrar sidebar
    $show_sidebar = bootstrap_theme_get_option('show_sidebar');
    $show_sidebar_bool = ($show_sidebar === '1' || $show_sidebar === 1 || $show_sidebar === true);
    if ($show_sidebar_bool) {
        echo '<div class="' . esc_attr($container_class) . '"><div class="row"><div class="col-lg-9">';
    } else {
        echo '<div class="' . esc_attr($container_class) . '">';
    }
}

/**
 * WooCommerce content wrapper end
 */
function bootstrap_theme_woocommerce_wrapper_end()
{
    // Determinar si mostrar sidebar
    $show_sidebar = bootstrap_theme_get_option('show_sidebar');
    $show_sidebar_bool = ($show_sidebar === '1' || $show_sidebar === 1 || $show_sidebar === true);
    if ($show_sidebar_bool) {
        echo '</div><div class="col-lg-3">';
        if (is_shop() || is_product_category() || is_product_tag()) {
            dynamic_sidebar('shop-sidebar');
        }
    }
    echo '</div></div></div>';
}

/**
 * Custom product title with Bootstrap classes
 */
function bootstrap_theme_product_title()
{
    echo '<h3 class="woocommerce-loop-product__title h6 mb-2"><a href="' . get_the_permalink() . '" class="text-decoration-none">' . get_the_title() . '</a></h3>';
}

/**
 * Set number of products per row
 */
function bootstrap_theme_loop_columns($columns)
{
    if (function_exists('bootstrap_theme_get_woocommerce_option')) {
        $cols = (int) bootstrap_theme_get_woocommerce_option('products_per_row');
        if ($cols > 0) return $cols;
    }
    return is_numeric($columns) && $columns > 0 ? (int)$columns : 3;
}

/**
 * Set number of products per page
 */
function bootstrap_theme_products_per_page($per_page)
{
    if (function_exists('bootstrap_theme_get_woocommerce_option')) {
        $pp = (int) bootstrap_theme_get_woocommerce_option('products_per_page');
        if ($pp > 0) return $pp;
    }
    return is_numeric($per_page) && $per_page > 0 ? (int)$per_page : 12;
}

/**
 * Force per page on main WooCommerce archives through pre_get_posts
 * 
 * @since 1.1.5
 * 
 * LESSONS LEARNED:
 * - pre_get_posts hook is essential for modifying WooCommerce shop queries
 * - Offset calculation must be: ($current_page - 1) * $posts_per_page
 * - Global variable $bootstrap_theme_current_page ensures page state communication
 * - Multiple detection methods needed: is_shop(), is_product_category(), post_type check
 * - no_found_rows must be false to enable pagination calculation
 * - Main query check (is_main_query()) prevents affecting admin/ajax queries
 */
function bootstrap_theme_adjust_shop_query($q) {
    // Solo en frontend y query principal
    if (is_admin() || !$q->is_main_query()) {
        return;
    }
    
    // Detectar páginas de WooCommerce de manera más específica
    $is_woo_page = false;
    if (function_exists('is_shop') && is_shop()) {
        $is_woo_page = true;
    } elseif (function_exists('is_product_category') && is_product_category()) {
        $is_woo_page = true;
    } elseif (function_exists('is_product_tag') && is_product_tag()) {
        $is_woo_page = true;
    } elseif ($q->get('post_type') === 'product') {
        $is_woo_page = true;
    }

    if ($is_woo_page && function_exists('bootstrap_theme_get_woocommerce_option')) {
        // Obtener página actual
        $current_paged = max(1, (int) $q->get('paged'), (int) $q->get('page'));
        
        // Guardar en global para el paginador
        global $bootstrap_theme_current_page;
        $bootstrap_theme_current_page = $current_paged;
        
        if ($current_paged > 1) {
            $q->set('paged', $current_paged);
        }

        // Obtener productos por página desde opciones
        $pp = (int) bootstrap_theme_get_woocommerce_option('products_per_page');
        
        if ($pp > 0) {
            $q->set('posts_per_page', $pp);
            
            // CRÍTICO: Establecer offset para paginación correcta
            if ($current_paged > 1) {
                $offset = ($current_paged - 1) * $pp;
                $q->set('offset', $offset);
            }
            
            // Asegurar que WordPress calcule el total para paginación
            $q->set('no_found_rows', false);
        }
        
        // Aplicar ordenamiento personalizado desde opciones del tema
        $orderby = bootstrap_theme_get_woocommerce_option('default_orderby');
        $order = bootstrap_theme_get_woocommerce_option('default_order');
        
        if (!empty($orderby) && !isset($_GET['orderby'])) {
            // Solo aplicar si no hay parámetro de ordenamiento en la URL
            switch ($orderby) {
                case 'title':
                    $q->set('orderby', 'title');
                    $q->set('order', $order ?: 'ASC');
                    break;
                case 'date':
                    $q->set('orderby', 'date');
                    $q->set('order', $order ?: 'DESC');
                    break;
                case 'modified':
                    $q->set('orderby', 'modified');
                    $q->set('order', $order ?: 'DESC');
                    break;
                case 'price':
                    $q->set('orderby', 'meta_value_num');
                    $q->set('meta_key', '_price');
                    $q->set('order', $order ?: 'ASC');
                    break;
                case 'popularity':
                    $q->set('orderby', 'meta_value_num');
                    $q->set('meta_key', 'total_sales');
                    $q->set('order', $order ?: 'DESC');
                    break;
                case 'rating':
                    $q->set('orderby', 'meta_value_num');
                    $q->set('meta_key', '_wc_average_rating');
                    $q->set('order', $order ?: 'DESC');
                    break;
                case 'sku':
                    $q->set('orderby', 'meta_value');
                    $q->set('meta_key', '_sku');
                    $q->set('order', $order ?: 'ASC');
                    break;
                case 'rand':
                    $q->set('orderby', 'rand');
                    break;
                case 'menu_order':
                default:
                    $q->set('orderby', 'menu_order title');
                    $q->set('order', $order ?: 'ASC');
                    break;
            }
        }
    }
}

/**
 * Force per page on WC product queries (more reliable with some setups)
 * 
 * @since 1.1.5
 * 
 * LESSONS LEARNED:
 * - woocommerce_product_query hook handles both arrays and WP_Query objects
 * - Array handling needed for shortcode queries, object handling for regular queries
 * - Always return modified array when dealing with array parameter
 * - Offset calculation remains the same: ($current_page - 1) * $posts_per_page
 */
function bootstrap_theme_adjust_wc_product_query($q) {
    
    // Si es un array (desde woocommerce_shortcode_products_query), manejarlo diferente
    if (is_array($q)) {
        if (function_exists('bootstrap_theme_get_woocommerce_option')) {
            $pp = (int) bootstrap_theme_get_woocommerce_option('products_per_page');
            if ($pp > 0) {
                $q['posts_per_page'] = $pp;
                
                // Obtener página actual para offset
                $current_page = max(1, get_query_var('paged'), get_query_var('page'));
                if ($current_page > 1) {
                    $offset = ($current_page - 1) * $pp;
                    $q['offset'] = $offset;
                }
            }
            
            // Aplicar ordenamiento personalizado si no hay parámetro en URL
            if (!isset($_GET['orderby'])) {
                $orderby = bootstrap_theme_get_woocommerce_option('default_orderby');
                $order = bootstrap_theme_get_woocommerce_option('default_order');
                
                if (!empty($orderby)) {
                    switch ($orderby) {
                        case 'title':
                            $q['orderby'] = 'title';
                            $q['order'] = $order ?: 'ASC';
                            break;
                        case 'date':
                            $q['orderby'] = 'date';
                            $q['order'] = $order ?: 'DESC';
                            break;
                        case 'modified':
                            $q['orderby'] = 'modified';
                            $q['order'] = $order ?: 'DESC';
                            break;
                        case 'price':
                            $q['orderby'] = 'meta_value_num';
                            $q['meta_key'] = '_price';
                            $q['order'] = $order ?: 'ASC';
                            break;
                        case 'popularity':
                            $q['orderby'] = 'meta_value_num';
                            $q['meta_key'] = 'total_sales';
                            $q['order'] = $order ?: 'DESC';
                            break;
                        case 'rating':
                            $q['orderby'] = 'meta_value_num';
                            $q['meta_key'] = '_wc_average_rating';
                            $q['order'] = $order ?: 'DESC';
                            break;
                        case 'sku':
                            $q['orderby'] = 'meta_value';
                            $q['meta_key'] = '_sku';
                            $q['order'] = $order ?: 'ASC';
                            break;
                        case 'rand':
                            $q['orderby'] = 'rand';
                            break;
                        case 'menu_order':
                        default:
                            $q['orderby'] = 'menu_order title';
                            $q['order'] = $order ?: 'ASC';
                            break;
                    }
                }
            }
        }
        return $q; // Retornar el array modificado
    }
    
    // Si es un objeto WP_Query, usar el método set()
    if (is_object($q) && method_exists($q, 'set')) {
        if (function_exists('bootstrap_theme_get_woocommerce_option')) {
            $pp = (int) bootstrap_theme_get_woocommerce_option('products_per_page');
            if ($pp > 0) {
                $q->set('posts_per_page', $pp);
            }
            
            // Aplicar ordenamiento personalizado si no hay parámetro en URL
            if (!isset($_GET['orderby'])) {
                $orderby = bootstrap_theme_get_woocommerce_option('default_orderby');
                $order = bootstrap_theme_get_woocommerce_option('default_order');
                
                if (!empty($orderby)) {
                    switch ($orderby) {
                        case 'title':
                            $q->set('orderby', 'title');
                            $q->set('order', $order ?: 'ASC');
                            break;
                        case 'date':
                            $q->set('orderby', 'date');
                            $q->set('order', $order ?: 'DESC');
                            break;
                        case 'modified':
                            $q->set('orderby', 'modified');
                            $q->set('order', $order ?: 'DESC');
                            break;
                        case 'price':
                            $q->set('orderby', 'meta_value_num');
                            $q->set('meta_key', '_price');
                            $q->set('order', $order ?: 'ASC');
                            break;
                        case 'popularity':
                            $q->set('orderby', 'meta_value_num');
                            $q->set('meta_key', 'total_sales');
                            $q->set('order', $order ?: 'DESC');
                            break;
                        case 'rating':
                            $q->set('orderby', 'meta_value_num');
                            $q->set('meta_key', '_wc_average_rating');
                            $q->set('order', $order ?: 'DESC');
                            break;
                        case 'sku':
                            $q->set('orderby', 'meta_value');
                            $q->set('meta_key', '_sku');
                            $q->set('order', $order ?: 'ASC');
                            break;
                        case 'rand':
                            $q->set('orderby', 'rand');
                            break;
                        case 'menu_order':
                        default:
                            $q->set('orderby', 'menu_order title');
                            $q->set('order', $order ?: 'ASC');
                            break;
                    }
                }
            }
        }
    }
    
    return $q;
}

/**
 * Customize WooCommerce breadcrumbs
 */
function bootstrap_theme_woocommerce_breadcrumbs()
{
    return array(
        'delimiter'   => ' <i class="fas fa-chevron-right mx-2"></i> ',
        'wrap_before' => '<nav aria-label="breadcrumb"><ol class="breadcrumb">',
        'wrap_after'  => '</ol></nav>',
        'before'      => '<li class="breadcrumb-item">',
        'after'       => '</li>',
        'home'        => _x('Home', 'breadcrumb', 'bootstrap-theme'),
    );
}
add_filter('woocommerce_breadcrumb_defaults', 'bootstrap_theme_woocommerce_breadcrumbs');

/**
 * Add Bootstrap classes to WooCommerce forms
 */
function bootstrap_theme_woocommerce_form_field_args($args, $key, $value)
{
    // Personalización para el campo teléfono de Chile
    if ($key === 'billing_phone') {
        $args['input_group'] = true;
        $args['input_group_text'] = '+56';
        $args['inputmode'] = 'numeric';
        $args['pattern'] = '[0-9]{9}';
        $args['maxlength'] = 9;
        $args['minlength'] = 9;
        $args['custom_attributes']['inputmode'] = 'numeric';
        $args['custom_attributes']['pattern'] = '[0-9]{9}';
        $args['custom_attributes']['maxlength'] = 9;
        $args['custom_attributes']['minlength'] = 9;
        $args['placeholder'] = '912345678';
        if (!isset($args['input_class'])) $args['input_class'] = array();
        $args['input_class'][] = 'form-control';
    } else {
        switch ($args['type']) {
            case 'select':
                if (!isset($args['input_class'])) $args['input_class'] = array();
                $args['input_class'][] = 'form-select';
                break;
            case 'textarea':
                if (!isset($args['input_class'])) $args['input_class'] = array();
                $args['input_class'][] = 'form-control';
                break;
            case 'checkbox':
                if (!isset($args['input_class'])) $args['input_class'] = array();
                $args['input_class'][] = 'form-check-input';
                break;
            case 'radio':
                if (!isset($args['input_class'])) $args['input_class'] = array();
                $args['input_class'][] = 'form-check-input';
                break;
            default:
                if (!isset($args['input_class'])) $args['input_class'] = array();
                $args['input_class'][] = 'form-control';
                break;
        }
    }
    return $args;
}


// Asegurar input group Bootstrap para billing_phone usando woocommerce_form_field
add_filter('woocommerce_form_field', function($field, $key, $args, $value) {
    if ($key === 'billing_phone') {
        // Construir clases del wrapper
        $wrapper_classes = array('form-row');
        if (!empty($args['class'])) {
            $wrapper_classes = array_merge($wrapper_classes, (array)$args['class']);
        }
        if (!empty($args['required'])) {
            $wrapper_classes[] = 'validate-required';
        }
        $wrapper_class = esc_attr(implode(' ', $wrapper_classes));
        
        // Construir label
        $label = '';
        if (!empty($args['label'])) {
            $label_id = !empty($args['id']) ? esc_attr($args['id']) : '';
            $label_class = !empty($args['label_class']) ? ' ' . esc_attr(implode(' ', (array)$args['label_class'])) : '';
            $required = !empty($args['required']) ? ' <abbr class="required" title="required">*</abbr>' : '';
            $label = '<label for="' . $label_id . '" class="woocommerce-form__label' . $label_class . '">' . esc_html($args['label']) . $required . '</label>';
        }
        
        // Extraer el input del $field
        if (preg_match('/(<input[^>]+>)/', $field, $matches)) {
            $input = $matches[1];
            $input_group = '<div class="input-group"><span class="input-group-text">+56</span>' . $input . '</div>';
            $field = '<p class="' . $wrapper_class . '">' . $label . $input_group . '</p>';
        }
    }
    return $field;
}, 10, 4);

// Ajustar el campo billing_phone en woocommerce_checkout_fields para validación y clases
add_filter('woocommerce_checkout_fields', function($fields) {
    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['label'] = __('Teléfono', 'bootstrap-theme');
        $fields['billing']['billing_phone']['placeholder'] = '912345678';
        $fields['billing']['billing_phone']['required'] = true;
        $fields['billing']['billing_phone']['inputmode'] = 'numeric';
        $fields['billing']['billing_phone']['pattern'] = '[0-9]{9}';
        $fields['billing']['billing_phone']['maxlength'] = 9;
        $fields['billing']['billing_phone']['minlength'] = 9;
        $fields['billing']['billing_phone']['custom_attributes'] = array(
            'inputmode' => 'numeric',
            'pattern' => '[0-9]{9}',
            'maxlength' => 9,
            'minlength' => 9,
        );
        $fields['billing']['billing_phone']['class'] = array('form-row-wide');
        $fields['billing']['billing_phone']['input_class'] = array('form-control');
    }
    return $fields;
}, 20);

/**
 * Add to cart AJAX handler
 */
function bootstrap_theme_ajax_add_to_cart()
{
    check_ajax_referer('bootstrap_theme_nonce', 'nonce');

    $product_id = absint($_POST['product_id']);
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);

    $cart_item_data = array();
    $variation_id = absint($_POST['variation_id']);
    $variation = array();

    if ($variation_id) {
        $variation = $_POST['variation'];
    }

    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);

    if ($passed_validation && false !== WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $cart_item_data)) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);

        wp_send_json_success(array(
            'message' => __('Product added to cart successfully!', 'bootstrap-theme'),
            'cart_count' => WC()->cart->get_cart_contents_count(),
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Failed to add product to cart.', 'bootstrap-theme'),
        ));
    }
}

/**
 * Get cart count AJAX handler
 */
function bootstrap_theme_get_cart_count()
{
    check_ajax_referer('bootstrap_theme_nonce', 'nonce');

    wp_send_json_success(WC()->cart->get_cart_contents_count());
}

/**
 * Get cart off-canvas content AJAX handler
 */
function bootstrap_theme_get_cart_offcanvas_content()
{
    check_ajax_referer('bootstrap_theme_nonce', 'nonce');

    // Asegurar que los totales (incluido envío) estén calculados
    if (function_exists('WC') && WC()->cart) {
        WC()->cart->calculate_totals();
    }

    ob_start();
    ?>
    <?php if (WC()->cart && !WC()->cart->is_empty()) : ?>
        <div class="cart-items">
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                if ($_product && $_product->exists() && $cart_item['quantity'] > 0) :
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
            ?>
                <div class="cart-item border-bottom p-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail', array('class' => 'img-fluid rounded')), $cart_item, $cart_item_key);
                            if (!$product_permalink) {
                                echo $thumbnail;
                            } else {
                                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                            }
                            ?>
                        </div>
                        <div class="col-9">
                            <div class="cart-item-details">
                                <h6 class="mb-1">
                                    <?php
                                    if (!$product_permalink) {
                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                    } else {
                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="text-decoration-none">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                    }
                                    ?>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if ($_product->is_sold_individually()) : ?>
                                        <span class="badge rounded-pill bg-dark border border-light text-light px-3 py-1"><?php esc_html_e('Cantidad fija: 1', 'bootstrap-theme'); ?></span>
                                    <?php else : ?>
                                        <span class="text-muted small">
                                            <?php esc_html_e('Qty:', 'bootstrap-theme'); ?> <?php echo esc_html($cart_item['quantity']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="fw-bold">
                                        <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; endforeach; ?>
        </div>
        
        <div class="cart-summary p-3">
            <div class="d-flex justify-content-between mb-2">
                <span><?php esc_html_e('Subtotal:', 'bootstrap-theme'); ?></span>
                <span class="fw-bold"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
            </div>
            <?php
            // Solo mostrar envío si hay productos no virtuales y no descargables
            $has_non_virtual = false;
            foreach (WC()->cart->get_cart() as $cart_item) {
                $product = $cart_item['data'];
                if ($product && !$product->is_virtual() && !$product->is_downloadable()) {
                    $has_non_virtual = true;
                    break;
                }
            }
            if (WC()->cart->needs_shipping() && $has_non_virtual) : ?>
            <div class="d-flex justify-content-between envio mb-2">
                <span><?php esc_html_e('Envío:', 'bootstrap-theme'); ?></span>
                <span class="fw-bold">
                    <?php
                    if (WC()->cart->show_shipping()) {
                        echo wp_kses_post(WC()->cart->get_cart_shipping_total());
                    } else {
                        esc_html_e('Calculado al finalizar compra', 'bootstrap-theme');
                    }
                    ?>
                </span>
            </div>
            <?php endif; ?>
            <div class="btn-group w-100" role="group">
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" type="button" class="btn btn-outline-primary">
                    <svg class="icon icon-sm me-2">
                        <use xlink:href="#fa-cart-shopping"></use>
                    </svg>
                    <?php esc_html_e('View Cart', 'bootstrap-theme'); ?>
                </a>
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" type="button" class="btn btn-outline-primary">
                    <svg class="icon icon-sm me-2">
                        <use xlink:href="#fa-bag-shopping"></use>
                    </svg>
                    <?php esc_html_e('Checkout', 'bootstrap-theme'); ?>
                </a>
            </div>
        </div>
    <?php else : ?>
        <div class="text-center p-4">
            <svg class="icon icon-xl text-muted mb-3">
                <use xlink:href="#fa-shop"></use>
            </svg>
            <h6 class="text-muted"><?php esc_html_e('Your cart is empty', 'bootstrap-theme'); ?></h6>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-primary mt-3">
                <?php esc_html_e('Continue Shopping', 'bootstrap-theme'); ?>
            </a>
        </div>
    <?php endif; ?>
    <?php
    $content = ob_get_clean();
    wp_send_json_success($content);
}

/**
 * Register shop sidebar
 */
function bootstrap_theme_shop_widgets_init()
{
    register_sidebar(array(
        'name'          => __('Shop Sidebar', 'bootstrap-theme'),
        'id'            => 'shop-sidebar',
        'description'   => __('Add widgets here to appear on shop pages.', 'bootstrap-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-4">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title mb-3">',
        'after_title'   => '</h5>',
    ));
}

/**
 * Remove WooCommerce default sidebar
 */
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/**
 * Add custom product badges
 */
function bootstrap_theme_product_badges()
{
    global $product;

    echo '<div class="product-badges position-absolute top-0 start-0 m-2">';

    $show_sale = function_exists('bootstrap_theme_get_woocommerce_option') ? (bool) bootstrap_theme_get_woocommerce_option('show_sale_badge') : true;
    $show_featured = function_exists('bootstrap_theme_get_woocommerce_option') ? (bool) bootstrap_theme_get_woocommerce_option('show_featured_badge') : true;
    $show_stock = function_exists('bootstrap_theme_get_woocommerce_option') ? (bool) bootstrap_theme_get_woocommerce_option('show_stock_badge') : true;


    if ($show_sale && $product->is_on_sale()) {
        echo '<span class="badge bg-danger sale-badge mb-1 d-block">' . esc_html__('Sale!', 'bootstrap-theme') . '</span>';
    }

    if ($show_featured && $product->is_featured()) {
        echo '<span class="badge bg-warning featured-badge mb-1 d-block">' . esc_html__('Featured', 'bootstrap-theme') . '</span>';
    }

    if ($show_stock && !$product->is_in_stock()) {
        echo '<span class="badge bg-secondary out-of-stock-badge mb-1 d-block">' . esc_html__('Out of Stock', 'bootstrap-theme') . '</span>';
    }

    echo '</div>';
}

/**
 * Customize single product tabs with Bootstrap nav-tabs
 */
add_filter('woocommerce_product_tabs', 'bootstrap_theme_product_tabs', 98);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
add_action('woocommerce_after_single_product_summary', 'bootstrap_theme_output_product_data_tabs_bootstrap', 10);

function bootstrap_theme_product_tabs($tabs)
{
    // Rename tabs
    if (isset($tabs['description'])) {
        $tabs['description']['title'] = __('Product Details', 'bootstrap-theme');
    }
    if (isset($tabs['additional_information'])) {
        $tabs['additional_information']['title'] = __('Specifications', 'bootstrap-theme');
    }
    if (isset($tabs['reviews'])) {
        $tabs['reviews']['title'] = __('Customer Reviews', 'bootstrap-theme');
    }
    return $tabs;
}

function bootstrap_theme_output_product_data_tabs_bootstrap() {
    global $product;
    $tabs = apply_filters('woocommerce_product_tabs', array());
    if (empty($tabs)) return;

    echo '<div class="accordion" id="productAccordion">';
    $i = 0;
    foreach ($tabs as $key => $tab) {
        $heading_id = 'heading-' . esc_attr($key);
        $collapse_id = 'collapse-' . esc_attr($key);
        $show = $i === 0 ? 'show' : '';
        echo '<div class="accordion-item">';
        echo '<h2 class="accordion-header" id="' . $heading_id . '">';
        echo '<button class="accordion-button ' . ($show ? '' : 'collapsed') . '" type="button" data-bs-toggle="collapse" data-bs-target="#' . $collapse_id . '" aria-expanded="' . ($show ? 'true' : 'false') . '" aria-controls="' . $collapse_id . '">';
        echo esc_html($tab['title']);
        echo '</button>';
        echo '</h2>';
        echo '<div id="' . $collapse_id . '" class="accordion-collapse collapse ' . $show . '" aria-labelledby="' . $heading_id . '" data-bs-parent="#productAccordion">';
        echo '<div class="accordion-body">';
        if (isset($tab['callback'])) {
            call_user_func($tab['callback'], $key, $tab);
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
        $i++;
    }
    echo '</div>';
}

/**
 * Manage checkout fields: show/hide predefined fields and add custom fields based on ACF config
 * https://www.businessbloomer.com/woocommerce-checkout-customization/
 */
function bootstrap_theme_checkout_fields($fields)
{
    // Add Bootstrap classes to all checkout fields
    foreach ($fields as $fieldset_key => $fieldset) {
        foreach ($fieldset as $field_key => $field) {
            if (!isset($fields[$fieldset_key][$field_key]['input_class'])) {
                $fields[$fieldset_key][$field_key]['input_class'] = array();
            }
            $fields[$fieldset_key][$field_key]['input_class'][] = 'form-control';
        }
    }

    // Get visible billing fields from ACF checkbox
    $visible_billing = function_exists('bootstrap_theme_get_woocommerce_option') 
        ? bootstrap_theme_get_woocommerce_option('woocommerce_checkout_billing_fields') 
        : array();
    if (!is_array($visible_billing)) {
        $visible_billing = array();
    }

    // Get visible shipping fields from ACF checkbox
    $visible_shipping = function_exists('bootstrap_theme_get_woocommerce_option')
        ? bootstrap_theme_get_woocommerce_option('woocommerce_checkout_shipping_fields')
        : array();
    if (!is_array($visible_shipping)) {
        $visible_shipping = array();
    }

    // Get visible order fields from ACF checkbox
    $visible_order = function_exists('bootstrap_theme_get_woocommerce_option')
        ? bootstrap_theme_get_woocommerce_option('woocommerce_checkout_order_fields')
        : array();
    if (!is_array($visible_order)) {
        $visible_order = array();
    }

    // Hide billing fields that are NOT in the visible list
    if (isset($fields['billing'])) {
        foreach ($fields['billing'] as $field_key => $field) {
            if (!in_array($field_key, $visible_billing)) {
                unset($fields['billing'][$field_key]);
            }
        }
    }

    // Hide shipping fields that are NOT in the visible list
    if (isset($fields['shipping'])) {
        foreach ($fields['shipping'] as $field_key => $field) {
            if (!in_array($field_key, $visible_shipping)) {
                unset($fields['shipping'][$field_key]);
            }
        }
    }

    // Hide order fields that are NOT in the visible list
    if (isset($fields['order'])) {
        foreach ($fields['order'] as $field_key => $field) {
            if (!in_array($field_key, $visible_order)) {
                unset($fields['order'][$field_key]);
            }
        }
    }

    // Make default checkout fields optional (except Email and First Name) if enabled in options
    $make_optional = function_exists('bootstrap_theme_get_woocommerce_option')
        ? (bool) bootstrap_theme_get_woocommerce_option('woocommerce_checkout_defaults_optional')
        : false;
    if ($make_optional) {
        if (isset($fields['billing'])) {
            foreach ($fields['billing'] as $field_key => &$field) {
                if ($field_key === 'billing_email' || $field_key === 'billing_first_name') {
                    $field['required'] = true;
                    if (isset($field['class']) && is_array($field['class'])) {
                        $field['class'][] = 'validate-required';
                        $field['class'] = array_values(array_unique($field['class']));
                    }
                } else {
                    $field['required'] = false;
                    if (isset($field['class']) && is_array($field['class'])) {
                        $field['class'] = array_diff($field['class'], array('validate-required'));
                    }
                }
            }
            unset($field);
        }

        if (isset($fields['shipping'])) {
            foreach ($fields['shipping'] as $field_key => &$field) {
                $field['required'] = false;
                if (isset($field['class']) && is_array($field['class'])) {
                    $field['class'] = array_diff($field['class'], array('validate-required'));
                }
            }
            unset($field);
        }

        if (isset($fields['order'])) {
            foreach ($fields['order'] as $field_key => &$field) {
                $field['required'] = false;
                if (isset($field['class']) && is_array($field['class'])) {
                    $field['class'] = array_diff($field['class'], array('validate-required'));
                }
            }
            unset($field);
        }
    }

    // Get custom fields configuration from ACF repeater
    $custom_fields = function_exists('bootstrap_theme_get_woocommerce_option')
        ? bootstrap_theme_get_woocommerce_option('woocommerce_checkout_custom_fields')
        : array();
    if (is_array($custom_fields)) {
        // Process custom fields from repeater
        foreach ($custom_fields as $field_config) {
            // Skip invalid/empty entries
            if (!is_array($field_config) || empty($field_config['field_name'])) {
                continue;
            }

            $field_name = sanitize_key($field_config['field_name']);
            $section = isset($field_config['section']) ? $field_config['section'] : 'billing';
            // Treat false, 0, '0', empty strings, null as disabled
            $enabled = isset($field_config['enabled']) && $field_config['enabled'] && $field_config['enabled'] !== 'false' && $field_config['enabled'] !== 0 && $field_config['enabled'] !== '0' ? true : false;
            $label = isset($field_config['label']) && !empty($field_config['label']) ? $field_config['label'] : $field_name;
            $field_type = isset($field_config['field_type']) ? $field_config['field_type'] : 'text';
            $placeholder = isset($field_config['placeholder']) && !empty($field_config['placeholder']) ? $field_config['placeholder'] : $label;
            // Check if field is required (Campo obligatorio)
            $is_required = isset($field_config['required']) && ($field_config['required'] === true || $field_config['required'] === 1 || $field_config['required'] === '1');
            // Get regex pattern for validation
            $validation_pattern = isset($field_config['validation_pattern']) && !empty($field_config['validation_pattern']) ? $field_config['validation_pattern'] : '';
            // Get regex pattern for auto-formatting
            $format_pattern = isset($field_config['format_pattern']) && !empty($field_config['format_pattern']) ? $field_config['format_pattern'] : '';

            // Add section prefix to field name
            $prefixed_field_name = $section . '_' . $field_name;

            if ($enabled) {
                // Add or update field
                if (!isset($fields[$section])) {
                    $fields[$section] = array();
                }

                $field_classes = array('form-row-wide');
                if ($is_required) {
                    $field_classes[] = 'validate-required';
                }

                $custom_attributes = array();
                // Add pattern attribute for HTML5 validation
                if (!empty($validation_pattern)) {
                    $custom_attributes['pattern'] = $validation_pattern;
                    $custom_attributes['data-validation-pattern'] = $validation_pattern;
                }
                // Add format pattern for JS auto-formatting
                if (!empty($format_pattern)) {
                    $custom_attributes['data-format-pattern'] = $format_pattern;
                }

                $fields[$section][$prefixed_field_name] = array(
                    'type' => $field_type,
                    'label' => $label,
                    'placeholder' => $placeholder,
                    'required' => $is_required,
                    'class' => $field_classes,
                    'input_class' => array('form-control'),
                    'clear' => true,
                    'priority' => isset($field_config['priority']) ? intval($field_config['priority']) : 100,
                    'custom_attributes' => $custom_attributes,
                );
            } else {
                // Remove field if it was previously added
                if (isset($fields[$section][$prefixed_field_name])) {
                    unset($fields[$section][$prefixed_field_name]);
                }
            }
        }
    }

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'bootstrap_theme_checkout_fields');

/**
 * Force shipping address section to be visible by default when cart needs shipping
 * This ensures the shipping fields appear even if "Ship to different address" is not checked
 * Only show for carts with non-virtual products
 */
add_filter('woocommerce_ship_to_different_address_checked', function($checked) {
    if (function_exists('WC') && WC()->cart && WC()->cart->needs_shipping()) {
        // Verificar si el carrito tiene al menos un producto no virtual y no descargable
        $has_non_virtual = false;
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if ($product && !$product->is_virtual() && !$product->is_downloadable()) {
                $has_non_virtual = true;
                break;
            }
        }
        return $has_non_virtual;
    }
    return $checked;
}, 20);

/**
 * Add "Detalles de envío" heading before shipping fields
 * Only show for non-virtual products
 */
add_action('woocommerce_before_checkout_shipping_form', function($checkout) {
    if (WC()->cart && WC()->cart->needs_shipping()) {
        // Verificar si el carrito tiene al menos un producto no virtual y no descargable
        $has_non_virtual = false;
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if ($product && !$product->is_virtual() && !$product->is_downloadable()) {
                $has_non_virtual = true;
                break;
            }
        }
        
        if ($has_non_virtual) {
            echo '<h3 id="ship-address" class="mb-3 mt-2">';
            esc_html_e('Detalles de envío', 'bootstrap-theme');
            echo '</h3>';
        }
    }
}, 1);

/**
 * Fix Chilean address labels - Comuna (city) and Región (state)
 */
add_filter('woocommerce_checkout_fields', function($fields) {
    if (isset($fields['shipping']['shipping_state'])) {
        $fields['shipping']['shipping_state']['label'] = __('Región', 'bootstrap-theme');
    }
    if (isset($fields['billing']['billing_state'])) {
        $fields['billing']['billing_state']['label'] = __('Región', 'bootstrap-theme');
    }
    return $fields;
}, 999);

// Enforce optional/required flags with high priority on billing fields
function bootstrap_theme_checkout_billing_required_overrides($fields) {
    $make_optional = function_exists('bootstrap_theme_get_woocommerce_option')
        ? (bool) bootstrap_theme_get_woocommerce_option('woocommerce_checkout_defaults_optional')
        : false;
    if (!is_array($fields)) {
        return $fields;
    }
    foreach ($fields as $key => &$args) {
        if ($make_optional) {
            // Solo email y nombre requeridos, el resto opcional
            if ($key === 'billing_email' || $key === 'billing_first_name') {
                $args['required'] = true;
                if (isset($args['class']) && is_array($args['class'])) {
                    $args['class'][] = 'validate-required';
                    $args['class'] = array_values(array_unique($args['class']));
                }
            } else {
                $args['required'] = false;
                if (isset($args['class']) && is_array($args['class'])) {
                    $args['class'] = array_diff($args['class'], array('validate-required'));
                }
            }
        } else {
            // Todos los campos requeridos
            $args['required'] = true;
            if (isset($args['class']) && is_array($args['class'])) {
                $args['class'][] = 'validate-required';
                $args['class'] = array_values(array_unique($args['class']));
            }
        }
    }
    unset($args);
    return $fields;
}
add_filter('woocommerce_billing_fields', 'bootstrap_theme_checkout_billing_required_overrides', 999);

// Enforce optional/required flags with high priority on shipping fields
function bootstrap_theme_checkout_shipping_required_overrides($fields) {
    $make_optional = function_exists('bootstrap_theme_get_woocommerce_option')
        ? (bool) bootstrap_theme_get_woocommerce_option('woocommerce_checkout_defaults_optional')
        : false;
    if (!$make_optional || !is_array($fields)) {
        return $fields;
    }
    foreach ($fields as $key => &$args) {
        $args['required'] = false;
        if (isset($args['class']) && is_array($args['class'])) {
            $args['class'] = array_diff($args['class'], array('validate-required'));
        }
    }
    unset($args);
    return $fields;
}
add_filter('woocommerce_shipping_fields', 'bootstrap_theme_checkout_shipping_required_overrides', 999);

/**
 * Validate custom checkout fields with regex patterns
 */
function bootstrap_theme_validate_custom_checkout_fields($data, $errors) {
    $custom_fields = function_exists('bootstrap_theme_get_woocommerce_option')
        ? bootstrap_theme_get_woocommerce_option('woocommerce_checkout_custom_fields')
        : array();
    
    if (!is_array($custom_fields)) {
        return;
    }
    
    foreach ($custom_fields as $field_config) {
        if (!is_array($field_config) || empty($field_config['field_name'])) {
            continue;
        }
        
        $field_name = sanitize_key($field_config['field_name']);
        $section = isset($field_config['section']) ? $field_config['section'] : 'billing';
        $enabled = isset($field_config['enabled']) && $field_config['enabled'] && $field_config['enabled'] !== 'false' && $field_config['enabled'] !== 0 && $field_config['enabled'] !== '0' ? true : false;
        $label = isset($field_config['label']) && !empty($field_config['label']) ? $field_config['label'] : $field_name;
        $validation_pattern = isset($field_config['validation_pattern']) && !empty($field_config['validation_pattern']) ? $field_config['validation_pattern'] : '';
        
        if (!$enabled) {
            continue;
        }
        
        $prefixed_field_name = $section . '_' . $field_name;
        $value = isset($_POST[$prefixed_field_name]) ? sanitize_text_field($_POST[$prefixed_field_name]) : '';
        
        // Validate with regex if pattern is provided and field has value
        if (!empty($validation_pattern) && !empty($value)) {
            // Add delimiters if not present
            $pattern = $validation_pattern;
            if (substr($pattern, 0, 1) !== '/') {
                $pattern = '/' . $pattern . '/';
            }
            
            if (!preg_match($pattern, $value)) {
                $errors->add('validation', sprintf(__('El formato del campo "%s" no es válido.', 'bootstrap-theme'), $label));
            }
        }
    }
}
add_action('woocommerce_after_checkout_validation', 'bootstrap_theme_validate_custom_checkout_fields', 10, 2);

/**
 * Save custom checkout fields to order meta
 */
function bootstrap_theme_save_custom_checkout_fields($order_id) {
    $custom_fields = function_exists('bootstrap_theme_get_woocommerce_option')
        ? bootstrap_theme_get_woocommerce_option('woocommerce_checkout_custom_fields')
        : array();
    
    if (!is_array($custom_fields)) {
        return;
    }
    
    foreach ($custom_fields as $field_config) {
        if (!is_array($field_config) || empty($field_config['field_name'])) {
            continue;
        }
        
        $field_name = sanitize_key($field_config['field_name']);
        $section = isset($field_config['section']) ? $field_config['section'] : 'billing';
        $enabled = isset($field_config['enabled']) && $field_config['enabled'] && $field_config['enabled'] !== 'false' && $field_config['enabled'] !== 0 && $field_config['enabled'] !== '0' ? true : false;
        
        if (!$enabled) {
            continue;
        }
        
        $prefixed_field_name = $section . '_' . $field_name;
        
        if (isset($_POST[$prefixed_field_name])) {
            update_post_meta($order_id, '_' . $prefixed_field_name, sanitize_text_field($_POST[$prefixed_field_name]));
        }
    }
}
add_action('woocommerce_checkout_update_order_meta', 'bootstrap_theme_save_custom_checkout_fields');

/**
 * Display custom checkout fields in order admin
 */
function bootstrap_theme_display_custom_checkout_fields_admin($order) {
    $custom_fields = function_exists('bootstrap_theme_get_woocommerce_option')
        ? bootstrap_theme_get_woocommerce_option('woocommerce_checkout_custom_fields')
        : array();
    
    if (!is_array($custom_fields)) {
        return;
    }
    
    $has_values = false;
    $output = '';
    
    foreach ($custom_fields as $field_config) {
        if (!is_array($field_config) || empty($field_config['field_name'])) {
            continue;
        }
        
        $field_name = sanitize_key($field_config['field_name']);
        $section = isset($field_config['section']) ? $field_config['section'] : 'billing';
        $enabled = isset($field_config['enabled']) && $field_config['enabled'] && $field_config['enabled'] !== 'false' && $field_config['enabled'] !== 0 && $field_config['enabled'] !== '0' ? true : false;
        $label = isset($field_config['label']) && !empty($field_config['label']) ? $field_config['label'] : $field_name;
        
        if (!$enabled) {
            continue;
        }
        
        $prefixed_field_name = $section . '_' . $field_name;
        $order_id = $order->get_id();
        $value = get_post_meta($order_id, '_' . $prefixed_field_name, true);
        
        if (!empty($value)) {
            $has_values = true;
            $output .= '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</p>';
        }
    }
    
    if ($has_values) {
        echo '<div class="order_data_column" style="clear:both; float:none; width:100%; margin-top: 20px;">';
        echo '<h3>' . __('Campos Personalizados', 'bootstrap-theme') . '</h3>';
        echo $output;
        echo '</div>';
    }
}
add_action('woocommerce_admin_order_data_after_billing_address', 'bootstrap_theme_display_custom_checkout_fields_admin');

/**
 * Remove WooCommerce default checkout layout classes (col2-set, col-1, col-2)
 * Using CSS to hide/override these classes
 */
add_action('wp_head', function() {
    if (is_checkout()) {
        echo '<style>
            p.form-row.col2-set { display: flex !important; flex-direction: column !important; }
            p.form-row.col2-set .col-1,
            p.form-row.col2-set .col-2 { width: 100% !important; }
            .col2-set { display: contents !important; }
            .col-1, .col-2 { width: auto !important; float: none !important; }
        </style>';
    }
}, 999);

/**
 * Add auto-formatting JavaScript for custom checkout fields
 */
add_action('wp_footer', 'bootstrap_theme_checkout_custom_fields_js');
function bootstrap_theme_checkout_custom_fields_js() {
    if (!is_checkout()) {
        return;
    }
    ?>
    <script>
    (function($) {
        'use strict';
        
        $(document).ready(function() {
            // Auto-format fields with data-format-pattern attribute
            $('input[data-format-pattern]').on('input blur', function() {
                var $field = $(this);
                var pattern = $field.attr('data-format-pattern');
                var value = $field.val();
                
                if (!pattern || !value) {
                    return;
                }
                
                // Common formatting patterns
                var formatted = value;
                
                switch(pattern) {
                    case 'rut':
                    case 'RUT':
                        // Chilean RUT format: 12.345.678-9
                        formatted = value.replace(/[^\dkK]/g, '').toUpperCase();
                        if (formatted.length > 1) {
                            var body = formatted.slice(0, -1);
                            var dv = formatted.slice(-1);
                            body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            formatted = body + '-' + dv;
                        }
                        break;
                    
                    case 'phone':
                    case 'telefono':
                        // Phone format: +56 9 1234 5678
                        formatted = value.replace(/\D/g, '');
                        if (formatted.startsWith('56')) {
                            formatted = '+' + formatted.slice(0, 2) + ' ' + formatted.slice(2, 3) + ' ' + formatted.slice(3, 7) + ' ' + formatted.slice(7, 11);
                        } else if (formatted.length === 9) {
                            formatted = '+56 ' + formatted.slice(0, 1) + ' ' + formatted.slice(1, 5) + ' ' + formatted.slice(5, 9);
                        }
                        formatted = formatted.trim();
                        break;
                    
                    case 'uppercase':
                        formatted = value.toUpperCase();
                        break;
                    
                    case 'lowercase':
                        formatted = value.toLowerCase();
                        break;
                    
                    case 'capitalize':
                        formatted = value.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                        break;
                    
                    case 'numbers':
                        formatted = value.replace(/\D/g, '');
                        break;
                    
                    case 'letters':
                        formatted = value.replace(/[^a-zA-ZáéíóúñÁÉÍÓÚÑ\s]/g, '');
                        break;
                    
                    default:
                        // Custom regex pattern
                        try {
                            // Try to use pattern as replacement regex
                            // Format: "search|replace" or just "search"
                            if (pattern.includes('|')) {
                                var parts = pattern.split('|');
                                var regex = new RegExp(parts[0], 'g');
                                formatted = value.replace(regex, parts[1] || '');
                            }
                        } catch(e) {
                            console.log('Invalid format pattern:', pattern);
                        }
                        break;
                }
                
                if (formatted !== value) {
                    $field.val(formatted);
                }
            });
            
            // Real-time validation feedback
            $('input[data-validation-pattern]').on('blur', function() {
                var $field = $(this);
                var pattern = $field.attr('data-validation-pattern');
                var value = $field.val();
                var $wrapper = $field.closest('.form-row');
                
                // Remove previous validation messages
                $wrapper.find('.custom-validation-error').remove();
                $wrapper.removeClass('woocommerce-invalid woocommerce-validated');
                
                if (!value) {
                    return; // Don't validate empty non-required fields
                }
                
                try {
                    var regex = new RegExp(pattern);
                    if (!regex.test(value)) {
                        $wrapper.addClass('woocommerce-invalid');
                        $field.after('<span class="custom-validation-error text-danger small d-block mt-1">Formato inválido</span>');
                    } else {
                        $wrapper.addClass('woocommerce-validated');
                    }
                } catch(e) {
                    console.log('Invalid validation pattern:', pattern);
                }
            });
            
            // Inyectar input group Bootstrap para Teléfono en checkout si no se aplica por PHP
            $(function() {
              var $phone = $('#billing_phone');
              if ($phone.length && !$phone.closest('.input-group').length) {
                var $input = $phone;
                var $parent = $input.parent();
                // Solo si el input está visible y no tiene input-group
                if ($parent.is('p.form-row')) {
                  $input.detach();
                  var $group = $('<div class="input-group"></div>');
                  $group.append('<span class="input-group-text">+56</span>');
                  $group.append($input);
                  $parent.prepend($group);
                }
              }
            });
        });
    })(jQuery);
    </script>
    <?php
}

/**
 * Add custom my account navigation classes
 */
function bootstrap_theme_account_menu_items($items)
{
    return $items;
}
add_filter('woocommerce_account_menu_items', 'bootstrap_theme_account_menu_items');

/**
 * Customize WooCommerce pagination
 */
function bootstrap_theme_woocommerce_pagination_args($args)
{
    $args['prev_text'] = '<i class="fas fa-chevron-left"></i> ' . __('Previous', 'bootstrap-theme');
    $args['next_text'] = __('Next', 'bootstrap-theme') . ' <i class="fas fa-chevron-right"></i>';

    return $args;
}
add_filter('woocommerce_pagination_args', 'bootstrap_theme_woocommerce_pagination_args');

// No agregamos un paginador adicional aquí para evitar duplicados; el template override manejará el marcado.

/**
 * Renderizar la paginación reutilizando el template part del tema
 */
function bootstrap_theme_render_theme_pagination() {
    global $wp_query;
    
    // Para WooCommerce, asegurar que tenemos la query correcta
    if (is_shop() || is_product_category() || is_product_tag()) {
        $total_pages = wc_get_loop_prop('total_pages');
        if ($total_pages && $total_pages > 1) {
            // Usar template de paginación del tema
            get_template_part('template-parts/pagination');
        }
    } else {
        // Para otras páginas, usar el template normal
        if ($wp_query->max_num_pages > 1) {
            get_template_part('template-parts/pagination');
        }
    }
}

/**
 * Customizar HTML del enlace "Ver carrito" con clases Bootstrap
 * Filtrar fragmentos AJAX que contienen el botón "Ver carrito"
 */

/**
 * Agregar JavaScript para cambiar clases del botón "Ver carrito" dinámicamente
 */
function bootstrap_theme_add_cart_button_js() {
    if (function_exists('is_woocommerce') && (is_woocommerce() || is_shop() || is_product_category() || is_product_tag() || is_home() || is_front_page())) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para cambiar clases de botones "Ver carrito"
            function updateCartButtonClasses() {
                const cartButtons = document.querySelectorAll('a.added_to_cart.wc-forward');
                cartButtons.forEach(function(button) {
                    button.className = button.className.replace('added_to_cart wc-forward', 'btn btn-primary btn-sm mt-2 w-100');
                });
            }
            
            // Ejecutar al cargar la página
            updateCartButtonClasses();
            
            // Ejecutar después de eventos AJAX de WooCommerce
            jQuery(document.body).on('added_to_cart', function() {
                setTimeout(updateCartButtonClasses, 100);
            });
            
            // Observar cambios en el DOM para botones que se agregan dinámicamente
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        updateCartButtonClasses();
                    }
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'bootstrap_theme_add_cart_button_js');

/**
 * Mover precio del hook por defecto al bloque de botones en el loop
 * Evita que se muestre duplicado (arriba y abajo)
 */
add_action('init', function(){
    if (function_exists('remove_action')) {
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
    }
}, 20);

/**
 * Añadir ícono a los botones de "Agregar al carrito" en el loop
 */
function bootstrap_theme_add_icon_to_add_to_cart($html, $product, $args) {
    // Insertar el ícono justo antes del texto dentro del enlace
    // Agrega una separación con me-1 para Bootstrap
    $icon = '<i class="fa-solid fa-cart-plus me-1" aria-hidden="true"></i>';
    // Envolver texto interno en <span> para facilitar estilos sin añadir CSS extra obligatorio
    $pos_open = strpos($html, '>');
    $pos_close = strrpos($html, '</a>');
    if ($pos_open !== false && $pos_close !== false && $pos_close > $pos_open) {
        $inner = substr($html, $pos_open + 1, $pos_close - ($pos_open + 1));
        // Evitar duplicar span si ya existe
        if (strip_tags($inner) !== '' && strpos($inner, '<span') === false) {
            $inner = '<span class="add-to-cart-text">' . $inner . '</span>';
        }
        $html = substr($html, 0, $pos_open + 1) . $icon . $inner . substr($html, $pos_close);
    } else {
        // Fallback simple: insertar icono al inicio si no se pudo envolver
        $html = $icon . $html;
    }
    return $html;
}
add_filter('woocommerce_loop_add_to_cart_link', 'bootstrap_theme_add_icon_to_add_to_cart', 10, 3);

/**
 * Paginación Bootstrap 5.3 para WooCommerce
 * 
 * @since 1.1.5
 * 
 * LESSONS LEARNED:
 * - Template parts improve code organization and maintenance
 * - Global variable $bootstrap_theme_current_page provides reliable page detection
 * - Priority: global variable > wc_get_loop_prop() > get_query_var()
 * - get_template_part() automatically includes template-parts/ directory
 * - Early return when total_pages <= 1 improves performance
 */
function bootstrap_theme_woocommerce_bootstrap_pagination() {
    global $wp_query, $bootstrap_theme_current_page;
    
    $total_pages = wc_get_loop_prop('total_pages');
    $current_page = wc_get_loop_prop('current_page');
    
    if (!$total_pages) {
        $total_pages = $wp_query->max_num_pages;
    }
    
    // Usar la variable global si está disponible, sino usar los métodos tradicionales
    if (!$current_page) {
        if (isset($bootstrap_theme_current_page) && $bootstrap_theme_current_page > 0) {
            $current_page = $bootstrap_theme_current_page;
        } else {
            $current_page = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
        }
    }
    
    if ($total_pages <= 1) return;
    
    // Cargar el template part de paginación
    get_template_part('template-parts/woocommerce/pagination');
}

/**
 * Verificar si hay pedidos pendientes para un producto específico
 * Compatible con HPOS
 */
function bootstrap_theme_has_pending_orders_for_product($product_id) {
    // Buscar pedidos recientes (últimos 10 minutos) con estado processing, pending o on-hold
    $ten_minutes_ago = strtotime('-10 minutes');
    $orders = wc_get_orders(array(
        'status' => array('processing', 'pending', 'on-hold'),
        'date_created' => '>=' . $ten_minutes_ago,
        'limit' => -1,
        'return' => 'ids'
    ));
    
    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() == $product_id) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Helper: dado un array de IDs de productos, retorna solo en-stock y con destacados primero.
 */
function bootstrap_theme_filter_instock_and_order_featured_first(array $ids) {
    if (empty($ids)) return $ids;
    $products = array();
    foreach ($ids as $id) {
        $p = wc_get_product($id);
        if ($p instanceof WC_Product) {
            if ($p->is_in_stock()) {
                $products[] = $p;
            }
        }
    }
    // Ordenar: destacados primero; mantener orden relativo para el resto
    usort($products, function($a, $b) {
        $fa = (int) $a->is_featured();
        $fb = (int) $b->is_featured();
        if ($fa === $fb) return 0;
        return $fa > $fb ? -1 : 1;
    });
    return array_map(function($p){ return $p->get_id(); }, $products);
}

/**
 * Related products: excluir sin stock y priorizar destacados.
 */
add_filter('woocommerce_related_products', function($related_ids, $product_id, $args){
    return bootstrap_theme_filter_instock_and_order_featured_first($related_ids);
}, 20, 3);

/**
 * Upsell products: excluir sin stock y priorizar destacados.
 */
add_filter('woocommerce_product_get_upsell_ids', function($upsell_ids, $product){
    return bootstrap_theme_filter_instock_and_order_featured_first((array)$upsell_ids);
}, 20, 2);

/**
 * Cross-sells en carrito: excluir sin stock y priorizar destacados.
 */
add_filter('woocommerce_cart_crosssell_ids', function($ids){
    return bootstrap_theme_filter_instock_and_order_featured_first((array)$ids);
}, 20);

/**
 * Related/upsell queries: asegurar filtro de stock en args (defensa adicional).
 */
add_filter('woocommerce_related_products_args', function($args){
    $args = is_array($args) ? $args : array();
    if (!isset($args['meta_query'])) $args['meta_query'] = array();
    $args['meta_query'][] = array(
        'key'   => '_stock_status',
        'value' => 'instock',
    );
    return $args;
}, 20);

add_filter('woocommerce_upsell_display_args', function($args){
    // Algunos temas/plantillas respetan meta_query en estos args
    if (!isset($args['meta_query'])) $args['meta_query'] = array();
    $args['meta_query'][] = array(
        'key'   => '_stock_status',
        'value' => 'instock',
    );
    return $args;
}, 20);

/**
 * Mostrar información de stock en tiempo real en productos
 */
add_action('woocommerce_single_product_summary', 'bootstrap_theme_realtime_stock_info', 26);
function bootstrap_theme_realtime_stock_info() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    $product_type = $product->get_type();
    
    // No mostrar para virtuales, composites, bundles o que no gestionan stock
    if ($product_type === 'composite' || 
        $product_type === 'bundle' || 
        !$product->managing_stock()) {
        return;
    }
    
    $stock_quantity = $product->get_stock_quantity();
    
    if ($stock_quantity <= 5) { // Mostrar info solo si stock es bajo
        echo '<div class="realtime-stock-info mt-3" data-product-id="' . $product->get_id() . '">';
        echo '<div class="alert alert-info d-flex align-items-center">';
        echo '<i class="fas fa-info-circle me-2"></i>';
        echo '<span class="stock-text">';
        
        if ($stock_quantity == 1) {
            echo '<strong>¡Última unidad disponible!</strong>';
        } elseif ($stock_quantity <= 3) {
            echo '<strong>Solo quedan ' . $stock_quantity . ' unidades</strong>';
        } else {
            echo 'Pocas unidades disponibles (' . $stock_quantity . ')';
        }
        
        echo '</span>';
        echo '</div>';
        echo '</div>';
    }
}

/**
 * Inyectar clases Bootstrap en formularios de productos variables
 */
add_filter('woocommerce_dropdown_variation_attribute_options_html', 'bootstrap_theme_variation_dropdown_html', 20, 2);
function bootstrap_theme_variation_dropdown_html($html, $args) {
    // Solo si está habilitada la opción del tema
    if (!function_exists('bootstrap_theme_get_woocommerce_option')) {
        return $html;
    }
    $enable_custom_styles = (bool) bootstrap_theme_get_woocommerce_option('enable_custom_styles');
    if (!$enable_custom_styles) {
        return $html;
    }
    
    // Inyectar form-select + col en el select (respetando clases existentes)
    if (strpos($html, 'class="') !== false) {
        $html = preg_replace('/class="([^"]*)"/', 'class="$1 form-select bg-dark text-light border-light col"', $html, 1);
    } else {
        $html = str_replace('<select', '<select class="form-select bg-dark text-light border-light col"', $html);
    }
    
    return $html;
}

/**
 * Inyectar clases Bootstrap en el botón "Limpiar" de variaciones
 */
add_filter('woocommerce_reset_variations_link', 'bootstrap_theme_reset_variations_link_html', 20);
function bootstrap_theme_reset_variations_link_html($html) {
    // Solo si está habilitada la opción del tema
    if (!function_exists('bootstrap_theme_get_woocommerce_option')) {
        return $html;
    }
    $enable_custom_styles = (bool) bootstrap_theme_get_woocommerce_option('enable_custom_styles');
    if (!$enable_custom_styles) {
        return $html;
    }
    
    // Reemplazar la clase reset_variations por btn + col
    $html = str_replace('class="reset_variations"', 'class="reset_variations btn btn-sm btn-outline-secondary col"', $html);
    
    return $html;
}

/**
 * Inyectar clases Bootstrap en input de cantidad de productos
 * NOTA (v1.5.6): Se excluyen productos variables para usar formulario nativo WooCommerce
 */
add_filter('woocommerce_quantity_input_classes', 'bootstrap_theme_variation_quantity_classes', 20, 2);
function bootstrap_theme_variation_quantity_classes($classes, $product) {
    // Solo si está habilitada la opción del tema
    if (!function_exists('bootstrap_theme_get_woocommerce_option')) {
        return $classes;
    }
    $enable_custom_styles = (bool) bootstrap_theme_get_woocommerce_option('enable_custom_styles');
    if (!$enable_custom_styles) {
        return $classes;
    }
    
    // NO aplicar a productos variables - usan formulario nativo de WooCommerce
    if ($product && $product->is_type('variable')) {
        return $classes;
    }
    
    $classes[] = 'form-control';
    return $classes;
}

/**
 * NOTA (v1.5.6): Función deshabilitada - productos variables usan formulario WooCommerce nativo
 * Agregar clases alert de Bootstrap al div de información de variación
 */
add_action('wp_footer', 'bootstrap_theme_variation_wrapper_classes_script');
function bootstrap_theme_variation_wrapper_classes_script() {
    if (!function_exists('bootstrap_theme_get_woocommerce_option') || !is_product()) {
        return;
    }
    $enable_custom_styles = (bool) bootstrap_theme_get_woocommerce_option('enable_custom_styles');
    if (!$enable_custom_styles) {
        return;
    }
    ?>
    <script>
    (function($) {
        'use strict';
        $(function() {
            var applyRowClass = function(scope) {
                var $ctx = scope && scope.length ? scope : $(document);
                $ctx.find('form.variations_form table.variations td.value').addClass('row');
            };

            // Inicial
            applyRowClass();
            $('.woocommerce-variation.single_variation').addClass('alert alert-info');

            // Reaplicar en eventos de variaciones
            $(document).on('found_variation hide_variation woocommerce_update_variation_values reset_data', 'form.variations_form', function() {
                applyRowClass($(this));
            });

            // Observer por si el DOM cambia dinámicamente
            var target = document.querySelector('form.variations_form');
            if (target && 'MutationObserver' in window) {
                var obs = new MutationObserver(function() { applyRowClass($(target)); });
                obs.observe(target, { childList: true, subtree: true });
            }
        });
    })(jQuery);
    </script>
    <?php
}

/**
 * AJAX: Actualizar cantidad de producto en carrito (bloque bs-cart)
 */
add_action('wp_ajax_bs_cart_update_quantity', 'bootstrap_theme_ajax_cart_update_quantity');
add_action('wp_ajax_nopriv_bs_cart_update_quantity', 'bootstrap_theme_ajax_cart_update_quantity');
function bootstrap_theme_ajax_cart_update_quantity() {
    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error('WooCommerce cart not available');
    }

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if (empty($cart_item_key)) {
        wp_send_json_error('Invalid cart item key');
    }

    try {
        $cart = WC()->cart;
        $cart_item = $cart->get_cart_item($cart_item_key);

        if (!$cart_item) {
            wp_send_json_error('Cart item not found');
        }

        $product = $cart_item['data'];
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        // Get maximum stock quantity
        $max_stock = $product->get_stock_quantity();
        
        // If stock management is disabled (null), allow unlimited quantity
        if ($max_stock === null || $max_stock === '') {
            $max_stock = 9999;
        } else {
            $max_stock = intval($max_stock);
        }
        
        // Validate new quantity does not exceed stock
        if ($new_quantity > $max_stock) {
            wp_send_json_error(sprintf('Stock limit: maximum %d item(s) available', $max_stock));
        }

        if ($new_quantity < 1) {
            $cart->remove_cart_item($cart_item_key);
        } else {
            $cart->set_quantity($cart_item_key, $new_quantity, false);
        }

        $cart->calculate_totals();

        wp_send_json_success(array(
            'cart_totals' => WC()->cart->get_totals(),
            'fragments' => apply_filters('woocommerce_update_order_review_fragments', array())
        ));
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

/**
 * AJAX: Remover producto del carrito (bloque bs-cart)
 */
add_action('wp_ajax_bs_cart_remove_item', 'bootstrap_theme_ajax_cart_remove_item');
add_action('wp_ajax_nopriv_bs_cart_remove_item', 'bootstrap_theme_ajax_cart_remove_item');
function bootstrap_theme_ajax_cart_remove_item() {
    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error('WooCommerce cart not available');
    }

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';

    if (empty($cart_item_key)) {
        wp_send_json_error('Invalid cart item key');
    }

    try {
        $cart = WC()->cart;
        $cart->remove_cart_item($cart_item_key);
        $cart->calculate_totals();

        wp_send_json_success(array(
            'cart_totals' => WC()->cart->get_totals(),
            'fragments' => apply_filters('woocommerce_update_order_review_fragments', array())
        ));
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

/**
 * AJAX: Obtener cantidad de items en el carrito
 */
add_action('wp_ajax_woocommerce_get_cart_count', 'bootstrap_theme_ajax_get_cart_count');
add_action('wp_ajax_nopriv_woocommerce_get_cart_count', 'bootstrap_theme_ajax_get_cart_count');
function bootstrap_theme_ajax_get_cart_count() {
    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error('Cart not available');
    }

    $count = WC()->cart->get_cart_contents_count();
    
    wp_send_json_success(array(
        'count' => $count
    ));
}

/**
 * Agregar columna de stock en el listado de productos del admin
 */
add_filter('manage_edit-product_columns', 'bootstrap_theme_add_stock_column', 15);
function bootstrap_theme_add_stock_column($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Agregar columna de stock después del precio
        if ($key === 'price') {
            $new_columns['stock_quantity'] = __('Stock', 'bootstrap-theme');
        }
    }
    
    return $new_columns;
}

/**
 * Mostrar contenido de la columna de stock
 */
add_action('manage_product_posts_custom_column', 'bootstrap_theme_show_stock_column_content', 10, 2);
function bootstrap_theme_show_stock_column_content($column, $post_id) {
    if ($column === 'stock_quantity') {
        $product = wc_get_product($post_id);
        
        if ($product) {
            if ($product->managing_stock()) {
                $stock = $product->get_stock_quantity();
                $status = $product->get_stock_status();
                
                if ($stock !== null) {
                    // Color según nivel de stock
                    $color = 'text-success';
                    if ($stock <= 0) {
                        $color = 'text-danger';
                    } elseif ($stock <= 5) {
                        $color = 'text-warning';
                    }
                    
                    echo '<span class="' . $color . '"><strong>' . $stock . '</strong></span>';
                } else {
                    echo '<span class="text-muted">—</span>';
                }
            } else {
                // No gestiona stock
                $status = $product->get_stock_status();
                if ($status === 'instock') {
                    echo '<span class="text-success">' . __('En stock', 'bootstrap-theme') . '</span>';
                } else {
                    echo '<span class="text-danger">' . __('Agotado', 'bootstrap-theme') . '</span>';
                }
            }
        }
    }
}

/**
 * Hacer la columna de stock ordenable
 */
add_filter('manage_edit-product_sortable_columns', 'bootstrap_theme_stock_sortable_column');
function bootstrap_theme_stock_sortable_column($columns) {
    $columns['stock_quantity'] = 'stock_quantity';
    return $columns;
}

/**
 * Aplicar ordenamiento por stock en el admin
 */
add_action('pre_get_posts', 'bootstrap_theme_stock_orderby', 20);
function bootstrap_theme_stock_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    if ($orderby === 'stock_quantity') {
        $query->set('meta_key', '_stock');
        $query->set('orderby', 'meta_value_num');
    }
}

/**
 * Modificar el HTML del precio en el loop de productos
 */
add_filter('woocommerce_get_price_html', 'bootstrap_theme_custom_price_html', 10, 2);
function bootstrap_theme_custom_price_html($price, $product) {
    if (!is_product()) {
        $price = '<span class="custom-price-loop d-block mb-2">' . $price . '</span>';
    }
    return $price;
}

/**
 * Forzar cálculo de impuestos para usuarios no logeados
 * Esto asegura que los precios con impuestos se muestren correctamente
 */
add_filter('woocommerce_cart_ready_to_calc_shipping', '__return_true');

/**
 * Asegurar que los totales se calculen en el checkout
 * Solo en páginas específicas para evitar loops infinitos
 */
add_action('woocommerce_checkout_update_order_review', function() {
    if (WC()->cart && !WC()->cart->is_empty()) {
        WC()->cart->calculate_totals();
    }
});

/**
 * Forzar visualización de impuestos en contextos específicos
 * Evita llamar calculate_totals() recursivamente
 */
add_filter('woocommerce_cart_needs_payment', function($needs_payment, $cart) {
    // Asegurar que se calculen impuestos sin crear loops
    if ($cart && is_checkout() && !did_action('woocommerce_calculated_totals')) {
        $cart->calculate_totals();
    }
    return $needs_payment;
}, 10, 2);
