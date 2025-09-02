-- =====================================================
-- ASIGNACIÓN COMPLETA DE PERMISOS POR ROLES
-- =====================================================

-- Limpiar asignaciones existentes
DELETE FROM role_has_permissions;

-- =====================================================
-- ROL: INVITADO
-- Solo acceso a vistas públicas
-- =====================================================
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id 
FROM permissions p, roles r 
WHERE r.nombre = 'Invitado' AND p.name IN (
    'home.view',
    'catalogo.view',
    'profile.view',
    'profile.edit',
    'profile.password',
    'profile.avatar'
);

-- =====================================================
-- ROL: ESTUDIANTE
-- Acceso a cursos, materiales, perfil y funciones estudiantiles
-- =====================================================
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id 
FROM permissions p, roles r 
WHERE r.nombre = 'Estudiante' AND p.name IN (
    -- Vistas básicas
    'home.view',
    'catalogo.view',
    'estudiante.dashboard.view',
    
    -- Cursos - Solo ver e inscribirse
    'courses.view',
    'courses.enroll',
    'courses.unenroll',
    'enrollments.view',
    'enrollments.track',
    
    -- Materiales - Solo ver y descargar
    'materials.view',
    'materials.download',
    'books.view',
    'books.download',
    'books.purchase',
    
    -- Componentes - Solo ver y reservar
    'components.view',
    'components.reserve',
    
    -- Laboratorios - Solo ver, acceder y reservar
    'labs.view',
    'labs.access',
    'labs.reserve',
    
    -- Ver sus propias calificaciones
    'grades.view',
    
    -- Comunicación
    'messages.view',
    'messages.send',
    'notifications.view',
    
    -- Perfil personal
    'profile.view',
    'profile.edit',
    'profile.password',
    'profile.avatar'
);

-- =====================================================
-- ROL: DOCENTE
-- Acceso completo a gestión educativa, sin administración del sistema
-- =====================================================
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id 
FROM permissions p, roles r 
WHERE r.nombre = 'Docente' AND p.name IN (
    -- Vistas básicas
    'home.view',
    'catalogo.view',
    'docente.dashboard.view',
    
    -- Cursos - Gestión completa
    'courses.view',
    'courses.create',
    'courses.edit',
    'courses.delete',
    'enrollments.view',
    'enrollments.approve',
    'enrollments.reject',
    'enrollments.track',
    
    -- Calificaciones - Gestión completa
    'grades.view',
    'grades.create',
    'grades.edit',
    'grades.delete',
    
    -- Materiales - Gestión completa
    'materials.view',
    'materials.create',
    'materials.edit',
    'materials.delete',
    'materials.download',
    'materials.upload',
    'books.view',
    'books.create',
    'books.edit',
    'books.delete',
    'books.download',
    
    -- Componentes - Ver y gestionar reservas
    'components.view',
    'components.reserve',
    
    -- Laboratorios - Gestión completa
    'labs.view',
    'labs.create',
    'labs.edit',
    'labs.delete',
    'labs.access',
    'labs.reserve',
    
    -- Reportes de estudiantes y cursos
    'reports.view',
    'reports.export',
    'reports.students',
    'reports.courses',
    
    -- Comunicación
    'messages.view',
    'messages.send',
    'messages.delete',
    'notifications.view',
    'notifications.send',
    
    -- Perfil personal
    'profile.view',
    'profile.edit',
    'profile.password',
    'profile.avatar'
);

-- =====================================================
-- ROL: ADMINISTRADOR
-- Acceso completo a todo el sistema
-- =====================================================
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id 
FROM permissions p, roles r 
WHERE r.nombre = 'Administrador';

-- =====================================================
-- ROL: MIRONES
-- Solo lectura para auditores externos
-- =====================================================
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id 
FROM permissions p, roles r 
WHERE r.nombre = 'Mirones' AND p.name IN (
    -- Vistas básicas
    'home.view',
    'catalogo.view',
    
    -- Solo ver, sin modificar
    'courses.view',
    'materials.view',
    'books.view',
    'components.view',
    'labs.view',
    'enrollments.view',
    'grades.view',
    
    -- Reportes básicos
    'reports.view',
    'analytics.view',
    
    -- Perfil personal
    'profile.view',
    'profile.edit',
    'profile.password',
    'profile.avatar'
);
