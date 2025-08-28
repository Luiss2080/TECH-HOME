-- =====================================================
-- Script SIMPLIFICADO de optimización para tabla cursos
-- Versión SEGURA que verifica columnas existentes
-- Fecha: 2025-08-28
-- =====================================================

USE tech_home;

-- Paso 1: Crear respaldo de la tabla
DROP TABLE IF EXISTS cursos_backup;

CREATE TABLE cursos_backup AS SELECT * FROM cursos;

-- Paso 2: Verificar estructura actual de la tabla
SELECT 'Estructura actual de la tabla cursos:' as info;
DESCRIBE cursos;

-- Paso 3: Agregar columna video_url si no existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'video_url') = 0,
              'ALTER TABLE cursos ADD COLUMN video_url VARCHAR(500) NULL COMMENT "URL del video de YouTube" AFTER descripcion;',
              'SELECT "Columna video_url ya existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Paso 4: Eliminar columnas innecesarias SOLO SI EXISTEN
-- Eliminar slug si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'slug') > 0,
              'ALTER TABLE cursos DROP COLUMN slug;',
              'SELECT "Columna slug no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar contenido si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'contenido') > 0,
              'ALTER TABLE cursos DROP COLUMN contenido;',
              'SELECT "Columna contenido no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar precio si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'precio') > 0,
              'ALTER TABLE cursos DROP COLUMN precio;',
              'SELECT "Columna precio no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar duracion_horas si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'duracion_horas') > 0,
              'ALTER TABLE cursos DROP COLUMN duracion_horas;',
              'SELECT "Columna duracion_horas no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar max_estudiantes si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'max_estudiantes') > 0,
              'ALTER TABLE cursos DROP COLUMN max_estudiantes;',
              'SELECT "Columna max_estudiantes no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar modalidad si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'modalidad') > 0,
              'ALTER TABLE cursos DROP COLUMN modalidad;',
              'SELECT "Columna modalidad no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar certificado si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'certificado') > 0,
              'ALTER TABLE cursos DROP COLUMN certificado;',
              'SELECT "Columna certificado no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar fecha_inicio si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'fecha_inicio') > 0,
              'ALTER TABLE cursos DROP COLUMN fecha_inicio;',
              'SELECT "Columna fecha_inicio no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar fecha_fin si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'fecha_fin') > 0,
              'ALTER TABLE cursos DROP COLUMN fecha_fin;',
              'SELECT "Columna fecha_fin no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar estudiantes_inscritos si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'estudiantes_inscritos') > 0,
              'ALTER TABLE cursos DROP COLUMN estudiantes_inscritos;',
              'SELECT "Columna estudiantes_inscritos no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar calificacion_promedio si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'calificacion_promedio') > 0,
              'ALTER TABLE cursos DROP COLUMN calificacion_promedio;',
              'SELECT "Columna calificacion_promedio no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar total_calificaciones si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'total_calificaciones') > 0,
              'ALTER TABLE cursos DROP COLUMN total_calificaciones;',
              'SELECT "Columna total_calificaciones no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar requisitos si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'requisitos') > 0,
              'ALTER TABLE cursos DROP COLUMN requisitos;',
              'SELECT "Columna requisitos no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar objetivos si existe
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND COLUMN_NAME = 'objetivos') > 0,
              'ALTER TABLE cursos DROP COLUMN objetivos;',
              'SELECT "Columna objetivos no existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Paso 5: Agregar índices SOLO SI NO EXISTEN
-- Índice para estado
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND INDEX_NAME = 'idx_estado') = 0,
              'CREATE INDEX idx_estado ON cursos (estado);',
              'SELECT "Índice idx_estado ya existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Índice para nivel
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND INDEX_NAME = 'idx_nivel') = 0,
              'CREATE INDEX idx_nivel ON cursos (nivel);',
              'SELECT "Índice idx_nivel ya existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Índice compuesto
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
               WHERE TABLE_SCHEMA = 'tech_home' AND TABLE_NAME = 'cursos' AND INDEX_NAME = 'idx_docente_categoria') = 0,
              'CREATE INDEX idx_docente_categoria ON cursos (docente_id, categoria_id);',
              'SELECT "Índice idx_docente_categoria ya existe" as mensaje;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Paso 6: Actualizar comentario de la tabla
ALTER TABLE cursos COMMENT = 'Tabla de cursos optimizada para videos de YouTube';

-- Paso 7: Mostrar estructura final
SELECT 'Estructura FINAL de la tabla cursos:' as info;
DESCRIBE cursos;

SELECT 'Script ejecutado exitosamente - Optimización completada' as resultado;
