function nextStep(step, template) {
    if (template) {
        document.getElementById('selected-template').innerText = template;
    }

    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));

    // Show current step
    document.getElementById('step' + step).classList.add('active');
    
    // Update stepper indicators
    for (let i = 1; i <= step; i++) {
        document.getElementById('step' + i + '-indicator').classList.add('active');
    }

    if (step === 3) {
        startSimulatedDeployment();
    }
}

function prevStep(step) {
    document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
    
    // Remove active state from higher steps
    for (let i = step + 1; i <= 3; i++) {
        document.getElementById('step' + i + '-indicator').classList.remove('active');
    }
}

function startSimulatedDeployment() {
    const logs = [
        "Provisioning isolated container...",
        "Setting up network interface...",
        "Cloning repository...",
        "Installing dependencies (this may take a moment)...",
        "Building project...",
        "Optimizing assets...",
        "Configuring SSL certificates...",
        "Deploying to edge nodes...",
        "Verifying health checks...",
        "Deployment COMPLETE."
    ];

    const terminal = document.getElementById('terminal-logs');
    const progress = document.getElementById('deploy-progress');
    let currentLog = 0;

    const interval = setInterval(() => {
        if (currentLog < logs.length) {
            const line = document.createElement('div');
            line.className = 'log-line';
            if (currentLog === logs.length - 1) line.className += ' success';
            line.innerText = `> ${logs[currentLog]}`;
            terminal.appendChild(line);
            terminal.scrollTop = terminal.scrollHeight;
            
            // Update progress
            const percent = ((currentLog + 1) / logs.length) * 100;
            progress.style.width = percent + '%';
            
            currentLog++;
        } else {
            clearInterval(interval);
            document.getElementById('success-message').style.display = 'block';
        }
    }, 800);
}
