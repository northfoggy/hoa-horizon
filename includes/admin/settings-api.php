<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register settings with sections for tabs
function hoa_horizon_register_settings() {
    // General group
    register_setting('hoa_horizon_general', 'hoa_horizon_site_name');
    register_setting('hoa_horizon_general', 'hoa_horizon_announcement_visibility');
    register_setting('hoa_horizon_general', 'hoa_horizon_frontend_roles', 'sanitize_text_field');
    register_setting('hoa_horizon_general', 'hoa_horizon_frontend_users', 'hoa_horizon_sanitize_users');

    // Appearance group
    register_setting('hoa_horizon_appearance', 'hoa_horizon_login_bg_color', 'sanitize_hex_color');
    register_setting('hoa_horizon_appearance', 'hoa_horizon_login_text_color', 'sanitize_hex_color');
    register_setting('hoa_horizon_appearance', 'hoa_horizon_login_button_color', 'sanitize_hex_color');

    // Modules group
    register_setting('hoa_horizon_modules', 'hoa_horizon_modules', 'hoa_horizon_sanitize_modules');

    // Homepage Blocks group
    register_setting('hoa_horizon_homepage', 'hoa_horizon_homepage_blocks', 'hoa_horizon_sanitize_blocks');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_block_order', 'sanitize_text_field');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_welcome_message', 'wp_kses_post');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_welcome_title', 'sanitize_text_field');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_header_image', 'absint');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_header_image_height', 'absint');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_header_image_width', 'absint');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_items_per_row', 'absint');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_theme', 'sanitize_text_field');
    register_setting('hoa_horizon_homepage', 'hoa_horizon_custom_css', 'sanitize_textarea_field');

    // General Settings section
    add_settings_section(
        'hoa_horizon_main_section',
        'General Settings',
        null,
        'hoa-horizon-settings-general'
    );

    add_settings_field(
        'hoa_horizon_site_name',
        'HOA Site Name',
        'hoa_horizon_site_name_callback',
        'hoa-horizon-settings-general',
        'hoa_horizon_main_section'
    );

    add_settings_field(
        'hoa_horizon_announcement_visibility',
        'Default Announcement Visibility',
        'hoa_horizon_announcement_visibility_callback',
        'hoa-horizon-settings-general',
        'hoa_horizon_main_section'
    );

    // Front-End Admin Access section
    add_settings_section(
        'hoa_horizon_frontend_section',
        'Front-End Admin Access',
        null,
        'hoa-horizon-settings-general'
    );

    add_settings_field(
        'hoa_horizon_frontend_roles',
        'Allowed Roles',
        'hoa_horizon_frontend_roles_callback',
        'hoa-horizon-settings-general',
        'hoa_horizon_frontend_section'
    );

    add_settings_field(
        'hoa_horizon_frontend_users',
        'Specific Users',
        'hoa_horizon_frontend_users_callback',
        'hoa-horizon-settings-general',
        'hoa_horizon_frontend_section'
    );

    // Appearance Settings section
    add_settings_section(
        'hoa_horizon_appearance_section',
        'Login Page Appearance',
        null,
        'hoa-horizon-settings-appearance'
    );

    add_settings_field(
        'hoa_horizon_login_bg_color',
        'Background Color',
        'hoa_horizon_login_bg_color_callback',
        'hoa-horizon-settings-appearance',
        'hoa_horizon_appearance_section'
    );

    add_settings_field(
        'hoa_horizon_login_text_color',
        'Text Color',
        'hoa_horizon_login_text_color_callback',
        'hoa-horizon-settings-appearance',
        'hoa_horizon_appearance_section'
    );

    add_settings_field(
        'hoa_horizon_login_button_color',
        'Button Color',
        'hoa_horizon_login_button_color_callback',
        'hoa-horizon-settings-appearance',
        'hoa_horizon_appearance_section'
    );

    // Modules Settings section
    add_settings_section(
        'hoa_horizon_modules_section',
        'Homepage Modules',
        null,
        'hoa-horizon-settings-modules'
    );

    add_settings_field(
        'hoa_horizon_modules',
        'Select Modules',
        'hoa_horizon_modules_callback',
        'hoa-horizon-settings-modules',
        'hoa_horizon_modules_section'
    );

    // Homepage Blocks Settings section
    add_settings_section(
        'hoa_horizon_homepage_section',
        'Homepage Blocks',
        null,
        'hoa-horizon-settings-homepage'
    );
}
add_action('admin_init', 'hoa_horizon_register_settings');