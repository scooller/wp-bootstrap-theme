<?php
/**
 * Floating Color Scheme Switcher (Light / Dark / Auto)
 *
 * Renders a small floating widget on the left side that lets users pick
 * their preferred color scheme. Preference is stored in localStorage
 * and overrides the global theme setting from ACF for that visitor.
 *
 * Uses Bootstrap 5.3 data-bs-theme values: 'light' | 'dark' | 'auto'
 *
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Output the floating switcher HTML
 */
function bootstrap_theme_render_color_scheme_switcher() {
    // Permite mostrar/ocultar el widget según la opción ACF
    $show = apply_filters( 'bootstrap_theme_show_color_scheme_switcher', true );
    $acf_show = function_exists('get_field') ? get_field('customization_show_color_scheme_widget', 'option') : true;
    if ( !$show || !$acf_show ) {
        return;
    }

    // Markup: a vertical pill with three buttons. Minimal inline style; prefers Bootstrap utilities.
    ?>
    <!-- Desktop switcher (md and up) -->
    <div id="bs-color-scheme-switcher" class="position-fixed start-0 top-50 translate-middle-y d-none d-md-flex flex-column gap-1 bg-body border rounded-end shadow p-2 z-3"
         style="--_offset: 8px; left: var(--_offset);">
    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-theme-value="light" aria-label="Tema claro" title="<?php esc_attr_e('Claro','bootstrap-theme') ?>"><svg class="icon" width="20" height="20"><use xlink:href="#fa-sun"></use></svg></button>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-theme-value="dark" aria-label="Tema oscuro" title="<?php esc_attr_e('Oscuro','bootstrap-theme') ?>"><svg class="icon" width="20" height="20"><use xlink:href="#fa-moon"></use></svg></button>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-theme-value="auto" aria-label="Tema automático" title="<?php esc_attr_e('Automático','bootstrap-theme') ?>"><svg class="icon" width="20" height="20"><use xlink:href="#fa-cloud-sun"></use></svg></button>
    </div>

    <!-- Mobile switcher (below md) -->
    <div id="bs-color-scheme-switcher-mobile" class="position-fixed bottom-0 start-0 m-3 d-flex d-md-none z-3">
        <div class="dropdown">
            <button class="btn btn-secondary btn-sm dropdown-toggle shadow" type="button" id="bsColorSchemeMenu" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="me-1"><?php esc_attr_e('Tema','bootstrap-theme'); ?></span>
                <span id="bsColorSchemeIconSvg">
                    <svg class="icon" width="20" height="20"><use xlink:href="#fa-cloud-sun"></use></svg>
                </span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="bsColorSchemeMenu" style="--bs-dropdown-min-width: auto;">
                <li><button class="dropdown-item" type="button" data-bs-theme-value="light"><svg class="icon" width="20" height="20"><use xlink:href="#fa-sun"></use></svg></button></li>
                <li><button class="dropdown-item" type="button" data-bs-theme-value="dark"><svg class="icon" width="20" height="20"><use xlink:href="#fa-moon"></use></svg></button></li>
                <li><button class="dropdown-item" type="button" data-bs-theme-value="auto"><svg class="icon" width="20" height="20"><use xlink:href="#fa-cloud-sun"></use></svg></button></li>
            </ul>
        </div>
    </div>

    <script>
    (function(){
    const STORAGE_KEY = 'bootstrap-theme-color-scheme';
    const htmlEl = document.documentElement;
    const desk = document.getElementById('bs-color-scheme-switcher');
    const mobile = document.getElementById('bs-color-scheme-switcher-mobile');
    const mobileIconSvg = document.getElementById('bsColorSchemeIconSvg');
    if (!desk && !mobile) return;

        // Apply the theme to <html data-bs-theme="...">
        function applyTheme(mode) {
            if (mode === 'auto') {
                htmlEl.setAttribute('data-bs-theme', 'auto');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                htmlEl.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
            } else {
                htmlEl.setAttribute('data-bs-theme', mode);
            }

            // Update active visual state across both UIs
            function setActiveIn(container) {
                if (!container) return;
                const buttons = container.querySelectorAll('[data-bs-theme-value]');
                buttons.forEach(el => {
                    const isActive = el.getAttribute('data-bs-theme-value') === mode;
                    // Desktop buttons styling
                    if (el.classList.contains('btn')) {
                        el.classList.toggle('btn-primary', isActive);
                        el.classList.toggle('text-white', isActive);
                        el.classList.toggle('btn-outline-secondary', !isActive);
                    }
                    // Dropdown items styling
                    if (el.classList.contains('dropdown-item')) {
                        el.classList.toggle('active', isActive);
                    }
                });
            }
            setActiveIn(desk);
            setActiveIn(mobile);

            // Cambia el icono SVG en mobile según el tema seleccionado
            if (mobileIconSvg) {
                let iconId = '#fa-cloud-sun';
                if (mode === 'light') iconId = '#fa-sun';
                else if (mode === 'dark') iconId = '#fa-moon';
                mobileIconSvg.innerHTML = `<svg class="icon" width="20" height="20"><use xlink:href="${iconId}"></use></svg>`;
            }
        }

        // Initialize from saved preference or from current attribute
        const saved = localStorage.getItem(STORAGE_KEY);
        const current = saved || (htmlEl.getAttribute('data-bs-theme') || 'auto');
        applyTheme(current);

        // React to system changes when in auto
        const mql = window.matchMedia('(prefers-color-scheme: dark)');
        function onSystemChange(e){
            const mode = localStorage.getItem(STORAGE_KEY) || 'auto';
            if (mode === 'auto') {
                htmlEl.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
            }
        }
        if (mql && mql.addEventListener) mql.addEventListener('change', onSystemChange);

        // Handle clicks
        function handleClick(container){
            if (!container) return;
            container.addEventListener('click', function(e){
                const el = e.target.closest('[data-bs-theme-value]');
                if (!el) return;
                const value = el.getAttribute('data-bs-theme-value');
                localStorage.setItem(STORAGE_KEY, value);
                applyTheme(value);
            });
        }
        handleClick(desk);
        handleClick(mobile);
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'bootstrap_theme_render_color_scheme_switcher', 9 );
