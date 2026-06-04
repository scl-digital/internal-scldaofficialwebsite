-- Event Bookings Table
CREATE TABLE IF NOT EXISTS `event_bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `event_id` INT NOT NULL,
  `booking_reference` VARCHAR(50) UNIQUE NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_email` VARCHAR(255) NOT NULL,
  `organization` VARCHAR(255),
  `ticket_count` INT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'ZAR',
  `paypal_order_id` VARCHAR(255),
  `payment_status` VARCHAR(50) DEFAULT 'completed',
  `qr_code_data` TEXT,
  `ticket_pdf_path` VARCHAR(500),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_event_id` (`event_id`),
  INDEX `idx_booking_reference` (`booking_reference`),
  INDEX `idx_customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Email Settings Table (for admin configuration)
CREATE TABLE IF NOT EXISTS `email_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `smtp_host` VARCHAR(255) NOT NULL,
  `smtp_port` INT NOT NULL DEFAULT 587,
  `smtp_username` VARCHAR(255) NOT NULL,
  `smtp_password` VARCHAR(500) NOT NULL,
  `smtp_encryption` VARCHAR(10) DEFAULT 'tls',
  `from_email` VARCHAR(255) NOT NULL,
  `from_name` VARCHAR(255) NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default email settings (update these with actual values)
INSERT INTO `email_settings` (`smtp_host`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_encryption`, `from_email`, `from_name`)
VALUES ('smtp.gmail.com', 587, 'your-email@gmail.com', '', 'tls', 'your-email@gmail.com', 'SCL Digital Agency')
ON DUPLICATE KEY UPDATE `id`=`id`;
