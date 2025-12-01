<?php
/**
 * Footer minimalista (Bootstrap example)
 * 
 * Editable via Widgets: Minimal footer with widget area
 */
?>
<footer class="site-footer py-4 border-top">
  <div class="container">
    <div class="text-center">
      <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
        <?php dynamic_sidebar( 'footer-1' ); ?>
      <?php else : ?>
        <p class="text-muted mb-0">&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></p>
        <div class="alert alert-info py-2 mt-2" role="alert">
          <small><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 1 for footer content','bootstrap-theme'); ?></small>
        </div>
      <?php endif; ?>
    </div>
  </div>
</footer>
