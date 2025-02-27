<?php
/*
 * Plugin Name: HOA Horizon
 * Description: A custom plugin for managing HOA features with a user-friendly homepage builder.
 * Version: 1.0.0
 * Author: Jason Mitchell
 * Text Domain: hoa-horizon
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/core/post-types.php';
require_once plugin_dir_path(__FILE__) . 'includes/core/activation.php';
require_once plugin_dir_path(__FILE__) . 'includes/core/migration.php';
require_once plugin_dir_path(__FILE__) . 'includes/core/rewrite.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/settings-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/settings-callbacks.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend/login.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend/admin-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/homepage-builder/builder-ui.php';
require_once plugin_dir_path(__FILE__) . 'includes/homepage-builder/block-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/homepage-builder/ajax-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/homepage-builder/frontend-display.php';

// Enqueue scripts and styles
function hoa_horizon_enqueue_scripts() {
    wp_enqueue_style('hoa-horizon-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css', array(), '1.0.0');
    wp_enqueue_script('hoa-horizon-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery', 'jquery-ui-sortable'), '1.1.0', true);
}
add_action('admin_enqueue_scripts', 'hoa_horizon_enqueue_scripts');
add_action('wp_enqueue_scripts', 'hoa_horizon_enqueue_scripts', 99);