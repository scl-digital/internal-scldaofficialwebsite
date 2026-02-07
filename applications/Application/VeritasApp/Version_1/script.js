document.addEventListener('DOMContentLoaded', () => {
    // --- State & Auth Check ---
    let currentUser = JSON.parse(localStorage.getItem('vre_user')) || null;
    let isPro = localStorage.getItem('vre_pro') === 'true';

    // Force redirect to login if not authenticated
    if (!currentUser && !window.location.href.includes('login.html')) {
        window.location.href = 'login.html';
        return;
    }

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

    // Premium UI Elements
    const accountBtn = document.getElementById('accountBtn');
    const accountLabel = document.getElementById('accountLabel');
    const proBadge = document.getElementById('proBadge');
    const subModal = document.getElementById('subModal');
    const closeSubBtn = document.getElementById('closeSubBtn');

    updateUIForAuthState();

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

    // Account Button Click (Logout)
    accountBtn.addEventListener('click', () => {
        if (currentUser) {
            if (confirm(`Logged in as ${currentUser.name}. Logout?`)) {
                logout();
            }
        }
    });

    closeSubBtn.addEventListener('click', () => subModal.classList.add('hidden'));

    // --- Build Gating Logic ---
    buildBtn.addEventListener('click', async () => {
        if (!currentUser) {
            window.location.href = 'login.html';
            return;
        }

        if (!isPro) {
            subModal.classList.remove('hidden');
            initPayPal();
            return;
        }

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

    // --- Functions ---

    function logout() {
        localStorage.removeItem('vre_user');
        localStorage.removeItem('vre_pro');
        window.location.href = 'login.html';
    }

    function updateUIForAuthState() {
        if (currentUser) {
            accountLabel.textContent = `Hi, ${currentUser.name}`;
            if (isPro) {
                proBadge.classList.remove('hidden');
                buildBtn.disabled = false;
                buildBtn.title = "";
            } else {
                proBadge.classList.add('hidden');
                buildBtn.disabled = true;
                buildBtn.title = "Upgrade to Pro to build";
            }
        }
    }

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

    function initPayPal() {
        if (window.paypal && !document.getElementById('paypal-button-container').hasChildNodes()) {
            paypal.Buttons({
                createOrder: (data, actions) => {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '9.99'
                            },
                            description: 'VRE Pro Subscription - 1 Year'
                        }]
                    });
                },
                onApprove: (data, actions) => {
                    return actions.order.capture().then(details => {
                        handlePaymentSuccess(details);
                    });
                }
            }).render('#paypal-button-container');
        }
    }

    function handlePaymentSuccess(details) {
        isPro = true;
        localStorage.setItem('vre_pro', 'true');
        updateUIForAuthState();
        subModal.classList.add('hidden');
        alert('Welcome to VRE Pro! You can now build your applications.');
    }

    closeModalBtn.addEventListener('click', () => {
        buildModal.classList.add('hidden');
    });

    function wait(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
});
