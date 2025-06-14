-- ==========================================
-- TECH HOME - BASE DE DATOS COMPLETA MYSQL 
-- Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
-- Versión: 2.0 - Sistema de Componentes, Ventas y Seguridad
-- ==========================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS tech_home 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE tech_home;

-- ==========================================
-- TABLAS PRINCIPALES DEL SISTEMA
-- ==========================================

-- TABLA: roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    estado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLA: usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    telefono VARCHAR(20) NULL,
    fecha_nacimiento DATE NULL,
    avatar VARCHAR(255) NULL,
    estado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    INDEX idx_email (email),
    INDEX idx_rol (rol_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- TABLA: categorias (Para cursos, libros y componentes)
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo ENUM('curso', 'libro', 'componente') NOT NULL,
    color VARCHAR(7) DEFAULT '#007bff',
    icono VARCHAR(50) DEFAULT 'fas fa-book',
    estado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_tipo (tipo),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ==========================================
-- MÓDULO DE CURSOS (SIN MATERIALES)
-- ==========================================

-- TABLA: cursos
CREATE TABLE cursos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    contenido LONGTEXT,
    docente_id INT NOT NULL,
    categoria_id INT NOT NULL,
    imagen_portada VARCHAR(255) NULL,
    precio DECIMAL(10,2) DEFAULT 0.00,
    duracion_horas INT DEFAULT 0,
    nivel ENUM('Principiante', 'Intermedio', 'Avanzado') DEFAULT 'Principiante',
    requisitos TEXT NULL,
    objetivos TEXT NULL,
    estado ENUM('Borrador', 'Publicado', 'Archivado') DEFAULT 'Borrador',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (docente_id) REFERENCES usuarios(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX idx_docente (docente_id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_estado (estado),
    INDEX idx_nivel (nivel)
) ENGINE=InnoDB;

-- ==========================================
-- MÓDULO DE LIBROS (CON STOCK)
-- ==========================================

-- TABLA: libros
CREATE TABLE libros (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(150) NOT NULL,
    descripcion TEXT,
    categoria_id INT NOT NULL,
    isbn VARCHAR(20) NULL,
    paginas INT DEFAULT 0,
    editorial VARCHAR(100) NULL,
    año_publicacion YEAR NULL,
    imagen_portada VARCHAR(255) NULL,
    archivo_pdf VARCHAR(500) NULL,
    enlace_externo VARCHAR(500) NULL,
    tamaño_archivo INT DEFAULT 0,
    stock INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    precio DECIMAL(10,2) DEFAULT 0.00,
    es_gratuito TINYINT(1) DEFAULT 1,
    estado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_autor (autor),
    INDEX idx_isbn (isbn),
    INDEX idx_stock (stock),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ==========================================
-- MÓDULO DE COMPONENTES
-- ==========================================

-- TABLA: componentes
CREATE TABLE componentes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    categoria_id INT NOT NULL,
    codigo_producto VARCHAR(50) UNIQUE,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    especificaciones JSON,
    imagen_principal VARCHAR(255),
    imagenes_adicionales JSON,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    proveedor VARCHAR(150),
    estado ENUM('Disponible', 'Agotado', 'Descontinuado') DEFAULT 'Disponible',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_codigo (codigo_producto),
    INDEX idx_stock (stock),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ==========================================
-- MÓDULO DE VENTAS
-- ==========================================

-- TABLA: ventas
CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_venta VARCHAR(20) UNIQUE NOT NULL,
    cliente_id INT NOT NULL,
    vendedor_id INT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0.00,
    impuestos DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    tipo_pago ENUM('Efectivo', 'Transferencia', 'Tarjeta', 'QR') DEFAULT 'Efectivo',
    estado ENUM('Pendiente', 'Completada', 'Cancelada', 'Reembolsada') DEFAULT 'Pendiente',
    notas TEXT NULL,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id),
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(id),
    INDEX idx_numero_venta (numero_venta),
    INDEX idx_cliente (cliente_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_venta)
) ENGINE=InnoDB;

-- TABLA: detalle_ventas
CREATE TABLE detalle_ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venta_id INT NOT NULL,
    tipo_producto ENUM('libro', 'componente') NOT NULL,
    producto_id INT NOT NULL,
    nombre_producto VARCHAR(200) NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    INDEX idx_venta (venta_id),
    INDEX idx_tipo_producto (tipo_producto),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB;

-- ==========================================
-- MÓDULO DE SEGURIDAD Y SESIONES
-- ==========================================

-- TABLA: sesiones_activas
CREATE TABLE sesiones_activas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL UNIQUE,
    dispositivo VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    navegador VARCHAR(100) NULL,
    sistema_operativo VARCHAR(100) NULL,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activa TINYINT(1) DEFAULT 1,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_activa (usuario_id, activa),
    INDEX idx_session_id (session_id),
    INDEX idx_ip_address (ip_address),
    INDEX idx_fecha_actividad (fecha_actividad)
) ENGINE=InnoDB;

-- TABLA: acceso_invitados
CREATE TABLE acceso_invitados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    fecha_inicio DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    dias_restantes INT DEFAULT 3,
    ultima_notificacion DATE NULL,
    notificaciones_enviadas JSON DEFAULT '[]',
    acceso_bloqueado TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_activo (usuario_id, acceso_bloqueado),
    INDEX idx_fecha_vencimiento (fecha_vencimiento),
    INDEX idx_dias_restantes (dias_restantes)
) ENGINE=InnoDB;

-- TABLA: intentos_login
CREATE TABLE intentos_login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    exito TINYINT(1) DEFAULT 0,
    fecha_intento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email_ip (email, ip_address),
    INDEX idx_fecha_intento (fecha_intento),
    INDEX idx_exito (exito)
) ENGINE=InnoDB;

-- ==========================================
-- TABLAS DE SEGUIMIENTO Y PROGRESO
-- ==========================================

-- TABLA: progreso_estudiantes
CREATE TABLE progreso_estudiantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estudiante_id INT NOT NULL,
    curso_id INT NOT NULL,
    progreso_porcentaje DECIMAL(5,2) DEFAULT 0.00,
    tiempo_estudiado INT DEFAULT 0, -- en minutos
    ultima_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completado TINYINT(1) DEFAULT 0,
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (estudiante_id) REFERENCES usuarios(id),
    FOREIGN KEY (curso_id) REFERENCES cursos(id),
    UNIQUE KEY unique_estudiante_curso (estudiante_id, curso_id),
    INDEX idx_estudiante (estudiante_id),
    INDEX idx_curso (curso_id),
    INDEX idx_completado (completado)
) ENGINE=InnoDB;

-- TABLA: descargas_libros (para seguimiento)
CREATE TABLE descargas_libros (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    libro_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    fecha_descarga TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (libro_id) REFERENCES libros(id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_libro (libro_id),
    INDEX idx_fecha (fecha_descarga)
) ENGINE=InnoDB;

-- ==========================================
-- CONFIGURACIONES DEL SISTEMA
-- ==========================================

-- TABLA: configuraciones
CREATE TABLE configuraciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    descripcion TEXT NULL,
    tipo ENUM('texto', 'numero', 'booleano', 'json') DEFAULT 'texto',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- VISTAS ÚTILES DEL SISTEMA
-- ==========================================

-- Vista: Información completa de usuarios
CREATE VIEW vista_usuarios AS
SELECT 
    u.id,
    u.nombre,
    u.apellido,
    CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
    u.email,
    u.telefono,
    u.avatar,
    u.estado,
    r.nombre as rol,
    u.fecha_creacion
FROM usuarios u
INNER JOIN roles r ON u.rol_id = r.id;

-- Vista: Cursos con información completa
CREATE VIEW vista_cursos AS
SELECT 
    c.id,
    c.titulo,
    c.descripcion,
    c.imagen_portada,
    c.precio,
    c.duracion_horas,
    c.nivel,
    c.estado,
    CONCAT(u.nombre, ' ', u.apellido) AS docente,
    cat.nombre AS categoria,
    cat.color AS color_categoria,
    c.fecha_creacion
FROM cursos c
INNER JOIN usuarios u ON c.docente_id = u.id
INNER JOIN categorias cat ON c.categoria_id = cat.id
WHERE cat.tipo = 'curso';

-- Vista: Libros con stock
CREATE VIEW vista_libros AS
SELECT 
    l.id,
    l.titulo,
    l.autor,
    l.precio,
    l.stock,
    l.stock_minimo,
    l.estado,
    cat.nombre as categoria,
    CASE 
        WHEN l.stock = 0 THEN 'Sin Stock'
        WHEN l.stock <= l.stock_minimo THEN 'Stock Bajo'
        ELSE 'Stock Normal'
    END as estado_stock,
    l.es_gratuito
FROM libros l
INNER JOIN categorias cat ON l.categoria_id = cat.id
WHERE cat.tipo = 'libro';

-- Vista: Componentes con stock
CREATE VIEW vista_componentes AS
SELECT 
    c.id,
    c.nombre,
    c.codigo_producto,
    c.marca,
    c.modelo,
    c.precio,
    c.stock,
    c.stock_minimo,
    c.estado,
    cat.nombre as categoria,
    CASE 
        WHEN c.stock = 0 THEN 'Sin Stock'
        WHEN c.stock <= c.stock_minimo THEN 'Stock Bajo'
        ELSE 'Stock Normal'
    END as estado_stock
FROM componentes c
INNER JOIN categorias cat ON c.categoria_id = cat.id
WHERE cat.tipo = 'componente';

-- Vista: Ventas con detalles
CREATE VIEW vista_ventas AS
SELECT 
    v.id,
    v.numero_venta,
    CONCAT(c.nombre, ' ', c.apellido) as cliente,
    CONCAT(ve.nombre, ' ', ve.apellido) as vendedor,
    v.total,
    v.tipo_pago,
    v.estado,
    v.fecha_venta,
    COUNT(dv.id) as total_items
FROM ventas v
INNER JOIN usuarios c ON v.cliente_id = c.id
LEFT JOIN usuarios ve ON v.vendedor_id = ve.id
LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
GROUP BY v.id;

-- Vista: Sesiones activas con información de usuario
CREATE VIEW vista_sesiones_activas AS
SELECT 
    s.id,
    s.session_id,
    s.dispositivo,
    s.ip_address,
    s.navegador,
    s.sistema_operativo,
    s.fecha_inicio,
    s.fecha_actividad,
    s.activa,
    u.id as usuario_id,
    CONCAT(u.nombre, ' ', u.apellido) as usuario_nombre,
    u.email,
    r.nombre as rol
FROM sesiones_activas s
INNER JOIN usuarios u ON s.usuario_id = u.id
INNER JOIN roles r ON u.rol_id = r.id;

-- Vista: Control de invitados
CREATE VIEW vista_invitados AS
SELECT 
    ai.id,
    ai.fecha_inicio,
    ai.fecha_vencimiento,
    ai.dias_restantes,
    ai.ultima_notificacion,
    ai.acceso_bloqueado,
    u.id as usuario_id,
    CONCAT(u.nombre, ' ', u.apellido) as usuario_nombre,
    u.email,
    CASE 
        WHEN ai.acceso_bloqueado = 1 THEN 'Bloqueado'
        WHEN CURRENT_DATE > ai.fecha_vencimiento THEN 'Vencido'
        WHEN DATEDIFF(ai.fecha_vencimiento, CURRENT_DATE) = 0 THEN 'Último día'
        ELSE CONCAT(DATEDIFF(ai.fecha_vencimiento, CURRENT_DATE), ' días restantes')
    END as estado_acceso
FROM acceso_invitados ai
INNER JOIN usuarios u ON ai.usuario_id = u.id;

-- ==========================================
-- FIN DEL SCRIPT SQL
-- ==========================================