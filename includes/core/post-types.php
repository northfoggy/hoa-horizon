<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register custom post type for announcements
function hoa_horizon_register_post_types() {
    register_post_type('hoa_announcement', array(
        'labels' => array(
            'name' => 'Announcements',
            'singular_name' => 'Announcement'
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => array('title', 'editor', 'author')
    ));
}
add_action('init', 'hoa_horizon_register_post_types');