<?php
/**
 * Footer with Columns (Bootstrap example)
 */
?>
<footer class="site-footer py-5 border-top">
  <div class="container">
    <div class="row">
      <div class="col-6 col-md-2 mb-3">
        <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
          <?php 
          // Capturar output del widget y procesar shortcodes
          ob_start();
          dynamic_sidebar( 'footer-1' );
          $widget_output = ob_get_clean();
          echo do_shortcode( $widget_output );
          ?>
        <?php else : ?>
          <h5><?php esc_html_e('Footer Column 1','bootstrap-theme'); ?></h5>
          <p class="text-muted"><?php esc_html_e('Añade widgets en Apariencia → Widgets → Footer 1','bootstrap-theme'); ?></p>
        <?php endif; ?>
      </div>

      <div class="col-6 col-md-2 mb-3">
        <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
          <?php dynamic_sidebar( 'footer-2' ); ?>
        <?php else : ?>
          <h5><?php esc_html_e('Footer Column 2','bootstrap-theme'); ?></h5>
          <p class="text-muted"><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 2','bootstrap-theme'); ?></p>
        <?php endif; ?>
      </div>

      <div class="col-6 col-md-2 mb-3">
        <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
          <?php dynamic_sidebar( 'footer-3' ); ?>
        <?php else : ?>
          <h5><?php esc_html_e('Footer Column 3','bootstrap-theme'); ?></h5>
          <p class="text-muted"><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 3','bootstrap-theme'); ?></p>
        <?php endif; ?>
      </div>

      <div class="col-md-5 offset-md-1 mb-3">
        <?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
          <?php dynamic_sidebar( 'footer-4' ); ?>
        <?php else : ?>
          <form>
            <h5><?php esc_html_e('Subscribe to our newsletter','bootstrap-theme'); ?></h5>
            <p class="text-muted"><?php esc_html_e('Add newsletter widget in Appearance → Widgets → Footer 4','bootstrap-theme'); ?></p>
            <div class="d-flex flex-column flex-sm-row w-100 gap-2">
              <label for="newsletter1" class="visually-hidden"><?php esc_html_e('Email address','bootstrap-theme'); ?></label>
              <input id="newsletter1" type="text" class="form-control" placeholder="<?php esc_attr_e('Email address','bootstrap-theme'); ?>">
              <button class="btn btn-primary" type="button"><?php esc_html_e('Subscribe','bootstrap-theme'); ?></button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-between py-4 my-4 border-top">
      <p class="mb-0 text-muted">&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></p>
      <div class="ms-sm-3">
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
      </div>
    </div>
  </div>
</footer>
