<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Rewrite rule function
function hoa_horizon_add_rewrite_rule() {
    add_rewrite_rule(
        '^hoa-horizon-admin/?$',
        'index.php?hoa_horizon_admin=1',
        'top'
    );
    
    // Add a fallback rule to catch old hoa-harmony-admin URLs and redirect
    add_rewrite_rule(
        '^hoa-harmony-admin/?$',
        'index.php?hoa_horizon_redirect=1',
        'top'
    );
    
    // Verify and force registration if needed
    global $wp_rewrite;
    $rules = $wp_rewrite->wp_rewrite_rules();
    if (!isset($rules['^hoa-horizon-admin/?$'])) {
        $wp_rewrite->add_rule('^hoa-horizon-admin/?$', 'index.php?hoa_horizon_admin=1', 'top');
        $wp_rewrite->flush_rules(true);
    }
}
add_action('init', 'hoa_horizon_add_rewrite_rule', 10, 0);

// Handle redirect for old URLs
function hoa_horizon_handle_redirect() {
    if (get_query_var('hoa_horizon_redirect')) {
        wp_safe_redirect(home_url('/hoa-horizon-admin'), 301); // Use 301 for permanent redirect
        exit;
    }
}
add_action('template_redirect', 'hoa_horizon_handle_redirect', 10, 0);

// Register query var
function hoa_horizon_query_vars($vars) {
    $vars[] = 'hoa_horizon_admin';
    $vars[] = 'hoa_horizon_redirect'; // Add redirect query var
    return $vars;
}
add_filter('query_vars', 'hoa_horizon_query_vars', 10, 1);

// Flush rewrite rules on plugin activation
function hoa_horizon_flush_rewrite_rules() {
    hoa_horizon_add_rewrite_rule();
    flush_rewrite_rules(true); // Force flush to update .htaccess
}
register_activation_hook(__FILE__, 'hoa_horizon_flush_rewrite_rules');

// Also flush rewrite rules on init to ensure updates
function hoa_horizon_init_flush() {
    if (get_option('hoa_horizon_flush_needed')) {
        flush_rewrite_rules(true); // Force flush to update .htaccess
        delete_option('hoa_horizon_flush_needed');
    }
}
add_action('init', 'hoa_horizon_init_flush', 99, 0);