<?php
/**
 * Sticky footer (Bootstrap example)
 *
 * Editable via Widgets: Sticky footer with widget areas
 * Nota: Para que sea sticky, envuelve el layout con `d-flex flex-column min-vh-100` y aplica `mt-auto` al footer.
 */
?>
<footer class="site-footer mt-auto py-3 border-top bg-light">
  <div class="container d-flex justify-content-between align-items-start">
    <div>
      <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
        <?php dynamic_sidebar( 'footer-1' ); ?>
      <?php else : ?>
        <span class="text-muted">&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></span>
        <div class="alert alert-info py-2 mt-2" role="alert">
          <small><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 1 for left content','bootstrap-theme'); ?></small>
        </div>
      <?php endif; ?>
    </div>
    <div>
      <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
        <?php dynamic_sidebar( 'footer-2' ); ?>
      <?php else : ?>
        <span class="text-muted"><a class="text-muted text-decoration-none" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to home','bootstrap-theme'); ?></a></span>
        <div class="alert alert-info py-2 mt-2" role="alert">
          <small><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 2 for right content','bootstrap-theme'); ?></small>
        </div>
      <?php endif; ?>
    </div>
  </div>
</footer>
