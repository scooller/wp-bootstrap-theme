<?php

/**
 * Template part for displaying posts
 *
 * @package BootstrapTheme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('mb-5'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail mb-3">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('large', array('class' => 'img-fluid rounded')); ?>
            </a>
        </div>
    <?php endif; ?>

    <header class="entry-header mb-3">
        <?php if (is_singular()) : ?>
            <?php if (function_exists('bootstrap_theme_should_show_page_title') ? bootstrap_theme_should_show_page_title() : true) : ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
            <?php endif; ?>
        <?php else : ?>
            <h2 class="entry-title">
                <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                    <?php the_title(); ?>
                </a>
            </h2>
        <?php endif; ?>

        <?php
            $show_date = function_exists('bootstrap_theme_should_show_meta_date') ? bootstrap_theme_should_show_meta_date() : true;
            $show_author = function_exists('bootstrap_theme_should_show_meta_author') ? bootstrap_theme_should_show_meta_author() : true;
            $has_meta = $show_date || $show_author || has_category() || comments_open() || get_comments_number();
        ?>
        <?php if ($has_meta) : ?>
            <div class="entry-meta text-muted mb-2">
                <small>
                    <?php if ($show_date) : ?>
                        <i class="fas fa-calendar-alt me-1"></i>
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    <?php endif; ?>

                    <?php if ($show_date && ($show_author || has_category() || comments_open() || get_comments_number())) : ?>
                        <span class="mx-2">|</span>
                    <?php endif; ?>

                    <?php if ($show_author) : ?>
                        <i class="fas fa-user me-1"></i>
                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="text-muted text-decoration-none"><?php the_author(); ?></a>
                    <?php endif; ?>

                    <?php if ($show_author && (has_category() || comments_open() || get_comments_number())) : ?>
                        <span class="mx-2">|</span>
                    <?php endif; ?>

                    <?php if (has_category()) : ?>
                        <i class="fas fa-folder me-1"></i>
                        <?php the_category(', '); ?>
                    <?php endif; ?>

                    <?php if ((has_category() || $show_author || $show_date) && (comments_open() || get_comments_number())) : ?>
                        <span class="mx-2">|</span>
                    <?php endif; ?>

                    <?php if (comments_open() || get_comments_number()) : ?>
                        <i class="fas fa-comment me-1"></i>
                        <a href="<?php comments_link(); ?>" class="text-muted text-decoration-none">
                            <?php
                            $comments_number = get_comments_number();
                            if ($comments_number == 0) {
                                _e('Sin Comentarios', 'bootstrap-theme');
                            } elseif ($comments_number == 1) {
                                _e('1 Comentario', 'bootstrap-theme');
                            } else {
                                printf(__('%s Comentarios', 'bootstrap-theme'), $comments_number);
                            }
                            ?>
                        </a>
                    <?php endif; ?>
                </small>
            </div>
        <?php endif; ?>
    </header>

    <div class="entry-content">
        <?php if (is_singular()) : ?>
            <?php the_content(); ?>

            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links mt-4"><span class="page-links-title">' . __('Páginas:', 'bootstrap-theme') . '</span>',
                'after'  => '</div>',
                'link_before' => '<span class="page-number">',
                'link_after'  => '</span>',
            ));
            ?>
        <?php else : ?>
            <?php the_excerpt(); ?>
            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                <?php _e('Leer Más', 'bootstrap-theme'); ?>
                <i class="fas fa-arrow-right ms-1"></i>
            </a>
        <?php endif; ?>
    </div>

    <?php if (is_singular() && has_tag()) : ?>
        <footer class="entry-footer mt-4">
            <div class="tags">
                <i class="fas fa-tags me-2"></i>
                <?php the_tags('', ', ', ''); ?>
            </div>
        </footer>
    <?php endif; ?>
</article>
