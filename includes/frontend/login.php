<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Prevent public registration
function hoa_horizon_remove_registration() {
    if (get_option('users_can_register')) {
        update_option('users_can_register', 0);
    }
    remove_action('register', 'register_new_user');
    remove_action('register_form', 'wp_register_form');
    remove_action('login_head', 'wp_shake_js', 12);
    add_filter('login_form_bottom', 'hoa_horizon_remove_register_link');
}
add_action('init', 'hoa_horizon_remove_registration');

function hoa_horizon_remove_register_link() {
    return '';
}

// Custom login shortcode
function hoa_horizon_login_shortcode() {
    if (is_user_logged_in()) {
        return '<p>You are already logged in. <a href="' . esc_url(home_url('/hoa-horizon-admin')) . '">Go to Admin Dashboard</a></p>';
    }

    $output = '<div class="hoa-horizon-login">';
    $output .= '<h2>Login to HOA Horizon</h2>';

    if (isset($_POST['hoa_horizon_login_submit']) && check_admin_referer('hoa_horizon_login_action', 'hoa_horizon_login_nonce')) {
        $credentials = array(
            'user_login' => sanitize_user($_POST['user_login']),
            'user_password' => $_POST['user_password'],
            'remember' => isset($_POST['rememberme']),
        );
        $user = wp_signon($credentials, false);
        if (is_wp_error($user)) {
            $output .= '<p class="error">' . esc_html($user->get_error_message()) . '</p>';
        } else {
            wp_redirect(home_url('/hoa-horizon-admin'));
            exit;
        }
    }

    $output .= '<form method="post" action="">';
    $output .= wp_nonce_field('hoa_horizon_login_action', 'hoa_horizon_login_nonce', true, false);
    $output .= '<div class="form-group">';
    $output .= '<label for="user_login">Username</label>';
    $output .= '<input type="text" name="user_login" id="user_login" required>';
    $output .= '</div>';
    $output .= '<div class="form-group">';
    $output .= '<label for="user_password">Password</label>';
    $output .= '<input type="password" name="user_password" id="user_password" required>';
    $output .= '</div>';
    $output .= '<div class="form-group">';
    $output .= '<label for="rememberme"><input type="checkbox" name="rememberme" id="rememberme"> Remember Me</label>';
    $output .= '</div>';
    $output .= '<input type="submit" name="hoa_horizon_login_submit" value="Log In" class="button">';
    $output .= '</form>';
    $output .= '<p>Forgot password? <a href="' . esc_url(wp_lostpassword_url()) . '">Reset it here</a>.</p>';
    $output .= '</div>';

    $bg_color = get_option('hoa_horizon_login_bg_color', '#fff');
    $text_color = get_option('hoa_horizon_login_text_color', '#000');
    $button_color = get_option('hoa_horizon_login_button_color', '#0073aa');

    $output .= '<style>
        .hoa-horizon-login {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background: ' . esc_attr($bg_color) . ';
            border: 1px solid #ddd;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            color: ' . esc_attr($text_color) . ';
        }
        .hoa-horizon-login h2 {
            margin-top: 0;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .button {
            background: ' . esc_attr($button_color) . ';
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            width: 100%;
        }
        .button:hover {
            background: ' . esc_attr(hoa_horizon_darken_color($button_color, 20)) . ';
        }
        .error {
            color: #721c24;
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>';

    return $output;
}
add_shortcode('hoa_horizon_login', 'hoa_horizon_login_shortcode');

// Helper function to darken a color
function hoa_horizon_darken_color($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $r = max(0, min(255, $r - ($r * $percent / 100)));
    $g = max(0, min(255, $g - ($g * $percent / 100)));
    $b = max(0, min(255, $b - ($b * $percent / 100)));
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}