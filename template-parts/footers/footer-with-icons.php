<?php
/**
 * Footer con iconos sociales (Bootstrap example)
 *
 * Editable via Widgets: Flexible footer with widget areas
 */
?>
<footer class="site-footer py-4 border-top">
  <div class="container">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
      <div class="mb-0">
        <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
          <?php dynamic_sidebar( 'footer-1' ); ?>
        <?php else : ?>
          <p class="mb-0 text-muted">&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></p>
          <div class="alert alert-info py-2 mt-2" role="alert">
            <small><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 1 for copyright info','bootstrap-theme'); ?></small>
          </div>
        <?php endif; ?>
      </div>
      <div class="mb-0">
        <ul class="social-links list-unstyled d-flex mb-0 gap-3">
          <?php $facebook_url = bootstrap_theme_get_extra_option('facebook_url'); if ($facebook_url): ?>
            <li><a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
          <?php endif; ?>
          <?php $twitter_url = bootstrap_theme_get_extra_option('twitter_url'); if ($twitter_url): ?>
            <li><a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X"><i class="fab fa-x-twitter"></i></a></li>
          <?php endif; ?>
          <?php $instagram_url = bootstrap_theme_get_extra_option('instagram_url'); if ($instagram_url): ?>
            <li><a href="<?php echo esc_url($instagram_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
          <?php endif; ?>
          <?php $linkedin_url = bootstrap_theme_get_extra_option('linkedin_url'); if ($linkedin_url): ?>
            <li><a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></li>
          <?php endif; ?>
          <?php $youtube_url = bootstrap_theme_get_extra_option('youtube_url'); if ($youtube_url): ?>
            <li><a href="<?php echo esc_url($youtube_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>
          <?php endif; ?>
          <?php $tiktok_url = bootstrap_theme_get_extra_option('tiktok_url'); if ($tiktok_url): ?>
            <li><a href="<?php echo esc_url($tiktok_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fab fa-tiktok"></i></a></li>
          <?php endif; ?>
        </ul>
        <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
          <?php dynamic_sidebar( 'footer-2' ); ?>
        <?php else : ?>          
          <div class="alert alert-info py-2 mt-2" role="alert">
            <small><?php esc_html_e('Add widgets in Appearance → Widgets → Footer 2 for social icons or other content','bootstrap-theme'); ?></small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>
