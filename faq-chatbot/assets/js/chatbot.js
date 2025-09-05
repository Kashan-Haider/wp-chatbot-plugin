/**
 * FAQ Chatbot JavaScript - Chat Interface
 */

(function() {
    'use strict';

    let chatbotData = {};
    let messageHistory = [];
    let widget = null;
    let messagesContainer = null;
    let isSticky = false;
    let stickyButton = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Check if we have the chatbot data
        if (typeof faqChatbot === 'undefined') {
            return;
        }

        chatbotData = faqChatbot.data;
        
        // Initialize sticky chatbot
        initializeStickyChat();
        
        // Initialize shortcode chatbot if present
        initializeShortcodeChat();
    });

    function initializeStickyChat() {
        widget = document.getElementById('faq-chatbot-widget');
        stickyButton = document.getElementById('faq-chatbot-button');
        
        if (!widget || !stickyButton) {
            return;
        }

        isSticky = true;
        messagesContainer = document.getElementById('chat-messages');
        
        setupStickyEventListeners();
        initializeChat();
    }

    function initializeShortcodeChat() {
        const shortcodeWidget = document.getElementById('faq-chatbot-widget-shortcode');
        if (!shortcodeWidget) {
            return;
        }

        // Hide sticky elements when shortcode is present
        document.body.classList.add('has-chatbot-shortcode');
        
        // Initialize shortcode version
        widget = shortcodeWidget;
        messagesContainer = document.getElementById('chat-messages-shortcode');
        isSticky = false;
        
        initializeChat();
        setupShortcodeEventListeners();
    }

    function setupStickyEventListeners() {
        // Toggle chatbot when button is clicked
        stickyButton.addEventListener('click', function() {
            toggleChatbot();
        });

        // Close chatbot when clicking outside (optional)
        document.addEventListener('click', function(e) {
            if (widget.style.display === 'block' && 
                !widget.contains(e.target) && 
                !stickyButton.contains(e.target)) {
                // Uncomment the line below if you want to close on outside click
                // hideChatbot();
            }
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && widget.style.display === 'block') {
                hideChatbot();
            }
        });

        // Prevent body scroll when chatbot is open
        widget.addEventListener('wheel', function(e) {
            e.stopPropagation();
        });

        // Setup restart button
        const restartBtn = document.getElementById('restart-chat');
        if (restartBtn) {
            restartBtn.addEventListener('click', restartChat);
        }
    }

    function setupShortcodeEventListeners() {
        const restartBtn = document.getElementById('restart-chat-shortcode');
        if (restartBtn) {
            restartBtn.addEventListener('click', restartChat);
        }
    }

    function showChatbot() {
        if (!widget) return;
        
        widget.style.display = 'block';
        stickyButton.style.display = 'none';
        
        // Focus management for accessibility
        const firstFocusable = widget.querySelector('button, input, select, textarea');
        if (firstFocusable) {
            firstFocusable.focus();
        }
        
        // Scroll to bottom if there are messages
        scrollToBottom();
    }

    function toggleChatbot() {
        if (!widget) return;
        
        if (widget.style.display === 'block') {
            hideChatbot();
        } else {
            showChatbot();
        }
    }

    function hideChatbot() {
        if (!widget) return;
        
        widget.style.display = 'none';
        stickyButton.style.display = 'flex';
        
        // Return focus to the button
        stickyButton.focus();
    }

    function initializeChat() {
        loadChatHistory();
        if (messageHistory.length === 0) {
            addBotMessage("Hello! I'm here to help you with our services. What would you like to know about?");
            showServiceOptions();
        } else {
            displayChatHistory();
        }
    }

    function setupEventListeners() {
        const restartBtn = document.getElementById('restart-chat');
        if (restartBtn) {
            restartBtn.addEventListener('click', restartChat);
        }
    }

    function addBotMessage(text, options = null) {
        const message = {
            type: 'bot',
            text: text,
            options: options,
            timestamp: Date.now()
        };
        messageHistory.push(message);
        displayMessage(message);
        saveChatHistory();
        scrollToBottom();
    }

    function addUserMessage(text) {
        const message = {
            type: 'user',
            text: text,
            timestamp: Date.now()
        };
        messageHistory.push(message);
        displayMessage(message);
        saveChatHistory();
        scrollToBottom();
    }

    function displayMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `faq-chatbot__message faq-chatbot__message--${message.type}`;
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'faq-chatbot__message-bubble';
        bubbleDiv.textContent = message.text;
        
        messageDiv.appendChild(bubbleDiv);
        messagesContainer.appendChild(messageDiv);

        if (message.options) {
            const optionsDiv = document.createElement('div');
            optionsDiv.className = 'faq-chatbot__options';
            
            message.options.forEach(function(option) {
                const btn = document.createElement('button');
                btn.className = 'faq-chatbot__option-btn';
                btn.textContent = option.text;
                btn.onclick = option.action;
                optionsDiv.appendChild(btn);
            });
            
            messagesContainer.appendChild(optionsDiv);
        }
        
        // Force scroll to bottom after adding message
        scrollToBottom();
    }

    function showServiceOptions() {
        const services = chatbotData.services || [];
        const options = services.map(function(service) {
            return {
                text: service.title,
                action: function() { showQuestions(service); }
            };
        });
        
        const lastMessage = messageHistory[messageHistory.length - 1];
        if (lastMessage) {
            lastMessage.options = options;
            // Re-render the last message with options
            messagesContainer.innerHTML = '';
            displayChatHistory();
        }
    }

    function showQuestions(service) {
        addUserMessage(service.title);
        
        const options = service.items.map(function(item) {
            return {
                text: item.question,
                action: function() { showAnswer(service, item); }
            };
        });
        
        addBotMessage(`Great! Here are some questions about ${service.title}:`, options);
    }

    function showAnswer(service, item) {
        addUserMessage(item.question);
        
        const actionOptions = [
            {
                text: "Our Services",
                action: function() {
                    addUserMessage("Our Services");
                    addBotMessage("What service would you like to know about?");
                    showServiceOptions();
                }
            },
            {
                text: "Contact Us",
                action: function() {
                    addUserMessage("Contact Us");
                    showContactForm();
                }
            }
        ];
        
        addBotMessage(item.answer, actionOptions);
    }

    function showContactForm() {
        const formHtml = `
            <div class="faq-chatbot__contact-form">
                <h4 class="faq-chatbot__form-header">Contact Us</h4>
                <p class="faq-chatbot__form-intro">Fill out the form below and we'll get back to you soon.</p>
                <form id="contact-form">
                    <div class="faq-chatbot__form-group">
                        <label class="faq-chatbot__label">Name <span class="required">*</span></label>
                        <input type="text" name="name" class="faq-chatbot__input" required>
                    </div>
                    <div class="faq-chatbot__form-group">
                        <label class="faq-chatbot__label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="faq-chatbot__input" required>
                    </div>
                    <div class="faq-chatbot__form-group">
                        <label class="faq-chatbot__label">Phone</label>
                        <input type="tel" name="phone" class="faq-chatbot__input">
                    </div>
                    <div class="faq-chatbot__form-group">
                        <label class="faq-chatbot__label">Service</label>
                        <select name="service" class="faq-chatbot__select">
                            <option value="">Select a service...</option>
                            ${chatbotData.services.map(s => `<option value="${s.title}">${s.title}</option>`).join('')}
                        </select>
                    </div>
                    <div class="faq-chatbot__form-group">
                        <label class="faq-chatbot__label">Message <span class="required">*</span></label>
                        <textarea name="message" class="faq-chatbot__textarea" required></textarea>
                    </div>
                    <div class="faq-chatbot__form-actions">
                        <button type="submit" class="faq-chatbot__submit-btn">Send Message</button>
                    </div>
                </form>
                <div class="faq-chatbot__form-message" style="display: none;"></div>
            </div>
        `;
        
        const formDiv = document.createElement('div');
        formDiv.innerHTML = formHtml;
        messagesContainer.appendChild(formDiv);
        
        const form = formDiv.querySelector('#contact-form');
        form.addEventListener('submit', handleFormSubmit);
        scrollToBottom();
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        if (!data.name || !data.email || !data.message) {
            showFormMessage('Please fill in all required fields.', 'error');
            return;
        }
        
        if (!isValidEmail(data.email)) {
            showFormMessage('Please enter a valid email address.', 'error');
            return;
        }
        
        const submitBtn = e.target.querySelector('.faq-chatbot__submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        
        // Send to REST API
        fetch(faqChatbot.restUrl + 'contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': faqChatbot.nonce
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showFormMessage(result.message || faqChatbot.strings.success, 'success');
                e.target.reset();
                
                // Add success message to chat
                setTimeout(() => {
                    addBotMessage("Thank you! Your message has been sent successfully. We'll get back to you soon!");
                }, 1000);
            } else {
                showFormMessage(result.message || faqChatbot.strings.error, 'error');
            }
        })
        .catch(error => {
            console.error('Contact form error:', error);
            showFormMessage(faqChatbot.strings.error, 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        });
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showFormMessage(message, type) {
        const messageDiv = document.querySelector('.faq-chatbot__form-message');
        if (messageDiv) {
            messageDiv.textContent = message;
            messageDiv.className = `faq-chatbot__form-message faq-chatbot__form-message--${type}`;
            messageDiv.style.display = 'block';
        }
    }

    function restartChat() {
        messageHistory = [];
        messagesContainer.innerHTML = '';
        clearChatHistory();
        initializeChat();
    }

    function displayChatHistory() {
        messageHistory.forEach(displayMessage);
        scrollToBottom();
    }

    function loadChatHistory() {
        try {
            const saved = sessionStorage.getItem('faq-chatbot-history');
            if (saved) {
                messageHistory = JSON.parse(saved);
            }
        } catch (e) {
            messageHistory = [];
        }
    }

    function saveChatHistory() {
        try {
            sessionStorage.setItem('faq-chatbot-history', JSON.stringify(messageHistory));
        } catch (e) {
            // Ignore storage errors
        }
    }

    function clearChatHistory() {
        try {
            sessionStorage.removeItem('faq-chatbot-history');
        } catch (e) {
            // Ignore storage errors
        }
    }

    function scrollToBottom() {
        if (messagesContainer) {
            setTimeout(() => {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 200);
        }
    }

})();
