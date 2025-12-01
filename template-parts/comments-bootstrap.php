<?php
/**
 * Bootstrap styled comments template for WooCommerce products and posts
 * @package BootstrapTheme
 */
if (post_password_required()) {
    return;
}
?>
<div class="comments-area mt-5">
    <?php if (have_comments()) : ?>
        <h4 class="mb-4">
            <?php
            $comments_number = get_comments_number();
            if ($comments_number == 1) {
                esc_html_e('1 Comentario', 'bootstrap-theme');
            } else {
                printf(esc_html__('%s Comentarios', 'bootstrap-theme'), $comments_number);
            }
            ?>
        </h4>
        <ul class="list-unstyled">
            <?php
            wp_list_comments(array(
                'style'      => 'ul',
                'short_ping' => true,
                'avatar_size'=> 48,
                'callback'   => function($comment, $args, $depth) {
                    ?>
                    <li <?php comment_class('mb-4'); ?> id="comment-<?php comment_ID(); ?>">
                        <div class="d-flex align-items-start">
                            <?php echo get_avatar($comment, 48, '', '', array('class' => 'rounded-circle me-3')); ?>
                            <div>
                                <div class="fw-bold mb-1">
                                    <?php comment_author_link(); ?>
                                    <span class="text-muted small ms-2">
                                        <?php comment_date('d M Y'); ?>
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <?php comment_text(); ?>
                                </div>
                                <div>
                                    <?php comment_reply_link(array_merge($args, array('reply_text' => esc_html__('Responder', 'bootstrap-theme'), 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            ));
            ?>
        </ul>
    <?php endif; ?>
    <?php if (comments_open()) : ?>
        <div class="mt-5">
            <?php comment_form(array(
                'class_form' => 'mb-4',
                'class_submit' => 'btn btn-primary',
                'title_reply' => esc_html__('Deja un comentario', 'bootstrap-theme'),
                'label_submit' => esc_html__('Enviar', 'bootstrap-theme'),
                'comment_field' => '<div class="mb-3"><textarea class="form-control" id="comment" name="comment" rows="4" required></textarea></div>',
                'fields' => array(
                    'author' => '<div class="mb-3"><input class="form-control" id="author" name="author" type="text" placeholder="Nombre" required></div>',
                    'email'  => '<div class="mb-3"><input class="form-control" id="email" name="email" type="email" placeholder="Email" required></div>',
                ),
            )); ?>
        </div>
    <?php endif; ?>
</div>
