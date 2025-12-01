<?php
/**
 * Template Part: Social Media Links
 * 
 * Muestra los enlaces de redes sociales configurados en las opciones del tema
 * 
 * @package Bootstrap_Theme
 */

// Obtener las URLs de redes sociales
$facebook_url  = bootstrap_theme_get_extra_option('facebook_url');
$twitter_url   = bootstrap_theme_get_extra_option('twitter_url');
$instagram_url = bootstrap_theme_get_extra_option('instagram_url');
$linkedin_url  = bootstrap_theme_get_extra_option('linkedin_url');
$youtube_url   = bootstrap_theme_get_extra_option('youtube_url');
$tiktok_url    = bootstrap_theme_get_extra_option('tiktok_url');

// Verificar si hay al menos una red social configurada
$custom_links = function_exists('get_field') ? get_field('extras_social_custom_links', 'option') : array();
if (!is_array($custom_links)) { $custom_links = array(); }

$has_social_links = ! empty( $facebook_url ) || ! empty( $twitter_url ) || ! empty( $instagram_url ) || 
				   ! empty( $linkedin_url ) || ! empty( $youtube_url ) || ! empty( $tiktok_url ) ||
				   ! empty( $custom_links );

if ( $has_social_links ) : ?>
<div class="social-media-links">
	<ul class="list-inline d-flex justify-content-center mb-0">
		<?php if ( ! empty( $facebook_url ) ) : ?>
		<li class="list-inline-item">
			<a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer" 
			   class="btn btn-outline-primary btn-sm" title="<?php esc_attr_e( 'Síguenos en Facebook', 'bootstrap-theme' ); ?>">
				<i class="fab fa-facebook-f" aria-hidden="true"></i>
				<span class="visually-hidden"><?php esc_html_e( 'Facebook', 'bootstrap-theme' ); ?></span>
			</a>
		</li>
		<?php endif; ?>

		<?php if ( ! empty( $twitter_url ) ) : ?>
		<li class="list-inline-item">
			<a href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener noreferrer" 
			   class="btn btn-outline-info btn-sm" title="<?php esc_attr_e( 'Síguenos en Twitter/X', 'bootstrap-theme' ); ?>">
				<i class="fab fa-x-twitter" aria-hidden="true"></i>
				<span class="visually-hidden"><?php esc_html_e( 'Twitter/X', 'bootstrap-theme' ); ?></span>
			</a>
		</li>
		<?php endif; ?>

		<?php if ( ! empty( $instagram_url ) ) : ?>
		<li class="list-inline-item">
			<a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer" 
			   class="btn btn-outline-danger btn-sm" title="<?php esc_attr_e( 'Síguenos en Instagram', 'bootstrap-theme' ); ?>">
				<i class="fab fa-instagram" aria-hidden="true"></i>
				<span class="visually-hidden"><?php esc_html_e( 'Instagram', 'bootstrap-theme' ); ?></span>
			</a>
		</li>
		<?php endif; ?>

		<?php if ( ! empty( $linkedin_url ) ) : ?>
		<li class="list-inline-item">
			<a href="<?php echo esc_url( $linkedin_url ); ?>" target="_blank" rel="noopener noreferrer" 
			   class="btn btn-outline-primary btn-sm" title="<?php esc_attr_e( 'Síguenos en LinkedIn', 'bootstrap-theme' ); ?>">
				<i class="fab fa-linkedin-in" aria-hidden="true"></i>
				<span class="visually-hidden"><?php esc_html_e( 'LinkedIn', 'bootstrap-theme' ); ?></span>
			</a>
		</li>
		<?php endif; ?>

		<?php if ( ! empty( $youtube_url ) ) : ?>
		<li class="list-inline-item">
			<a href="<?php echo esc_url( $youtube_url ); ?>" target="_blank" rel="noopener noreferrer" 
			   class="btn btn-outline-danger btn-sm" title="<?php esc_attr_e( 'Síguenos en YouTube', 'bootstrap-theme' ); ?>">
				<i class="fab fa-youtube" aria-hidden="true"></i>
				<span class="visually-hidden"><?php esc_html_e( 'YouTube', 'bootstrap-theme' ); ?></span>
			</a>
		</li>
		<?php endif; ?>

		<?php if ( ! empty( $tiktok_url ) ) : ?>
		<li class="list-inline-item">
			<a href="<?php echo esc_url( $tiktok_url ); ?>" target="_blank" rel="noopener noreferrer" 
			   class="btn btn-outline-dark btn-sm" title="<?php esc_attr_e( 'Síguenos en TikTok', 'bootstrap-theme' ); ?>">
				<i class="fab fa-tiktok" aria-hidden="true"></i>
				<span class="visually-hidden"><?php esc_html_e( 'TikTok', 'bootstrap-theme' ); ?></span>
			</a>
		</li>
		<?php endif; ?>
		<?php if ( ! empty( $custom_links ) ) : ?>
			<?php foreach ( $custom_links as $item ) :
				$name = isset($item['name']) ? $item['name'] : '';
				$url  = isset($item['url']) ? $item['url'] : '';
				$icon = isset($item['icon']) ? $item['icon'] : 'fa-solid fa-link';
				if ( empty($url) ) { continue; }
			?>
			<li class="list-inline-item">
				<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"
				   class="btn btn-outline-secondary btn-sm" title="<?php echo esc_attr( $name ?: __('Síguenos', 'bootstrap-theme') ); ?>">
					<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
					<?php if ( ! empty( $name ) ) : ?>
						<span class="visually-hidden"><?php echo esc_html( $name ); ?></span>
					<?php endif; ?>
				</a>
			</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</div>
<?php endif; ?>
