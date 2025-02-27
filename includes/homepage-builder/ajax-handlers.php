<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// AJAX handlers for updating block settings
function hoa_horizon_update_welcome_block() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hoa_horizon_nonce')) {
        wp_send_json_error('Invalid security token');
    }
    
    // Get and sanitize values
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    
    // Update options
    update_option('hoa_horizon_welcome_title', $title);
    update_option('hoa_horizon_welcome_message', $content);
    
    wp_send_json_success(array(
        'title' => $title,
        'content' => wpautop(wp_trim_words($content, 10, '...')),
        'message' => 'Welcome block updated successfully'
    ));
}
add_action('wp_ajax_hoa_horizon_update_welcome_block', 'hoa_horizon_update_welcome_block');

function hoa_horizon_save_homepage() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hoa_horizon_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    $data = array(
        'welcome_title' => isset($_POST['welcome_title']) ? sanitize_text_field($_POST['welcome_title']) : '',
        'welcome_message' => isset($_POST['welcome_message']) ? wp_kses_post($_POST['welcome_message']) : '',
        'block_order' => isset($_POST['block_order']) ? sanitize_text_field($_POST['block_order']) : '',
        'items_per_row' => isset($_POST['items_per_row']) ? absint($_POST['items_per_row']) : 3,
        'theme' => isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : 'light',
        'header_image' => isset($_POST['header_image']) ? absint($_POST['header_image']) : 0,
        'header_image_height' => isset($_POST['header_image_height']) ? absint($_POST['header_image_height']) : 200,
        'header_image_width' => isset($_POST['header_image_width']) ? absint($_POST['header_image_width']) : 1200
    );
    
    // Update options
    update_option('hoa_horizon_welcome_title', $data['welcome_title']);
    update_option('hoa_horizon_welcome_message', $data['welcome_message']);
    update_option('hoa_horizon_block_order', $data['block_order']);
    update_option('hoa_horizon_items_per_row', $data['items_per_row']);
    update_option('hoa_horizon_theme', $data['theme']);
    update_option('hoa_horizon_header_image', $data['header_image']);
    update_option('hoa_horizon_header_image_height', $data['header_image_height']);
    update_option('hoa_horizon_header_image_width', $data['header_image_width']);
    
    wp_send_json_success(array('message' => 'Homepage saved successfully'));
}
add_action('wp_ajax_hoa_horizon_save_homepage', 'hoa_horizon_save_homepage');

function hoa_horizon_get_homepage_data() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hoa_horizon_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    $data = array(
        'welcome_title' => get_option('hoa_horizon_welcome_title', 'Welcome to our Community'),
        'welcome_message' => get_option('hoa_horizon_welcome_message', 'Welcome to our community! We are glad you are here. Stay tuned for updates and events.'),
        'block_order' => get_option('hoa_horizon_block_order', 'welcome,whats_new,upcoming_events'),
        'items_per_row' => get_option('hoa_horizon_items_per_row', 3),
        'theme' => get_option('hoa_horizon_theme', 'light'),
        'header_image' => get_option('hoa_horizon_header_image', 0),
        'header_image_height' => get_option('hoa_horizon_header_image_height', 200),
        'header_image_width' => get_option('hoa_horizon_header_image_width', 1200)
    );
    
    wp_send_json_success($data);
}
add_action('wp_ajax_hoa_horizon_get_homepage_data', 'hoa_horizon_get_homepage_data');