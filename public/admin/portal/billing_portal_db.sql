-- Enhanced Database Structure for Billing & Payment Automation
-- Database: clientportal

-- Core clients table with billing information
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    company VARCHAR(100),
    website VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    package_name VARCHAR(100),
    monthly_amount DECIMAL(10,2) DEFAULT 0.00,
    next_due_date DATE,
    billing_cycle ENUM('monthly', 'quarterly', 'annually') DEFAULT 'monthly',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Enhanced invoices table
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    due_date DATE NOT NULL,
    issue_date DATE NOT NULL,
    paid_date DATETIME NULL,
    description TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_client_status (client_id, status),
    INDEX idx_due_date (due_date),
    INDEX idx_invoice_number (invoice_number)
);

-- Payment records table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    client_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    gateway ENUM('payfast', 'stripe', 'paypal', 'manual', 'bank_transfer') NOT NULL,
    gateway_transaction_id VARCHAR(255),
    gateway_reference VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_client_date (client_id, payment_date),
    INDEX idx_gateway_ref (gateway_transaction_id)
);

-- Invoice line items for detailed billing
CREATE TABLE invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(8,2) DEFAULT 1.00,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- Enhanced staff table
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'accountant') DEFAULT 'staff',
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Services/packages table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) DEFAULT 0.00,
    billing_cycle ENUM('one_time', 'monthly', 'quarterly', 'annually') DEFAULT 'monthly',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Enhanced projects table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    service_id INT,
    title VARCHAR(150) NOT NULL,
    status ENUM('active', 'pending', 'completed', 'on_hold') DEFAULT 'pending',
    progress INT DEFAULT 0,
    start_date DATE,
    deadline DATE,
    description TEXT,
    budget DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

-- Enhanced messages table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_id INT NOT NULL,
    to_id INT NOT NULL,
    from_type ENUM('client', 'staff') NOT NULL,
    to_type ENUM('client', 'staff') NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recipient (to_id, to_type, is_read)
);

-- Email notifications log
CREATE TABLE email_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    invoice_id INT,
    type ENUM('invoice_created', 'payment_reminder', 'payment_received', 'overdue_notice') NOT NULL,
    email_to VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    sent_at DATETIME NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- File uploads table
CREATE TABLE client_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100),
    uploaded_by_type ENUM('client', 'staff') DEFAULT 'client',
    uploaded_by_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Cron job logs for automation tracking
CREATE TABLE automation_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_type ENUM('invoice_generation', 'payment_reminder', 'overdue_check') NOT NULL,
    status ENUM('success', 'error', 'partial') NOT NULL,
    details TEXT,
    records_processed INT DEFAULT 0,
    execution_time DECIMAL(8,3),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO staff (name, email, password, role) VALUES 
('Admin User', 'admin@scl.com', 'password', 'admin');

-- Insert sample services
INSERT INTO services (name, description, price, billing_cycle) VALUES 
('Website Development', 'Complete website development package', 15000.00, 'one_time'),
('Monthly Maintenance', 'Website maintenance and updates', 2500.00, 'monthly'),
('SEO Package', 'Search engine optimization services', 3500.00, 'monthly'),
('Social Media Management', 'Complete social media management', 4000.00, 'monthly'),
('Branding Package', 'Complete brand identity design', 8000.00, 'one_time');

-- Insert sample client
INSERT INTO clients (name, email, password, company, package_name, monthly_amount, next_due_date) VALUES 
('John Doe', 'client@example.com', 'password', 'Example Corp', 'Premium Package', 5000.00, '2024-10-01');
