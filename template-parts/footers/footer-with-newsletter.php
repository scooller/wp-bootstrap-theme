<?php
/**
 * Footer con newsletter (Bootstrap example)
 */
?>
<footer class="site-footer py-5 border-top">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
          <?php 
          // Capturar output del widget y procesar shortcodes
          ob_start();
          dynamic_sidebar( 'footer-1' );
          $widget_output = ob_get_clean();
          echo do_shortcode( $widget_output );
          ?>
        <?php else : ?>
          <h5 class="mb-3"><?php esc_html_e('Subscribe to our newsletter','bootstrap-theme'); ?></h5>
          <p class="text-muted mb-3"><?php esc_html_e('Add newsletter widget in Appearance → Widgets → Footer 1, or use shortcode [bootstrap_newsletter]','bootstrap-theme'); ?></p>
          <div class="alert alert-info py-2">
            <small><?php esc_html_e('Use shortcode [bootstrap_newsletter] for newsletter functionality','bootstrap-theme'); ?></small>
          </div>
        <?php endif; ?>
      </div>
      <div class="col-md-6 text-md-end mt-4 mt-md-0">
        <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
          <?php 
          // Capturar output del widget y procesar shortcodes
          ob_start();
          dynamic_sidebar( 'footer-2' );
          $widget_output = ob_get_clean();
          echo do_shortcode( $widget_output );
          ?>
        <?php else : ?>
          <?php
          wp_nav_menu([
            'theme_location' => 'footer',
            'container' => false,
            'menu_class' => 'list-unstyled d-inline-flex gap-3 mb-0',
            'fallback_cb' => false,
            'depth' => 2,
            'walker' => new WP_Bootstrap_Navwalker(),
          ]);
          ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>
