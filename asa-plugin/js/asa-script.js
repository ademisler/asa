(function($){
    let chatbot = $('#asa-chatbot');
    let launcher = chatbot.find('.asa-launcher');
    let windowEl = chatbot.find('.asa-window');
    let messagesEl = chatbot.find('.asa-messages');
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

    function sendMessage(){
        let text = inputEl.val();
        if(!text) return;
        addMessage('user', text);
        inputEl.val('');
        addMessage('bot', '...');
        $.post(asaSettings.ajaxUrl, {
            action: 'asa_chat',
            message: text
        }, function(res){
            messagesEl.find('.bot:last').text(res.data || 'Error');
        });
    }

    function addMessage(sender, text){
        let msg = $('<div>').addClass(sender).text(text);
        messagesEl.append(msg);
        messagesEl.scrollTop(messagesEl.prop('scrollHeight'));
    }
})(jQuery);
