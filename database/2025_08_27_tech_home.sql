-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-08-2025 a las 00:43:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tech_home`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `InscribirEstudianteCurso` (IN `p_estudiante_id` INT, IN `p_curso_id` INT, IN `p_metodo_pago` VARCHAR(20), IN `p_monto_pagado` DECIMAL(10,2))   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `ProcesarDescargaLibro` (IN `p_usuario_id` INT, IN `p_libro_id` INT, IN `p_ip_address` VARCHAR(45), IN `p_user_agent` TEXT)   BEGIN
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
        SELECT 'success' as resultado, 'Descarga procesada exitosamente' as mensaje;
    ELSE
        ROLLBACK;
        SELECT 'error' as resultado, 'Libro no disponible para descarga' as mensaje;
    END IF;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalcularProgresoGeneral` (`p_estudiante_id` INT) RETURNS DECIMAL(5,2) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_progreso DECIMAL(5,2) DEFAULT 0.00;
    
    SELECT COALESCE(AVG(progreso_porcentaje), 0.00)
    INTO v_progreso
    FROM progreso_estudiantes 
    WHERE estudiante_id = p_estudiante_id;
    
    RETURN v_progreso;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `ObtenerNivelEstudiante` (`p_estudiante_id` INT) RETURNS VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_progreso DECIMAL(5,2);
    DECLARE v_cursos_completados INT;
    DECLARE v_nivel VARCHAR(20) DEFAULT 'Principiante';
    
    SELECT 
        CalcularProgresoGeneral(p_estudiante_id),
        COUNT(CASE WHEN completado = 1 THEN 1 END)
    INTO v_progreso, v_cursos_completados
    FROM progreso_estudiantes 
    WHERE estudiante_id = p_estudiante_id;
    
    IF v_cursos_completados >= 5 AND v_progreso >= 90.00 THEN
        SET v_nivel = 'Experto';
    ELSEIF v_cursos_completados >= 3 AND v_progreso >= 75.00 THEN
        SET v_nivel = 'Avanzado';
    ELSEIF v_cursos_completados >= 1 AND v_progreso >= 50.00 THEN
        SET v_nivel = 'Intermedio';
    END IF;
    
    RETURN v_nivel;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso_invitados`
--

CREATE TABLE `acceso_invitados` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `dias_restantes` int(11) DEFAULT 3,
  `ultima_notificacion` date DEFAULT NULL,
  `notificaciones_enviadas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notificaciones_enviadas`)),
  `acceso_bloqueado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `acceso_invitados`
--

INSERT INTO `acceso_invitados` (`id`, `usuario_id`, `fecha_inicio`, `fecha_vencimiento`, `dias_restantes`, `ultima_notificacion`, `notificaciones_enviadas`, `acceso_bloqueado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 6, '2025-08-18', '2025-08-21', 3, NULL, NULL, 0, '2025-08-18 15:16:31', '2025-08-18 15:16:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activation_tokens`
--

CREATE TABLE `activation_tokens` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `activation_tokens`
--

INSERT INTO `activation_tokens` (`id`, `email`, `token`, `usado`, `fecha_creacion`) VALUES
(2, 'luisrochavela990@gmail.com', '977518154ce4e6209be95eaee5c0a273a23c68189bcf4a2ef03c777225389d29', 1, '2025-08-22 13:20:28'),
(3, 'leonardopenaanez@gmail.com', '0e19bbc1a467d40ed69bedeb13c85663d088f70c390a13f3e6f34204e645ec45', 1, '2025-08-25 12:59:25'),
(4, 'leonardopenaanez@gmail.com', '857fe405624c298dd023d909749145629cccbd2295b8b595f1f30e3b3fe90dda', 1, '2025-08-25 13:17:14'),
(5, 'tantani.m.g@gmail.com', '5f07a806d850d8d560d255b53e91b58861656b7711dc71c97c926d1708da9d44', 1, '2025-08-25 13:31:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_cursos`
--

CREATE TABLE `calificaciones_cursos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `calificacion` tinyint(4) NOT NULL CHECK (`calificacion` >= 1 and `calificacion` <= 5),
  `comentario` text DEFAULT NULL,
  `fecha_calificacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `calificaciones_cursos`
--

INSERT INTO `calificaciones_cursos` (`id`, `usuario_id`, `curso_id`, `calificacion`, `comentario`, `fecha_calificacion`, `fecha_actualizacion`) VALUES
(1, 4, 1, 5, 'Excelente curso, muy didáctico', '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(2, 4, 2, 4, 'Muy bueno, fácil de seguir', '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(3, 5, 1, 4, 'Buen contenido', '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(4, 8, 2, 3, 'Regular, podría mejorar', '2025-08-27 22:25:22', '2025-08-27 22:25:22');

--
-- Disparadores `calificaciones_cursos`
--
DELIMITER $$
CREATE TRIGGER `tr_actualizar_calificacion_curso` AFTER INSERT ON `calificaciones_cursos` FOR EACH ROW BEGIN
    UPDATE `cursos` c
    SET 
        `total_calificaciones` = (SELECT COUNT(*) FROM `calificaciones_cursos` WHERE curso_id = NEW.curso_id),
        `calificacion_promedio` = (SELECT AVG(calificacion) FROM `calificaciones_cursos` WHERE curso_id = NEW.curso_id)
    WHERE c.id = NEW.curso_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_libros`
--

CREATE TABLE `calificaciones_libros` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `calificacion` tinyint(4) NOT NULL CHECK (`calificacion` >= 1 and `calificacion` <= 5),
  `comentario` text DEFAULT NULL,
  `fecha_calificacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `calificaciones_libros`
--

INSERT INTO `calificaciones_libros` (`id`, `usuario_id`, `libro_id`, `calificacion`, `comentario`, `fecha_calificacion`, `fecha_actualizacion`) VALUES
(1, 4, 2, 5, 'Perfecto para principiantes', '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(2, 5, 2, 4, 'Muy útil', '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(3, 4, 5, 4, 'Excelente referencia', '2025-08-27 22:25:22', '2025-08-27 22:25:22');

--
-- Disparadores `calificaciones_libros`
--
DELIMITER $$
CREATE TRIGGER `tr_actualizar_calificacion_libro` AFTER INSERT ON `calificaciones_libros` FOR EACH ROW BEGIN
    UPDATE `libros` l
    SET 
        `total_calificaciones` = (SELECT COUNT(*) FROM `calificaciones_libros` WHERE libro_id = NEW.libro_id),
        `calificacion_promedio` = (SELECT AVG(calificacion) FROM `calificaciones_libros` WHERE libro_id = NEW.libro_id)
    WHERE l.id = NEW.libro_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('curso','libro','componente') NOT NULL,
  `color` varchar(7) DEFAULT '#007bff',
  `icono` varchar(50) DEFAULT 'fas fa-book',
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `tipo`, `color`, `icono`, `estado`, `fecha_creacion`) VALUES
(1, 'Robótica', 'Cursos relacionados con robótica y automatización', 'curso', '#e74c3c', 'fas fa-robot', 1, '2025-08-18 15:16:31'),
(2, 'Programación', 'Cursos de desarrollo de software y programación', 'curso', '#3498db', 'fas fa-code', 1, '2025-08-18 15:16:31'),
(3, 'Electrónica', 'Cursos de electrónica y circuitos', 'curso', '#f39c12', 'fas fa-bolt', 1, '2025-08-18 15:16:31'),
(4, 'Inteligencia Artificial', 'Machine Learning, Deep Learning y AI', 'curso', '#9b59b6', 'fas fa-brain', 1, '2025-08-18 15:16:31'),
(5, 'Ciencias de Datos', 'Análisis de datos y visualización', 'curso', '#2ecc71', 'fas fa-chart-bar', 1, '2025-08-18 15:16:31'),
(6, 'Robótica Educativa', 'Libros sobre robótica y automatización', 'libro', '#e74c3c', 'fas fa-robot', 1, '2025-08-18 15:16:31'),
(7, 'Programación Avanzada', 'Libros de desarrollo y programación', 'libro', '#3498db', 'fas fa-book-open', 1, '2025-08-18 15:16:31'),
(8, 'Electrónica Práctica', 'Manuales de electrónica y circuitos', 'libro', '#f39c12', 'fas fa-microchip', 1, '2025-08-18 15:16:31'),
(9, 'Inteligencia Artificial', 'Textos de IA y Machine Learning', 'libro', '#9b59b6', 'fas fa-brain', 1, '2025-08-18 15:16:31'),
(10, 'Matemáticas y Física', 'Fundamentos científicos', 'libro', '#34495e', 'fas fa-calculator', 1, '2025-08-18 15:16:31'),
(11, 'Microcontroladores', 'Arduino, Raspberry Pi, ESP32', 'componente', '#e67e22', 'fas fa-microchip', 1, '2025-08-18 15:16:31'),
(12, 'Sensores', 'Sensores de temperatura, humedad, movimiento', 'componente', '#27ae60', 'fas fa-thermometer-half', 1, '2025-08-18 15:16:31'),
(13, 'Motores y Actuadores', 'Servos, motores paso a paso, actuadores', 'componente', '#8e44ad', 'fas fa-cogs', 1, '2025-08-18 15:16:31'),
(14, 'Componentes Electrónicos', 'Resistencias, capacitores, LEDs', 'componente', '#f39c12', 'fas fa-plug', 1, '2025-08-18 15:16:31'),
(15, 'Herramientas', 'Soldadores, multímetros, protoboards', 'componente', '#95a5a6', 'fas fa-tools', 1, '2025-08-18 15:16:31'),
(16, 'Kits Educativos', 'Kits completos para aprendizaje', 'componente', '#e74c3c', 'fas fa-box', 1, '2025-08-18 15:16:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `componentes`
--

CREATE TABLE `componentes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `codigo_producto` varchar(50) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `especificaciones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`especificaciones`)),
  `imagen_principal` varchar(255) DEFAULT NULL,
  `imagenes_adicionales` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`imagenes_adicionales`)),
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 5,
  `proveedor` varchar(150) DEFAULT NULL,
  `estado` enum('Disponible','Agotado','Descontinuado') DEFAULT 'Disponible',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `componentes`
--

INSERT INTO `componentes` (`id`, `nombre`, `descripcion`, `categoria_id`, `codigo_producto`, `marca`, `modelo`, `especificaciones`, `imagen_principal`, `imagenes_adicionales`, `precio`, `stock`, `stock_minimo`, `proveedor`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Arduino UNO R3', 'Placa de desarrollo con microcontrolador ATmega328P', 11, 'ARD-UNO-R3', 'Arduino', 'UNO R3', NULL, NULL, NULL, 45.00, 50, 10, 'Arduino Store', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(2, 'Raspberry Pi 4 Model B', 'Computadora de placa única de 4GB RAM', 11, 'RPI-4B-4GB', 'Raspberry Pi', '4 Model B', NULL, NULL, NULL, 120.00, 25, 5, 'Raspberry Foundation', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(3, 'ESP32 DevKit V1', 'Módulo WiFi y Bluetooth con microcontrolador dual-core', 11, 'ESP32-DEVKIT', 'Espressif', 'DevKit V1', NULL, NULL, NULL, 25.00, 75, 15, 'Espressif Systems', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(4, 'Sensor Ultrasónico HC-SR04', 'Sensor de distancia por ultrasonido', 12, 'HC-SR04', 'Generic', 'HC-SR04', NULL, NULL, NULL, 8.00, 100, 20, 'Electronics Pro', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(5, 'Sensor de Temperatura DHT22', 'Sensor digital de temperatura y humedad', 12, 'DHT22', 'Aosong', 'DHT22', NULL, NULL, NULL, 12.00, 80, 15, 'Sensor Tech', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(6, 'Sensor PIR de Movimiento', 'Detector de movimiento infrarrojo pasivo', 12, 'PIR-HC-SR501', 'Generic', 'HC-SR501', NULL, NULL, NULL, 6.00, 60, 10, 'Electronics Pro', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(7, 'Servo Motor SG90', 'Micro servo de 9g para proyectos de robótica', 13, 'SERVO-SG90', 'TowerPro', 'SG90', NULL, NULL, NULL, 15.00, 40, 8, 'TowerPro', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(8, 'Motor Paso a Paso 28BYJ-48', 'Motor paso a paso unipolar con driver ULN2003', 13, 'STEPPER-28BYJ', 'Generic', '28BYJ-48', NULL, NULL, NULL, 18.00, 30, 5, 'Motor Solutions', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(9, 'Motor DC 12V', 'Motor de corriente continua de 12V y 300 RPM', 13, 'MOTOR-DC-12V', 'Generic', 'DC-300RPM', NULL, NULL, NULL, 22.00, 25, 5, 'Motor Solutions', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(10, 'Kit de LEDs 5mm (100 piezas)', 'Surtido de LEDs de colores de 5mm', 14, 'LED-KIT-100', 'Generic', 'LED-5MM', NULL, NULL, NULL, 20.00, 50, 10, 'LED World', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(11, 'Resistencias 1/4W (500 piezas)', 'Kit de resistencias de diferentes valores', 14, 'RES-KIT-500', 'Generic', '1/4W', NULL, NULL, NULL, 25.00, 40, 8, 'Electronics Pro', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(12, 'Jumper Wires (120 piezas)', 'Cables de conexión macho-macho, hembra-hembra', 14, 'JUMPER-120', 'Generic', 'Dupont', NULL, NULL, NULL, 15.00, 60, 12, 'Wire Tech', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(13, 'Multímetro Digital DT830B', 'Multímetro básico para mediciones eléctricas', 15, 'MULTI-DT830B', 'Generic', 'DT830B', NULL, NULL, NULL, 35.00, 20, 3, 'Tool Master', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(14, 'Soldador de Estaño 40W', 'Soldador eléctrico con control de temperatura', 15, 'SOLD-40W', 'Weller', 'SP40N', NULL, NULL, NULL, 85.00, 15, 3, 'Weller Tools', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(15, 'Protoboard 830 puntos', 'Placa de pruebas sin soldadura', 15, 'PROTO-830', 'Generic', '830-tie', NULL, NULL, NULL, 12.00, 45, 8, 'Proto Tech', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(16, 'Kit Básico Arduino para Principiantes', 'Kit completo con Arduino UNO y componentes básicos', 16, 'KIT-ARD-BASIC', 'Tech Home', 'BASIC-V1', NULL, NULL, NULL, 180.00, 20, 3, 'Tech Home Store', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(17, 'Kit Avanzado de Robótica', 'Kit completo para construcción de robots', 16, 'KIT-ROBOT-ADV', 'Tech Home', 'ROBOT-V2', NULL, NULL, NULL, 350.00, 10, 2, 'Tech Home Store', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(18, 'Kit de Sensores IoT', 'Colección de sensores para proyectos IoT', 16, 'KIT-IOT-SENS', 'Tech Home', 'IOT-V1', NULL, NULL, NULL, 220.00, 15, 3, 'Tech Home Store', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('texto','numero','booleano','json') DEFAULT 'texto',
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `fecha_actualizacion`) VALUES
(1, 'nombre_instituto', 'Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada', 'Nombre completo del instituto', 'texto', '2025-08-18 15:16:31'),
(2, 'email_contacto', 'contacto@techhome.bo', 'Email principal de contacto', 'texto', '2025-08-18 15:16:31'),
(3, 'telefono_contacto', '+591 3 123 4567', 'Teléfono de contacto principal', 'texto', '2025-08-18 15:16:31'),
(4, 'direccion', 'Santa Cruz de la Sierra, Bolivia', 'Dirección física del instituto', 'texto', '2025-08-18 15:16:31'),
(5, 'moneda', 'Bs', 'Símbolo de moneda para precios', 'texto', '2025-08-18 15:16:31'),
(6, 'max_file_size', '52428800', 'Tamaño máximo de archivo en bytes (50MB)', 'numero', '2025-08-18 15:16:31'),
(7, 'biblioteca_publica', 'true', 'Si la biblioteca es accesible sin login', 'booleano', '2025-08-18 15:16:31'),
(8, 'registro_publico', 'true', 'Si está habilitado el registro público', 'booleano', '2025-08-18 15:16:31'),
(9, 'session_timeout', '3600', 'Tiempo de expiración de sesión en segundos (1 hora)', 'numero', '2025-08-18 15:16:31'),
(10, 'max_login_attempts', '5', 'Máximo número de intentos de login fallidos', 'numero', '2025-08-18 15:16:31'),
(11, 'session_restriction', 'true', 'Restricción de una sesión por usuario', 'booleano', '2025-08-18 15:16:31'),
(12, 'track_sessions', 'true', 'Habilitar seguimiento de sesiones activas', 'booleano', '2025-08-18 15:16:31'),
(13, 'lockout_time', '900', 'Tiempo de bloqueo tras intentos fallidos (15 min)', 'numero', '2025-08-18 15:16:31'),
(14, 'invitado_dias_acceso', '3', 'Días de acceso para usuarios invitados', 'numero', '2025-08-18 15:16:31'),
(15, 'invitado_notificacion_diaria', 'true', 'Enviar notificación diaria a invitados', 'booleano', '2025-08-18 15:16:31'),
(16, 'iva_porcentaje', '13', 'Porcentaje de IVA para ventas', 'numero', '2025-08-18 15:16:31'),
(17, 'descuento_maximo', '20', 'Porcentaje máximo de descuento permitido', 'numero', '2025-08-18 15:16:31'),
(18, 'numeracion_ventas', 'VTA-{YEAR}-{NUMBER}', 'Formato de numeración de ventas', 'texto', '2025-08-18 15:16:31'),
(19, 'porcentaje_ganancia', '30', 'Porcentaje de ganancia para docentes', 'numero', '2025-08-18 15:16:31'),
(20, 'cursos_gratuitos', 'true', 'Permitir cursos gratuitos', 'booleano', '2025-08-27 22:25:22'),
(21, 'libros_gratuitos', 'true', 'Permitir libros gratuitos', 'booleano', '2025-08-27 22:25:22'),
(22, 'max_descargas_usuario_dia', '10', 'Máximo descargas por usuario por día', 'numero', '2025-08-27 22:25:22'),
(23, 'max_inscripciones_usuario', '5', 'Máximo inscripciones activas por usuario', 'numero', '2025-08-27 22:25:22'),
(24, 'generar_certificados', 'true', 'Habilitar generación de certificados', 'booleano', '2025-08-27 22:25:22'),
(25, 'calificacion_minima_certificado', '3.0', 'Calificación mínima para certificado', 'numero', '2025-08-27 22:25:22'),
(26, 'progreso_minimo_certificado', '80.00', 'Progreso mínimo para certificado (%)', 'numero', '2025-08-27 22:25:22'),
(27, 'formato_numero_certificado', 'CERT-{YEAR}-{COURSE}-{NUMBER}', 'Formato de número de certificado', 'texto', '2025-08-27 22:25:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `contenido` longtext DEFAULT NULL,
  `docente_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT 0.00,
  `duracion_horas` int(11) DEFAULT 0,
  `max_estudiantes` int(11) DEFAULT NULL,
  `nivel` enum('Principiante','Intermedio','Avanzado') DEFAULT 'Principiante',
  `modalidad` enum('Presencial','Virtual','Híbrido') DEFAULT 'Virtual',
  `certificado` tinyint(1) DEFAULT 1,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estudiantes_inscritos` int(11) DEFAULT 0,
  `calificacion_promedio` decimal(3,2) DEFAULT 0.00,
  `total_calificaciones` int(11) DEFAULT 0,
  `requisitos` text DEFAULT NULL,
  `objetivos` text DEFAULT NULL,
  `estado` enum('Borrador','Publicado','Archivado') DEFAULT 'Borrador',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `titulo`, `slug`, `descripcion`, `contenido`, `docente_id`, `categoria_id`, `imagen_portada`, `precio`, `duracion_horas`, `max_estudiantes`, `nivel`, `modalidad`, `certificado`, `fecha_inicio`, `fecha_fin`, `estudiantes_inscritos`, `calificacion_promedio`, `total_calificaciones`, `requisitos`, `objetivos`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Introducción a la Robótica', 'introducción-a-la-robótica-1', 'Curso básico de robótica con Arduino', NULL, 2, 1, 'robotica.jpg', 299.00, 20, NULL, 'Principiante', 'Virtual', 1, NULL, NULL, 2, 4.50, 2, NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-27 22:25:22'),
(2, 'Programación en Python', 'programación-en-python-2', 'Aprende Python desde cero', NULL, 3, 2, 'python.jpg', 399.00, 40, NULL, 'Principiante', 'Virtual', 1, NULL, NULL, 2, 3.50, 2, NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-27 22:25:22'),
(3, 'Machine Learning Avanzado', 'machine-learning-avanzado-3', 'Técnicas avanzadas de ML con Python', NULL, 2, 4, 'ml.jpg', 599.00, 60, NULL, 'Avanzado', 'Virtual', 1, NULL, NULL, 0, 0.00, 0, NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-27 22:25:21'),
(4, 'Electrónica Digital', 'electrónica-digital-4', 'Fundamentos de electrónica digital', NULL, 3, 3, 'electronica.jpg', 349.00, 30, NULL, 'Intermedio', 'Virtual', 1, NULL, NULL, 1, 0.00, 0, NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-27 22:25:22'),
(5, 'Análisis de Datos con Pandas', 'analisis-de-datos-con-pandas-5', 'Manejo profesional de datos en Python', NULL, 2, 5, 'pandas.jpg', 449.00, 35, NULL, 'Intermedio', 'Virtual', 1, NULL, NULL, 0, 0.00, 0, NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-27 22:25:21'),
(6, 'Robótica Avanzada con ROS', NULL, 'Desarrollo de robots autónomos usando Robot Operating System', 'Curso completo sobre ROS, navegación autónoma, SLAM y control robótico avanzado', 2, 1, 'ros_robotica.jpg', 899.00, 80, 15, 'Avanzado', 'Híbrido', 1, '2025-09-15', '2025-12-15', 0, 0.00, 0, 'Conocimientos básicos de robótica, programación en Python y C++', 'Dominar ROS, crear robots autónomos, implementar algoritmos de navegación', 'Publicado', '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(7, 'Construcción de Drones DIY', NULL, 'Aprende a construir y programar drones desde cero', 'Desde la selección de componentes hasta el vuelo autónomo', 2, 1, 'drones_diy.jpg', 750.00, 60, 12, 'Intermedio', 'Presencial', 1, '2025-10-01', '2025-11-30', 0, 0.00, 0, 'Conocimientos básicos de electrónica y programación', 'Construir, programar y volar drones autónomos', 'Publicado', '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(8, 'Desarrollo Web Full Stack', NULL, 'Conviértete en desarrollador web profesional', 'HTML, CSS, JavaScript, React, Node.js, MongoDB', 3, 2, 'fullstack_web.jpg', 1299.00, 120, 25, 'Intermedio', 'Virtual', 1, '2025-09-01', '2025-12-20', 0, 0.00, 0, 'Conocimientos básicos de programación', 'Desarrollar aplicaciones web completas y modernas', 'Publicado', '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(9, 'Mobile App Development con Flutter', NULL, 'Desarrollo de aplicaciones móviles multiplataforma', 'Flutter, Dart, Firebase, desarrollo para iOS y Android', 3, 2, 'flutter_mobile.jpg', 950.00, 90, 20, 'Intermedio', 'Virtual', 1, '2025-09-10', '2025-12-10', 0, 0.00, 0, 'Programación orientada a objetos, conocimientos básicos de móvil', 'Crear aplicaciones móviles profesionales', 'Publicado', '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(10, 'Diseño de PCB Profesional', NULL, 'Diseño y fabricación de circuitos impresos', 'KiCad, Eagle, técnicas de diseño, fabricación industrial', 3, 3, 'pcb_design.jpg', 680.00, 45, 18, 'Avanzado', 'Híbrido', 1, '2025-09-20', '2025-11-20', 0, 0.00, 0, 'Conocimientos sólidos de electrónica analógica y digital', 'Diseñar PCBs profesionales y gestionar su fabricación', 'Publicado', '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(11, 'Deep Learning con TensorFlow', NULL, 'Redes neuronales profundas para IA avanzada', 'CNN, RNN, GANs, computer vision, NLP', 2, 4, 'deep_learning.jpg', 1499.00, 100, 15, 'Avanzado', 'Virtual', 1, '2025-09-05', '2025-12-15', 0, 0.00, 0, 'Python avanzado, matemáticas, ML básico', 'Dominar deep learning y crear sistemas de IA avanzados', 'Publicado', '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(12, 'Computer Vision Aplicado', NULL, 'Visión artificial para aplicaciones reales', 'OpenCV, detección de objetos, reconocimiento facial, OCR', 2, 4, 'computer_vision.jpg', 850.00, 65, 20, 'Intermedio', 'Virtual', 1, '2025-09-25', '2025-11-25', 0, 0.00, 0, 'Python, matemáticas básicas, ML introductorio', 'Implementar sistemas de visión artificial', 'Publicado', '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(13, 'Robótica Avanzada con ROS', NULL, 'Desarrollo de robots autónomos usando Robot Operating System', 'Curso completo sobre ROS, navegación autónoma, SLAM y control robótico avanzado', 2, 1, 'ros_robotica.jpg', 899.00, 80, 15, 'Avanzado', 'Híbrido', 1, '2025-09-15', '2025-12-15', 0, 0.00, 0, 'Conocimientos básicos de robótica, programación en Python y C++', 'Dominar ROS, crear robots autónomos, implementar algoritmos de navegación', 'Publicado', '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(14, 'Construcción de Drones DIY', NULL, 'Aprende a construir y programar drones desde cero', 'Desde la selección de componentes hasta el vuelo autónomo', 2, 1, 'drones_diy.jpg', 750.00, 60, 12, 'Intermedio', 'Presencial', 1, '2025-10-01', '2025-11-30', 0, 0.00, 0, 'Conocimientos básicos de electrónica y programación', 'Construir, programar y volar drones autónomos', 'Publicado', '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(15, 'Desarrollo Web Full Stack', NULL, 'Conviértete en desarrollador web profesional', 'HTML, CSS, JavaScript, React, Node.js, MongoDB', 3, 2, 'fullstack_web.jpg', 1299.00, 120, 25, 'Intermedio', 'Virtual', 1, '2025-09-01', '2025-12-20', 0, 0.00, 0, 'Conocimientos básicos de programación', 'Desarrollar aplicaciones web completas y modernas', 'Publicado', '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(16, 'Mobile App Development con Flutter', NULL, 'Desarrollo de aplicaciones móviles multiplataforma', 'Flutter, Dart, Firebase, desarrollo para iOS y Android', 3, 2, 'flutter_mobile.jpg', 950.00, 90, 20, 'Intermedio', 'Virtual', 1, '2025-09-10', '2025-12-10', 0, 0.00, 0, 'Programación orientada a objetos, conocimientos básicos de móvil', 'Crear aplicaciones móviles profesionales', 'Publicado', '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(17, 'Diseño de PCB Profesional', NULL, 'Diseño y fabricación de circuitos impresos', 'KiCad, Eagle, técnicas de diseño, fabricación industrial', 3, 3, 'pcb_design.jpg', 680.00, 45, 18, 'Avanzado', 'Híbrido', 1, '2025-09-20', '2025-11-20', 0, 0.00, 0, 'Conocimientos sólidos de electrónica analógica y digital', 'Diseñar PCBs profesionales y gestionar su fabricación', 'Publicado', '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(18, 'Deep Learning con TensorFlow', NULL, 'Redes neuronales profundas para IA avanzada', 'CNN, RNN, GANs, computer vision, NLP', 2, 4, 'deep_learning.jpg', 1499.00, 100, 15, 'Avanzado', 'Virtual', 1, '2025-09-05', '2025-12-15', 0, 0.00, 0, 'Python avanzado, matemáticas, ML básico', 'Dominar deep learning y crear sistemas de IA avanzados', 'Publicado', '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(19, 'Computer Vision Aplicado', NULL, 'Visión artificial para aplicaciones reales', 'OpenCV, detección de objetos, reconocimiento facial, OCR', 2, 4, 'computer_vision.jpg', 850.00, 65, 20, 'Intermedio', 'Virtual', 1, '2025-09-25', '2025-11-25', 0, 0.00, 0, 'Python, matemáticas básicas, ML introductorio', 'Implementar sistemas de visión artificial', 'Publicado', '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(20, 'Robótica Avanzada con ROS', NULL, 'Desarrollo de robots autónomos usando Robot Operating System', 'Curso completo sobre ROS, navegación autónoma, SLAM y control robótico avanzado', 2, 1, 'ros_robotica.jpg', 899.00, 80, 15, 'Avanzado', 'Híbrido', 1, '2025-09-15', '2025-12-15', 0, 0.00, 0, 'Conocimientos básicos de robótica, programación en Python y C++', 'Dominar ROS, crear robots autónomos, implementar algoritmos de navegación', 'Publicado', '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(21, 'Construcción de Drones DIY', NULL, 'Aprende a construir y programar drones desde cero', 'Desde la selección de componentes hasta el vuelo autónomo', 2, 1, 'drones_diy.jpg', 750.00, 60, 12, 'Intermedio', 'Presencial', 1, '2025-10-01', '2025-11-30', 0, 0.00, 0, 'Conocimientos básicos de electrónica y programación', 'Construir, programar y volar drones autónomos', 'Publicado', '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(22, 'Desarrollo Web Full Stack', NULL, 'Conviértete en desarrollador web profesional', 'HTML, CSS, JavaScript, React, Node.js, MongoDB', 3, 2, 'fullstack_web.jpg', 1299.00, 120, 25, 'Intermedio', 'Virtual', 1, '2025-09-01', '2025-12-20', 0, 0.00, 0, 'Conocimientos básicos de programación', 'Desarrollar aplicaciones web completas y modernas', 'Publicado', '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(23, 'Mobile App Development con Flutter', NULL, 'Desarrollo de aplicaciones móviles multiplataforma', 'Flutter, Dart, Firebase, desarrollo para iOS y Android', 3, 2, 'flutter_mobile.jpg', 950.00, 90, 20, 'Intermedio', 'Virtual', 1, '2025-09-10', '2025-12-10', 0, 0.00, 0, 'Programación orientada a objetos, conocimientos básicos de móvil', 'Crear aplicaciones móviles profesionales', 'Publicado', '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(24, 'Diseño de PCB Profesional', NULL, 'Diseño y fabricación de circuitos impresos', 'KiCad, Eagle, técnicas de diseño, fabricación industrial', 3, 3, 'pcb_design.jpg', 680.00, 45, 18, 'Avanzado', 'Híbrido', 1, '2025-09-20', '2025-11-20', 0, 0.00, 0, 'Conocimientos sólidos de electrónica analógica y digital', 'Diseñar PCBs profesionales y gestionar su fabricación', 'Publicado', '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(25, 'Deep Learning con TensorFlow', NULL, 'Redes neuronales profundas para IA avanzada', 'CNN, RNN, GANs, computer vision, NLP', 2, 4, 'deep_learning.jpg', 1499.00, 100, 15, 'Avanzado', 'Virtual', 1, '2025-09-05', '2025-12-15', 0, 0.00, 0, 'Python avanzado, matemáticas, ML básico', 'Dominar deep learning y crear sistemas de IA avanzados', 'Publicado', '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(26, 'Computer Vision Aplicado', NULL, 'Visión artificial para aplicaciones reales', 'OpenCV, detección de objetos, reconocimiento facial, OCR', 2, 4, 'computer_vision.jpg', 850.00, 65, 20, 'Intermedio', 'Virtual', 1, '2025-09-25', '2025-11-25', 0, 0.00, 0, 'Python, matemáticas básicas, ML introductorio', 'Implementar sistemas de visión artificial', 'Publicado', '2025-08-27 22:28:49', '2025-08-27 22:28:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descargas_libros`
--

CREATE TABLE `descargas_libros` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `fecha_descarga` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `descargas_libros`
--

INSERT INTO `descargas_libros` (`id`, `usuario_id`, `libro_id`, `ip_address`, `user_agent`, `fecha_descarga`) VALUES
(1, 4, 2, '192.168.1.1', NULL, '2025-08-18 15:16:31'),
(2, 5, 2, '192.168.1.2', NULL, '2025-08-18 15:16:31'),
(3, 5, 5, '192.168.1.2', NULL, '2025-08-18 15:16:31'),
(4, 4, 5, '192.168.1.1', NULL, '2025-08-18 15:16:31'),
(5, 6, 2, '192.168.1.3', NULL, '2025-08-18 15:16:31'),
(6, 8, 1, '192.168.1.4', NULL, '2025-08-18 15:16:31');

--
-- Disparadores `descargas_libros`
--
DELIMITER $$
CREATE TRIGGER `tr_actualizar_descargas_libro` AFTER INSERT ON `descargas_libros` FOR EACH ROW BEGIN
    UPDATE `libros` 
    SET `descargas_totales` = `descargas_totales` + 1
    WHERE `id` = NEW.libro_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `tipo_producto` enum('libro','componente') NOT NULL,
  `producto_id` int(11) NOT NULL,
  `nombre_producto` varchar(200) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `venta_id`, `tipo_producto`, `producto_id`, `nombre_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 'componente', 17, 'Kit Básico Arduino para Principiantes', 1, 180.00, 180.00),
(2, 2, 'componente', 14, 'Soldador de Estaño 40W', 1, 85.00, 85.00),
(3, 3, 'componente', 18, 'Kit Avanzado de Robótica', 1, 350.00, 350.00),
(4, 4, 'libro', 1, 'Robótica Práctica con Arduino', 1, 150.00, 150.00),
(5, 4, 'componente', 1, 'Arduino UNO R3', 2, 45.00, 90.00),
(6, 4, 'componente', 4, 'Sensor Ultrasónico HC-SR04', 4, 8.00, 32.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos_cursos`
--

CREATE TABLE `favoritos_cursos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos_libros`
--

CREATE TABLE `favoritos_libros` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones_cursos`
--

CREATE TABLE `inscripciones_cursos` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('Activa','Completada','Cancelada','Pausada') DEFAULT 'Activa',
  `metodo_pago` enum('Gratuito','Efectivo','Transferencia','Tarjeta','QR') DEFAULT 'Gratuito',
  `monto_pagado` decimal(10,2) DEFAULT 0.00,
  `fecha_completado` timestamp NULL DEFAULT NULL,
  `certificado_emitido` tinyint(1) DEFAULT 0,
  `fecha_certificado` timestamp NULL DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inscripciones_cursos`
--

INSERT INTO `inscripciones_cursos` (`id`, `estudiante_id`, `curso_id`, `fecha_inscripcion`, `estado`, `metodo_pago`, `monto_pagado`, `fecha_completado`, `certificado_emitido`, `fecha_certificado`, `notas`, `fecha_actualizacion`) VALUES
(1, 4, 1, '2025-08-27 22:25:22', 'Activa', 'Gratuito', 0.00, NULL, 0, NULL, NULL, '2025-08-27 22:25:22'),
(2, 4, 2, '2025-08-27 22:25:22', 'Completada', 'Transferencia', 0.00, NULL, 0, NULL, NULL, '2025-08-27 22:25:22'),
(3, 5, 1, '2025-08-27 22:25:22', 'Activa', 'Gratuito', 0.00, NULL, 0, NULL, NULL, '2025-08-27 22:25:22'),
(4, 5, 4, '2025-08-27 22:25:22', 'Activa', 'Tarjeta', 0.00, NULL, 0, NULL, NULL, '2025-08-27 22:25:22'),
(5, 8, 2, '2025-08-27 22:25:22', 'Pausada', 'Efectivo', 0.00, NULL, 0, NULL, NULL, '2025-08-27 22:25:22');

--
-- Disparadores `inscripciones_cursos`
--
DELIMITER $$
CREATE TRIGGER `tr_actualizar_inscritos_curso` AFTER INSERT ON `inscripciones_cursos` FOR EACH ROW BEGIN
    UPDATE `cursos` 
    SET `estudiantes_inscritos` = `estudiantes_inscritos` + 1
    WHERE `id` = NEW.curso_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_login`
--

CREATE TABLE `intentos_login` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `exito` tinyint(1) DEFAULT 0,
  `fecha_intento` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `autor` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `paginas` int(11) DEFAULT 0,
  `editorial` varchar(100) DEFAULT NULL,
  `año_publicacion` year(4) DEFAULT NULL,
  `idioma` varchar(50) DEFAULT 'Español',
  `formato` enum('PDF','EPUB','MOBI','Físico','Digital') DEFAULT 'PDF',
  `descargas_totales` int(11) DEFAULT 0,
  `calificacion_promedio` decimal(3,2) DEFAULT 0.00,
  `total_calificaciones` int(11) DEFAULT 0,
  `palabras_clave` text DEFAULT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `archivo_pdf` varchar(500) DEFAULT NULL,
  `enlace_externo` varchar(500) DEFAULT NULL,
  `tamaño_archivo` int(11) DEFAULT 0,
  `stock` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 5,
  `precio` decimal(10,2) DEFAULT 0.00,
  `es_gratuito` tinyint(1) DEFAULT 1,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `slug`, `autor`, `descripcion`, `categoria_id`, `isbn`, `paginas`, `editorial`, `año_publicacion`, `idioma`, `formato`, `descargas_totales`, `calificacion_promedio`, `total_calificaciones`, `palabras_clave`, `imagen_portada`, `archivo_pdf`, `enlace_externo`, `tamaño_archivo`, `stock`, `stock_minimo`, `precio`, `es_gratuito`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Robótica Práctica con Arduino', 'robótica-practica-con-arduino-1', 'Juan Martínez', 'Guía completa para proyectos de robótica con Arduino', 6, '978-1234567890', 320, 'Editorial Tech', '2022', 'Español', 'PDF', 1, 0.00, 0, NULL, 'robotica_arduino.jpg', '/libros/robotica_arduino.pdf', NULL, 0, 25, 5, 150.00, 0, 1, '2025-08-18 15:16:31', '2025-08-27 22:25:21'),
(2, 'Python para Principiantes', 'python-para-principiantes-2', 'Ana López', 'Introducción al lenguaje Python desde cero', 7, '978-0987654321', 280, 'Code Press', '2021', 'Español', 'PDF', 3, 4.50, 2, NULL, 'python_principiantes.jpg', '/libros/python_principiantes.pdf', NULL, 0, 30, 5, 120.00, 0, 1, '2025-08-18 15:16:31', '2025-08-27 22:25:22'),
(3, 'Fundamentos de Electrónica', 'fundamentos-de-electrónica-3', 'Carlos Sánchez', 'Teoría y práctica de circuitos electrónicos', 8, '978-5432109876', 450, 'Electro Books', '2020', 'Español', 'PDF', 0, 0.00, 0, NULL, 'fundamentos_electronica.jpg', '/libros/fundamentos_electronica.pdf', NULL, 0, 15, 3, 200.00, 0, 1, '2025-08-18 15:16:31', '2025-08-27 22:25:21'),
(4, 'Machine Learning Avanzado', 'machine-learning-avanzado-4', 'María García', 'Técnicas avanzadas de aprendizaje automático', 9, '978-6789054321', 380, 'AI Publications', '2023', 'Español', 'PDF', 0, 0.00, 0, NULL, 'ml_avanzado.jpg', '/libros/ml_avanzado.pdf', NULL, 0, 20, 5, 250.00, 0, 1, '2025-08-18 15:16:31', '2025-08-27 22:25:21'),
(5, 'Matemáticas para Ingenieros', 'matematicas-para-ingenieros-5', 'Pedro Fernández', 'Fundamentos matemáticos para ingeniería', 10, '978-1234509876', 310, 'Math Ed', '2022', 'Español', 'PDF', 2, 4.00, 1, NULL, 'matematicas.jpg', '/libros/matematicas.pdf', NULL, 0, 40, 8, 180.00, 0, 1, '2025-08-18 15:16:31', '2025-08-27 22:25:22'),
(6, 'ROS Robot Programming', NULL, 'Yoonseok Pyo, Ryu Woongjae, Lim Hyungjoo', 'Guía completa de programación con Robot Operating System', 6, '978-1495229978', 450, 'ROBOTIS', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'ROS, robótica, programación, sistemas autónomos', 'ros_programming.jpg', '/libros/ros_programming.pdf', NULL, 15728640, 5, 2, 89.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(7, 'Autonomous Mobile Robots', NULL, 'Roland Siegwart, Illah Nourbakhsh', 'Fundamentos teóricos y prácticos de robots móviles autónomos', 6, '978-0262015356', 512, 'MIT Press', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'robots móviles, navegación, SLAM, sensores', 'autonomous_robots.jpg', '/libros/autonomous_robots.pdf', NULL, 25165824, 8, 3, 125.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(8, 'Clean Code in Python', NULL, 'Mariano Anaya', 'Principios y técnicas para escribir código Python mantenible', 7, '978-1788835830', 480, 'Packt Publishing', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'Python, clean code, buenas prácticas, desarrollo', 'clean_code_python.jpg', '/libros/clean_code_python.pdf', NULL, 12582912, 15, 5, 75.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(9, 'JavaScript: The Definitive Guide', NULL, 'David Flanagan', 'Guía completa y definitiva del lenguaje JavaScript', 7, '978-1491952023', 688, 'O\'Reilly Media', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'JavaScript, web development, ES2023, programación', 'javascript_guide.jpg', '/libros/javascript_guide.pdf', NULL, 18874368, 12, 4, 95.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(10, 'React Patterns', NULL, 'Michael Chan', 'Patrones avanzados y mejores prácticas en React', 7, '978-1787127685', 350, 'Packt Publishing', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'React, patterns, componentes, hooks', 'react_patterns.jpg', '/libros/react_patterns.pdf', NULL, 10485760, 10, 3, 68.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(11, 'High-Speed Digital Design', NULL, 'Howard Johnson, Martin Graham', 'Diseño de circuitos digitales de alta velocidad', 8, '978-0133957242', 624, 'Prentice Hall', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'circuitos digitales, PCB, alta velocidad, EMI', 'high_speed_digital.jpg', '/libros/high_speed_digital.pdf', NULL, 32505856, 6, 2, 158.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(12, 'Practical Electronics for Inventors', NULL, 'Paul Scherz, Simon Monk', 'Electrónica práctica para inventores e innovadores', 8, '978-1259587542', 1056, 'McGraw-Hill', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'electrónica práctica, componentes, circuitos, inventos', 'practical_electronics.jpg', '/libros/practical_electronics.pdf', NULL, 45088768, 20, 5, 118.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(13, 'Hands-On Machine Learning', NULL, 'Aurélien Géron', 'Machine Learning práctico con Scikit-Learn y TensorFlow', 9, '978-1492032649', 856, 'O\'Reilly Media', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'machine learning, scikit-learn, tensorflow, prático', 'hands_on_ml.jpg', '/libros/hands_on_ml.pdf', NULL, 38797312, 18, 5, 145.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(14, 'Deep Learning', NULL, 'Ian Goodfellow, Yoshua Bengio, Aaron Courville', 'El libro definitivo sobre deep learning', 9, '978-0262035613', 800, 'MIT Press', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'deep learning, neural networks, AI, teoría', 'deep_learning_book.jpg', '/libros/deep_learning_book.pdf', NULL, 42991616, 12, 3, 189.00, 0, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(15, 'Introduction to Algorithms (Free Edition)', NULL, 'Thomas Cormen, Charles Leiserson', 'Introducción gratuita a algoritmos fundamentales', 10, '978-0262033848', 200, 'Tech Home Press', '2024', 'Español', 'PDF', 0, 0.00, 0, 'algoritmos, estructuras de datos, gratuito, introducción', 'intro_algorithms.jpg', '/libros/intro_algorithms.pdf', NULL, 8388608, 0, 0, 0.00, 1, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(16, 'Open Source Robotics', NULL, 'Community Authors', 'Robótica de código abierto para todos', 6, '978-0000000001', 180, 'Open Robotics', '2024', 'Español', 'PDF', 0, 0.00, 0, 'robótica, open source, arduino, gratuito', 'open_robotics.jpg', '/libros/open_robotics.pdf', NULL, 6291456, 0, 0, 0.00, 1, 1, '2025-08-27 22:26:03', '2025-08-27 22:26:03'),
(17, 'ROS Robot Programming', NULL, 'Yoonseok Pyo, Ryu Woongjae, Lim Hyungjoo', 'Guía completa de programación con Robot Operating System', 6, '978-1495229978', 450, 'ROBOTIS', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'ROS, robótica, programación, sistemas autónomos', 'ros_programming.jpg', '/libros/ros_programming.pdf', NULL, 15728640, 5, 2, 89.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(18, 'Autonomous Mobile Robots', NULL, 'Roland Siegwart, Illah Nourbakhsh', 'Fundamentos teóricos y prácticos de robots móviles autónomos', 6, '978-0262015356', 512, 'MIT Press', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'robots móviles, navegación, SLAM, sensores', 'autonomous_robots.jpg', '/libros/autonomous_robots.pdf', NULL, 25165824, 8, 3, 125.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(19, 'Clean Code in Python', NULL, 'Mariano Anaya', 'Principios y técnicas para escribir código Python mantenible', 7, '978-1788835830', 480, 'Packt Publishing', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'Python, clean code, buenas prácticas, desarrollo', 'clean_code_python.jpg', '/libros/clean_code_python.pdf', NULL, 12582912, 15, 5, 75.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(20, 'JavaScript: The Definitive Guide', NULL, 'David Flanagan', 'Guía completa y definitiva del lenguaje JavaScript', 7, '978-1491952023', 688, 'O\'Reilly Media', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'JavaScript, web development, ES2023, programación', 'javascript_guide.jpg', '/libros/javascript_guide.pdf', NULL, 18874368, 12, 4, 95.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(21, 'React Patterns', NULL, 'Michael Chan', 'Patrones avanzados y mejores prácticas en React', 7, '978-1787127685', 350, 'Packt Publishing', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'React, patterns, componentes, hooks', 'react_patterns.jpg', '/libros/react_patterns.pdf', NULL, 10485760, 10, 3, 68.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(22, 'High-Speed Digital Design', NULL, 'Howard Johnson, Martin Graham', 'Diseño de circuitos digitales de alta velocidad', 8, '978-0133957242', 624, 'Prentice Hall', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'circuitos digitales, PCB, alta velocidad, EMI', 'high_speed_digital.jpg', '/libros/high_speed_digital.pdf', NULL, 32505856, 6, 2, 158.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(23, 'Practical Electronics for Inventors', NULL, 'Paul Scherz, Simon Monk', 'Electrónica práctica para inventores e innovadores', 8, '978-1259587542', 1056, 'McGraw-Hill', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'electrónica práctica, componentes, circuitos, inventos', 'practical_electronics.jpg', '/libros/practical_electronics.pdf', NULL, 45088768, 20, 5, 118.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(24, 'Hands-On Machine Learning', NULL, 'Aurélien Géron', 'Machine Learning práctico con Scikit-Learn y TensorFlow', 9, '978-1492032649', 856, 'O\'Reilly Media', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'machine learning, scikit-learn, tensorflow, prático', 'hands_on_ml.jpg', '/libros/hands_on_ml.pdf', NULL, 38797312, 18, 5, 145.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(25, 'Deep Learning', NULL, 'Ian Goodfellow, Yoshua Bengio, Aaron Courville', 'El libro definitivo sobre deep learning', 9, '978-0262035613', 800, 'MIT Press', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'deep learning, neural networks, AI, teoría', 'deep_learning_book.jpg', '/libros/deep_learning_book.pdf', NULL, 42991616, 12, 3, 189.00, 0, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(26, 'Introduction to Algorithms (Free Edition)', NULL, 'Thomas Cormen, Charles Leiserson', 'Introducción gratuita a algoritmos fundamentales', 10, '978-0262033848', 200, 'Tech Home Press', '2024', 'Español', 'PDF', 0, 0.00, 0, 'algoritmos, estructuras de datos, gratuito, introducción', 'intro_algorithms.jpg', '/libros/intro_algorithms.pdf', NULL, 8388608, 0, 0, 0.00, 1, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(27, 'Open Source Robotics', NULL, 'Community Authors', 'Robótica de código abierto para todos', 6, '978-0000000001', 180, 'Open Robotics', '2024', 'Español', 'PDF', 0, 0.00, 0, 'robótica, open source, arduino, gratuito', 'open_robotics.jpg', '/libros/open_robotics.pdf', NULL, 6291456, 0, 0, 0.00, 1, 1, '2025-08-27 22:28:16', '2025-08-27 22:28:16'),
(28, 'ROS Robot Programming', NULL, 'Yoonseok Pyo, Ryu Woongjae, Lim Hyungjoo', 'Guía completa de programación con Robot Operating System', 6, '978-1495229978', 450, 'ROBOTIS', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'ROS, robótica, programación, sistemas autónomos', 'ros_programming.jpg', '/libros/ros_programming.pdf', NULL, 15728640, 5, 2, 89.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(29, 'Autonomous Mobile Robots', NULL, 'Roland Siegwart, Illah Nourbakhsh', 'Fundamentos teóricos y prácticos de robots móviles autónomos', 6, '978-0262015356', 512, 'MIT Press', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'robots móviles, navegación, SLAM, sensores', 'autonomous_robots.jpg', '/libros/autonomous_robots.pdf', NULL, 25165824, 8, 3, 125.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(30, 'Clean Code in Python', NULL, 'Mariano Anaya', 'Principios y técnicas para escribir código Python mantenible', 7, '978-1788835830', 480, 'Packt Publishing', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'Python, clean code, buenas prácticas, desarrollo', 'clean_code_python.jpg', '/libros/clean_code_python.pdf', NULL, 12582912, 15, 5, 75.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(31, 'JavaScript: The Definitive Guide', NULL, 'David Flanagan', 'Guía completa y definitiva del lenguaje JavaScript', 7, '978-1491952023', 688, 'O\'Reilly Media', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'JavaScript, web development, ES2023, programación', 'javascript_guide.jpg', '/libros/javascript_guide.pdf', NULL, 18874368, 12, 4, 95.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(32, 'React Patterns', NULL, 'Michael Chan', 'Patrones avanzados y mejores prácticas en React', 7, '978-1787127685', 350, 'Packt Publishing', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'React, patterns, componentes, hooks', 'react_patterns.jpg', '/libros/react_patterns.pdf', NULL, 10485760, 10, 3, 68.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(33, 'High-Speed Digital Design', NULL, 'Howard Johnson, Martin Graham', 'Diseño de circuitos digitales de alta velocidad', 8, '978-0133957242', 624, 'Prentice Hall', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'circuitos digitales, PCB, alta velocidad, EMI', 'high_speed_digital.jpg', '/libros/high_speed_digital.pdf', NULL, 32505856, 6, 2, 158.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(34, 'Practical Electronics for Inventors', NULL, 'Paul Scherz, Simon Monk', 'Electrónica práctica para inventores e innovadores', 8, '978-1259587542', 1056, 'McGraw-Hill', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'electrónica práctica, componentes, circuitos, inventos', 'practical_electronics.jpg', '/libros/practical_electronics.pdf', NULL, 45088768, 20, 5, 118.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(35, 'Hands-On Machine Learning', NULL, 'Aurélien Géron', 'Machine Learning práctico con Scikit-Learn y TensorFlow', 9, '978-1492032649', 856, 'O\'Reilly Media', '2024', 'Inglés', 'PDF', 0, 0.00, 0, 'machine learning, scikit-learn, tensorflow, prático', 'hands_on_ml.jpg', '/libros/hands_on_ml.pdf', NULL, 38797312, 18, 5, 145.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(36, 'Deep Learning', NULL, 'Ian Goodfellow, Yoshua Bengio, Aaron Courville', 'El libro definitivo sobre deep learning', 9, '978-0262035613', 800, 'MIT Press', '2023', 'Inglés', 'PDF', 0, 0.00, 0, 'deep learning, neural networks, AI, teoría', 'deep_learning_book.jpg', '/libros/deep_learning_book.pdf', NULL, 42991616, 12, 3, 189.00, 0, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(37, 'Introduction to Algorithms (Free Edition)', NULL, 'Thomas Cormen, Charles Leiserson', 'Introducción gratuita a algoritmos fundamentales', 10, '978-0262033848', 200, 'Tech Home Press', '2024', 'Español', 'PDF', 0, 0.00, 0, 'algoritmos, estructuras de datos, gratuito, introducción', 'intro_algorithms.jpg', '/libros/intro_algorithms.pdf', NULL, 8388608, 0, 0, 0.00, 1, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49'),
(38, 'Open Source Robotics', NULL, 'Community Authors', 'Robótica de código abierto para todos', 6, '978-0000000001', 180, 'Open Robotics', '2024', 'Español', 'PDF', 0, 0.00, 0, 'robótica, open source, arduino, gratuito', 'open_robotics.jpg', '/libros/open_robotics.pdf', NULL, 6291456, 0, 0, 0.00, 1, 1, '2025-08-27 22:28:49', '2025-08-27 22:28:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(6, 'App\\Models\\User', 6),
(6, 'App\\Models\\User', 9),
(7, 'App\\Models\\User', 9),
(8, 'App\\Models\\User', 9),
(9, 'App\\Models\\User', 9),
(38, 'App\\Models\\User', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(11) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 15),
(1, 'App\\Models\\User', 21),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 11),
(2, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 10),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 18),
(7, 'App\\Models\\User', 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos_curso`
--

CREATE TABLE `modulos_curso` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `contenido` longtext DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `duracion_minutos` int(11) DEFAULT 0,
  `recursos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recursos`)),
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modulos_curso`
--

INSERT INTO `modulos_curso` (`id`, `curso_id`, `titulo`, `descripcion`, `contenido`, `orden`, `duracion_minutos`, `recursos`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'Introducción a la Robótica', 'Conceptos básicos y fundamentos', NULL, 1, 120, NULL, 1, '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(2, 1, 'Hardware de Arduino', 'Conociendo la placa Arduino UNO', NULL, 2, 90, NULL, 1, '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(3, 1, 'Primeros pasos con código', 'Programación básica en Arduino IDE', NULL, 3, 150, NULL, 1, '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(4, 2, 'Fundamentos de Python', 'Variables, tipos de datos y operadores', NULL, 1, 180, NULL, 1, '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(5, 2, 'Control de flujo', 'Condicionales y bucles', NULL, 2, 120, NULL, 1, '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(6, 2, 'Funciones y módulos', 'Programación modular en Python', NULL, 3, 150, NULL, 1, '2025-08-27 22:25:22', '2025-08-27 22:25:22'),
(7, 6, 'Introducción a ROS', 'Conceptos fundamentales del Robot Operating System', NULL, 1, 180, '{\"videos\": [\"intro_ros.mp4\"], \"documentos\": [\"ros_basics.pdf\"], \"codigo\": [\"ros_examples.zip\"]}', 1, '2025-08-27 22:26:04', '2025-08-27 22:26:04'),
(8, 6, 'Nodos y Comunicación', 'Publishers, Subscribers y Services', NULL, 2, 240, '{\"videos\": [\"nodes_comm.mp4\"], \"practicas\": [\"publisher_example.py\", \"subscriber_example.py\"]}', 1, '2025-08-27 22:26:04', '2025-08-27 22:26:04'),
(9, 6, 'Navegación y SLAM', 'Simultaneous Localization and Mapping', NULL, 3, 300, '{\"videos\": [\"slam_nav.mp4\"], \"simulaciones\": [\"gazebo_world.world\"], \"algoritmos\": [\"slam_implementation.py\"]}', 1, '2025-08-27 22:26:04', '2025-08-27 22:26:04'),
(10, 6, 'Control Avanzado', 'PID, MPC y control adaptativo', NULL, 4, 240, '{\"videos\": [\"advanced_control.mp4\"], \"matlab\": [\"control_examples.m\"], \"python\": [\"pid_controller.py\"]}', 1, '2025-08-27 22:26:04', '2025-08-27 22:26:04'),
(11, 8, 'Frontend Moderno', 'React, Redux, Material-UI', NULL, 1, 360, '{\"videos\": [\"react_intro.mp4\"], \"proyectos\": [\"todo_app\"], \"componentes\": [\"reusable_components.zip\"]}', 1, '2025-08-27 22:26:04', '2025-08-27 22:26:04'),
(12, 8, 'Backend con Node.js', 'Express, APIs REST, autenticación', NULL, 2, 420, '{\"videos\": [\"nodejs_backend.mp4\"], \"apis\": [\"rest_api_example\"], \"auth\": [\"jwt_implementation.js\"]}', 1, '2025-08-27 22:26:04', '2025-08-27 22:26:04'),
(13, 8, 'Base de Datos', 'MongoDB, Mongoose, optimización', NULL, 3, 300, '{\"videos\": [\"mongodb_tutorial.mp4\"], \"schemas\": [\"user_schema.js\"], \"queries\": [\"advanced_queries.js\"]}', 1, '2025-08-27 22:26:04', '2025-08-27 22:26:04'),
(14, 8, 'Deployment y DevOps', 'Docker, AWS, CI/CD', NULL, 4, 240, '{\"videos\": [\"deployment.mp4\"], \"docker\": [\"Dockerfile\"], \"aws\": [\"deployment_guide.pdf\"]}', 1, '2025-08-27 22:26:04', '2025-08-27 22:26:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `email`, `token`, `expires_at`, `used`, `created_at`) VALUES
(2, 'admin@techhome.bo', 'daf9f58b874dcf8a7df556a053f649d5b113c5d4e616946bda19aa2468fb73ce', '2025-08-19 15:08:55', 0, '2025-08-19 14:53:55'),
(10, 'luisrochavelaa1@gmail.com', 'b8706d4679c0b8d9db9183b48489704a556ba0d9f9b2d8171537cd79ae8989a3', '2025-08-20 00:07:30', 0, '2025-08-19 23:52:30'),
(11, 'jhoel0521@gmail.com', '3e91ca19e5d956cf38ab7a30b30ebb6d1fdd1edad6e5be755c0e22a2a62631f5', '2025-08-20 00:08:29', 0, '2025-08-19 23:53:29'),
(20, 'luisrochavela1@gmail.com', 'f2b0de26371a353964e1565d9f22e2478a715eef8ce7c679d1ad46425d6299db', '2025-08-20 14:08:57', 0, '2025-08-20 13:53:57'),
(21, 'naxelf666@gmail.com', 'b8b09e0e252f99e7afac81ffd2dac6e33388458fa86e6835cfef297f54e1582e', '2025-08-20 14:12:30', 0, '2025-08-20 13:57:30'),
(23, 'leonardopenaanez@gmail.com', '6fd1abd954ba83e0dbe938b25683cb8ec352ed4f4e92451af6e7135b9acb0bde', '2025-08-25 13:33:35', 1, '2025-08-25 13:18:35'),
(24, 'douglasdfh88@gmail.com', 'bcc17aca6fe7e2fafd8da08ca7106fbd07497cdde59edc6048ec52986a75ce7f', '2025-08-25 13:46:34', 1, '2025-08-25 13:31:34'),
(25, 'tantani.m.g@gmail.com', '77675369749b5e14dd74af19334372b18ddd81835fddab5a4705252ba8236233', '2025-08-25 13:51:18', 1, '2025-08-25 13:36:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'login', 'web', NULL, NULL),
(2, 'logout', 'web', NULL, NULL),
(3, 'admin.dashboard', 'web', NULL, NULL),
(4, 'admin.reportes', 'web', NULL, NULL),
(5, 'admin.configuracion', 'web', NULL, NULL),
(6, 'admin.usuarios.ver', 'web', NULL, NULL),
(7, 'admin.usuarios.crear', 'web', NULL, NULL),
(8, 'admin.usuarios.editar', 'web', NULL, NULL),
(9, 'admin.usuarios.eliminar', 'web', NULL, NULL),
(10, 'admin.ventas.ver', 'web', NULL, NULL),
(11, 'admin.ventas.crear', 'web', NULL, NULL),
(12, 'admin.ventas.editar', 'web', NULL, NULL),
(13, 'admin.ventas.eliminar', 'web', NULL, NULL),
(14, 'estudiantes.dashboard', 'web', NULL, NULL),
(15, 'cursos.ver', 'web', NULL, NULL),
(16, 'cursos.crear', 'web', NULL, NULL),
(17, 'cursos.editar', 'web', NULL, NULL),
(18, 'cursos.eliminar', 'web', NULL, NULL),
(19, 'libros.ver', 'web', NULL, NULL),
(20, 'libros.crear', 'web', NULL, NULL),
(21, 'libros.editar', 'web', NULL, NULL),
(22, 'libros.eliminar', 'web', NULL, NULL),
(23, 'libros.descargar', 'web', NULL, NULL),
(24, 'materiales.ver', 'web', NULL, NULL),
(25, 'materiales.crear', 'web', NULL, NULL),
(26, 'materiales.editar', 'web', NULL, NULL),
(27, 'materiales.eliminar', 'web', NULL, NULL),
(28, 'laboratorios.ver', 'web', NULL, NULL),
(29, 'laboratorios.crear', 'web', NULL, NULL),
(30, 'laboratorios.editar', 'web', NULL, NULL),
(31, 'laboratorios.eliminar', 'web', NULL, NULL),
(32, 'componentes.ver', 'web', NULL, NULL),
(33, 'componentes.crear', 'web', NULL, NULL),
(34, 'componentes.editar', 'web', NULL, NULL),
(35, 'componentes.eliminar', 'web', NULL, NULL),
(36, 'docente.dashboard', 'web', NULL, NULL),
(37, 'api.verify_session', 'web', NULL, NULL),
(38, 'admin.usuarios.roles', 'web', '2025-08-19 13:14:55', '2025-08-19 13:14:55'),
(39, 'admin.usuarios.permisos', 'web', '2025-08-19 14:02:01', '2025-08-19 14:02:01'),
(40, 'admin.libros', 'web', NULL, NULL),
(41, 'admin.libros.crear', 'web', NULL, NULL),
(42, 'admin.libros.editar', 'web', NULL, NULL),
(43, 'admin.libros.eliminar', 'web', NULL, NULL),
(44, 'admin.libros.ver', 'web', NULL, NULL),
(45, 'admin.cursos', 'web', NULL, NULL),
(46, 'admin.cursos.crear', 'web', NULL, NULL),
(47, 'admin.cursos.editar', 'web', NULL, NULL),
(48, 'admin.cursos.eliminar', 'web', NULL, NULL),
(49, 'admin.cursos.ver', 'web', NULL, NULL),
(50, 'cursos.inscribir', 'web', NULL, NULL),
(51, 'cursos.calificar', 'web', NULL, NULL),
(52, 'libros.calificar', 'web', NULL, NULL),
(53, 'libros.favoritos', 'web', NULL, NULL),
(54, 'cursos.favoritos', 'web', NULL, NULL),
(55, 'modulos.ver', 'web', NULL, NULL),
(56, 'modulos.crear', 'web', NULL, NULL),
(57, 'modulos.editar', 'web', NULL, NULL),
(58, 'progreso.ver', 'web', NULL, NULL),
(59, 'certificados.generar', 'web', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_estudiantes`
--

CREATE TABLE `progreso_estudiantes` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `progreso_porcentaje` decimal(5,2) DEFAULT 0.00,
  `tiempo_estudiado` int(11) DEFAULT 0,
  `ultima_actividad` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completado` tinyint(1) DEFAULT 0,
  `fecha_inscripcion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `progreso_estudiantes`
--

INSERT INTO `progreso_estudiantes` (`id`, `estudiante_id`, `curso_id`, `progreso_porcentaje`, `tiempo_estudiado`, `ultima_actividad`, `completado`, `fecha_inscripcion`) VALUES
(1, 4, 1, 75.50, 480, '2025-08-18 15:16:31', 0, '2025-08-18 15:16:31'),
(2, 4, 2, 100.00, 1200, '2025-08-18 15:16:31', 1, '2025-08-18 15:16:31'),
(3, 5, 1, 45.30, 300, '2025-08-18 15:16:31', 0, '2025-08-18 15:16:31'),
(4, 5, 4, 68.90, 750, '2025-08-18 15:16:31', 0, '2025-08-18 15:16:31'),
(5, 8, 2, 23.80, 180, '2025-08-18 15:16:31', 0, '2025-08-18 15:16:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_modulos`
--

CREATE TABLE `progreso_modulos` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  `completado` tinyint(1) DEFAULT 0,
  `tiempo_estudiado` int(11) DEFAULT 0,
  `progreso_porcentaje` decimal(5,2) DEFAULT 0.00,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_completado` timestamp NULL DEFAULT NULL,
  `ultima_actividad` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(1, 'Administrador', 'Acceso completo al sistema', 1, '2025-08-18 15:16:30'),
(2, 'Docente', 'Puede crear y gestionar cursos', 1, '2025-08-18 15:16:30'),
(3, 'Estudiante', 'Puede acceder a cursos y materiales', 1, '2025-08-18 15:16:30'),
(7, 'Invitado', 'Acceso temporal de 3 días a todo el material', 1, '2025-08-22 12:16:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 7),
(2, 1),
(2, 2),
(2, 3),
(2, 7),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(6, 2),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(14, 3),
(15, 1),
(15, 2),
(15, 3),
(15, 7),
(16, 1),
(16, 2),
(17, 1),
(17, 2),
(18, 1),
(19, 1),
(19, 2),
(19, 3),
(19, 7),
(20, 1),
(20, 2),
(21, 1),
(22, 1),
(23, 1),
(23, 2),
(23, 3),
(23, 7),
(24, 1),
(24, 2),
(24, 3),
(24, 7),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(28, 2),
(28, 3),
(28, 7),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(32, 2),
(32, 3),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(37, 3),
(37, 7),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(46, 2),
(47, 1),
(47, 2),
(48, 1),
(49, 1),
(49, 2),
(50, 1),
(50, 2),
(50, 3),
(51, 1),
(51, 2),
(51, 3),
(52, 1),
(52, 2),
(52, 3),
(53, 1),
(53, 2),
(53, 3),
(54, 1),
(54, 2),
(54, 3),
(55, 1),
(55, 2),
(55, 3),
(56, 1),
(56, 2),
(57, 1),
(57, 2),
(58, 1),
(58, 2),
(58, 3),
(59, 1),
(59, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_activas`
--

CREATE TABLE `sesiones_activas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `dispositivo` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `navegador` varchar(100) DEFAULT NULL,
  `sistema_operativo` varchar(100) DEFAULT NULL,
  `fecha_inicio` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actividad` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `telefono`, `fecha_nacimiento`, `avatar`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Admin', 'Tech Home', 'luisrochavela1@gmail.com', '$2y$10$rOqR/us0TLqgtfz6yZGCVua37JMzB7HO5S6tMWwZRuyn8oBIW/y46', '', '1998-05-21', NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:54:44'),
(2, 'María', 'Gómez', 'maria.gomez@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07'),
(3, 'Carlos', 'Fernández', 'carlos.fernandez@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07'),
(4, 'Ana', 'Rodríguez', 'ana.rodriguez@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07'),
(5, 'Luis', 'Pérez', 'luis.perez@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07'),
(6, 'Demo', 'Invitado', 'demo@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07'),
(7, 'Pedro', 'Morales', 'pedro.morales@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07'),
(8, 'Laura', 'Santos', 'laura.santos@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07'),
(9, 'JHOEL', 'ZURITA', 'jhoel0521@gmail.com', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', '1231312', '1998-05-21', NULL, 1, '2025-08-18 21:51:10', '2025-08-19 23:42:07'),
(10, 'test', 'UPDS', 'test123@gmail.com', '$2y$10$3VQQ7OoyKAqZ0zDxuF/CNO1Q1w7bqLPFGueVCbGVPbMDbzPGoESm6', '12312312', '2002-06-13', NULL, 1, '2025-08-20 12:09:11', '2025-08-20 12:09:11'),
(11, 'test2', 'UPDS', 'test2080@gmail.com', '$2y$10$GnPAx8X6yW0LPDrq0Eb8p.CaagbOU/4xN9EeBJ8f3737HxPOKtqUK', '12312312', '2002-06-13', NULL, 1, '2025-08-20 12:30:29', '2025-08-20 12:30:29'),
(12, 'Carlos', 'Rocha', 'carlosrocha123@gmail.com', '$2y$10$yqdtUnCIFVhj8mTGiAyG4OLqwO/Uoajn6Shlst8e5IrlOHd0yMcry', '', '2003-07-18', NULL, 1, '2025-08-20 12:33:03', '2025-08-20 12:33:03'),
(14, 'Douglas', 'Flor', 'douglasdfh88@gmail.com', '$2y$10$JCIKzYXL8Qp0vLJu4eeJEe1vUCB0XRieVyJA6QyWmn8HbdA5swIw6', '21321421', '1994-07-15', NULL, 1, '2025-08-20 13:28:06', '2025-08-20 13:28:06'),
(15, 'Felipe', 'Nazel', 'naxelf666@gmail.com', '$2y$10$ifVgYZbviCw8VjaT3MQZ8.4M1TIoRbJGp2MH8A7zFAYQeSZaTng9S', '4325342', '2002-06-14', NULL, 1, '2025-08-20 13:29:33', '2025-08-20 13:29:33'),
(18, 'Luis', 'Rocha', 'luisrochavela990@gmail.com', '$2y$10$NByMTYDxfYa50zKkFlJgZOxW6fiuFNc3AVtgUgor22S3qaD6IPWKO', '+59168832824', '2002-03-09', NULL, 1, '2025-08-22 13:20:28', '2025-08-22 13:20:28'),
(20, 'Leonardo', 'Peña Añez', 'leonardopenaanez@gmail.com', '$2y$10$k54gKH7i.qX8Q10pbOP9j..aBw5InzaiQEnZYMPCd6itcN/w13/VC', '75678428', '2005-08-06', NULL, 1, '2025-08-25 13:17:14', '2025-08-25 13:17:14'),
(21, 'Gustavo', 'Tantani Mamani', 'tantani.m.g@gmail.com', '$2y$10$fZFHauZ2i/vZG0Q3e2mf.ecp839PSbuqvoB3sudBctoq4a/uiTmbG', '70017480', '2000-10-01', NULL, 1, '2025-08-25 13:31:45', '2025-08-25 13:31:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
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
  `fecha_venta` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `numero_venta`, `cliente_id`, `vendedor_id`, `subtotal`, `descuento`, `impuestos`, `total`, `tipo_pago`, `estado`, `notas`, `fecha_venta`, `fecha_actualizacion`) VALUES
(1, 'VTA-2025-001', 4, 7, 180.00, 0.00, 23.40, 203.40, 'Efectivo', 'Completada', NULL, '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(2, 'VTA-2025-002', 5, 7, 85.00, 8.50, 9.95, 86.45, 'Transferencia', 'Completada', NULL, '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(3, 'VTA-2025-003', 8, 7, 350.00, 35.00, 40.95, 355.95, 'Tarjeta', 'Completada', NULL, '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(4, 'VTA-2025-004', 4, 7, 270.00, 0.00, 35.10, 305.10, 'QR', 'Completada', NULL, '2025-08-18 15:16:31', '2025-08-18 15:16:31');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_cursos_estadisticas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_cursos_estadisticas` (
`id` int(11)
,`titulo` varchar(200)
,`slug` varchar(250)
,`descripcion` text
,`contenido` longtext
,`docente_id` int(11)
,`categoria_id` int(11)
,`imagen_portada` varchar(255)
,`precio` decimal(10,2)
,`duracion_horas` int(11)
,`max_estudiantes` int(11)
,`nivel` enum('Principiante','Intermedio','Avanzado')
,`modalidad` enum('Presencial','Virtual','Híbrido')
,`certificado` tinyint(1)
,`fecha_inicio` date
,`fecha_fin` date
,`estudiantes_inscritos` int(11)
,`calificacion_promedio` decimal(3,2)
,`total_calificaciones` int(11)
,`requisitos` text
,`objetivos` text
,`estado` enum('Borrador','Publicado','Archivado')
,`fecha_creacion` timestamp
,`fecha_actualizacion` timestamp
,`docente_nombre` varchar(100)
,`docente_apellido` varchar(100)
,`categoria_nombre` varchar(100)
,`categoria_color` varchar(7)
,`estudiantes_activos` bigint(21)
,`estudiantes_con_progreso` bigint(21)
,`progreso_promedio` decimal(9,6)
,`estudiantes_completaron` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_estudiantes_progreso`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_estudiantes_progreso` (
`id` int(11)
,`nombre` varchar(100)
,`apellido` varchar(100)
,`email` varchar(150)
,`cursos_inscritos` bigint(21)
,`cursos_con_progreso` bigint(21)
,`cursos_completados` bigint(21)
,`progreso_promedio` decimal(9,6)
,`tiempo_total_estudiado` decimal(32,0)
,`libros_descargados` bigint(21)
,`libros_favoritos` bigint(21)
,`cursos_favoritos` bigint(21)
,`nivel_estudiante` varchar(20)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_libros_estadisticas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_libros_estadisticas` (
`id` int(11)
,`titulo` varchar(200)
,`slug` varchar(250)
,`autor` varchar(150)
,`descripcion` text
,`categoria_id` int(11)
,`isbn` varchar(20)
,`paginas` int(11)
,`editorial` varchar(100)
,`año_publicacion` year(4)
,`idioma` varchar(50)
,`formato` enum('PDF','EPUB','MOBI','Físico','Digital')
,`descargas_totales` int(11)
,`calificacion_promedio` decimal(3,2)
,`total_calificaciones` int(11)
,`palabras_clave` text
,`imagen_portada` varchar(255)
,`archivo_pdf` varchar(500)
,`enlace_externo` varchar(500)
,`tamaño_archivo` int(11)
,`stock` int(11)
,`stock_minimo` int(11)
,`precio` decimal(10,2)
,`es_gratuito` tinyint(1)
,`estado` tinyint(1)
,`fecha_creacion` timestamp
,`fecha_actualizacion` timestamp
,`categoria_nombre` varchar(100)
,`categoria_color` varchar(7)
,`usuarios_descargaron` bigint(21)
,`total_descargas_real` bigint(21)
,`dias_con_descargas` bigint(21)
,`ultima_descarga` timestamp
,`usuarios_favorito` bigint(21)
,`estado_disponibilidad` varchar(10)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_cursos_estadisticas`
--
DROP TABLE IF EXISTS `vista_cursos_estadisticas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_cursos_estadisticas`  AS SELECT `c`.`id` AS `id`, `c`.`titulo` AS `titulo`, `c`.`slug` AS `slug`, `c`.`descripcion` AS `descripcion`, `c`.`contenido` AS `contenido`, `c`.`docente_id` AS `docente_id`, `c`.`categoria_id` AS `categoria_id`, `c`.`imagen_portada` AS `imagen_portada`, `c`.`precio` AS `precio`, `c`.`duracion_horas` AS `duracion_horas`, `c`.`max_estudiantes` AS `max_estudiantes`, `c`.`nivel` AS `nivel`, `c`.`modalidad` AS `modalidad`, `c`.`certificado` AS `certificado`, `c`.`fecha_inicio` AS `fecha_inicio`, `c`.`fecha_fin` AS `fecha_fin`, `c`.`estudiantes_inscritos` AS `estudiantes_inscritos`, `c`.`calificacion_promedio` AS `calificacion_promedio`, `c`.`total_calificaciones` AS `total_calificaciones`, `c`.`requisitos` AS `requisitos`, `c`.`objetivos` AS `objetivos`, `c`.`estado` AS `estado`, `c`.`fecha_creacion` AS `fecha_creacion`, `c`.`fecha_actualizacion` AS `fecha_actualizacion`, `u`.`nombre` AS `docente_nombre`, `u`.`apellido` AS `docente_apellido`, `cat`.`nombre` AS `categoria_nombre`, `cat`.`color` AS `categoria_color`, count(distinct `ic`.`estudiante_id`) AS `estudiantes_activos`, count(distinct `pe`.`estudiante_id`) AS `estudiantes_con_progreso`, coalesce(avg(`pe`.`progreso_porcentaje`),0) AS `progreso_promedio`, count(distinct case when `pe`.`completado` = 1 then `pe`.`estudiante_id` end) AS `estudiantes_completaron` FROM ((((`cursos` `c` left join `usuarios` `u` on(`c`.`docente_id` = `u`.`id`)) left join `categorias` `cat` on(`c`.`categoria_id` = `cat`.`id`)) left join `inscripciones_cursos` `ic` on(`c`.`id` = `ic`.`curso_id` and `ic`.`estado` = 'Activa')) left join `progreso_estudiantes` `pe` on(`c`.`id` = `pe`.`curso_id`)) GROUP BY `c`.`id` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_estudiantes_progreso`
--
DROP TABLE IF EXISTS `vista_estudiantes_progreso`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estudiantes_progreso`  AS SELECT `u`.`id` AS `id`, `u`.`nombre` AS `nombre`, `u`.`apellido` AS `apellido`, `u`.`email` AS `email`, count(distinct `ic`.`curso_id`) AS `cursos_inscritos`, count(distinct `pe`.`curso_id`) AS `cursos_con_progreso`, count(distinct case when `pe`.`completado` = 1 then `pe`.`curso_id` end) AS `cursos_completados`, coalesce(avg(`pe`.`progreso_porcentaje`),0) AS `progreso_promedio`, sum(`pe`.`tiempo_estudiado`) AS `tiempo_total_estudiado`, count(distinct `dl`.`libro_id`) AS `libros_descargados`, count(distinct `fl`.`libro_id`) AS `libros_favoritos`, count(distinct `fc`.`curso_id`) AS `cursos_favoritos`, `ObtenerNivelEstudiante`(`u`.`id`) AS `nivel_estudiante` FROM (((((`usuarios` `u` left join `inscripciones_cursos` `ic` on(`u`.`id` = `ic`.`estudiante_id`)) left join `progreso_estudiantes` `pe` on(`u`.`id` = `pe`.`estudiante_id`)) left join `descargas_libros` `dl` on(`u`.`id` = `dl`.`usuario_id`)) left join `favoritos_libros` `fl` on(`u`.`id` = `fl`.`usuario_id`)) left join `favoritos_cursos` `fc` on(`u`.`id` = `fc`.`usuario_id`)) WHERE `u`.`id` in (select `model_has_roles`.`model_id` from `model_has_roles` where `model_has_roles`.`role_id` = 3) GROUP BY `u`.`id` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_libros_estadisticas`
--
DROP TABLE IF EXISTS `vista_libros_estadisticas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_libros_estadisticas`  AS SELECT `l`.`id` AS `id`, `l`.`titulo` AS `titulo`, `l`.`slug` AS `slug`, `l`.`autor` AS `autor`, `l`.`descripcion` AS `descripcion`, `l`.`categoria_id` AS `categoria_id`, `l`.`isbn` AS `isbn`, `l`.`paginas` AS `paginas`, `l`.`editorial` AS `editorial`, `l`.`año_publicacion` AS `año_publicacion`, `l`.`idioma` AS `idioma`, `l`.`formato` AS `formato`, `l`.`descargas_totales` AS `descargas_totales`, `l`.`calificacion_promedio` AS `calificacion_promedio`, `l`.`total_calificaciones` AS `total_calificaciones`, `l`.`palabras_clave` AS `palabras_clave`, `l`.`imagen_portada` AS `imagen_portada`, `l`.`archivo_pdf` AS `archivo_pdf`, `l`.`enlace_externo` AS `enlace_externo`, `l`.`tamaño_archivo` AS `tamaño_archivo`, `l`.`stock` AS `stock`, `l`.`stock_minimo` AS `stock_minimo`, `l`.`precio` AS `precio`, `l`.`es_gratuito` AS `es_gratuito`, `l`.`estado` AS `estado`, `l`.`fecha_creacion` AS `fecha_creacion`, `l`.`fecha_actualizacion` AS `fecha_actualizacion`, `cat`.`nombre` AS `categoria_nombre`, `cat`.`color` AS `categoria_color`, count(distinct `dl`.`usuario_id`) AS `usuarios_descargaron`, count(`dl`.`id`) AS `total_descargas_real`, count(distinct cast(`dl`.`fecha_descarga` as date)) AS `dias_con_descargas`, coalesce(max(`dl`.`fecha_descarga`),`l`.`fecha_creacion`) AS `ultima_descarga`, count(distinct `fl`.`usuario_id`) AS `usuarios_favorito`, CASE WHEN `l`.`es_gratuito` = 1 THEN 'Ilimitado' WHEN `l`.`stock` <= 0 THEN 'Agotado' WHEN `l`.`stock` <= `l`.`stock_minimo` THEN 'Stock Bajo' ELSE 'Disponible' END AS `estado_disponibilidad` FROM (((`libros` `l` left join `categorias` `cat` on(`l`.`categoria_id` = `cat`.`id`)) left join `descargas_libros` `dl` on(`l`.`id` = `dl`.`libro_id`)) left join `favoritos_libros` `fl` on(`l`.`id` = `fl`.`libro_id`)) GROUP BY `l`.`id` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acceso_invitados`
--
ALTER TABLE `acceso_invitados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_usuario_activo` (`usuario_id`,`acceso_bloqueado`),
  ADD KEY `idx_fecha_vencimiento` (`fecha_vencimiento`),
  ADD KEY `idx_dias_restantes` (`dias_restantes`);

--
-- Indices de la tabla `activation_tokens`
--
ALTER TABLE `activation_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_usado` (`usado`);

--
-- Indices de la tabla `calificaciones_cursos`
--
ALTER TABLE `calificaciones_cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_curso` (`usuario_id`,`curso_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_curso` (`curso_id`),
  ADD KEY `idx_calificacion` (`calificacion`),
  ADD KEY `idx_fecha` (`fecha_calificacion`);

--
-- Indices de la tabla `calificaciones_libros`
--
ALTER TABLE `calificaciones_libros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_libro` (`usuario_id`,`libro_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_libro` (`libro_id`),
  ADD KEY `idx_calificacion` (`calificacion`),
  ADD KEY `idx_fecha` (`fecha_calificacion`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `componentes`
--
ALTER TABLE `componentes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_producto` (`codigo_producto`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_codigo` (`codigo_producto`),
  ADD KEY `idx_stock` (`stock`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_docente` (`docente_id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_nivel` (`nivel`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_modalidad` (`modalidad`),
  ADD KEY `idx_fecha_inicio` (`fecha_inicio`),
  ADD KEY `idx_titulo_estado` (`titulo`,`estado`),
  ADD KEY `idx_precio_nivel` (`precio`,`nivel`),
  ADD KEY `idx_fecha_creacion_estado` (`fecha_creacion`,`estado`);

--
-- Indices de la tabla `descargas_libros`
--
ALTER TABLE `descargas_libros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_libro` (`libro_id`),
  ADD KEY `idx_fecha` (`fecha_descarga`),
  ADD KEY `idx_libro_fecha` (`libro_id`,`fecha_descarga`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha_descarga`),
  ADD KEY `idx_fecha_libro_usuario` (`fecha_descarga`,`libro_id`,`usuario_id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venta` (`venta_id`),
  ADD KEY `idx_tipo_producto` (`tipo_producto`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- Indices de la tabla `favoritos_cursos`
--
ALTER TABLE `favoritos_cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_curso_fav` (`usuario_id`,`curso_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_curso` (`curso_id`),
  ADD KEY `idx_fecha` (`fecha_agregado`);

--
-- Indices de la tabla `favoritos_libros`
--
ALTER TABLE `favoritos_libros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_libro_fav` (`usuario_id`,`libro_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_libro` (`libro_id`),
  ADD KEY `idx_fecha` (`fecha_agregado`);

--
-- Indices de la tabla `inscripciones_cursos`
--
ALTER TABLE `inscripciones_cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_estudiante_curso` (`estudiante_id`,`curso_id`),
  ADD KEY `idx_estudiante` (`estudiante_id`),
  ADD KEY `idx_curso` (`curso_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_inscripcion` (`fecha_inscripcion`),
  ADD KEY `idx_estudiante_estado_fecha` (`estudiante_id`,`estado`,`fecha_inscripcion`);

--
-- Indices de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_ip` (`email`,`ip_address`),
  ADD KEY `idx_fecha_intento` (`fecha_intento`),
  ADD KEY `idx_exito` (`exito`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_autor` (`autor`),
  ADD KEY `idx_isbn` (`isbn`),
  ADD KEY `idx_stock` (`stock`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_idioma` (`idioma`),
  ADD KEY `idx_formato` (`formato`),
  ADD KEY `idx_descargas` (`descargas_totales`),
  ADD KEY `idx_calificacion` (`calificacion_promedio`),
  ADD KEY `idx_titulo_estado` (`titulo`,`estado`),
  ADD KEY `idx_autor_estado` (`autor`,`estado`),
  ADD KEY `idx_precio_gratuito` (`precio`,`es_gratuito`),
  ADD KEY `idx_fecha_creacion_estado` (`fecha_creacion`,`estado`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `idx_model_id_and_model_type` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `idx_model_id_and_model_type` (`model_id`,`model_type`);

--
-- Indices de la tabla `modulos_curso`
--
ALTER TABLE `modulos_curso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_curso_orden` (`curso_id`,`orden`),
  ADD KEY `idx_curso` (`curso_id`),
  ADD KEY `idx_orden` (`orden`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_used` (`used`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `progreso_estudiantes`
--
ALTER TABLE `progreso_estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_estudiante_curso` (`estudiante_id`,`curso_id`),
  ADD KEY `idx_estudiante` (`estudiante_id`),
  ADD KEY `idx_curso` (`curso_id`),
  ADD KEY `idx_completado` (`completado`),
  ADD KEY `idx_curso_progreso` (`curso_id`,`progreso_porcentaje`),
  ADD KEY `idx_estudiante_curso_progreso` (`estudiante_id`,`curso_id`,`progreso_porcentaje`),
  ADD KEY `idx_curso_completado_progreso` (`curso_id`,`completado`,`progreso_porcentaje`);

--
-- Indices de la tabla `progreso_modulos`
--
ALTER TABLE `progreso_modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_estudiante_modulo` (`estudiante_id`,`modulo_id`),
  ADD KEY `idx_estudiante` (`estudiante_id`),
  ADD KEY `idx_modulo` (`modulo_id`),
  ADD KEY `idx_completado` (`completado`),
  ADD KEY `idx_ultima_actividad` (`ultima_actividad`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indices de la tabla `sesiones_activas`
--
ALTER TABLE `sesiones_activas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `idx_usuario_activa` (`usuario_id`,`activa`),
  ADD KEY `idx_session_id` (`session_id`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_fecha_actividad` (`fecha_actividad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_venta` (`numero_venta`),
  ADD KEY `vendedor_id` (`vendedor_id`),
  ADD KEY `idx_numero_venta` (`numero_venta`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha` (`fecha_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acceso_invitados`
--
ALTER TABLE `acceso_invitados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `activation_tokens`
--
ALTER TABLE `activation_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `calificaciones_cursos`
--
ALTER TABLE `calificaciones_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `calificaciones_libros`
--
ALTER TABLE `calificaciones_libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `componentes`
--
ALTER TABLE `componentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `descargas_libros`
--
ALTER TABLE `descargas_libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `favoritos_cursos`
--
ALTER TABLE `favoritos_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `favoritos_libros`
--
ALTER TABLE `favoritos_libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones_cursos`
--
ALTER TABLE `inscripciones_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `modulos_curso`
--
ALTER TABLE `modulos_curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `progreso_estudiantes`
--
ALTER TABLE `progreso_estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `progreso_modulos`
--
ALTER TABLE `progreso_modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sesiones_activas`
--
ALTER TABLE `sesiones_activas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acceso_invitados`
--
ALTER TABLE `acceso_invitados`
  ADD CONSTRAINT `acceso_invitados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calificaciones_cursos`
--
ALTER TABLE `calificaciones_cursos`
  ADD CONSTRAINT `fk_calificaciones_cursos_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_calificaciones_cursos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `calificaciones_libros`
--
ALTER TABLE `calificaciones_libros`
  ADD CONSTRAINT `fk_calificaciones_libros_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_calificaciones_libros_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `componentes`
--
ALTER TABLE `componentes`
  ADD CONSTRAINT `componentes_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `cursos_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `descargas_libros`
--
ALTER TABLE `descargas_libros`
  ADD CONSTRAINT `descargas_libros_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `descargas_libros_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`);

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `favoritos_cursos`
--
ALTER TABLE `favoritos_cursos`
  ADD CONSTRAINT `fk_favoritos_cursos_curso` FOREIGN KEY (`curso_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favoritos_cursos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `favoritos_libros`
--
ALTER TABLE `favoritos_libros`
  ADD CONSTRAINT `fk_favoritos_libros_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favoritos_libros_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `inscripciones_cursos`
--
ALTER TABLE `inscripciones_cursos`
  ADD CONSTRAINT `fk_inscripciones_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscripciones_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `modulos_curso`
--
ALTER TABLE `modulos_curso`
  ADD CONSTRAINT `fk_modulos_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `progreso_estudiantes`
--
ALTER TABLE `progreso_estudiantes`
  ADD CONSTRAINT `progreso_estudiantes_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `progreso_estudiantes_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);

--
-- Filtros para la tabla `progreso_modulos`
--
ALTER TABLE `progreso_modulos`
  ADD CONSTRAINT `fk_progreso_modulos_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_progreso_modulos_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos_curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones_activas`
--
ALTER TABLE `sesiones_activas`
  ADD CONSTRAINT `sesiones_activas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
