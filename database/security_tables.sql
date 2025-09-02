-- Crear tabla de auditoría centralizada
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) NULL,
  `record_id` varchar(50) NULL,
  `old_values` JSON NULL,
  `new_values` JSON NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_table_record` (`table_name`, `record_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de logs de seguridad
CREATE TABLE IF NOT EXISTS `security_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` enum('LOGIN_SUCCESS','LOGIN_FAILED','LOGOUT','RATE_LIMIT','ACCESS_DENIED','PASSWORD_CHANGE','EMAIL_CHANGE','SUSPICIOUS_ACTIVITY') NOT NULL,
  `user_id` int(11) NULL,
  `email` varchar(150) NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `details` JSON,
  `severity` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'MEDIUM',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_severity` (`severity`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
