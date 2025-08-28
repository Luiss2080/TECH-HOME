-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2025 a las 03:28:16
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
(19, 'porcentaje_ganancia', '30', 'Porcentaje de ganancia para docentes', 'numero', '2025-08-18 15:16:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text NOT NULL,
  `contenido` longtext DEFAULT NULL,
  `docente_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT 0.00,
  `duracion_horas` int(11) DEFAULT 0,
  `nivel` enum('Principiante','Intermedio','Avanzado') DEFAULT 'Principiante',
  `requisitos` text DEFAULT NULL,
  `objetivos` text DEFAULT NULL,
  `estado` enum('Borrador','Publicado','Archivado') DEFAULT 'Borrador',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `titulo`, `descripcion`, `contenido`, `docente_id`, `categoria_id`, `imagen_portada`, `precio`, `duracion_horas`, `nivel`, `requisitos`, `objetivos`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Introducción a la Robótica', 'Curso básico de robótica con Arduino', NULL, 2, 1, 'robotica.jpg', 299.00, 20, 'Principiante', NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(2, 'Programación en Python', 'Aprende Python desde cero', NULL, 3, 2, 'python.jpg', 399.00, 40, 'Principiante', NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(3, 'Machine Learning Avanzado', 'Técnicas avanzadas de ML con Python', NULL, 2, 4, 'ml.jpg', 599.00, 60, 'Avanzado', NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(4, 'Electrónica Digital', 'Fundamentos de electrónica digital', NULL, 3, 3, 'electronica.jpg', 349.00, 30, 'Intermedio', NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(5, 'Análisis de Datos con Pandas', 'Manejo profesional de datos en Python', NULL, 2, 5, 'pandas.jpg', 449.00, 35, 'Intermedio', NULL, NULL, 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31');

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
  `autor` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `paginas` int(11) DEFAULT 0,
  `editorial` varchar(100) DEFAULT NULL,
  `año_publicacion` year(4) DEFAULT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `archivo_pdf` varchar(500) DEFAULT NULL,
  `enlace_externo` varchar(500) DEFAULT NULL,
  `tamaño_archivo` int(11) DEFAULT 0,
  `stock` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 5,
  `precio` decimal(10,2) DEFAULT 0.00,
  `es_gratuito` tinyint(1) DEFAULT 1,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `autor`, `descripcion`, `categoria_id`, `isbn`, `paginas`, `editorial`, `año_publicacion`, `imagen_portada`, `archivo_pdf`, `enlace_externo`, `tamaño_archivo`, `stock`, `stock_minimo`, `precio`, `es_gratuito`, `estado`, `fecha_creacion`) VALUES
(1, 'Robótica Práctica con Arduino', 'Juan Martínez', 'Guía completa para proyectos de robótica con Arduino', 6, '978-1234567890', 320, 'Editorial Tech', '2022', 'robotica_arduino.jpg', '/libros/robotica_arduino.pdf', NULL, 0, 25, 5, 150.00, 0, 1, '2025-08-18 15:16:31'),
(2, 'Python para Principiantes', 'Ana López', 'Introducción al lenguaje Python desde cero', 7, '978-0987654321', 280, 'Code Press', '2021', 'python_principiantes.jpg', '/libros/python_principiantes.pdf', NULL, 0, 30, 5, 120.00, 0, 1, '2025-08-18 15:16:31'),
(3, 'Fundamentos de Electrónica', 'Carlos Sánchez', 'Teoría y práctica de circuitos electrónicos', 8, '978-5432109876', 450, 'Electro Books', '2020', 'fundamentos_electronica.jpg', '/libros/fundamentos_electronica.pdf', NULL, 0, 15, 3, 200.00, 0, 1, '2025-08-18 15:16:31'),
(4, 'Machine Learning Avanzado', 'María García', 'Técnicas avanzadas de aprendizaje automático', 9, '978-6789054321', 380, 'AI Publications', '2023', 'ml_avanzado.jpg', '/libros/ml_avanzado.pdf', NULL, 0, 20, 5, 250.00, 0, 1, '2025-08-18 15:16:31'),
(5, 'Matemáticas para Ingenieros', 'Pedro Fernández', 'Fundamentos matemáticos para ingeniería', 10, '978-1234509876', 310, 'Math Ed', '2022', 'matematicas.jpg', '/libros/matematicas.pdf', NULL, 0, 40, 8, 180.00, 0, 1, '2025-08-18 15:16:31');

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
(6, 'App\\Models\\User', 9),
(7, 'App\\Models\\User', 20);

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
(39, 'admin.usuarios.permisos', 'web', '2025-08-19 14:02:01', '2025-08-19 14:02:01');

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
(6, 'Mirones', 'astuto', 1, '2025-08-18 19:12:47'),
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
(39, 1);

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
  ADD KEY `idx_nivel` (`nivel`);

--
-- Indices de la tabla `descargas_libros`
--
ALTER TABLE `descargas_libros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_libro` (`libro_id`),
  ADD KEY `idx_fecha` (`fecha_descarga`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venta` (`venta_id`),
  ADD KEY `idx_tipo_producto` (`tipo_producto`),
  ADD KEY `idx_producto` (`producto_id`);

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
  ADD KEY `idx_estado` (`estado`);

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
  ADD KEY `idx_completado` (`completado`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `progreso_estudiantes`
--
ALTER TABLE `progreso_estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Filtros para la tabla `progreso_estudiantes`
--
ALTER TABLE `progreso_estudiantes`
  ADD CONSTRAINT `progreso_estudiantes_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `progreso_estudiantes_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);

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
