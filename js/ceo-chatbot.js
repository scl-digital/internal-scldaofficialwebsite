class CEOChatbot {
    constructor() {
        this.isOpen = false;
        this.chatHistory = [];
        this.init();
    }

    init() {
        this.createWidget();
        this.bindEvents();
        this.showWelcomeMessage();
    }

    createWidget() {
        // Create widget container
        const widget = document.createElement('div');
        widget.className = 'ceo-chatbot-widget';
        widget.innerHTML = `
            <div class="chatbot-container">
                <div class="chatbot-header">
                    <div class="header-content">
                        <img src="/images/Shinsa_Lyonga_Lomboto_Website_Chat_Image.png" class="logo" alt="SCL Logo">
                        <div class="header-text">
                            <h3>Shinsa Lomboto</h3>
                            <p>CEO / Founder & Managing Director</p>
                        </div>
                    </div>
                    <button class="minimize-btn">−</button>
                </div>
                <div class="chat-messages" id="ceoChatMessages">
                    <!-- Messages will be added here -->
                </div>
                <div class="chat-input-container">
                    <div class="typing-indicator" id="typingIndicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                    <div class="input-wrapper">
                        <textarea id="ceoChatInput" class="chat-input" placeholder="Type your message..." rows="1"></textarea>
                        <div class="button-group">
                            <label for="fileInput" class="file-upload-label" title="Upload a file">
                                <span>📎</span>
                                <input type="file" id="fileInput" class="file-input" accept=".txt,.md,.pdf,.doc,.docx">
                            </label>
                            <button id="sendMessageBtn" class="send-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <button class="chat-toggle-btn">
                <img src="/images/Shinsa_Lyonga_Lomboto_Website_Chat_Image.png" alt="Chat with Shinsa" class="ceo-profile-image">
            </button>
        `;
        document.body.appendChild(widget);
        
        // Store references to elements
        this.elements = {
            widget: widget,
            container: widget.querySelector('.chatbot-container'),
            messages: widget.querySelector('.chat-messages'),
            input: widget.querySelector('.chat-input'),
            sendBtn: widget.querySelector('.send-btn'),
            toggleBtn: widget.querySelector('.chat-toggle-btn'),
            minimizeBtn: widget.querySelector('.minimize-btn'),
            typingIndicator: widget.querySelector('.typing-indicator'),
            fileInput: widget.querySelector('.file-input')
        };
    }

    bindEvents() {
        // Toggle chat
        this.elements.toggleBtn.addEventListener('click', () => this.toggleChat());
        this.elements.minimizeBtn.addEventListener('click', () => this.toggleChat());
        
        // Send message on button click
        this.elements.sendBtn.addEventListener('click', () => this.sendMessage());
        
        // Send message on Enter (but allow Shift+Enter for new line)
        this.elements.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Auto-resize textarea
        this.elements.input.addEventListener('input', () => {
            this.adjustTextareaHeight();
        });
        
        // File upload
        this.elements.fileInput.addEventListener('change', (e) => this.handleFileUpload(e));
        
        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.elements.widget.contains(e.target) && !e.target.closest('.ceo-chatbot-widget')) {
                this.toggleChat();
            }
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.elements.container.style.display = 'flex';
            this.elements.toggleBtn.style.display = 'none';
            this.elements.input.focus();
        } else {
            this.elements.container.style.display = 'none';
            this.elements.toggleBtn.style.display = 'flex';
        }
    }

    showWelcomeMessage() {
        const welcomeMessage = `Hello! I'm Shinsa Lomboto, CEO and Founder of Software Creative Labs. I'm here to assist you. How can I help you today?`;
        this.addMessage('ceo', welcomeMessage, true);
        
        // Show quick options
        setTimeout(() => {
            this.showQuickOptions();
        }, 1000);
    }

    showQuickOptions() {
        const options = [
            {
                text: 'View Our Work',
                action: () => {
                    this.addMessage('user', 'I\'d like to see some of your work.');
                    window.open('/case_studies.html', '_blank');
                    this.respondToOption('work');
                }
            },
            {
                text: 'Contact Our Team',
                action: () => {
                    this.addMessage('user', 'I\'d like to get in touch with your team.');
                    window.open('mailto:contact@softwarecreativelabs.com', '_blank');
                    this.respondToOption('contact');
                }
            },
            {
                text: 'Make a Payment',
                action: () => {
                    this.addMessage('user', 'I\'d like to make a payment.');
                    window.open('/payments.html', '_blank');
                    this.respondToOption('payment');
                }
            }
        ];

        const optionsContainer = document.createElement('div');
        optionsContainer.className = 'quick-options';
        
        options.forEach(option => {
            const button = document.createElement('button');
            button.className = 'quick-option';
            button.textContent = option.text;
            button.addEventListener('click', option.action);
            optionsContainer.appendChild(button);
        });

        this.addMessage('ceo', '', false, optionsContainer);
    }

    respondToOption(option) {
        const responses = {
            'work': 'I\'ve opened our case studies in a new tab. You can see some of our recent projects and success stories there. Is there anything specific you\'d like to know about our work?',
            'contact': 'I\'ve opened your email client with our contact address. Our team will get back to you as soon as possible. Is there anything else I can help you with?',
            'payment': 'I\'ve opened our secure payment portal in a new tab. You can complete your payment there. Let me know if you need any assistance with the process.'
        };

        this.showTypingIndicator();
        setTimeout(() => {
            this.hideTypingIndicator();
            this.addMessage('ceo', responses[option] || 'How else can I assist you today?');
            this.showQuickOptions();
        }, 1000);
    }

    async sendMessage() {
        const message = this.elements.input.value.trim();
        if (!message) return;

        // Add user message to chat
        this.addMessage('user', message);
        this.elements.input.value = '';
        this.adjustTextareaHeight();

        // Show typing indicator
        this.showTypingIndicator();

        // Simulate AI response (in a real implementation, this would be an API call)
        setTimeout(() => {
            this.hideTypingIndicator();
            this.generateResponse(message);
        }, 1000);
    }

    async generateResponse(message) {
        // In a real implementation, this would call the Gemini API
        // For now, we'll use some predefined responses
        const responses = [
            "I'd be happy to help with that. Could you provide more details about what you're looking for?",
            "That's a great question. Let me look into that for you.",
            "I understand you're interested in that. Here's what I can tell you...",
            "Thanks for reaching out. Our team can provide more detailed information about that.",
            "I'll make sure to pass that along to the right person on our team."
        ];

        const randomResponse = responses[Math.floor(Math.random() * responses.length)];
        this.addMessage('ceo', randomResponse);
    }

    addMessage(sender, message, isTyping = false, customContent = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        
        if (customContent) {
            messageContent.appendChild(customContent);
        } else if (isTyping) {
            messageContent.textContent = '';
            this.typeText(messageContent, message);
        } else {
            messageContent.textContent = message;
        }
        
        messageDiv.appendChild(messageContent);
        this.elements.messages.appendChild(messageDiv);
        this.scrollToBottom();
        
        // Add to chat history
        this.chatHistory.push({ sender, message, timestamp: new Date() });
    }

    typeText(element, text, index = 0) {
        if (index < text.length) {
            element.textContent += text.charAt(index);
            setTimeout(() => this.typeText(element, text, index + 1), 20);
        }
    }

    showTypingIndicator() {
        this.elements.typingIndicator.style.display = 'flex';
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        this.elements.typingIndicator.style.display = 'none';
    }

    adjustTextareaHeight() {
        const textarea = this.elements.input;
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
    }

    scrollToBottom() {
        this.elements.messages.scrollTop = this.elements.messages.scrollHeight;
    }

    async handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Add file message to chat
        const fileMessage = `📎 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
        this.addMessage('user', fileMessage);
        this.showTypingIndicator();

        // In a real implementation, you would process the file here
        // For now, we'll just show a generic response
        setTimeout(() => {
            this.hideTypingIndicator();
            this.addMessage('ceo', 'Thank you for sharing the file. I\'ve received it and will review the contents. How can I assist you with this document?');
        }, 1500);

        // Reset file input
        event.target.value = '';
    }
}

// Initialize the chatbot when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.ceoChatbot = new CEOChatbot();
});
