<?php
/**
 * The template for displaying all single posts
 *
 * @package BootstrapTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <?php
    while (have_posts()) : the_post();
        
        // Obtener opciones de página usando las funciones helper
        // Estas funciones ya manejan la prioridad: página override > global
        $show_title = bootstrap_theme_should_show_page_title();
        $show_sidebar = bootstrap_theme_should_show_sidebar();
        $container_width = bootstrap_theme_get_page_container_width();
        
        // Determinar clase de contenedor
        $container_class = 'container';
        if ($container_width === 'container-fluid' || $container_width === 'fluid') {
            $container_class = 'container-fluid';
        } elseif ($container_width && $container_width !== 'default' && $container_width !== 'container') {
            $container_class = $container_width;
        }
    ?>
    
    <div class="<?php echo esc_attr($container_class); ?>">
        <div class="row">
            <div class="<?php echo $show_sidebar ? 'col-lg-8' : 'col-12'; ?>">
                
                <?php get_template_part('template-parts/content', get_post_type()); ?>
                
                <?php
                // Check if post navigation should be displayed for WooCommerce
                $show_navigation = true;
                if (function_exists('bootstrap_theme_get_woocommerce_option') && is_product()) {
                    $show_navigation = bootstrap_theme_get_woocommerce_option('show_product_navigation');
                }
                
                if ($show_navigation) {
                    // Navegación entre posts
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    
                    if ($prev_post || $next_post) :
                    ?>
                        <nav class="post-navigation mt-5 pt-4 border-top" aria-label="<?php esc_attr_e('Navegación entre entradas', 'bootstrap-theme'); ?>">
                            <div class="row">
                                <?php if ($prev_post) : ?>
                                    <div class="col-6 mb-3">
                                        <div class="nav-previous">
                                            <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="btn btn-outline-primary btn-sm">
                                                <svg class="icon me-2">
                                                    <use xlink:href="#fa-arrow-left"></use>
                                                </svg>
                                            <div class="nav-text">
                                                <span class="nav-subtitle text-muted small d-block"><?php esc_html_e('Entrada anterior', 'bootstrap-theme'); ?></span>
                                                <span class="nav-title"><?php echo esc_html(get_the_title($prev_post)); ?></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($next_post) : ?>
                                <div class="col-6 mb-3">
                                    <div class="nav-next text-md-end">
                                        <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="btn btn-outline-primary btn-sm">
                                            <div class="nav-text">
                                                <span class="nav-subtitle text-muted small d-block"><?php esc_html_e('Siguiente entrada', 'bootstrap-theme'); ?></span>
                                                <span class="nav-title"><?php echo esc_html(get_the_title($next_post)); ?></span>
                                            </div>
                                            <svg class="icon ms-2">
                                                <use xlink:href="#fa-arrow-right"></use>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php 
                    endif; 
                }
                ?>
                
                <?php
                // Mostrar posts relacionados (desde Extras)
                $show_related_posts = bootstrap_theme_get_extra_option('show_related_posts');
                // Coerción: por defecto true si no está configurado explícitamente a 0/false
                $show_related_posts = ($show_related_posts === '' || $show_related_posts === null) ? true : (bool)$show_related_posts;
                
                if ($show_related_posts) :
                    $categories = get_the_category();
                    if (!empty($categories)) :
                        $category_ids = array();
                        foreach ($categories as $category) {
                            $category_ids[] = $category->term_id;
                        }
                        
                        $related_posts = get_posts(array(
                            'category__in' => $category_ids,
                            'post__not_in' => array(get_the_ID()),
                            'posts_per_page' => 3,
                            'ignore_sticky_posts' => 1
                        ));
                        
                        if (!empty($related_posts)) :
                ?>
                            <section class="related-posts mt-5 pt-4 border-top">
                                <h3 class="h4 mb-4"><?php esc_html_e('Entradas relacionadas', 'bootstrap-theme'); ?></h3>
                                <div class="row g-3">
                                    <?php foreach ($related_posts as $post) : setup_postdata($post); ?>
                                        <div class="col-md-4">
                                            <article class="card h-100">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <div class="card-img-top" style="height: 200px; overflow: hidden;">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php the_post_thumbnail('medium', array('class' => 'w-100 h-100', 'style' => 'object-fit: cover;')); ?>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                                                            <?php the_title(); ?>
                                                        </a>
                                                    </h5>
                                                    
                                                    <div class="card-meta text-muted small mb-2">
                                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                            <?php echo esc_html(get_the_date()); ?>
                                                        </time>
                                                    </div>
                                                    
                                                    <p class="card-text">
                                                        <?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?>
                                                    </p>
                                                    
                                                    <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-outline-primary">
                                                        <?php esc_html_e('Leer más', 'bootstrap-theme'); ?>
                                                    </a>
                                                </div>
                                            </article>
                                        </div>
                                    <?php endforeach; wp_reset_postdata(); ?>
                                </div>
                            </section>
                <?php 
                        endif;
                    endif;
                endif;
                ?>
                
                <?php
                // Mostrar comentarios si están habilitados
                if (comments_open() || get_comments_number()) :
                ?>
                    <section class="comments-section mt-5 pt-4 border-top">
                        <?php comments_template('/template-parts/comments-bootstrap.php'); ?>
                    </section>
                <?php endif; ?>
                
            </div>
            
            <?php if ($show_sidebar) : ?>
                <div class="col-lg-4">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>