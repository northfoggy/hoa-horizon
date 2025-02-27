<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Render the Homepage Builder interface
function hoa_horizon_render_homepage_builder() {
    // Get current homepage settings
    $selected_blocks = get_option('hoa_horizon_homepage_blocks', array('welcome', 'whats_new', 'upcoming_events'));
    $block_order_string = get_option('hoa_horizon_block_order', 'welcome,whats_new,upcoming_events');
    $block_order = explode(',', $block_order_string);
    $welcome_title = get_option('hoa_horizon_welcome_title', 'Welcome to our Community');
    $welcome_message = get_option('hoa_horizon_welcome_message', 'Welcome to our community! We are glad you are here. Stay tuned for updates and events.');
    $header_image_id = get_option('hoa_horizon_header_image', 0);
    $header_image_height = get_option('hoa_horizon_header_image_height', 200);
    $header_image_width = get_option('hoa_horizon_header_image_width', 1200);
    $items_per_row = get_option('hoa_horizon_items_per_row', 3);
    $current_theme = get_option('hoa_horizon_theme', 'light');
    
    // Define header_image_url
    $header_image_url = $header_image_id ? wp_get_attachment_url($header_image_id) : '';
    
    // Create ordered blocks array based on the current order
    $ordered_blocks = array_intersect($block_order, $selected_blocks);
    
    // Define all available blocks
    $available_blocks = array(
        'welcome' => 'Welcome Message',
        'weather' => 'Weather',
        'whats_new' => 'What\'s New',
        'upcoming_events' => 'Upcoming Events',
    );
    ?>
    
    <div class="hoa-homepage-builder">
        <h2>Homepage Builder</h2>
        <p class="hoa-builder-intro">Customize your HOA homepage by dragging and dropping blocks, editing content, and adjusting settings.</p>
        
        <form method="post" action="" id="homepage-builder-form">
            <?php wp_nonce_field('hoa_horizon_homepage_builder', 'hoa_horizon_nonce'); ?>
            <input type="hidden" name="hoa_horizon_block_order" id="hoa-block-order-input" value="<?php echo esc_attr($block_order_string); ?>">
            
            <div class="hoa-editor-layout">
                <!-- Left sidebar with available blocks and settings -->
                <div class="hoa-editor-sidebar">
                    <div class="editor-panel">
                        <h3>Available Blocks</h3>
                        <p class="description">Drag blocks to add them to your homepage</p>
                        
                        <div class="hoa-block-palette">
                            <?php foreach ($available_blocks as $block_key => $block_name) : ?>
                                <div class="block-item" data-block="<?php echo esc_attr($block_key); ?>" draggable="true">
                                    <div class="block-icon"><?php echo hoa_horizon_get_block_icon($block_key); ?></div>
                                    <div class="block-name"><?php echo esc_html($block_name); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="editor-panel">
                        <h3>Homepage Settings</h3>
                        
                        <div class="form-group">
                            <label for="hoa_horizon_items_per_row">Items Per Row</label>
                            <select name="hoa_horizon_items_per_row" id="hoa_horizon_items_per_row">
                                <option value="1" <?php selected($items_per_row, 1); ?>>1 Block (Full Width)</option>
                                <option value="2" <?php selected($items_per_row, 2); ?>>2 Blocks</option>
                                <option value="3" <?php selected($items_per_row, 3); ?>>3 Blocks</option>
                                <option value="4" <?php selected($items_per_row, 4); ?>>4 Blocks</option>
                            </select>
                        </div>
                        
                        <div class="form-group theme-selector">
                            <label>Theme</label>
                            <div class="theme-options">
                                <div class="theme-option <?php echo $current_theme === 'light' ? 'selected' : ''; ?>" data-theme="light">
                                    <input type="radio" name="hoa_horizon_theme" value="light" 
                                        <?php checked($current_theme, 'light'); ?> id="theme_light">
                                    <label for="theme_light">
                                        <div class="theme-preview light-theme"></div>
                                        <span>Light</span>
                                    </label>
                                </div>
                                <div class="theme-option <?php echo $current_theme === 'dark' ? 'selected' : ''; ?>" data-theme="dark">
                                    <input type="radio" name="hoa_horizon_theme" value="dark" 
                                        <?php checked($current_theme, 'dark'); ?> id="theme_dark">
                                    <label for="theme_dark">
                                        <div class="theme-preview dark-theme"></div>
                                        <span>Dark</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Quick Layouts</label>
                            <div class="preset-options">
                                <button type="button" class="preset-button" data-preset="standard">
                                    <span class="preset-icon dashicons dashicons-grid-view"></span>
                                    <span>Standard</span>
                                </button>
                                <button type="button" class="preset-button" data-preset="welcome-focused">
                                    <span class="preset-icon dashicons dashicons-welcome-write-blog"></span>
                                    <span>Welcome Focus</span>
                                </button>
                                <button type="button" class="preset-button" data-preset="events-news">
                                    <span class="preset-icon dashicons dashicons-calendar-alt"></span>
                                    <span>Events & News</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="hoa_horizon_header_image">Header Image</label>
                            <div class="media-uploader-wrap">
                                <input type="hidden" name="hoa_horizon_header_image" id="hoa_horizon_header_image" value="<?php echo esc_attr($header_image_id); ?>">
                                <button type="button" class="button upload-header-image">Select Image</button>
                                <button type="button" class="button remove-header-image" <?php echo !$header_image_id ? 'style="display:none"' : ''; ?>>Remove Image</button>
                            </div>
                            <div class="header-image-preview">
                                <?php if ($header_image_url) : ?>
                                    <img src="<?php echo esc_url($header_image_url); ?>" alt="Header Preview">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Header Size (pixels)</label>
                            <div class="input-group">
                                <label for="hoa_horizon_header_image_width">Width:</label>
                                <input type="number" name="hoa_horizon_header_image_width" id="hoa_horizon_header_image_width" 
                                       value="<?php echo esc_attr($header_image_width); ?>" min="300" max="2000" step="10">
                            </div>
                            <div class="input-group">
                                <label for="hoa_horizon_header_image_height">Height:</label>
                                <input type="number" name="hoa_horizon_header_image_height" id="hoa_horizon_header_image_height" 
                                       value="<?php echo esc_attr($header_image_height); ?>" min="100" max="1000" step="10">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="hoa_horizon_save_homepage" class="button button-primary">Save Homepage</button>
                        <button type="button" class="button button-secondary preview-homepage">Preview Homepage</button>
                    </div>
                </div>
                
                <!-- Right side with preview and block editing area -->
                <div class="hoa-editor-main">
                    <div class="editor-panel">
                        <h3>Homepage Layout</h3>
                        <p class="description">Drag to rearrange blocks. Click on a block to edit its content.</p>
                        
                        <div class="hoa-layout-preview" data-items-per-row="<?php echo esc_attr($items_per_row); ?>" data-theme="<?php echo esc_attr($current_theme); ?>">
                            <!-- Header Area -->
                            <div class="preview-header <?php echo $header_image_url ? 'has-image' : ''; ?>" 
                                 style="<?php echo $header_image_url ? 'background-image: url(' . esc_url($header_image_url) . ');' : ''; ?>
                                        height: <?php echo esc_attr($header_image_height); ?>px;
                                        max-width: <?php echo esc_attr($header_image_width); ?>px;">
                                <?php if (!$header_image_url) : ?>
                                    <div class="placeholder-text">Header Image Area</div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Sortable blocks container -->
                            <div class="hoa-sortable-blocks-container">
                                <!-- If no blocks are selected, show a placeholder -->
                                <?php if (empty($ordered_blocks)) : ?>
                                    <div class="layout-empty-state">
                                        <p>Your homepage is empty! Drag blocks from the left to add content.</p>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($ordered_blocks as $block_key) : ?>
                                        <div class="preview-block" data-block="<?php echo esc_attr($block_key); ?>">
                                            <div class="preview-block-header">
                                                <span class="block-title">
                                                    <?php echo hoa_horizon_get_block_icon($block_key); ?> 
                                                    <?php echo esc_html($available_blocks[$block_key]); ?>
                                                </span>
                                                <div class="block-controls">
                                                    <button type="button" class="edit-block" title="Edit"><span class="dashicons dashicons-edit"></span></button>
                                                    <button type="button" class="remove-block" title="Remove"><span class="dashicons dashicons-no"></span></button>
                                                </div>
                                            </div>
                                            <div class="preview-block-content">
                                                <?php echo hoa_horizon_get_block_preview($block_key); ?>
                                            </div>
                                            <input type="hidden" name="hoa_horizon_homepage_blocks[]" value="<?php echo esc_attr($block_key); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Block configuration modals -->
    <div id="welcome-block-modal" class="block-config-modal">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h3>Configure Welcome Block</h3>
            <div class="form-group">
                <label for="welcome_title">Title</label>
                <input type="text" id="welcome_title" value="<?php echo esc_attr($welcome_title); ?>">
            </div>
            <div class="form-group">
                <label for="welcome_message">Content</label>
                <textarea id="welcome_message" rows="8"><?php echo esc_textarea($welcome_message); ?></textarea>
            </div>
            <button type="button" class="button save-welcome-config">Save Changes</button>
        </div>
    </div>
    
    <div id="weather-block-modal" class="block-config-modal">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h3>Configure Weather Block</h3>
            <p>Weather data will be automatically pulled based on the HOA location.</p>
            <div class="form-group">
                <label>Display Options</label>
                <div>
                    <label><input type="checkbox" checked disabled> Current Temperature</label>
                </div>
                <div>
                    <label><input type="checkbox" checked disabled> Weather Condition</label>
                </div>
                <div>
                    <label><input type="checkbox" checked disabled> Daily Forecast</label>
                </div>
            </div>
            <p class="description">Weather block settings will be implemented in a future update.</p>
            <button type="button" class="button close-modal-button">Close</button>
        </div>
    </div>
    
    <div id="whats_new-block-modal" class="block-config-modal">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h3>Configure What's New Block</h3>
            <div class="form-group">
                <label>Number of announcements to display</label>
                <select disabled>
                    <option selected>3</option>
                    <option>5</option>
                    <option>10</option>
                </select>
            </div>
            <div class="form-group">
                <label><input type="checkbox" checked disabled> Show date</label>
            </div>
            <div class="form-group">
                <label><input type="checkbox" checked disabled> Show author</label>
            </div>
            <p class="description">What's New block settings will be implemented in a future update.</p>
            <button type="button" class="button close-modal-button">Close</button>
        </div>
    </div>
    
    <div id="upcoming_events-block-modal" class="block-config-modal">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h3>Configure Upcoming Events Block</h3>
            <div class="form-group">
                <label>Display Mode</label>
                <select disabled>
                    <option selected>List</option>
                    <option>Calendar</option>
                </select>
            </div>
            <div class="form-group">
                <label>Number of events to display</label>
                <select disabled>
                    <option>3</option>
                    <option selected>5</option>
                    <option>10</option>
                </select>
            </div>
            <p class="description">Upcoming Events block settings will be implemented in a future update.</p>
            <button type="button" class="button close-modal-button">Close</button>
        </div>
    </div>
    <?php
}