<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
function hoa_horizon_admin_menu() {
    add_menu_page(
        'HOA Horizon Settings',
        'HOA Horizon',
        'manage_options',
        'hoa-horizon-settings',
        'hoa_horizon_settings_page',
        'dashicons-admin-home',
        20
    );
}
add_action('admin_menu', 'hoa_horizon_admin_menu');