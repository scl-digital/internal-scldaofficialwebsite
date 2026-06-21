// VRE Application Controller
// Main application logic for the browser-less wrapper

class VREApp {
    constructor() {
        this.currentUrl = '';
        this.isLoading = false;
        this.settings = this.loadSettings();
        this.zoomLevel = 1.0;
        this.isFullscreen = false;
        
        this.init();
    }
    
    init() {
        console.log('🚀 VRE Application initialized');
        
        // Cache DOM elements
        this.dom = {
            appFrame: document.getElementById('appFrame'),
            loadingOverlay: document.getElementById('loadingOverlay'),
            errorOverlay: document.getElementById('errorOverlay'),
            progressBar: document.getElementById('progressBar'),
            appTitle: document.getElementById('appTitle'),
            urlText: document.getElementById('urlText'),
            statusDot: document.getElementById('statusDot'),
            statusText: document.getElementById('statusText'),
            settingsPanel: document.getElementById('settingsPanel'),
            quickActions: document.getElementById('quickActions'),
            errorMessage: document.getElementById('errorMessage'),
            memoryUsage: document.getElementById('memoryUsage')
        };
        
        // Bind events
        this.bindEvents();
        
        // Apply saved settings
        this.applySettings();
        
        // Load target URL from query params or default
        this.loadTargetUrl();
        
        // Start memory monitor
        this.startMemoryMonitor();
    }
    
    bindEvents() {
        // Control buttons
        document.getElementById('refreshBtn').addEventListener('click', () => this.refresh());
        document.getElementById('homeBtn').addEventListener('click', () => this.goHome());
        document.getElementById('settingsBtn').addEventListener('click', () => this.toggleSettings());
        document.getElementById('minimizeBtn').addEventListener('click', () => this.minimize());
        document.getElementById('maximizeBtn').addEventListener('click', () => this.maximize());
        document.getElementById('closeBtn').addEventListener('click', () => this.close());
        
        // Settings panel
        document.getElementById('settingsClose').addEventListener('click', () => this.toggleSettings());
        document.getElementById('saveSettings').addEventListener('click', () => this.saveSettings());
        document.getElementById('resetSettings').addEventListener('click', () => this.resetSettings());
        
        // Setting toggles
        document.getElementById('alwaysOnTop').addEventListener('change', (e) => {
            this.settings.alwaysOnTop = e.target.checked;
            this.updateAlwaysOnTop();
        });
        
        document.getElementById('hideTopbar').addEventListener('change', (e) => {
            this.settings.hideTopbar = e.target.checked;
            this.toggleTopbar();
        });
        
        document.getElementById('hardwareAccel').addEventListener('change', (e) => {
            this.settings.hardwareAccel = e.target.checked;
        });
        
        document.getElementById('cacheEnabled').addEventListener('change', (e) => {
            this.settings.cacheEnabled = e.target.checked;
        });
        
        document.getElementById('blockCookies').addEventListener('change', (e) => {
            this.settings.blockCookies = e.target.checked;
        });
        
        document.getElementById('clearOnClose').addEventListener('change', (e) => {
            this.settings.clearOnClose = e.target.checked;
        });
        
        // Error retry
        document.getElementById('retryBtn').addEventListener('click', () => this.retryLoad());
        
        // Quick actions
        document.getElementById('screenshotBtn').addEventListener('click', () => this.takeScreenshot());
        document.getElementById('fullscreenBtn').addEventListener('click', () => this.toggleFullscreen());
        document.getElementById('zoomBtn').addEventListener('click', () => this.showZoomControls());
        
        // Keyboard shortcuts
        this.bindKeyboardShortcuts();
        
        // Iframe events
        this.dom.appFrame.addEventListener('load', () => this.onFrameLoad());
        this.dom.appFrame.addEventListener('error', () => this.onFrameError());
        
        // Show quick actions on mouse move
        let quickActionsTimeout;
        document.addEventListener('mousemove', (e) => {
            if (e.clientY > window.innerHeight - 100) {
                this.dom.quickActions.classList.remove('hidden');
                clearTimeout(quickActionsTimeout);
                quickActionsTimeout = setTimeout(() => {
                    this.dom.quickActions.classList.add('hidden');
                }, 3000);
            }
        });
    }
    
    bindKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + R - Refresh
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                this.refresh();
            }
            
            // Ctrl/Cmd + W - Close
            if ((e.ctrlKey || e.metaKey) && e.key === 'w') {
                e.preventDefault();
                this.close();
            }
            
            // Ctrl/Cmd + B - Toggle topbar
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                this.toggleTopbar();
            }
            
            // F11 - Fullscreen
            if (e.key === 'F11') {
                e.preventDefault();
                this.toggleFullscreen();
            }
            
            // Ctrl/Cmd + = - Zoom in
            if ((e.ctrlKey || e.metaKey) && e.key === '=') {
                e.preventDefault();
                this.zoomIn();
            }
            
            // Ctrl/Cmd + - - Zoom out
            if ((e.ctrlKey || e.metaKey) && e.key === '-') {
                e.preventDefault();
                this.zoomOut();
            }
            
            // Ctrl/Cmd + 0 - Reset zoom
            if ((e.ctrlKey || e.metaKey) && e.key === '0') {
                e.preventDefault();
                this.resetZoom();
            }
            
            // F5 - Refresh
            if (e.key === 'F5') {
                e.preventDefault();
                this.refresh();
            }
            
            // Escape - Exit fullscreen
            if (e.key === 'Escape' && this.isFullscreen) {
                this.toggleFullscreen();
            }
        });
    }
    
    loadTargetUrl() {
        // Get URL from query params
        const params = new URLSearchParams(window.location.search);
        const targetUrl = params.get('url') || this.settings.lastUrl || 'https://example.com';
        
        this.currentUrl = targetUrl;
        this.loadUrl(targetUrl);
    }
    
    async loadUrl(url) {
        if (!this.isValidUrl(url)) {
            this.showError('Invalid URL', 'Please provide a valid HTTPS URL.');
            return;
        }
        
        this.isLoading = true;
        this.currentUrl = url;
        this.dom.urlText.textContent = this.formatUrl(url);
        
        // Show loading overlay
        this.dom.loadingOverlay.style.display = 'flex';
        this.dom.errorOverlay.classList.add('hidden');
        
        // Simulate loading progress
        await this.simulateProgress();
        
        // Load in iframe
        try {
            this.dom.appFrame.src = url;
            this.settings.lastUrl = url;
            this.saveSettingsToStorage();
        } catch (error) {
            this.onFrameError();
        }
    }
    
    async simulateProgress() {
        const steps = [
            { progress: 20, delay: 200 },
            { progress: 45, delay: 300 },
            { progress: 70, delay: 400 },
            { progress: 90, delay: 300 }
        ];
        
        for (const step of steps) {
            await this.sleep(step.delay);
            this.dom.progressBar.style.width = `${step.progress}%`;
        }
    }
    
    onFrameLoad() {
        this.isLoading = false;
        
        // Complete progress
        this.dom.progressBar.style.width = '100%';
        
        // Hide loading overlay after a short delay
        setTimeout(() => {
            this.dom.loadingOverlay.style.display = 'none';
            this.dom.progressBar.style.width = '0%';
        }, 300);
        
        // Update title from iframe
        try {
            const iframeDoc = this.dom.appFrame.contentDocument || this.dom.appFrame.contentWindow.document;
            const title = iframeDoc.title || 'Application';
            this.dom.appTitle.textContent = title;
            document.title = `${title} - VRE`;
        } catch (e) {
            // Cross-origin restriction
            this.dom.appTitle.textContent = 'Application';
        }
        
        // Update connection status
        this.updateConnectionStatus(true);
        
        // Show notification
        this.showNotification('Application loaded successfully', 'success');
    }
    
    onFrameError() {
        this.isLoading = false;
        this.dom.loadingOverlay.style.display = 'none';
        this.showError('Connection Failed', `Unable to load ${this.currentUrl}`);
        this.updateConnectionStatus(false);
    }
    
    showError(title, message) {
        this.dom.errorOverlay.classList.remove('hidden');
        document.querySelector('.error-title').textContent = title;
        this.dom.errorMessage.textContent = message;
    }
    
    retryLoad() {
        this.dom.errorOverlay.classList.add('hidden');
        this.loadUrl(this.currentUrl);
    }
    
    refresh() {
        this.showNotification('Refreshing...', 'info');
        this.dom.appFrame.contentWindow.location.reload();
    }
    
    goHome() {
        if (this.settings.homeUrl && this.settings.homeUrl !== this.currentUrl) {
            this.loadUrl(this.settings.homeUrl);
        } else {
            this.refresh();
        }
    }
    
    toggleSettings() {
        this.dom.settingsPanel.classList.toggle('hidden');
    }
    
    minimize() {
        // In Electron: window.electronAPI.minimize()
        this.showNotification('Minimize triggered', 'info');
        console.log('Minimize window');
    }
    
    maximize() {
        // In Electron: window.electronAPI.maximize()
        this.showNotification('Maximize triggered', 'info');
        console.log('Toggle maximize');
    }
    
    close() {
        if (this.settings.clearOnClose) {
            // Clear cache and data
            console.log('Clearing data...');
        }
        
        // In Electron: window.electronAPI.close()
        this.showNotification('Closing application...', 'info');
        console.log('Close window');
        
        // For demo: just hide
        setTimeout(() => {
            document.body.style.opacity = '0';
        }, 500);
    }
    
    toggleTopbar() {
        document.querySelector('.vre-topbar').classList.toggle('hidden');
        const isHidden = document.querySelector('.vre-topbar').classList.contains('hidden');
        this.showNotification(isHidden ? 'Topbar hidden (Ctrl+B to show)' : 'Topbar visible', 'info');
    }
    
    updateAlwaysOnTop() {
        // In Electron: window.electronAPI.setAlwaysOnTop(this.settings.alwaysOnTop)
        console.log('Always on top:', this.settings.alwaysOnTop);
    }
    
    takeScreenshot() {
        this.showNotification('Screenshot saved', 'success');
        // In real app: capture screenshot of iframe content
        console.log('Taking screenshot...');
    }
    
    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            this.isFullscreen = true;
            this.showNotification('Fullscreen mode', 'info');
        } else {
            document.exitFullscreen();
            this.isFullscreen = false;
            this.showNotification('Exited fullscreen', 'info');
        }
    }
    
    zoomIn() {
        this.zoomLevel = Math.min(this.zoomLevel + 0.1, 2.0);
        this.applyZoom();
    }
    
    zoomOut() {
        this.zoomLevel = Math.max(this.zoomLevel - 0.1, 0.5);
        this.applyZoom();
    }
    
    resetZoom() {
        this.zoomLevel = 1.0;
        this.applyZoom();
    }
    
    applyZoom() {
        this.dom.appFrame.style.transform = `scale(${this.zoomLevel})`;
        this.dom.appFrame.style.transformOrigin = 'top left';
        this.dom.appFrame.style.width = `${100 / this.zoomLevel}%`;
        this.dom.appFrame.style.height = `${100 / this.zoomLevel}%`;
        
        const zoomBtn = document.getElementById('zoomBtn');
        zoomBtn.querySelector('span').textContent = `Zoom: ${Math.round(this.zoomLevel * 100)}%`;
        
        this.showNotification(`Zoom: ${Math.round(this.zoomLevel * 100)}%`, 'info');
    }
    
    showZoomControls() {
        // Show a quick zoom menu
        this.showNotification('Use Ctrl +/- to zoom, Ctrl 0 to reset', 'info');
    }
    
    updateConnectionStatus(connected) {
        if (connected) {
            this.dom.statusDot.classList.remove('disconnected');
            this.dom.statusText.textContent = 'Connected';
        } else {
            this.dom.statusDot.classList.add('disconnected');
            this.dom.statusText.textContent = 'Disconnected';
        }
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification-toast ${type}`;
        
        const icon = document.createElement('div');
        icon.className = 'notification-icon';
        
        let iconSvg = '';
        switch(type) {
            case 'success':
                iconSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
                break;
            case 'error':
                iconSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
                break;
            default:
                iconSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
        }
        
        icon.innerHTML = iconSvg;
        
        const messageEl = document.createElement('div');
        messageEl.className = 'notification-message';
        messageEl.textContent = message;
        
        notification.appendChild(icon);
        notification.appendChild(messageEl);
        document.body.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    startMemoryMonitor() {
        // Simulate memory usage monitoring
        setInterval(() => {
            // In real app: get actual memory usage from Electron
            const memoryMB = (Math.random() * 200 + 100).toFixed(0);
            this.dom.memoryUsage.textContent = `${memoryMB} MB`;
        }, 2000);
    }
    
    applySettings() {
        // Apply all settings from storage
        document.getElementById('alwaysOnTop').checked = this.settings.alwaysOnTop || false;
        document.getElementById('hideTopbar').checked = this.settings.hideTopbar || false;
        document.getElementById('hardwareAccel').checked = this.settings.hardwareAccel !== false;
        document.getElementById('cacheEnabled').checked = this.settings.cacheEnabled !== false;
        document.getElementById('blockCookies').checked = this.settings.blockCookies || false;
        document.getElementById('clearOnClose').checked = this.settings.clearOnClose || false;
    }
    
    saveSettings() {
        this.saveSettingsToStorage();
        this.toggleSettings();
        this.showNotification('Settings saved', 'success');
    }
    
    resetSettings() {
        this.settings = {
            alwaysOnTop: false,
            hideTopbar: false,
            hardwareAccel: true,
            cacheEnabled: true,
            blockCookies: false,
            clearOnClose: false,
            lastUrl: 'https://example.com',
            homeUrl: ''
        };
        this.applySettings();
        this.saveSettingsToStorage();
        this.showNotification('Settings reset to defaults', 'info');
    }
    
    loadSettings() {
        const saved = localStorage.getItem('vre-app-settings');
        if (saved) {
            try {
                return JSON.parse(saved);
            } catch (e) {
                console.error('Failed to load settings:', e);
            }
        }
        return {
            alwaysOnTop: false,
            hideTopbar: false,
            hardwareAccel: true,
            cacheEnabled: true,
            blockCookies: false,
            clearOnClose: false,
            lastUrl: 'https://example.com',
            homeUrl: ''
        };
    }
    
    saveSettingsToStorage() {
        localStorage.setItem('vre-app-settings', JSON.stringify(this.settings));
    }
    
    isValidUrl(string) {
        try {
            const url = new URL(string);
            return url.protocol === 'http:' || url.protocol === 'https:';
        } catch (_) {
            return false;
        }
    }
    
    formatUrl(url) {
        try {
            const urlObj = new URL(url);
            return urlObj.hostname + urlObj.pathname;
        } catch (_) {
            return url;
        }
    }
    
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Initialize app when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.vreApp = new VREApp();
    });
} else {
    window.vreApp = new VREApp();
}

// Console branding
console.log('%c⚙️ VRE Application', 'font-size: 18px; font-weight: bold; color: #3b82f6;');
console.log('%cBrowser-Less Wrapper v1.0.0', 'font-size: 12px; color: #9ca3af;');
console.log('%c\n📋 Keyboard Shortcuts:', 'font-weight: bold; color: #e4e6eb; margin-top: 8px;');
console.log('%cCtrl+R / F5    - Refresh', 'color: #9ca3af;');
console.log('%cCtrl+W         - Close', 'color: #9ca3af;');
console.log('%cCtrl+B         - Toggle topbar', 'color: #9ca3af;');
console.log('%cF11            - Fullscreen', 'color: #9ca3af;');
console.log('%cCtrl +/-/0     - Zoom controls', 'color: #9ca3af;');
