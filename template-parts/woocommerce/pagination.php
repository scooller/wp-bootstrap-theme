<?php
/**
 * Template part for WooCommerce pagination with Bootstrap 5.3
 *
 * @package Bootstrap_Theme
 * @since 1.1.5
 * 
 * LESSONS LEARNED:
 * - WooCommerce pagination requires custom offset calculation: ($current_page - 1) * $posts_per_page
 * - Global variables are essential for page state communication between functions
 * - wc_get_loop_prop() may not always return correct current_page in template parts
 * - Priority order: $bootstrap_theme_current_page (global) > wc_get_loop_prop() > get_query_var()
 * - Bootstrap 5.3 pagination uses: .pagination, .page-item, .page-link, .justify-content-center
 * - Template parts improve code organization and maintenance
 * - Always include accessibility attributes: aria-label, aria-current="page"
 * - Ellipsis (...) handling improves UX for large page ranges
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

global $wp_query, $bootstrap_theme_current_page;

$total_pages = wc_get_loop_prop('total_pages');
$current_page = wc_get_loop_prop('current_page');

if (!$total_pages) {
    $total_pages = $wp_query->max_num_pages;
}

// Priorizar la variable global que se establece en la función principal
if (isset($bootstrap_theme_current_page) && $bootstrap_theme_current_page > 0) {
    $current_page = $bootstrap_theme_current_page;
} elseif (!$current_page) {
    $current_page = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
}

// Solo mostrar si hay más de una página
if ($total_pages <= 1) return;

$big = 999999999;

?>

<nav aria-label="<?php esc_attr_e('Navegación de productos', 'bootstrap-theme'); ?>" class="wc-pagination mt-4">
    <ul class="pagination justify-content-center">
        
        <?php // Botón anterior ?>
        <?php if ($current_page > 1) : ?>
            <?php $prev_url = str_replace($big, $current_page - 1, get_pagenum_link($big)); ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo esc_url($prev_url); ?>" aria-label="<?php esc_attr_e('Página anterior', 'bootstrap-theme'); ?>">
                    &laquo; <?php esc_html_e('Anterior', 'bootstrap-theme'); ?>
                </a>
            </li>
        <?php endif; ?>
        
        <?php 
        // Números de página
        $start_page = max(1, $current_page - 2);
        $end_page = min($total_pages, $current_page + 2);
        
        // Mostrar primera página si no está en el rango
        if ($start_page > 1) :
            $first_url = str_replace($big, 1, get_pagenum_link($big));
        ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo esc_url($first_url); ?>">1</a>
            </li>
            <?php if ($start_page > 2) : ?>
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php // Rango de páginas ?>
        <?php for ($i = $start_page; $i <= $end_page; $i++) : ?>
            <?php if ($i == $current_page) : ?>
                <li class="page-item active" aria-current="page">
                    <span class="page-link"><?php echo $i; ?></span>
                </li>
            <?php else : ?>
                <?php $page_url = str_replace($big, $i, get_pagenum_link($big)); ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo esc_url($page_url); ?>"><?php echo $i; ?></a>
                </li>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php 
        // Mostrar última página si no está en el rango
        if ($end_page < $total_pages) :
            if ($end_page < $total_pages - 1) :
        ?>
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            <?php endif; ?>
            <?php $last_url = str_replace($big, $total_pages, get_pagenum_link($big)); ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo esc_url($last_url); ?>"><?php echo $total_pages; ?></a>
            </li>
        <?php endif; ?>
        
        <?php // Botón siguiente ?>
        <?php if ($current_page < $total_pages) : ?>
            <?php $next_url = str_replace($big, $current_page + 1, get_pagenum_link($big)); ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo esc_url($next_url); ?>" aria-label="<?php esc_attr_e('Página siguiente', 'bootstrap-theme'); ?>">
                    <?php esc_html_e('Siguiente', 'bootstrap-theme'); ?> &raquo;
                </a>
            </li>
        <?php endif; ?>
        
    </ul>
</nav>