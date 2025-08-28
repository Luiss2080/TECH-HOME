-- =============================================
-- Migración: Crear tabla movimientos_stock
-- Fecha: 2025-08-29
-- Descripción: Tabla para registrar entradas y salidas de stock de componentes
-- =============================================

-- Crear tabla movimientos_stock
CREATE TABLE IF NOT EXISTS movimientos_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    componente_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'reserva', 'liberacion') NOT NULL,
    cantidad INT NOT NULL,
    stock_anterior INT NOT NULL,
    stock_nuevo INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    referencia_tipo ENUM('venta', 'compra', 'ajuste_manual', 'devolucion', 'reserva') DEFAULT 'ajuste_manual',
    referencia_id INT NULL COMMENT 'ID de la venta, compra, etc.',
    usuario_id INT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices para optimización
    INDEX idx_componente (componente_id),
    INDEX idx_fecha (fecha_movimiento),
    INDEX idx_tipo (tipo_movimiento),
    INDEX idx_referencia (referencia_tipo, referencia_id),
    
    -- Llaves foráneas
    FOREIGN KEY (componente_id) REFERENCES componentes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla stock_reservado para manejar reservas de stock durante el proceso de venta
CREATE TABLE IF NOT EXISTS stock_reservado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    componente_id INT NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    referencia_tipo ENUM('venta_proceso', 'carrito', 'orden_pendiente') NOT NULL,
    referencia_id INT NULL,
    usuario_id INT NULL,
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion TIMESTAMP NULL,
    estado ENUM('activo', 'liberado', 'completado') DEFAULT 'activo',
    
    -- Índices
    INDEX idx_componente (componente_id),
    INDEX idx_estado (estado),
    INDEX idx_expiracion (fecha_expiracion),
    INDEX idx_referencia (referencia_tipo, referencia_id),
    
    -- Llaves foráneas
    FOREIGN KEY (componente_id) REFERENCES componentes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar y agregar campos a la tabla componentes
-- Agregar stock_reservado si no existe
SET @sql = 'SELECT COUNT(*) INTO @col_exists FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "componentes" AND COLUMN_NAME = "stock_reservado"';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE componentes ADD COLUMN stock_reservado INT DEFAULT 0 COMMENT "Stock reservado para ventas en proceso"', 
    'SELECT "Campo stock_reservado ya existe" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar alerta_stock_bajo si no existe
SET @sql = 'SELECT COUNT(*) INTO @col_exists FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "componentes" AND COLUMN_NAME = "alerta_stock_bajo"';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE componentes ADD COLUMN alerta_stock_bajo TINYINT(1) DEFAULT 1 COMMENT "Enviar alerta cuando esté bajo de stock"', 
    'SELECT "Campo alerta_stock_bajo ya existe" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar permite_venta_sin_stock si no existe
SET @sql = 'SELECT COUNT(*) INTO @col_exists FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "componentes" AND COLUMN_NAME = "permite_venta_sin_stock"';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE componentes ADD COLUMN permite_venta_sin_stock TINYINT(1) DEFAULT 0 COMMENT "Permitir venta aunque no haya stock"', 
    'SELECT "Campo permite_venta_sin_stock ya existe" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Crear vista para stock disponible real
DROP VIEW IF EXISTS vista_stock_disponible;
CREATE VIEW vista_stock_disponible AS
SELECT 
    c.id,
    c.nombre,
    c.stock as stock_total,
    IFNULL(c.stock_reservado, 0) as stock_reservado,
    (c.stock - IFNULL(c.stock_reservado, 0)) as stock_disponible,
    c.stock_minimo,
    CASE 
        WHEN (c.stock - IFNULL(c.stock_reservado, 0)) <= 0 THEN 'Agotado'
        WHEN (c.stock - IFNULL(c.stock_reservado, 0)) <= c.stock_minimo THEN 'Stock Bajo'
        ELSE 'Disponible'
    END as estado_stock,
    c.precio,
    c.estado
FROM componentes c
WHERE c.estado != 'Descontinuado';

-- Insertar permisos específicos para gestión de stock si no existen
-- Verificar si la tabla permissions existe
SET @table_exists = 0;
SELECT COUNT(*) INTO @table_exists 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'permissions';

-- Solo insertar permisos si la tabla existe
SET @sql = IF(@table_exists > 0, 
    'INSERT IGNORE INTO permissions (name, description, guard_name, created_at, updated_at) VALUES
    ("componentes.stock.ver", "Ver información de stock de componentes", "web", NOW(), NOW()),
    ("componentes.stock.ajustar", "Ajustar stock de componentes", "web", NOW(), NOW()),
    ("componentes.stock.reservar", "Reservar stock de componentes", "web", NOW(), NOW()),
    ("componentes.stock.reportes", "Ver reportes de stock", "web", NOW(), NOW()),
    ("ventas.procesar", "Procesar ventas de componentes", "web", NOW(), NOW())', 
    'SELECT "Tabla permissions no existe, permisos no insertados" AS message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Comentarios para documentación
ALTER TABLE movimientos_stock COMMENT = 'Registro de todos los movimientos de stock de componentes';
ALTER TABLE stock_reservado COMMENT = 'Stock reservado temporalmente durante procesos de venta';

-- Mensaje final
SELECT 'Migración completada exitosamente' AS status,
       'Tablas movimientos_stock y stock_reservado creadas' AS detalle,
       'Campos adicionales agregados a componentes' AS campos,
       'Vista vista_stock_disponible creada' AS vista;
