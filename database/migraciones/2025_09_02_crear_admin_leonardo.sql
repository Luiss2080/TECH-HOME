-- ====================================================================
-- CREAR USUARIO ADMINISTRADOR: Leonardo Peña Ruiz
-- Fecha: 2025-09-02
-- Descripción: Crear usuario administrador con todos los permisos
-- ====================================================================

-- --------------------------------------------------------
-- 1. INSERTAR USUARIO LEONARDO
-- --------------------------------------------------------

INSERT INTO `usuarios` (
    `nombre`, 
    `apellido`, 
    `email`, 
    `password`, 
    `telefono`, 
    `fecha_nacimiento`, 
    `avatar`, 
    `estado`, 
    `bloqueado`,
    `fecha_creacion`, 
    `fecha_actualizacion`
) VALUES (
    'Leonardo',
    'Peña Ruiz',
    'leonardopenaruiz@gmail.com',
    '$2y$10$P.4Cv8mrBcCIQWmxTHC1YewnKJDEHmvrVUYjSpstH.niJehGJUJ7e', -- Hash de '14.Leo2015'
    NULL,
    '1990-01-01',
    NULL,
    1, -- Activo
    0, -- No bloqueado
    NOW(),
    NOW()
);

-- Obtener el ID del usuario recién creado
SET @admin_user_id = LAST_INSERT_ID();

-- --------------------------------------------------------
-- 2. VERIFICAR Y CREAR ROL ADMINISTRADOR SI NO EXISTE
-- --------------------------------------------------------

INSERT IGNORE INTO `roles` (`nombre`, `descripcion`, `estado`) 
VALUES ('administrador', 'Administrador del sistema con acceso completo', 1);

-- Obtener ID del rol administrador
SET @admin_role_id = (SELECT id FROM roles WHERE nombre = 'administrador' LIMIT 1);

-- --------------------------------------------------------
-- 3. ASIGNAR ROL ADMINISTRADOR AL USUARIO
-- --------------------------------------------------------

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) 
VALUES (@admin_role_id, 'App\\Models\\User', @admin_user_id);

-- --------------------------------------------------------
-- 4. CREAR PERMISOS BÁSICOS SI NO EXISTEN
-- --------------------------------------------------------

INSERT IGNORE INTO `permissions` (`name`, `guard_name`) VALUES
-- Permisos generales
('login', 'web'),
('logout', 'web'),
('dashboard.access', 'web'),

-- Permisos de administración
('admin.dashboard', 'web'),
('admin.usuarios.ver', 'web'),
('admin.usuarios.crear', 'web'),
('admin.usuarios.editar', 'web'),
('admin.usuarios.eliminar', 'web'),
('admin.usuarios.bloquear', 'web'),
('admin.usuarios.roles', 'web'),
('admin.usuarios.permisos', 'web'),

-- Permisos de roles y permisos
('admin.roles.ver', 'web'),
('admin.roles.crear', 'web'),
('admin.roles.editar', 'web'),
('admin.roles.eliminar', 'web'),
('admin.permisos.ver', 'web'),
('admin.permisos.crear', 'web'),
('admin.permisos.editar', 'web'),
('admin.permisos.eliminar', 'web'),
('admin.configuracion', 'web'),

-- Permisos de cursos
('admin.cursos.ver', 'web'),
('admin.cursos.crear', 'web'),
('admin.cursos.editar', 'web'),
('admin.cursos.eliminar', 'web'),
('cursos.ver', 'web'),
('cursos.crear', 'web'),
('cursos.editar', 'web'),
('cursos.eliminar', 'web'),
('cursos.inscribirse', 'web'),
('cursos.progreso', 'web'),

-- Permisos de suscripciones
('admin.suscripciones.ver', 'web'),
('admin.suscripciones.crear', 'web'),
('admin.suscripciones.editar', 'web'),
('admin.suscripciones.eliminar', 'web'),

-- Permisos de reportes
('admin.reportes.ver', 'web'),
('admin.reportes.acceso', 'web'),
('admin.reportes.export', 'web'),
('admin.reportes', 'web'),

-- Permisos de libros
('admin.libros', 'web'),
('admin.libros.crear', 'web'),
('admin.libros.editar', 'web'),
('admin.libros.eliminar', 'web'),
('libros.ver', 'web'),
('libros.descargar', 'web'),

-- Permisos de materiales
('admin.materiales', 'web'),
('admin.materiales.crear', 'web'),
('admin.materiales.editar', 'web'),
('admin.materiales.eliminar', 'web'),
('admin.materiales.ver', 'web'),
('materiales.ver', 'web'),

-- Permisos de laboratorios
('admin.laboratorios', 'web'),
('admin.laboratorios.crear', 'web'),
('admin.laboratorios.editar', 'web'),
('admin.laboratorios.eliminar', 'web'),
('admin.laboratorios.ver', 'web'),
('laboratorios.ver', 'web'),

-- Permisos de componentes
('componentes.ver', 'web'),
('componentes.crear', 'web'),
('componentes.editar', 'web'),
('componentes.eliminar', 'web'),

-- Permisos de ventas
('admin.ventas.ver', 'web'),
('admin.ventas.crear', 'web'),
('admin.ventas.editar', 'web'),
('admin.ventas.eliminar', 'web'),

-- Permisos API
('api.verify_session', 'web'),
('api.admin.reportes.estadisticas', 'web'),
('api.admin.suscripciones.estadisticas', 'web'),
('api.admin.cursos.estadisticas', 'web');

-- --------------------------------------------------------
-- 5. ASIGNAR TODOS LOS PERMISOS AL ROL ADMINISTRADOR
-- --------------------------------------------------------

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, @admin_role_id 
FROM permissions p 
WHERE p.id NOT IN (
    SELECT rp.permission_id 
    FROM role_has_permissions rp 
    WHERE rp.role_id = @admin_role_id
);

-- --------------------------------------------------------
-- 6. ASIGNAR PERMISOS DIRECTOS AL USUARIO (OPCIONAL)
-- --------------------------------------------------------

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`)
SELECT p.id, 'App\\Models\\User', @admin_user_id 
FROM permissions p 
WHERE p.name IN (
    'admin.dashboard',
    'admin.usuarios.ver',
    'admin.configuracion',
    'admin.reportes.ver',
    'admin.cursos.ver',
    'admin.suscripciones.ver'
)
AND p.id NOT IN (
    SELECT mp.permission_id 
    FROM model_has_permissions mp 
    WHERE mp.model_type = 'App\\Models\\User' 
    AND mp.model_id = @admin_user_id
);

-- --------------------------------------------------------
-- 7. VERIFICACIÓN FINAL
-- --------------------------------------------------------

SELECT 
    'Usuario creado exitosamente:' as Status,
    @admin_user_id as user_id,
    'Leonardo Peña Ruiz' as nombre_completo,
    'leonardopenaruiz@gmail.com' as email,
    'administrador' as rol;

SELECT 
    'Permisos asignados al rol administrador:' as Info,
    COUNT(*) as total_permisos
FROM role_has_permissions 
WHERE role_id = @admin_role_id;

SELECT 
    'Verificación de usuario y rol:' as Info,
    u.nombre,
    u.apellido,
    u.email,
    u.estado,
    r.nombre as rol
FROM usuarios u
JOIN model_has_roles mr ON u.id = mr.model_id
JOIN roles r ON mr.role_id = r.id
WHERE u.id = @admin_user_id;

-- --------------------------------------------------------
-- NOTAS IMPORTANTES:
-- --------------------------------------------------------

-- Password hash corresponde a: 14.Leo2015
-- El usuario se crea activo (estado = 1)
-- El usuario no está bloqueado (bloqueado = 0)
-- Se asignan TODOS los permisos disponibles al rol administrador
-- Se verifica que no haya duplicados en las asignaciones

-- MIGRACIÓN COMPLETADA EXITOSAMENTE