/**
 * ASA AI Sales Agent Frontend JavaScript
 * 
 * Handles all frontend chatbot functionality including:
 * - Chat window management and user interactions
 * - Proactive messaging based on page content
 * - Chat history management with localStorage
 * - AJAX communication with WordPress backend
 * - Accessibility features and keyboard navigation
 * 
 * @since 1.0.0
 * @package ASA_AI_Sales_Agent
 */
(function($) {
    'use strict';
    
    // Initialize when DOM is ready
    $(function() {
        // Get main chatbot container - exit if not found
        const chatbot = $('#asa-chatbot');
        if (chatbot.length === 0) return;

        // Cache DOM elements for better performance
        const launcher = chatbot.find('.asa-launcher');              // Chat launcher button
        const windowEl = chatbot.find('.asa-window');                // Main chat window
        const messagesEl = chatbot.find('.asa-messages');            // Messages container
        const typingEl = chatbot.find('.asa-typing');                // Typing indicator
        const inputEl = chatbot.find('.asa-text');                   // Message input field
        const sendBtn = chatbot.find('.asa-send');                   // Send message button
        const clearBtn = chatbot.find('.asa-clear-input');           // Clear input button
        const welcomeWrapper = chatbot.find('.asa-welcome-wrapper'); // Proactive message wrapper
        const proactiveCloseBtn = chatbot.find('.asa-proactive-close'); // Close proactive message
        const clearHistoryBtn = chatbot.find('.asa-clear-history');  // Clear chat history button

        // Configuration constants
        const proactiveClosedKey = 'asa_proactive_closed';           // localStorage key for dismissed proactive messages
        const proactiveDelay = parseInt(asaSettings.proactiveDelay, 10) || 3000; // Delay before showing proactive message
        const historyLimit = parseInt(asaSettings.historyLimit, 10) || 50;       // Maximum chat history entries

        // Accessibility variables for focus management
        let focusableEls = $();    // All focusable elements in chat window
        let firstFocusable;        // First focusable element
        let lastFocusable;         // Last focusable element
        
        // Chat functionality variables
        let history = [];                    // Chat history array
        let proactiveMessageTimeout;         // Timeout for proactive message display
        let lastProactiveMessage = null;     // Store the last generated proactive message

        // Load and restore chat history from localStorage
        try {
            const storedHistory = JSON.parse(localStorage.getItem('asa_chat_history'));
            if (Array.isArray(storedHistory)) {
                // Limit history to prevent localStorage bloat
                history = storedHistory.slice(-historyLimit);
                // Restore messages to chat window
                history.forEach(msg => {
                    renderMessage(msg.role, msg.parts[0].text);
                });
            }
        } catch (e) {
            // Handle corrupted localStorage data gracefully
            console.error('Could not parse chat history:', e);
            localStorage.removeItem('asa_chat_history');
        }

        // Extract page content for contextual AI responses
        // Look for common content containers in WordPress themes
        let currentPageContent = '';
        
        // Function to get page content
        function getPageContent() {
            const selectors = ['.entry-content', '.post-content', 'article', 'main', '.content', '#content', '.site-content', 'body'];
            let content = '';
            
            for (const selector of selectors) {
                const element = document.querySelector(selector);
                if (element && element.innerText.trim().length > 100) {
                    content = element.innerText.replace(/\s\s+/g, ' ').trim().substring(0, 4000);
                    break;
                }
            }
            
            // If no substantial content found, wait a bit more and try again
            if (content.length < 100) {
                const bodyContent = document.body ? document.body.innerText.replace(/\s\s+/g, ' ').trim() : '';
                content = bodyContent.substring(0, 4000);
            }
            
            return content;
        }

        // Initialize chatbot based on API key availability
        if (!asaSettings.hasApiKey) {
            // Disable functionality if no API key is configured
            inputEl.prop('disabled', true).attr('placeholder', asaSettings.apiKeyPlaceholder);
            sendBtn.prop('disabled', true);
        } else {
            // Wait for page to be fully loaded before fetching proactive message
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    currentPageContent = getPageContent();
                    console.log('ASA: DOMContentLoaded - Content length:', currentPageContent.length);
                    // Small delay to ensure all content is rendered
                    setTimeout(() => {
                        currentPageContent = getPageContent(); // Get content again after delay
                        console.log('ASA: After delay - Content length:', currentPageContent.length);
                        fetchProactiveMessage();
                    }, 1000);
                });
            } else {
                // Page already loaded
                currentPageContent = getPageContent();
                console.log('ASA: Page already loaded - Content length:', currentPageContent.length);
                // Add small delay even for already loaded pages to ensure content is ready
                setTimeout(() => {
                    currentPageContent = getPageContent(); // Refresh content
                    console.log('ASA: After refresh - Content length:', currentPageContent.length);
                    fetchProactiveMessage();
                }, 500);
            }
        }

        function fetchProactiveMessage() {
            console.log('ASA: fetchProactiveMessage called for page:', window.location.pathname);
            
            // Check if proactive message was closed for this specific page
            const currentPageKey = proactiveClosedKey + '_' + window.location.pathname;
            if (sessionStorage.getItem(currentPageKey) === 'true') {
                console.log('ASA: Proactive message was closed for this page, skipping');
                return;
            }
            
            // Also check general proactive closed status (but only for current session)
            if (sessionStorage.getItem(proactiveClosedKey) === 'true') {
                // Reset general closed status if user navigated to a new page
                // This allows proactive messages on new pages even if closed on previous page
                const lastClosedPage = sessionStorage.getItem(proactiveClosedKey + '_lastPage');
                if (lastClosedPage !== window.location.pathname) {
                    console.log('ASA: Resetting proactive closed status for new page');
                    sessionStorage.removeItem(proactiveClosedKey);
                } else {
                    console.log('ASA: Proactive message was closed generally, skipping');
                    return;
                }
            }
            
            console.log('ASA: Making AJAX request for proactive message');
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
                    console.log('ASA: Proactive message AJAX response:', response);
                    if (response.success && response.data) {
                        const proactiveText = response.data;
                        console.log('ASA: Proactive message text:', proactiveText);

                        // If chat is already open, add the message directly and stop.
                        if (chatbot.hasClass('asa-open')) {
                            console.log('ASA: Chat is open, adding message directly');
                            updateHistoryAndRender('model', proactiveText);
                            return;
                        }

                        // Otherwise, prepare the external bubble for later.
                        lastProactiveMessage = proactiveText;
                        console.log('ASA: Setting proactive message with delay:', proactiveDelay + 'ms');
                        setTimeout(() => {
                            console.log('ASA: Showing proactive message bubble');
                            welcomeWrapper.find('.asa-proactive-message').text(proactiveText);
                            welcomeWrapper.addClass('active');
                            proactiveMessageTimeout = setTimeout(() => hideProactiveMessage(), 15000);
                        }, proactiveDelay);
                    } else {
                        console.log('ASA: No proactive message generated or error occurred');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('ASA: Proactive message AJAX error:', error);
                }
            });
        }
        
        function hideProactiveMessage(permanent = false) {
            clearTimeout(proactiveMessageTimeout);
            welcomeWrapper.removeClass('active');
            if (permanent) {
                // Store both general and page-specific closed status
                sessionStorage.setItem(proactiveClosedKey, 'true');
                sessionStorage.setItem(proactiveClosedKey + '_lastPage', window.location.pathname);
                
                // Also store page-specific closed status
                const currentPageKey = proactiveClosedKey + '_' + window.location.pathname;
                sessionStorage.setItem(currentPageKey, 'true');
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
