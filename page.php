<?php
/**
 * The template for displaying all pages
 *
 * @package BootstrapTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <?php
    // Verificar si hay contenido
    while (have_posts()) : the_post();
        
        // Hero section con imagen destacada como fondo
        if (has_post_thumbnail()) :
            $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
        ?>
            <section class="hero-section position-relative d-flex align-items-center justify-content-center text-white" 
                     style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?php echo esc_url($featured_image); ?>') center/cover no-repeat; min-height: 50vh;">
                <div class="container text-center">
                    <?php 
                    $hero_text = get_field('page_hero_text');
                    if ($hero_text) : 
                    ?>
                        <div class="hero-content">
                            <?php echo wp_kses_post($hero_text); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif;
        
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
    
    <div class="<?php echo esc_attr($container_class); ?>" <?php  if (has_post_thumbnail()) ?>>
        <div class="row">
            <div class="<?php echo $show_sidebar ? 'col-lg-8' : 'col-12'; ?>">
                <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                    
                    <?php 
                    // Solo mostrar título si no hay imagen destacada y debe mostrarse según configuración
                    if (!has_post_thumbnail() && $show_title) : 
                    ?>
                        <header class="entry-header mb-4">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                            
                            <?php
                            // Meta información de la página si está habilitada
                            $show_date = function_exists('bootstrap_theme_should_show_meta_date') ? bootstrap_theme_should_show_meta_date() : false;
                            $show_author = function_exists('bootstrap_theme_should_show_meta_author') ? bootstrap_theme_should_show_meta_author() : false;
                            
                            if ($show_date || $show_author) :
                            ?>
                                <div class="entry-meta text-muted mb-3">
                                    <small>
                                        <?php if ($show_date) : ?>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <?php echo esc_html(get_the_date()); ?>
                                            </time>
                                        <?php endif; ?>
                                        
                                        <?php if ($show_date && $show_author) : ?>
                                            <span class="mx-2">|</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($show_author) : ?>
                                            <i class="fas fa-user me-1"></i>
                                            <span><?php esc_html_e('Por:', 'bootstrap-theme'); ?> <?php the_author(); ?></span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php
                        the_content();

                        // Paginación de páginas largas
                        wp_link_pages(array(
                            'before' => '<div class="page-links mt-4"><nav aria-label="' . esc_attr__('Páginas del contenido', 'bootstrap-theme') . '"><ul class="pagination justify-content-center">',
                            'after'  => '</ul></nav></div>',
                            'link_before' => '<li class="page-item"><span class="page-link">',
                            'link_after'  => '</span></li>',
                            'before_page_number' => '<li class="page-item"><span class="page-link">',
                            'after_page_number' => '</span></li>',
                        ));
                        ?>
                    </div>

                    <?php
                    // Mostrar comentarios si están habilitados
                    if (comments_open() || get_comments_number()) :
                    ?>
                        <footer class="entry-footer mt-5">
                            <?php comments_template(); ?>
                        </footer>
                    <?php endif; ?>
                    
                </article>
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