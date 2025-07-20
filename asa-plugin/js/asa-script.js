(function($){
    let chatbot = $('#asa-chatbot');
    let launcher = chatbot.find('.asa-launcher');
    let windowEl = chatbot.find('.asa-window');
    let messagesEl = chatbot.find('.asa-messages');
    let typingEl = chatbot.find('.asa-typing');
    let inputEl = chatbot.find('.asa-text');
    let sendBtn = chatbot.find('.asa-send');
    let welcomeEl = chatbot.find('.asa-welcome');

    welcomeEl.text(asaSettings.proactiveMessage);

    launcher.on('click', function(){
        windowEl.toggle();
    });

    chatbot.find('.asa-close').on('click', function(){
        windowEl.hide();
    });

    sendBtn.on('click', function(){
        sendMessage();
    });

    inputEl.on('keydown', function(e){
        if(e.key === 'Enter') {
            sendMessage();
        }
    });

    if(!asaSettings.apiKey){
        inputEl.prop('disabled', true).attr('placeholder','Configure API key in settings');
        sendBtn.prop('disabled', true);
    }

    function sendMessage(){
        let text = inputEl.val();
        if(!text) return;
        addMessage('user', text);
        inputEl.val('');
        typingEl.show();
        $.post(asaSettings.ajaxUrl, {
            action: 'asa_chat',
            message: text
        }, function(res){
            typingEl.hide();
            addMessage('bot', res.success ? res.data : 'Error');
        });
    }

    function addMessage(sender, text){
        let wrapper = $('<div>').addClass(sender);
        $('<div>').addClass('bubble').text(text).appendTo(wrapper);
        messagesEl.append(wrapper);
        messagesEl.scrollTop(messagesEl.prop('scrollHeight'));
    }
})(jQuery);
