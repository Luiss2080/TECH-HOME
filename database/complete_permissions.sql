-- ===================================================
-- SISTEMA COMPLETO DE PERMISOS GRANULARES TECH-HOME
-- ===================================================

-- Primero, limpiar permisos existentes que no sean necesarios
DELETE FROM role_has_permissions WHERE permission_id IN (
    SELECT id FROM permissions WHERE name LIKE 'login%' OR name LIKE 'logout%'
);

DELETE FROM permissions WHERE name IN ('login', 'logout');

-- ===================================================
-- 1. PERMISOS DE NAVEGACIÓN Y VISTAS BÁSICAS
-- ===================================================

INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at) VALUES
-- Home y vistas públicas
('home.view', 'web', NOW(), NOW()),
('catalogo.view', 'web', NOW(), NOW()),

-- Dashboard específicos
('admin.dashboard.view', 'web', NOW(), NOW()),
('docente.dashboard.view', 'web', NOW(), NOW()),
('estudiante.dashboard.view', 'web', NOW(), NOW()),

-- ===================================================
-- 2. PERMISOS DE USUARIOS Y GESTIÓN DE CUENTAS
-- ===================================================

-- Gestión completa de usuarios (Solo Admin)
('users.view', 'web', NOW(), NOW()),
('users.create', 'web', NOW(), NOW()),
('users.edit', 'web', NOW(), NOW()),
('users.delete', 'web', NOW(), NOW()),
('users.activate', 'web', NOW(), NOW()),
('users.deactivate', 'web', NOW(), NOW()),
('users.reset_password', 'web', NOW(), NOW()),
('users.impersonate', 'web', NOW(), NOW()),

-- Gestión de roles y permisos (Solo Admin)
('roles.view', 'web', NOW(), NOW()),
('roles.create', 'web', NOW(), NOW()),
('roles.edit', 'web', NOW(), NOW()),
('roles.delete', 'web', NOW(), NOW()),
('permissions.view', 'web', NOW(), NOW()),
('permissions.assign', 'web', NOW(), NOW()),

-- ===================================================
-- 3. PERMISOS DE CURSOS
-- ===================================================

-- Cursos - Vista y gestión
('courses.view', 'web', NOW(), NOW()),
('courses.create', 'web', NOW(), NOW()),
('courses.edit', 'web', NOW(), NOW()),
('courses.delete', 'web', NOW(), NOW()),
('courses.enroll', 'web', NOW(), NOW()),
('courses.unenroll', 'web', NOW(), NOW()),

-- Cursos - Inscripciones y seguimiento
('enrollments.view', 'web', NOW(), NOW()),
('enrollments.approve', 'web', NOW(), NOW()),
('enrollments.reject', 'web', NOW(), NOW()),
('enrollments.track', 'web', NOW(), NOW()),

-- Cursos - Calificaciones
('grades.view', 'web', NOW(), NOW()),
('grades.create', 'web', NOW(), NOW()),
('grades.edit', 'web', NOW(), NOW()),
('grades.delete', 'web', NOW(), NOW()),

-- ===================================================
-- 4. PERMISOS DE MATERIALES Y RECURSOS
-- ===================================================

-- Materiales
('materials.view', 'web', NOW(), NOW()),
('materials.create', 'web', NOW(), NOW()),
('materials.edit', 'web', NOW(), NOW()),
('materials.delete', 'web', NOW(), NOW()),
('materials.download', 'web', NOW(), NOW()),
('materials.upload', 'web', NOW(), NOW()),

-- Libros
('books.view', 'web', NOW(), NOW()),
('books.create', 'web', NOW(), NOW()),
('books.edit', 'web', NOW(), NOW()),
('books.delete', 'web', NOW(), NOW()),
('books.download', 'web', NOW(), NOW()),
('books.purchase', 'web', NOW(), NOW()),

-- ===================================================
-- 5. PERMISOS DE COMPONENTES Y LABORATORIOS
-- ===================================================

-- Componentes
('components.view', 'web', NOW(), NOW()),
('components.create', 'web', NOW(), NOW()),
('components.edit', 'web', NOW(), NOW()),
('components.delete', 'web', NOW(), NOW()),
('components.reserve', 'web', NOW(), NOW()),

-- Laboratorios
('labs.view', 'web', NOW(), NOW()),
('labs.create', 'web', NOW(), NOW()),
('labs.edit', 'web', NOW(), NOW()),
('labs.delete', 'web', NOW(), NOW()),
('labs.access', 'web', NOW(), NOW()),
('labs.reserve', 'web', NOW(), NOW()),

-- ===================================================
-- 6. PERMISOS DE VENTAS Y COMERCIO
-- ===================================================

-- Ventas
('sales.view', 'web', NOW(), NOW()),
('sales.create', 'web', NOW(), NOW()),
('sales.edit', 'web', NOW(), NOW()),
('sales.delete', 'web', NOW(), NOW()),
('sales.process', 'web', NOW(), NOW()),
('sales.refund', 'web', NOW(), NOW()),

-- Inventario
('inventory.view', 'web', NOW(), NOW()),
('inventory.manage', 'web', NOW(), NOW()),
('inventory.audit', 'web', NOW(), NOW()),

-- ===================================================
-- 7. PERMISOS DE REPORTES Y ESTADÍSTICAS
-- ===================================================

-- Reportes generales
('reports.view', 'web', NOW(), NOW()),
('reports.export', 'web', NOW(), NOW()),
('reports.students', 'web', NOW(), NOW()),
('reports.courses', 'web', NOW(), NOW()),
('reports.sales', 'web', NOW(), NOW()),
('reports.financial', 'web', NOW(), NOW()),

-- Analytics
('analytics.view', 'web', NOW(), NOW()),
('analytics.advanced', 'web', NOW(), NOW()),

-- ===================================================
-- 8. PERMISOS DE SEGURIDAD Y MONITOREO
-- ===================================================

-- Seguridad
('security.dashboard', 'web', NOW(), NOW()),
('security.logs', 'web', NOW(), NOW()),
('security.audit', 'web', NOW(), NOW()),
('security.monitor', 'web', NOW(), NOW()),
('security.export', 'web', NOW(), NOW()),

-- Sistema
('system.settings', 'web', NOW(), NOW()),
('system.maintenance', 'web', NOW(), NOW()),
('system.backup', 'web', NOW(), NOW()),

-- ===================================================
-- 9. PERMISOS DE COMUNICACIÓN
-- ===================================================

-- Mensajería
('messages.view', 'web', NOW(), NOW()),
('messages.send', 'web', NOW(), NOW()),
('messages.delete', 'web', NOW(), NOW()),

-- Notificaciones
('notifications.view', 'web', NOW(), NOW()),
('notifications.send', 'web', NOW(), NOW()),
('notifications.manage', 'web', NOW(), NOW()),

-- ===================================================
-- 10. PERMISOS DE PERFIL Y CUENTA PERSONAL
-- ===================================================

-- Perfil personal
('profile.view', 'web', NOW(), NOW()),
('profile.edit', 'web', NOW(), NOW()),
('profile.password', 'web', NOW(), NOW()),
('profile.avatar', 'web', NOW(), NOW());
