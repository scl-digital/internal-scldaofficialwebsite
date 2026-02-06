document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const appUrlInput = document.getElementById('appUrl');
    const appNameInput = document.getElementById('appName');
    const iconInput = document.getElementById('iconInput');
    const iconUploadArea = document.getElementById('iconUploadArea');
    const iconPreviewContainer = document.getElementById('iconPreviewContainer');
    const iconPreview = document.getElementById('iconPreview');
    const removeIconBtn = document.getElementById('removeIconBtn');
    const buildBtn = document.getElementById('buildBtn');
    const buildProgress = document.getElementById('buildProgress');
    const platformCards = document.querySelectorAll('.platform-card');
    
    const previewName = document.getElementById('previewName');
    const previewUrl = document.getElementById('previewUrl');
    
    const buildModal = document.getElementById('buildModal');
    const buildLogs = document.getElementById('buildLogs');
    const modalProgressBar = document.getElementById('modalProgressBar');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // --- Interaction Logic ---

    // Live Preview Updates
    appUrlInput.addEventListener('input', (e) => {
        const val = e.target.value || 'https://your-app.com';
        previewUrl.textContent = val;
    });

    appNameInput.addEventListener('input', (e) => {
        const val = e.target.value || 'My App';
        previewName.textContent = val;
    });

    // Icon Upload
    iconUploadArea.addEventListener('click', (e) => {
        if (e.target !== removeIconBtn) {
            iconInput.click();
        }
    });

    iconInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                iconPreview.src = event.target.result;
                iconPreviewContainer.classList.remove('hidden');
                document.querySelector('.upload-placeholder').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    removeIconBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        iconInput.value = '';
        iconPreview.src = '';
        iconPreviewContainer.classList.add('hidden');
        document.querySelector('.upload-placeholder').classList.remove('hidden');
    });

    // Platform Selection
    platformCards.forEach(card => {
        card.addEventListener('click', () => {
            platformCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
        });
    });

    // --- Mock Build Process ---

    buildBtn.addEventListener('click', async () => {
        if (!appUrlInput.checkValidity()) {
            appUrlInput.reportValidity();
            return;
        }

        // Feedback on button
        buildBtn.disabled = true;
        buildProgress.classList.remove('hidden');
        buildBtn.querySelector('span').textContent = 'Preparing...';

        await wait(800);

        // Show Modal
        buildModal.classList.remove('hidden');
        runBuildSimulation();
    });

    async function runBuildSimulation() {
        buildLogs.innerHTML = '';
        modalProgressBar.style.width = '0%';
        closeModalBtn.classList.add('hidden');

        const logs = [
            'Initializing VRE Build Engine...',
            'Validating application endpoint...',
            'Compiling custom top-bar assets...',
            'Injecting branding resources...',
            'Optimizing Chromium shell for resource efficiency...',
            'Packaging for target platform: ' + document.querySelector('.platform-card.active span').textContent,
            'Signing application binary...',
            'Verifying integrity...',
            'Build Successful!'
        ];

        for (let i = 0; i < logs.length; i++) {
            const p = document.createElement('p');
            p.textContent = '> ' + logs[i];
            buildLogs.appendChild(p);
            buildLogs.scrollTop = buildLogs.scrollHeight;
            
            const progress = ((i + 1) / logs.length) * 100;
            modalProgressBar.style.width = progress + '%';
            
            await wait(Math.random() * 1000 + 500);
        }

        closeModalBtn.classList.remove('hidden');
        buildBtn.disabled = false;
        buildProgress.classList.add('hidden');
        buildBtn.querySelector('span').textContent = 'Build My Application';
    }

    closeModalBtn.addEventListener('click', () => {
        buildModal.classList.add('hidden');
    });

    // Helper
    function wait(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
});
