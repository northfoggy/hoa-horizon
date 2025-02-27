<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="hoa-module hoa-whats-new">
    <h2>What’s New</h2>
    <?php
    // Get recent announcements
    $args = array(
        'post_type' => 'hoa_announcement',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    $recent_posts = get_posts($args);
    
    if ($recent_posts) {
        echo '<ul class="announcements-list">';
        foreach ($recent_posts as $post) {
            $date = date_i18n(get_option('date_format'), strtotime($post->post_date));
            echo '<li>';
            echo '<div class="announcement-date">' . esc_html($date) . '</div>';
            echo '<h3 class="announcement-title">' . esc_html($post->post_title) . '</h3>';
            echo '<div class="announcement-excerpt">' . wp_trim_words($post->post_content, 20, '... <a href="' . get_permalink($post->ID) . '">Read More</a>') . '</div>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No announcements yet. Check back soon!</p>';
    }
    ?>
</section>