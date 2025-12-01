<?php
/**
 * Footer oscuro (Bootstrap example)
 * 
 * Editable via Widgets: Dark footer with widget areas
 */
?>
<footer class="site-footer py-5 bg-dark text-light border-top border-secondary">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
          <?php dynamic_sidebar( 'footer-1' ); ?>
        <?php else : ?>
          <p class="mb-0">&copy; <?php echo esc_html(date('Y')); ?> <a class="text-light text-decoration-none" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></p>
          <div class="alert alert-dark py-2 mt-2" role="alert">
            <small class="text-light"><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 1 for copyright info','bootstrap-theme'); ?></small>
          </div>
        <?php endif; ?>
      </div>
      <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
          <?php dynamic_sidebar( 'footer-2' ); ?>
        <?php else : ?>
          <?php
          if ( has_nav_menu( 'footer' ) ) {
            wp_nav_menu([
              'theme_location' => 'footer',
              'container' => false,
              'menu_class' => 'list-unstyled d-inline-flex gap-3 mb-0',
              'fallback_cb' => false,
              'depth' => 2,
              'walker' => new WP_Bootstrap_Navwalker(),
            ]);
          } else {
            echo '<div class="alert alert-dark py-2" role="alert">';
            echo '<small class="text-light">' . esc_html__('Add widgets in Appearance → Widgets → Footer 2 or setup Footer menu','bootstrap-theme') . '</small>';
            echo '</div>';
          }
          ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>
