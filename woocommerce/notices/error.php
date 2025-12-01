<?php
if (!defined('ABSPATH')) {
	exit;
}

if (empty($notices)) {
	return;
}
?>
<?php foreach ($notices as $notice) : ?>
	<div class="alert alert-danger woocommerce-error mb-3" <?php echo wc_get_notice_data_attr($notice); ?> role="alert">
		<?php echo wc_kses_notice($notice['notice']); ?>
	</div>
<?php endforeach; ?>
