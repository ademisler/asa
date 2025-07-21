(function($){
    $(function(){
        let chatbot = $('#asa-chatbot');
        let launcher = chatbot.find('.asa-launcher');
        let windowEl = chatbot.find('.asa-window');
        let messagesEl = chatbot.find('.asa-messages');
        let typingEl = chatbot.find('.asa-typing');
        let inputEl = chatbot.find('.asa-text');
        let sendBtn = chatbot.find('.asa-send');
        let clearBtn = chatbot.find('.asa-clear-input');
        let welcomeEl = chatbot.find('.asa-proactive-message');
        let welcomeWrapper = chatbot.find('.asa-welcome-wrapper');
        let proactiveCloseBtn = chatbot.find('.asa-proactive-close');
        let history = JSON.parse(localStorage.getItem('asa_chat_history')) || []; // Load history from local storage
        let currentPageContent = document.body.innerText; // Get all visible text from the page

        // Render existing messages from history
        history.forEach(msg => {
            addMessage(msg.role, msg.parts[0].text);
        });

        welcomeEl.text(asaSettings.proactiveMessage);

        // Auto-hide proactive message after 10 seconds
        setTimeout(function(){
            welcomeWrapper.removeClass('active');
        }, 10000);

        proactiveCloseBtn.on('click', function(){
            welcomeWrapper.removeClass('active');
        });

        launcher.on('click', function(){
            chatbot.toggleClass('asa-open');
            windowEl.slideToggle(150);
            welcomeWrapper.removeClass('active'); // Hide proactive message when chat opens
        });

        welcomeWrapper.on('click', function(){
            welcomeWrapper.removeClass('active');
            launcher.trigger('click'); // Open chat when proactive message is clicked
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

        // Toggle clear button visibility
        inputEl.on('keyup change', function(){
            if ($(this).val().length > 0) {
                clearBtn.show();
            } else {
                clearBtn.hide();
            }
        });

        // Clear input on button click
        clearBtn.on('click', function(){
            inputEl.val('').trigger('change'); // Trigger change to hide button if empty
            inputEl.focus();
        });

        function sendMessage(){
            let text = inputEl.val().trim();
            if(!text) return;
            addMessage('user', text);
            inputEl.val('');
            typingEl.show();
            inputEl.prop('disabled', true);
            sendBtn.prop('disabled', true);
            clearBtn.hide(); // Hide clear button when sending
            $.ajax({
                type: 'POST',
                url: asaSettings.ajaxUrl,
                data: { 
                    action: 'asa_chat', 
                    message: text,
                    history: JSON.stringify(history),
                    security: asaSettings.nonce,
                    currentPageUrl: asaSettings.currentPageUrl,
                    currentPageTitle: asaSettings.currentPageTitle,
                    currentPageContent: currentPageContent
                },
                dataType: 'json'
            }).done(function(res){
                typingEl.hide();
                if(res.success){
                    addMessage('model', res.data);
                } else {
                    addMessage('bot', 'Üzgünüm, bir hata oluştu: ' + (res.data.message || 'Yanıt alınamadı.'));
                }
                inputEl.prop('disabled', false);
                sendBtn.prop('disabled', false);
                inputEl.focus();
                if (inputEl.val().length > 0) clearBtn.show(); // Show clear button if input has text
            }).fail(function(jqXHR, textStatus, errorThrown){
                typingEl.hide();
                addMessage('bot','Üzgünüm, sunucuyla iletişim kurulamadı. Lütfen daha sonra tekrar deneyin.');
                inputEl.prop('disabled', false);
                sendBtn.prop('disabled', false);
                inputEl.focus();
                if (inputEl.val().length > 0) clearBtn.show(); // Show clear button if input has text
            });
        }

        function addMessage(sender, text){
            history.push({role: sender === 'user' ? 'user' : 'model', parts: [{text: text}]});
            localStorage.setItem('asa_chat_history', JSON.stringify(history)); // Save history to local storage
            let wrapper = $('<div>').addClass(sender);
            $('<div>').addClass('bubble').text(text).appendTo(wrapper);
            messagesEl.append(wrapper);
            messagesEl.scrollTop(messagesEl.prop('scrollHeight'));
        }
    });
})(jQuery);
