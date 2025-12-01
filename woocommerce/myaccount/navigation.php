<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<?php do_action('woocommerce_before_account_navigation'); ?>

<nav class="woocommerce-MyAccount-navigation">
	<ul class="list-group list-group-flush mb-4">
		<?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
			<?php $classes = wc_get_account_menu_item_classes($endpoint); ?>
			<?php $is_active = strpos($classes, 'is-active') !== false ? ' active' : ''; ?>			
            <a class="list-group-item list-group-item-action<?php echo esc_attr($is_active); ?> <?php echo esc_attr($classes); ?>" href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>">
                <?php echo esc_html($label); ?>
            </a>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action('woocommerce_after_account_navigation'); ?>
