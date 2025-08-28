-- =============================================
-- Script alternativo simple para MySQL
-- Fecha: 2025-08-29
-- Descripción: Versión simplificada compatible con todas las versiones de MySQL
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
    referencia_id INT NULL,
    usuario_id INT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    KEY idx_componente (componente_id),
    KEY idx_fecha (fecha_movimiento),
    KEY idx_tipo (tipo_movimiento),
    KEY idx_referencia (referencia_tipo, referencia_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla stock_reservado
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
    
    KEY idx_componente (componente_id),
    KEY idx_estado (estado),
    KEY idx_expiracion (fecha_expiracion),
    KEY idx_referencia (referencia_tipo, referencia_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar campos a componentes (ejecutar uno por uno si hay errores)
-- Campo stock_reservado
ALTER TABLE componentes ADD COLUMN stock_reservado INT DEFAULT 0;

-- Campo alerta_stock_bajo  
ALTER TABLE componentes ADD COLUMN alerta_stock_bajo TINYINT(1) DEFAULT 1;

-- Campo permite_venta_sin_stock
ALTER TABLE componentes ADD COLUMN permite_venta_sin_stock TINYINT(1) DEFAULT 0;

-- Crear vista simple
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

SELECT 'Script ejecutado exitosamente' as mensaje;
