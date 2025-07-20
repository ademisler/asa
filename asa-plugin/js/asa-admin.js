jQuery(document).ready(function($){
    $('.asa-tabs a').on('click', function(e){
        e.preventDefault();
        var target = $(this).attr('href');
        $('.asa-tabs a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.asa-tab-content').hide();
        $(target).show();
    });

    if (typeof wp !== 'undefined' && wp.colorPicker){
        $('.asa-color-field').wpColorPicker();
    }

    var mediaUploader;
    $('#asa_avatar_upload').on('click', function(e){
        e.preventDefault();
        if (mediaUploader){
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media({
            title: 'Choose Avatar',
            button: { text: 'Choose Avatar' },
            multiple: false
        });
        mediaUploader.on('select', function(){
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#asa_avatar').val(attachment.url);
            $('#asa_avatar_preview').attr('src', attachment.url).show();
        });
        mediaUploader.open();
    });
});
