<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Callback functions for settings fields
function hoa_horizon_site_name_callback() {
    $value = get_option('hoa_horizon_site_name', 'Academy Park POA');
    echo '<input type="text" name="hoa_horizon_site_name" value="' . esc_attr($value) . '" />';
}

function hoa_horizon_announcement_visibility_callback() {
    $value = get_option('hoa_horizon_announcement_visibility', 'public');
    ?>
    <select name="hoa_horizon_announcement_visibility">
        <option value="public" <?php selected($value, 'public'); ?>>Public</option>
        <option value="logged_in" <?php selected($value, 'logged_in'); ?>>Logged-in Users Only</option>
    </select>
    <?php
}

function hoa_horizon_frontend_roles_callback() {
    $roles = get_option('hoa_horizon_frontend_roles', array());
    if (!is_array($roles)) {
        $roles = array();
    }
    $available_roles = array(
        'hoa_administrator' => 'HOA Administrator',
        'board_member' => 'Board Member',
        'committee_member' => 'Committee Member',
    );
    ?>
    <select name="hoa_horizon_frontend_roles[]" multiple="multiple" size="3">
        <?php foreach ($available_roles as $role_key => $role_name) : ?>
            <option value="<?php echo esc_attr($role_key); ?>" <?php selected(in_array($role_key, $roles), true); ?>>
                <?php echo esc_html($role_name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description">Select roles that can access the front-end admin page. Hold Ctrl (Windows) or Command (Mac) to select multiple.</p>
    <?php
}

function hoa_horizon_frontend_users_callback() {
    $selected_users = get_option('hoa_horizon_frontend_users', array());
    if (!is_array($selected_users)) {
        $selected_users = array();
    }
    $users = get_users(array('fields' => array('ID', 'display_name')));
    ?>
    <select name="hoa_horizon_frontend_users[]" multiple="multiple" size="5" style="width: 100%;">
        <?php foreach ($users as $user) : ?>
            <option value="<?php echo esc_attr($user->ID); ?>" <?php selected(in_array($user->ID, $selected_users), true); ?>>
                <?php echo esc_html($user->display_name) . ' (' . $user->ID . ')'; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description">Select specific users who can access the front-end admin page. Hold Ctrl (Windows) or Command (Mac) to select multiple.</p>
    <?php
}

function hoa_horizon_login_bg_color_callback() {
    $value = get_option('hoa_horizon_login_bg_color', '#fff');
    echo '<input type="text" name="hoa_horizon_login_bg_color" value="' . esc_attr($value) . '" class="color-picker" data-default-color="' . esc_attr($value) . '" />';
}

function hoa_horizon_login_text_color_callback() {
    $value = get_option('hoa_horizon_login_text_color', '#000');
    echo '<input type="text" name="hoa_horizon_login_text_color" value="' . esc_attr($value) . '" class="color-picker" data-default-color="' . esc_attr($value) . '" />';
}

function hoa_horizon_login_button_color_callback() {
    $value = get_option('hoa_horizon_login_button_color', '#0073aa');
    echo '<input type="text" name="hoa_horizon_login_button_color" value="' . esc_attr($value) . '" class="color-picker" data-default-color="' . esc_attr($value) . '" />';
}

function hoa_horizon_modules_callback() {
    $selected_modules = get_option('hoa_horizon_modules', array('welcome'));
    if (!is_array($selected_modules)) {
        $selected_modules = array();
    }
    $available_modules = array(
        'welcome' => 'HOA Welcome Message',
        'weather' => 'Weather',
        'whats_new' => 'What\'s New',
        'upcoming_events' => 'Upcoming Events',
    );
    ?>
    <select name="hoa_horizon_modules[]" multiple="multiple" size="4">
        <?php foreach ($available_modules as $module_key => $module_name) : ?>
            <option value="<?php echo esc_attr($module_key); ?>" <?php selected(in_array($module_key, $selected_modules), true); ?>>
                <?php echo esc_html($module_name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description">Select modules to display on the homepage. Hold Ctrl (Windows) or Command (Mac) to select multiple.</p>
    <?php
}

// Sanitize functions (included here for completeness, as they are part of settings logic)
function hoa_horizon_sanitize_users($input) {
    if (!is_array($input)) {
        return array();
    }
    return array_map('intval', $input);
}

function hoa_horizon_sanitize_modules($input) {
    if (!is_array($input)) {
        return array();
    }
    $valid_modules = array('welcome', 'weather', 'whats_new', 'upcoming_events');
    return array_intersect($input, $valid_modules);
}

function hoa_horizon_sanitize_blocks($input) {
    if (!is_array($input)) {
        return array();
    }
    $valid_blocks = array('welcome', 'weather', 'whats_new', 'upcoming_events');
    return array_intersect($input, $valid_blocks);
}