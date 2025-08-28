-- Migration: Crear tabla rate_limit_attempts para control de rate limiting
-- Fecha: 2025-08-28
-- Descripción: Tabla para manejar intentos y rate limiting por IP/usuario/acción

CREATE TABLE IF NOT EXISTS `rate_limit_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` varchar(64) NOT NULL COMMENT 'Hash único del cliente (IP + User-Agent + Email)',
  `action` varchar(50) NOT NULL COMMENT 'Tipo de acción (login, otp, password_reset, etc)',
  `ip_address` varchar(45) NOT NULL COMMENT 'Dirección IP del cliente',
  `user_agent` text COMMENT 'User Agent del navegador',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_client_action_time` (`client_id`, `action`, `created_at`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_ip_action` (`ip_address`, `action`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices adicionales para optimización
ALTER TABLE `rate_limit_attempts` 
ADD INDEX `idx_cleanup` (`created_at`),
ADD INDEX `idx_client_id` (`client_id`);

-- Comentarios de documentación
ALTER TABLE `rate_limit_attempts` COMMENT = 'Tabla para control de rate limiting y protección contra ataques de fuerza bruta';