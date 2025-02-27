<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Display homepage blocks on WordPress homepage
function hoa_horizon_display_homepage() {
    if (is_front_page() && get_option('hoa_horizon_active') === 'yes') {
        add_filter('the_content', 'hoa_horizon_replace_homepage_content');
    }
}
add_action('template_redirect', 'hoa_horizon_display_homepage', 100);

// Replace homepage content with modules
function hoa_horizon_replace_homepage_content($content) {
    // Only modify the main content on the homepage
    if (is_front_page() && is_main_query()) {
        return hoa_horizon_modules_shortcode();
    }
    return $content;
}

// Front-end modules shortcode
function hoa_horizon_modules_shortcode() {
    $selected_blocks = get_option('hoa_horizon_homepage_blocks', array('welcome', 'whats_new', 'upcoming_events')); // Force default blocks
    $block_order_raw = get_option('hoa_horizon_block_order', 'welcome,whats_new,upcoming_events'); // Force default order
    $welcome_title = get_option('hoa_horizon_welcome_title', 'Welcome to our Community');
    $welcome_message = get_option('hoa_horizon_welcome_message', 'Welcome to our community! We are glad you are here. Stay tuned for updates and events.');
    $header_image_id = get_option('hoa_horizon_header_image', 0);
    $header_image_height = get_option('hoa_horizon_header_image_height', 200); // Default 200px
    $header_image_width = get_option('hoa_horizon_header_image_width', 1200); // Default 1200px
    $items_per_row = get_option('hoa_horizon_items_per_row', 3); // Default 3 items per row
    $theme = get_option('hoa_horizon_theme', 'light');

    // Define header_image_url
    $header_image_url = $header_image_id ? wp_get_attachment_url($header_image_id) : '';
    
    // Ensure block_order is valid
    $block_order = explode(',', $block_order_raw);
    if (empty(array_filter($block_order))) { // If block_order is empty or contains only empty strings
        $block_order = explode(',', 'welcome,whats_new,upcoming_events'); // Force default order
    }

    $output = '<div class="hoa-horizon-homepage theme-' . esc_attr($theme) . '" data-items-per-row="' . esc_attr($items_per_row) . '">';
    
    // Header Image
    if ($header_image_url) {
        $output .= '<header class="hoa-horizon-header" style="background-image: url(' . esc_url($header_image_url) . '); height: ' . esc_attr($header_image_height) . 'px; max-width: ' . esc_attr($header_image_width) . 'px;"></header>';
    }

    // Main content container
    $output .= '<div class="hoa-blocks-container">';
    
    // Ordered Blocks
    $ordered_blocks = array_intersect($block_order, $selected_blocks);
    foreach ($ordered_blocks as $block_key) {
        switch ($block_key) {
            case 'welcome':
                $output .= '<section class="hoa-module hoa-welcome">';
                $output .= '<h2>' . esc_html($welcome_title) . '</h2>';
                $output .= '<div class="hoa-welcome-content">' . wpautop($welcome_message) . '</div>';
                $output .= '</section>';
                break;
            case 'weather':
                $output .= '<section class="hoa-module hoa-weather">';
                $output .= '<h2>Today\'s Weather</h2>';
                $output .= '<div class="weather-widget">';
                $output .= '<div class="weather-main">';
                $output .= '<span class="weather-icon">☀️</span>';
                $output .= '<span class="weather-temp">72°F</span>';
                $output .= '</div>';
                $output .= '<div class="weather-conditions">Sunny</div>';
                $output .= '<div class="weather-forecast">Forecast: Clear skies throughout the day</div>';
                $output .= '</div>';
                $output .= '</section>';
                break;
            case 'whats_new':
                $output .= '<section class="hoa-module hoa-whats-new">';
                $output .= '<h2>What\'s New</h2>';
                
                // Get recent announcements
                $args = array(
                    'post_type' => 'hoa_announcement',
                    'posts_per_page' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                $recent_posts = get_posts($args);
                
                if ($recent_posts) {
                    $output .= '<ul class="announcements-list">';
                    foreach ($recent_posts as $post) {
                        $date = date_i18n(get_option('date_format'), strtotime($post->post_date));
                        $output .= '<li>';
                        $output .= '<div class="announcement-date">' . esc_html($date) . '</div>';
                        $output .= '<h3 class="announcement-title">' . esc_html($post->post_title) . '</h3>';
                        $output .= '<div class="announcement-excerpt">' . wp_trim_words($post->post_content, 20, '... <a href="' . get_permalink($post->ID) . '">Read More</a>') . '</div>';
                        $output .= '</li>';
                    }
                    $output .= '</ul>';
                } else {
                    $output .= '<p>No announcements yet. Check back soon!</p>';
                }
                
                $output .= '</section>';
                break;
            case 'upcoming_events':
                $output .= '<section class="hoa-module hoa-upcoming-events">';
                $output .= '<h2>Upcoming Events</h2>';
                $output .= '<div class="events-list">';
                // This is a placeholder. In the real implementation, you would fetch actual events
                $output .= '<div class="event-item">';
                $output .= '<div class="event-date">March 15, 2025</div>';
                $output .= '<h3 class="event-title">Community Pool Opening</h3>';
                $output .= '<div class="event-details">Join us for the seasonal opening of the community pool with refreshments and activities for all ages.</div>';
                $output .= '</div>';
                
                $output .= '<div class="event-item">';
                $output .= '<div class="event-date">April 2, 2025</div>';
                $output .= '<h3 class="event-title">Spring Neighborhood Cleanup</h3>';
                $output .= '<div class="event-details">Volunteer to help clean up the common areas and prepare for spring planting.</div>';
                $output .= '</div>';
                
                $output .= '<div class="event-item">';
                $output .= '<div class="event-date">April 22, 2025</div>';
                $output .= '<h3 class="event-title">Earth Day Celebration</h3>';
                $output .= '<div class="event-details">Environmental awareness activities and tree planting ceremony in the community park.</div>';
                $output .= '</div>';
                
                $output .= '</div>';
                $output .= '</section>';
                break;
        }
    }
    
    $output .= '</div>'; // End blocks container
    $output .= '</div>'; // End homepage container

    // Add theme styles
    $output .= '<style>
        .hoa-horizon-homepage {
            max-width: 1200px;
            margin: 20px auto;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        
        .hoa-horizon-header {
            width: 100%;
            margin: 0 auto 20px;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
        }
        
        .hoa-blocks-container {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(1, 1fr);
        }
        
        .hoa-horizon-homepage[data-items-per-row="1"] .hoa-blocks-container {
            grid-template-columns: 1fr;
        }
        
        .hoa-horizon-homepage[data-items-per-row="2"] .hoa-blocks-container {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .hoa-horizon-homepage[data-items-per-row="3"] .hoa-blocks-container {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .hoa-horizon-homepage[data-items-per-row="4"] .hoa-blocks-container {
            grid-template-columns: repeat(4, 1fr);
        }
        
        @media (max-width: 768px) {
            .hoa-blocks-container {
                grid-template-columns: 1fr !important;
            }
        }
        
        /* Theme: Light */
        .theme-light .hoa-module {
            background: #ffffff;
            color: #333333;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .theme-light .hoa-module h2 {
            color: #2c3e50;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-top: 0;
        }
        
        /* Theme: Dark */
        .theme-dark .hoa-module {
            background: #2c3e50;
            color: #ecf0f1;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            padding: 20px;
            border: 1px solid #34495e;
        }
        
        .theme-dark .hoa-module h2 {
            color: #3498db;
            border-bottom: 2px solid #34495e;
            padding-bottom: 10px;
            margin-top: 0;
        }
        
        /* Weather Module */
        .weather-widget {
            text-align: center;
        }
        
        .weather-main {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .weather-icon {
            font-size: 3em;
            margin-right: 15px;
        }
        
        .weather-temp {
            font-size: 2.5em;
            font-weight: bold;
        }
        
        .weather-conditions {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        
        /* Announcements Module */
        .announcements-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .announcements-list li {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .theme-dark .announcements-list li {
            border-bottom-color: #34495e;
        }
        
        .announcements-list li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .announcement-date {
            font-size: 0.85em;
            color: #7f8c8d;
        }
        
        .theme-dark .announcement-date {
            color: #bdc3c7;
        }
        
        .announcement-title {
            margin: 5px 0;
            font-size: 1.2em;
        }
        
        .announcement-excerpt {
            font-size: 0.95em;
        }
        
        /* Events Module */
        .events-list .event-item {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .theme-dark .events-list .event-item {
            border-bottom-color: #34495e;
        }
        
        .events-list .event-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .event-date {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .theme-dark .event-date {
            color: #e74c3c;
        }
        
        .event-title {
            margin: 5px 0;
            font-size: 1.2em;
        }
        
        .event-details {
            font-size: 0.95em;
            margin-top: 5px;
        }
    </style>';

    return $output;
}
add_shortcode('hoa_horizon_modules', 'hoa_horizon_modules_shortcode');