/**
 * ASA AI Sales Agent Admin JavaScript
 * 
 * Handles admin interface functionality including:
 * - Settings tabs management
 * - Color contrast validation
 * - AJAX form submission and API testing
 * - Icon picker interface
 * - Real-time feedback and notifications
 * 
 * @since 1.0.0
 * @package ASA_AI_Sales_Agent
 */
jQuery(document).ready(function($) {
    'use strict';

    // =============================================================================
    // 1. TABS MANAGEMENT
    // =============================================================================
    
    /**
     * Initialize settings tabs
     * Ensures the correct tab is visible on page load and handles tab switching
     */
    const initialActiveTab = $('.asa-tabs .nav-tab-active').attr('href');
    if (initialActiveTab) {
        $('.asa-tab-content').hide();
        $(initialActiveTab).show();
    }

    /**
     * Handle tab click events
     * Switches between General, Appearance, and Behavior settings tabs
     */
    $('.asa-tabs a').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');

        // Don't do anything if this tab is already active
        if ($(this).hasClass('nav-tab-active')) {
            return;
        }

        // Update active tab styling
        $('.asa-tabs a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Show target tab content, hide others
        $('.asa-tab-content').hide();
        $(target).show();
    });

    // --- 2. Native Color Picker ---
    // No initialization needed for <input type="color"> elements

    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    function luminance(r, g, b) {
        const a = [r, g, b].map(v => {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
    }

    function contrast(rgb1, rgb2) {
        const lum1 = luminance(rgb1.r, rgb1.g, rgb1.b) + 0.05;
        const lum2 = luminance(rgb2.r, rgb2.g, rgb2.b) + 0.05;
        return lum1 > lum2 ? lum1 / lum2 : lum2 / lum1;
    }

    function checkContrast() {
        const color = $('#asa_primary_color').val();
        const rgb = hexToRgb(color);
        if (!rgb) return;
        const ratio = contrast(rgb, { r: 255, g: 255, b: 255 });
        const warning = $('#asa-color-contrast-warning');
        if (ratio < 4.5) {
            warning.text('Low contrast with white text');
            warning.show();
        } else {
            warning.hide();
        }
    }

    function showNotice(type, message) {
        if (window.wp && wp.data && wp.data.dispatch) {
            wp.data.dispatch('core/notices').createNotice(type, message, { isDismissible: true });
        } else {
            const notice = $('<div>').addClass('notice notice-' + type).text(message);
            $('.wrap').prepend(notice);
            setTimeout(() => notice.fadeOut(300, () => notice.remove()), 5000);
        }
    }

    $('#asa_primary_color').on('change input', checkContrast);
    checkContrast();

    // --- 3. AJAX SAVE SETTINGS ---
    $('#asa-settings-form').on('submit', function(e) { // Düzeltme: Spesifik form ID'si kullanıldı.
        e.preventDefault();
        const submitButton = $('#submit');
        const originalText = submitButton.val();

        submitButton.val(asaaisaaAdminSettings.savingText).prop('disabled', true).removeClass('asa-success asa-error').addClass('asa-saving');

        const formData = $(this).serialize(); // Düzeltme: FormData yerine .serialize() kullanıldı.

        $.ajax({
            url: asaaisaaAdminSettings.ajaxUrl,
            type: 'POST',
            data: formData + '&action=asaaisaa_save_settings&security=' + asaaisaaAdminSettings.nonce + '&_cache_bust=' + Date.now(),
            cache: false, // Prevent jQuery from caching
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            },
            success: function(response) {
                if (response.success) {
                    submitButton.val(asaaisaaAdminSettings.savedText).removeClass('asa-saving').addClass('asa-success');
                } else {
                    submitButton.val(asaaisaaAdminSettings.errorText).removeClass('asa-saving').addClass('asa-error');
                    showNotice('error', 'Error: ' + (response.data.message || 'Unknown error occurred.'));
                }
            },
            error: function(jqXHR) {
                submitButton.val(asaaisaaAdminSettings.errorText).removeClass('asa-saving').addClass('asa-error');
                showNotice('error', asaaisaaAdminSettings.ajaxErrorText + jqXHR.statusText);
            },
            complete: function() {
                setTimeout(function() {
                    submitButton.val(originalText).prop('disabled', false).removeClass('asa-success asa-error asa-saving');
                }, 2500);
            }
        });
    });

    // --- 4. TEST API KEY ---
    $('#asa-test-api-key').on('click', function(e) {
        e.preventDefault();
        const statusEl = $('#asa-api-key-test-status');
        statusEl.text(asaaisaaAdminSettings.testingText).removeClass('success error');
        $.ajax({
            url: asaaisaaAdminSettings.ajaxUrl,
            type: 'POST',
            data: {
                action: 'asaaisaa_test_api_key',
                security: asaaisaaAdminSettings.nonce,
                apiKey: $('#asa_api_key').val(),
                _cache_bust: Date.now()
            },
            cache: false,
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        }).done(function(res) {
            if (res.success) {
                statusEl.text(asaaisaaAdminSettings.testSuccessText).addClass('success');
            } else {
                statusEl.text(asaaisaaAdminSettings.testErrorText).addClass('error');
            }
        }).fail(function(jqXHR) {
            statusEl.text(asaaisaaAdminSettings.ajaxErrorText + jqXHR.statusText).addClass('error');
        });
    });
    
    

    // --- 5. ICON PICKER ---
    const modal = $('#asa-icon-picker-modal');
    const iconList = $('.asa-icon-list');
    
    const faIcons = ["fas fa-robot", "fas fa-comment-dots", "fas fa-user-astronaut", "fas fa-headset", "fas fa-life-ring", "fas fa-question-circle", "fas fa-cogs", "fas fa-paper-plane", "fas fa-lightbulb", "fas fa-rocket", "fas fa-user-circle", "fas fa-comment", "fas fa-comments", "fas fa-smile", "fas fa-store", "fas fa-shopping-cart", "fas fa-book", "fas fa-brain", "fas fa-briefcase", "fas fa-bullhorn", "fas fa-chart-bar", "fas fa-check-circle", "fas fa-clipboard-list", "fas fa-coffee", "fas fa-concierge-bell", "fas fa-credit-card", "fas fa-desktop", "fas fa-envelope", "fas fa-gift", "fas fa-globe", "fas fa-graduation-cap", "fas fa-heart", "fas fa-home", "fas fa-info-circle", "fas fa-key", "fas fa-mobile-alt", "fas fa-money-bill-wave", "fas fa-paint-brush", "fas fa-phone", "fas fa-plug", "fas fa-plus-circle", "fas fa-search", "fas fa-shield-alt", "fas fa-star", "fas fa-sync-alt", "fas fa-thumbs-up", "fas fa-tools", "fas fa-trash", "fas fa-trophy", "fas fa-user-friends", "fas fa-video"];

    function renderIcons(icons) {
        iconList.empty();
        icons.forEach(iconClass => {
            iconList.append($('<i>').addClass(iconClass).attr('data-icon', iconClass));
        });
    }

    $('#asa-open-icon-picker').on('click', function() {
        if (iconList.children().length === 0) {
            renderIcons(faIcons);
        }
        modal.css('display', 'flex');
    });

    $('.asa-icon-picker-close, .asa-icon-picker-modal').on('click', function(e) {
        if (e.target === this) {
            modal.hide();
        }
    });

    iconList.on('click', 'i', function() {
        const selectedIcon = $(this).data('icon');
        $('#asa_avatar_icon').val(selectedIcon);
        $('.asa-icon-preview i').attr('class', selectedIcon);
        modal.hide();
    });

});
