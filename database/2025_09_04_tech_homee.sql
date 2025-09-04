-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-09-2025 a las 16:55:40
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
-- Estructura de tabla para la tabla `acceso_materiales`
--

CREATE TABLE `acceso_materiales` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `tipo_acceso` enum('visualizado','descargado') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `fecha_acceso` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(5, 'tantani.m.g@gmail.com', '5f07a806d850d8d560d255b53e91b58861656b7711dc71c97c926d1708da9d44', 1, '2025-08-25 13:31:45'),
(6, 'lr0900573@gmail.com', 'fa07e0078884d6141ea1e89f0cf6e05551b3426ce71caf3df9a3f76bd26db9ed', 1, '2025-09-01 01:46:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` varchar(50) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estructura de tabla para la tabla `codigos_otp`
--

CREATE TABLE `codigos_otp` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `expira_en` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_reservado` int(11) DEFAULT 0,
  `alerta_stock_bajo` tinyint(1) DEFAULT 1,
  `permite_venta_sin_stock` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `componentes`
--

INSERT INTO `componentes` (`id`, `nombre`, `descripcion`, `categoria_id`, `codigo_producto`, `marca`, `modelo`, `especificaciones`, `imagen_principal`, `imagenes_adicionales`, `precio`, `stock`, `stock_minimo`, `proveedor`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `stock_reservado`, `alerta_stock_bajo`, `permite_venta_sin_stock`) VALUES
(1, 'Arduino UNO R3', 'Placa de desarrollo con microcontrolador ATmega328P', 11, 'ARD-UNO-R3', 'Arduino', 'UNO R3', NULL, NULL, NULL, 45.00, 50, 10, 'Arduino Store', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(2, 'Raspberry Pi 4 Model B', 'Computadora de placa única de 4GB RAM', 11, 'RPI-4B-4GB', 'Raspberry Pi', '4 Model B', NULL, NULL, NULL, 120.00, 25, 5, 'Raspberry Foundation', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(3, 'ESP32 DevKit V1', 'Módulo WiFi y Bluetooth con microcontrolador dual-core', 11, 'ESP32-DEVKIT', 'Espressif', 'DevKit V1', NULL, NULL, NULL, 25.00, 75, 15, 'Espressif Systems', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(4, 'Sensor Ultrasónico HC-SR04', 'Sensor de distancia por ultrasonido', 12, 'HC-SR04', 'Generic', 'HC-SR04', NULL, NULL, NULL, 8.00, 100, 20, 'Electronics Pro', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(5, 'Sensor de Temperatura DHT22', 'Sensor digital de temperatura y humedad', 12, 'DHT22', 'Aosong', 'DHT22', NULL, NULL, NULL, 12.00, 80, 15, 'Sensor Tech', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(6, 'Sensor PIR de Movimiento', 'Detector de movimiento infrarrojo pasivo', 12, 'PIR-HC-SR501', 'Generic', 'HC-SR501', NULL, NULL, NULL, 6.00, 60, 10, 'Electronics Pro', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(7, 'Servo Motor SG90', 'Micro servo de 9g para proyectos de robótica', 13, 'SERVO-SG90', 'TowerPro', 'SG90', NULL, NULL, NULL, 15.00, 40, 8, 'TowerPro', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(8, 'Motor Paso a Paso 28BYJ-48', 'Motor paso a paso unipolar con driver ULN2003', 13, 'STEPPER-28BYJ', 'Generic', '28BYJ-48', NULL, NULL, NULL, 18.00, 30, 5, 'Motor Solutions', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(9, 'Motor DC 12V', 'Motor de corriente continua de 12V y 300 RPM', 13, 'MOTOR-DC-12V', 'Generic', 'DC-300RPM', NULL, NULL, NULL, 22.00, 25, 5, 'Motor Solutions', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(10, 'Kit de LEDs 5mm (100 piezas)', 'Surtido de LEDs de colores de 5mm', 14, 'LED-KIT-100', 'Generic', 'LED-5MM', NULL, NULL, NULL, 20.00, 50, 10, 'LED World', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(11, 'Resistencias 1/4W (500 piezas)', 'Kit de resistencias de diferentes valores', 14, 'RES-KIT-500', 'Generic', '1/4W', NULL, NULL, NULL, 25.00, 40, 8, 'Electronics Pro', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(12, 'Jumper Wires (120 piezas)', 'Cables de conexión macho-macho, hembra-hembra', 14, 'JUMPER-120', 'Generic', 'Dupont', NULL, NULL, NULL, 15.00, 60, 12, 'Wire Tech', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(13, 'Multímetro Digital DT830B', 'Multímetro básico para mediciones eléctricas', 15, 'MULTI-DT830B', 'Generic', 'DT830B', NULL, NULL, NULL, 35.00, 20, 3, 'Tool Master', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(14, 'Soldador de Estaño 40W', 'Soldador eléctrico con control de temperatura', 15, 'SOLD-40W', 'Weller', 'SP40N', NULL, NULL, NULL, 85.00, 15, 3, 'Weller Tools', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(15, 'Protoboard 830 puntos', 'Placa de pruebas sin soldadura', 15, 'PROTO-830', 'Generic', '830-tie', NULL, NULL, NULL, 12.00, 45, 8, 'Proto Tech', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(16, 'Kit Básico Arduino para Principiantes', 'Kit completo con Arduino UNO y componentes básicos', 16, 'KIT-ARD-BASIC', 'Tech Home', 'BASIC-V1', NULL, NULL, NULL, 180.00, 20, 3, 'Tech Home Store', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(17, 'Kit Avanzado de Robótica', 'Kit completo para construcción de robots', 16, 'KIT-ROBOT-ADV', 'Tech Home', 'ROBOT-V2', NULL, NULL, NULL, 350.00, 10, 2, 'Tech Home Store', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(18, 'Kit de Sensores IoT', 'Colección de sensores para proyectos IoT', 16, 'KIT-IOT-SENS', 'Tech Home', 'IOT-V1', NULL, NULL, NULL, 220.00, 15, 3, 'Tech Home Store', 'Disponible', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 0, 1, 0),
(19, 'Arduino Uno R3', 'Placa microcontrolador ATmega328P compatible.', 11, 'CMP-1001', 'Arduino', 'UNO R3', '{\"mcu\":\"ATmega328P\",\"voltaje\":\"5V\",\"io_pines\":14,\"pwm\":6,\"memoria_flash\":\"32KB\"}', '/img/componentes/arduino_uno.jpg', '[\"/img/componentes/arduino_uno_1.jpg\",\"/img/componentes/arduino_uno_2.jpg\"]', 89.90, 50, 5, 'TechParts SRL', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(20, 'Raspberry Pi Pico', 'Microcontrolador RP2040 con USB.', 11, 'CMP-1002', 'Raspberry Pi', 'Pico', '{\"mcu\":\"RP2040\",\"velocidad_mhz\":133,\"ram_kb\":264,\"usb\":\"Micro-USB\"}', '/img/componentes/rp_pico.jpg', '[\"/img/componentes/rp_pico_1.jpg\"]', 49.50, 70, 10, 'ElectroStore', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(21, 'ESP32 DevKit v1', 'WiFi+BLE SoC dual-core Xtensa.', 11, 'CMP-1003', 'Espressif', 'DevKit v1', '{\"wifi\":\"802.11 b/g/n\",\"bluetooth\":\"BLE 4.2\",\"gpio\":30}', '/img/componentes/esp32.jpg', '[]', 64.00, 80, 10, 'ElectroStore', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(22, 'Sensor DHT22', 'Sensor digital de temperatura y humedad.', 12, 'CMP-1004', 'Aosong', 'DHT22', '{\"rango_temp\":\"-40–80°C\",\"rango_hum\":\"0–100%\",\"precision\":\"±0.5°C\"}', '/img/componentes/dht22.jpg', '[\"/img/componentes/dht22_1.jpg\"]', 28.90, 120, 15, 'SensTech', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 5, 1, 0),
(23, 'Sensor Ultrasonido HC-SR04', 'Medición de distancia por ultrasonido.', 12, 'CMP-1005', 'Elecfreaks', 'HC-SR04', '{\"distancia_max_cm\":400,\"angulo\":\"15°\",\"tension\":\"5V\"}', '/img/componentes/hcsr04.jpg', '[]', 19.90, 150, 20, 'SensTech', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(24, 'Módulo MPU-6050', 'Acelerómetro+Giroscopio 6 ejes.', 12, 'CMP-1006', 'InvenSense', 'MPU-6050', '{\"interfaz\":\"I2C\",\"rango_g\":\"±2/±4/±8/±16\"}', '/img/componentes/mpu6050.jpg', '[\"/img/componentes/mpu6050_1.jpg\"]', 34.50, 90, 10, 'SensTech', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 3, 1, 0),
(25, 'Servo SG90 9g', 'Servo micro 180°.', 13, 'CMP-1007', 'TowerPro', 'SG90', '{\"angulo\":\"180°\",\"torque_kgcm\":1.8,\"voltaje\":\"4.8-6V\"}', '/img/componentes/sg90.jpg', '[]', 16.00, 200, 30, 'ActuaParts', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 10, 1, 0),
(26, 'Motor Paso a Paso NEMA17', 'Motor bipolar 1.8°.', 13, 'CMP-1008', 'Wantai', 'NEMA17', '{\"corriente\":\"1.5A\",\"paso\":\"1.8°\",\"tension\":\"12V\"}', '/img/componentes/nema17.jpg', '[\"/img/componentes/nema17_1.jpg\"]', 129.00, 40, 5, 'ActuaParts', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(27, 'Driver L298N', 'Driver puente H dual para motores DC.', 13, 'CMP-1009', 'ST', 'L298N', '{\"corriente_max\":\"2A\",\"voltaje\":\"5-35V\"}', '/img/componentes/l298n.jpg', '[]', 29.00, 110, 15, 'ActuaParts', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 4, 1, 0),
(28, 'Kit Resistencias 1/4W', 'Surtido 1% 600 piezas.', 14, 'CMP-1010', 'UniOhm', 'Surtido 600', '{\"tolerancia\":\"±1%\",\"potencia\":\"0.25W\",\"rangos_ohm\":\"10Ω–1MΩ\"}', '/img/componentes/res_kit.jpg', '[]', 24.50, 85, 10, 'ElectroBits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(29, 'Kit Capacitores Electrolíticos', 'Surtido 100 piezas.', 14, 'CMP-1011', 'Nichicon', 'Assorted', '{\"voltajes\":\"10V–50V\",\"capacitancias\":\"1µF–1000µF\"}', '/img/componentes/cap_kit.jpg', '[\"/img/componentes/cap_kit_1.jpg\"]', 22.00, 70, 10, 'ElectroBits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(30, 'LED 5mm Surtido', 'Paquete 500 LEDs varios colores.', 14, 'CMP-1012', 'LiteOn', '5mm Mix', '{\"colores\":[\"rojo\",\"verde\",\"azul\",\"amarillo\",\"blanco\"]}', '/img/componentes/led_pack.jpg', '[]', 21.00, 150, 20, 'ElectroBits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(31, 'Protoboard 830 puntos', 'Breadboard tamaño estándar.', 15, 'CMP-1013', 'Elegoo', 'MB-102', '{\"puntos\":830,\"lineas_alimentacion\":2}', '/img/componentes/protoboard.jpg', '[]', 18.50, 120, 20, 'ToolTech', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(32, 'Multímetro Digital', 'Básico para electrónica.', 15, 'CMP-1014', 'Mastech', 'DT-830B', '{\"funciones\":[\"V\",\"A\",\"Ω\",\"diodo\",\"continuidad\"]}', '/img/componentes/multimetro.jpg', '[]', 55.00, 60, 10, 'ToolTech', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(33, 'Soldador 60W con estaño', 'Incluye soporte y estaño 1mm.', 15, 'CMP-1015', 'YIHUA', '60W Kit', '{\"potencia\":\"60W\",\"temp_max\":\"450°C\"}', '/img/componentes/soldador.jpg', '[\"/img/componentes/soldador_1.jpg\"]', 69.00, 45, 8, 'ToolTech', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 2, 1, 0),
(34, 'Cables Jumper 120pcs', 'M/M, M/H, H/H surtidos.', 14, 'CMP-1016', 'Elegoo', 'Jump-120', '{\"cantidad\":120,\"tipos\":[\"MM\",\"MH\",\"HH\"]}', '/img/componentes/jumper.jpg', '[]', 14.90, 180, 25, 'ElectroBits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(35, 'Fuente 5V 2A', 'Fuente con conector 5.5x2.1mm.', 14, 'CMP-1017', 'Mean Well', 'GST25A05', '{\"salida\":\"5V 2A\",\"eficiencia\":\"Level VI\"}', '/img/componentes/psu5v.jpg', '[]', 39.00, 75, 10, 'ElectroBits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(36, 'Módulo Relevador 1 Canal', 'Relay 10A con optoacoplador.', 14, 'CMP-1018', 'Songle', 'SRD-05VDC-SL-C', '{\"contacto\":\"10A 250VAC\",\"control\":\"5V\"}', '/img/componentes/relay1c.jpg', '[]', 17.50, 130, 20, 'ElectroBits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 6, 1, 0),
(37, 'Pantalla OLED 0.96\" I2C', 'Resolución 128x64.', 14, 'CMP-1019', 'Waveshare', 'SSD1306', '{\"interfaz\":\"I2C\",\"tamano\":\"0.96\\\"\"}', '/img/componentes/oled096.jpg', '[\"/img/componentes/oled096_1.jpg\"]', 48.00, 65, 10, 'ElectroBits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(38, 'LCD 16x2 I2C', 'Módulo con backpack I2C.', 14, 'CMP-1020', 'Hitachi', 'HD44780+I2C', '{\"caracteres\":\"16x2\",\"interfaz\":\"I2C\"}', '/img/componentes/lcd16x2.jpg', '[]', 29.90, 90, 12, 'ElectroBits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(39, 'Kit Educativo Arduino Starter', 'Incluye sensores y módulos.', 16, 'CMP-1021', 'Elegoo', 'Starter Kit', '{\"incluye\":[\"UNO\",\"sensores\",\"cables\",\"protoboard\"]}', '/img/componentes/kit_arduino.jpg', '[]', 289.00, 35, 5, 'EduKits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(40, 'Kit Educativo Robótica Básica', 'Brazo robótico y guías.', 16, 'CMP-1022', 'MakeBlock', 'mBot Basic', '{\"material\":\"Aluminio\",\"edad_min\":8}', '/img/componentes/kit_robotica.jpg', '[]', 359.00, 20, 3, 'EduKits', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 1, 1, 0),
(41, 'ESP8266 NodeMCU', 'WiFi SoC con Lua/Arduino.', 11, 'CMP-1023', 'Espressif', 'NodeMCU', '{\"wifi\":\"802.11 b/g/n\",\"flash_mb\":4}', '/img/componentes/esp8266.jpg', '[]', 44.00, 95, 10, 'ElectroStore', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(42, 'Sensor Magnetómetro HMC5883L', 'Brújula digital 3 ejes.', 12, 'CMP-1024', 'Honeywell', 'HMC5883L', '{\"interfaz\":\"I2C\",\"rango_gauss\":\"±8\"}', '/img/componentes/hmc5883l.jpg', '[]', 33.00, 85, 10, 'SensTech', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0),
(43, 'Driver DRV8825', 'Driver para motores paso a paso.', 13, 'CMP-1025', 'Texas Instruments', 'DRV8825', '{\"corriente\":\"2.2A\",\"microstepping\":\"1/32\"}', '/img/componentes/drv8825.jpg', '[\"/img/componentes/drv8825_1.jpg\"]', 42.00, 70, 10, 'ActuaParts', 'Disponible', '2025-09-02 04:23:04', '2025-09-02 04:23:04', 0, 1, 0);

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
  `video_url` varchar(500) DEFAULT NULL COMMENT 'URL del video de YouTube',
  `docente_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `nivel` enum('Principiante','Intermedio','Avanzado') DEFAULT 'Principiante',
  `estado` enum('Borrador','Publicado','Archivado') DEFAULT 'Borrador',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `es_gratuito` tinyint(1) DEFAULT 1 COMMENT '1 = Gratuito, 0 = De pago'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Tabla de cursos optimizada para videos de YouTube';

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `titulo`, `descripcion`, `video_url`, `docente_id`, `categoria_id`, `imagen_portada`, `nivel`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `es_gratuito`) VALUES
(1, 'Introducción a la Robótica', 'Curso básico de robótica con Arduino', NULL, 2, 1, 'robotica.jpg', 'Principiante', 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 1),
(2, 'Programación en Python', 'Aprende Python desde cero', NULL, 3, 2, 'python.jpg', 'Principiante', 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 1),
(3, 'Machine Learning Avanzado', 'Técnicas avanzadas de ML con Python', NULL, 2, 4, 'ml.jpg', 'Avanzado', 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 1),
(4, 'Electrónica Digital', 'Fundamentos de electrónica digital', NULL, 3, 3, 'electronica.jpg', 'Intermedio', 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 1),
(5, 'Análisis de Datos con Pandas', 'Manejo profesional de datos en Python', NULL, 2, 5, 'pandas.jpg', 'Intermedio', 'Publicado', '2025-08-18 15:16:31', '2025-08-18 15:16:31', 1),
(82, 'Robótica desde Cero', 'Introducción a sensores, actuadores y control con Arduino', 'https://youtu.be/rob1', 101, 1, '/img/cursos/rob_basico.jpg', 'Principiante', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(83, 'Robots Móviles', 'Line follower, evasión de obstáculos y BLE', 'https://youtu.be/rob2', 102, 1, '/img/cursos/rob_moviles.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(84, 'Brazos Robóticos', 'Cinemática básica y control por PWM', 'https://youtu.be/rob3', 103, 1, '/img/cursos/rob_brazos.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(85, 'Robótica Educativa en el Aula', 'Planificaciones STEAM y proyectos guiados', 'https://youtu.be/rob4', 104, 1, '/img/cursos/rob_aula.jpg', 'Principiante', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(86, 'Visión para Robótica', 'Introducción a visión por computador en robots', 'https://youtu.be/rob5', 105, 1, '/img/cursos/rob_vision.jpg', 'Avanzado', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(87, 'Programación en C desde Cero', 'Sintaxis, funciones, punteros y estructuras', 'https://youtu.be/prog1', 101, 2, '/img/cursos/c_desde_cero.jpg', 'Principiante', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(88, 'POO en Java', 'Clases, herencia, interfaces y colecciones', 'https://youtu.be/prog2', 102, 2, '/img/cursos/java_poo.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(89, 'Python para Ciencia de Datos', 'Numpy, Pandas y visualización', 'https://youtu.be/prog3', 103, 2, '/img/cursos/python_cd.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(90, 'Patrones de Diseño', 'GoF, SOLID y arquitectura limpia', 'https://youtu.be/prog4', 104, 2, '/img/cursos/patrones.jpg', 'Avanzado', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(91, 'APIs con Node.js', 'REST, autenticación y testing', 'https://youtu.be/prog5', 105, 2, '/img/cursos/node_apis.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(92, 'Electrónica Básica', 'Leyes de Ohm/Kirchhoff, mediciones y seguridad', 'https://youtu.be/elec1', 101, 3, '/img/cursos/elec_basica.jpg', 'Principiante', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(93, 'Diseño de PCBs', 'Flujo EDA, ruteo, fabricación y montaje', 'https://youtu.be/elec2', 102, 3, '/img/cursos/pcbs.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(94, 'Sensores en Profundidad', 'DHT22, MPU6050, ultrasonido, magnetómetro', 'https://youtu.be/elec3', 103, 3, '/img/cursos/sensores.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(95, 'Fuentes Conmutadas', 'Topologías, control y protección', 'https://youtu.be/elec4', 104, 3, '/img/cursos/fuentes.jpg', 'Avanzado', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(96, 'Instrumentación y Medición', 'Osciloscopio, multímetro y generador de funciones', 'https://youtu.be/elec5', 105, 3, '/img/cursos/instrumentacion.jpg', 'Principiante', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(97, 'Fundamentos de IA', 'Búsqueda, agentes racionales y probabilidad', 'https://youtu.be/ia1', 101, 4, '/img/cursos/ia_fund.jpg', 'Principiante', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(98, 'ML con Scikit-Learn', 'Regresión, clasificación y validación', 'https://youtu.be/ia2', 102, 4, '/img/cursos/ml_sklearn.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(99, 'Redes Neuronales', 'Perceptrón, backprop y regularización', 'https://youtu.be/ia3', 103, 4, '/img/cursos/redes_nn.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(100, 'Visión por Computador', 'Características, detección y reconocimiento', 'https://youtu.be/ia4', 104, 4, '/img/cursos/vision.jpg', 'Avanzado', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(101, 'NLP con Transformers', 'Embeddings, atención y fine-tuning', 'https://youtu.be/ia5', 105, 4, '/img/cursos/nlp_transformers.jpg', 'Avanzado', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(102, 'Estadística para Data Science', 'Distribuciones, estimación e inferencia', 'https://youtu.be/ds1', 101, 5, '/img/cursos/estadistica.jpg', 'Principiante', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(103, 'Limpieza y Preparación de Datos', 'Pipelines y manejo de valores faltantes', 'https://youtu.be/ds2', 102, 5, '/img/cursos/data_cleaning.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(104, 'Visualización Efectiva', 'Principios de diseño y librerías', 'https://youtu.be/ds3', 103, 5, '/img/cursos/visualizacion.jpg', 'Intermedio', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(105, 'Aprendizaje No Supervisado', 'Clustering y reducción de dimensión', 'https://youtu.be/ds4', 104, 5, '/img/cursos/unsupervised.jpg', 'Avanzado', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(106, 'MLOps Básico', 'Versionado, despliegue y monitoreo de modelos', 'https://youtu.be/ds5', 105, 5, '/img/cursos/mlops.jpg', 'Avanzado', 'Publicado', '2025-09-02 04:34:44', '2025-09-02 04:34:44', 1),
(107, 'Curso Test CRUD 09:53:44', 'Descripción de prueba para el curso CRUD.', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 2, 1, NULL, 'Principiante', 'Borrador', '2025-09-02 13:53:44', '2025-09-02 13:53:44', 1),
(109, 'Curso Test CRUD ACTUALIZADO', 'Descripción de prueba para el curso CRUD.', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 2, 1, NULL, 'Principiante', 'Publicado', '2025-09-02 13:54:03', '2025-09-02 13:54:03', 1),
(110, 'Curso Test CRUD 09:54:03', 'Descripción de prueba para el curso CRUD.', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 2, 1, NULL, 'Principiante', 'Archivado', '2025-09-02 13:54:03', '2025-09-02 13:54:03', 1),
(113, 'holaaaaaaaaaaa', 'sadjkajjjjjjjjjjjjjjjjjjjjjjadsaasddddddddddddddddddd', 'https://youtu.be/tih8r1HMoNc?si=_69B__-1_TJVYxlV', 22, 1, 'curso_tih8r1HMoNc_1756823491633.jpg', 'Intermedio', 'Publicado', '2025-09-02 14:32:04', '2025-09-02 14:32:22', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos_backup`
--

CREATE TABLE `cursos_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
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
-- Volcado de datos para la tabla `cursos_backup`
--

INSERT INTO `cursos_backup` (`id`, `titulo`, `descripcion`, `contenido`, `docente_id`, `categoria_id`, `imagen_portada`, `precio`, `duracion_horas`, `nivel`, `requisitos`, `objetivos`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
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
  `producto_tipo` varchar(50) NOT NULL,
  `tipo_producto` enum('libro','componente') NOT NULL,
  `producto_id` int(11) NOT NULL,
  `nombre_producto` varchar(200) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas_inventario`
--

CREATE TABLE `entradas_inventario` (
  `id` int(11) NOT NULL,
  `numero_entrada` varchar(20) NOT NULL,
  `tipo_producto` enum('componente','libro') NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `costo_total` decimal(10,2) NOT NULL,
  `proveedor` varchar(150) NOT NULL,
  `numero_factura` varchar(50) DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `usuario_registro_id` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_entrada` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `entradas_inventario`
--

INSERT INTO `entradas_inventario` (`id`, `numero_entrada`, `tipo_producto`, `producto_id`, `cantidad`, `precio_unitario`, `costo_total`, `proveedor`, `numero_factura`, `fecha_factura`, `usuario_registro_id`, `observaciones`, `fecha_entrada`) VALUES
(1, 'ENT-2025-001', 'componente', 1, 50, 38.50, 1925.00, 'Arduino Store', 'ARD-2025-0156', '2025-08-15', 1, 'Lote de Arduino UNO R3 originales', '2025-08-15 18:30:00'),
(2, 'ENT-2025-002', 'componente', 3, 100, 21.00, 2100.00, 'Espressif Systems', 'ESP-2025-0089', '2025-08-16', 1, 'ESP32 DevKit V1 - Nueva versión', '2025-08-16 14:15:00'),
(3, 'ENT-2025-003', 'libro', 1, 30, 135.00, 4050.00, 'Editorial Tech', 'ET-2025-0234', '2025-08-17', 2, 'Segunda edición actualizada', '2025-08-17 13:45:00'),
(4, 'ENT-2025-004', 'componente', 10, 75, 18.00, 1350.00, 'LED World', 'LW-2025-0167', '2025-08-18', 1, 'Kit LEDs surtidos alta calidad', '2025-08-18 15:20:00'),
(5, 'ENT-2025-005', 'componente', 4, 200, 6.80, 1360.00, 'Electronics Pro', 'EP-2025-0298', '2025-08-20', 1, 'Sensores HC-SR04 calibrados', '2025-08-20 19:10:00'),
(6, 'ENT-2025-006', 'libro', 4, 25, 225.00, 5625.00, 'AI Publications', 'AI-2025-0134', '2025-08-22', 2, 'Incluye código de ejemplos', '2025-08-22 12:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id`, `estudiante_id`, `curso_id`, `fecha_inscripcion`) VALUES
(1, 18, 1, '2025-09-02 13:41:15');

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

--
-- Volcado de datos para la tabla `intentos_login`
--

INSERT INTO `intentos_login` (`id`, `email`, `ip_address`, `user_agent`, `exito`, `fecha_intento`) VALUES
(1, 'luisrochavela1@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0', 0, '2025-09-02 02:08:36'),
(2, 'luisrochavela1@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0', 0, '2025-09-02 02:08:47'),
(3, 'lr0900573@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 0, '2025-09-02 13:41:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `laboratorios`
--

CREATE TABLE `laboratorios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text NOT NULL,
  `objetivos` text DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `docente_responsable_id` int(11) NOT NULL,
  `participantes` longtext DEFAULT NULL COMMENT 'JSON con IDs de usuarios participantes',
  `componentes_utilizados` longtext DEFAULT NULL COMMENT 'JSON con componentes utilizados',
  `tecnologias` longtext DEFAULT NULL COMMENT 'JSON con tecnologías utilizadas',
  `resultado` text DEFAULT NULL,
  `conclusiones` text DEFAULT NULL,
  `nivel_dificultad` enum('Básico','Intermedio','Avanzado','Experto') DEFAULT 'Básico',
  `duracion_dias` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Planificado','En Progreso','Completado','Suspendido','Cancelado') DEFAULT 'Planificado',
  `publico` tinyint(1) DEFAULT 1 COMMENT 'Si es visible públicamente',
  `destacado` tinyint(1) DEFAULT 0 COMMENT 'Si aparece en portada',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `laboratorios`
--

INSERT INTO `laboratorios` (`id`, `nombre`, `descripcion`, `objetivos`, `categoria_id`, `docente_responsable_id`, `participantes`, `componentes_utilizados`, `tecnologias`, `resultado`, `conclusiones`, `nivel_dificultad`, `duracion_dias`, `fecha_inicio`, `fecha_fin`, `estado`, `publico`, `destacado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Robot Seguidor de Línea Autónomo', 'Desarrollo de un robot capaz de seguir una línea negra sobre superficie blanca utilizando sensores ópticos', 'Implementar algoritmos de control PID, integrar sensores y actuadores, desarrollar lógica de navegación autónoma', 1, 2, '[4,5,8]', '[1,4,6,7,10,12]', '[\"Arduino IDE\", \"C/C++\", \"Control PID\", \"Sensores IR\"]', 'Robot funcional capaz de seguir líneas con precisión del 95% a velocidad constante', 'El proyecto demostró la importancia del ajuste fino de parámetros PID. Se logró un excelente balance entre velocidad y precisión.', 'Intermedio', 21, '2025-07-15', '2025-08-05', 'Completado', 1, 1, '2025-08-06 14:30:00', '2025-08-06 14:30:00'),
(2, 'Sistema de Monitoreo IoT con ESP32', 'Implementación de una red de sensores inalámbricos para monitoreo ambiental en tiempo real', 'Desarrollar comunicación WiFi, implementar base de datos en la nube, crear dashboard web', 4, 3, '[5,10,12]', '[3,5,6,12,14]', '[\"ESP32-IDF\", \"WiFi\", \"MQTT\", \"Firebase\", \"HTML/CSS/JS\"]', 'Sistema completo de monitoreo con dashboard web y alertas automáticas', 'La integración IoT permite escalabilidad. Se identificó la importancia de protocolos de comunicación eficientes.', 'Avanzado', 35, '2025-06-01', '2025-07-05', 'Completado', 1, 1, '2025-07-06 18:20:00', '2025-07-06 18:20:00'),
(3, 'Prototipo de Brazo Robótico', 'Construcción y programación de un brazo robótico de 4 grados de libertad para tareas de manipulación', 'Aplicar cinemática directa e inversa, implementar control de servomotores, desarrollar interfaz de usuario', 1, 2, '[8,4]', '[7,8,1,11,15]', '[\"Arduino\", \"Cinemática\", \"Servomotores\", \"Python GUI\"]', 'Brazo robótico funcional con precisión de posicionamiento de ±2mm', 'El proyecto destacó la complejidad de los cálculos cinemáticos. Se logró una interfaz intuitiva para el control.', 'Avanzado', 42, '2025-05-10', '2025-06-21', 'Completado', 1, 0, '2025-06-22 13:15:00', '2025-06-22 13:15:00'),
(4, 'Red Neuronal para Reconocimiento de Imágenes', 'Implementación y entrenamiento de una CNN para clasificación de componentes electrónicos', 'Aplicar deep learning, procesar datasets de imágenes, optimizar arquitectura de red', 4, 2, '[4,10]', '[]', '[\"Python\", \"TensorFlow\", \"OpenCV\", \"Jupyter Notebook\"]', 'Modelo con 89% de precisión en clasificación de 10 tipos de componentes', 'El preprocessing de imágenes fue crucial. Se demostró la viabilidad del reconocimiento automático de componentes.', 'Experto', 28, '2025-07-20', '2025-08-17', 'Completado', 1, 1, '2025-08-18 15:45:00', '2025-08-18 15:45:00'),
(5, 'Estación Meteorológica Inteligente', 'Sistema autónomo de medición y predicción climática con machine learning', 'Integrar múltiples sensores ambientales, implementar algoritmos predictivos, crear API REST', 5, 3, '[12,18]', '[3,5,6,11,14,15]', '[\"ESP32\", \"Sensores Múltiples\", \"Machine Learning\", \"API REST\", \"Base de Datos\"]', 'Estación funcional con predicciones 72h con 78% de precisión', 'La combinación de IoT y ML abre nuevas posibilidades. Los datos históricos mejoran significativamente las predicciones.', 'Avanzado', 49, '2025-04-15', '2025-06-03', 'Completado', 1, 1, '2025-06-04 20:30:00', '2025-06-04 20:30:00');

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
(5, 'Matemáticas para Ingenieros', 'Pedro Fernández', 'Fundamentos matemáticos para ingeniería', 10, '978-1234509876', 310, 'Math Ed', '2022', 'matematicas.jpg', '/libros/matematicas.pdf', NULL, 0, 40, 8, 180.00, 0, 1, '2025-08-18 15:16:31'),
(6, 'Robótica con Arduino para Aula', 'Sofía Ramos', 'Proyectos didácticos con Arduino para primaria y secundaria', 6, '978-6000000001', 240, 'EduTech', '2022', 'robotica_aula.jpg', '/libros/robotica_aula.pdf', NULL, 0, 20, 5, 120.00, 0, 1, '2025-09-02 04:26:05'),
(7, 'Iniciación a la Robótica', 'Héctor Aguilar', 'Conceptos básicos de robótica y sensores', 6, '978-6000000002', 200, 'Aprende+', '2021', 'iniciacion_robotica.jpg', '/libros/iniciacion_robotica.pdf', NULL, 0, 18, 5, 95.00, 0, 1, '2025-09-02 04:26:05'),
(8, 'Talleres STEAM con Robots', 'Daniela Peña', 'Secuencias didácticas STEAM con kits educativos', 6, '978-6000000003', 260, 'STEAM House', '2023', 'steam_robots.jpg', '/libros/steam_robots.pdf', NULL, 0, 15, 5, 135.00, 0, 1, '2025-09-02 04:26:05'),
(9, 'Robótica Móvil para Jóvenes', 'Luis Cabrera', 'Line follower, evasión de obstáculos y bluetooth', 6, '978-6000000004', 280, 'RoboPress', '2020', 'robotica_movil.jpg', '/libros/robotica_movil.pdf', NULL, 0, 22, 5, 145.00, 0, 1, '2025-09-02 04:26:05'),
(10, 'Didáctica de la Robótica', 'Paula Calderón', 'Metodologías activas y evaluación por competencias', 6, '978-6000000005', 220, 'EduLab', '2022', 'didactica_robotica.jpg', '/libros/didactica_robotica.pdf', NULL, 0, 16, 5, 110.00, 0, 1, '2025-09-02 04:26:05'),
(11, 'Patrones de Diseño en la Práctica', 'Carolina Ibáñez', 'Aplicación de GoF y patrones modernos', 7, '978-7000000001', 320, 'WebDev Press', '2023', 'patrones_practica.jpg', '/libros/patrones_practica.pdf', NULL, 0, 20, 5, 165.00, 0, 1, '2025-09-02 04:26:05'),
(12, 'Programación Concurrente', 'Julián Vera', 'Hilos, sincronización y concurrencia avanzada', 7, '978-7000000002', 340, 'Code Press', '2021', 'prog_concurrente.jpg', '/libros/prog_concurrente.pdf', NULL, 0, 14, 5, 155.00, 0, 1, '2025-09-02 04:26:05'),
(13, 'Diseño Orientado a Objetos', 'Natalia Ríos', 'Principios SOLID y arquitectura limpia', 7, '978-7000000003', 300, 'Clean Arch', '2022', 'doo.jpg', '/libros/doo.pdf', NULL, 0, 25, 5, 150.00, 0, 1, '2025-09-02 04:26:05'),
(14, 'Estructuras y Algoritmos Avanzados', 'Sergio Maldonado', 'Grafos, DP, flujos y heurísticas', 7, '978-7000000004', 410, 'Algoritmia Ed.', '2020', 'eda_avanzados.jpg', '/libros/eda_avanzados.pdf', NULL, 0, 12, 5, 175.00, 0, 1, '2025-09-02 04:26:05'),
(15, 'Metaprogramación en Python', 'Lucía Navarro', 'Decoradores, introspección y generación de código', 7, '978-7000000005', 260, 'PyBooks', '2023', 'metaprogramacion_py.jpg', '/libros/metaprogramacion_py.pdf', NULL, 0, 18, 5, 140.00, 0, 1, '2025-09-02 04:26:05'),
(16, 'Electrónica desde Cero', 'Carlos Sánchez', 'Fundamentos, mediciones y prácticas guiadas', 8, '978-8000000001', 350, 'Electro Books', '2020', 'electronica_cero.jpg', '/libros/electronica_cero.pdf', NULL, 0, 28, 5, 130.00, 0, 1, '2025-09-02 04:26:05'),
(17, 'Proyectos con Sensores', 'María Valdez', 'Aplicaciones con DHT22, MPU6050, HC-SR04', 8, '978-8000000002', 280, 'Maker Ed.', '2021', 'proyectos_sensores.jpg', '/libros/proyectos_sensores.pdf', NULL, 0, 20, 5, 125.00, 0, 1, '2025-09-02 04:26:05'),
(18, 'Fuentes y Regulación', 'Óscar Rivas', 'Diseño de fuentes lineales y conmutadas', 8, '978-8000000003', 300, 'PowerLab', '2022', 'fuentes_regulacion.jpg', '/libros/fuentes_regulacion.pdf', NULL, 0, 16, 5, 155.00, 0, 1, '2025-09-02 04:26:05'),
(19, 'Señales y Filtros', 'Patricia Vela', 'Análisis de señales y filtros analógicos/digitales', 8, '978-8000000004', 320, 'Signal Press', '2021', 'senales_filtros.jpg', '/libros/senales_filtros.pdf', NULL, 0, 14, 5, 160.00, 0, 1, '2025-09-02 04:26:05'),
(20, 'Electrónica de Potencia', 'Elena Cordero', 'Convertidores, drivers y control', 8, '978-8000000005', 380, 'PowerLab', '2023', 'electronica_potencia.jpg', '/libros/electronica_potencia.pdf', NULL, 0, 10, 5, 185.00, 0, 1, '2025-09-02 04:26:05'),
(21, 'Fundamentos de IA', 'Stuart Russell', 'Búsqueda, probabilidad, aprendizaje y agentes', 9, '978-9000000001', 350, 'AI Publications', '2021', 'fund_ia.jpg', '/libros/fund_ia.pdf', NULL, 0, 22, 5, 190.00, 0, 1, '2025-09-02 04:26:05'),
(22, 'Aprendizaje Automático Práctico', 'Tom Mitchell', 'Modelos supervisados y no supervisados', 9, '978-9000000002', 320, 'AI Press', '2020', 'ml_practico.jpg', '/libros/ml_practico.pdf', NULL, 0, 18, 5, 175.00, 0, 1, '2025-09-02 04:26:05'),
(23, 'Redes Neuronales Profundas', 'Ian Goodfellow', 'CNN, RNN, optimización y regularización', 9, '978-9000000003', 420, 'Deep Books', '2022', 'deep_learning.jpg', '/libros/deep_learning.pdf', NULL, 0, 15, 5, 210.00, 0, 1, '2025-09-02 04:26:05'),
(24, 'Procesamiento de Lenguaje Natural', 'Christopher Manning', 'Embeddings, transformers y aplicaciones', 9, '978-9000000004', 360, 'NLP House', '2023', 'pln.jpg', '/libros/pln.pdf', NULL, 0, 17, 5, 200.00, 0, 1, '2025-09-02 04:26:05'),
(25, 'Visión por Computador', 'Richard Szeliski', 'Formación de imagen, características y reconocimiento', 9, '978-9000000005', 400, 'Vision Lab', '2021', 'vision.jpg', '/libros/vision.pdf', NULL, 0, 13, 5, 195.00, 0, 1, '2025-09-02 04:26:05'),
(26, 'Cálculo para Ingeniería', 'James Stewart', 'Límites, derivadas, integrales y aplicaciones', 10, '978-1000000001', 680, 'Math Ed', '2022', 'calculo.jpg', '/libros/calculo.pdf', NULL, 0, 25, 8, 180.00, 0, 1, '2025-09-02 04:26:05'),
(27, 'Álgebra Lineal Aplicada', 'Gilbert Strang', 'Vectores, matrices, descomposiciones y aplicaciones', 10, '978-1000000002', 520, 'Linear Press', '2021', 'algebra_lineal.jpg', '/libros/algebra_lineal.pdf', NULL, 0, 20, 6, 170.00, 0, 1, '2025-09-02 04:26:05'),
(28, 'Probabilidad y Estadística', 'Morris H. DeGroot', 'Modelos probabilísticos e inferencia', 10, '978-1000000003', 600, 'Stats House', '2020', 'prob_est.jpg', '/libros/prob_est.pdf', NULL, 0, 18, 6, 175.00, 0, 1, '2025-09-02 04:26:05'),
(29, 'Física Universitaria', 'Hugh D. Young', 'Mecánica, ondas, termodinámica y electromagnetismo', 10, '978-1000000004', 760, 'PhysBooks', '2019', 'fisica_uni.jpg', '/libros/fisica_uni.pdf', NULL, 0, 14, 6, 190.00, 0, 1, '2025-09-02 04:26:05'),
(30, 'Métodos Numéricos', 'Richard L. Burden', 'Ecuaciones, interpolación, integración y EDOs', 10, '978-1000000005', 520, 'Num Press', '2023', 'metodos_numericos.jpg', '/libros/metodos_numericos.pdf', NULL, 0, 16, 6, 165.00, 0, 1, '2025-09-02 04:26:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` varchar(20) NOT NULL,
  `archivo` varchar(500) DEFAULT NULL,
  `enlace_externo` varchar(500) DEFAULT NULL,
  `tamaño_archivo` int(11) DEFAULT 0,
  `duracion` int(11) DEFAULT NULL COMMENT 'Duración en segundos para videos/audios',
  `categoria_id` int(11) NOT NULL,
  `docente_id` int(11) NOT NULL,
  `imagen_preview` varchar(255) DEFAULT NULL,
  `publico` tinyint(1) DEFAULT 1 COMMENT 'Si es accesible sin login',
  `descargas` int(11) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `titulo`, `descripcion`, `tipo`, `archivo`, `enlace_externo`, `tamaño_archivo`, `duracion`, `categoria_id`, `docente_id`, `imagen_preview`, `publico`, `descargas`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Manual de Sensores para Robótica', 'Guía PDF de sensores (DHT22, HC-SR04, MPU6050) y conexiones', 'pdf', '/materiales/robotica/manual_sensores.pdf', NULL, 2048, 0, 1, 101, '/materiales/img/manual_sensores.jpg', 1, 3, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(2, 'Video: Calibración de Servos SG90', 'Procedimiento paso a paso para calibrar servos SG90', 'video', '/materiales/robotica/video_servo_calibracion.mp4', 'https://youtu.be/rob_servo', 512000, 780, 1, 102, '/materiales/img/servo_calibracion.jpg', 1, 5, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(3, 'Guía: Brazo Robótico 3 DOF', 'Montaje, cinemática básica y control PWM', 'guia', '/materiales/robotica/guia_brazo_3dof.pdf', NULL, 3072, 0, 1, 103, '/materiales/img/brazo_3dof.jpg', 1, 2, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(4, 'Código: Seguidor de Línea', 'Sketch Arduino para seguidor de línea con 5 sensores', 'codigo', '/materiales/robotica/line_follower.ino', 'https://git.example/line_follower', 64, 0, 1, 104, '/materiales/img/line_follower.jpg', 1, 8, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(5, 'Dataset: Lecturas Ultrasonido', 'CSV con distancias medidas en diferentes escenarios', 'dataset', '/materiales/robotica/dataset_ultrasonido.csv', NULL, 2560, 0, 1, 105, '/materiales/img/dataset_ultra.jpg', 0, 1, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(6, 'Apuntes de POO en Java', 'Colecciones, genéricos y patrones básicos', 'pdf', '/materiales/programacion/poo_java.pdf', NULL, 1536, 0, 2, 101, '/materiales/img/poo_java.jpg', 1, 4, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(7, 'Video: Patrones de Diseño', 'Factory, Strategy y Observer con ejemplos', 'video', '/materiales/programacion/patrones_diseno.mp4', 'https://youtu.be/patrones_java', 430000, 900, 2, 102, '/materiales/img/patrones.jpg', 1, 6, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(8, 'Guía: APIs REST con Node.js', 'Buenas prácticas, rutas, middlewares y testing', 'guia', '/materiales/programacion/guia_api_node.pdf', NULL, 2048, 0, 2, 103, '/materiales/img/api_node.jpg', 1, 2, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(9, 'Código: Búsqueda Binaria en C', 'Implementación y pruebas unitarias', 'codigo', '/materiales/programacion/busqueda_binaria.c', 'https://git.example/c-busqueda', 32, 0, 2, 104, '/materiales/img/codigo_c.jpg', 1, 7, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(10, 'Dataset: Logs de API de Prueba', 'Logs anonimizados para ejercicios de parsing', 'dataset', '/materiales/programacion/logs_api.csv', NULL, 8192, 0, 2, 105, '/materiales/img/logs_api.jpg', 0, 0, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(11, 'Manual: Leyes de Ohm y Kirchhoff', 'Resumen teórico con ejemplos de laboratorio', 'pdf', '/materiales/electronica/ohm_kirchhoff.pdf', NULL, 1792, 0, 3, 101, '/materiales/img/ohm_kirchhoff.jpg', 1, 12, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(12, 'Video: Diseño de PCB en KiCad', 'Flujo, ruteo y exportación de gerbers', 'video', '/materiales/electronica/kicad_pcb.mp4', 'https://youtu.be/kicad_pcb', 480000, 1200, 3, 102, '/materiales/img/kicad.jpg', 1, 9, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(13, 'Guía: Medición con Multímetro', 'Continuidad, resistencia, voltaje y corriente', 'guia', '/materiales/electronica/guia_multimetro.pdf', NULL, 1024, 0, 3, 103, '/materiales/img/multimetro.jpg', 1, 3, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(14, 'Código: Lectura de MPU6050 (I2C)', 'Ejemplo Arduino para acelerómetro/giroscopio', 'codigo', '/materiales/electronica/mpu6050_i2c.ino', 'https://git.example/mpu6050', 48, 0, 3, 104, '/materiales/img/mpu6050.jpg', 1, 6, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(15, 'Dataset: Señales de Sensores', 'CSV con señales capturadas a 1 kHz', 'dataset', '/materiales/electronica/senales_sensores.csv', NULL, 12288, 0, 3, 105, '/materiales/img/senales.jpg', 0, 2, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(16, 'Notas: Fundamentos de IA', 'Agentes racionales, búsqueda y probabilidad', 'pdf', '/materiales/ia/fundamentos_ia.pdf', NULL, 2048, 0, 4, 101, '/materiales/img/fund_ia.jpg', 1, 5, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(17, 'Video: Clasificación con sklearn', 'Pipeline, métricas y validación cruzada', 'video', '/materiales/ia/sklearn_clasificacion.mp4', 'https://youtu.be/sklearn_clf', 520000, 1100, 4, 102, '/materiales/img/sklearn.jpg', 1, 8, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(18, 'Guía: Red Neuronal desde Cero', 'Implementación forward/backprop en numpy', 'guia', '/materiales/ia/nn_desde_cero.pdf', NULL, 3072, 0, 4, 103, '/materiales/img/nn.jpg', 1, 4, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(19, 'Código: CNN básica (PyTorch)', 'Modelo simple para MNIST/CIFAR', 'codigo', '/materiales/ia/cnn_pytorch.py', 'https://git.example/cnn', 64, 0, 4, 104, '/materiales/img/cnn.jpg', 1, 7, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(20, 'Dataset: Imágenes de Demostración', 'Subset de 2k imágenes para práctica', 'dataset', '/materiales/ia/dataset_imagenes.zip', NULL, 204800, 0, 4, 105, '/materiales/img/dataset_img.jpg', 0, 1, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(21, 'Apuntes de Estadística Descriptiva', 'Medidas, distribuciones y visualización', 'pdf', '/materiales/ds/estadistica_descriptiva.pdf', NULL, 2304, 0, 5, 101, '/materiales/img/estadistica.jpg', 1, 6, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(22, 'Video: Limpieza de Datos con Pandas', 'Tratamiento de nulos, tipos y outliers', 'video', '/materiales/ds/pandas_limpieza.mp4', 'https://youtu.be/pandas_clean', 610000, 1300, 5, 102, '/materiales/img/pandas.jpg', 1, 10, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(23, 'Guía: Visualización Efectiva', 'Principios de diseño y librerías Python', 'guia', '/materiales/ds/visualizacion_efectiva.pdf', NULL, 2048, 0, 5, 103, '/materiales/img/viz.jpg', 1, 3, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(24, 'Código: Pipeline de ML (sklearn)', 'Preprocesamiento, grid search y evaluación', 'codigo', '/materiales/ds/pipeline_ml.py', 'https://git.example/pipeline', 72, 0, 5, 104, '/materiales/img/pipeline.jpg', 1, 5, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40'),
(25, 'Dataset: Ventas Limpias (Demo)', 'Dataset limpio para prácticas de análisis', 'dataset', '/materiales/ds/ventas_limpias.csv', NULL, 10240, 0, 5, 105, '/materiales/img/ventas.jpg', 0, 0, 1, '2025-09-02 04:42:40', '2025-09-02 04:42:40');

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
(1, 'App\\Models\\User', 206),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 11),
(2, 'App\\Models\\User', 14),
(2, 'App\\Models\\User', 22),
(2, 'App\\Models\\User', 208),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 10),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 207),
(6, 'App\\Models\\User', 9),
(7, 'App\\Models\\User', 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_stock`
--

CREATE TABLE `movimientos_stock` (
  `id` int(11) NOT NULL,
  `componente_id` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste','reserva','liberacion') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `referencia_tipo` enum('venta','compra','ajuste_manual','devolucion','reserva') DEFAULT 'ajuste_manual',
  `referencia_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `nota` decimal(5,2) NOT NULL,
  `fecha_calificacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_history`
--

CREATE TABLE `password_history` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(40, 'home.view', 'web', NULL, NULL),
(41, 'catalogo.view', 'web', NULL, NULL),
(42, 'profile.view', 'web', NULL, NULL),
(43, 'profile.edit', 'web', NULL, NULL),
(44, 'profile.password', 'web', NULL, NULL),
(45, 'profile.avatar', 'web', NULL, NULL),
(46, 'courses.enroll', 'web', NULL, NULL),
(47, 'courses.unenroll', 'web', NULL, NULL),
(48, 'materials.download', 'web', NULL, NULL),
(51, 'inscripciones.ver', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(52, 'inscripciones.crear', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(53, 'inscripciones.cancelar', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(54, 'progreso.ver', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(55, 'notas.ver', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(56, 'estudiantes.ver', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(57, 'notas.crear', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(58, 'notas.editar', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(59, 'reportes.estudiantes', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(60, 'laboratorios.reservar', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(61, 'componentes.reservar', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(62, 'auditoria.ver', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(63, 'estadisticas.ver', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(64, 'libros.comprar', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(65, 'ventas.procesar', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(66, 'archivos.subir', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(67, 'archivos.descargar', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(68, 'mensajes.ver', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(69, 'mensajes.enviar', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(70, 'notificaciones.ver', 'web', '2025-09-02 13:12:31', '2025-09-02 13:12:31'),
(71, 'admin.dashboard.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(72, 'docente.dashboard.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(73, 'estudiante.dashboard.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(74, 'users.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(75, 'users.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(76, 'users.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(77, 'users.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(78, 'users.activate', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(79, 'users.deactivate', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(80, 'users.reset_password', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(81, 'users.impersonate', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(82, 'roles.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(83, 'roles.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(84, 'roles.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(85, 'roles.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(86, 'permissions.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(87, 'permissions.assign', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(88, 'courses.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(89, 'courses.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(90, 'courses.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(91, 'courses.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(92, 'enrollments.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(93, 'enrollments.approve', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(94, 'enrollments.reject', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(95, 'enrollments.track', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(96, 'grades.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(97, 'grades.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(98, 'grades.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(99, 'grades.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(100, 'materials.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(101, 'materials.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(102, 'materials.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(103, 'materials.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(104, 'materials.upload', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(105, 'books.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(106, 'books.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(107, 'books.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(108, 'books.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(109, 'books.download', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(110, 'books.purchase', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(111, 'components.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(112, 'components.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(113, 'components.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(114, 'components.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(115, 'components.reserve', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(116, 'labs.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(117, 'labs.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(118, 'labs.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(119, 'labs.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(120, 'labs.access', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(121, 'labs.reserve', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(122, 'sales.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(123, 'sales.create', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(124, 'sales.edit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(125, 'sales.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(126, 'sales.process', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(127, 'sales.refund', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(128, 'inventory.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(129, 'inventory.manage', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(130, 'inventory.audit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(131, 'reports.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(132, 'reports.export', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(133, 'reports.students', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(134, 'reports.courses', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(135, 'reports.sales', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(136, 'reports.financial', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(137, 'analytics.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(138, 'analytics.advanced', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(139, 'security.dashboard', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(140, 'security.logs', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(141, 'security.audit', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(142, 'security.monitor', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(143, 'security.export', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(144, 'system.settings', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(145, 'system.maintenance', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(146, 'system.backup', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(147, 'messages.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(148, 'messages.send', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(149, 'messages.delete', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(150, 'notifications.view', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(151, 'notifications.send', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12'),
(152, 'notifications.manage', 'web', '2025-09-02 13:25:12', '2025-09-02 13:25:12');

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
(5, 8, 2, 23.80, 180, '2025-08-18 15:16:31', 0, '2025-08-18 15:16:31'),
(6, 18, 1, 0.00, 0, '2025-09-02 13:41:19', 0, '2025-09-02 13:41:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rate_limit_attempts`
--

CREATE TABLE `rate_limit_attempts` (
  `id` int(11) NOT NULL,
  `client_id` varchar(64) NOT NULL COMMENT 'Hash único del cliente (IP + User-Agent + Email)',
  `action` varchar(50) NOT NULL COMMENT 'Tipo de acción (login, otp, password_reset, etc)',
  `ip_address` varchar(45) NOT NULL COMMENT 'Dirección IP del cliente',
  `user_agent` text DEFAULT NULL COMMENT 'User Agent del navegador',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para control de rate limiting y protección contra ataques de fuerza bruta';

--
-- Volcado de datos para la tabla `rate_limit_attempts`
--

INSERT INTO `rate_limit_attempts` (`id`, `client_id`, `action`, `ip_address`, `user_agent`, `created_at`) VALUES
(13, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 12:30:14'),
(14, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 12:30:35'),
(15, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 12:30:41'),
(16, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 13:40:34'),
(17, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 13:40:55'),
(18, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 13:41:54'),
(19, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 13:42:08'),
(20, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 13:42:28'),
(21, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 14:09:19'),
(22, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 14:10:00'),
(23, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 14:20:56'),
(24, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 14:21:17'),
(25, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 14:21:33'),
(26, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 14:24:24'),
(27, '58c9c9af61f5bb300a170e27d9c07d319909250a0d1ee5e53977b39a883585db', 'default', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 14:26:56'),
(28, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 14:27:14'),
(29, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 14:15:13'),
(30, 'a31a21ddffedddf38a7b4114fdc0294339022c7951062e8dd72fb9e8d09796ea', 'default', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 14:15:40');

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
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(40, 2),
(40, 3),
(40, 6),
(40, 7),
(41, 1),
(41, 2),
(41, 3),
(41, 6),
(41, 7),
(42, 1),
(42, 2),
(42, 3),
(42, 6),
(42, 7),
(43, 1),
(43, 2),
(43, 3),
(43, 6),
(43, 7),
(44, 1),
(44, 2),
(44, 3),
(44, 6),
(44, 7),
(45, 1),
(45, 2),
(45, 3),
(45, 6),
(45, 7),
(46, 1),
(46, 3),
(47, 1),
(47, 3),
(48, 1),
(48, 2),
(48, 3),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(72, 2),
(73, 1),
(73, 3),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(88, 2),
(88, 3),
(88, 6),
(89, 1),
(89, 2),
(90, 1),
(90, 2),
(91, 1),
(91, 2),
(92, 1),
(92, 2),
(92, 3),
(92, 6),
(93, 1),
(93, 2),
(94, 1),
(94, 2),
(95, 1),
(95, 2),
(95, 3),
(96, 1),
(96, 2),
(96, 3),
(96, 6),
(97, 1),
(97, 2),
(98, 1),
(98, 2),
(99, 1),
(99, 2),
(100, 1),
(100, 2),
(100, 3),
(100, 6),
(101, 1),
(101, 2),
(102, 1),
(102, 2),
(103, 1),
(103, 2),
(104, 1),
(104, 2),
(105, 1),
(105, 2),
(105, 3),
(105, 6),
(106, 1),
(106, 2),
(107, 1),
(107, 2),
(108, 1),
(108, 2),
(109, 1),
(109, 2),
(109, 3),
(110, 1),
(110, 3),
(111, 1),
(111, 2),
(111, 3),
(111, 6),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(115, 2),
(115, 3),
(116, 1),
(116, 2),
(116, 3),
(116, 6),
(117, 1),
(117, 2),
(118, 1),
(118, 2),
(119, 1),
(119, 2),
(120, 1),
(120, 2),
(120, 3),
(121, 1),
(121, 2),
(121, 3),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(131, 2),
(131, 6),
(132, 1),
(132, 2),
(133, 1),
(133, 2),
(134, 1),
(134, 2),
(135, 1),
(136, 1),
(137, 1),
(137, 6),
(138, 1),
(139, 1),
(140, 1),
(141, 1),
(142, 1),
(143, 1),
(144, 1),
(145, 1),
(146, 1),
(147, 1),
(147, 2),
(147, 3),
(148, 1),
(148, 2),
(148, 3),
(149, 1),
(149, 2),
(150, 1),
(150, 2),
(150, 3),
(151, 1),
(151, 2),
(152, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `security_log`
--

CREATE TABLE `security_log` (
  `id` int(11) NOT NULL,
  `event_type` enum('LOGIN_SUCCESS','LOGIN_FAILED','LOGOUT','RATE_LIMIT','ACCESS_DENIED','PASSWORD_CHANGE','EMAIL_CHANGE','SUSPICIOUS_ACTIVITY') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `severity` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'MEDIUM',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `security_log`
--

INSERT INTO `security_log` (`id`, `event_type`, `user_id`, `email`, `ip_address`, `user_agent`, `details`, `severity`, `created_at`) VALUES
(1, 'SUSPICIOUS_ACTIVITY', NULL, NULL, '0.0.0.0', '', '{\"test\":\"Segundo intento\",\"mensaje\":\"Probando logging corregido\"}', 'CRITICAL', '2025-09-02 12:40:56');

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
-- Estructura de tabla para la tabla `stock_reservado`
--

CREATE TABLE `stock_reservado` (
  `id` int(11) NOT NULL,
  `componente_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `referencia_tipo` enum('venta_proceso','carrito','orden_pendiente') NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_reserva` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  `estado` enum('activo','liberado','completado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `intentos_fallidos` int(11) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `telefono`, `fecha_nacimiento`, `avatar`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `intentos_fallidos`, `bloqueado_hasta`) VALUES
(1, 'Admin', 'Tech Home', 'luisrochavela1@gmail.com', '$2y$10$rOqR/us0TLqgtfz6yZGCVua37JMzB7HO5S6tMWwZRuyn8oBIW/y46', '', '1998-05-21', NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:54:44', 0, NULL),
(2, 'María', 'Gómez', 'maria.gomez@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07', 0, NULL),
(3, 'Carlos', 'Fernández', 'carlos.fernandez@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07', 0, NULL),
(4, 'Ana', 'Rodríguez', 'ana.rodriguez@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07', 0, NULL),
(5, 'Luis', 'Pérez', 'luis.perez@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07', 0, NULL),
(6, 'Demo', 'Invitado', 'demo@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07', 0, NULL),
(7, 'Pedro', 'Morales', 'pedro.morales@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07', 0, NULL),
(8, 'Laura', 'Santos', 'laura.santos@techhome.bo', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', NULL, NULL, NULL, 1, '2025-08-18 15:16:31', '2025-08-19 23:42:07', 0, NULL),
(9, 'JHOEL', 'ZURITA', 'jhoel0521@gmail.com', '$2y$10$xdeoOY9xiJnH8sS8iaYo6.iJE1i/25LTCuWSNclF1h8S0qmH2LM5.', '1231312', '1998-05-21', NULL, 1, '2025-08-18 21:51:10', '2025-08-19 23:42:07', 0, NULL),
(10, 'test', 'UPDS', 'test123@gmail.com', '$2y$10$3VQQ7OoyKAqZ0zDxuF/CNO1Q1w7bqLPFGueVCbGVPbMDbzPGoESm6', '12312312', '2002-06-13', NULL, 1, '2025-08-20 12:09:11', '2025-08-20 12:09:11', 0, NULL),
(11, 'test2', 'UPDS', 'test2080@gmail.com', '$2y$10$GnPAx8X6yW0LPDrq0Eb8p.CaagbOU/4xN9EeBJ8f3737HxPOKtqUK', '12312312', '2002-06-13', NULL, 1, '2025-08-20 12:30:29', '2025-08-20 12:30:29', 0, NULL),
(12, 'Carlos', 'Rocha', 'carlosrocha123@gmail.com', '$2y$10$yqdtUnCIFVhj8mTGiAyG4OLqwO/Uoajn6Shlst8e5IrlOHd0yMcry', '', '2003-07-18', NULL, 1, '2025-08-20 12:33:03', '2025-08-20 12:33:03', 0, NULL),
(14, 'Douglas', 'Flor', 'douglasdfh88@gmail.com', '$2y$10$JCIKzYXL8Qp0vLJu4eeJEe1vUCB0XRieVyJA6QyWmn8HbdA5swIw6', '21321421', '1994-07-15', NULL, 1, '2025-08-20 13:28:06', '2025-08-20 13:28:06', 0, NULL),
(15, 'Felipe', 'Nazel', 'naxelf666@gmail.com', '$2y$10$ifVgYZbviCw8VjaT3MQZ8.4M1TIoRbJGp2MH8A7zFAYQeSZaTng9S', '4325342', '2002-06-14', NULL, 1, '2025-08-20 13:29:33', '2025-08-20 13:29:33', 0, NULL),
(18, 'Luis', 'Rocha', 'luisrochavela990@gmail.com', '$2y$10$NByMTYDxfYa50zKkFlJgZOxW6fiuFNc3AVtgUgor22S3qaD6IPWKO', '+59168832824', '2002-03-09', NULL, 1, '2025-08-22 13:20:28', '2025-08-22 13:20:28', 0, NULL),
(20, 'Leonardo', 'Peña Añez', 'leonardopenaanez@gmail.com', '$2y$10$k54gKH7i.qX8Q10pbOP9j..aBw5InzaiQEnZYMPCd6itcN/w13/VC', '75678428', '2005-08-06', NULL, 1, '2025-08-25 13:17:14', '2025-08-25 13:17:14', 0, NULL),
(21, 'Gustavo', 'Tantani Mamani', 'tantani.m.g@gmail.com', '$2y$10$fZFHauZ2i/vZG0Q3e2mf.ecp839PSbuqvoB3sudBctoq4a/uiTmbG', '70017480', '2000-10-01', NULL, 1, '2025-08-25 13:31:45', '2025-08-25 13:31:45', 0, NULL),
(22, 'Luis', 'Rocha', 'lr0900573@gmail.com', '$2y$10$0ALdoKNZs10z097oNpHTK.wZlGEZcwCm6wM06RMFEviUUv8A/x3Xy', '234324', '2011-02-12', NULL, 1, '2025-09-01 01:46:40', '2025-09-01 01:46:40', 4, '2025-09-02 09:46:54'),
(101, 'Juan', 'Pérez', 'juan.perez101@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70012345', '1980-01-10', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(102, 'María', 'Gómez', 'maria.gomez102@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70012346', '1985-02-15', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(103, 'Carlos', 'Fernández', 'carlos.fernandez103@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70012347', '1978-07-22', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(104, 'Ana', 'Rodríguez', 'ana.rodriguez104@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70012348', '1990-03-18', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(105, 'Luis', 'Pérez', 'luis.perez105@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70012349', '1982-11-05', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(201, 'Sofía', 'Ramírez', 'sofia.ramirez201@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70123456', '2002-06-21', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(202, 'Mateo', 'Vargas', 'mateo.vargas202@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70123457', '2001-12-14', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(203, 'Lucía', 'Torres', 'lucia.torres203@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70123458', '2003-03-30', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(204, 'Diego', 'Rojas', 'diego.rojas204@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70123459', '2002-09-07', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(205, 'Valentina', 'Mendoza', 'valentina.mendoza205@techhome.bo', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '70123460', '2001-11-25', NULL, 1, '2025-09-02 04:33:51', '2025-09-02 04:33:51', 0, NULL),
(206, 'Admin', 'Test', 'admin@test.com', '$2y$10$naUUV9HNRVJBq2zMhW44T.eZMFEVF4wjJyXC7a2MQUsRnu1185kES', NULL, NULL, NULL, 1, '2025-09-02 13:05:27', '2025-09-02 13:05:27', 0, NULL),
(207, 'Juan', 'Estudiante', 'estudiante@test.com', '$2y$10$iaLxVlFlr6eQMrBXNEegceW8udWRCATFJ6jlYuVCkVOIQv8C17OoS', NULL, NULL, NULL, 1, '2025-09-02 13:05:27', '2025-09-02 13:05:27', 0, NULL),
(208, 'Juan', 'Pérez', 'docente@test.com', '$2y$10$x.rxf5xCmDdaTj9JglLrS.NUP0b6AhKRFJ9Hk/CeFWXTCzx1mdt0u', NULL, NULL, NULL, 1, '2025-09-02 13:49:06', '2025-09-02 13:49:06', 0, NULL);

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
(4, 'VTA-2025-004', 4, 7, 270.00, 0.00, 35.10, 305.10, 'QR', 'Completada', NULL, '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(126, 'VTA-2025-051', 201, 101, 180.00, 0.00, 23.40, 203.40, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(127, 'VTA-2025-052', 202, 102, 85.00, 8.50, 9.95, 86.45, 'Transferencia', 'Pendiente', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(128, 'VTA-2025-053', 203, 103, 350.00, 35.00, 40.95, 355.95, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(129, 'VTA-2025-054', 204, 104, 270.00, 0.00, 35.10, 305.10, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(130, 'VTA-2025-055', 205, 105, 99.90, 0.00, 12.99, 112.89, 'Efectivo', 'Cancelada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(131, 'VTA-2025-056', 201, 101, 49.50, 4.50, 5.85, 50.85, 'Transferencia', 'Reembolsada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(132, 'VTA-2025-057', 202, 102, 420.00, 21.00, 51.87, 450.87, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(133, 'VTA-2025-058', 203, 103, 215.75, 15.75, 26.02, 226.02, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(134, 'VTA-2025-059', 204, 104, 640.00, 40.00, 78.00, 678.00, 'Efectivo', 'Pendiente', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(135, 'VTA-2025-060', 205, 105, 75.00, 0.00, 9.75, 84.75, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(136, 'VTA-2025-061', 201, 101, 130.00, 10.00, 15.60, 135.60, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(137, 'VTA-2025-062', 202, 102, 199.99, 0.00, 26.00, 225.99, 'QR', 'Cancelada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(138, 'VTA-2025-063', 203, 103, 510.00, 25.00, 63.05, 548.05, 'Efectivo', 'Reembolsada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(139, 'VTA-2025-064', 204, 104, 320.00, 20.00, 39.00, 339.00, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(140, 'VTA-2025-065', 205, 105, 289.50, 0.00, 37.64, 327.14, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(141, 'VTA-2025-066', 201, 101, 950.00, 50.00, 117.00, 1017.00, 'QR', 'Pendiente', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(142, 'VTA-2025-067', 202, 102, 35.00, 0.00, 4.55, 39.55, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(143, 'VTA-2025-068', 203, 103, 60.00, 0.00, 7.80, 67.80, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(144, 'VTA-2025-069', 204, 104, 780.00, 78.00, 91.26, 793.26, 'Tarjeta', 'Cancelada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(145, 'VTA-2025-070', 205, 105, 145.25, 10.25, 17.58, 152.58, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(146, 'VTA-2025-071', 201, 101, 59.90, 5.90, 7.02, 61.02, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(147, 'VTA-2025-072', 202, 102, 499.00, 49.00, 58.50, 508.50, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(148, 'VTA-2025-073', 203, 103, 125.75, 0.00, 16.35, 142.10, 'Tarjeta', 'Pendiente', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(149, 'VTA-2025-074', 204, 104, 88.80, 8.80, 10.40, 90.40, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(150, 'VTA-2025-075', 205, 105, 230.40, 20.40, 27.30, 237.30, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(151, 'VTA-2025-076', 201, 101, 45.00, 0.00, 5.85, 50.85, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(152, 'VTA-2025-077', 202, 102, 8.00, 0.00, 1.04, 9.04, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(153, 'VTA-2025-078', 203, 103, 12.00, 0.00, 1.56, 13.56, 'Tarjeta', 'Pendiente', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(154, 'VTA-2025-079', 204, 104, 15.00, 0.00, 1.95, 16.95, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(155, 'VTA-2025-080', 205, 105, 10.00, 0.00, 1.30, 11.30, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(156, 'VTA-2025-081', 201, 101, 5.00, 0.00, 0.65, 5.65, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(157, 'VTA-2025-082', 202, 102, 9.00, 0.00, 1.17, 10.17, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(158, 'VTA-2025-083', 203, 103, 80.00, 0.00, 10.40, 90.40, 'QR', 'Pendiente', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(159, 'VTA-2025-084', 204, 104, 6.00, 0.00, 0.78, 6.78, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(160, 'VTA-2025-085', 205, 105, 18.00, 0.00, 2.34, 20.34, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(161, 'VTA-2025-086', 201, 101, 95.00, 0.00, 12.35, 107.35, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(162, 'VTA-2025-087', 202, 102, 16.00, 0.00, 2.08, 18.08, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(163, 'VTA-2025-088', 203, 103, 7.50, 0.00, 0.98, 8.48, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(164, 'VTA-2025-089', 204, 104, 25.00, 0.00, 3.25, 28.25, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(165, 'VTA-2025-090', 205, 105, 22.00, 0.00, 2.86, 24.86, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(166, 'VTA-2025-091', 201, 101, 4.00, 0.00, 0.52, 4.52, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(167, 'VTA-2025-092', 202, 102, 180.00, 0.00, 23.40, 203.40, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(168, 'VTA-2025-093', 203, 103, 350.00, 0.00, 45.50, 395.50, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(169, 'VTA-2025-094', 204, 104, 12.50, 0.00, 1.63, 14.13, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(170, 'VTA-2025-095', 205, 105, 3.50, 0.00, 0.46, 3.96, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(171, 'VTA-2025-096', 201, 101, 2.80, 0.00, 0.36, 3.16, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(172, 'VTA-2025-097', 202, 102, 14.00, 0.00, 1.82, 15.82, 'Transferencia', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(173, 'VTA-2025-098', 203, 103, 9.50, 0.00, 1.24, 10.74, 'Tarjeta', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(174, 'VTA-2025-099', 204, 104, 22.00, 0.00, 2.86, 24.86, 'QR', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57'),
(175, 'VTA-2025-100', 205, 105, 11.00, 0.00, 1.43, 12.43, 'Efectivo', 'Completada', NULL, '2025-09-02 04:50:57', '2025-09-02 04:50:57');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_stock_disponible`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_stock_disponible` (
`id` int(11)
,`nombre` varchar(200)
,`stock_total` int(11)
,`stock_reservado` int(11)
,`stock_disponible` bigint(12)
,`stock_minimo` int(11)
,`estado_stock` varchar(10)
,`precio` decimal(10,2)
,`estado` enum('Disponible','Agotado','Descontinuado')
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_stock_disponible`
--
DROP TABLE IF EXISTS `vista_stock_disponible`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_stock_disponible`  AS SELECT `c`.`id` AS `id`, `c`.`nombre` AS `nombre`, `c`.`stock` AS `stock_total`, ifnull(`c`.`stock_reservado`,0) AS `stock_reservado`, `c`.`stock`- ifnull(`c`.`stock_reservado`,0) AS `stock_disponible`, `c`.`stock_minimo` AS `stock_minimo`, CASE WHEN `c`.`stock` - ifnull(`c`.`stock_reservado`,0) <= 0 THEN 'Agotado' WHEN `c`.`stock` - ifnull(`c`.`stock_reservado`,0) <= `c`.`stock_minimo` THEN 'Stock Bajo' ELSE 'Disponible' END AS `estado_stock`, `c`.`precio` AS `precio`, `c`.`estado` AS `estado` FROM `componentes` AS `c` WHERE `c`.`estado` <> 'Descontinuado' ;

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
-- Indices de la tabla `acceso_materiales`
--
ALTER TABLE `acceso_materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_material` (`material_id`),
  ADD KEY `idx_fecha` (`fecha_acceso`),
  ADD KEY `idx_tipo` (`tipo_acceso`);

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
-- Indices de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_address` (`ip_address`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `codigos_otp`
--
ALTER TABLE `codigos_otp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_codigo` (`codigo`),
  ADD KEY `idx_expira_en` (`expira_en`),
  ADD KEY `idx_utilizado` (`utilizado`);

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
  ADD KEY `idx_docente_categoria` (`docente_id`,`categoria_id`);

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
-- Indices de la tabla `entradas_inventario`
--
ALTER TABLE `entradas_inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_entrada` (`numero_entrada`),
  ADD KEY `idx_tipo_producto` (`tipo_producto`),
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_usuario` (`usuario_registro_id`),
  ADD KEY `idx_fecha` (`fecha_entrada`),
  ADD KEY `idx_proveedor` (`proveedor`),
  ADD KEY `idx_numero_factura` (`numero_factura`),
  ADD KEY `idx_fecha_factura` (`fecha_factura`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_ip` (`email`,`ip_address`),
  ADD KEY `idx_fecha_intento` (`fecha_intento`),
  ADD KEY `idx_exito` (`exito`);

--
-- Indices de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_docente` (`docente_responsable_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_nivel` (`nivel_dificultad`),
  ADD KEY `idx_publico` (`publico`),
  ADD KEY `idx_destacado` (`destacado`),
  ADD KEY `idx_fecha_inicio` (`fecha_inicio`),
  ADD KEY `idx_fecha_fin` (`fecha_fin`);

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
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_docente` (`docente_id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_publico` (`publico`),
  ADD KEY `idx_descargas` (`descargas`);

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
-- Indices de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_componente` (`componente_id`),
  ADD KEY `idx_fecha` (`fecha_movimiento`),
  ADD KEY `idx_tipo` (`tipo_movimiento`),
  ADD KEY `idx_referencia` (`referencia_tipo`,`referencia_id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `password_history`
--
ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

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
-- Indices de la tabla `rate_limit_attempts`
--
ALTER TABLE `rate_limit_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_client_action_time` (`client_id`,`action`,`created_at`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_action` (`ip_address`,`action`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_cleanup` (`created_at`),
  ADD KEY `idx_client_id` (`client_id`);

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
-- Indices de la tabla `security_log`
--
ALTER TABLE `security_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_address` (`ip_address`);

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
-- Indices de la tabla `stock_reservado`
--
ALTER TABLE `stock_reservado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_componente` (`componente_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_expiracion` (`fecha_expiracion`),
  ADD KEY `idx_referencia` (`referencia_tipo`,`referencia_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_intentos_fallidos` (`intentos_fallidos`),
  ADD KEY `idx_bloqueado_hasta` (`bloqueado_hasta`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `codigos_otp`
--
ALTER TABLE `codigos_otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `componentes`
--
ALTER TABLE `componentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de la tabla `descargas_libros`
--
ALTER TABLE `descargas_libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT de la tabla `progreso_estudiantes`
--
ALTER TABLE `progreso_estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rate_limit_attempts`
--
ALTER TABLE `rate_limit_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `security_log`
--
ALTER TABLE `security_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sesiones_activas`
--
ALTER TABLE `sesiones_activas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock_reservado`
--
ALTER TABLE `stock_reservado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acceso_invitados`
--
ALTER TABLE `acceso_invitados`
  ADD CONSTRAINT `acceso_invitados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `acceso_materiales`
--
ALTER TABLE `acceso_materiales`
  ADD CONSTRAINT `acceso_materiales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acceso_materiales_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `codigos_otp`
--
ALTER TABLE `codigos_otp`
  ADD CONSTRAINT `fk_codigos_otp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `entradas_inventario`
--
ALTER TABLE `entradas_inventario`
  ADD CONSTRAINT `entradas_inventario_ibfk_1` FOREIGN KEY (`usuario_registro_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD CONSTRAINT `laboratorios_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `laboratorios_ibfk_2` FOREIGN KEY (`docente_responsable_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `materiales_ibfk_2` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`);

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
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `password_history`
--
ALTER TABLE `password_history`
  ADD CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

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
