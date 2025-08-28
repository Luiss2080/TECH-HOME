-- Tablas adicionales para el sistema Tech Home
-- Base de datos: tech_home

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('video','documento','presentacion','audio','enlace','otro') NOT NULL DEFAULT 'documento',
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
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_docente` (`docente_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_estado` (`estado`),
  KEY `idx_publico` (`publico`),
  CONSTRAINT `materiales_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `materiales_ibfk_2` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_docente` (`docente_responsable_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_nivel` (`nivel_dificultad`),
  KEY `idx_publico` (`publico`),
  KEY `idx_destacado` (`destacado`),
  KEY `idx_fecha_inicio` (`fecha_inicio`),
  CONSTRAINT `laboratorios_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `laboratorios_ibfk_2` FOREIGN KEY (`docente_responsable_id`) REFERENCES `usuarios` (`id`)
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
  `fecha_entrada` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_entrada` (`numero_entrada`),
  KEY `idx_tipo_producto` (`tipo_producto`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_usuario` (`usuario_registro_id`),
  KEY `idx_fecha` (`fecha_entrada`),
  KEY `idx_proveedor` (`proveedor`),
  CONSTRAINT `entradas_inventario_ibfk_1` FOREIGN KEY (`usuario_registro_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `fecha_acceso` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_material` (`material_id`),
  KEY `idx_fecha` (`fecha_acceso`),
  KEY `idx_tipo` (`tipo_acceso`),
  CONSTRAINT `acceso_materiales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `acceso_materiales_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Volcado de datos de ejemplo para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `titulo`, `descripcion`, `tipo`, `archivo`, `enlace_externo`, `tamaño_archivo`, `duracion`, `categoria_id`, `docente_id`, `imagen_preview`, `publico`, `descargas`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Introducción a Arduino - Video Tutorial', 'Video básico sobre conceptos fundamentales de Arduino', 'video', '/materiales/videos/intro_arduino.mp4', NULL, 157286400, 1800, 1, 2, 'intro_arduino_thumb.jpg', 1, 45, 1, '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(2, 'Manual de Soldadura Electrónica', 'Guía práctica para soldadura de componentes electrónicos', 'documento', '/materiales/documentos/manual_soldadura.pdf', NULL, 5242880, NULL, 3, 3, 'soldadura_preview.jpg', 1, 67, 1, '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(3, 'Presentación: Machine Learning Básico', 'Slides introductorios sobre conceptos de ML', 'presentacion', '/materiales/presentaciones/ml_basico.pptx', NULL, 12582912, NULL, 4, 2, 'ml_slides_thumb.jpg', 1, 23, 1, '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(4, 'Tutorial Raspberry Pi 4', 'Configuración inicial y primeros pasos con Raspberry Pi 4', 'video', '/materiales/videos/rpi4_tutorial.mp4', 'https://youtube.com/watch?v=example', 0, 2700, 1, 2, 'rpi4_thumb.jpg', 1, 89, 1, '2025-08-18 15:16:31', '2025-08-18 15:16:31'),
(5, 'Datasheet ESP32', 'Hoja de datos completa del microcontrolador ESP32', 'documento', '/materiales/datasheets/esp32_datasheet.pdf', 'https://www.espressif.com/sites/default/files/documentation/esp32_datasheet_en.pdf', 2097152, NULL, 3, 3, NULL, 1, 34, 1, '2025-08-18 15:16:31', '2025-08-18 15:16:31');

-- --------------------------------------------------------

--
-- Volcado de datos de ejemplo para la tabla `laboratorios`
--

INSERT INTO `laboratorios` (`id`, `nombre`, `descripcion`, `objetivos`, `categoria_id`, `docente_responsable_id`, `participantes`, `componentes_utilizados`, `tecnologias`, `resultado`, `conclusiones`, `nivel_dificultad`, `duracion_dias`, `fecha_inicio`, `fecha_fin`, `estado`, `publico`, `destacado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Robot Seguidor de Línea Autónomo', 'Desarrollo de un robot capaz de seguir una línea negra sobre superficie blanca utilizando sensores ópticos', 'Implementar algoritmos de control PID, integrar sensores y actuadores, desarrollar lógica de navegación autónoma', 1, 2, '[4,5,8]', '[1,4,6,7,10,12]', '["Arduino IDE", "C/C++", "Control PID", "Sensores IR"]', 'Robot funcional capaz de seguir líneas con precisión del 95% a velocidad constante', 'El proyecto demostró la importancia del ajuste fino de parámetros PID. Se logró un excelente balance entre velocidad y precisión.', 'Intermedio', 21, '2025-07-15', '2025-08-05', 'Completado', 1, 1, '2025-08-06 10:30:00', '2025-08-06 10:30:00'),
(2, 'Sistema de Monitoreo IoT con ESP32', 'Implementación de una red de sensores inalámbricos para monitoreo ambiental en tiempo real', 'Desarrollar comunicación WiFi, implementar base de datos en la nube, crear dashboard web', 4, 3, '[5,10,12]', '[3,5,6,12,14]', '["ESP32-IDF", "WiFi", "MQTT", "Firebase", "HTML/CSS/JS"]', 'Sistema completo de monitoreo con dashboard web y alertas automáticas', 'La integración IoT permite escalabilidad. Se identificó la importancia de protocolos de comunicación eficientes.', 'Avanzado', 35, '2025-06-01', '2025-07-05', 'Completado', 1, 1, '2025-07-06 14:20:00', '2025-07-06 14:20:00'),
(3, 'Prototipo de Brazo Robótico', 'Construcción y programación de un brazo robótico de 4 grados de libertad para tareas de manipulación', 'Aplicar cinemática directa e inversa, implementar control de servomotores, desarrollar interfaz de usuario', 1, 2, '[8,4]', '[7,8,1,11,15]', '["Arduino", "Cinemática", "Servomotores", "Python GUI"]', 'Brazo robótico funcional con precisión de posicionamiento de ±2mm', 'El proyecto destacó la complejidad de los cálculos cinemáticos. Se logró una interfaz intuitiva para el control.', 'Avanzado', 42, '2025-05-10', '2025-06-21', 'Completado', 1, 0, '2025-06-22 09:15:00', '2025-06-22 09:15:00'),
(4, 'Red Neuronal para Reconocimiento de Imágenes', 'Implementación y entrenamiento de una CNN para clasificación de componentes electrónicos', 'Aplicar deep learning, procesar datasets de imágenes, optimizar arquitectura de red', 4, 2, '[4,10]', '[]', '["Python", "TensorFlow", "OpenCV", "Jupyter Notebook"]', 'Modelo con 89% de precisión en clasificación de 10 tipos de componentes', 'El preprocessing de imágenes fue crucial. Se demostró la viabilidad del reconocimiento automático de componentes.', 'Experto', 28, '2025-07-20', '2025-08-17', 'Completado', 1, 1, '2025-08-18 11:45:00', '2025-08-18 11:45:00'),
(5, 'Estación Meteorológica Inteligente', 'Sistema autónomo de medición y predicción climática con machine learning', 'Integrar múltiples sensores ambientales, implementar algoritmos predictivos, crear API REST', 5, 3, '[12,18]', '[3,5,6,11,14,15]', '["ESP32", "Sensores Múltiples", "Machine Learning", "API REST", "Base de Datos"]', 'Estación funcional con predicciones 72h con 78% de precisión', 'La combinación de IoT y ML abre nuevas posibilidades. Los datos históricos mejoran significativamente las predicciones.', 'Avanzado', 49, '2025-04-15', '2025-06-03', 'Completado', 1, 1, '2025-06-04 16:30:00', '2025-06-04 16:30:00');

-- --------------------------------------------------------

--
-- Volcado de datos de ejemplo para la tabla `entradas_inventario`
--

INSERT INTO `entradas_inventario` (`id`, `numero_entrada`, `tipo_producto`, `producto_id`, `cantidad`, `precio_unitario`, `costo_total`, `proveedor`, `numero_factura`, `fecha_factura`, `usuario_registro_id`, `observaciones`, `fecha_entrada`) VALUES
(1, 'ENT-2025-001', 'componente', 1, 50, 38.50, 1925.00, 'Arduino Store', 'ARD-2025-0156', '2025-08-15', 1, 'Lote de Arduino UNO R3 originales', '2025-08-15 14:30:00'),
(2, 'ENT-2025-002', 'componente', 3, 100, 21.00, 2100.00, 'Espressif Systems', 'ESP-2025-0089', '2025-08-16', 1, 'ESP32 DevKit V1 - Nueva versión', '2025-08-16 10:15:00'),
(3, 'ENT-2025-003', 'libro', 1, 30, 135.00, 4050.00, 'Editorial Tech', 'ET-2025-0234', '2025-08-17', 2, 'Segunda edición actualizada', '2025-08-17 09:45:00'),
(4, 'ENT-2025-004', 'componente', 10, 75, 18.00, 1350.00, 'LED World', 'LW-2025-0167', '2025-08-18', 1, 'Kit LEDs surtidos alta calidad', '2025-08-18 11:20:00'),
(5, 'ENT-2025-005', 'componente', 4, 200, 6.80, 1360.00, 'Electronics Pro', 'EP-2025-0298', '2025-08-20', 1, 'Sensores HC-SR04 calibrados', '2025-08-20 15:10:00'),
(6, 'ENT-2025-006', 'libro', 4, 25, 225.00, 5625.00, 'AI Publications', 'AI-2025-0134', '2025-08-22', 2, 'Incluye código de ejemplos', '2025-08-22 08:30:00');

-- --------------------------------------------------------

--
-- Volcado de datos de ejemplo para la tabla `acceso_materiales`
--

INSERT INTO `acceso_materiales` (`id`, `usuario_id`, `material_id`, `tipo_acceso`, `ip_address`, `user_agent`, `fecha_acceso`) VALUES
(1, 4, 1, 'visualizado', '192.168.1.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-18 16:30:00'),
(2, 4, 1, 'descargado', '192.168.1.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-18 16:35:00'),
(3, 5, 2, 'visualizado', '192.168.1.2', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2025-08-19 10:15:00'),
(4, 8, 1, 'visualizado', '192.168.1.4', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', '2025-08-19 14:20:00'),
(5, 5, 3, 'descargado', '192.168.1.2', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2025-08-20 09:45:00'),
(6, 10, 4, 'visualizado', '192.168.1.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-21 13:10:00'),
(7, 12, 2, 'descargado', '192.168.1.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-22 11:30:00');

-- --------------------------------------------------------

--
-- Nuevos índices para optimización
--

-- Para la tabla entradas_inventario
ALTER TABLE `entradas_inventario` 
ADD INDEX `idx_numero_factura` (`numero_factura`),
ADD INDEX `idx_fecha_factura` (`fecha_factura`);

-- Para la tabla materiales
ALTER TABLE `materiales` 
ADD INDEX `idx_descargas` (`descargas`);

-- Para la tabla laboratorios
ALTER TABLE `laboratorios` 
ADD INDEX `idx_fecha_fin` (`fecha_fin`);

-- --------------------------------------------------------

-- Actualizar numeración automática para entradas de inventario
ALTER TABLE `entradas_inventario` AUTO_INCREMENT=7;
ALTER TABLE `materiales` AUTO_INCREMENT=6;
ALTER TABLE `laboratorios` AUTO_INCREMENT=6;
ALTER TABLE `acceso_materiales` AUTO_INCREMENT=8;

COMMIT;