<?php
// Panel de administración para suscriptores del newsletter
add_action('acf/input/admin_footer', 'bootstrap_theme_newsletter_admin_panel');

function bootstrap_theme_newsletter_admin_panel() {
    // Solo mostrar en la página de opciones del tema
    if (!isset($_GET['page']) || $_GET['page'] !== 'theme-general-settings') return;
    global $wpdb;
    $table = $wpdb->prefix . 'bootstrap_newsletter_subscribers';
    $subscribers = $wpdb->get_results("SELECT * FROM $table ORDER BY subscribed_at DESC");
    ?>
    <div class="acf-field acf-field-tab" style="margin-top:2em;">
        <h2>Suscriptores del Newsletter</h2>
        <form method="post" style="margin-bottom:1em;">
            <button type="submit" name="newsletter_download_csv" class="button button-primary">Descargar CSV</button>
        </form>
        <table class="widefat">
            <thead>
                <tr><th>Nombre</th><th>Email</th><th>Fecha de suscripción</th></tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $s): ?>
                <tr>
                    <td><?php echo esc_html($s->name); ?></td>
                    <td><?php echo esc_html($s->email); ?></td>
                    <td><?php echo esc_html($s->subscribed_at); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    // Descargar CSV
    if (isset($_POST['newsletter_download_csv'])) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="newsletter-subscribers.csv"');
        echo "Nombre,Email,Fecha\n";
        foreach ($subscribers as $s) {
            echo '"' . $s->name . '","' . $s->email . '","' . $s->subscribed_at . "\n";
        }
        exit;
    }
}
