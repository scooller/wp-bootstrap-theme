<?php
if (!defined('ABSPATH')) {
	exit;
}

$current_user = wp_get_current_user();
?>
<div class="card mb-4">
	<div class="card-body">
		<div class="alert alert-secondary mb-0" role="alert">
			<p class="mb-0">
				<?php
				printf(
					/* translators: 1: user display name 2: orders url 3: addresses url 4: account details url */
					wp_kses_post(
						__(
							'Hola %1$s (no eres %1$s? <a href="%5$s">Cerrar sesión</a>). Desde tu panel puedes ver tus <a href="%2$s">pedidos recientes</a>, administrar tus <a href="%3$s">direcciones</a> y editar los <a href="%4$s">detalles de tu cuenta</a>.',
							'bootstrap-theme'
						)
					),
					'<strong>' . esc_html($current_user->display_name) . '</strong>',
					esc_url(wc_get_endpoint_url('orders')),
					esc_url(wc_get_endpoint_url('edit-address')),
					esc_url(wc_get_endpoint_url('edit-account')),
					esc_url(wp_logout_url())
				);
				?>
			</p>
		</div>
	</div>
</div>

<?php do_action('woocommerce_account_dashboard'); ?>
<?php do_action('woocommerce_before_my_account'); ?>
<?php do_action('woocommerce_after_my_account'); ?>
