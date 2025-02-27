<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="hoa-module hoa-welcome">
    <h2><?php echo esc_html($welcome_title); ?></h2>
    <div class="hoa-welcome-content"><?php echo wpautop($welcome_message); ?></div>
</section>