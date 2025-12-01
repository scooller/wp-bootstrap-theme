<?php
/**
 * Footer Simple (Bootstrap example variant)
 * 
 * Editable via Widgets: Uses widgets for flexible content management
 */
?>
<footer class="site-footer py-5 border-top">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-6 mb-3">
        <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
          <?php dynamic_sidebar( 'footer-1' ); ?>
        <?php else : ?>
          <a href="<?php echo esc_url( home_url('/') ); ?>" class="d-inline-flex align-items-center mb-2 text-decoration-none">
            <?php bloginfo('name'); ?>
          </a>
          <p class="text-muted mb-0">&copy; <?php echo esc_html( date('Y') ); ?> <?php bloginfo('name'); ?></p>
          <div class="alert alert-info py-2 mt-2" role="alert">
            <small><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 1','bootstrap-theme'); ?></small>
          </div>
        <?php endif; ?>
      </div>
      <div class="col-12 col-md-6 mb-3 text-md-end">
        <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
          <?php dynamic_sidebar( 'footer-2' ); ?>
        <?php else : ?>
          <?php
          if ( has_nav_menu( 'footer' ) ) {
            wp_nav_menu([
              'theme_location' => 'footer',
              'container' => false,
              'menu_class' => 'list-unstyled d-flex flex-wrap justify-content-md-end mb-0 gap-3',
              'fallback_cb' => false,
              'depth' => 2,
              'walker' => new WP_Bootstrap_Navwalker(),
            ]);
          } else {
            echo '<div class="alert alert-info py-2" role="alert">';
            echo '<small>' . esc_html__('Add widgets in Appearance → Widgets → Footer 2 or setup Footer menu','bootstrap-theme') . '</small>';
            echo '</div>';
          }
          ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>
