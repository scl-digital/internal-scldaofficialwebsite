// Conversational Navigation System
class ConversationalNavigation {
    constructor() {
        this.init();
    }

    init() {
        this.createModal();
        this.interceptNavigationButtons();
    }

    createModal() {
        // Create modal HTML
        const modalHTML = `
            <div id="conversational-modal" class="conv-modal">
                <div class="conv-modal-content">
                    <div class="conv-modal-header">
                        <img src="images/SCLDA_Softwarecreativelabs.com_logo.png" alt="SCL Digital Agency" class="conv-modal-logo">
                        <button class="conv-modal-close">&times;</button>
                    </div>
                    <div class="conv-modal-body">
                        <div class="conv-avatar">
                            <img src="images/Shinsa_Lyonga_Lomboto_Website_Chat_Image.png" alt="Assistant">
                        </div>
                        <div class="conv-message-container">
                            <div class="conv-typing-indicator">
                                <div class="conv-typing-dot"></div>
                                <div class="conv-typing-dot"></div>
                                <div class="conv-typing-dot"></div>
                            </div>
                            <div class="conv-message" id="conv-message"></div>
                        </div>
                        <div class="conv-actions" id="conv-actions"></div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Add styles
        this.addStyles();

        // Bind events
        this.bindModalEvents();
    }

    addStyles() {
        const styles = `
            <style>
                .conv-modal {
                    display: none;
                    position: fixed;
                    z-index: 10000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.7);
                    backdrop-filter: blur(5px);
                    animation: fadeIn 0.3s ease;
                }

                .conv-modal.show {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .conv-modal-content {
                    background: linear-gradient(145deg, #ffffff, #f8f9fa);
                    border-radius: 20px;
                    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
                    max-width: 500px;
                    width: 90%;
                    max-height: 80vh;
                    overflow: hidden;
                    animation: slideUp 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                }

                .conv-modal-header {
                    background: linear-gradient(135deg, #1e348d, #167ac6);
                    color: white;
                    padding: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }

                .conv-modal-logo {
                    height: 40px;
                    width: auto;
                }

                .conv-modal-close {
                    background: none;
                    border: none;
                    color: white;
                    font-size: 28px;
                    cursor: pointer;
                    padding: 0;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s ease;
                }

                .conv-modal-close:hover {
                    background: rgba(255, 255, 255, 0.2);
                    transform: rotate(90deg);
                }

                .conv-modal-body {
                    padding: 30px;
                    text-align: center;
                }

                .conv-avatar {
                    margin-bottom: 20px;
                }

                .conv-avatar img {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    border: 4px solid #1e348d;
                    object-fit: cover;
                    animation: pulse 2s infinite;
                }

                .conv-message-container {
                    margin-bottom: 30px;
                    min-height: 60px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                }

                .conv-typing-indicator {
                    display: flex;
                    gap: 8px;
                    margin-bottom: 15px;
                }

                .conv-typing-dot {
                    width: 8px;
                    height: 8px;
                    background: #1e348d;
                    border-radius: 50%;
                    animation: typing 1.4s infinite;
                }

                .conv-typing-dot:nth-child(2) {
                    animation-delay: 0.2s;
                }

                .conv-typing-dot:nth-child(3) {
                    animation-delay: 0.4s;
                }

                .conv-message {
                    font-size: 18px;
                    line-height: 1.6;
                    color: #333;
                    opacity: 0;
                    transform: translateY(20px);
                    transition: all 0.5s ease;
                }

                .conv-message.show {
                    opacity: 1;
                    transform: translateY(0);
                }

                .conv-actions {
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                    opacity: 0;
                    transform: translateY(20px);
                    transition: all 0.5s ease 0.3s;
                }

                .conv-actions.show {
                    opacity: 1;
                    transform: translateY(0);
                }

                .conv-btn {
                    background: linear-gradient(135deg, #1e348d, #167ac6);
                    color: white;
                    border: none;
                    padding: 15px 25px;
                    border-radius: 50px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    box-shadow: 0 5px 15px rgba(30, 52, 141, 0.3);
                }

                .conv-btn:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 25px rgba(30, 52, 141, 0.4);
                }

                .conv-btn.secondary {
                    background: linear-gradient(135deg, #6c757d, #495057);
                    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
                }

                .conv-btn.secondary:hover {
                    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
                }

                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }

                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(50px) scale(0.9);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }

                @keyframes pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                }

                @keyframes typing {
                    0%, 60%, 100% { transform: translateY(0); }
                    30% { transform: translateY(-10px); }
                }

                @media (max-width: 480px) {
                    .conv-modal-content {
                        margin: 20px;
                        width: calc(100% - 40px);
                    }
                    
                    .conv-modal-body {
                        padding: 20px;
                    }
                    
                    .conv-message {
                        font-size: 16px;
                    }
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    bindModalEvents() {
        const modal = document.getElementById('conversational-modal');
        const closeBtn = modal.querySelector('.conv-modal-close');

        closeBtn.addEventListener('click', () => this.closeModal());
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                this.closeModal();
            }
        });
    }

    interceptNavigationButtons() {
        // Define navigation mappings with conversational messages
        const navigationMappings = {
            'about.html': {
                title: 'About Our Company',
                message: "I'd love to tell you more about SCL Digital Agency! We're passionate about helping businesses grow through innovative digital solutions.",
                confirmText: "Let's Learn About SCL DA",
                icon: '🏢'
            },
            'services-platform/our_services.html': {
                title: 'Our Services',
                message: "Wonderful! I'm excited to show you our comprehensive range of services. From web development to digital marketing, we've got you covered.",
                confirmText: "Explore Our Services",
                icon: '🛠️'
            },
            'applications/products.html/': {
                title: 'Our Products',
                message: "Great choice! Let me show you our innovative products and solutions that can transform your business operations.",
                confirmText: "View Our Products",
                icon: '📦'
            },
            'case_studies.html': {
                title: 'Success Stories',
                message: "I'm thrilled to share some amazing success stories from our valued clients. These case studies showcase real results!",
                confirmText: "See Success Stories",
                icon: '📈'
            },
            'contactus.html': {
                title: 'Get In Touch',
                message: "Perfect! I'd be happy to connect you with our team. We're here to help and would love to hear from you.",
                confirmText: "Contact Our Team",
                icon: '📞'
            },
            'payments.html': {
                title: 'Payment Information',
                message: "No worries! Let me guide you to our secure payment portal where you can easily manage your transactions.",
                confirmText: "Go to Payments",
                icon: '💳'
            },
            'clientportal.html': {
                title: 'Client Portal',
                message: "Welcome back! I'll take you to your secure client portal where you can access all your project information.",
                confirmText: "Access Client Portal",
                icon: '🔐'
            },
            'events-platform/eventspage.html': {
                title: 'Events & Workshops',
                message: "Exciting! Let me show you our upcoming events and workshops. There's always something interesting happening!",
                confirmText: "View Events",
                icon: '📅'
            }
        };

        // Intercept all navigation links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (!link) return;

            const href = link.getAttribute('href');
            const mapping = navigationMappings[href];

            if (mapping) {
                e.preventDefault();
                this.showConversationalModal(href, mapping);
            }
        });
    }

    showConversationalModal(destination, config) {
        const modal = document.getElementById('conversational-modal');
        const messageEl = document.getElementById('conv-message');
        const actionsEl = document.getElementById('conv-actions');
        const typingIndicator = modal.querySelector('.conv-typing-indicator');

        // Reset modal state
        messageEl.classList.remove('show');
        actionsEl.classList.remove('show');
        messageEl.textContent = '';
        actionsEl.innerHTML = '';
        typingIndicator.style.display = 'flex';

        // Show modal
        modal.classList.add('show');

        // Show typing indicator for realistic delay
        setTimeout(() => {
            typingIndicator.style.display = 'none';
            
            // Show message
            messageEl.innerHTML = `
                <div style="font-size: 24px; margin-bottom: 10px;">${config.icon}</div>
                <h3 style="margin-bottom: 15px; color: #1e348d;">${config.title}</h3>
                <p>${config.message}</p>
            `;
            messageEl.classList.add('show');

            // Show actions after message
            setTimeout(() => {
                actionsEl.innerHTML = `
                    <button class="conv-btn" onclick="conversationalNav.navigateTo('${destination}')">
                        ${config.confirmText}
                    </button>
                    <button class="conv-btn secondary" onclick="conversationalNav.closeModal()">
                        Maybe Later
                    </button>
                `;
                actionsEl.classList.add('show');
            }, 500);
        }, 1500);
    }

    navigateTo(destination) {
        const modal = document.getElementById('conversational-modal');
        const messageEl = document.getElementById('conv-message');
        
        // Show loading message
        messageEl.innerHTML = `
            <div style="font-size: 24px; margin-bottom: 10px;">✨</div>
            <p>Taking you there now... Thank you for choosing SCL Digital Agency!</p>
        `;

        // Navigate after a brief delay
        setTimeout(() => {
            window.location.href = destination;
        }, 1000);
    }

    closeModal() {
        const modal = document.getElementById('conversational-modal');
        modal.classList.remove('show');
    }
}

// Initialize the conversational navigation system
let conversationalNav;
document.addEventListener('DOMContentLoaded', () => {
    conversationalNav = new ConversationalNavigation();
});
