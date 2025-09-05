/**
 * FAQ Chatbot JavaScript - Chat Interface
 */

(function() {
    'use strict';

    let chatbotData = {};
    let messageHistory = [];
    let widget = null;
    let messagesContainer = null;

    document.addEventListener('DOMContentLoaded', function() {
        widget = document.getElementById('faq-chatbot-widget');
        if (!widget || typeof faqChatbot === 'undefined') {
            return;
        }

        chatbotData = faqChatbot.data;
        messagesContainer = document.getElementById('chat-messages');
        
        initializeChat();
        setupEventListeners();
    });

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
        
        // Simulate form submission
        const submitBtn = e.target.querySelector('.faq-chatbot__submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        
        setTimeout(() => {
            showFormMessage('Thank you! Your message has been sent successfully.', 'success');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
            e.target.reset();
        }, 1000);
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
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }

})();
