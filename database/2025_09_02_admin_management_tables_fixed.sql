-- ====================================================================
-- MIGRACIÓN: Gestión de Roles para Administrador (CORREGIDA)
-- Fecha: 2025-09-02
-- Descripción: Tablas para suscripciones, reportes de acceso y bloqueo de usuarios
-- ====================================================================

-- --------------------------------------------------------
-- Tabla para gestión de suscripciones
-- --------------------------------------------------------

CREATE TABLE `suscripciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `tipo_plan` enum('basico','premium','profesional') NOT NULL DEFAULT 'basico',
  `fecha_inicio` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('activa','suspendida','cancelada','expirada') NOT NULL DEFAULT 'activa',
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `caracteristicas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`caracteristicas`)),
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_vencimiento` (`fecha_vencimiento`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla para reportes de acceso detallados
-- --------------------------------------------------------

CREATE TABLE `reportes_acceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `recurso_tipo` enum('curso','material','libro','laboratorio','componente') NOT NULL,
  `recurso_id` int(11) NOT NULL,
  `recurso_nombre` varchar(255) DEFAULT NULL,
  `accion` enum('visualizar','descargar','completar','inscribir','acceder') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `duracion_sesion` int(11) DEFAULT NULL COMMENT 'Duración en segundos',
  `datos_adicionales` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_adicionales`)),
  `fecha_acceso` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_recurso_tipo_id` (`recurso_tipo`, `recurso_id`),
  KEY `idx_fecha_acceso` (`fecha_acceso`),
  KEY `idx_accion` (`accion`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Actualizar tabla usuarios para bloqueo/desbloqueo
-- --------------------------------------------------------

ALTER TABLE `usuarios` 
ADD COLUMN `bloqueado` tinyint(1) NOT NULL DEFAULT 0 AFTER `estado`,
ADD COLUMN `motivo_bloqueo` varchar(255) DEFAULT NULL AFTER `bloqueado`,
ADD COLUMN `fecha_bloqueo` timestamp NULL DEFAULT NULL AFTER `motivo_bloqueo`,
ADD COLUMN `bloqueado_por` int(11) DEFAULT NULL AFTER `fecha_bloqueo`,
ADD KEY `idx_bloqueado` (`bloqueado`),
ADD KEY `idx_fecha_bloqueo` (`fecha_bloqueo`);

-- Agregar foreign key para bloqueado_por
ALTER TABLE `usuarios` 
ADD CONSTRAINT `fk_usuarios_bloqueado_por` FOREIGN KEY (`bloqueado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL;

-- --------------------------------------------------------
-- Insertar datos de prueba para suscripciones
-- --------------------------------------------------------

INSERT INTO `suscripciones` (`usuario_id`, `tipo_plan`, `fecha_inicio`, `fecha_vencimiento`, `estado`, `precio`, `metodo_pago`, `descripcion`, `caracteristicas`) VALUES
(1, 'premium', '2025-09-01', '2025-12-01', 'activa', 99.00, 'tarjeta_credito', 'Suscripción Premium trimestral', '{"acceso_completo": true, "descargas_ilimitadas": true, "soporte_prioritario": true}'),
(2, 'basico', '2025-09-02', '2025-10-02', 'activa', 29.00, 'paypal', 'Suscripción Básica mensual', '{"acceso_basico": true, "descargas_limitadas": 10, "soporte_email": true}'),
(3, 'profesional', '2025-08-15', '2026-08-15', 'activa', 299.00, 'transferencia', 'Suscripción Profesional anual', '{"acceso_completo": true, "descargas_ilimitadas": true, "soporte_24_7": true, "certificados": true}');

-- --------------------------------------------------------
-- Insertar datos de prueba para reportes de acceso
-- --------------------------------------------------------

INSERT INTO `reportes_acceso` (`usuario_id`, `recurso_tipo`, `recurso_id`, `recurso_nombre`, `accion`, `ip_address`, `user_agent`, `duracion_sesion`, `datos_adicionales`) VALUES
(1, 'curso', 82, 'Robótica desde Cero', 'visualizar', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 1800, '{"progreso": "25%", "tiempo_video": 450}'),
(2, 'material', 1, 'Manual Arduino', 'descargar', '192.168.1.101', 'Mozilla/5.0 (Android 11)', NULL, '{"tipo_archivo": "pdf", "tamaño_mb": 2.5}'),
(3, 'curso', 83, 'Robots Móviles', 'completar', '192.168.1.102', 'Mozilla/5.0 (iPhone)', 3600, '{"calificacion": 85, "intentos": 2}'),
(1, 'laboratorio', 1, 'Lab Sensores', 'acceder', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0)', 2400, '{"equipos_usados": ["Arduino", "DHT22"], "experimentos": 3}');

-- --------------------------------------------------------
-- Crear índices adicionales para optimización
-- --------------------------------------------------------

-- Índices compuestos para reportes frecuentes
CREATE INDEX `idx_reportes_usuario_fecha` ON `reportes_acceso` (`usuario_id`, `fecha_acceso`);
CREATE INDEX `idx_reportes_recurso_accion` ON `reportes_acceso` (`recurso_tipo`, `recurso_id`, `accion`);

-- Índices para suscripciones
CREATE INDEX `idx_suscripciones_usuario_estado` ON `suscripciones` (`usuario_id`, `estado`);
CREATE INDEX `idx_suscripciones_plan_estado` ON `suscripciones` (`tipo_plan`, `estado`);

-- --------------------------------------------------------
-- Comentarios finales
-- --------------------------------------------------------

-- Esta migración agrega las siguientes funcionalidades:
-- 1. Gestión completa de suscripciones con diferentes planes
-- 2. Sistema de reportes de acceso detallado para todos los recursos
-- 3. Funcionalidad de bloqueo/desbloqueo de usuarios
-- 4. Índices optimizados para consultas frecuentes
-- 5. Datos de prueba para testing

-- MIGRACIÓN EJECUTADA EXITOSAMENTE