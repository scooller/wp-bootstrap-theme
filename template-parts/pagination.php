<?php

/**
 * Template part for displaying pagination
 *
 * @package BootstrapTheme
 */

global $wp_query;

// Para WooCommerce, usar sus propias funciones de paginación
if (function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag())) {
    $total_pages = wc_get_loop_prop('total_pages');
    if ($total_pages <= 1) {
        return;
    }
} else {
    // Para posts normales
    if ($wp_query->max_num_pages <= 1) {
        return;
    }
}
?>

<nav aria-label="<?php _e('Paginación de entradas', 'bootstrap-theme'); ?>" class="mt-5">
    <div class="row">
        <div class="col-md-6">
            <?php
            $prev_link = get_previous_posts_link(__('&laquo; Entradas Anteriores', 'bootstrap-theme'));
            if ($prev_link) {
                echo '<div class="previous-posts">' . $prev_link . '</div>';
            }
            ?>
        </div>
        <div class="col-md-6 text-md-end">
            <?php
            $next_link = get_next_posts_link(__('Siguientes Entradas &raquo;', 'bootstrap-theme'));
            if ($next_link) {
                echo '<div class="next-posts">' . $next_link . '</div>';
            }
            ?>
        </div>
    </div>

    <?php
    // Numbered pagination - usar diferentes funciones según el contexto
    if (function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag())) {
        // Para WooCommerce
        $pagination = paginate_links(array(
            'total'     => wc_get_loop_prop('total_pages'),
            'current'   => max(1, get_query_var('paged')),
            'type'      => 'array',
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>',
        ));
    } else {
        // Para posts normales
        $pagination = paginate_links(array(
            'type'      => 'array',
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>',
        ));
    }

    if ($pagination) :
    ?>
        <ul class="pagination justify-content-center mt-4">
            <?php foreach ($pagination as $page) : ?>
                <li class="page-item<?php echo strpos($page, 'current') !== false ? ' active' : ''; ?>">
                    <?php
                    if (strpos($page, 'current') !== false) {
                        echo str_replace('page-numbers current', 'page-link', $page);
                    } else {
                        echo str_replace('page-numbers', 'page-link', $page);
                    }
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</nav>
