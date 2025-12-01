<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package BootstrapTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="error-404 not-found">
                    <?php
                    // Obtener configuraciones personalizables
                    $error_title = bootstrap_theme_get_option('404_title') ?: esc_html__('¡Oops! Página no encontrada', 'bootstrap-theme');
                    $error_message = bootstrap_theme_get_option('404_message') ?: esc_html__('Lo sentimos, pero la página que buscas no existe. Es posible que haya sido movida, eliminada o que hayas escrito mal la dirección.', 'bootstrap-theme');
                    $error_image = bootstrap_theme_get_option('404_image');
                    $show_search = bootstrap_theme_get_option('404_show_search');
                    $show_search = ($show_search === '' || $show_search === null) ? false : (bool)$show_search;
                    $show_home_button = bootstrap_theme_get_option('404_show_home_button');
                    $show_home_button = ($show_home_button === '' || $show_home_button === null) ? false : (bool)$show_home_button;
                    $home_button_text = bootstrap_theme_get_option('404_home_button_text') ?: esc_html__('Volver al Inicio', 'bootstrap-theme');
                    ?>

                    <?php if ($error_image && isset($error_image['url'])) : ?>
                        <div class="error-image mb-4">
                            <img src="<?php echo esc_url($error_image['url']); ?>" 
                                 alt="<?php echo esc_attr($error_title); ?>" 
                                 class="img-fluid" 
                                 style="max-height: 300px;">
                        </div>
                    <?php else : ?>
                        <div class="error-icon mb-4">
                            <svg class="icon" style="width: 120px; height: 120px; color: #6c757d;">
                                <use xlink:href="#fa-search"></use>
                            </svg>
                        </div>
                    <?php endif; ?>

                    <header class="page-header mb-4">
                        <h1 class="page-title display-4 mb-3"><?php echo esc_html($error_title); ?></h1>
                        <p class="lead text-muted"><?php echo wp_kses_post($error_message); ?></p>
                    </header>

                    <div class="page-content">
                        <?php if ($show_search) : ?>
                            <div class="search-section mb-4">
                                <h3 class="h5 mb-3"><?php esc_html_e('Intenta buscar lo que necesitas:', 'bootstrap-theme'); ?></h3>
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <?php get_search_form(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="actions">
                            <?php if ($show_home_button) : ?>
                                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg me-3">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-home"></use>
                                    </svg>
                                    <?php echo esc_html($home_button_text); ?>
                                </a>
                            <?php endif; ?>
                            
                            <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                                <svg class="icon me-2">
                                    <use xlink:href="#fa-arrow-left"></use>
                                </svg>
                                <?php esc_html_e('Volver Atrás', 'bootstrap-theme'); ?>
                            </button>
                        </div>

                        <!-- Páginas populares o recientes -->
                        <?php
                        $show_recent_posts = bootstrap_theme_get_option('404_show_recent_posts');
                        // Coerción booleana segura: por defecto true si no está configurado explícitamente
                        $show_recent_posts = ($show_recent_posts === '' || $show_recent_posts === null) ? false : (bool)$show_recent_posts;
                        if ($show_recent_posts) :
                            $recent_posts = wp_get_recent_posts(array(
                                'numberposts' => 3,
                                'post_status' => 'publish'
                            ));
                            
                            if (!empty($recent_posts)) :
                        ?>
                            <div class="recent-posts mt-5">
                                <h3 class="h4 mb-4"><?php esc_html_e('O puedes revisar nuestras últimas entradas:', 'bootstrap-theme'); ?></h3>
                                <div class="row g-3">
                                    <?php foreach ($recent_posts as $post_data) : ?>
                                        <div class="col-md-4">
                                            <div class="card h-100">
                                                <?php 
                                                $post_id = $post_data['ID'];
                                                if (has_post_thumbnail($post_id)) :
                                                ?>
                                                    <div class="card-img-top" style="height: 150px; overflow: hidden;">
                                                        <?php echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'w-100 h-100', 'style' => 'object-fit: cover;')); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="text-decoration-none">
                                                            <?php echo esc_html(get_the_title($post_id)); ?>
                                                        </a>
                                                    </h5>
                                                    <p class="card-text text-muted small">
                                                        <?php echo esc_html(wp_trim_words(get_post_field('post_content', $post_id), 15)); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endif; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();