document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const authTabs = document.querySelectorAll('.auth-tab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const doLoginBtn = document.getElementById('doLoginBtn');
    const doRegisterBtn = document.getElementById('doRegisterBtn');

    const loginEmail = document.getElementById('loginEmail');
    const loginPass = document.getElementById('loginPass');
    const regName = document.getElementById('regName');
    const regEmail = document.getElementById('regEmail');
    const regPass = document.getElementById('regPass');

    // Tab Switching
    authTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            authTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            if (tab.dataset.tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
            }
        });
    });

    // Mock Login
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = loginEmail.value;
        const pass = loginPass.value;

        if (email && pass) {
            login({
                name: email.split('@')[0],
                email: email
            });
        }
    });

    // Mock Register
    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = regName.value;
        const email = regEmail.value;
        const pass = regPass.value;

        if (name && email && pass) {
            login({
                name: name,
                email: email
            });
        }
    });

    function login(user) {
        // Show loading state
        const activeBtn = loginForm.classList.contains('hidden') ? doRegisterBtn : doLoginBtn;
        const originalText = activeBtn.textContent;
        activeBtn.disabled = true;
        activeBtn.textContent = 'Authenticating...';

        setTimeout(() => {
            // Save state
            localStorage.setItem('vre_user', JSON.stringify(user));

            // Redirect
            window.location.href = 'index.html';
        }, 1200);
    }
});
