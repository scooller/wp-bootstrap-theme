<?php

/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package BootstrapTheme
 */
?>

<section class="no-results not-found">
    <div class="text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-4"></i>

        <header class="page-header mb-4">
            <h1 class="page-title"><?php _e('No hay nada aquí', 'bootstrap-theme'); ?></h1>
        </header>

        <div class="page-content">
            <?php if (is_home() && current_user_can('publish_posts')) : ?>
                <p class="lead mb-4">
                    <?php
                    printf(
                        wp_kses(
                            __('¿Listo para publicar tu primera entrada? <a href="%1$s">Comienza aquí</a>.', 'bootstrap-theme'),
                            array(
                                'a' => array(
                                    'href' => array(),
                                ),
                            )
                        ),
                        esc_url(admin_url('post-new.php'))
                    );
                    ?>
                </p>
            <?php elseif (is_search()) : ?>
                <p class="lead mb-4">
                    <?php _e('Lo siento, pero no encontramos coincidencias con tus términos de búsqueda. Por favor intenta de nuevo con palabras clave diferentes.', 'bootstrap-theme'); ?>
                </p>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <?php get_search_form(); ?>
                    </div>
                </div>
            <?php else : ?>
                <p class="lead mb-4">
                    <?php _e('Parece que no podemos encontrar lo que buscas. Tal vez una búsqueda pueda ayudar.', 'bootstrap-theme'); ?>
                </p>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <?php get_search_form(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
