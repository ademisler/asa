(function($) {
    $(function() {
        const chatbot = $('#asa-chatbot');
        if (chatbot.length === 0) return;

        const launcher = chatbot.find('.asa-launcher');
        const windowEl = chatbot.find('.asa-window');
        const messagesEl = chatbot.find('.asa-messages');
        const typingEl = chatbot.find('.asa-typing');
        const inputEl = chatbot.find('.asa-text');
        const sendBtn = chatbot.find('.asa-send');
        const clearBtn = chatbot.find('.asa-clear-input');
        const welcomeWrapper = chatbot.find('.asa-welcome-wrapper');
        const proactiveCloseBtn = chatbot.find('.asa-proactive-close');
        const proactiveClosedKey = 'asa_proactive_closed';
        const proactiveDelay = parseInt(asaSettings.proactiveDelay, 10) || 3000;
        const clearHistoryBtn = chatbot.find('.asa-clear-history');

        let focusableEls = $();
        let firstFocusable;
        let lastFocusable;
        
        let history = [];
        const historyLimit = parseInt(asaSettings.historyLimit, 10) || 50;
        let proactiveMessageTimeout;
        let lastProactiveMessage = null; // Store the last proactive message

        try {
            const storedHistory = JSON.parse(localStorage.getItem('asa_chat_history'));
            if (Array.isArray(storedHistory)) {
                history = storedHistory.slice(-historyLimit);
                history.forEach(msg => {
                    renderMessage(msg.role, msg.parts[0].text);
                });
            }
        } catch (e) {
            console.error('Could not parse chat history:', e);
            localStorage.removeItem('asa_chat_history');
        }

        const mainContent = document.querySelector('.entry-content, .post-content, article, main, body');
        const currentPageContent = mainContent ? mainContent.innerText.replace(/\s\s+/g, ' ').trim().substring(0, 4000) : '';

        if (!asaSettings.hasApiKey) {
            inputEl.prop('disabled', true).attr('placeholder', asaSettings.apiKeyPlaceholder);
            sendBtn.prop('disabled', true);
        } else {
            fetchProactiveMessage();
        }

        function fetchProactiveMessage() {
            if (sessionStorage.getItem(proactiveClosedKey) === 'true') {
                return;
            }
            $.ajax({
                url: asaSettings.proactiveMessageAjaxUrl,
                type: 'POST',
                data: {
                    action: 'asa_generate_proactive_message',
                    security: asaSettings.nonce,
                    currentPageUrl: window.location.href,
                    currentPageTitle: document.title,
                    currentPageContent: currentPageContent
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const proactiveText = response.data;

                        // If chat is already open, add the message directly and stop.
                        if (chatbot.hasClass('asa-open')) {
                            updateHistoryAndRender('model', proactiveText);
                            return;
                        }

                        // Otherwise, prepare the external bubble for later.
                        lastProactiveMessage = proactiveText;
                        setTimeout(() => {
                            welcomeWrapper.find('.asa-proactive-message').text(proactiveText);
                            welcomeWrapper.addClass('active');
                            proactiveMessageTimeout = setTimeout(() => hideProactiveMessage(), 15000);
                        }, proactiveDelay);
                    }
                }
            });
        }
        
        function hideProactiveMessage(permanent = false) {
            clearTimeout(proactiveMessageTimeout);
            welcomeWrapper.removeClass('active');
            if (permanent) {
                sessionStorage.setItem(proactiveClosedKey, 'true');
            }
        }

        proactiveCloseBtn.on('click', (e) => {
            e.stopPropagation();
            hideProactiveMessage(true);
        });

        function updateFocusable() {
            focusableEls = windowEl.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(':visible');
            firstFocusable = focusableEls.first();
            lastFocusable = focusableEls.last();
        }

        function openChatWindow() {
            hideProactiveMessage();
            chatbot.addClass('asa-open');
            windowEl.slideDown(150, () => {
                updateFocusable();
                inputEl.focus();
                launcher.attr('aria-expanded', 'true');

                // Add proactive message to chat if it exists and hasn't been added
                if (lastProactiveMessage) {
                    // Check if the last message in history is not the proactive one
                    const lastMessage = history.length > 0 ? history[history.length - 1] : null;
                    if (!lastMessage || lastMessage.parts[0].text !== lastProactiveMessage) {
                         updateHistoryAndRender('model', lastProactiveMessage);
                    }
                    lastProactiveMessage = null; // Clear after adding
                }
            });
        }

        launcher.on('click', function() {
            const isOpen = chatbot.hasClass('asa-open');
            if (isOpen) {
                windowEl.slideUp(150, () => {
                    chatbot.removeClass('asa-open');
                    launcher.attr('aria-expanded', 'false');
                });
            } else {
                openChatWindow();
            }
        });

        launcher.on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).trigger('click');
            }
        });

        windowEl.on('keydown', function(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                launcher.trigger('click');
                return;
            }
            if (e.key === 'Tab') {
                updateFocusable();
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable[0]) {
                        e.preventDefault();
                        lastFocusable.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusable[0]) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                }
            }
        });

        welcomeWrapper.on('click', function() {
            if (!chatbot.hasClass('asa-open')) {
                openChatWindow();
            }
        });

        chatbot.find('.asa-close').on('click', () => launcher.trigger('click'));

        clearHistoryBtn.on('click', function() {
            if (confirm(asaSettings.clearHistoryConfirm)) {
                history = [];
                localStorage.removeItem('asa_chat_history');
                messagesEl.empty();
            }
        });

        sendBtn.on('click', sendMessage);
        inputEl.on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        inputEl.on('input', function() {
            clearBtn.toggle($(this).val().length > 0);
        });

        clearBtn.on('click', function() {
            inputEl.val('').focus().trigger('input');
        });

        function sendMessage() {
            const text = inputEl.val().trim();
            if (!text) return;

            updateHistoryAndRender('user', text);
            inputEl.val('').prop('disabled', true).trigger('input');
            sendBtn.prop('disabled', true);
            typingEl.show();
            messagesEl.scrollTop(messagesEl.prop('scrollHeight'));
            
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
            }).done(function(res) {
                if (res.success && res.data) {
                    updateHistoryAndRender('model', res.data);
                } else {
                    renderMessage('bot', asaSettings.errorMessage + (res.data.message || asaSettings.noResponseText));
                }
            }).fail(function() {
                renderMessage('bot', asaSettings.serverErrorText);
            }).always(function() {
                typingEl.hide();
                inputEl.prop('disabled', false);
                sendBtn.prop('disabled', false);
            });
        }
        
        function renderMessage(sender, text) {
            const wrapper = $('<div>').addClass(sender === 'model' ? 'bot' : sender);
            const bubble = $('<div>').addClass('bubble');
            
            if (sender === 'bot' || sender === 'model') {
                const converter = new showdown.Converter({
                    simplifiedAutoLink: true,
                    strikethrough: true,
                    tables: true
                });
                const dirty = converter.makeHtml(text);
                const clean = DOMPurify.sanitize(dirty);
                bubble.html(clean);
            } else {
                bubble.text(text);
            }
            messagesEl.append(wrapper.append(bubble));
            messagesEl.scrollTop(messagesEl.prop('scrollHeight'));
        }

        function updateHistoryAndRender(sender, text) {
            history.push({ role: sender, parts: [{ text: text }] });
            if (history.length > historyLimit) {
                history = history.slice(-historyLimit);
            }
            localStorage.setItem('asa_chat_history', JSON.stringify(history));
            renderMessage(sender, text);
        }
    });
})(jQuery);
