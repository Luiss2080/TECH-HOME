-- Migration: Crear tabla codigos_otp para autenticación OTP
-- Fecha: 2025-08-27
-- Descripción: Tabla para almacenar códigos OTP de doble factor de autenticación

CREATE TABLE IF NOT EXISTS `codigos_otp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `expira_en` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_expira_en` (`expira_en`),
  KEY `idx_utilizado` (`utilizado`),
  CONSTRAINT `fk_codigos_otp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar campos de protección contra fuerza bruta a la tabla usuarios si no existen
ALTER TABLE `usuarios` 
ADD COLUMN IF NOT EXISTS `intentos_fallidos` int(11) NOT NULL DEFAULT 0,
ADD COLUMN IF NOT EXISTS `bloqueado_hasta` datetime NULL DEFAULT NULL;

-- Índices adicionales para optimización
ALTER TABLE `usuarios` 
ADD INDEX IF NOT EXISTS `idx_intentos_fallidos` (`intentos_fallidos`),
ADD INDEX IF NOT EXISTS `idx_bloqueado_hasta` (`bloqueado_hasta`);