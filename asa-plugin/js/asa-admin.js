jQuery(document).ready(function($){
    // Set initial tab state on page load
    $('.asa-tab-content').hide();
    $('.asa-tab-content.active').show();

    // Tabs click handler
    $('.asa-tabs a').on('click', function(e){
        e.preventDefault();
        var target = $(this).attr('href');
        
        // Update active class on tabs
        $('.asa-tabs a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Show/hide tab content
        $('.asa-tab-content').hide();
        $(target).show();
    });

    // Color Picker
    if (typeof wp !== 'undefined' && wp.colorPicker){
        $('.asa-color-field').wpColorPicker();
    }

    // Save Settings Feedback
    $('form').on('submit', function(e){
        e.preventDefault();
        const submitButton = $('#submit');
        const originalText = submitButton.val();
        
        submitButton.val('Saving...');
        submitButton.prop('disabled', true).addClass('asa-saving');

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
                    submitButton.val('Saved!');
                    submitButton.removeClass('asa-saving').addClass('asa-success');
                } else {
                    submitButton.val('Error!');
                    submitButton.removeClass('asa-saving').addClass('asa-error');
                    console.error('Error saving settings:', response.data);
                }
                setTimeout(function(){
                    submitButton.val(originalText);
                    submitButton.prop('disabled', false).removeClass('asa-success asa-error');
                }, 2000);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                submitButton.val('Error!');
                submitButton.removeClass('asa-saving').addClass('asa-error');
                console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                setTimeout(function(){
                    submitButton.val(originalText);
                    submitButton.prop('disabled', false).removeClass('asa-success asa-error');
                }, 2000);
            }
        });
    });

    // Icon Picker
    const modal = $('#asa-icon-picker-modal');
    const iconList = $('.asa-icon-list');
    let allIcons = [];

    $('#asa-open-icon-picker').on('click', function(){
        modal.show();
        if (allIcons.length === 0) {
            fetchIcons();
        }
    });

    $('.asa-icon-picker-close').on('click', function(){ modal.hide(); });
    $(window).on('click', function(e){ if ($(e.target).is(modal)) { modal.hide(); } });

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
    });
});