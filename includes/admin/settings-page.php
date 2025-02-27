<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Settings page content with tabs
function hoa_horizon_settings_page() {
    ?>
    <div class="wrap">
        <h1>HOA Horizon Settings</h1>
        <h2 class="nav-tab-wrapper">
            <a href="#tab-general" class="nav-tab nav-tab-active">General</a>
            <a href="#tab-appearance" class="nav-tab">Appearance</a>
            <a href="#tab-modules" class="nav-tab">Front End Modules</a>
            <a href="#tab-homepage" class="nav-tab">Homepage Builder</a>
        </h2>
        <div id="tab-general" class="settings-section">
            <form method="post" action="options.php">
                <?php
                settings_fields('hoa_horizon_general');
                do_settings_sections('hoa-horizon-settings-general');
                submit_button();
                ?>
            </form>
        </div>
        <div id="tab-appearance" class="settings-section" style="display: none;">
            <form method="post" action="options.php">
                <?php
                settings_fields('hoa_horizon_appearance');
                do_settings_sections('hoa-horizon-settings-appearance');
                submit_button('Save Changes');
                ?>
                <p><input type="submit" name="hoa_horizon_reset_colors" value="Reset to Defaults" class="button-secondary" style="margin-top: 10px;" /></p>
            </form>
        </div>
        <div id="tab-modules" class="settings-section" style="display: none;">
            <form method="post" action="options.php">
                <?php
                settings_fields('hoa_horizon_modules');
                do_settings_sections('hoa-horizon-settings-modules');
                submit_button();
                ?>
            </form>
        </div>
        <div id="tab-homepage" class="settings-section" style="display: none;">
            <?php hoa_horizon_render_homepage_builder(); ?>
        </div>
        <style>
            .nav-tab-wrapper {
                margin-bottom: 20px;
            }
            .nav-tab {
                display: inline-block;
                padding: 10px 15px;
                margin-right: 5px;
                background: #f1f1f1;
                border: 1px solid #ccc;
                border-bottom: none;
                text-decoration: none;
                color: #0073aa;
                cursor: pointer;
            }
            .nav-tab-active, .nav-tab:hover {
                background: #fff;
                border-color: #ccc;
                border-bottom: 1px solid #fff;
                color: #0073aa;
            }
            .settings-section {
                border: 1px solid #ccc;
                padding: 20px;
                background: #fff;
            }
            .wp-picker-container .wp-color-result {
                margin: 0 0 6px 6px;
            }
            .wp-picker-container input[type="text"] {
                width: 100px !important;
            }
        </style>
        <script>
        jQuery(document).ready(function($) {
            // Initialize color pickers immediately
            $('.color-picker').wpColorPicker();
            
            // Tab navigation
            let currentTab = '#tab-general'; // Default to General tab
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                currentTab = $(this).attr('href'); // Save the current tab
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.settings-section').hide();
                $(currentTab).show();
                if (currentTab === '#tab-homepage') {
                    reloadHomepageBuilder(); // Refresh the builder when showing this tab
                }
            });
            $('.settings-section').hide();
            $('#tab-general').show();
        });
        </script>
    </div>
    <?php
}