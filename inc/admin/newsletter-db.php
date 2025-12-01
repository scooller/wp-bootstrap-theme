<?php
// Crear tabla personalizada para suscriptores del newsletter
function bootstrap_theme_create_newsletter_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'bootstrap_newsletter_subscribers';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        name varchar(255) DEFAULT '',
        subscribed_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'bootstrap_theme_create_newsletter_table');
