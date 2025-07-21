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
        let clearHistoryBtn = chatbot.find('.asa-clear-history');
        let history = JSON.parse(localStorage.getItem('asa_chat_history')) || []; // Load history from local storage
        
        // Smarter content extraction and cleaning
        let mainContent = document.querySelector('.entry-content') || document.querySelector('.post-content') || document.querySelector('article') || document.body;
        let rawText = mainContent.innerText;
        let currentPageContent = rawText.replace(/\s\s+/g, ' ').trim(); // Remove extra whitespace and trim

        // Render existing messages from history
        history.forEach(msg => {
            addMessage(msg.role, msg.parts[0].text);
        });

        // Fetch proactive message asynchronously
        $.ajax({
            url: asaSettings.proactiveMessageAjaxUrl,
            type: 'POST',
            data: {
                action: 'asa_generate_proactive_message',
                security: asaSettings.nonce, // Add nonce for security
                currentPageUrl: window.location.href, // Use live URL
                currentPageTitle: document.title, // Use live title
                currentPageContent: currentPageContent
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    // Add a random delay to simulate human behavior
                    const delay = Math.floor(Math.random() * 2000) + 2000; // Delay between 2-4 seconds
                    setTimeout(function() {
                        welcomeEl.text(response.data);
                        welcomeWrapper.addClass('active');
                    }, delay);
                }
                // If not successful, do nothing. The bubble remains hidden.
            },
            error: function() {
                // On error, do nothing. The bubble remains hidden.
            }
        });

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

        clearHistoryBtn.on('click', function(){
            history = [];
            localStorage.removeItem('asa_chat_history');
            messagesEl.empty();
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
                    addMessage('bot', 'Sorry, an error occurred: ' + (res.data.message || 'No response received.'));
                }
                inputEl.prop('disabled', false);
                sendBtn.prop('disabled', false);
                inputEl.focus();
                if (inputEl.val().length > 0) clearBtn.show(); // Show clear button if input has text
            }).fail(function(jqXHR, textStatus, errorThrown){
                typingEl.hide();
                addMessage('bot','Sorry, could not communicate with the server. Please try again later.');
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
            let bubble = $('<div>').addClass('bubble');
            if (sender === 'bot' || sender === 'model') {
                let converter = new showdown.Converter();
                let html = converter.makeHtml(text);
                bubble.html(html);
            } else {
                bubble.text(text);
            }
            bubble.appendTo(wrapper);
            messagesEl.append(wrapper);
            messagesEl.scrollTop(messagesEl.prop('scrollHeight'));
        }
    });
})(jQuery);