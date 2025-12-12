import React, { useState } from 'react';
import { Mail, Upload, Send, Settings, AlertCircle, CheckCircle, X } from 'lucide-react';

export default function BulkEmailSender() {
  const [activeTab, setActiveTab] = useState('setup');
  const [smtpConfig, setSmtpConfig] = useState({
    host: 'smtp.gmail.com',
    port: '587',
    username: '',
    password: '',
    fromEmail: '',
    fromName: ''
  });
  const [csvData, setCsvData] = useState([]);
  const [emailTemplate, setEmailTemplate] = useState({
    subject: '',
    body: ''
  });
  const [sendStatus, setSendStatus] = useState([]);
  const [isSending, setIsSending] = useState(false);

  const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (event) => {
      const text = event.target.result;
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

      setCsvData(data);
    };
    reader.readAsText(file);
  };

  const getAvailableFields = () => {
    if (csvData.length === 0) return [];
    return Object.keys(csvData[0]);
  };

  const simulateSendEmail = (recipient, personalizedSubject, personalizedBody) => {
    return new Promise((resolve) => {
      setTimeout(() => {
        const success = Math.random() > 0.1;
        resolve({
          email: recipient,
          success,
          message: success ? 'Sent successfully' : 'Failed to send'
        });
      }, Math.random() * 1000 + 500);
    });
  };

  const personalizeText = (template, data) => {
    let result = template;
    Object.keys(data).forEach(key => {
      const regex = new RegExp(`\\{${key}\\}`, 'g');
      result = result.replace(regex, data[key]);
    });
    return result;
  };

  const handleSendEmails = async () => {
    if (!emailTemplate.subject || !emailTemplate.body) {
      alert('Please fill in both subject and body');
      return;
    }
    if (csvData.length === 0) {
      alert('Please upload a CSV file with recipients');
      return;
    }
    if (!smtpConfig.username || !smtpConfig.password) {
      alert('Please configure SMTP settings');
      return;
    }

    setIsSending(true);
    setSendStatus([]);
    const statuses = [];

    for (const recipient of csvData) {
      const personalizedSubject = personalizeText(emailTemplate.subject, recipient);
      const personalizedBody = personalizeText(emailTemplate.body, recipient);
      
      const result = await simulateSendEmail(
        recipient.Email || recipient.email,
        personalizedSubject,
        personalizedBody
      );
      
      statuses.push(result);
      setSendStatus([...statuses]);
    }

    setIsSending(false);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
      <div className="max-w-6xl mx-auto">
        <div className="bg-white rounded-2xl shadow-xl overflow-hidden">
          {/* Header */}
          <div className="bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
            <div className="flex items-center gap-3">
              <Mail className="w-8 h-8 text-white" />
              <h1 className="text-3xl font-bold text-white">Bulk Email Sender</h1>
            </div>
            <p className="text-blue-100 mt-2">Send personalized emails to multiple recipients</p>
          </div>

          {/* Tabs */}
          <div className="flex border-b">
            {['setup', 'compose', 'send'].map((tab) => (
              <button
                key={tab}
                onClick={() => setActiveTab(tab)}
                className={`flex-1 py-4 px-6 font-medium transition-colors ${
                  activeTab === tab
                    ? 'border-b-2 border-blue-600 text-blue-600'
                    : 'text-gray-500 hover:text-gray-700'
                }`}
              >
                {tab === 'setup' && <Settings className="w-5 h-5 inline mr-2" />}
                {tab === 'compose' && <Mail className="w-5 h-5 inline mr-2" />}
                {tab === 'send' && <Send className="w-5 h-5 inline mr-2" />}
                {tab.charAt(0).toUpperCase() + tab.slice(1)}
              </button>
            ))}
          </div>

          {/* Content */}
          <div className="p-6">
            {/* Setup Tab */}
            {activeTab === 'setup' && (
              <div className="space-y-6">
                <div>
                  <h2 className="text-xl font-semibold mb-4 flex items-center gap-2">
                    <Settings className="w-5 h-5" />
                    SMTP Configuration
                  </h2>
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">
                        SMTP Host
                      </label>
                      <input
                        type="text"
                        value={smtpConfig.host}
                        onChange={(e) => setSmtpConfig({...smtpConfig, host: e.target.value})}
                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="smtp.gmail.com"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">
                        Port
                      </label>
                      <input
                        type="text"
                        value={smtpConfig.port}
                        onChange={(e) => setSmtpConfig({...smtpConfig, port: e.target.value})}
                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="587"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">
                        Username / Email
                      </label>
                      <input
                        type="email"
                        value={smtpConfig.username}
                        onChange={(e) => setSmtpConfig({...smtpConfig, username: e.target.value})}
                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="your@email.com"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">
                        Password / App Password
                      </label>
                      <input
                        type="password"
                        value={smtpConfig.password}
                        onChange={(e) => setSmtpConfig({...smtpConfig, password: e.target.value})}
                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">
                        From Email
                      </label>
                      <input
                        type="email"
                        value={smtpConfig.fromEmail}
                        onChange={(e) => setSmtpConfig({...smtpConfig, fromEmail: e.target.value})}
                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="sender@yourdomain.com"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">
                        From Name
                      </label>
                      <input
                        type="text"
                        value={smtpConfig.fromName}
                        onChange={(e) => setSmtpConfig({...smtpConfig, fromName: e.target.value})}
                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Your Company"
                      />
                    </div>
                  </div>
                </div>

                <div>
                  <h2 className="text-xl font-semibold mb-4 flex items-center gap-2">
                    <Upload className="w-5 h-5" />
                    Upload Recipients CSV
                  </h2>
                  <div className="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                    <Upload className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <input
                      type="file"
                      accept=".csv"
                      onChange={handleFileUpload}
                      className="hidden"
                      id="csv-upload"
                    />
                    <label
                      htmlFor="csv-upload"
                      className="cursor-pointer text-blue-600 hover:text-blue-700 font-medium"
                    >
                      Click to upload CSV
                    </label>
                    <p className="text-sm text-gray-500 mt-2">
                      CSV should have headers like: Email, FirstName, LastName, etc.
                    </p>
                  </div>
                  {csvData.length > 0 && (
                    <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                      <p className="text-green-800 font-medium">
                        ✓ Loaded {csvData.length} recipients
                      </p>
                      <p className="text-sm text-green-600 mt-1">
                        Available fields: {getAvailableFields().join(', ')}
                      </p>
                    </div>
                  )}
                </div>
              </div>
            )}

            {/* Compose Tab */}
            {activeTab === 'compose' && (
              <div className="space-y-6">
                <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <p className="text-sm text-blue-800">
                    <strong>💡 Tip:</strong> Use curly braces to insert personalized fields from your CSV.
                    {csvData.length > 0 && (
                      <span> Available: {getAvailableFields().map(f => `{${f}}`).join(', ')}</span>
                    )}
                  </p>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Email Subject
                  </label>
                  <input
                    type="text"
                    value={emailTemplate.subject}
                    onChange={(e) => setEmailTemplate({...emailTemplate, subject: e.target.value})}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Hello {FirstName}, here's your update!"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Email Body
                  </label>
                  <textarea
                    value={emailTemplate.body}
                    onChange={(e) => setEmailTemplate({...emailTemplate, body: e.target.value})}
                    rows={12}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                    placeholder="Hi {FirstName},&#10;&#10;Thank you for being a valued customer...&#10;&#10;Best regards,&#10;{FromName}"
                  />
                </div>

                {csvData.length > 0 && (
                  <div className="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 className="font-semibold text-gray-700 mb-2">Preview (First Recipient)</h3>
                    <div className="bg-white p-4 rounded border">
                      <p className="text-sm text-gray-600 mb-2">
                        <strong>Subject:</strong> {personalizeText(emailTemplate.subject, csvData[0])}
                      </p>
                      <div className="text-sm text-gray-800 whitespace-pre-wrap">
                        {personalizeText(emailTemplate.body, csvData[0])}
                      </div>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* Send Tab */}
            {activeTab === 'send' && (
              <div className="space-y-6">
                <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                  <h2 className="text-xl font-semibold mb-4">Ready to Send</h2>
                  <div className="grid grid-cols-3 gap-4 mb-4">
                    <div className="bg-white p-4 rounded-lg shadow-sm">
                      <p className="text-sm text-gray-600">Recipients</p>
                      <p className="text-2xl font-bold text-blue-600">{csvData.length}</p>
                    </div>
                    <div className="bg-white p-4 rounded-lg shadow-sm">
                      <p className="text-sm text-gray-600">Subject</p>
                      <p className="text-lg font-semibold truncate">{emailTemplate.subject || 'Not set'}</p>
                    </div>
                    <div className="bg-white p-4 rounded-lg shadow-sm">
                      <p className="text-sm text-gray-600">From</p>
                      <p className="text-lg font-semibold truncate">{smtpConfig.fromEmail || 'Not set'}</p>
                    </div>
                  </div>
                  
                  <button
                    onClick={handleSendEmails}
                    disabled={isSending || csvData.length === 0}
                    className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all"
                  >
                    <Send className="w-5 h-5" />
                    {isSending ? 'Sending...' : `Send ${csvData.length} Emails`}
                  </button>
                </div>

                {sendStatus.length > 0 && (
                  <div className="space-y-2">
                    <h3 className="font-semibold text-gray-700 mb-3">Send Status</h3>
                    <div className="max-h-96 overflow-y-auto space-y-2">
                      {sendStatus.map((status, idx) => (
                        <div
                          key={idx}
                          className={`p-4 rounded-lg border flex items-center justify-between ${
                            status.success
                              ? 'bg-green-50 border-green-200'
                              : 'bg-red-50 border-red-200'
                          }`}
                        >
                          <div className="flex items-center gap-3">
                            {status.success ? (
                              <CheckCircle className="w-5 h-5 text-green-600" />
                            ) : (
                              <AlertCircle className="w-5 h-5 text-red-600" />
                            )}
                            <span className="font-medium">{status.email}</span>
                          </div>
                          <span className={`text-sm ${status.success ? 'text-green-600' : 'text-red-600'}`}>
                            {status.message}
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>

        {/* Instructions */}
        <div className="mt-6 bg-white rounded-lg shadow p-6">
          <h3 className="font-semibold text-lg mb-3">Quick Start Guide</h3>
          <ol className="space-y-2 text-sm text-gray-700">
            <li><strong>1. Setup:</strong> Configure your SMTP settings (for Gmail, use an App Password)</li>
            <li><strong>2. Upload CSV:</strong> Upload a CSV file with Email column and any custom fields</li>
            <li><strong>3. Compose:</strong> Write your email template using {'{FieldName}'} for personalization</li>
            <li><strong>4. Send:</strong> Review and send your emails</li>
          </ol>
          <div className="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
            <p className="text-sm text-yellow-800">
              <strong>Note:</strong> This is a demo interface. In production, connect to a real SMTP server and handle authentication securely.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}