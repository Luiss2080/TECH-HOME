-- Script para eliminar el rol "Mirones" de la base de datos
-- Ejecutar este script en la base de datos para limpiar el rol no deseado

USE tech_home;

-- 1. Eliminar las asignaciones del rol Mirones de model_has_roles
DELETE FROM model_has_roles 
WHERE role_id = (SELECT id FROM roles WHERE nombre = 'Mirones');

-- 2. Eliminar los permisos asociados al rol Mirones de role_has_permissions
DELETE FROM role_has_permissions 
WHERE role_id = (SELECT id FROM roles WHERE nombre = 'Mirones');

-- 3. Eliminar el rol Mirones de la tabla roles
DELETE FROM roles WHERE nombre = 'Mirones';

-- Verificar que el rol fue eliminado
SELECT * FROM roles WHERE nombre = 'Mirones';

-- Script completado
SELECT 'Rol Mirones eliminado exitosamente' as resultado;
