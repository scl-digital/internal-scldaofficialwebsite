-- Region Blocking Table for SCL Website Compliance
-- This table stores blocked regions/countries for compliance purposes

CREATE TABLE IF NOT EXISTS `blocked_regions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(2) NOT NULL COMMENT 'ISO 3166-1 alpha-2 country code',
  `country_name` varchar(100) NOT NULL COMMENT 'Full country name',
  `reason` text DEFAULT NULL COMMENT 'Reason for blocking (compliance, license, etc.)',
  `blocked_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'When the region was blocked',
  `blocked_by` varchar(50) DEFAULT NULL COMMENT 'Admin who blocked the region',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_country` (`country_code`),
  KEY `idx_blocked_at` (`blocked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Regions blocked for compliance reasons';
