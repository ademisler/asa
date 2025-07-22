jQuery(document).ready(function($) {

    // --- 1. TABS MANAGEMENT ---
    // Sayfa yüklendiğinde doğru sekmenin görünür olduğundan emin ol
    const initialActiveTab = $('.asa-tabs .nav-tab-active').attr('href');
    if (initialActiveTab) {
        $('.asa-tab-content').hide();
        $(initialActiveTab).show();
    }

    // Sekmelere tıklama olayı
    $('.asa-tabs a').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');

        if ($(this).hasClass('nav-tab-active')) {
            return; // Zaten aktifse işlem yapma
        }

        $('.asa-tabs a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.asa-tab-content').hide();
        $(target).show();
    });

    // --- 2. WORDPRESS COLOR PICKER ---
    if ($.fn.wpColorPicker) {
        $('.asa-color-field').wpColorPicker({
            change: checkContrast,
            clear: checkContrast
        });
    }

    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) {
            hex = hex.split('').map(c => c + c).join('');
        }
        const bigint = parseInt(hex, 16);
        return {
            r: (bigint >> 16) & 255,
            g: (bigint >> 8) & 255,
            b: bigint & 255
        };
    }

    function luminance(c) {
        const a = [c.r, c.g, c.b].map(v => {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
    }

    function contrast(rgb1, rgb2) {
        const l1 = luminance(rgb1) + 0.05;
        const l2 = luminance(rgb2) + 0.05;
        return l1 > l2 ? l1 / l2 : l2 / l1;
    }

    function checkContrast() {
        const color = $('#asa_primary_color').val() || '#333333';
        const ratio = contrast(hexToRgb(color), { r: 255, g: 255, b: 255 });
        const warning = $('#asa-contrast-warning');
        if (ratio < 4.5) {
            warning.text(asaAdminSettings.contrastWarningText).show();
        } else {
            warning.hide();
        }
    }

    checkContrast();

    // --- 3. AJAX SAVE SETTINGS ---
    $('#asa-settings-form').on('submit', function(e) { // Düzeltme: Spesifik form ID'si kullanıldı.
        e.preventDefault();
        const submitButton = $('#submit');
        const originalText = submitButton.val();

        submitButton.val(asaAdminSettings.savingText).prop('disabled', true).removeClass('asa-success asa-error').addClass('asa-saving');

        const formData = $(this).serialize(); // Düzeltme: FormData yerine .serialize() kullanıldı.

        $.ajax({
            url: asaAdminSettings.ajaxUrl,
            type: 'POST',
            data: formData + '&action=asa_save_settings&security=' + asaAdminSettings.nonce,
            success: function(response) {
                if (response.success) {
                    submitButton.val(asaAdminSettings.savedText).removeClass('asa-saving').addClass('asa-success');
                } else {
                    submitButton.val(asaAdminSettings.errorText).removeClass('asa-saving').addClass('asa-error');
                    alert('Error: ' + (response.data.message || 'Unknown error occurred.'));
                }
            },
            error: function(jqXHR) {
                submitButton.val(asaAdminSettings.errorText).removeClass('asa-saving').addClass('asa-error');
                alert(asaAdminSettings.ajaxErrorText + jqXHR.statusText);
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
        statusEl.text(asaAdminSettings.testingText).removeClass('success error');
        $.post(asaAdminSettings.ajaxUrl, {
            action: 'asa_test_api_key',
            security: asaAdminSettings.nonce,
            apiKey: $('#asa_api_key').val()
        }).done(function(res) {
            if (res.success) {
                statusEl.text(asaAdminSettings.testSuccessText).addClass('success');
            } else {
                statusEl.text(asaAdminSettings.testErrorText).addClass('error');
            }
        }).fail(function(jqXHR) {
            statusEl.text(asaAdminSettings.ajaxErrorText + jqXHR.statusText).addClass('error');
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