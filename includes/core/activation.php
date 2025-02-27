<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Activation hook
function hoa_horizon_activate() {
    error_reporting(E_ALL & ~E_DEPRECATED); // Suppress deprecated warnings temporarily
    update_option('hoa_horizon_active', 'yes');
    if (!get_role('hoa_administrator')) {
        add_role('hoa_administrator', 'HOA Administrator', array('read' => true, 'manage_options' => true));
    }
    if (!get_role('board_member')) {
        add_role('board_member', 'Board Member', array('read' => true, 'edit_posts' => true));
    }
    if (!get_role('committee_member')) {
        add_role('committee_member', 'Committee Member', array('read' => true, 'edit_posts' => true));
    }
    
    // Flush rewrite rules to ensure new rules are registered
    hoa_horizon_add_rewrite_rule();
    flush_rewrite_rules();
    
    // Mark that a flush is needed on next init to ensure persistence
    update_option('hoa_horizon_flush_needed', true);
    
    // Check for old HOA Harmony options and migrate if needed
    $old_active = get_option('hoa_harmony_active');
    if ($old_active) {
        // Migrate old options to new names
        $old_site_name = get_option('hoa_harmony_site_name');
        if ($old_site_name) update_option('hoa_horizon_site_name', $old_site_name);
        $old_announcement_visibility = get_option('hoa_harmony_announcement_visibility');
        if ($old_announcement_visibility) update_option('hoa_horizon_announcement_visibility', $old_announcement_visibility);
        $old_frontend_roles = get_option('hoa_harmony_frontend_roles');
        if ($old_frontend_roles) update_option('hoa_horizon_frontend_roles', $old_frontend_roles);
        $old_frontend_users = get_option('hoa_harmony_frontend_users');
        if ($old_frontend_users) update_option('hoa_horizon_frontend_users', $old_frontend_users);
        $old_login_bg_color = get_option('hoa_harmony_login_bg_color');
        if ($old_login_bg_color) update_option('hoa_horizon_login_bg_color', $old_login_bg_color);
        $old_login_text_color = get_option('hoa_harmony_login_text_color');
        if ($old_login_text_color) update_option('hoa_horizon_login_text_color', $old_login_text_color);
        $old_login_button_color = get_option('hoa_harmony_login_button_color');
        if ($old_login_button_color) update_option('hoa_horizon_login_button_color', $old_login_button_color);
        $old_modules = get_option('hoa_harmony_modules');
        if ($old_modules) update_option('hoa_horizon_modules', $old_modules);
        
        // Clean up old options and rewrite rules
        delete_option('hoa_harmony_active');
        delete_option('hoa_harmony_site_name');
        delete_option('hoa_harmony_announcement_visibility');
        delete_option('hoa_harmony_frontend_roles');
        delete_option('hoa_harmony_frontend_users');
        delete_option('hoa_harmony_login_bg_color');
        delete_option('hoa_harmony_login_text_color');
        delete_option('hoa_harmony_login_button_color');
        delete_option('hoa_harmony_modules');
        delete_option('hoa_harmony_notice_shown');
        
        // Remove old rewrite rules (if possible) and force flush
        global $wp_rewrite;
        if (isset($wp_rewrite->rules['hoa-harmony-admin/?$'])) {
            unset($wp_rewrite->rules['hoa-harmony-admin/?$']);
        }
        if (isset($wp_rewrite->rules['^hoa-harmony-admin/?$'])) {
            unset($wp_rewrite->rules['^hoa-harmony-admin/?$']);
        }
        $wp_rewrite->flush_rules(true); // Force flush to update .htaccess
    }
    
    $login_page = get_page_by_path('hoa-login');
    if (!$login_page) {
        $page_id = wp_insert_post(array(
            'post_title' => 'Login',
            'post_name' => 'hoa-login',
            'post_content' => '[hoa_horizon_login]',
            'post_status' => 'publish',
            'post_type' => 'page',
        ));
        if ($page_id && !is_wp_error($page_id)) {
            update_option('hoa_horizon_login_page_id', $page_id);
        }
    }
    
    // Create and set the homepage
    $homepage = get_page_by_path('home');
    if (!$homepage) {
        $homepage_id = wp_insert_post(array(
            'post_title' => 'Home',
            'post_name' => 'home',
            'post_content' => '[hoa_horizon_modules]',
            'post_status' => 'publish',
            'post_type' => 'page',
        ));
        if ($homepage_id && !is_wp_error($homepage_id)) {
            // Set this page as the static homepage
            update_option('show_on_front', 'page');
            update_option('page_on_front', $homepage_id);
        }
    }
    
    // Set up default options for the homepage builder
    if (!get_option('hoa_horizon_theme')) {
        update_option('hoa_horizon_theme', 'light');
    }
    
    if (!get_option('hoa_horizon_welcome_title')) {
        update_option('hoa_horizon_welcome_title', 'Welcome to our Community');
    }
    
    // Set default homepage blocks if not already set
    if (!get_option('hoa_horizon_homepage_blocks')) {
        update_option('hoa_horizon_homepage_blocks', array('welcome', 'whats_new', 'upcoming_events'));
    }
    
    // Set default block order if not already set
    if (!get_option('hoa_horizon_block_order')) {
        update_option('hoa_horizon_block_order', 'welcome,whats_new,upcoming_events');
    }
}
register_activation_hook(__FILE__, 'hoa_horizon_activate');

// Deactivation hook
function hoa_horizon_deactivate() {
    delete_option('hoa_horizon_active');
    remove_role('hoa_administrator');
    remove_role('board_member');
    remove_role('committee_member');
    remove_role('hoa_admin');
    remove_role('hoa_member');
    flush_rewrite_rules();
    
    $login_page_id = get_option('hoa_horizon_login_page_id');
    if ($login_page_id) {
        wp_delete_post($login_page_id, true);
        delete_option('hoa_horizon_login_page_id');
    }
    
    // Optionally remove the homepage on deactivation
    $homepage_id = get_option('page_on_front');
    if ($homepage_id) {
        wp_delete_post($homepage_id, true);
        delete_option('show_on_front');
        delete_option('page_on_front');
    }
}
register_deactivation_hook(__FILE__, 'hoa_horizon_deactivate');