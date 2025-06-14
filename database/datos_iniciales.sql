-- ==========================================
-- DATOS INICIALES DEL SISTEMA - VERSIÓN 2.0
-- Tech Home Bolivia - Sistema de Componentes, Ventas y Seguridad
-- ==========================================

-- Insertar roles del sistema
INSERT INTO roles (nombre, descripcion) VALUES 
('Administrador', 'Acceso completo al sistema'),
('Docente', 'Puede crear y gestionar cursos'),
('Estudiante', 'Puede acceder a cursos y materiales'),
('Invitado', 'Acceso temporal de 3 días a todo el material'),
('Vendedor', 'Puede gestionar ventas de productos');

-- Insertar categorías del sistema
INSERT INTO categorias (nombre, descripcion, tipo, color, icono) VALUES 
-- Categorías para CURSOS
('Robótica', 'Cursos relacionados con robótica y automatización', 'curso', '#e74c3c', 'fas fa-robot'),
('Programación', 'Cursos de desarrollo de software y programación', 'curso', '#3498db', 'fas fa-code'),
('Electrónica', 'Cursos de electrónica y circuitos', 'curso', '#f39c12', 'fas fa-bolt'),
('Inteligencia Artificial', 'Machine Learning, Deep Learning y AI', 'curso', '#9b59b6', 'fas fa-brain'),
('Ciencias de Datos', 'Análisis de datos y visualización', 'curso', '#2ecc71', 'fas fa-chart-bar'),

-- Categorías para LIBROS
('Robótica Educativa', 'Libros sobre robótica y automatización', 'libro', '#e74c3c', 'fas fa-robot'),
('Programación Avanzada', 'Libros de desarrollo y programación', 'libro', '#3498db', 'fas fa-book-open'),
('Electrónica Práctica', 'Manuales de electrónica y circuitos', 'libro', '#f39c12', 'fas fa-microchip'),
('Inteligencia Artificial', 'Textos de IA y Machine Learning', 'libro', '#9b59b6', 'fas fa-brain'),
('Matemáticas y Física', 'Fundamentos científicos', 'libro', '#34495e', 'fas fa-calculator'),

-- Categorías para COMPONENTES
('Microcontroladores', 'Arduino, Raspberry Pi, ESP32', 'componente', '#e67e22', 'fas fa-microchip'),
('Sensores', 'Sensores de temperatura, humedad, movimiento', 'componente', '#27ae60', 'fas fa-thermometer-half'),
('Motores y Actuadores', 'Servos, motores paso a paso, actuadores', 'componente', '#8e44ad', 'fas fa-cogs'),
('Componentes Electrónicos', 'Resistencias, capacitores, LEDs', 'componente', '#f39c12', 'fas fa-plug'),
('Herramientas', 'Soldadores, multímetros, protoboards', 'componente', '#95a5a6', 'fas fa-tools'),
('Kits Educativos', 'Kits completos para aprendizaje', 'componente', '#e74c3c', 'fas fa-box');

-- Insertar usuarios del sistema
-- Contraseña para todos: TechHome2025
INSERT INTO usuarios (nombre, apellido, email, password, rol_id) VALUES 
('Admin', 'Tech Home', 'admin@techhome.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('María', 'Gómez', 'maria.gomez@techhome.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('Carlos', 'Fernández', 'carlos.fernandez@techhome.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('Ana', 'Rodríguez', 'ana.rodriguez@techhome.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
('Luis', 'Pérez', 'luis.perez@techhome.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
('Demo', 'Invitado', 'demo@techhome.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4),
('Pedro', 'Morales', 'pedro.morales@techhome.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5),
('Laura', 'Santos', 'laura.santos@techhome.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3);

-- Insertar cursos de ejemplo (SIN MATERIALES)
INSERT INTO cursos (titulo, descripcion, docente_id, categoria_id, imagen_portada, precio, duracion_horas, nivel, estado) VALUES 
('Introducción a la Robótica', 'Curso básico de robótica con Arduino', 2, 1, 'robotica.jpg', 299.00, 20, 'Principiante', 'Publicado'),
('Programación en Python', 'Aprende Python desde cero', 3, 2, 'python.jpg', 399.00, 40, 'Principiante', 'Publicado'),
('Machine Learning Avanzado', 'Técnicas avanzadas de ML con Python', 2, 4, 'ml.jpg', 599.00, 60, 'Avanzado', 'Publicado'),
('Electrónica Digital', 'Fundamentos de electrónica digital', 3, 3, 'electronica.jpg', 349.00, 30, 'Intermedio', 'Publicado'),
('Análisis de Datos con Pandas', 'Manejo profesional de datos en Python', 2, 5, 'pandas.jpg', 449.00, 35, 'Intermedio', 'Publicado');

-- Insertar libros CON STOCK
INSERT INTO libros (titulo, autor, descripcion, categoria_id, isbn, paginas, editorial, año_publicacion, imagen_portada, archivo_pdf, stock, stock_minimo, precio, es_gratuito) VALUES 
('Robótica Práctica con Arduino', 'Juan Martínez', 'Guía completa para proyectos de robótica con Arduino', 6, '978-1234567890', 320, 'Editorial Tech', 2022, 'robotica_arduino.jpg', '/libros/robotica_arduino.pdf', 25, 5, 150.00, 0),
('Python para Principiantes', 'Ana López', 'Introducción al lenguaje Python desde cero', 7, '978-0987654321', 280, 'Code Press', 2021, 'python_principiantes.jpg', '/libros/python_principiantes.pdf', 30, 5, 120.00, 0),
('Fundamentos de Electrónica', 'Carlos Sánchez', 'Teoría y práctica de circuitos electrónicos', 8, '978-5432109876', 450, 'Electro Books', 2020, 'fundamentos_electronica.jpg', '/libros/fundamentos_electronica.pdf', 15, 3, 200.00, 0),
('Machine Learning Avanzado', 'María García', 'Técnicas avanzadas de aprendizaje automático', 9, '978-6789054321', 380, 'AI Publications', 2023, 'ml_avanzado.jpg', '/libros/ml_avanzado.pdf', 20, 5, 250.00, 0),
('Matemáticas para Ingenieros', 'Pedro Fernández', 'Fundamentos matemáticos para ingeniería', 10, '978-1234509876', 310, 'Math Ed', 2022, 'matematicas.jpg', '/libros/matematicas.pdf', 40, 8, 180.00, 0);

-- Insertar componentes electrónicos
INSERT INTO componentes (nombre, descripcion, categoria_id, codigo_producto, marca, modelo, precio, stock, stock_minimo, proveedor, estado) VALUES 
-- Microcontroladores
('Arduino UNO R3', 'Placa de desarrollo con microcontrolador ATmega328P', 11, 'ARD-UNO-R3', 'Arduino', 'UNO R3', 45.00, 50, 10, 'Arduino Store', 'Disponible'),
('Raspberry Pi 4 Model B', 'Computadora de placa única de 4GB RAM', 11, 'RPI-4B-4GB', 'Raspberry Pi', '4 Model B', 120.00, 25, 5, 'Raspberry Foundation', 'Disponible'),
('ESP32 DevKit V1', 'Módulo WiFi y Bluetooth con microcontrolador dual-core', 11, 'ESP32-DEVKIT', 'Espressif', 'DevKit V1', 25.00, 75, 15, 'Espressif Systems', 'Disponible'),

-- Sensores
('Sensor Ultrasónico HC-SR04', 'Sensor de distancia por ultrasonido', 12, 'HC-SR04', 'Generic', 'HC-SR04', 8.00, 100, 20, 'Electronics Pro', 'Disponible'),
('Sensor de Temperatura DHT22', 'Sensor digital de temperatura y humedad', 12, 'DHT22', 'Aosong', 'DHT22', 12.00, 80, 15, 'Sensor Tech', 'Disponible'),
('Sensor PIR de Movimiento', 'Detector de movimiento infrarrojo pasivo', 12, 'PIR-HC-SR501', 'Generic', 'HC-SR501', 6.00, 60, 10, 'Electronics Pro', 'Disponible'),

-- Motores y Actuadores
('Servo Motor SG90', 'Micro servo de 9g para proyectos de robótica', 13, 'SERVO-SG90', 'TowerPro', 'SG90', 15.00, 40, 8, 'TowerPro', 'Disponible'),
('Motor Paso a Paso 28BYJ-48', 'Motor paso a paso unipolar con driver ULN2003', 13, 'STEPPER-28BYJ', 'Generic', '28BYJ-48', 18.00, 30, 5, 'Motor Solutions', 'Disponible'),
('Motor DC 12V', 'Motor de corriente continua de 12V y 300 RPM', 13, 'MOTOR-DC-12V', 'Generic', 'DC-300RPM', 22.00, 25, 5, 'Motor Solutions', 'Disponible'),

-- Componentes Electrónicos
('Kit de LEDs 5mm (100 piezas)', 'Surtido de LEDs de colores de 5mm', 14, 'LED-KIT-100', 'Generic', 'LED-5MM', 20.00, 50, 10, 'LED World', 'Disponible'),
('Resistencias 1/4W (500 piezas)', 'Kit de resistencias de diferentes valores', 14, 'RES-KIT-500', 'Generic', '1/4W', 25.00, 40, 8, 'Electronics Pro', 'Disponible'),
('Jumper Wires (120 piezas)', 'Cables de conexión macho-macho, hembra-hembra', 14, 'JUMPER-120', 'Generic', 'Dupont', 15.00, 60, 12, 'Wire Tech', 'Disponible'),

-- Herramientas
('Multímetro Digital DT830B', 'Multímetro básico para mediciones eléctricas', 15, 'MULTI-DT830B', 'Generic', 'DT830B', 35.00, 20, 3, 'Tool Master', 'Disponible'),
('Soldador de Estaño 40W', 'Soldador eléctrico con control de temperatura', 15, 'SOLD-40W', 'Weller', 'SP40N', 85.00, 15, 3, 'Weller Tools', 'Disponible'),
('Protoboard 830 puntos', 'Placa de pruebas sin soldadura', 15, 'PROTO-830', 'Generic', '830-tie', 12.00, 45, 8, 'Proto Tech', 'Disponible'),

-- Kits Educativos
('Kit Básico Arduino para Principiantes', 'Kit completo con Arduino UNO y componentes básicos', 16, 'KIT-ARD-BASIC', 'Tech Home', 'BASIC-V1', 180.00, 20, 3, 'Tech Home Store', 'Disponible'),
('Kit Avanzado de Robótica', 'Kit completo para construcción de robots', 16, 'KIT-ROBOT-ADV', 'Tech Home', 'ROBOT-V2', 350.00, 10, 2, 'Tech Home Store', 'Disponible'),
('Kit de Sensores IoT', 'Colección de sensores para proyectos IoT', 16, 'KIT-IOT-SENS', 'Tech Home', 'IOT-V1', 220.00, 15, 3, 'Tech Home Store', 'Disponible');

-- Insertar ventas de ejemplo
INSERT INTO ventas (numero_venta, cliente_id, vendedor_id, subtotal, descuento, impuestos, total, tipo_pago, estado) VALUES 
('VTA-2025-001', 4, 7, 180.00, 0.00, 23.40, 203.40, 'Efectivo', 'Completada'),
('VTA-2025-002', 5, 7, 85.00, 8.50, 9.95, 86.45, 'Transferencia', 'Completada'),
('VTA-2025-003', 8, 7, 350.00, 35.00, 40.95, 355.95, 'Tarjeta', 'Completada'),
('VTA-2025-004', 4, 7, 270.00, 0.00, 35.10, 305.10, 'QR', 'Completada');

-- Insertar detalles de ventas
INSERT INTO detalle_ventas (venta_id, tipo_producto, producto_id, nombre_producto, cantidad, precio_unitario, subtotal) VALUES 
-- Venta 1: Kit Arduino
(1, 'componente', 17, 'Kit Básico Arduino para Principiantes', 1, 180.00, 180.00),
-- Venta 2: Soldador
(2, 'componente', 14, 'Soldador de Estaño 40W', 1, 85.00, 85.00),
-- Venta 3: Kit Robótica
(3, 'componente', 18, 'Kit Avanzado de Robótica', 1, 350.00, 350.00),
-- Venta 4: Libros + Componentes
(4, 'libro', 1, 'Robótica Práctica con Arduino', 1, 150.00, 150.00),
(4, 'componente', 1, 'Arduino UNO R3', 2, 45.00, 90.00),
(4, 'componente', 4, 'Sensor Ultrasónico HC-SR04', 4, 8.00, 32.00);

-- Insertar progreso de estudiantes
INSERT INTO progreso_estudiantes (estudiante_id, curso_id, progreso_porcentaje, tiempo_estudiado, completado) VALUES 
(4, 1, 75.50, 480, 0), -- Ana en Robótica - 8 horas estudiadas
(4, 2, 100.00, 1200, 1), -- Ana completó Python - 20 horas
(5, 1, 45.30, 300, 0), -- Luis en Robótica - 5 horas
(5, 4, 68.90, 750, 0), -- Luis en Electrónica - 12.5 horas
(8, 2, 23.80, 180, 0); -- Laura en Python - 3 horas

-- Insertar descargas de libros
INSERT INTO descargas_libros (usuario_id, libro_id, ip_address) VALUES 
(4, 2, '192.168.1.1'),
(5, 2, '192.168.1.2'),
(5, 5, '192.168.1.2'),
(4, 5, '192.168.1.1'),
(6, 2, '192.168.1.3'), -- Descarga del usuario invitado
(8, 1, '192.168.1.4');

-- Insertar configuraciones del sistema
INSERT INTO configuraciones (clave, valor, descripcion, tipo) VALUES 
-- Configuraciones básicas
('nombre_instituto', 'Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada', 'Nombre completo del instituto', 'texto'),
('email_contacto', 'contacto@techhome.bo', 'Email principal de contacto', 'texto'),
('telefono_contacto', '+591 3 123 4567', 'Teléfono de contacto principal', 'texto'),
('direccion', 'Santa Cruz de la Sierra, Bolivia', 'Dirección física del instituto', 'texto'),
('moneda', 'Bs', 'Símbolo de moneda para precios', 'texto'),

-- Configuraciones de archivos y sistema
('max_file_size', '52428800', 'Tamaño máximo de archivo en bytes (50MB)', 'numero'),
('biblioteca_publica', 'true', 'Si la biblioteca es accesible sin login', 'booleano'),
('registro_publico', 'true', 'Si está habilitado el registro público', 'booleano'),

-- Configuraciones de seguridad y sesiones
('session_timeout', '3600', 'Tiempo de expiración de sesión en segundos (1 hora)', 'numero'),
('max_login_attempts', '5', 'Máximo número de intentos de login fallidos', 'numero'),
('session_restriction', 'true', 'Restricción de una sesión por usuario', 'booleano'),
('track_sessions', 'true', 'Habilitar seguimiento de sesiones activas', 'booleano'),
('lockout_time', '900', 'Tiempo de bloqueo tras intentos fallidos (15 min)', 'numero'),

-- Configuraciones específicas para invitados
('invitado_dias_acceso', '3', 'Días de acceso para usuarios invitados', 'numero'),
('invitado_notificacion_diaria', 'true', 'Enviar notificación diaria a invitados', 'booleano'),

-- Configuraciones de ventas
('iva_porcentaje', '13', 'Porcentaje de IVA para ventas', 'numero'),
('descuento_maximo', '20', 'Porcentaje máximo de descuento permitido', 'numero'),
('numeracion_ventas', 'VTA-{YEAR}-{NUMBER}', 'Formato de numeración de ventas', 'texto'),

-- Configuraciones académicas
('porcentaje_ganancia', '30', 'Porcentaje de ganancia para docentes', 'numero');

-- Insertar acceso de invitado de ejemplo
INSERT INTO acceso_invitados (usuario_id, fecha_inicio, fecha_vencimiento, dias_restantes) VALUES 
(6, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), 3);

-- ==========================================
-- FIN DE DATOS INICIALES
-- ==========================================