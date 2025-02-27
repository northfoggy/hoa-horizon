<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Helper functions for block icons and previews
function hoa_horizon_get_block_icon($block_key) {
    switch ($block_key) {
        case 'welcome':
            return '<span class="dashicons dashicons-format-aside"></span>';
        case 'weather':
            return '<span class="dashicons dashicons-cloud"></span>';
        case 'whats_new':
            return '<span class="dashicons dashicons-megaphone"></span>';
        case 'upcoming_events':
            return '<span class="dashicons dashicons-calendar-alt"></span>';
        default:
            return '<span class="dashicons dashicons-admin-page"></span>';
    }
}

function hoa_horizon_get_block_preview($block_key) {
    switch ($block_key) {
        case 'welcome':
            $welcome_title = get_option('hoa_horizon_welcome_title', 'Welcome');
            $message = get_option('hoa_horizon_welcome_message', 'Welcome message preview...');
            return '<h3>' . esc_html($welcome_title) . '</h3><p>' . wp_trim_words($message, 10, '...') . '</p>';
        case 'weather':
            return '<h3>Weather</h3><p>☀️ 72°F - Sunny</p><p>Today\'s Forecast preview</p>';
        case 'whats_new':
            return '<h3>What\'s New</h3><p>📢 Latest Community Updates</p>';
        case 'upcoming_events':
            return '<h3>Upcoming Events</h3><p>📅 Community Calendar</p>';
        default:
            return '<h3>Content Block</h3><p>Block content preview</p>';
    }
}