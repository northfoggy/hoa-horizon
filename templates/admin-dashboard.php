<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
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
</div>