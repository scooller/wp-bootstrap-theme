<?php
/**
 * Template helper functions
 *
 * @package BootstrapTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Mostrar mensaje de alerta del tema sobre el header
 * Usa Bootstrap Alert con dismiss button y soporte para shortcodes
 */
function bootstrap_theme_show_alert_message() {
    if (!function_exists('bootstrap_theme_get_option')) {
        return;
    }
    
    $show_alert = bootstrap_theme_get_option('show_alert_message');
    $alert_message = bootstrap_theme_get_option('alert_message');
    $alert_type = bootstrap_theme_get_option('alert_type') ?: 'primary';
    $alert_dismissible = bootstrap_theme_get_option('alert_dismissible');
    
    // No mostrar si está desactivado o no hay mensaje
    if (!$show_alert || empty($alert_message)) {
        return;
    }
    
    // Procesar shortcodes en el mensaje
    $alert_message = do_shortcode($alert_message);
    
    // Clases del alert
    $alert_classes = 'alert alert-' . esc_attr($alert_type) . ' mb-0';
    if ($alert_dismissible) {
        $alert_classes .= ' alert-dismissible fade show';
    }
    
    ?>
    <div class="<?php echo esc_attr($alert_classes); ?>" role="alert">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <?php 
                    // Nota: No usar wp_kses aquí porque los shortcodes ya sanitizan su output
                    // y wp_kses rompe el JavaScript inline generado por shortcodes
                    echo $alert_message; 
                    ?>
                </div>
            </div>
        </div>
        <?php if ($alert_dismissible) : ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php esc_attr_e('Cerrar', 'bootstrap-theme'); ?>"></button>
        <?php endif; ?>
    </div>
    <?php
}
add_action('wp_body_open', 'bootstrap_theme_show_alert_message', 5);

/**
 * Bootstrap Nav Walker Class
 */
class Bootstrap_Nav_Walker extends Walker_Nav_Menu
{

    /**
     * Start Level - Wraps the sub menu in specified tags
     */
    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
    }

    /**
     * End Level - Closes the sub menu tags
     */
    function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * Start Element - Wraps each menu item in specified tags
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Check if menu item has children
        $has_children = in_array('menu-item-has-children', $classes);

        if ($depth === 0) {
            $class_names = $has_children ? 'nav-item dropdown' : 'nav-item';
        } else {
            $class_names = '';
        }

        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) . '"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn) . '"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url) . '"' : '';

        if ($depth === 0) {
            if ($has_children) {
                $attributes .= ' class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"';
            } else {
                $attributes .= ' class="nav-link"';
            }
        } else {
            $attributes .= ' class="dropdown-item"';
        }

        $item_output = $args->before ?? '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= ($args->link_before ?? '') . apply_filters('the_title', $item->title, $item->ID) . ($args->link_after ?? '');
        $item_output .= '</a>';
        $item_output .= $args->after ?? '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * End Element - Closes each menu item
     */
    function end_el(&$output, $item, $depth = 0, $args = array())
    {
        $output .= "</li>\n";
    }
}

/**
 * Get social media links
 */
function bootstrap_theme_get_social_links()
{
    $social_networks = array(
        'facebook'  => array('icon' => 'fab fa-facebook-f', 'label' => __('Facebook', 'bootstrap-theme')),
        'twitter'   => array('icon' => 'fab fa-twitter', 'label' => __('Twitter', 'bootstrap-theme')),
        'instagram' => array('icon' => 'fab fa-instagram', 'label' => __('Instagram', 'bootstrap-theme')),
        'linkedin'  => array('icon' => 'fab fa-linkedin-in', 'label' => __('LinkedIn', 'bootstrap-theme')),
        'youtube'   => array('icon' => 'fab fa-youtube', 'label' => __('YouTube', 'bootstrap-theme')),
        'github'    => array('icon' => 'fab fa-github', 'label' => __('GitHub', 'bootstrap-theme')),
    );

    $output = '';

    foreach ($social_networks as $network => $data) {
        $url = get_theme_mod("social_{$network}");
        if ($url) {
            $output .= sprintf(
                '<a href="%s" class="social-link me-3" target="_blank" rel="noopener noreferrer" aria-label="%s">
                    <i class="%s"></i>
                </a>',
                esc_url($url),
                esc_attr($data['label']),
                esc_attr($data['icon'])
            );
        }
    }

    return $output;
}

/**
 * Get post reading time
 */
function bootstrap_theme_get_reading_time($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words per minute

    return sprintf(_n('%d minute read', '%d minutes read', $reading_time, 'bootstrap-theme'), $reading_time);
}

/**
 * Custom excerpt with more control
 */
function bootstrap_theme_get_excerpt($post_id = null, $length = 20, $more = '...')
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $excerpt = get_post_field('post_excerpt', $post_id);

    if (empty($excerpt)) {
        $content = get_post_field('post_content', $post_id);
        $excerpt = wp_trim_words($content, $length, $more);
    }

    return $excerpt;
}

/**
 * Get featured image with fallback
 */
function bootstrap_theme_get_featured_image($post_id = null, $size = 'large', $fallback = true)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail($post_id, $size, array('class' => 'img-fluid'));
    } elseif ($fallback) {
        return '<div class="placeholder-image bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-image fa-3x text-muted"></i>
                </div>';
    }

    return '';
}

/**
 * Breadcrumb function
 */
function bootstrap_theme_breadcrumb()
{
    if (is_front_page()) {
        return;
    }

    $delimiter = ' <i class="fas fa-chevron-right mx-2"></i> ';
    $home = __('Home', 'bootstrap-theme');
    $before = '<li class="breadcrumb-item active" aria-current="page">';
    $after = '</li>';

    echo '<nav aria-label="breadcrumb"><ol class="breadcrumb py-2">';
    echo '<li class="breadcrumb-item"><a href="' . home_url() . '">' . $home . '</a></li>';

    if (is_category()) {
        $cat_obj = get_queried_object();
        $thisCat = $cat_obj->term_id;
        $thisCat = get_category($thisCat);
        $parentCat = get_category($thisCat->parent);

        if ($thisCat->parent != 0) {
            echo '<li class="breadcrumb-item"><a href="' . get_category_link($parentCat->term_id) . '">' . $parentCat->cat_name . '</a></li>';
        }

        echo $before . single_cat_title('', false) . $after;
    } elseif (is_day()) {
        echo '<li class="breadcrumb-item"><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
        echo '<li class="breadcrumb-item"><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a></li>';
        echo $before . get_the_time('d') . $after;
    } elseif (is_month()) {
        echo '<li class="breadcrumb-item"><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
        echo $before . get_the_time('F') . $after;
    } elseif (is_year()) {
        echo $before . get_the_time('Y') . $after;
    } elseif (is_single() && !is_attachment()) {
        if (get_post_type() != 'post') {
            $post_type = get_post_type_object(get_post_type());
            echo '<li class="breadcrumb-item"><a href="' . get_post_type_archive_link(get_post_type()) . '">' . $post_type->labels->singular_name . '</a></li>';
            echo $before . get_the_title() . $after;
        } else {
            $cat = get_the_category();
            $cat = $cat[0];
            echo '<li class="breadcrumb-item"><a href="' . get_category_link($cat->term_id) . '">' . $cat->cat_name . '</a></li>';
            echo $before . get_the_title() . $after;
        }
    } elseif (is_page() && !$post->post_parent) {
        echo $before . get_the_title() . $after;
    } elseif (is_page() && $post->post_parent) {
        $parent_id = $post->post_parent;
        $breadcrumbs = array();

        while ($parent_id) {
            $page = get_page($parent_id);
            $breadcrumbs[] = '<li class="breadcrumb-item"><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
            $parent_id = $page->post_parent;
        }

        $breadcrumbs = array_reverse($breadcrumbs);
        foreach ($breadcrumbs as $crumb) {
            echo $crumb;
        }

        echo $before . get_the_title() . $after;
    } elseif (is_search()) {
        echo $before . __('Search results for: ', 'bootstrap-theme') . get_search_query() . $after;
    } elseif (is_tag()) {
        echo $before . __('Tag: ', 'bootstrap-theme') . single_tag_title('', false) . $after;
    } elseif (is_author()) {
        global $author;
        $userdata = get_userdata($author);
        echo $before . __('Author: ', 'bootstrap-theme') . $userdata->display_name . $after;
    } elseif (is_404()) {
        echo $before . __('Error 404', 'bootstrap-theme') . $after;
    }

    echo '</ol></nav>';
}

/**
 * Related posts function with cache support
 */
function bootstrap_theme_related_posts($post_id = null, $number = 3)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $categories = wp_get_post_categories($post_id);

    if (empty($categories)) {
        return;
    }

    // Verificar si cache está habilitado
    $cache_enabled = function_exists('bootstrap_theme_is_cache_enabled') 
        ? bootstrap_theme_is_cache_enabled() 
        : false;

    $cache_key = 'related_posts_' . $post_id . '_' . $number;
    $related_posts_data = false;

    // Intentar obtener desde cache si está habilitado
    if ($cache_enabled) {
        $related_posts_data = get_transient($cache_key);
    }

    // Si no hay cache o está deshabilitado, ejecutar query
    if (false === $related_posts_data) {
        $args = array(
            'category__in'   => $categories,
            'post__not_in'   => array($post_id),
            'posts_per_page' => $number,
            'orderby'        => 'rand',
            'fields'         => 'ids', // Solo IDs para cache más ligero
        );

        $query = new WP_Query($args);
        $related_posts_data = $query->posts;

        // Guardar en cache solo si está habilitado (TTL: 12 horas)
        if ($cache_enabled && !empty($related_posts_data)) {
            set_transient($cache_key, $related_posts_data, 12 * HOUR_IN_SECONDS);
        }
    }

    // Si hay posts relacionados, renderizar
    if (!empty($related_posts_data)) {
        echo '<div class="related-posts mt-5">';
        echo '<h3 class="mb-4">' . __('Related Posts', 'bootstrap-theme') . '</h3>';
        echo '<div class="row">';

        foreach ($related_posts_data as $related_id) {
            $post = get_post($related_id);
            if (!$post) {
                continue;
            }

            setup_postdata($post);

            echo '<div class="col-md-4 mb-4">';
            echo '<div class="card h-100">';

            if (has_post_thumbnail($related_id)) {
                echo '<div class="card-img-top">';
                echo get_the_post_thumbnail($related_id, 'medium', array('class' => 'img-fluid'));
                echo '</div>';
            }

            echo '<div class="card-body">';
            echo '<h5 class="card-title"><a href="' . get_permalink($related_id) . '" class="text-decoration-none">' . get_the_title($related_id) . '</a></h5>';
            echo '<p class="card-text">' . bootstrap_theme_get_excerpt($related_id, 15) . '</p>';
            echo '<small class="text-muted">' . get_the_date('', $related_id) . '</small>';
            echo '</div>';

            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';

        wp_reset_postdata();
    }
}

/**
 * Invalidar cache de posts relacionados cuando se actualiza un post
 */
function bootstrap_theme_clear_related_posts_cache($post_id)
{
    // Solo para posts (no páginas u otros tipos)
    if (get_post_type($post_id) !== 'post') {
        return;
    }

    // Verificar si cache está habilitado
    if (!function_exists('bootstrap_theme_is_cache_enabled') || !bootstrap_theme_is_cache_enabled()) {
        return;
    }

    // Obtener categorías del post
    $categories = wp_get_post_categories($post_id);

    if (empty($categories)) {
        return;
    }

    // Obtener todos los posts de las mismas categorías
    $args = array(
        'category__in'   => $categories,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    );

    $related_posts = get_posts($args);

    // Limpiar cache de cada post relacionado
    foreach ($related_posts as $related_id) {
        // Borrar diferentes variaciones del cache (para diferentes números de posts)
        for ($i = 1; $i <= 10; $i++) {
            delete_transient('related_posts_' . $related_id . '_' . $i);
        }
    }
}
add_action('save_post', 'bootstrap_theme_clear_related_posts_cache');
add_action('delete_post', 'bootstrap_theme_clear_related_posts_cache');

/**
 * Schema markup for articles
 */
function bootstrap_theme_article_schema()
{
    if (!is_single()) {
        return;
    }

    global $post;
    $author = get_the_author_meta('display_name', $post->post_author);
    $published = get_the_date('c');
    $modified = get_the_modified_date('c');

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title(),
        'author' => array(
            '@type' => 'Person',
            'name' => $author
        ),
        'datePublished' => $published,
        'dateModified' => $modified,
    );

    if (has_post_thumbnail()) {
        $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
        if ($thumbnail) {
            $schema['image'] = $thumbnail[0];
        }
    }

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
}
add_action('wp_head', 'bootstrap_theme_article_schema');

/**
 * Get responsive logo HTML
 * - Uses only height (desktop + mobile); width is auto via CSS
 * - CSS is enqueued via wp_add_inline_style on the main stylesheet handle
 */
function bootstrap_theme_get_responsive_logo($classes = 'me-2', $url = false) {
    $custom_logo = bootstrap_theme_get_option('custom_logo');
    $logo_url = '';

    if ( $custom_logo && isset( $custom_logo['url'] ) ) {
        $logo_url = $custom_logo['url'];
    } else {
        $customizer_logo_id = get_theme_mod( 'custom_logo' );
        if ( $customizer_logo_id ) {
            $logo_url = wp_get_attachment_image_url( $customizer_logo_id, 'full' );
        }
    }

    if ( $logo_url ) {
        // Only output the <img>; CSS for sizes is injected separately at enqueue time
        // fetchpriority="high" para LCP, loading="eager" para evitar lazy load
        $logo_class = 'logo-responsive ' . $classes;
        if( $url ) {
            return esc_url( $logo_url );
        }else{
            return sprintf(
                '<img src="%s" alt="%s" class="%s" decoding="async" fetchpriority="high" loading="eager">',
                esc_url( $logo_url ),
                esc_attr( get_bloginfo( 'name' ) ),
                esc_attr( $logo_class )
            );
        }
    } else {
        if( $url ) {
            return bloginfo( 'name' );
        }else {
            return '<span class="fs-4">'.bloginfo( 'name' ).'</span>';
        }
    }
}

/**
 * Enqueue responsive logo CSS based on theme options
 * Ensures styles are printed with the main stylesheet (after it's enqueued)
 */
function bootstrap_theme_enqueue_responsive_logo_css() {
    // Ensure at least one stylesheet handle is available
    $target_handle = null;
    if ( wp_style_is( 'bootstrap-theme-custom', 'enqueued' ) ) {
        // Attach to custom theme CSS so it prints later and overrides base styles
        $target_handle = 'bootstrap-theme-custom';
    } elseif ( wp_style_is( 'bootstrap-theme-style', 'enqueued' ) ) {
        $target_handle = 'bootstrap-theme-style';
    }

    if ( ! $target_handle ) {
        return; // Bail if styles not enqueued yet
    }

    $logo_height        = intval( bootstrap_theme_get_option('logo_height') );
    $logo_mobile_height = intval( bootstrap_theme_get_option('logo_mobile_height') );

    // Provide sane defaults if options are empty
    if ( $logo_height <= 0 ) {
        $logo_height = 50;
    }
    if ( $logo_mobile_height <= 0 ) {
        $logo_mobile_height = 32;
    }

    // Agregar aspect-ratio para evitar reflows (CLS)
    $css = "img.logo-responsive{height: {$logo_height}px; width:auto; aspect-ratio:auto; contain:layout;}\n@media (max-width:767.98px){img.logo-responsive{height: {$logo_mobile_height}px;}}";
    wp_add_inline_style( $target_handle, $css );
}
add_action( 'wp_enqueue_scripts', 'bootstrap_theme_enqueue_responsive_logo_css', 20 );
