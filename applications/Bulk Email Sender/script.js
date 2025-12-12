// script.js

// State management
let appState = {
    csvData: [],
    smtpConfig: {
      host: 'smtp.gmail.com',
      port: '587',
      username: '',
      password: '',
      fromEmail: '',
      fromName: ''
    },
    emailTemplate: {
      subject: '',
      body: ''
    },
    isSending: false
  };
  
  // DOM Elements
  const elements = {
    tabs: document.querySelectorAll('.tab'),
    tabContents: document.querySelectorAll('.tab-content'),
    csvFileInput: document.getElementById('csvFile'),
    uploadArea: document.getElementById('uploadArea'),
    csvStatus: document.getElementById('csvStatus'),
    emailSubject: document.getElementById('emailSubject'),
    emailBody: document.getElementById('emailBody'),
    availableFields: document.getElementById('availableFields'),
    previewBox: document.getElementById('previewBox'),
    previewSubject: document.getElementById('previewSubject'),
    previewBody: document.getElementById('previewBody'),
    recipientCount: document.getElementById('recipientCount'),
    subjectPreview: document.getElementById('subjectPreview'),
    fromPreview: document.getElementById('fromPreview'),
    sendButton: document.getElementById('sendButton'),
    sendButtonText: document.getElementById('sendButtonText'),
    sendStatusContainer: document.getElementById('sendStatusContainer'),
    sendStatusList: document.getElementById('sendStatusList'),
    smtpHost: document.getElementById('smtpHost'),
    smtpPort: document.getElementById('smtpPort'),
    smtpUsername: document.getElementById('smtpUsername'),
    smtpPassword: document.getElementById('smtpPassword'),
    fromEmail: document.getElementById('fromEmail'),
    fromName: document.getElementById('fromName')
  };
  
  // Tab switching
  elements.tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const targetTab = tab.dataset.tab;
      
      // Update active tab
      elements.tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      
      // Update active content
      elements.tabContents.forEach(content => {
        content.classList.remove('active');
        if (content.id === targetTab) {
          content.classList.add('active');
        }
      });
  
      // Update preview if on compose tab
      if (targetTab === 'compose') {
        updatePreview();
      }
  
      // Update send tab stats
      if (targetTab === 'send') {
        updateSendStats();
      }
    });
  });
  
  // File upload handling
  elements.uploadArea.addEventListener('click', () => {
    elements.csvFileInput.click();
  });
  
  elements.csvFileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
  
    const reader = new FileReader();
    reader.onload = (event) => {
      const text = event.target.result;
      parseCSV(text);
    };
    reader.readAsText(file);
  });
  
  // Parse CSV
  function parseCSV(text) {
    const lines = text.split('\n').filter(line => line.trim());
    
    if (lines.length < 2) {
      alert('CSV must have headers and at least one data row');
      return;
    }
  
    const headers = lines[0].split(',').map(h => h.trim());
    const data = lines.slice(1).map(line => {
      const values = line.split(',').map(v => v.trim());
      const obj = {};
      headers.forEach((header, i) => {
        obj[header] = values[i] || '';
      });
      return obj;
    });
  
    appState.csvData = data;
    displayCSVStatus();
    updateAvailableFields();
  }
  
  // Display CSV upload status
  function displayCSVStatus() {
    const count = appState.csvData.length;
    const fields = getAvailableFields().join(', ');
    
    elements.csvStatus.innerHTML = `
      <p>✓ Loaded ${count} recipient${count !== 1 ? 's' : ''}</p>
      <small>Available fields: ${fields}</small>
    `;
    elements.csvStatus.style.display = 'block';
  }
  
  // Get available CSV fields
  function getAvailableFields() {
    if (appState.csvData.length === 0) return [];
    return Object.keys(appState.csvData[0]);
  }
  
  // Update available fields display
  function updateAvailableFields() {
    const fields = getAvailableFields();
    if (fields.length > 0) {
      elements.availableFields.textContent = ' Available: ' + fields.map(f => `{${f}}`).join(', ');
    } else {
      elements.availableFields.textContent = '';
    }
  }
  
  // Personalize text with CSV data
  function personalizeText(template, data) {
    let result = template;
    Object.keys(data).forEach(key => {
      const regex = new RegExp(`\\{${key}\\}`, 'g');
      result = result.replace(regex, data[key]);
    });
    return result;
  }
  
  // Update email preview
  function updatePreview() {
    if (appState.csvData.length === 0) {
      elements.previewBox.style.display = 'none';
      return;
    }
  
    const subject = elements.emailSubject.value;
    const body = elements.emailBody.value;
  
    if (!subject && !body) {
      elements.previewBox.style.display = 'none';
      return;
    }
  
    const firstRecipient = appState.csvData[0];
    const personalizedSubject = personalizeText(subject, firstRecipient);
    const personalizedBody = personalizeText(body, firstRecipient);
  
    elements.previewSubject.textContent = personalizedSubject;
    elements.previewBody.textContent = personalizedBody;
    elements.previewBox.style.display = 'block';
  }
  
  // Update email template on input
  elements.emailSubject.addEventListener('input', () => {
    appState.emailTemplate.subject = elements.emailSubject.value;
    updatePreview();
    updateSendStats();
  });
  
  elements.emailBody.addEventListener('input', () => {
    appState.emailTemplate.body = elements.emailBody.value;
    updatePreview();
    updateSendStats();
  });
  
  // Update SMTP config
  [elements.smtpHost, elements.smtpPort, elements.smtpUsername, 
   elements.smtpPassword, elements.fromEmail, elements.fromName].forEach(input => {
    input.addEventListener('input', () => {
      appState.smtpConfig = {
        host: elements.smtpHost.value,
        port: elements.smtpPort.value,
        username: elements.smtpUsername.value,
        password: elements.smtpPassword.value,
        fromEmail: elements.fromEmail.value,
        fromName: elements.fromName.value
      };
      updateSendStats();
    });
  });
  
  // Update send tab statistics
  function updateSendStats() {
    const count = appState.csvData.length;
    if (elements.recipientCount) {
      elements.recipientCount.textContent = String(count);
    }
    if (elements.subjectPreview) {
      elements.subjectPreview.textContent = appState.emailTemplate.subject || 'Not set';
    }
    if (elements.fromPreview) {
      elements.fromPreview.textContent = appState.smtpConfig.fromEmail || 'Not set';
    }
    if (elements.sendButtonText) {
      elements.sendButtonText.textContent = `Send ${count} Email${count !== 1 ? 's' : ''}`;
    }
  }

  // Simulate sending an email (demo only)
  function simulateSendEmail(recipient, personalizedSubject, personalizedBody) {
    return new Promise((resolve) => {
      setTimeout(() => {
        const success = Math.random() > 0.1;
        resolve({
          email: recipient.Email || recipient.email || 'unknown',
          success,
          message: success ? 'Sent successfully' : 'Failed to send'
        });
      }, Math.random() * 1000 + 500);
    });
  }

  // Handle send button click
  elements.sendButton.addEventListener('click', async () => {
    if (appState.isSending) return;

    const subject = appState.emailTemplate.subject || elements.emailSubject.value;
    const body = appState.emailTemplate.body || elements.emailBody.value;

    if (!subject || !body) {
      alert('Please fill in both subject and body');
      return;
    }
    if (appState.csvData.length === 0) {
      alert('Please upload a CSV file with recipients');
      return;
    }
    if (!appState.smtpConfig.username || !appState.smtpConfig.password) {
      alert('Please configure SMTP settings');
      return;
    }

    appState.isSending = true;
    elements.sendButton.setAttribute('disabled', 'true');
    elements.sendButton.classList.add('disabled');
    if (elements.sendStatusContainer) {
      elements.sendStatusContainer.style.display = 'block';
    }
    if (elements.sendStatusList) {
      elements.sendStatusList.innerHTML = '';
    }

    for (const recipient of appState.csvData) {
      const personalizedSubject = personalizeText(subject, recipient);
      const personalizedBody = personalizeText(body, recipient);

      // Demo send
      const result = await simulateSendEmail(recipient, personalizedSubject, personalizedBody);

      const item = document.createElement('div');
      item.className = `status-item ${result.success ? 'success' : 'error'}`;
      const left = document.createElement('div');
      left.className = 'status-left';
      const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      icon.setAttribute('class', 'icon');
      icon.setAttribute('viewBox', '0 0 24 24');
      icon.setAttribute('fill', 'none');
      icon.setAttribute('stroke', 'currentColor');
      icon.setAttribute('stroke-width', '2');
      const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
      path.setAttribute('d', result.success ? 'M20 6 9 17l-5-5' : 'M18 6 6 18M6 6l12 12');
      icon.appendChild(path);
      const emailSpan = document.createElement('span');
      emailSpan.className = 'status-email';
      emailSpan.textContent = result.email;
      left.appendChild(icon);
      left.appendChild(emailSpan);
      const msg = document.createElement('span');
      msg.className = `status-message ${result.success ? 'success' : 'error'}`;
      msg.textContent = result.message;
      item.appendChild(left);
      item.appendChild(msg);
      if (elements.sendStatusList) {
        elements.sendStatusList.appendChild(item);
      }

      // Update button text as we go
      const remaining = Math.max(0, appState.csvData.length - (elements.sendStatusList?.children.length || 0));
      if (elements.sendButtonText) {
        elements.sendButtonText.textContent = remaining > 0 ? `Sending... (${remaining} left)` : 'Done';
      }
    }

    appState.isSending = false;
    elements.sendButton.removeAttribute('disabled');
    elements.sendButton.classList.remove('disabled');
    updateSendStats();
  });