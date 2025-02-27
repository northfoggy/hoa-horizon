/**
 * HOA Horizon Admin JavaScript
 * 
 * Handles the interactive homepage builder functionality
 */

(function($) {
    $(document).ready(function() {
        console.log('HOA Horizon admin script loaded, jQuery version:', $.fn.jquery);
        
        let currentTab = '#tab-homepage'; // Default to Homepage Builder tab

        // Initialize color pickers
        function initColorPickers() {
            if (typeof $.fn.wpColorPicker === 'undefined') {
                console.warn('wpColorPicker not loaded yet, retrying in 100ms...');
                setTimeout(initColorPickers, 100);
                return;
            }
            console.log('Initializing HOA Horizon color pickers');
            $('.color-picker').each(function() {
                var $input = $(this);
                $input.wpColorPicker({
                    defaultColor: $input.data('default-color'),
                    change: function(event, ui) {
                        $input.val(ui.color.toString());
                    },
                    clear: function() {
                        $input.val('');
                    }
                });
            });
        }
        
        // Initialize color pickers if they exist
        if ($('.color-picker').length) {
            initColorPickers();
        }
        
        // Tab navigation in WordPress admin
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
        
        // Ensure default tab is visible on load in WordPress admin
        if ($('.settings-section').length) {
            $('.settings-section').hide();
            $('#tab-general').show();
        }

        // -------------------------------------------------------------
        // Homepage Builder Functionality
        // -------------------------------------------------------------

        if ($('.hoa-homepage-builder').length) {
            initHomepageBuilder();
        }
        
        function initHomepageBuilder() {
            // Make blocks draggable from the palette
            initDragAndDrop();
            
            // Initialize sortable for the layout container
            initSortable();
            
            // Setup event listeners for the homepage builder
            setupEventListeners();
            
            // Handle media uploader for images
            setupMediaUploader();
        }
        
        // Initialize drag and drop functionality
        function initDragAndDrop() {
            // Make palette blocks draggable
            $('.block-item').on('dragstart', function(e) {
                const blockData = {
                    type: $(this).data('block')
                };
                e.originalEvent.dataTransfer.setData('text/plain', JSON.stringify(blockData));
                $(this).addClass('dragging');
            }).on('dragend', function() {
                $(this).removeClass('dragging');
            });
            
            // Make the sortable container a drop target
            $('.hoa-sortable-blocks-container').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('drop-target');
            }).on('dragleave', function() {
                $(this).removeClass('drop-target');
            }).on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drop-target');
                
                try {
                    const blockData = JSON.parse(e.originalEvent.dataTransfer.getData('text/plain'));
                    if (blockData && blockData.type) {
                        addBlock(blockData.type);
                    }
                } catch (err) {
                    console.error('Error processing dropped block:', err);
                }
            });
        }
        
        // Initialize sortable functionality
        function initSortable() {
            if (typeof $.fn.sortable !== 'undefined') {
                $('.hoa-sortable-blocks-container').sortable({
                    placeholder: 'preview-block-placeholder',
                    handle: '.preview-block-header',
                    update: function() {
                        updateBlockOrder();
                    }
                });
            } else {
                console.warn('jQuery UI Sortable not available');
            }
        }
        
        // Setup event listeners
        function setupEventListeners() {
            // Theme selection
            $('.theme-option').click(function() {
                $('.theme-option').removeClass('selected');
                $(this).addClass('selected');
                $(this).find('input[type="radio"]').prop('checked', true);
                
                const theme = $(this).data('theme');
                $('.hoa-layout-preview').attr('data-theme', theme);
            });
            
            // Items per row selection
            $('#hoa_horizon_items_per_row').change(function() {
                const itemsPerRow = $(this).val();
                $('.hoa-layout-preview').attr('data-items-per-row', itemsPerRow);
            });
            
            // Quick layout presets
            $('.preset-button').click(function() {
                const preset = $(this).data('preset');
                applyLayoutPreset(preset);
            });
            
            // Edit block button click
            $(document).on('click', '.edit-block', function() {
                const blockType = $(this).closest('.preview-block').data('block');
                openBlockModal(blockType);
            });
            
            // Remove block button click
            $(document).on('click', '.remove-block', function() {
                const $block = $(this).closest('.preview-block');
                
                if (confirm('Are you sure you want to remove this block?')) {
                    $block.fadeOut(300, function() {
                        $(this).remove();
                        updateBlockOrder();
                        
                        // Show empty state if no blocks remain
                        if ($('.preview-block').length === 0) {
                            $('.hoa-sortable-blocks-container').html('<div class="layout-empty-state"><p>Your homepage is empty! Drag blocks from the left to add content.</p></div>');
                        }
                    });
                }
            });
            
            // Close modal button
            $('.close-modal, .close-modal-button').click(function() {
                $(this).closest('.block-config-modal').hide();
            });
            
            // Preview homepage button
            $('.preview-homepage').click(function(e) {
                e.preventDefault();
                window.open(window.location.origin, '_blank');
            });
            
            // Save welcome block config
            $('.save-welcome-config').click(function() {
                const title = $('#welcome_title').val();
                const content = $('#welcome_message').val();
                
                // Update the preview block content immediately
                $('.preview-block[data-block="welcome"] .preview-block-content').html(
                    '<h3>' + title + '</h3><p>' + (content.length > 100 ? content.substring(0, 100) + '...' : content) + '</p>'
                );
                
                // Update the hidden form fields
                $('input[name="hoa_horizon_welcome_title"]').val(title);
                $('textarea[name="hoa_horizon_welcome_message"]').val(content);
                
                // Close the modal
                $('#welcome-block-modal').hide();
                
                // Save via AJAX
                saveWelcomeBlock(title, content);
            });
            
            // Save homepage button
            $('#save-homepage-button').click(function(e) {
                e.preventDefault(); // Prevent default form submission
                saveHomepageData();
            });
        }
        
        // Set up the WordPress media uploader
        function setupMediaUploader() {
            $('.upload-header-image').click(function(e) {
                e.preventDefault();
                
                // Create the media frame
                const frame = wp.media({
                    title: 'Select or Upload Header Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });
                
                // When an image is selected, run a callback
                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    
                    // Set the image ID
                    $('#hoa_horizon_header_image').val(attachment.id);
                    
                    // Update the preview
                    if (attachment.url) {
                        $('.preview-header').addClass('has-image').css('background-image', 'url(' + attachment.url + ')');
                        $('.header-image-preview').html('<img src="' + attachment.url + '" alt="Header Preview">');
                        $('.remove-header-image').show();
                    }
                });
                
                // Open the modal
                frame.open();
            });
            
            // Remove header image
            $('.remove-header-image').click(function() {
                $('#hoa_horizon_header_image').val('');
                $('.preview-header').removeClass('has-image').css('background-image', 'none');
                $('.header-image-preview').empty();
                $(this).hide();
            });
        }
        
        // Add a new block to the layout
        function addBlock(blockType) {
            // Check if the block already exists (some blocks should be unique)
            if (blockType === 'welcome' && $('.preview-block[data-block="welcome"]').length > 0) {
                alert('You can only have one Welcome block on your homepage.');
                return;
            }
            
            if (blockType === 'weather' && $('.preview-block[data-block="weather"]').length > 0) {
                alert('You can only have one Weather block on your homepage.');
                return;
            }
            
            // Get block title from the palette item
            const blockTitle = $('.block-item[data-block="' + blockType + '"] .block-name').text();
            const blockIcon = $('.block-item[data-block="' + blockType + '"] .block-icon').html();
            
            // Create block HTML
            const blockHtml = `
                <div class="preview-block" data-block="${blockType}">
                    <div class="preview-block-header">
                        <span class="block-title">
                            ${blockIcon} ${blockTitle}
                        </span>
                        <div class="block-controls">
                            <button type="button" class="edit-block" title="Edit"><span class="dashicons dashicons-edit"></span></button>
                            <button type="button" class="remove-block" title="Remove"><span class="dashicons dashicons-no"></span></button>
                        </div>
                    </div>
                    <div class="preview-block-content">
                        ${getBlockPreview(blockType)}
                    </div>
                    <input type="hidden" name="hoa_horizon_homepage_blocks[]" value="${blockType}">
                </div>
            `;
            
            // Remove empty state message if it exists
            $('.layout-empty-state').remove();
            
            // Add the block to the container
            $('.hoa-sortable-blocks-container').append(blockHtml);
            
            // Update the block order
            updateBlockOrder();
            
            // Optional: Open the edit modal for the new block
            openBlockModal(blockType);
        }
        
        // Update the block order hidden input field
        function updateBlockOrder() {
            const blockOrder = [];
            
            $('.preview-block').each(function() {
                blockOrder.push($(this).data('block'));
            });
            
            // Update the hidden input with the current block order
            $('#hoa-block-order-input').val(blockOrder.join(','));
        }
        
        // Get preview HTML for a block
        function getBlockPreview(blockType) {
            switch (blockType) {
                case 'welcome':
                    const welcomeTitle = $('input[name="hoa_horizon_welcome_title"]').val() || 'Welcome to our Community';
                    const welcomeMessage = $('textarea[name="hoa_horizon_welcome_message"]').val() || 'Welcome to our community! We are glad you\'re here.';
                    return `<h3>${welcomeTitle}</h3><p>${welcomeMessage.substring(0, 100)}${welcomeMessage.length > 100 ? '...' : ''}</p>`;
                    
                case 'weather':
                    return `<h3>Weather</h3>
                           <div class="weather-preview">
                               <div class="weather-icon">☀️</div>
                               <div class="weather-temp">72°F</div>
                               <div class="weather-conditions">Sunny</div>
                           </div>`;
                    
                case 'whats_new':
                    return `<h3>What's New</h3>
                           <div class="announcements-preview">
                               <p>📢 Latest community announcements will appear here</p>
                           </div>`;
                    
                case 'upcoming_events':
                    return `<h3>Upcoming Events</h3>
                           <div class="events-preview">
                               <p>📅 Community calendar events will display here</p>
                           </div>`;
                    
                default:
                    return `<p>Content block (${blockType})</p>`;
            }
        }
        
        // Open the configuration modal for a block
        function openBlockModal(blockType) {
            // Hide all modals first
            $('.block-config-modal').hide();
            
            // Show the specific modal for this block type
            $('#' + blockType + '-block-modal').show();
            
            // If it's the welcome block, populate its fields
            if (blockType === 'welcome') {
                const currentTitle = $('input[name="hoa_horizon_welcome_title"]').val() || 'Welcome to our Community';
                const welcomeMessage = $('textarea[name="hoa_horizon_welcome_message"]').val() || 'Welcome to our community! We are glad you\'re here.';
                $('#welcome_title').val(currentTitle);
                $('#welcome_message').val(welcomeMessage);
            }
        }
        
        // Apply a predefined layout preset
        function applyLayoutPreset(preset) {
            // Clear existing blocks
            $('.hoa-sortable-blocks-container').empty();
            
            switch (preset) {
                case 'standard':
                    // Add welcome, weather, what's new and events blocks
                    addBlock('welcome');
                    addBlock('weather');
                    addBlock('whats_new');
                    addBlock('upcoming_events');
                    
                    // Set to 2 blocks per row
                    $('#hoa_horizon_items_per_row').val(2).trigger('change');
                    break;
                    
                case 'welcome-focused':
                    // Add welcome and what's new blocks
                    addBlock('welcome');
                    addBlock('whats_new');
                    
                    // Set to 1 block per row for focus
                    $('#hoa_horizon_items_per_row').val(1).trigger('change');
                    break;
                    
                case 'events-news':
                    // Focus on events and announcements
                    addBlock('upcoming_events');
                    addBlock('whats_new');
                    
                    // Set to 2 blocks per row
                    $('#hoa_horizon_items_per_row').val(2).trigger('change');
                    break;
            }
        }
        
        // Save welcome block via AJAX
        function saveWelcomeBlock(title, content) {
            if (typeof hoaHorizon === 'undefined' || !hoaHorizon.ajaxUrl) {
                return;
            }
            
            $.ajax({
                url: hoaHorizon.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hoa_horizon_update_welcome_block',
                    nonce: hoaHorizon.nonce,
                    title: title,
                    content: content
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Welcome block saved:', response.data);
                        alert('Welcome block saved successfully!');
                    } else {
                        console.error('Error saving welcome block:', response.data);
                        alert('Error saving welcome block. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error when saving welcome block:', error);
                    alert('An error occurred while saving. Please check the console for details.');
                }
            });
        }
        
        // Save homepage data via AJAX
        function saveHomepageData() {
            const data = {
                action: 'hoa_horizon_save_homepage',
                nonce: hoaHorizon.nonce,
                welcome_title: $('input[name="hoa_horizon_welcome_title"]').val(),
                welcome_message: $('textarea[name="hoa_horizon_welcome_message"]').val(),
                block_order: $('#hoa-block-order-input').val(),
                items_per_row: $('#hoa_horizon_items_per_row').val(),
                theme: $('.hoa-layout-preview').attr('data-theme')
            };
            
            $.ajax({
                url: hoaHorizon.ajaxUrl,
                type: 'POST',
                data: data,
                beforeSend: function() {
                    $('#save-homepage-button').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Homepage saved:', response.data);
                        alert('Homepage saved successfully!');
                        // Refresh the homepage builder UI
                        reloadHomepageBuilder();
                        // Stay on or return to the Homepage Builder tab
                        $('.nav-tab[href="#tab-homepage"]').trigger('click');
                    } else {
                        console.error('Error saving homepage:', response.data);
                        alert('Error saving homepage. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error when saving homepage:', error);
                    alert('An error occurred while saving. Please check the console for details.');
                },
                complete: function() {
                    $('#save-homepage-button').prop('disabled', false).text('Save Homepage');
                }
            });
        }
        
        // Reload homepage builder with saved data
        function reloadHomepageBuilder() {
            $.ajax({
                url: hoaHorizon.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hoa_horizon_get_homepage_data',
                    nonce: hoaHorizon.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        // Update welcome block
                        $('input[name="hoa_horizon_welcome_title"]').val(data.welcome_title);
                        $('textarea[name="hoa_horizon_welcome_message"]').val(data.welcome_message);
                        $('.preview-block[data-block="welcome"] .preview-block-content').html(
                            '<h3>' + data.welcome_title + '</h3><p>' + (data.welcome_message.length > 100 ? data.welcome_message.substring(0, 100) + '...' : data.welcome_message) + '</p>'
                        );
                        // Update block order, theme, items per row, etc.
                        $('#hoa-block-order-input').val(data.block_order);
                        $('#hoa_horizon_items_per_row').val(data.items_per_row).trigger('change');
                        $('.hoa-layout-preview').attr('data-theme', data.theme);
                        // Rebuild blocks based on block_order
                        rebuildBlocks(data.block_order.split(','));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading homepage data:', error);
                    alert('Error loading homepage data. Please try again.');
                }
            });
        }
        
        // Rebuild blocks based on block order
        function rebuildBlocks(blockOrder) {
            $('.hoa-sortable-blocks-container').empty();
            blockOrder.forEach(function(blockType) {
                if (blockType) {
                    addBlock(blockType);
                }
            });
            // Ensure the sortable is reinitialized
            initSortable();
        }
    });
})(jQuery);