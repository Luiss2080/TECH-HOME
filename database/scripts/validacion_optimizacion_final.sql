-- ============================================================================
-- SCRIPT DE VALIDACIÓN Y OPTIMIZACIÓN FINAL
-- Fecha: 27 de Agosto de 2025
-- Descripción: Verificaciones, correcciones y optimizaciones finales
-- ============================================================================

USE tech_home;

-- ============================================================================
-- 1. VERIFICACIONES DE INTEGRIDAD
-- ============================================================================

-- Verificar que no existan registros huérfanos
SELECT 'Verificaciones de Integridad:' as titulo;

-- Verificar cursos sin docente válido
SELECT 
    'Cursos sin docente válido:' as verificacion,
    COUNT(*) as cantidad
FROM cursos c 
LEFT JOIN usuarios u ON c.docente_id = u.id 
WHERE u.id IS NULL;

-- Verificar libros sin categoría válida
SELECT 
    'Libros sin categoría válida:' as verificacion,
    COUNT(*) as cantidad
FROM libros l 
LEFT JOIN categorias cat ON l.categoria_id = cat.id 
WHERE cat.id IS NULL;

-- Verificar descargas de libros inexistentes
SELECT 
    'Descargas de libros inexistentes:' as verificacion,
    COUNT(*) as cantidad
FROM descargas_libros dl 
LEFT JOIN libros l ON dl.libro_id = l.id 
WHERE l.id IS NULL;

-- Verificar progreso de cursos inexistentes
SELECT 
    'Progreso de cursos inexistentes:' as verificacion,
    COUNT(*) as cantidad
FROM progreso_estudiantes pe 
LEFT JOIN cursos c ON pe.curso_id = c.id 
WHERE c.id IS NULL;

-- ============================================================================
-- 2. CORRECCIÓN DE DATOS INCONSISTENTES
-- ============================================================================

-- Corregir slugs duplicados en cursos
UPDATE cursos c1 
JOIN (
    SELECT MIN(id) as min_id, slug 
    FROM cursos 
    WHERE slug IS NOT NULL 
    GROUP BY slug 
    HAVING COUNT(*) > 1
) duplicates ON c1.slug = duplicates.slug AND c1.id != duplicates.min_id
SET c1.slug = CONCAT(c1.slug, '-', c1.id);

-- Corregir slugs duplicados en libros
UPDATE libros l1 
JOIN (
    SELECT MIN(id) as min_id, slug 
    FROM libros 
    WHERE slug IS NOT NULL 
    GROUP BY slug 
    HAVING COUNT(*) > 1
) duplicates ON l1.slug = duplicates.slug AND l1.id != duplicates.min_id
SET l1.slug = CONCAT(l1.slug, '-', l1.id);

-- Corregir stock negativo en libros
UPDATE libros 
SET stock = 0 
WHERE stock < 0 AND es_gratuito = 0;

-- Corregir progreso mayor a 100%
UPDATE progreso_estudiantes 
SET progreso_porcentaje = 100.00 
WHERE progreso_porcentaje > 100.00;

-- Corregir calificaciones fuera de rango
UPDATE calificaciones_cursos 
SET calificacion = 5 
WHERE calificacion > 5;

UPDATE calificaciones_cursos 
SET calificacion = 1 
WHERE calificacion < 1;

UPDATE calificaciones_libros 
SET calificacion = 5 
WHERE calificacion > 5;

UPDATE calificaciones_libros 
SET calificacion = 1 
WHERE calificacion < 1;

-- ============================================================================
-- 3. OPTIMIZACIONES DE RENDIMIENTO
-- ============================================================================

-- Analizar y optimizar tablas principales
ANALYZE TABLE cursos, libros, usuarios, descargas_libros, progreso_estudiantes;

-- Crear índices adicionales si no existen
ALTER TABLE cursos 
ADD INDEX IF NOT EXISTS idx_titulo_estado (titulo, estado),
ADD INDEX IF NOT EXISTS idx_precio_nivel (precio, nivel),
ADD INDEX IF NOT EXISTS idx_fecha_creacion_estado (fecha_creacion, estado);

ALTER TABLE libros 
ADD INDEX IF NOT EXISTS idx_titulo_estado (titulo, estado),
ADD INDEX IF NOT EXISTS idx_autor_estado (autor, estado),
ADD INDEX IF NOT EXISTS idx_precio_gratuito (precio, es_gratuito),
ADD INDEX IF NOT EXISTS idx_fecha_creacion_estado (fecha_creacion, estado);

-- Índices compuestos para consultas específicas del módulo
ALTER TABLE inscripciones_cursos 
ADD INDEX IF NOT EXISTS idx_estudiante_estado_fecha (estudiante_id, estado, fecha_inscripcion);

ALTER TABLE progreso_estudiantes 
ADD INDEX IF NOT EXISTS idx_curso_completado_progreso (curso_id, completado, progreso_porcentaje);

ALTER TABLE descargas_libros 
ADD INDEX IF NOT EXISTS idx_fecha_libro_usuario (fecha_descarga, libro_id, usuario_id);

-- ============================================================================
-- 4. PROCEDURES PARA FUNCIONALIDADES COMUNES
-- ============================================================================

-- Procedure para inscribir estudiante a curso
DELIMITER $$
DROP PROCEDURE IF EXISTS InscribirEstudianteCurso$$
CREATE PROCEDURE InscribirEstudianteCurso(
    IN p_estudiante_id INT,
    IN p_curso_id INT,
    IN p_metodo_pago VARCHAR(20),
    IN p_monto_pagado DECIMAL(10,2)
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
DELIMITER ;

-- Procedure para procesar descarga de libro
DELIMITER $$
DROP PROCEDURE IF EXISTS ProcesarDescargaLibro$$
CREATE PROCEDURE ProcesarDescargaLibro(
    IN p_usuario_id INT,
    IN p_libro_id INT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
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
        SELECT 'success' as resultado, 'Descarga procesada exitosamente' as mensaje;
    ELSE
        ROLLBACK;
        SELECT 'error' as resultado, 'Libro no disponible para descarga' as mensaje;
    END IF;
END$$
DELIMITER ;

-- ============================================================================
-- 5. FUNCTIONS ÚTILES
-- ============================================================================

-- Function para calcular progreso general del estudiante
DELIMITER $$
DROP FUNCTION IF EXISTS CalcularProgresoGeneral$$
CREATE FUNCTION CalcularProgresoGeneral(p_estudiante_id INT) 
RETURNS DECIMAL(5,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_progreso DECIMAL(5,2) DEFAULT 0.00;
    
    SELECT COALESCE(AVG(progreso_porcentaje), 0.00)
    INTO v_progreso
    FROM progreso_estudiantes 
    WHERE estudiante_id = p_estudiante_id;
    
    RETURN v_progreso;
END$$
DELIMITER ;

-- Function para obtener nivel del estudiante basado en progreso
DELIMITER $$
DROP FUNCTION IF EXISTS ObtenerNivelEstudiante$$
CREATE FUNCTION ObtenerNivelEstudiante(p_estudiante_id INT) 
RETURNS VARCHAR(20)
READS SQL DATA
DETERMINISTIC
BEGIN
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

-- ============================================================================
-- 6. VIEWS ÚTILES PARA REPORTES
-- ============================================================================

-- Vista de cursos con estadísticas
DROP VIEW IF EXISTS vista_cursos_estadisticas;
CREATE VIEW vista_cursos_estadisticas AS
SELECT 
    c.*,
    u.nombre as docente_nombre,
    u.apellido as docente_apellido,
    cat.nombre as categoria_nombre,
    cat.color as categoria_color,
    COUNT(DISTINCT ic.estudiante_id) as estudiantes_activos,
    COUNT(DISTINCT pe.estudiante_id) as estudiantes_con_progreso,
    COALESCE(AVG(pe.progreso_porcentaje), 0) as progreso_promedio,
    COUNT(DISTINCT CASE WHEN pe.completado = 1 THEN pe.estudiante_id END) as estudiantes_completaron
FROM cursos c
LEFT JOIN usuarios u ON c.docente_id = u.id
LEFT JOIN categorias cat ON c.categoria_id = cat.id
LEFT JOIN inscripciones_cursos ic ON c.id = ic.curso_id AND ic.estado = 'Activa'
LEFT JOIN progreso_estudiantes pe ON c.id = pe.curso_id
GROUP BY c.id;

-- Vista de libros con estadísticas
DROP VIEW IF EXISTS vista_libros_estadisticas;
CREATE VIEW vista_libros_estadisticas AS
SELECT 
    l.*,
    cat.nombre as categoria_nombre,
    cat.color as categoria_color,
    COUNT(DISTINCT dl.usuario_id) as usuarios_descargaron,
    COUNT(dl.id) as total_descargas_real,
    COUNT(DISTINCT DATE(dl.fecha_descarga)) as dias_con_descargas,
    COALESCE(MAX(dl.fecha_descarga), l.fecha_creacion) as ultima_descarga,
    COUNT(DISTINCT fl.usuario_id) as usuarios_favorito,
    CASE 
        WHEN l.es_gratuito = 1 THEN 'Ilimitado'
        WHEN l.stock <= 0 THEN 'Agotado'
        WHEN l.stock <= l.stock_minimo THEN 'Stock Bajo'
        ELSE 'Disponible'
    END as estado_disponibilidad
FROM libros l
LEFT JOIN categorias cat ON l.categoria_id = cat.id
LEFT JOIN descargas_libros dl ON l.id = dl.libro_id
LEFT JOIN favoritos_libros fl ON l.id = fl.libro_id
GROUP BY l.id;

-- Vista de estudiantes con progreso
DROP VIEW IF EXISTS vista_estudiantes_progreso;
CREATE VIEW vista_estudiantes_progreso AS
SELECT 
    u.id,
    u.nombre,
    u.apellido,
    u.email,
    COUNT(DISTINCT ic.curso_id) as cursos_inscritos,
    COUNT(DISTINCT pe.curso_id) as cursos_con_progreso,
    COUNT(DISTINCT CASE WHEN pe.completado = 1 THEN pe.curso_id END) as cursos_completados,
    COALESCE(AVG(pe.progreso_porcentaje), 0) as progreso_promedio,
    SUM(pe.tiempo_estudiado) as tiempo_total_estudiado,
    COUNT(DISTINCT dl.libro_id) as libros_descargados,
    COUNT(DISTINCT fl.libro_id) as libros_favoritos,
    COUNT(DISTINCT fc.curso_id) as cursos_favoritos,
    ObtenerNivelEstudiante(u.id) as nivel_estudiante
FROM usuarios u
LEFT JOIN inscripciones_cursos ic ON u.id = ic.estudiante_id
LEFT JOIN progreso_estudiantes pe ON u.id = pe.estudiante_id
LEFT JOIN descargas_libros dl ON u.id = dl.usuario_id
LEFT JOIN favoritos_libros fl ON u.id = fl.usuario_id
LEFT JOIN favoritos_cursos fc ON u.id = fc.usuario_id
WHERE u.id IN (SELECT model_id FROM model_has_roles WHERE role_id = 3) -- Solo estudiantes
GROUP BY u.id;

-- ============================================================================
-- 7. VALIDACIONES FINALES
-- ============================================================================

-- Verificar que todos los slugs son únicos
SELECT 'Verificaciones finales:' as titulo;

SELECT 
    'Slugs duplicados en cursos:' as verificacion,
    COUNT(*) - COUNT(DISTINCT slug) as cantidad
FROM cursos 
WHERE slug IS NOT NULL;

SELECT 
    'Slugs duplicados en libros:' as verificacion,
    COUNT(*) - COUNT(DISTINCT slug) as cantidad
FROM libros 
WHERE slug IS NOT NULL;

-- Verificar consistencia de contadores
SELECT 
    'Inconsistencias en contador de descargas:' as verificacion,
    COUNT(*) as cantidad
FROM libros l 
WHERE l.descargas_totales != (
    SELECT COUNT(*) 
    FROM descargas_libros dl 
    WHERE dl.libro_id = l.id
);

SELECT 
    'Inconsistencias en contador de estudiantes:' as verificacion,
    COUNT(*) as cantidad
FROM cursos c 
WHERE c.estudiantes_inscritos != (
    SELECT COUNT(*) 
    FROM inscripciones_cursos ic 
    WHERE ic.curso_id = c.id AND ic.estado IN ('Activa', 'Completada')
);

-- ============================================================================
-- 8. ESTADÍSTICAS FINALES
-- ============================================================================

SELECT 'RESUMEN FINAL DE LA BASE DE DATOS' as titulo;

SELECT 
    'Total de usuarios:' as categoria, 
    COUNT(*) as cantidad 
FROM usuarios
UNION ALL
SELECT 
    'Total de cursos:', 
    COUNT(*) 
FROM cursos
UNION ALL
SELECT 
    'Total de libros:', 
    COUNT(*) 
FROM libros
UNION ALL
SELECT 
    'Total de categorías:', 
    COUNT(*) 
FROM categorias
UNION ALL
SELECT 
    'Total de inscripciones:', 
    COUNT(*) 
FROM inscripciones_cursos
UNION ALL
SELECT 
    'Total de descargas:', 
    COUNT(*) 
FROM descargas_libros
UNION ALL
SELECT 
    'Total de módulos:', 
    COUNT(*) 
FROM modulos_curso
UNION ALL
SELECT 
    'Total de permisos:', 
    COUNT(*) 
FROM permissions
UNION ALL
SELECT 
    'Libros con stock bajo:', 
    COUNT(*) 
FROM libros 
WHERE stock <= stock_minimo AND es_gratuito = 0
UNION ALL
SELECT 
    'Cursos publicados:', 
    COUNT(*) 
FROM cursos 
WHERE estado = 'Publicado';

SELECT 'Base de datos optimizada y lista para producción' as mensaje_final;
