(function($){
    $(function(){
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
            chatbot.toggleClass('asa-open');
            windowEl.slideToggle(150);
        });

        chatbot.find('.asa-close').on('click', function(){
            windowEl.slideUp(150, function(){
                chatbot.removeClass('asa-open');
            });
        });

        sendBtn.on('click', function(){
            sendMessage();
        });

        inputEl.on('keydown', function(e){
            if(e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        if(!asaSettings.apiKey){
            inputEl.prop('disabled', true).attr('placeholder','Configure API key in settings');
            sendBtn.prop('disabled', true);
        }

        function sendMessage(){
            let text = inputEl.val().trim();
            if(!text) return;
            addMessage('user', text);
            inputEl.val('');
            typingEl.show();
            $.ajax({
                type: 'POST',
                url: asaSettings.ajaxUrl,
                data: { action: 'asa_chat', message: text },
                dataType: 'json'
            }).done(function(res){
                typingEl.hide();
                if(res.success){
                    addMessage('bot', res.data);
                } else {
                    addMessage('bot', 'Error: ' + (res.data || 'Unable to get response'));
                }
            }).fail(function(){
                typingEl.hide();
                addMessage('bot','Error');
            });
        }

        function addMessage(sender, text){
            let wrapper = $('<div>').addClass(sender);
            $('<div>').addClass('bubble').text(text).appendTo(wrapper);
            messagesEl.append(wrapper);
            messagesEl.scrollTop(messagesEl.prop('scrollHeight'));
        }
    });
})(jQuery);
