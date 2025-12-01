<?php
/**
 * WordPress Login Page Customizer
 * 
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize login page styles
 */
function bootstrap_theme_login_styles() {
    $logo_url = bootstrap_theme_get_responsive_logo('',true);    
    $primary_color = bootstrap_theme_get_customization_option('primary_color') ?: '#0d6efd';
    $dark_color = bootstrap_theme_get_customization_option('customization_dark_color') ?: '#212529';
    $light_color = bootstrap_theme_get_customization_option('customization_light_color') ?: '#f8f9fa';
    ?>
    
    <style>
        body.login {
            /*background: <?php echo esc_attr($dark_color); ?>;*/
            background: <?php echo esc_attr($light_color); ?>;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        #login {
            padding: 8% 0 0;
        }
        
        #login h1 a {
            background-image: url('<?php echo esc_url($logo_url); ?>');
            background-size: contain;
            background-position: center;
            width: 100%;
            height: 120px;
            margin-bottom: 25px;
        }
        
        .login form#loginform {
            background: <?php echo esc_attr($light_color); ?>;
            border: none;
            border-radius: 0.375rem;
            box-shadow: none;
            padding: 26px 24px;
            position: relative;
            overflow: inherit;
            min-height: 220px;
        }
        .login form#loginform:before{
            z-index: -1;
            position: absolute;
            content: "";
            bottom: 15px;
            left: 10px;
            width: 50%;
            top: 80%;
            max-width:300px;
            background: #777;
            box-shadow: 0 15px 10px #777;
            transform: rotate(-3deg);
        }
        
        .login form#loginform .input,
        .login input[type="text"],
        .login input[type="password"] {
            border: none;
            outline: none;
            border-radius: 15px;
            padding: 10px;
            font-size: 15px;
            background-color: #ccc;
            box-shadow: inset 2px 5px 10px rgba(0,0,0,0.3);
            transition: 300ms ease-in-out;
            color: <?php echo esc_attr($primary_color); ?>;
        }
        
        .login input[type="text"]:focus,
        .login input[type="password"]:focus {
            background-color: <?php echo esc_attr($light_color); ?>;
            transform: scale(1.05);
            box-shadow: 13px 13px 100px #969696,
                        -13px -13px 100px #ffffff;
        }
        
        .login label {
            color: #212529;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        .login #wp-submit{
            border: none;
            color: <?php echo esc_attr($dark_color); ?>;
            padding: 0.2em 1.7em;
            font-size: 12px;
            border-radius: 0.5em;
            background: #e8e8e8;
            cursor: pointer;
            border: 1px solid #e8e8e8;
            transition: all 0.3s;
            box-shadow: 6px 6px 12px #c5c5c5, -6px -6px 12px #ffffff;
        }
        .login #wp-submit:active,
        .login #wp-submit:hover{
            box-shadow: inset 4px 4px 12px #c5c5c5, inset -4px -4px 12px #ffffff;
        }
        
        .login #nav,
        .login #backtoblog {
            text-align: center;
            padding: 0;
            margin: 16px 0 0;
        }
        
        .login #nav a,
        .login #backtoblog a {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.15s ease-in-out;
        }
        
        .login #nav a:hover,
        .login #backtoblog a:hover {
            color: <?php echo esc_attr($primary_color); ?>;
        }
        
        .login .message,
        .login .success,
        .login #login_error {
            border-left: 4px solid <?php echo esc_attr($primary_color); ?>;
            background: #ffffff;
            border-radius: 0.375rem;
            padding: 12px;
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .login #login_error {
            border-left-color: #dc3545;
        }
        
        .login .privacy-policy-page-link {
            text-align: center;
            margin-top: 16px;
        }
        
        .login .privacy-policy-page-link a {
            color: #ffffff;
            font-size: 0.875rem;
        }
        
        .login .privacy-policy-page-link a:hover {
            color: <?php echo esc_attr($primary_color); ?>;
        }
        
        .login form .forgetmenot {
            float: none;
            margin-bottom: 16px;
        }
        
        .login form .forgetmenot label {
            font-size: 0.875rem;
            font-weight: 400;
        }
        
        .login form input[type="checkbox"] {
            margin-right: 8px;
            border-radius: 0.25rem;
        }
        
        .login form .submit {
            padding: 0;
            margin-top: 16px;
        }
        
        @media screen and (max-width: 782px) {
            #login {
                padding: 5% 0 0;
            }
            
            .login form {
                margin-left: 0;
                margin-right: 0;
                padding: 20px;
            }
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'bootstrap_theme_login_styles');

/**
 * Change login logo URL to home page
 */
function bootstrap_theme_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'bootstrap_theme_login_logo_url');

/**
 * Change login logo title to site name
 */
function bootstrap_theme_login_logo_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'bootstrap_theme_login_logo_title');
