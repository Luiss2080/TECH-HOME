-- ============================================================================
-- SCRIPT OPTIMIZADO DE BASE DE DATOS TECH HOME - VERSION LIMPIA
-- Fecha: 27 de Agosto de 2025
-- Descripción: Script limpio basado en funcionalidades realmente usadas
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS `tech_home` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tech_home`;

-- ============================================================================
-- 1. TABLA USUARIOS (NÚCLEO DEL SISTEMA)
-- ============================================================================

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `nombre_completo` varchar(200) GENERATED ALWAYS AS (CONCAT(`nombre`, ' ', `apellido`)) STORED,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `genero` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT 'Bolivia',
  `estado_cuenta` enum('Activo','Inactivo','Suspendido','Pendiente') DEFAULT 'Pendiente',
  `email_verificado` tinyint(1) DEFAULT 0,
  `fecha_verificacion` timestamp NULL DEFAULT NULL,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_estado_cuenta` (`estado_cuenta`),
  KEY `idx_email_verificado` (`email_verificado`),
  KEY `idx_nombre_completo` (`nombre_completo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. TABLA CATEGORÍAS (PARA CURSOS Y LIBROS)
-- ============================================================================

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#6c757d',
  `icono` varchar(50) DEFAULT 'fas fa-folder',
  `imagen` varchar(255) DEFAULT NULL,
  `es_activa` tinyint(1) DEFAULT 1,
  `orden` int(11) DEFAULT 0,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_activa` (`es_activa`),
  KEY `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. TABLA CURSOS (FUNCIONALIDAD PRINCIPAL)
-- ============================================================================

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `descripcion_corta` varchar(500) DEFAULT NULL,
  `docente_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `precio` decimal(10,2) DEFAULT 0.00,
  `es_gratuito` tinyint(1) DEFAULT 1,
  `duracion_horas` decimal(5,2) DEFAULT NULL,
  `max_estudiantes` int(11) DEFAULT NULL,
  `nivel` enum('Principiante','Intermedio','Avanzado') DEFAULT 'Principiante',
  `modalidad` enum('Presencial','Virtual','Híbrido') DEFAULT 'Virtual',
  `certificado` tinyint(1) DEFAULT 1,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estudiantes_inscritos` int(11) DEFAULT 0,
  `calificacion_promedio` decimal(3,2) DEFAULT 0.00,
  `total_calificaciones` int(11) DEFAULT 0,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `video_preview` varchar(255) DEFAULT NULL,
  `requisitos` text DEFAULT NULL,
  `objetivos` text DEFAULT NULL,
  `contenido_temas` text DEFAULT NULL,
  `estado` enum('Borrador','Publicado','Archivado','Cancelado') DEFAULT 'Borrador',
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_docente` (`docente_id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_modalidad` (`modalidad`),
  KEY `idx_fecha_inicio` (`fecha_inicio`),
  KEY `idx_calificacion` (`calificacion_promedio`),
  CONSTRAINT `fk_cursos_docente` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cursos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. TABLA LIBROS (FUNCIONALIDAD PRINCIPAL)
-- ============================================================================

CREATE TABLE `libros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `autor` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `editorial` varchar(100) DEFAULT NULL,
  `año_publicacion` int(11) DEFAULT NULL,
  `idioma` varchar(50) DEFAULT 'Español',
  `numero_paginas` int(11) DEFAULT NULL,
  `formato` enum('PDF','EPUB','MOBI','Físico','Digital') DEFAULT 'PDF',
  `precio` decimal(8,2) DEFAULT 0.00,
  `es_gratuito` tinyint(1) DEFAULT 1,
  `stock` int(11) DEFAULT 0,
  `tamaño_archivo` bigint(20) DEFAULT NULL,
  `descargas_totales` int(11) DEFAULT 0,
  `calificacion_promedio` decimal(3,2) DEFAULT 0.00,
  `total_calificaciones` int(11) DEFAULT 0,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `palabras_clave` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_autor` (`autor`),
  KEY `idx_estado` (`estado`),
  KEY `idx_idioma` (`idioma`),
  KEY `idx_formato` (`formato`),
  KEY `idx_descargas` (`descargas_totales`),
  KEY `idx_calificacion` (`calificacion_promedio`),
  CONSTRAINT `fk_libros_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. TABLA INSCRIPCIONES_CURSOS
-- ============================================================================

CREATE TABLE `inscripciones_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_inscripcion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Activa','Completada','Cancelada','Pausada') DEFAULT 'Activa',
  `metodo_pago` enum('Gratuito','Efectivo','Transferencia','Tarjeta','QR') DEFAULT 'Gratuito',
  `monto_pagado` decimal(10,2) DEFAULT 0.00,
  `progreso_porcentaje` decimal(5,2) DEFAULT 0.00,
  `fecha_completado` timestamp NULL DEFAULT NULL,
  `certificado_emitido` tinyint(1) DEFAULT 0,
  `fecha_certificado` timestamp NULL DEFAULT NULL,
  `calificacion_final` decimal(5,2) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_estudiante_curso` (`estudiante_id`,`curso_id`),
  KEY `idx_estudiante` (`estudiante_id`),
  KEY `idx_curso` (`curso_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_inscripcion` (`fecha_inscripcion`),
  CONSTRAINT `fk_inscripciones_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inscripciones_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. TABLA PROGRESO_ESTUDIANTES (ACTUALIZADA)
-- ============================================================================

CREATE TABLE `progreso_estudiantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `progreso_porcentaje` decimal(5,2) DEFAULT 0.00,
  `tiempo_estudiado` int(11) DEFAULT 0 COMMENT 'Tiempo total en minutos',
  `ultima_leccion` varchar(200) DEFAULT NULL,
  `puntos_obtenidos` int(11) DEFAULT 0,
  `racha_dias` int(11) DEFAULT 0,
  `fecha_inicio` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultima_actividad` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_estudiante_curso` (`estudiante_id`,`curso_id`),
  KEY `idx_estudiante` (`estudiante_id`),
  KEY `idx_curso` (`curso_id`),
  KEY `idx_progreso` (`progreso_porcentaje`),
  KEY `idx_ultima_actividad` (`fecha_ultima_actividad`),
  CONSTRAINT `fk_progreso_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_progreso_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. TABLA MODULOS_CURSO
-- ============================================================================

CREATE TABLE `modulos_curso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `contenido` longtext DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `duracion_minutos` int(11) DEFAULT 0,
  `es_gratuito` tinyint(1) DEFAULT 0,
  `video_url` varchar(500) DEFAULT NULL,
  `recursos_adicionales` json DEFAULT NULL,
  `objetivos` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_curso` (`curso_id`),
  KEY `idx_orden` (`orden`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `fk_modulos_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. TABLA PROGRESO_MODULOS
-- ============================================================================

CREATE TABLE `progreso_modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `completado` tinyint(1) DEFAULT 0,
  `progreso_porcentaje` decimal(5,2) DEFAULT 0.00,
  `tiempo_dedicado` int(11) DEFAULT 0,
  `fecha_inicio` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_completado` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_modulo_usuario` (`modulo_id`,`usuario_id`),
  KEY `idx_modulo` (`modulo_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_completado` (`completado`),
  CONSTRAINT `fk_progreso_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos_curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_progreso_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. TABLA CALIFICACIONES_CURSOS
-- ============================================================================

CREATE TABLE `calificaciones_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `calificacion` tinyint(4) NOT NULL CHECK (`calificacion` >= 1 AND `calificacion` <= 5),
  `comentario` text DEFAULT NULL,
  `fecha_calificacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_usuario_curso` (`usuario_id`,`curso_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_curso` (`curso_id`),
  KEY `idx_calificacion` (`calificacion`),
  CONSTRAINT `fk_calificaciones_curso_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_calificaciones_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. TABLA CALIFICACIONES_LIBROS
-- ============================================================================

CREATE TABLE `calificaciones_libros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `calificacion` tinyint(4) NOT NULL CHECK (`calificacion` >= 1 AND `calificacion` <= 5),
  `comentario` text DEFAULT NULL,
  `fecha_calificacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_usuario_libro` (`usuario_id`,`libro_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_libro` (`libro_id`),
  KEY `idx_calificacion` (`calificacion`),
  CONSTRAINT `fk_calificaciones_libro_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_calificaciones_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. TABLA FAVORITOS_CURSOS
-- ============================================================================

CREATE TABLE `favoritos_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_agregado` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_curso_usuario` (`curso_id`,`usuario_id`),
  KEY `idx_curso` (`curso_id`),
  KEY `idx_usuario` (`usuario_id`),
  CONSTRAINT `fk_favoritos_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favoritos_curso_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. TABLA FAVORITOS_LIBROS
-- ============================================================================

CREATE TABLE `favoritos_libros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libro_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_agregado` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_libro_usuario` (`libro_id`,`usuario_id`),
  KEY `idx_libro` (`libro_id`),
  KEY `idx_usuario` (`usuario_id`),
  CONSTRAINT `fk_favoritos_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favoritos_libro_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 13. TABLA DESCARGAS_LIBROS
-- ============================================================================

CREATE TABLE `descargas_libros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `fecha_descarga` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_libro` (`libro_id`),
  KEY `idx_fecha` (`fecha_descarga`),
  CONSTRAINT `fk_descargas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_descargas_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 23. SISTEMA DE ROLES Y PERMISOS (COMPLETO)
-- ============================================================================

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) NOT NULL,
  `guard_name` varchar(125) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) NOT NULL,
  `guard_name` varchar(125) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(125) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(125) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 24. SISTEMA DE SEGURIDAD AVANZADO
-- ============================================================================

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_email` (`email`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_used` (`used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activation_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_email` (`email`),
  KEY `idx_usado` (`usado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sesiones_activas` (
  `id` varchar(128) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) NOT NULL,
  `dispositivo` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `navegador` varchar(100) DEFAULT NULL,
  `sistema_operativo` varchar(100) DEFAULT NULL,
  `datos_sesion` longtext DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `fecha_inicio` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actividad` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_activa` (`activa`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_fecha_actividad` (`fecha_actividad`),
  CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `intentos_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `exito` tinyint(1) DEFAULT 0,
  `motivo_fallo` varchar(100) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `fecha_intento` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_fecha` (`fecha_intento`),
  KEY `idx_exito` (`exito`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `acceso_invitados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `dias_restantes` int(11) DEFAULT 3,
  `ultima_notificacion` date DEFAULT NULL,
  `notificaciones_enviadas` json DEFAULT NULL,
  `acceso_bloqueado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_vencimiento` (`fecha_vencimiento`),
  KEY `idx_bloqueado` (`acceso_bloqueado`),
  CONSTRAINT `fk_acceso_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 25. CONFIGURACIONES DEL SISTEMA (COMPLETA)
-- ============================================================================

CREATE TABLE `configuraciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('texto','numero','booleano','json') DEFAULT 'texto',
  `categoria` varchar(50) DEFAULT 'general',
  `es_publica` tinyint(1) DEFAULT 0,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_publica` (`es_publica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 15. TABLA MATERIALES EDUCATIVOS
-- ============================================================================

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `docente_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `tipo_material` enum('Video','Documento','Presentacion','Codigo','Simulacion','Otro') DEFAULT 'Documento',
  `archivo_url` varchar(500) DEFAULT NULL,
  `tamaño_archivo` bigint(20) DEFAULT NULL,
  `duracion_minutos` int(11) DEFAULT NULL,
  `nivel_dificultad` enum('Básico','Intermedio','Avanzado') DEFAULT 'Básico',
  `palabras_clave` text DEFAULT NULL,
  `descargas_totales` int(11) DEFAULT 0,
  `calificacion_promedio` decimal(3,2) DEFAULT 0.00,
  `total_calificaciones` int(11) DEFAULT 0,
  `es_publico` tinyint(1) DEFAULT 1,
  `requiere_inscripcion` tinyint(1) DEFAULT 0,
  `estado` enum('Borrador','Publicado','Archivado') DEFAULT 'Borrador',
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_docente` (`docente_id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_curso` (`curso_id`),
  KEY `idx_tipo` (`tipo_material`),
  KEY `idx_nivel` (`nivel_dificultad`),
  KEY `idx_estado` (`estado`),
  KEY `idx_publico` (`es_publico`),
  CONSTRAINT `fk_materiales_docente` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_materiales_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_materiales_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 16. TABLA LABORATORIOS VIRTUALES
-- ============================================================================

CREATE TABLE `laboratorios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `docente_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `tipo_laboratorio` enum('Simulacion','Virtual','Remoto','Hibrido') DEFAULT 'Virtual',
  `plataforma` varchar(100) DEFAULT NULL,
  `url_acceso` varchar(500) DEFAULT NULL,
  `duracion_estimada` int(11) DEFAULT 60 COMMENT 'Duración en minutos',
  `max_participantes` int(11) DEFAULT NULL,
  `nivel` enum('Principiante','Intermedio','Avanzado') DEFAULT 'Principiante',
  `objetivos` text DEFAULT NULL,
  `instrucciones` longtext DEFAULT NULL,
  `materiales_necesarios` json DEFAULT NULL,
  `software_requerido` json DEFAULT NULL,
  `imagen_preview` varchar(255) DEFAULT NULL,
  `total_sesiones` int(11) DEFAULT 0,
  `calificacion_promedio` decimal(3,2) DEFAULT 0.00,
  `total_calificaciones` int(11) DEFAULT 0,
  `es_gratuito` tinyint(1) DEFAULT 1,
  `precio` decimal(10,2) DEFAULT 0.00,
  `estado` enum('Borrador','Activo','Mantenimiento','Inactivo') DEFAULT 'Borrador',
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_docente` (`docente_id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_tipo` (`tipo_laboratorio`),
  KEY `idx_nivel` (`nivel`),
  KEY `idx_estado` (`estado`),
  KEY `idx_gratuito` (`es_gratuito`),
  CONSTRAINT `fk_laboratorios_docente` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_laboratorios_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 17. TABLA COMPONENTES ELECTRÓNICOS
-- ============================================================================

CREATE TABLE `componentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `codigo_producto` varchar(50) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `especificaciones` json DEFAULT NULL,
  `imagen_principal` varchar(255) DEFAULT NULL,
  `imagenes_adicionales` json DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock_actual` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 5,
  `stock_maximo` int(11) DEFAULT 100,
  `proveedor` varchar(150) DEFAULT NULL,
  `ubicacion_almacen` varchar(100) DEFAULT NULL,
  `fecha_ultima_compra` date DEFAULT NULL,
  `total_vendido` int(11) DEFAULT 0,
  `estado` enum('Disponible','Agotado','Descontinuado','En_Pedido') DEFAULT 'Disponible',
  `es_kit` tinyint(1) DEFAULT 0,
  `componentes_kit` json DEFAULT NULL COMMENT 'IDs de componentes si es kit',
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_producto` (`codigo_producto`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_marca` (`marca`),
  KEY `idx_estado` (`estado`),
  KEY `idx_stock` (`stock_actual`),
  KEY `idx_kit` (`es_kit`),
  KEY `idx_precio` (`precio_venta`),
  CONSTRAINT `fk_componentes_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 18. TABLA VENTAS (SISTEMA COMERCIAL)
-- ============================================================================

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_venta` varchar(20) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `impuestos` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `tipo_pago` enum('Efectivo','Transferencia','Tarjeta','QR') DEFAULT 'Efectivo',
  `estado` enum('Pendiente','Completada','Cancelada','Reembolsada') DEFAULT 'Pendiente',
  `notas` text DEFAULT NULL,
  `fecha_venta` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_venta` (`numero_venta`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_vendedor` (`vendedor_id`),
  KEY `idx_fecha` (`fecha_venta`),
  KEY `idx_estado` (`estado`),
  KEY `idx_tipo_pago` (`tipo_pago`),
  CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ventas_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 19. TABLA DETALLE DE VENTAS
-- ============================================================================

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `tipo_producto` enum('libro','componente') NOT NULL,
  `producto_id` int(11) NOT NULL,
  `nombre_producto` varchar(200) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_venta` (`venta_id`),
  KEY `idx_tipo_producto` (`tipo_producto`),
  KEY `idx_producto_id` (`producto_id`),
  CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 26. PROCEDIMIENTOS ALMACENADOS ADICIONALES
-- ============================================================================

DELIMITER $$

CREATE PROCEDURE `ActualizarStockComponente`(
    IN `p_componente_id` INT, 
    IN `p_cantidad_vendida` INT
)
BEGIN
    DECLARE v_stock_actual INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Verificar stock actual
    SELECT stock_actual 
    INTO v_stock_actual 
    FROM componentes 
    WHERE id = p_componente_id;
    
    -- Verificar si hay suficiente stock
    IF v_stock_actual >= p_cantidad_vendida THEN
        -- Actualizar stock y contador de ventas
        UPDATE componentes 
        SET stock_actual = stock_actual - p_cantidad_vendida,
            total_vendido = total_vendido + p_cantidad_vendida,
            estado = CASE 
                WHEN (stock_actual - p_cantidad_vendida) <= 0 THEN 'Agotado'
                WHEN (stock_actual - p_cantidad_vendida) <= stock_minimo THEN 'Disponible'
                ELSE estado
            END
        WHERE id = p_componente_id;
        
        COMMIT;
    ELSE
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente';
    END IF;
END$$

CREATE PROCEDURE `ProcesarVentaCompleta`(
    IN `p_cliente_id` INT,
    IN `p_vendedor_id` INT,
    IN `p_productos` JSON,
    IN `p_tipo_pago` VARCHAR(20),
    IN `p_descuento` DECIMAL(10,2)
)
BEGIN
    DECLARE v_venta_id INT;
    DECLARE v_numero_venta VARCHAR(20);
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_impuestos DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_total DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_contador INT DEFAULT 0;
    DECLARE v_max_productos INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Generar número de venta
    SELECT CONCAT('VTA-', YEAR(NOW()), '-', LPAD(COUNT(*) + 1, 3, '0'))
    INTO v_numero_venta
    FROM ventas 
    WHERE YEAR(fecha_venta) = YEAR(NOW());
    
    -- Crear venta principal
    INSERT INTO ventas 
    (numero_venta, cliente_id, vendedor_id, subtotal, descuento, tipo_pago, estado) 
    VALUES (v_numero_venta, p_cliente_id, p_vendedor_id, 0.00, p_descuento, p_tipo_pago, 'Pendiente');
    
    SET v_venta_id = LAST_INSERT_ID();
    
    -- Procesar cada producto
    SET v_max_productos = JSON_LENGTH(p_productos);
    
    WHILE v_contador < v_max_productos DO
        SET @producto = JSON_EXTRACT(p_productos, CONCAT('$[', v_contador, ']'));
        SET @tipo = JSON_UNQUOTE(JSON_EXTRACT(@producto, '$.tipo'));
        SET @id = JSON_UNQUOTE(JSON_EXTRACT(@producto, '$.id'));
        SET @cantidad = JSON_UNQUOTE(JSON_EXTRACT(@producto, '$.cantidad'));
        SET @precio = JSON_UNQUOTE(JSON_EXTRACT(@producto, '$.precio'));
        SET @nombre = JSON_UNQUOTE(JSON_EXTRACT(@producto, '$.nombre'));
        SET @item_subtotal = @cantidad * @precio;
        
        -- Insertar detalle de venta
        INSERT INTO detalle_ventas 
        (venta_id, tipo_producto, producto_id, nombre_producto, cantidad, precio_unitario, subtotal) 
        VALUES (v_venta_id, @tipo, @id, @nombre, @cantidad, @precio, @item_subtotal);
        
        -- Actualizar stock si es componente
        IF @tipo = 'componente' THEN
            CALL ActualizarStockComponente(@id, @cantidad);
        END IF;
        
        SET v_subtotal = v_subtotal + @item_subtotal;
        SET v_contador = v_contador + 1;
    END WHILE;
    
    -- Calcular totales
    SET v_impuestos = (v_subtotal - p_descuento) * 0.13; -- 13% IVA
    SET v_total = v_subtotal - p_descuento + v_impuestos;
    
    -- Actualizar venta con totales
    UPDATE ventas 
    SET subtotal = v_subtotal, 
        impuestos = v_impuestos, 
        total = v_total,
        estado = 'Completada'
    WHERE id = v_venta_id;
    
    COMMIT;
    
    SELECT v_venta_id as venta_id, v_numero_venta as numero_venta, v_total as total;
END$$

DELIMITER ;

-- ============================================================================
-- 25. PROCEDIMIENTOS ALMACENADOS
-- ============================================================================

DELIMITER $$

CREATE PROCEDURE `InscribirEstudianteCurso`(
    IN `p_estudiante_id` INT, 
    IN `p_curso_id` INT, 
    IN `p_metodo_pago` VARCHAR(20), 
    IN `p_monto_pagado` DECIMAL(10,2)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Insertar inscripción
    INSERT INTO inscripciones_cursos 
    (estudiante_id, curso_id, metodo_pago, monto_pagado) 
    VALUES (p_estudiante_id, p_curso_id, p_metodo_pago, p_monto_pagado);
    
    -- Crear progreso inicial
    INSERT IGNORE INTO progreso_estudiantes 
    (estudiante_id, curso_id, progreso_porcentaje) 
    VALUES (p_estudiante_id, p_curso_id, 0.00);
    
    -- Actualizar contador de inscritos
    UPDATE cursos 
    SET estudiantes_inscritos = estudiantes_inscritos + 1 
    WHERE id = p_curso_id;
    
    COMMIT;
END$$

CREATE PROCEDURE `ProcesarDescargaLibro`(
    IN `p_usuario_id` INT, 
    IN `p_libro_id` INT, 
    IN `p_ip_address` VARCHAR(45), 
    IN `p_user_agent` TEXT
)
BEGIN
    DECLARE v_stock INT DEFAULT 0;
    DECLARE v_es_gratuito TINYINT DEFAULT 1;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Verificar disponibilidad
    SELECT stock, es_gratuito 
    INTO v_stock, v_es_gratuito 
    FROM libros 
    WHERE id = p_libro_id AND estado = 1;
    
    -- Verificar si está disponible
    IF v_es_gratuito = 1 OR v_stock > 0 THEN
        -- Registrar descarga
        INSERT INTO descargas_libros 
        (usuario_id, libro_id, ip_address, user_agent) 
        VALUES (p_usuario_id, p_libro_id, p_ip_address, p_user_agent);
        
        -- Reducir stock si no es gratuito
        IF v_es_gratuito = 0 THEN
            UPDATE libros 
            SET stock = stock - 1, descargas_totales = descargas_totales + 1 
            WHERE id = p_libro_id;
        ELSE
            UPDATE libros 
            SET descargas_totales = descargas_totales + 1 
            WHERE id = p_libro_id;
        END IF;
        
        COMMIT;
    ELSE
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Libro no disponible para descarga';
    END IF;
END$$

DELIMITER ;

-- ============================================================================
-- 27. TRIGGERS PARA MANTENER CONSISTENCIA
-- ============================================================================

DELIMITER $$

CREATE TRIGGER `tr_actualizar_calificacion_curso` 
    AFTER INSERT ON `calificaciones_cursos` 
    FOR EACH ROW 
BEGIN
    DECLARE v_promedio DECIMAL(3,2);
    DECLARE v_total INT;
    
    SELECT AVG(calificacion), COUNT(*) 
    INTO v_promedio, v_total
    FROM calificaciones_cursos 
    WHERE curso_id = NEW.curso_id;
    
    UPDATE cursos 
    SET calificacion_promedio = v_promedio, 
        total_calificaciones = v_total 
    WHERE id = NEW.curso_id;
END$$

CREATE TRIGGER `tr_actualizar_calificacion_libro` 
    AFTER INSERT ON `calificaciones_libros` 
    FOR EACH ROW 
BEGIN
    DECLARE v_promedio DECIMAL(3,2);
    DECLARE v_total INT;
    
    SELECT AVG(calificacion), COUNT(*) 
    INTO v_promedio, v_total
    FROM calificaciones_libros 
    WHERE libro_id = NEW.libro_id;
    
    UPDATE libros 
    SET calificacion_promedio = v_promedio, 
        total_calificaciones = v_total 
    WHERE id = NEW.libro_id;
END$$

CREATE TRIGGER `tr_actualizar_calificacion_material` 
    AFTER INSERT ON `calificaciones_libros` 
    FOR EACH ROW 
BEGIN
    -- Actualizar también calificaciones de materiales si el "libro" es en realidad material
    IF EXISTS(SELECT 1 FROM materiales WHERE id = NEW.libro_id) THEN
        DECLARE v_promedio DECIMAL(3,2);
        DECLARE v_total INT;
        
        SELECT AVG(calificacion), COUNT(*) 
        INTO v_promedio, v_total
        FROM calificaciones_libros 
        WHERE libro_id = NEW.libro_id;
        
        UPDATE materiales 
        SET calificacion_promedio = v_promedio, 
            total_calificaciones = v_total 
        WHERE id = NEW.libro_id;
    END IF;
END$$

CREATE TRIGGER `tr_alerta_stock_bajo` 
    AFTER UPDATE ON `componentes` 
    FOR EACH ROW 
BEGIN
    IF NEW.stock_actual <= NEW.stock_minimo AND OLD.stock_actual > OLD.stock_minimo THEN
        INSERT INTO configuraciones (clave, valor, descripcion, tipo) 
        VALUES (
            CONCAT('alerta_stock_', NEW.id, '_', UNIX_TIMESTAMP()),
            CONCAT('El componente "', NEW.nombre, '" tiene stock bajo: ', NEW.stock_actual, ' unidades'),
            'Alerta automática de stock bajo',
            'texto'
        ) ON DUPLICATE KEY UPDATE 
        valor = CONCAT('El componente "', NEW.nombre, '" tiene stock bajo: ', NEW.stock_actual, ' unidades'),
        fecha_actualizacion = NOW();
    END IF;
END$$

DELIMITER ;

-- ============================================================================
-- 28. DATOS INICIALES DEL SISTEMA
-- ============================================================================

-- Insertar roles básicos
INSERT INTO `roles` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('administrador', 'web', NOW(), NOW()),
('docente', 'web', NOW(), NOW()),
('estudiante', 'web', NOW(), NOW()),
('invitado', 'web', NOW(), NOW());

-- Insertar permisos COMPLETOS del sistema
INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
-- Permisos básicos del sistema
('login', 'web', NOW(), NOW()),
('logout', 'web', NOW(), NOW()),

-- Permisos de administración
('admin.dashboard', 'web', NOW(), NOW()),
('admin.reportes', 'web', NOW(), NOW()),
('admin.configuracion', 'web', NOW(), NOW()),
('admin.usuarios.ver', 'web', NOW(), NOW()),
('admin.usuarios.crear', 'web', NOW(), NOW()),
('admin.usuarios.editar', 'web', NOW(), NOW()),
('admin.usuarios.eliminar', 'web', NOW(), NOW()),
('admin.usuarios.roles', 'web', NOW(), NOW()),
('admin.usuarios.permisos', 'web', NOW(), NOW()),

-- Permisos de ventas (sistema comercial)
('admin.ventas.ver', 'web', NOW(), NOW()),
('admin.ventas.crear', 'web', NOW(), NOW()),
('admin.ventas.editar', 'web', NOW(), NOW()),
('admin.ventas.eliminar', 'web', NOW(), NOW()),
('admin.ventas.reportes', 'web', NOW(), NOW()),

-- Permisos de cursos
('cursos.ver', 'web', NOW(), NOW()),
('cursos.crear', 'web', NOW(), NOW()),
('cursos.editar', 'web', NOW(), NOW()),
('cursos.eliminar', 'web', NOW(), NOW()),
('cursos.inscribir', 'web', NOW(), NOW()),
('cursos.calificar', 'web', NOW(), NOW()),
('cursos.favoritos', 'web', NOW(), NOW()),
('admin.cursos', 'web', NOW(), NOW()),
('admin.cursos.crear', 'web', NOW(), NOW()),
('admin.cursos.editar', 'web', NOW(), NOW()),
('admin.cursos.eliminar', 'web', NOW(), NOW()),
('admin.cursos.ver', 'web', NOW(), NOW()),

-- Permisos de libros
('libros.ver', 'web', NOW(), NOW()),
('libros.crear', 'web', NOW(), NOW()),
('libros.editar', 'web', NOW(), NOW()),
('libros.eliminar', 'web', NOW(), NOW()),
('libros.descargar', 'web', NOW(), NOW()),
('libros.calificar', 'web', NOW(), NOW()),
('libros.favoritos', 'web', NOW(), NOW()),
('admin.libros', 'web', NOW(), NOW()),
('admin.libros.crear', 'web', NOW(), NOW()),
('admin.libros.editar', 'web', NOW(), NOW()),
('admin.libros.eliminar', 'web', NOW(), NOW()),
('admin.libros.ver', 'web', NOW(), NOW()),

-- Permisos de materiales educativos
('materiales.ver', 'web', NOW(), NOW()),
('materiales.crear', 'web', NOW(), NOW()),
('materiales.editar', 'web', NOW(), NOW()),
('materiales.eliminar', 'web', NOW(), NOW()),
('materiales.descargar', 'web', NOW(), NOW()),
('admin.materiales', 'web', NOW(), NOW()),

-- Permisos de laboratorios virtuales
('laboratorios.ver', 'web', NOW(), NOW()),
('laboratorios.crear', 'web', NOW(), NOW()),
('laboratorios.editar', 'web', NOW(), NOW()),
('laboratorios.eliminar', 'web', NOW(), NOW()),
('laboratorios.acceder', 'web', NOW(), NOW()),
('admin.laboratorios', 'web', NOW(), NOW()),

-- Permisos de componentes (inventario)
('componentes.ver', 'web', NOW(), NOW()),
('componentes.crear', 'web', NOW(), NOW()),
('componentes.editar', 'web', NOW(), NOW()),
('componentes.eliminar', 'web', NOW(), NOW()),
('componentes.comprar', 'web', NOW(), NOW()),
('admin.componentes', 'web', NOW(), NOW()),

-- Permisos de dashboards
('estudiantes.dashboard', 'web', NOW(), NOW()),
('docente.dashboard', 'web', NOW(), NOW()),

-- Permisos de módulos
('modulos.ver', 'web', NOW(), NOW()),
('modulos.crear', 'web', NOW(), NOW()),
('modulos.editar', 'web', NOW(), NOW()),
('modulos.completar', 'web', NOW(), NOW()),

-- Permisos de API
('api.verify_session', 'web', NOW(), NOW());

-- Asignar permisos a roles
-- ADMINISTRADOR: Todos los permisos
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, 1 FROM permissions p; 

-- DOCENTE: Permisos de gestión de contenido educativo
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, 2 FROM permissions p 
WHERE p.name IN (
    'login', 'logout', 'docente.dashboard',
    'cursos.ver', 'cursos.crear', 'cursos.editar', 'cursos.calificar',
    'libros.ver', 'libros.crear', 'libros.editar',
    'materiales.ver', 'materiales.crear', 'materiales.editar', 'materiales.eliminar',
    'laboratorios.ver', 'laboratorios.crear', 'laboratorios.editar',
    'modulos.ver', 'modulos.crear', 'modulos.editar',
    'componentes.ver'
);

-- ESTUDIANTE: Permisos de acceso a contenido
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, 3 FROM permissions p 
WHERE p.name IN (
    'login', 'logout', 'estudiantes.dashboard',
    'cursos.ver', 'cursos.inscribir', 'cursos.calificar', 'cursos.favoritos',
    'libros.ver', 'libros.descargar', 'libros.calificar', 'libros.favoritos',
    'materiales.ver', 'materiales.descargar',
    'laboratorios.ver', 'laboratorios.acceder',
    'modulos.ver', 'modulos.completar',
    'componentes.ver', 'componentes.comprar'
);

-- INVITADO: Permisos limitados de visualización
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, 4 FROM permissions p 
WHERE p.name IN (
    'login', 'logout', 'estudiantes.dashboard',
    'cursos.ver', 'libros.ver', 'materiales.ver', 'laboratorios.ver',
    'componentes.ver'
);

-- ============================================================================
-- 28. CATEGORÍAS INICIALES (ACTUALIZADAS)
-- ============================================================================

INSERT INTO `categorias` (`nombre`, `slug`, `descripcion`, `color`, `icono`, `orden`) VALUES
-- Categorías para cursos y libros
('Programación', 'programacion', 'Cursos y libros sobre programación y desarrollo de software', '#007bff', 'fas fa-code', 1),
('Robótica', 'robotica', 'Cursos de robótica, Arduino, sensores y automatización', '#28a745', 'fas fa-robot', 2),
('Electrónica', 'electronica', 'Fundamentos de electrónica y circuitos', '#ffc107', 'fas fa-microchip', 3),
('Inteligencia Artificial', 'inteligencia-artificial', 'Machine Learning, Deep Learning y AI', '#6f42c1', 'fas fa-brain', 4),
('Desarrollo Web', 'desarrollo-web', 'HTML, CSS, JavaScript, frameworks web', '#17a2b8', 'fas fa-globe', 5),
('Bases de Datos', 'bases-datos', 'SQL, NoSQL, diseño de bases de datos', '#dc3545', 'fas fa-database', 6),
('Redes y Seguridad', 'redes-seguridad', 'Redes informáticas y ciberseguridad', '#6c757d', 'fas fa-shield-alt', 7),
('Diseño Digital', 'diseno-digital', 'UI/UX, diseño gráfico digital', '#e83e8c', 'fas fa-paint-brush', 8),

-- Categorías específicas para componentes electrónicos
('Microcontroladores', 'microcontroladores', 'Arduino, Raspberry Pi, ESP32 y similares', '#2c3e50', 'fas fa-microchip', 11),
('Sensores', 'sensores', 'Sensores de temperatura, humedad, movimiento', '#27ae60', 'fas fa-thermometer-half', 12),
('Motores y Actuadores', 'motores-actuadores', 'Servos, motores paso a paso, actuadores', '#8e44ad', 'fas fa-cogs', 13),
('Componentes Electrónicos', 'componentes-electronicos', 'Resistencias, capacitores, LEDs', '#f39c12', 'fas fa-plug', 14),
('Herramientas', 'herramientas', 'Soldadores, multímetros, protoboards', '#95a5a6', 'fas fa-tools', 15),
('Kits Educativos', 'kits-educativos', 'Kits completos para aprendizaje', '#e74c3c', 'fas fa-box', 16);

-- ============================================================================
-- 29. USUARIOS INICIALES
-- ============================================================================

INSERT INTO `usuarios` (`nombre`, `apellido`, `email`, `password`, `estado_cuenta`, `email_verificado`, `fecha_verificacion`) VALUES
('Administrador', 'Sistema', 'admin@techhome.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Activo', 1, NOW()),
('Juan Carlos', 'Mendoza', 'docente1@techhome.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Activo', 1, NOW()),
('María Elena', 'Rodriguez', 'docente2@techhome.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Activo', 1, NOW()),
('Pedro Luis', 'García', 'estudiante1@techhome.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Activo', 1, NOW()),
('Ana Sofia', 'Vargas', 'estudiante2@techhome.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Activo', 1, NOW()),
('Carlos Eduardo', 'Morales', 'estudiante3@techhome.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Activo', 1, NOW());

-- Asignar roles a usuarios
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1), -- Admin
(2, 'App\\Models\\User', 2), -- Docente 1
(2, 'App\\Models\\User', 3), -- Docente 2
(3, 'App\\Models\\User', 4), -- Estudiante 1
(3, 'App\\Models\\User', 5), -- Estudiante 2
(3, 'App\\Models\\User', 6); -- Estudiante 3

-- ============================================================================
-- 32. COMPONENTES ELECTRÓNICOS DE EJEMPLO
-- ============================================================================

INSERT INTO `componentes` (`nombre`, `descripcion`, `categoria_id`, `codigo_producto`, `marca`, `modelo`, `precio_compra`, `precio_venta`, `stock_actual`, `stock_minimo`, `proveedor`, `estado`) VALUES
-- Microcontroladores
('Arduino UNO R3', 'Placa de desarrollo con microcontrolador ATmega328P, ideal para principiantes en robótica', 11, 'ARD-UNO-R3', 'Arduino', 'UNO R3', 35.00, 45.00, 50, 10, 'Arduino Store Bolivia', 'Disponible'),
('Raspberry Pi 4 Model B 4GB', 'Computadora de placa única con 4GB RAM, WiFi y Bluetooth integrado', 11, 'RPI-4B-4GB', 'Raspberry Pi', '4 Model B', 95.00, 120.00, 25, 5, 'Raspberry Foundation', 'Disponible'),
('ESP32 DevKit V1', 'Módulo WiFi y Bluetooth con microcontrolador dual-core, perfecto para IoT', 11, 'ESP32-DEVKIT', 'Espressif', 'DevKit V1', 20.00, 25.00, 75, 15, 'Espressif Distribuidores', 'Disponible'),
('Arduino Nano v3.0', 'Versión compacta del Arduino UNO, ideal para proyectos pequeños', 11, 'ARD-NANO-V3', 'Arduino', 'Nano v3.0', 18.00, 22.00, 40, 8, 'Arduino Store Bolivia', 'Disponible'),

-- Sensores
('Sensor Ultrasónico HC-SR04', 'Sensor de distancia por ultrasonido, rango 2cm-400cm', 12, 'HC-SR04', 'Generic', 'HC-SR04', 6.00, 8.00, 100, 20, 'Electronics Pro', 'Disponible'),
('Sensor DHT22 Temperatura/Humedad', 'Sensor digital de temperatura (-40-80°C) y humedad (0-100%RH)', 12, 'DHT22', 'Aosong', 'DHT22', 9.00, 12.00, 80, 15, 'Sensor Tech Bolivia', 'Disponible'),
('Sensor PIR de Movimiento', 'Detector de movimiento infrarrojo pasivo con alcance de 7 metros', 12, 'PIR-HC-SR501', 'Generic', 'HC-SR501', 4.50, 6.00, 60, 10, 'Electronics Pro', 'Disponible'),
('Sensor de Luz LDR GL5549', 'Fotorresistencia para detección de luminosidad ambiente', 12, 'LDR-GL5549', 'Generic', 'GL5549', 1.50, 2.50, 150, 30, 'Components World', 'Disponible'),

-- Motores y Actuadores
('Servo Motor SG90', 'Micro servo de 9g, torque 1.8kg/cm, ideal para robótica educativa', 13, 'SERVO-SG90', 'TowerPro', 'SG90', 12.00, 15.00, 40, 8, 'TowerPro Bolivia', 'Disponible'),
('Motor Paso a Paso 28BYJ-48', 'Motor paso a paso unipolar con driver ULN2003 incluido', 13, 'STEPPER-28BYJ', 'Generic', '28BYJ-48', 14.00, 18.00, 30, 5, 'Motor Solutions', 'Disponible'),
('Motor DC 12V 300RPM', 'Motor de corriente continua con caja reductora, 12V 300RPM', 13, 'MOTOR-DC-12V', 'Generic', 'DC-300RPM', 18.00, 22.00, 25, 5, 'Motor Solutions', 'Disponible'),
('Servo Motor MG996R', 'Servo de metal gear, alto torque 10kg/cm, para aplicaciones pesadas', 13, 'SERVO-MG996R', 'TowerPro', 'MG996R', 28.00, 35.00, 20, 4, 'TowerPro Bolivia', 'Disponible'),

-- Componentes Electrónicos
('Kit LEDs 5mm (100 piezas)', 'Surtido de LEDs de colores: rojo, verde, azul, amarillo, blanco', 14, 'LED-KIT-100', 'Generic', 'LED-5MM', 15.00, 20.00, 50, 10, 'LED World Bolivia', 'Disponible'),
('Kit Resistencias 1/4W (500 pcs)', 'Resistencias de carbón 1/4W, valores de 1Ω a 10MΩ', 14, 'RES-KIT-500', 'Generic', '1/4W-500', 18.00, 25.00, 40, 8, 'Electronics Pro', 'Disponible'),
('Jumper Wires (120 piezas)', 'Cables de conexión: macho-macho, hembra-hembra, macho-hembra', 14, 'JUMPER-120', 'Generic', 'Dupont', 12.00, 15.00, 60, 12, 'Wire Tech', 'Disponible'),
('Protoboard 830 puntos', 'Placa de pruebas sin soldadura, 830 puntos de conexión', 14, 'PROTO-830', 'Generic', '830-tie', 8.00, 12.00, 45, 8, 'Proto Tech', 'Disponible'),

-- Herramientas
('Multímetro Digital DT830B', 'Multímetro básico para mediciones de voltaje, corriente y resistencia', 15, 'MULTI-DT830B', 'Generic', 'DT830B', 28.00, 35.00, 20, 3, 'Tool Master', 'Disponible'),
('Soldador de Estaño 40W', 'Soldador eléctrico con control de temperatura variable', 15, 'SOLD-40W', 'Weller', 'SP40N', 65.00, 85.00, 15, 3, 'Weller Tools', 'Disponible'),
('Cautín 30W Básico', 'Soldador básico de 30W para trabajos de electrónica', 15, 'CAUTIN-30W', 'Generic', 'BASIC-30W', 25.00, 32.00, 25, 5, 'Tool Master', 'Disponible'),

-- Kits Educativos
('Kit Básico Arduino Principiantes', 'Kit completo: Arduino UNO + sensores + actuadores + componentes', 16, 'KIT-ARD-BASIC', 'Tech Home', 'BASIC-V1', 140.00, 180.00, 20, 3, 'Tech Home Store', 'Disponible'),
('Kit Avanzado Robótica', 'Kit completo para construcción de robots móviles', 16, 'KIT-ROBOT-ADV', 'Tech Home', 'ROBOT-V2', 280.00, 350.00, 10, 2, 'Tech Home Store', 'Disponible'),
('Kit Sensores IoT', 'Colección de sensores para proyectos Internet de las Cosas', 16, 'KIT-IOT-SENS', 'Tech Home', 'IOT-V1', 175.00, 220.00, 15, 3, 'Tech Home Store', 'Disponible');

-- ============================================================================
-- 33. MATERIALES EDUCATIVOS DE EJEMPLO
-- ============================================================================

INSERT INTO `materiales` (`titulo`, `slug`, `descripcion`, `docente_id`, `categoria_id`, `tipo_material`, `nivel_dificultad`, `es_publico`, `estado`) VALUES
('Guía de Instalación Arduino IDE', 'guia-instalacion-arduino-ide', 'Tutorial paso a paso para instalar y configurar el Arduino IDE en Windows, Mac y Linux', 2, 2, 'Documento', 'Básico', 1, 'Publicado'),
('Códigos de Ejemplo Sensores', 'codigos-ejemplo-sensores', 'Biblioteca completa de códigos para diferentes tipos de sensores', 2, 2, 'Codigo', 'Intermedio', 1, 'Publicado'),
('Simulación Circuitos Básicos', 'simulacion-circuitos-basicos', 'Simulaciones interactivas de circuitos electrónicos fundamentales', 3, 3, 'Simulacion', 'Básico', 1, 'Publicado'),
('Presentación Introducción a Python', 'presentacion-intro-python', 'Slides de introducción al lenguaje Python para principiantes', 3, 1, 'Presentacion', 'Básico', 1, 'Publicado'),
('Video Tutorial: Mi Primer Robot', 'video-primer-robot', 'Video tutorial completo para construir un robot básico con Arduino', 2, 2, 'Video', 'Intermedio', 0, 'Publicado'),
('Documentación API TensorFlow', 'documentacion-tensorflow', 'Guía completa de la API de TensorFlow para proyectos de IA', 3, 4, 'Documento', 'Avanzado', 1, 'Publicado');

-- ============================================================================
-- 34. LABORATORIOS VIRTUALES DE EJEMPLO
-- ============================================================================

INSERT INTO `laboratorios` (`nombre`, `slug`, `descripcion`, `docente_id`, `categoria_id`, `tipo_laboratorio`, `plataforma`, `duracion_estimada`, `nivel`, `objetivos`, `es_gratuito`, `estado`) VALUES
('Simulador de Circuitos Arduino', 'simulador-circuitos-arduino', 'Laboratorio virtual para simular circuitos con Arduino sin hardware físico', 2, 2, 'Simulacion', 'Tinkercad Circuits', 90, 'Principiante', 'Aprender a diseñar y probar circuitos Arduino de forma virtual', 1, 'Activo'),
('Laboratorio de Python Online', 'laboratorio-python-online', 'Entorno de desarrollo Python en la nube para práctica de programación', 3, 1, 'Virtual', 'Jupyter Notebook', 120, 'Principiante', 'Practicar programación Python sin instalación local', 1, 'Activo'),
('Simulador de Redes Neuronales', 'simulador-redes-neuronales', 'Herramienta interactiva para diseñar y entrenar redes neuronales básicas', 3, 4, 'Simulacion', 'TensorFlow Playground', 150, 'Intermedio', 'Comprender el funcionamiento de las redes neuronales', 1, 'Activo'),
('Kit de Robótica Virtual', 'kit-robotica-virtual', 'Simulador 3D para programar robots virtuales en diferentes entornos', 2, 2, 'Virtual', 'V-REP/CoppeliaSim', 180, 'Avanzado', 'Programar y controlar robots en entornos simulados', 0, 'Activo');

-- ============================================================================
-- 35. CONFIGURACIONES DEL SISTEMA (COMPLETAS)
-- ============================================================================

INSERT INTO `configuraciones` (`clave`, `valor`, `descripcion`, `tipo`, `categoria`) VALUES
-- Información del Instituto
('nombre_instituto', 'Tech Home Bolivia – Instituto de Robótica y Tecnología Avanzada', 'Nombre completo del instituto', 'texto', 'instituto'),
('email_contacto', 'contacto@techhome.bo', 'Email principal de contacto del instituto', 'texto', 'instituto'),
('telefono_contacto', '+591 3 123 4567', 'Teléfono de contacto principal', 'texto', 'instituto'),
('direccion_instituto', 'Santa Cruz de la Sierra, Bolivia', 'Dirección física del instituto', 'texto', 'instituto'),
('sitio_web', 'https://www.techhome.bo', 'Sitio web oficial del instituto', 'texto', 'instituto'),
('logo_instituto', '/images/logos/tech-home-logo.png', 'Ruta del logo del instituto', 'texto', 'instituto'),

-- Configuraciones del Sistema
('moneda_simbolo', 'Bs', 'Símbolo de moneda para precios (Bolivianos)', 'texto', 'sistema'),
('iva_porcentaje', '13', 'Porcentaje de IVA para ventas en Bolivia', 'numero', 'sistema'),
('max_file_size', '52428800', 'Tamaño máximo de archivo en bytes (50MB)', 'numero', 'sistema'),
('session_timeout', '3600', 'Tiempo de expiración de sesión en segundos (1 hora)', 'numero', 'sistema'),
('max_login_attempts', '5', 'Máximo número de intentos de login fallidos', 'numero', 'sistema'),
('lockout_time', '900', 'Tiempo de bloqueo tras intentos fallidos (15 min)', 'numero', 'sistema'),
('session_restriction', 'true', 'Restricción de una sesión por usuario', 'booleano', 'sistema'),
('track_sessions', 'true', 'Habilitar seguimiento de sesiones activas', 'booleano', 'sistema'),

-- Configuraciones Académicas
('biblioteca_publica', 'true', 'Si la biblioteca es accesible sin login', 'booleano', 'academico'),
('registro_publico', 'true', 'Si está habilitado el registro público de estudiantes', 'booleano', 'academico'),
('cursos_gratuitos', 'true', 'Permitir cursos gratuitos para estudiantes', 'booleano', 'academico'),
('libros_gratuitos', 'true', 'Permitir libros gratuitos en biblioteca', 'booleano', 'academico'),
('laboratorios_gratuitos', 'true', 'Permitir acceso gratuito a laboratorios virtuales', 'booleano', 'academico'),
('materiales_gratuitos', 'true', 'Permitir descarga gratuita de materiales', 'booleano', 'academico'),
('generar_certificados', 'true', 'Habilitar generación automática de certificados', 'booleano', 'academico'),
('calificacion_minima_certificado', '4.0', 'Calificación mínima requerida para certificado', 'numero', 'academico'),

-- Configuraciones de Invitados
('invitado_dias_acceso', '3', 'Días de acceso para usuarios invitados', 'numero', 'invitados'),
('invitado_notificacion_diaria', 'true', 'Enviar notificación diaria a invitados', 'booleano', 'invitados'),
('invitado_acceso_completo', 'false', 'Si invitados tienen acceso completo', 'booleano', 'invitados'),

-- Configuraciones Comerciales (Sistema de Ventas)
('ventas_activas', 'true', 'Sistema de ventas activo para componentes', 'booleano', 'ventas'),
('descuento_maximo', '20', 'Porcentaje máximo de descuento permitido', 'numero', 'ventas'),
('stock_minimo_alerta', '5', 'Cantidad mínima de stock para generar alertas', 'numero', 'ventas'),
('numeracion_ventas', 'VTA-{YEAR}-{NUMBER}', 'Formato de numeración de ventas', 'texto', 'ventas'),
('porcentaje_ganancia', '30', 'Porcentaje de ganancia por defecto para productos', 'numero', 'ventas'),
('impuesto_iva', '13', 'Porcentaje de IVA aplicado a las ventas', 'numero', 'ventas'),
('metodos_pago', '["Efectivo","Transferencia","Tarjeta","QR"]', 'Métodos de pago disponibles', 'json', 'ventas'),

-- Límites del Sistema
('max_descargas_dia', '10', 'Máximo descargas por usuario por día', 'numero', 'limites'),
('max_inscripciones_activas', '5', 'Máximo inscripciones activas por estudiante', 'numero', 'limites'),
('max_intentos_login', '5', 'Máximo intentos de login por IP', 'numero', 'limites'),
('tiempo_bloqueo_ip', '15', 'Minutos de bloqueo por intentos fallidos', 'numero', 'limites'),

-- Configuraciones de Email
('smtp_host', 'smtp.gmail.com', 'Servidor SMTP para envío de emails', 'texto', 'email'),
('smtp_port', '587', 'Puerto SMTP', 'numero', 'email'),
('smtp_username', 'noreply@techhome.bo', 'Usuario SMTP', 'texto', 'email'),
('email_desde', 'Tech Home Bolivia', 'Nombre del remitente de emails', 'texto', 'email'),
('email_activacion_activo', 'true', 'Requiere activación por email', 'booleano', 'email'),

-- Configuraciones de Archivos
('tipos_archivo_permitidos', '["pdf","doc","docx","ppt","pptx","jpg","jpeg","png","gif","zip","rar"]', 'Tipos de archivo permitidos para subir', 'json', 'archivos'),
('directorio_uploads', '/uploads/', 'Directorio base para archivos subidos', 'texto', 'archivos'),
('directorio_libros', '/uploads/libros/', 'Directorio para archivos de libros', 'texto', 'archivos'),
('directorio_materiales', '/uploads/materiales/', 'Directorio para materiales educativos', 'texto', 'archivos'),
('directorio_imagenes', '/uploads/imagenes/', 'Directorio para imágenes', 'texto', 'archivos'),

-- Configuraciones de Seguridad
('password_min_length', '8', 'Longitud mínima de contraseña', 'numero', 'seguridad'),
('password_require_special', 'true', 'Requiere caracteres especiales en contraseña', 'booleano', 'seguridad'),
('password_require_numbers', 'true', 'Requiere números en contraseña', 'booleano', 'seguridad'),
('password_require_uppercase', 'true', 'Requiere mayúsculas en contraseña', 'booleano', 'seguridad'),
('token_expiration_hours', '24', 'Horas de validez de tokens de recuperación', 'numero', 'seguridad'),

-- Configuraciones de Interfaz
('tema_por_defecto', 'tech-home', 'Tema visual por defecto del sistema', 'texto', 'interfaz'),
('idioma_por_defecto', 'es', 'Idioma por defecto del sistema', 'texto', 'interfaz'),
('mostrar_estadisticas_publicas', 'true', 'Mostrar estadísticas en página principal', 'booleano', 'interfaz'),
('permitir_registro', 'true', 'Permitir auto-registro de nuevos usuarios', 'booleano', 'interfaz');

-- ============================================================================
-- 36. CURSOS DE EJEMPLO (ACTUALIZADOS)
-- ============================================================================

INSERT INTO `cursos` (`titulo`, `slug`, `descripcion`, `descripcion_corta`, `docente_id`, `categoria_id`, `precio`, `es_gratuito`, `duracion_horas`, `nivel`, `modalidad`, `estudiantes_inscritos`, `estado`) VALUES
('Arduino desde Cero', 'arduino-desde-cero', 'Aprende los fundamentos de Arduino desde lo más básico hasta proyectos avanzados. Incluye programación, sensores, actuadores y proyectos prácticos.', 'Curso completo de Arduino para principiantes', 2, 2, 0.00, 1, 40.0, 'Principiante', 'Virtual', 0, 'Publicado'),

('Desarrollo Web con HTML, CSS y JavaScript', 'desarrollo-web-html-css-js', 'Curso completo para crear sitios web modernos usando HTML5, CSS3 y JavaScript. Incluye responsive design y mejores prácticas.', 'Aprende a crear sitios web desde cero', 2, 5, 150.00, 0, 60.0, 'Principiante', 'Virtual', 0, 'Publicado'),

('Python para Principiantes', 'python-principiantes', 'Introducción completa al lenguaje de programación Python. Desde sintaxis básica hasta proyectos prácticos.', 'Tu primer lenguaje de programación', 3, 1, 0.00, 1, 50.0, 'Principiante', 'Virtual', 0, 'Publicado'),

('Inteligencia Artificial con Python', 'ia-python-avanzado', 'Curso avanzado de IA que cubre Machine Learning, redes neuronales y deep learning usando Python y librerías especializadas.', 'Domina la IA con Python', 3, 4, 300.00, 0, 80.0, 'Avanzado', 'Virtual', 0, 'Publicado'),

('Fundamentos de Electrónica Digital', 'electronica-digital-fundamentos', 'Aprende los conceptos fundamentales de electrónica digital, circuitos lógicos, compuertas y sistemas digitales.', 'Bases sólidas en electrónica digital', 2, 3, 120.00, 0, 45.0, 'Intermedio', 'Híbrido', 0, 'Publicado');

-- ============================================================================
-- 22. MÓDULOS DE CURSOS
-- ============================================================================

INSERT INTO `modulos_curso` (`curso_id`, `titulo`, `descripcion`, `orden`, `duracion_minutos`, `es_gratuito`, `estado`) VALUES
-- Módulos para Arduino desde Cero
(1, 'Introducción a Arduino', 'Qué es Arduino, historia y componentes básicos', 1, 60, 1, 1),
(1, 'Configuración del Entorno', 'Instalación del IDE y primeras configuraciones', 2, 45, 1, 1),
(1, 'Mi Primer Programa', 'Hola mundo con LED parpadeante', 3, 30, 0, 1),
(1, 'Entradas Digitales', 'Lectura de botones y switches', 4, 45, 0, 1),
(1, 'Sensores Analógicos', 'Trabajando con sensores de temperatura y luz', 5, 60, 0, 1),
(1, 'Proyecto Final', 'Sistema de alarma básico', 6, 90, 0, 1),

-- Módulos para Desarrollo Web
(2, 'Fundamentos de HTML5', 'Estructura básica y etiquetas principales', 1, 90, 1, 1),
(2, 'Estilos con CSS3', 'Selectores, propiedades y responsive design', 2, 120, 0, 1),
(2, 'JavaScript Básico', 'Variables, funciones y DOM', 3, 150, 0, 1),
(2, 'Proyecto Web Completo', 'Desarrollar un sitio web responsive', 4, 180, 0, 1),

-- Módulos para Python
(3, 'Instalación y Sintaxis Básica', 'Configuración del entorno Python', 1, 45, 1, 1),
(3, 'Variables y Tipos de Datos', 'Números, cadenas, listas y diccionarios', 2, 60, 1, 1),
(3, 'Estructuras de Control', 'Condicionales y bucles', 3, 75, 0, 1),
(3, 'Funciones y Módulos', 'Crear y usar funciones reutilizables', 4, 90, 0, 1),
(3, 'Proyecto Final Python', 'Calculadora avanzada', 5, 120, 0, 1);

-- ============================================================================
-- 23. LIBROS DE EJEMPLO
-- ============================================================================

INSERT INTO `libros` (`titulo`, `slug`, `autor`, `descripcion`, `categoria_id`, `isbn`, `editorial`, `año_publicacion`, `numero_paginas`, `precio`, `es_gratuito`, `stock`, `archivo_pdf`, `estado`) VALUES
('Guía Completa de Arduino', 'guia-completa-arduino', 'Tech Home Bolivia', 'Manual completo para aprender Arduino desde cero con ejemplos prácticos y proyectos paso a paso.', 2, '978-1234567890', 'Tech Home Editorial', 2025, 250, 0.00, 1, 0, 'arduino-completo.pdf', 1),

('Manual de HTML5 y CSS3', 'manual-html5-css3', 'Equipo Tech Home', 'Guía práctica para crear sitios web modernos usando las últimas tecnologías web.', 5, '978-1234567891', 'Tech Home Editorial', 2025, 180, 25.00, 0, 10, 'html5-css3-manual.pdf', 1),

('Python: De Cero a Experto', 'python-cero-experto', 'María Elena Rodriguez', 'Libro completo para dominar Python desde conceptos básicos hasta temas avanzados.', 1, '978-1234567892', 'Tech Home Editorial', 2025, 320, 45.00, 0, 8, 'python-completo.pdf', 1),

('Introducción a la Electrónica', 'introduccion-electronica', 'Juan Carlos Mendoza', 'Fundamentos de electrónica explicados de manera simple y práctica.', 3, '978-1234567893', 'Tech Home Editorial', 2024, 200, 0.00, 1, 0, 'electronica-intro.pdf', 1),

('Algoritmos y Estructuras de Datos', 'algoritmos-estructuras-datos', 'Varios Autores', 'Conceptos fundamentales de algoritmos y estructuras de datos con ejemplos en múltiples lenguajes.', 1, '978-1234567894', 'Tech Home Editorial', 2024, 400, 60.00, 0, 5, 'algoritmos-estructuras.pdf', 1),

('Redes Informáticas Básicas', 'redes-informaticas-basicas', 'Tech Home Bolivia', 'Introducción a las redes de computadoras, protocolos y configuraciones básicas.', 7, '978-1234567895', 'Tech Home Editorial', 2025, 280, 35.00, 0, 12, 'redes-basicas.pdf', 1);

-- ============================================================================
-- 24. INSCRIPCIONES Y PROGRESO DE EJEMPLO
-- ============================================================================

-- Inscribir estudiantes a cursos
INSERT INTO `inscripciones_cursos` (`estudiante_id`, `curso_id`, `metodo_pago`, `monto_pagado`, `estado`) VALUES
(4, 1, 'Gratuito', 0.00, 'Activa'), -- Pedro en Arduino
(4, 3, 'Gratuito', 0.00, 'Activa'), -- Pedro en Python
(5, 1, 'Gratuito', 0.00, 'Activa'), -- Ana en Arduino
(5, 2, 'Transferencia', 150.00, 'Activa'), -- Ana en Desarrollo Web
(6, 3, 'Gratuito', 0.00, 'Completada'), -- Carlos en Python
(6, 5, 'Tarjeta', 120.00, 'Activa'); -- Carlos en Electrónica

-- Crear progreso inicial
INSERT INTO `progreso_estudiantes` (`estudiante_id`, `curso_id`, `progreso_porcentaje`, `tiempo_estudiado`) VALUES
(4, 1, 35.50, 180), -- Pedro en Arduino - 35% completado, 3 horas
(4, 3, 20.00, 90),  -- Pedro en Python - 20% completado, 1.5 horas
(5, 1, 60.00, 250), -- Ana en Arduino - 60% completado, 4.2 horas
(5, 2, 15.00, 120), -- Ana en Desarrollo Web - 15% completado, 2 horas
(6, 3, 100.00, 400), -- Carlos en Python - 100% completado, 6.7 horas
(6, 5, 25.00, 110); -- Carlos en Electrónica - 25% completado, 1.8 horas

-- Actualizar contadores de estudiantes inscritos
UPDATE cursos SET estudiantes_inscritos = (
    SELECT COUNT(*) FROM inscripciones_cursos WHERE curso_id = cursos.id
);

-- ============================================================================
-- 25. PROGRESO EN MÓDULOS
-- ============================================================================

INSERT INTO `progreso_modulos` (`modulo_id`, `usuario_id`, `completado`, `progreso_porcentaje`, `tiempo_dedicado`, `fecha_completado`) VALUES
-- Pedro en Arduino (ha completado los primeros módulos)
(1, 4, 1, 100.00, 60, NOW() - INTERVAL 5 DAY),
(2, 4, 1, 100.00, 45, NOW() - INTERVAL 4 DAY),
(3, 4, 0, 50.00, 15, NULL),

-- Ana en Arduino (más avanzada)
(1, 5, 1, 100.00, 55, NOW() - INTERVAL 7 DAY),
(2, 5, 1, 100.00, 40, NOW() - INTERVAL 6 DAY),
(3, 5, 1, 100.00, 30, NOW() - INTERVAL 5 DAY),
(4, 5, 1, 100.00, 45, NOW() - INTERVAL 3 DAY),
(5, 5, 0, 75.00, 45, NULL),

-- Carlos en Python (curso completo)
(9, 6, 1, 100.00, 45, NOW() - INTERVAL 10 DAY),
(10, 6, 1, 100.00, 60, NOW() - INTERVAL 9 DAY),
(11, 6, 1, 100.00, 75, NOW() - INTERVAL 7 DAY),
(12, 6, 1, 100.00, 90, NOW() - INTERVAL 5 DAY),
(13, 6, 1, 100.00, 120, NOW() - INTERVAL 2 DAY);

-- ============================================================================
-- 26. DESCARGAS DE LIBROS
-- ============================================================================

INSERT INTO `descargas_libros` (`usuario_id`, `libro_id`, `fecha_descarga`, `ip_address`) VALUES
(4, 1, NOW() - INTERVAL 3 DAY, '192.168.1.100'), -- Pedro descargó Arduino
(4, 4, NOW() - INTERVAL 1 DAY, '192.168.1.100'), -- Pedro descargó Electrónica
(5, 1, NOW() - INTERVAL 2 DAY, '192.168.1.101'), -- Ana descargó Arduino
(5, 2, NOW() - INTERVAL 1 DAY, '192.168.1.101'), -- Ana descargó HTML/CSS
(6, 3, NOW() - INTERVAL 4 DAY, '192.168.1.102'), -- Carlos descargó Python
(6, 5, NOW() - INTERVAL 2 DAY, '192.168.1.102'), -- Carlos descargó Algoritmos
(4, 6, NOW() - INTERVAL 1 HOUR, '192.168.1.100'); -- Pedro descargó Redes

-- Actualizar contadores de descargas
UPDATE libros SET descargas_totales = (
    SELECT COUNT(*) FROM descargas_libros WHERE libro_id = libros.id
);

-- ============================================================================
-- 27. FAVORITOS DE EJEMPLO
-- ============================================================================

INSERT INTO `favoritos_cursos` (`curso_id`, `usuario_id`, `fecha_agregado`) VALUES
(2, 4, NOW() - INTERVAL 2 DAY), -- Pedro marcó Desarrollo Web
(4, 5, NOW() - INTERVAL 1 DAY), -- Ana marcó IA Python
(1, 6, NOW() - INTERVAL 3 DAY), -- Carlos marcó Arduino
(5, 4, NOW() - INTERVAL 1 DAY); -- Pedro marcó Electrónica

INSERT INTO `favoritos_libros` (`libro_id`, `usuario_id`, `fecha_agregado`) VALUES
(3, 4, NOW() - INTERVAL 2 DAY), -- Pedro marcó Python libro
(2, 5, NOW() - INTERVAL 1 DAY), -- Ana marcó HTML/CSS
(5, 6, NOW() - INTERVAL 3 DAY), -- Carlos marcó Algoritmos
(1, 5, NOW() - INTERVAL 1 HOUR); -- Ana marcó Arduino libro

-- ============================================================================
-- 28. CALIFICACIONES DE EJEMPLO
-- ============================================================================

INSERT INTO `calificaciones_cursos` (`usuario_id`, `curso_id`, `calificacion`, `comentario`) VALUES
(6, 3, 5, 'Excelente curso de Python, muy bien explicado y con buenos ejemplos prácticos.'),
(5, 1, 4, 'Buen curso de Arduino, me ayudó mucho a entender los conceptos básicos.'),
(4, 1, 4, 'Muy útil para principiantes, aunque me gustaría más proyectos prácticos.');

INSERT INTO `calificaciones_libros` (`usuario_id`, `libro_id`, `calificacion`, `comentario`) VALUES
(4, 1, 5, 'Manual excelente, muy completo y fácil de seguir.'),
(5, 2, 4, 'Buen libro sobre HTML y CSS, ejemplos claros.'),
(6, 3, 5, 'El mejor libro de Python que he leído, muy recomendado.'),
(4, 4, 4, 'Buena introducción a la electrónica, conceptos bien explicados.');

-- Actualizar calificaciones promedio (se hará automáticamente con triggers, pero por si acaso)
UPDATE cursos c SET 
    calificacion_promedio = (SELECT AVG(calificacion) FROM calificaciones_cursos WHERE curso_id = c.id),
    total_calificaciones = (SELECT COUNT(*) FROM calificaciones_cursos WHERE curso_id = c.id);

UPDATE libros l SET 
    calificacion_promedio = (SELECT AVG(calificacion) FROM calificaciones_libros WHERE libro_id = l.id),
    total_calificaciones = (SELECT COUNT(*) FROM calificaciones_libros WHERE libro_id = l.id);

-- ============================================================================
-- 44. VENTAS DE EJEMPLO (SISTEMA COMERCIAL)
-- ============================================================================

INSERT INTO `ventas` (`numero_venta`, `cliente_id`, `vendedor_id`, `subtotal`, `descuento`, `impuestos`, `total`, `tipo_pago`, `estado`, `notas`) VALUES
('VTA-2025-001', 4, 1, 180.00, 0.00, 23.40, 203.40, 'Efectivo', 'Completada', 'Venta de kit básico Arduino - Cliente estudiante'),
('VTA-2025-002', 5, 1, 85.00, 8.50, 9.95, 86.45, 'Transferencia', 'Completada', 'Descuento del 10% por ser estudiante activo'),
('VTA-2025-003', 6, 1, 350.00, 35.00, 40.95, 355.95, 'Tarjeta', 'Completada', 'Kit avanzado de robótica con descuento'),
('VTA-2025-004', 4, 1, 272.00, 0.00, 35.36, 307.36, 'QR', 'Completada', 'Compra múltiple: libro + componentes'),
('VTA-2025-005', 5, 1, 65.00, 0.00, 8.45, 73.45, 'Efectivo', 'Completada', 'Herramientas básicas de soldadura');

-- Detalles de las ventas
INSERT INTO `detalle_ventas` (`venta_id`, `tipo_producto`, `producto_id`, `nombre_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
-- Venta 1: Kit básico Arduino
(1, 'componente', 16, 'Kit Básico Arduino Principiantes', 1, 180.00, 180.00),

-- Venta 2: Soldador con descuento
(2, 'componente', 14, 'Soldador de Estaño 40W', 1, 85.00, 85.00),

-- Venta 3: Kit avanzado robótica
(3, 'componente', 17, 'Kit Avanzado Robótica', 1, 350.00, 350.00),

-- Venta 4: Compra múltiple
(4, 'libro', 3, 'Python: De Cero a Experto', 1, 45.00, 45.00),
(4, 'componente', 1, 'Arduino UNO R3', 2, 45.00, 90.00),
(4, 'componente', 4, 'Sensor Ultrasónico HC-SR04', 4, 8.00, 32.00),
(4, 'componente', 10, 'Kit LEDs 5mm (100 piezas)', 1, 20.00, 20.00),
(4, 'componente', 12, 'Jumper Wires (120 piezas)', 4, 15.00, 60.00),
(4, 'componente', 15, 'Protoboard 830 puntos', 2, 12.00, 24.00),

-- Venta 5: Herramientas
(5, 'componente', 16, 'Cautín 30W Básico', 1, 32.00, 32.00),
(5, 'componente', 13, 'Multímetro Digital DT830B', 1, 35.00, 35.00);

-- ============================================================================
-- 45. DATOS DE EJEMPLO PARA TOKENS Y SEGURIDAD
-- ============================================================================

-- Ejemplos de tokens de activación (ya usados)
INSERT INTO `activation_tokens` (`email`, `token`, `usado`) VALUES
('estudiante1@techhome.com', '977518154ce4e6209be95eaee5c0a273a23c68189bcf4a2ef03c777225389d29', 1),
('estudiante2@techhome.com', '0e19bbc1a467d40ed69bedeb13c85663d088f70c390a13f3e6f34204e645ec45', 1),
('estudiante3@techhome.com', '857fe405624c298dd023d909749145629cccbd2295b8b595f1f30e3b3fe90dda', 1);

-- Ejemplos de tokens de reset (algunos ya usados)
INSERT INTO `password_reset_tokens` (`email`, `token`, `expires_at`, `used`) VALUES
('admin@techhome.com', 'daf9f58b874dcf8a7df556a053f649d5b113c5d4e616946bda19aa2468fb73ce', DATE_ADD(NOW(), INTERVAL 24 HOUR), 0),
('docente1@techhome.com', 'b8706d4679c0b8d9db9183b48489704a556ba0d9f9b2d8171537cd79ae8989a3', DATE_SUB(NOW(), INTERVAL 2 HOUR), 1);

-- Ejemplo de acceso de invitado
INSERT INTO `acceso_invitados` (`usuario_id`, `fecha_inicio`, `fecha_vencimiento`, `dias_restantes`) VALUES
(6, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 3);

-- Ejemplos de intentos de login
INSERT INTO `intentos_login` (`email`, `ip_address`, `exito`, `motivo_fallo`, `fecha_intento`) VALUES
('admin@techhome.com', '192.168.1.100', 1, NULL, NOW() - INTERVAL 1 HOUR),
('estudiante1@techhome.com', '192.168.1.101', 1, NULL, NOW() - INTERVAL 30 MINUTE),
('usuario_falso@test.com', '192.168.1.200', 0, 'Usuario no existe', NOW() - INTERVAL 15 MINUTE),
('admin@techhome.com', '192.168.1.200', 0, 'Contraseña incorrecta', NOW() - INTERVAL 10 MINUTE);

-- ============================================================================
-- 46. VISTAS PARA OPTIMIZAR CONSULTAS (ACTUALIZADAS)
-- ============================================================================

CREATE VIEW `vista_cursos_completa` AS
SELECT 
    c.*,
    cat.nombre as categoria_nombre,
    cat.color as categoria_color,
    u.nombre_completo as docente_nombre,
    COUNT(DISTINCT ic.estudiante_id) as total_inscritos_real,
    AVG(pe.progreso_porcentaje) as progreso_promedio,
    COUNT(DISTINCT cc.id) as total_calificaciones_real,
    AVG(cc.calificacion) as calificacion_promedio_real
FROM cursos c
LEFT JOIN categorias cat ON c.categoria_id = cat.id
LEFT JOIN usuarios u ON c.docente_id = u.id
LEFT JOIN inscripciones_cursos ic ON c.id = ic.curso_id AND ic.estado = 'Activa'
LEFT JOIN progreso_estudiantes pe ON c.id = pe.curso_id
LEFT JOIN calificaciones_cursos cc ON c.id = cc.curso_id
GROUP BY c.id;

CREATE VIEW `vista_componentes_inventario` AS
SELECT 
    comp.*,
    cat.nombre as categoria_nombre,
    cat.color as categoria_color,
    CASE 
        WHEN comp.stock_actual <= 0 THEN 'Sin Stock'
        WHEN comp.stock_actual <= comp.stock_minimo THEN 'Stock Bajo'
        WHEN comp.stock_actual <= (comp.stock_minimo * 2) THEN 'Stock Medio'
        ELSE 'Stock Alto'
    END as nivel_stock,
    (comp.precio_venta - comp.precio_compra) as ganancia_unitaria,
    ((comp.precio_venta - comp.precio_compra) / comp.precio_compra * 100) as margen_porcentaje,
    comp.total_vendido * comp.precio_venta as ingresos_totales
FROM componentes comp
LEFT JOIN categorias cat ON comp.categoria_id = cat.id;

CREATE VIEW `vista_ventas_resumen` AS
SELECT 
    v.*,
    cliente.nombre_completo as cliente_nombre,
    vendedor.nombre_completo as vendedor_nombre,
    COUNT(dv.id) as total_items,
    SUM(dv.cantidad) as total_cantidad_productos
FROM ventas v
LEFT JOIN usuarios cliente ON v.cliente_id = cliente.id
LEFT JOIN usuarios vendedor ON v.vendedor_id = vendedor.id
LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
GROUP BY v.id;

CREATE VIEW `vista_materiales_completa` AS
SELECT 
    m.*,
    cat.nombre as categoria_nombre,
    cat.color as categoria_color,
    u.nombre_completo as docente_nombre,
    c.titulo as curso_titulo,
    COUNT(DISTINCT dl.usuario_id) as total_descargas
FROM materiales m
LEFT JOIN categorias cat ON m.categoria_id = cat.id
LEFT JOIN usuarios u ON m.docente_id = u.id
LEFT JOIN cursos c ON m.curso_id = c.id
LEFT JOIN descargas_libros dl ON m.id = dl.libro_id -- Reutilizamos tabla descargas para materiales
GROUP BY m.id;

CREATE VIEW `vista_estudiantes_dashboard` AS
SELECT 
    u.id,
    u.nombre_completo,
    u.email,
    COUNT(DISTINCT ic.curso_id) as cursos_inscritos,
    COUNT(DISTINCT CASE WHEN ic.estado = 'Completada' THEN ic.curso_id END) as cursos_completados,
    AVG(pe.progreso_porcentaje) as progreso_promedio,
    COUNT(DISTINCT fc.curso_id) as cursos_favoritos,
    COUNT(DISTINCT fl.libro_id) as libros_favoritos,
    COUNT(DISTINCT dl.libro_id) as libros_descargados,
    MAX(ic.fecha_inscripcion) as ultima_inscripcion,
    MAX(pe.fecha_ultima_actividad) as ultima_actividad
FROM usuarios u
LEFT JOIN inscripciones_cursos ic ON u.id = ic.estudiante_id
LEFT JOIN progreso_estudiantes pe ON u.id = pe.estudiante_id
LEFT JOIN favoritos_cursos fc ON u.id = fc.usuario_id
LEFT JOIN favoritos_libros fl ON u.id = fl.usuario_id
LEFT JOIN descargas_libros dl ON u.id = dl.usuario_id
WHERE u.id IN (SELECT model_id FROM model_has_roles WHERE role_id = 3) -- Solo estudiantes
GROUP BY u.id;

-- ============================================================================
-- FINALIZACIÓN DEL SCRIPT
-- ============================================================================

COMMIT;

-- ============================================================================
-- ESTADÍSTICAS FINALES Y MENSAJE DE CONFIRMACIÓN
-- ============================================================================

SELECT 
    'BASE DE DATOS TECH HOME CREADA EXITOSAMENTE' as ESTADO,
    'Instituto de Robótica y Tecnología Avanzada' as DESCRIPCION,
    '27 de Agosto de 2025' as FECHA_CREACION,
    'Sistema Académico y Comercial Completo' as FUNCIONALIDAD;

SELECT 
    'RESUMEN DE DATOS INICIALES' as SECCION,
    (SELECT COUNT(*) FROM usuarios) as USUARIOS_TOTALES,
    (SELECT COUNT(*) FROM roles) as ROLES_SISTEMA,
    (SELECT COUNT(*) FROM permissions) as PERMISOS_DEFINIDOS,
    (SELECT COUNT(*) FROM cursos) as CURSOS_DISPONIBLES,
    (SELECT COUNT(*) FROM libros) as LIBROS_BIBLIOTECA,
    (SELECT COUNT(*) FROM componentes) as COMPONENTES_TIENDA,
    (SELECT COUNT(*) FROM materiales) as MATERIALES_EDUCATIVOS,
    (SELECT COUNT(*) FROM laboratorios) as LABORATORIOS_VIRTUALES,
    (SELECT COUNT(*) FROM ventas) as VENTAS_REGISTRADAS,
    (SELECT COUNT(*) FROM configuraciones) as CONFIGURACIONES_SISTEMA;

SELECT 
    'ESTRUCTURA DE LA BASE DE DATOS' as SECCION,
    '25 Tablas Principales' as TABLAS,
    '5 Vistas Optimizadas' as CONSULTAS,
    '4 Procedimientos Almacenados' as AUTOMATIZACION,
    '4 Triggers Activos' as CONSISTENCIA,
    'Sistema de Roles Completo' as SEGURIDAD,
    'Gestión Comercial Integrada' as VENTAS,
    'Configuración Avanzada' as PERSONALIZACION;

SELECT 
    'FUNCIONALIDADES IMPLEMENTADAS' as SECCION,
    'Sistema Académico Completo' as EDUCACION,
    'Biblioteca Digital' as CONTENIDO,
    'Laboratorios Virtuales' as PRACTICA,
    'Tienda de Componentes' as COMERCIO,
    'Control de Inventario' as STOCK,
    'Sistema de Ventas' as FACTURACION,
    'Gestión de Usuarios' as ADMINISTRACION,
    'Seguridad Avanzada' as PROTECCION;

SELECT 
    'CONFIGURACIÓN ESPECÍFICA BOLIVIA' as LOCALIZACION,
    'Bolivianos (Bs)' as MONEDA,
    '13% IVA' as IMPUESTOS,
    'Santa Cruz de la Sierra' as UBICACION,
    'Español' as IDIOMA,
    'Tech Home Bolivia' as INSTITUTO;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
