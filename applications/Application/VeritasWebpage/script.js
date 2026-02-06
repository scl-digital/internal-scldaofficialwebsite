// Veritas-Rhythm-Engine Frontend Controller
// Handles UI interactions, animations, and engine simulation

class VeritasEngine {
    constructor() {
        this.isInitialized = false;
        this.targetUrl = '';
        this.options = {
            fullscreen: false,
            darkMode: true,
            isolateContext: true
        };
        
        this.init();
    }
    
    init() {
        // Cache DOM elements
        this.elements = {
            launchButton: document.getElementById('launchBtn'),
            urlInput: document.getElementById('targetUrl'),
            fullscreenCheckbox: document.getElementById('fullscreen'),
            darkModeCheckbox: document.getElementById('darkMode'),
            isolateContextCheckbox: document.getElementById('isolateContext'),
            statusDots: document.querySelectorAll('.status-dot'),
            cards: document.querySelectorAll('.objective-card')
        };
        
        // Bind events
        this.bindEvents();
        
        // Initialize animations
        this.initAnimations();
        
        // Load saved preferences
        this.loadPreferences();
        
        console.log('🚀 Veritas-Rhythm-Engine initialized');
    }
    
    bindEvents() {
        // Launch button
        this.elements.launchButton.addEventListener('click', () => this.launch());
        
        // Input field
        this.elements.urlInput.addEventListener('input', (e) => {
            this.targetUrl = e.target.value;
            this.updateLaunchButton();
        });
        
        // Enter key to launch
        this.elements.urlInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && this.targetUrl) {
                this.launch();
            }
        });
        
        // Option checkboxes
        this.elements.fullscreenCheckbox.addEventListener('change', (e) => {
            this.options.fullscreen = e.target.checked;
            this.savePreferences();
        });
        
        this.elements.darkModeCheckbox.addEventListener('change', (e) => {
            this.options.darkMode = e.target.checked;
            this.savePreferences();
        });
        
        this.elements.isolateContextCheckbox.addEventListener('change', (e) => {
            this.options.isolateContext = e.target.checked;
            this.updateStatusIndicator('context', e.target.checked);
            this.savePreferences();
        });
        
        // Card hover effects
        this.elements.cards.forEach(card => {
            card.addEventListener('mouseenter', () => this.onCardHover(card));
            card.addEventListener('mouseleave', () => this.onCardLeave(card));
        });
    }
    
    initAnimations() {
        // Stagger animation for cards on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        this.elements.cards.forEach(card => {
            observer.observe(card);
        });
        
        // Parallax effect for hero section
        window.addEventListener('scroll', () => this.handleScroll());
    }
    
    handleScroll() {
        const scrollY = window.scrollY;
        const heroSection = document.querySelector('.hero-section');
        
        if (heroSection) {
            heroSection.style.transform = `translateY(${scrollY * 0.3}px)`;
            heroSection.style.opacity = `${1 - scrollY * 0.002}`;
        }
    }
    
    onCardHover(card) {
        // Add subtle glow effect
        const index = card.getAttribute('data-index');
        card.style.boxShadow = '0 12px 32px rgba(0, 217, 255, 0.15)';
    }
    
    onCardLeave(card) {
        card.style.boxShadow = '';
    }
    
    updateLaunchButton() {
        const button = this.elements.launchButton;
        
        if (this.targetUrl) {
            button.classList.remove('disabled');
            button.style.opacity = '1';
        } else {
            button.classList.add('disabled');
            button.style.opacity = '0.5';
        }
    }
    
    updateStatusIndicator(type, active) {
        const statusItems = document.querySelectorAll('.status-item');
        
        statusItems.forEach(item => {
            const label = item.querySelector('.status-label').textContent.toLowerCase();
            if (label === type) {
                const dot = item.querySelector('.status-dot');
                if (active) {
                    dot.classList.remove('standby');
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                    dot.classList.add('standby');
                }
            }
        });
    }
    
    async launch() {
        if (!this.targetUrl) {
            this.showNotification('Please enter a target URL', 'warning');
            return;
        }
        
        // Validate URL
        if (!this.isValidUrl(this.targetUrl)) {
            this.showNotification('Please enter a valid URL', 'error');
            return;
        }
        
        // Show launching animation
        this.showLaunchSequence();
        
        // Simulate engine initialization
        await this.simulateEngineStart();
        
        // In a real implementation, this would communicate with the Electron/desktop wrapper
        console.log('🎯 Launching VRE with configuration:', {
            url: this.targetUrl,
            options: this.options
        });
        
        // Open URL (in real app, this would be handled by desktop wrapper)
        this.openInWrapper();
    }
    
    async simulateEngineStart() {
        const steps = [
            { message: 'Initializing wrapper shell...', delay: 300 },
            { message: 'Loading web context...', delay: 500 },
            { message: 'Applying isolation parameters...', delay: 400 },
            { message: 'Rendering target application...', delay: 600 }
        ];
        
        for (const step of steps) {
            this.updateLaunchStatus(step.message);
            await this.sleep(step.delay);
        }
    }
    
    showLaunchSequence() {
        const button = this.elements.launchButton;
        button.classList.add('launching');
        button.innerHTML = `
            <span class="button-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </span>
            <span class="button-text">INITIALIZING...</span>
            <span class="button-backdrop"></span>
        `;
        
        // Animate all status dots to active
        document.querySelectorAll('.status-dot').forEach(dot => {
            dot.classList.remove('standby');
            dot.classList.add('active');
        });
    }
    
    updateLaunchStatus(message) {
        const button = this.elements.launchButton;
        const buttonText = button.querySelector('.button-text');
        if (buttonText) {
            buttonText.textContent = message.toUpperCase();
        }
    }
    
    openInWrapper() {
        // In a real desktop application, this would open the URL in an isolated wrapper
        // For demo purposes, we'll open in a new window with specific parameters
        
        const features = [];
        
        if (this.options.fullscreen) {
            features.push('fullscreen=yes');
        }
        
        features.push('menubar=no');
        features.push('toolbar=no');
        features.push('location=no');
        features.push('status=no');
        
        // Show success notification
        this.showNotification('Application launched successfully!', 'success');
        
        // Reset button after delay
        setTimeout(() => {
            this.resetLaunchButton();
        }, 2000);
        
        // In production, this would be handled by the desktop wrapper:
        // window.electronAPI.launchApp(this.targetUrl, this.options);
        
        console.log('✅ VRE would open:', this.targetUrl);
        console.log('📋 With features:', features.join(', '));
    }
    
    resetLaunchButton() {
        const button = this.elements.launchButton;
        button.classList.remove('launching');
        button.innerHTML = `
            <span class="button-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="5 3 19 12 5 21 5 3"/>
                </svg>
            </span>
            <span class="button-text">INITIALIZE ENGINE</span>
            <span class="button-backdrop"></span>
        `;
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Style based on type
        const colors = {
            success: 'var(--color-success)',
            warning: 'var(--color-warning)',
            error: '#ff4444',
            info: 'var(--color-accent-primary)'
        };
        
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 30px;
            padding: 1rem 1.5rem;
            background: var(--color-bg-elevated);
            border: 1px solid ${colors[type]};
            border-radius: 6px;
            color: ${colors[type]};
            font-family: var(--font-mono);
            font-size: 0.875rem;
            font-weight: 600;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    isValidUrl(string) {
        try {
            const url = new URL(string);
            return url.protocol === 'http:' || url.protocol === 'https:';
        } catch (_) {
            return false;
        }
    }
    
    savePreferences() {
        localStorage.setItem('vre-preferences', JSON.stringify({
            targetUrl: this.targetUrl,
            options: this.options
        }));
    }
    
    loadPreferences() {
        const saved = localStorage.getItem('vre-preferences');
        if (saved) {
            try {
                const data = JSON.parse(saved);
                
                if (data.targetUrl) {
                    this.targetUrl = data.targetUrl;
                    this.elements.urlInput.value = data.targetUrl;
                    this.updateLaunchButton();
                }
                
                if (data.options) {
                    this.options = { ...this.options, ...data.options };
                    
                    // Update checkboxes
                    this.elements.fullscreenCheckbox.checked = this.options.fullscreen;
                    this.elements.darkModeCheckbox.checked = this.options.darkMode;
                    this.elements.isolateContextCheckbox.checked = this.options.isolateContext;
                }
            } catch (e) {
                console.error('Failed to load preferences:', e);
            }
        }
    }
    
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Add notification animations to stylesheet dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
    
    .launch-button.launching {
        cursor: wait;
        opacity: 0.8;
    }
    
    .launch-button.disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }
`;
document.head.appendChild(style);

// Initialize engine when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.veritasEngine = new VeritasEngine();
    });
} else {
    window.veritasEngine = new VeritasEngine();
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + K to focus URL input
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('targetUrl').focus();
    }
    
    // Ctrl/Cmd + Enter to launch
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        if (window.veritasEngine.targetUrl) {
            window.veritasEngine.launch();
        }
    }
});

// Easter egg: Console styling
console.log('%c🚀 VERITAS-RHYTHM-ENGINE', 'font-size: 20px; font-weight: bold; color: #00d9ff; font-family: monospace;');
console.log('%cLightweight Browser-Less Wrapper', 'font-size: 12px; color: #8a95a8; font-family: monospace;');
console.log('%c\nKeyboard Shortcuts:', 'font-weight: bold; color: #e8eef5; margin-top: 10px;');
console.log('%cCtrl/Cmd + K: Focus URL input', 'color: #8a95a8;');
console.log('%cCtrl/Cmd + Enter: Launch application', 'color: #8a95a8;');
console.log('%c\nEngine Status: READY', 'color: #00ff88; font-weight: bold; margin-top: 10px;');
