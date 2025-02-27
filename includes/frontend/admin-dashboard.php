<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle the admin page output
function hoa_horizon_admin_page() {
    $query_var = get_query_var('hoa_horizon_admin');
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Only process if the URL matches exactly /hoa-horizon-admin/ or /hoa-horizon-admin
    if (strpos($request_uri, '/hoa-horizon-admin') === false || strpos($request_uri, 'favicon.ico') !== false || strpos($request_uri, 'robots.txt') !== false) {
        return; // Exit silently for non-admin or favicon/robots requests
    }
    
    if (!$query_var) {
        // Debug: Log if query var is missing with detailed context
        error_log('HOA Horizon: hoa_horizon_admin query var not found for URL: ' . $request_uri . '. Check rewrite rules, permalinks, .htaccess, and server config.');
        
        // Check if rewrite rules exist in database
        global $wp_rewrite;
        $rules = get_option('rewrite_rules');
        $has_horizon_rule = false;
        if ($rules && is_array($rules)) {
            $has_horizon_rule = isset($rules['^hoa-horizon-admin/?$']);
        }
        error_log('HOA Horizon: Rewrite rule for hoa-horizon-admin exists: ' . ($has_horizon_rule ? 'Yes' : 'No'));
        
        wp_die('Front-end admin not found. Please check your permalinks, flush rewrite rules, ensure .htaccess is writable, and verify server configuration. <a href="' . admin_url('options-permalink.php') . '">Flush Permalinks</a> | <a href="' . admin_url('plugins.php') . '">Go to Plugins</a>', 'Error', array('response' => 404));
    }

    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/hoa-login'));
        exit;
    }
    $current_user = wp_get_current_user();
    $allowed_roles = get_option('hoa_horizon_frontend_roles', array());
    if (!is_array($allowed_roles)) {
        $allowed_roles = array();
    }
    $allowed_users = get_option('hoa_horizon_frontend_users', array());
    if (!is_array($allowed_users)) {
        $allowed_users = array();
    }
    if (!array_intersect($allowed_roles, $current_user->roles) && !in_array($current_user->ID, $allowed_users)) {
        wp_safe_redirect(home_url('/'));
        exit;
    }

    // Get colors from Appearance settings
    $bg_color = get_option('hoa_horizon_login_bg_color', '#fff');
    $text_color = get_option('hoa_horizon_login_text_color', '#000');
    $button_color = get_option('hoa_horizon_login_button_color', '#0073aa');

    get_header();
    ?>
    <div class="hoa-horizon-admin-wrap">
        <header class="hoa-horizon-admin-header">
            <h1>HOA Horizon Admin Dashboard</h1>
            <p>Welcome, <?php echo esc_html($current_user->display_name); ?>!</p>
        </header>
        <nav class="hoa-horizon-admin-nav">
            <ul>
                <li><a href="#announcements" class="active">Announcements</a></li>
                <li><a href="#homepage-builder">Homepage Builder</a></li>
                <li><a href="#documents">Documents (Coming Soon)</a></li>
                <li><a href="#calendar">Calendar (Coming Soon)</a></li>
                <li><a href="#settings">Settings (Coming Soon)</a></li>
            </ul>
        </nav>
        <main class="hoa-horizon-admin-content">
            <section id="announcements" class="hoa-horizon-section">
                <h2>Manage Announcements</h2>
                <?php
                if (isset($_POST['hoa_horizon_submit_announcement']) && check_admin_referer('hoa_horizon_admin_action', 'hoa_horizon_nonce')) {
                    $title = sanitize_text_field($_POST['announcement_title']);
                    $content = wp_kses_post($_POST['announcement_content']);
                    $post_id = wp_insert_post(array(
                        'post_title' => $title,
                        'post_content' => $content,
                        'post_type' => 'hoa_announcement',
                        'post_status' => 'publish',
                        'post_author' => $current_user->ID,
                    ));
                    if ($post_id && !is_wp_error($post_id)) {
                        echo '<p class="success">Announcement posted successfully!</p>';
                    } else {
                        echo '<p class="error">Error posting announcement.</p>';
                    }
                }
                ?>
                <form method="post" action="">
                    <?php wp_nonce_field('hoa_horizon_admin_action', 'hoa_horizon_nonce'); ?>
                    <div class="form-group">
                        <label for="announcement_title">Title</label>
                        <input type="text" id="announcement_title" name="announcement_title" required>
                    </div>
                    <div class="form-group">
                        <label for="announcement_content">Content</label>
                        <textarea id="announcement_content" name="announcement_content" rows="5" required></textarea>
                    </div>
                    <input type="submit" name="hoa_horizon_submit_announcement" value="Post Announcement" class="button">
                </form>
                <h3>Your Recent Announcements</h3>
                <?php
                $args = array(
                    'post_type' => 'hoa_announcement',
                    'posts_per_page' => 5,
                    'author' => $current_user->ID,
                );
                $query = new WP_Query($args);
                if ($query->have_posts()) {
                    echo '<ul>';
                    while ($query->have_posts()) {
                        $query->the_post();
                        $edit_link = admin_url("post.php?post=" . get_the_ID() . "&action=edit");
                        echo '<li>' . get_the_title() . ' - <a href="' . esc_url($edit_link) . '">Edit</a></li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No announcements found.</p>';
                }
                wp_reset_postdata();
                ?>
            </section>
            
            <section id="homepage-builder" class="hoa-horizon-section">
                <?php hoa_horizon_render_homepage_builder(); ?>
            </section>
            
            <section id="documents" class="hoa-horizon-section" style="display: none;">
                <h2>Manage Documents</h2>
                <p>Coming soon!</p>
            </section>
            
            <section id="calendar" class="hoa-horizon-section" style="display: none;">
                <h2>Calendar</h2>
                <p>Coming soon!</p>
            </section>
            
            <section id="settings" class="hoa-horizon-section" style="display: none;">
                <h2>Settings</h2>
                <p>Coming soon!</p>
            </section>
        </main>
        <?php
        // JavaScript for tab navigation, image upload, and block ordering (already handled in admin-script.js)
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Handle tab navigation clicks
            let currentTab = '#announcements'; // Default to Announcements tab
            $('.hoa-horizon-admin-nav a').on('click', function(e) {
                e.preventDefault();
                currentTab = $(this).attr('href'); // Save the current tab
                $('.hoa-horizon-admin-nav a').removeClass('active');
                $(this).addClass('active');
                $('.hoa-horizon-section').hide();
                $(currentTab).show();
                if (currentTab === '#homepage-builder') {
                    reloadHomepageBuilder(); // Refresh the builder when showing this tab
                }
            });

            // Show the default section (Announcements) on page load
            $('#announcements').show();
        });
        </script>
        <?php
        // Add inline styles for the front-end admin page using the same colors as the login page
        echo '<style>
            .hoa-horizon-admin-wrap {
                max-width: 1200px;
                margin: 20px auto;
                background: ' . esc_attr($bg_color) . ';
                color: ' . esc_attr($text_color) . ';
                border: 1px solid #ddd;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .hoa-horizon-admin-header h1, .hoa-horizon-admin-header p {
                color: ' . esc_attr($text_color) . ';
            }
            .hoa-horizon-admin-nav a, .hoa-horizon-section h2, .form-group label {
                color: ' . esc_attr($text_color) . ';
            }
            .button {
                background: ' . esc_attr($button_color) . ';
                color: #fff;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                display: inline-block;
            }
            .button:hover {
                background: ' . esc_attr(hoa_horizon_darken_color($button_color, 20)) . ';
            }
            .success {
                color: #155724;
                background: #d4edda;
                padding: 10px;
                border-radius: 4px;
                margin-bottom: 15px;
            }
            .error {
                color: #721c24;
                background: #f8d7da;
                padding: 10px;
                border-radius: 4px;
                margin-bottom: 15px;
            }
            .hoa-horizon-section .wp-editor-container {
                margin-bottom: 20px;
            }
            .hoa-horizon-section .button {
                margin-top: 10px;
            }
        </style>';
    
    get_footer();
    exit;
}
add_action('template_redirect', 'hoa_horizon_admin_page', 99, 0);