(function($) {
    $(function() {
        const chatbot = $('#asa-chatbot');
        if (chatbot.length === 0) return; // Chatbot yoksa devam etme

        const launcher = chatbot.find('.asa-launcher');
        const windowEl = chatbot.find('.asa-window');
        const messagesEl = chatbot.find('.asa-messages');
        const typingEl = chatbot.find('.asa-typing');
        const inputEl = chatbot.find('.asa-text');
        const sendBtn = chatbot.find('.asa-send');
        const clearBtn = chatbot.find('.asa-clear-input');
        const welcomeWrapper = chatbot.find('.asa-welcome-wrapper');
        const proactiveCloseBtn = chatbot.find('.asa-proactive-close');
        const clearHistoryBtn = chatbot.find('.asa-clear-history');
        
        let history = [];
        let proactiveMessageTimeout;

        // Geçmişi yükle ve render et
        try {
            const storedHistory = JSON.parse(localStorage.getItem('asa_chat_history'));
            if (Array.isArray(storedHistory)) {
                history = storedHistory;
                history.forEach(msg => {
                    renderMessage(msg.role, msg.parts[0].text); // Sadece render et, geçmişe tekrar ekleme
                });
            }
        } catch (e) {
            console.error('Could not parse chat history:', e);
            localStorage.removeItem('asa_chat_history');
        }

        // Akıllı içerik çıkarma
        const mainContent = document.querySelector('.entry-content, .post-content, article, main, body');
        const currentPageContent = mainContent ? mainContent.innerText.replace(/\s\s+/g, ' ').trim().substring(0, 4000) : '';

        // API anahtarı yoksa erken çık
        if (!asaSettings.hasApiKey) {
            inputEl.prop('disabled', true).attr('placeholder', asaSettings.apiKeyPlaceholder);
            sendBtn.prop('disabled', true);
        } else {
             // Sadece API anahtarı varsa proaktif mesajı getir
            fetchProactiveMessage();
        }

        function fetchProactiveMessage() {
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
                        const delay = Math.floor(Math.random() * 2000) + 2000;
                        setTimeout(() => {
                            welcomeWrapper.find('.asa-proactive-message').text(response.data);
                            welcomeWrapper.addClass('active');
                            // İyileştirme: 15 saniye sonra gizle
                            proactiveMessageTimeout = setTimeout(() => welcomeWrapper.removeClass('active'), 15000);
                        }, delay);
                    }
                }
            });
        }
        
        function hideProactiveMessage() {
            clearTimeout(proactiveMessageTimeout);
            welcomeWrapper.removeClass('active');
        }

        proactiveCloseBtn.on('click', (e) => {
            e.stopPropagation();
            hideProactiveMessage();
        });

        launcher.on('click', function() {
            const isOpen = chatbot.hasClass('asa-open');
            if (isOpen) {
                windowEl.slideUp(150, () => chatbot.removeClass('asa-open'));
            } else {
                hideProactiveMessage();
                chatbot.addClass('asa-open');
                windowEl.slideDown(150, () => inputEl.focus());
            }
        });

        welcomeWrapper.on('click', function() {
            if (!chatbot.hasClass('asa-open')) {
                launcher.trigger('click');
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
                    // Hatalı yanıtları geçmişe kaydetme, sadece göster
                    renderMessage('bot', asaSettings.errorMessage + (res.data.message || asaSettings.noResponseText));
                }
            }).fail(function() {
                // Sunucu hatalarını geçmişe kaydetme, sadece göster
                renderMessage('bot', asaSettings.serverErrorText);
            }).always(function() {
                typingEl.hide();
                inputEl.prop('disabled', false).focus();
                sendBtn.prop('disabled', false);
            });
        }
        
        // İyileştirme: Sadece render eden ve geçmişi GÜNCELLEMEYEN fonksiyon
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

        // İyileştirme: Hem geçmişi güncelleyen hem de render eden fonksiyon
        function updateHistoryAndRender(sender, text) {
            history.push({ role: sender, parts: [{ text: text }] });
            localStorage.setItem('asa_chat_history', JSON.stringify(history));
            renderMessage(sender, text);
        }
    });
})(jQuery);