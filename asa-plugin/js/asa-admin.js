jQuery(document).ready(function($){
    // Tabs
    $('.asa-tabs a').on('click', function(e){
        e.preventDefault();
        var target = $(this).attr('href');
        $('.asa-tabs a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.asa-tab-content').removeClass('active').hide();
        $(target).addClass('active').show();
    });

    // Color Picker
    if (typeof wp !== 'undefined' && wp.colorPicker){
        $('.asa-color-field').wpColorPicker({
            change: function(event, ui) {
                // Update live preview color
                $('#asa-chatbot').css('--asa-color', ui.color.toString());
            }
        });
    }

    // Save Settings Feedback
    $('form').on('submit', function(e){
        const submitButton = $('#submit');
        const originalText = submitButton.val();
        submitButton.val('Kaydediliyor...');
        submitButton.prop('disabled', true);
        submitButton.css({'background-color': '#ffc107', 'border-color': '#ffc107'}); // Indicate saving

        // Using AJAX for better feedback
        e.preventDefault(); // Prevent default form submission
        const formData = new FormData(this);
        formData.append('action', 'asa_save_settings');
        formData.append('security', asaAdminSettings.nonce);

        $.ajax({
            url: asaAdminSettings.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    submitButton.val('Kaydedildi!');
                    submitButton.css({'background-color': 'var(--asa-admin-success-color)', 'border-color': 'var(--asa-admin-success-color)'});
                } else {
                    submitButton.val('Hata!');
                    submitButton.css({'background-color': 'var(--asa-admin-error-color)', 'border-color': 'var(--asa-admin-error-color)'});
                    console.error('Error saving settings:', response.data);
                }
                setTimeout(function(){
                    submitButton.val(originalText);
                    submitButton.prop('disabled', false);
                    submitButton.css({'background-color': '', 'border-color': ''}); // Revert to original style
                }, 2000); // Show "Kaydedildi!" or "Hata!" for 2 seconds
            },
            error: function(jqXHR, textStatus, errorThrown) {
                submitButton.val('Hata!');
                submitButton.css({'background-color': 'var(--asa-admin-error-color)', 'border-color': 'var(--asa-admin-error-color)'});
                console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                setTimeout(function(){
                    submitButton.val(originalText);
                    submitButton.prop('disabled', false);
                    submitButton.css({'background-color': '', 'border-color': ''}); // Revert to original style
                }, 2000); // Show "Hata!" for 2 seconds
            }
        });
    });

    // Icon Picker
    const modal = $('#asa-icon-picker-modal');
    const iconList = $('.asa-icon-list');
    const searchInput = $('#asa-icon-search');
    let allIcons = [];

    $('#asa-open-icon-picker').on('click', function(){
        modal.show();
        if (allIcons.length === 0) {
            fetchIcons();
        }
    });

    $('.asa-icon-picker-close').on('click', function(){
        modal.hide();
    });

    $(window).on('click', function(e){
        if ($(e.target).is(modal)) {
            modal.hide();
        }
    });

    searchInput.on('keyup', function(){
        const searchTerm = $(this).val().toLowerCase();
        const filteredIcons = allIcons.filter(icon => icon.toLowerCase().includes(searchTerm));
        renderIcons(filteredIcons);
    });

    function fetchIcons() {
        const faIcons = ["fas fa-robot", "fas fa-comment-dots", "fas fa-user-astronaut", "fas fa-headset", "fas fa-life-ring", "fas fa-question-circle", "fas fa-cogs", "fas fa-paper-plane", "fas fa-lightbulb", "fas fa-rocket", "fas fa-user-circle", "fas fa-comment", "fas fa-comments", "fas fa-smile", "fas fa-store", "fas fa-shopping-cart", "fas fa-book", "fas fa-brain", "fas fa-briefcase", "fas fa-bullhorn", "fas fa-chart-bar", "fas fa-check-circle", "fas fa-clipboard-list", "fas fa-coffee", "fas fa-concierge-bell", "fas fa-credit-card", "fas fa-desktop", "fas fa-envelope", "fas fa-gift", "fas fa-globe", "fas fa-graduation-cap", "fas fa-heart", "fas fa-home", "fas fa-info-circle", "fas fa-key", "fas fa-mobile-alt", "fas fa-money-bill-wave", "fas fa-paint-brush", "fas fa-phone", "fas fa-plug", "fas fa-plus-circle", "fas fa-search", "fas fa-shield-alt", "fas fa-star", "fas fa-sync-alt", "fas fa-thumbs-up", "fas fa-tools", "fas fa-trash", "fas fa-trophy", "fas fa-user-friends", "fas fa-video"];
        allIcons = faIcons;
        renderIcons(allIcons);
    }

    function renderIcons(icons) {
        iconList.empty();
        icons.forEach(iconClass => {
            const iconElement = $('<i>').addClass(iconClass).attr('data-icon', iconClass);
            iconList.append(iconElement);
        });
    }

    iconList.on('click', 'i', function(){
        const selectedIcon = $(this).data('icon');
        $('#asa_avatar_icon').val(selectedIcon);
        $('.asa-icon-preview i').attr('class', selectedIcon);
        modal.hide();
        // Update live preview avatar
        $('#asa-chatbot .asa-launcher i').attr('class', selectedIcon + ' asa-avatar');
        $('#asa-chatbot .asa-header i').attr('class', selectedIcon + ' asa-avatar');
        $('#asa-chatbot .asa-launcher img').remove(); // Remove image if icon is selected
        $('#asa-chatbot .asa-header img').remove();
    });

    // Tooltip Logic
    $('.asa-tooltip-icon').on({
        mouseenter: function(){
            const tooltipText = $(this).data('tooltip');
            const tooltip = $('<div class="asa-tooltip-content"></div>').text(tooltipText);
            $(this).append(tooltip);
        },
        mouseleave: function(){
            $(this).find('.asa-tooltip-content').remove();
        }
    });

    // Live Preview Updates
    $('input[name="asa_title"]').on('keyup', function(){
        $('#asa-chatbot .asa-title').text($(this).val());
    });

    $('input[name="asa_subtitle"]').on('keyup', function(){
        $('#asa-chatbot .asa-subtitle').text($(this).val());
    });

    $('input[name="asa_avatar_image_url"]').on('keyup', function(){
        const imageUrl = $(this).val();
        if (imageUrl) {
            $('#asa-chatbot .asa-launcher').html('<img src="' + imageUrl + '" class="asa-avatar" />');
            $('#asa-chatbot .asa-header').find('.asa-avatar').remove(); // Remove existing icon/image
            $('#asa-chatbot .asa-header').prepend('<img src="' + imageUrl + '" class="asa-avatar" />');
        } else {
            // If image URL is cleared, revert to icon
            const currentIcon = $('#asa_avatar_icon').val();
            $('#asa-chatbot .asa-launcher').html('<i class="' + currentIcon + ' asa-avatar"></i>');
            $('#asa-chatbot .asa-header').find('.asa-avatar').remove();
            $('#asa-chatbot .asa-header').prepend('<i class="' + currentIcon + ' asa-avatar"></i>');
        }
    });

    $('input[name="asa_position"]').on('change', function(){
        $('#asa-chatbot').removeClass('asa-position-left asa-position-right').addClass('asa-position-' + $(this).val());
    });

    $('input[name="asa_show_credit"]').on('change', function(){
        if ($(this).is(':checked')) {
            $('#asa-chatbot .asa-credit').show();
        } else {
            $('#asa-chatbot .asa-credit').hide();
        }
    });

    // Initial live preview update on load
    $('input[name="asa_title"]').trigger('keyup');
    $('input[name="asa_subtitle"]').trigger('keyup');
    $('input[name="asa_avatar_image_url"]').trigger('keyup');
    $('input[name="asa_position"]').trigger('change');
    $('input[name="asa_show_credit"]').trigger('change');
});
