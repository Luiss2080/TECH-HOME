-- Insertar permisos básicos para el sistema
-- Este archivo debe ejecutarse después de la migración inicial

INSERT INTO permissions (name, descripcion, guard_name, created_at, updated_at) VALUES
-- Permisos de usuarios
('usuarios.ver', 'Ver lista de usuarios del sistema', 'web', NOW(), NOW()),
('usuarios.crear', 'Crear nuevos usuarios', 'web', NOW(), NOW()),
('usuarios.editar', 'Editar información de usuarios existentes', 'web', NOW(), NOW()),
('usuarios.eliminar', 'Eliminar usuarios del sistema', 'web', NOW(), NOW()),

-- Permisos de administración
('admin.dashboard', 'Acceder al panel de administración', 'web', NOW(), NOW()),
('admin.configuracion', 'Acceder a la configuración del sistema', 'web', NOW(), NOW()),
('admin.reportes', 'Ver y generar reportes del sistema', 'web', NOW(), NOW()),

-- Permisos de cursos
('cursos.ver', 'Ver lista de cursos disponibles', 'web', NOW(), NOW()),
('cursos.crear', 'Crear nuevos cursos', 'web', NOW(), NOW()),
('cursos.editar', 'Editar información de cursos', 'web', NOW(), NOW()),
('cursos.eliminar', 'Eliminar cursos del sistema', 'web', NOW(), NOW()),
('cursos.publicar', 'Publicar cursos en la plataforma', 'web', NOW(), NOW()),

-- Permisos de libros
('libros.ver', 'Ver biblioteca de libros', 'web', NOW(), NOW()),
('libros.crear', 'Agregar nuevos libros a la biblioteca', 'web', NOW(), NOW()),
('libros.editar', 'Editar información de libros', 'web', NOW(), NOW()),
('libros.eliminar', 'Eliminar libros de la biblioteca', 'web', NOW(), NOW()),

-- Permisos de componentes
('componentes.ver', 'Ver inventario de componentes', 'web', NOW(), NOW()),
('componentes.crear', 'Agregar componentes al inventario', 'web', NOW(), NOW()),
('componentes.editar', 'Editar información de componentes', 'web', NOW(), NOW()),
('componentes.eliminar', 'Eliminar componentes del inventario', 'web', NOW(), NOW()),

-- Permisos de ventas
('ventas.ver', 'Ver lista de ventas', 'web', NOW(), NOW()),
('ventas.crear', 'Procesar nuevas ventas', 'web', NOW(), NOW()),
('ventas.editar', 'Editar información de ventas', 'web', NOW(), NOW()),
('ventas.eliminar', 'Cancelar o eliminar ventas', 'web', NOW(), NOW()),

-- Permisos de configuración
('configuracion.roles', 'Gestionar roles del sistema', 'web', NOW(), NOW()),
('configuracion.permisos', 'Gestionar permisos del sistema', 'web', NOW(), NOW()),
('configuracion.sistema', 'Configurar ajustes del sistema', 'web', NOW(), NOW());

-- Asignar permisos básicos al rol administrador
-- Primero obtenemos el ID del rol administrador
SET @admin_role_id = (SELECT id FROM roles WHERE nombre = 'administrador' LIMIT 1);

-- Si existe el rol administrador, asignar todos los permisos
INSERT INTO role_has_permissions (role_id, permission_id)
SELECT @admin_role_id, id FROM permissions 
WHERE @admin_role_id IS NOT NULL;

-- Asignar permisos básicos al rol docente
SET @docente_role_id = (SELECT id FROM roles WHERE nombre = 'docente' LIMIT 1);

INSERT INTO role_has_permissions (role_id, permission_id)
SELECT @docente_role_id, id FROM permissions 
WHERE name IN (
    'cursos.ver', 'cursos.crear', 'cursos.editar', 'cursos.publicar',
    'libros.ver', 'libros.crear', 'libros.editar',
    'componentes.ver', 'componentes.crear', 'componentes.editar',
    'usuarios.ver'
) AND @docente_role_id IS NOT NULL;

-- Asignar permisos básicos al rol estudiante
SET @estudiante_role_id = (SELECT id FROM roles WHERE nombre = 'estudiante' LIMIT 1);

INSERT INTO role_has_permissions (role_id, permission_id)
SELECT @estudiante_role_id, id FROM permissions 
WHERE name IN (
    'cursos.ver',
    'libros.ver',
    'componentes.ver'
) AND @estudiante_role_id IS NOT NULL;
