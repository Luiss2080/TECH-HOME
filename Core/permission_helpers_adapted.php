<?php

/**
 * Helpers de permisos adaptados a la estructura existente de TECH-HOME
 */

use Core\Session;

/**
 * Verificar si el usuario actual tiene un permiso específico
 */
function can($permission)
{
    if (!Session::has('user_id')) {
        return false;
    }
    
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=tech_home', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $userId = Session::get('user_id');
        
        // Consulta adaptada a tu estructura existente
        $sql = "
            SELECT COUNT(*) as has_permission
            FROM usuarios u
            JOIN model_has_roles mhr ON u.id = mhr.model_id AND mhr.model_type = 'App\\\\Models\\\\User'
            JOIN role_has_permissions rhp ON mhr.role_id = rhp.role_id
            JOIN permissions p ON rhp.permission_id = p.id
            WHERE u.id = ? AND p.name = ?
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $permission]);
        
        return $stmt->fetchColumn() > 0;
        
    } catch (Exception $e) {
        error_log("Error verificando permiso: " . $e->getMessage());
        return false;
    }
}

/**
 * Verificar si el usuario actual tiene cualquiera de los permisos dados
 */
function canAny(array $permissions)
{
    foreach ($permissions as $permission) {
        if (can($permission)) {
            return true;
        }
    }
    return false;
}

/**
 * Verificar si el usuario actual tiene todos los permisos dados
 */
function canAll(array $permissions)
{
    foreach ($permissions as $permission) {
        if (!can($permission)) {
            return false;
        }
    }
    return true;
}

/**
 * Verificar si el usuario actual tiene un rol específico
 */
function hasRole($role)
{
    if (!Session::has('user_id')) {
        return false;
    }
    
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=tech_home', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $userId = Session::get('user_id');
        
        $sql = "
            SELECT COUNT(*) as has_role
            FROM usuarios u
            JOIN model_has_roles mhr ON u.id = mhr.model_id AND mhr.model_type = 'App\\\\Models\\\\User'
            JOIN roles r ON mhr.role_id = r.id
            WHERE u.id = ? AND r.nombre = ?
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $role]);
        
        return $stmt->fetchColumn() > 0;
        
    } catch (Exception $e) {
        error_log("Error verificando rol: " . $e->getMessage());
        return false;
    }
}

/**
 * Verificar si el usuario actual es administrador
 */
function isAdmin()
{
    return hasRole('Administrador');
}

/**
 * Verificar si el usuario actual es docente
 */
function isDocente()
{
    return hasRole('Docente');
}

/**
 * Verificar si el usuario actual es estudiante
 */
function isEstudiante()
{
    return hasRole('Estudiante');
}

/**
 * Verificar si el usuario actual es invitado
 */
function isInvitado()
{
    return hasRole('Invitado');
}

/**
 * Verificar si el usuario actual es miron (auditor)
 */
function isMiron()
{
    return hasRole('Mirones');
}

/**
 * Obtener el rol principal del usuario actual
 */
function getUserRole()
{
    if (!Session::has('user_id')) {
        return null;
    }
    
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=tech_home', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $userId = Session::get('user_id');
        
        $sql = "
            SELECT r.nombre
            FROM usuarios u
            JOIN model_has_roles mhr ON u.id = mhr.model_id AND mhr.model_type = 'App\\\\Models\\\\User'
            JOIN roles r ON mhr.role_id = r.id
            WHERE u.id = ?
            ORDER BY r.id ASC
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchColumn();
        
    } catch (Exception $e) {
        error_log("Error obteniendo rol: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtener todos los permisos del usuario actual
 */
function getUserPermissions()
{
    if (!Session::has('user_id')) {
        return [];
    }
    
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=tech_home', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $userId = Session::get('user_id');
        
        $sql = "
            SELECT DISTINCT p.name
            FROM usuarios u
            JOIN model_has_roles mhr ON u.id = mhr.model_id AND mhr.model_type = 'App\\\\Models\\\\User'
            JOIN role_has_permissions rhp ON mhr.role_id = rhp.role_id
            JOIN permissions p ON rhp.permission_id = p.id
            WHERE u.id = ?
            ORDER BY p.name
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
        
    } catch (Exception $e) {
        error_log("Error obteniendo permisos: " . $e->getMessage());
        return [];
    }
}

/**
 * Crear un botón condicionado por permisos
 */
function permissionButton($permission, $text, $url, $class = 'btn btn-primary', $attributes = '')
{
    if (can($permission)) {
        return "<a href='{$url}' class='{$class}' {$attributes}>{$text}</a>";
    }
    return '';
}

/**
 * Crear un enlace condicionado por permisos
 */
function permissionLink($permission, $text, $url, $class = '', $attributes = '')
{
    if (can($permission)) {
        $classAttr = $class ? "class='{$class}'" : '';
        return "<a href='{$url}' {$classAttr} {$attributes}>{$text}</a>";
    }
    return '';
}

/**
 * Mostrar contenido solo si tiene el permiso
 */
function ifCan($permission, $content)
{
    return can($permission) ? $content : '';
}

/**
 * Mostrar contenido solo si NO tiene el permiso
 */
function ifCannot($permission, $content)
{
    return !can($permission) ? $content : '';
}

/**
 * Mostrar contenido solo si tiene el rol
 */
function ifRole($role, $content)
{
    return hasRole($role) ? $content : '';
}

/**
 * Crear menú de navegación basado en permisos
 */
function navItem($permission, $text, $url, $icon = '', $class = 'nav-link')
{
    if (can($permission)) {
        $iconHtml = $icon ? "<i class='{$icon}'></i> " : '';
        return "<a href='{$url}' class='{$class}'>{$iconHtml}{$text}</a>";
    }
    return '';
}

/**
 * Verificar si el usuario puede acceder a una sección específica
 */
function canAccessSection($section)
{
    $sectionPermissions = [
        'admin' => ['admin.dashboard', 'admin.usuarios.ver', 'admin.reportes'],
        'docente' => ['docente.dashboard', 'cursos.crear', 'estudiantes.ver'],
        'estudiante' => ['estudiantes.dashboard', 'cursos.ver', 'inscripciones.ver'],
        'cursos' => ['cursos.ver', 'cursos.crear'],
        'materiales' => ['materiales.ver', 'materiales.crear'],
        'libros' => ['libros.ver', 'libros.crear'],
        'laboratorios' => ['laboratorios.ver', 'laboratorios.crear'],
        'componentes' => ['componentes.ver', 'componentes.crear']
    ];
    
    if (isset($sectionPermissions[$section])) {
        return canAny($sectionPermissions[$section]);
    }
    
    return false;
}

/**
 * Obtener el nivel de acceso del usuario (para UI condicional)
 */
function getAccessLevel()
{
    if (isAdmin()) return 'admin';
    if (isDocente()) return 'docente';
    if (isEstudiante()) return 'estudiante';
    if (isMiron()) return 'miron';
    if (isInvitado()) return 'invitado';
    return 'guest';
}

/**
 * Verificar si el usuario puede realizar acciones CRUD en una entidad
 */
function canCRUD($entity, $action)
{
    $permission = $entity . '.' . $action;
    return can($permission);
}

/**
 * Generar clase CSS basada en permisos (para mostrar/ocultar elementos)
 */
function permissionClass($permission, $showClass = '', $hideClass = 'd-none')
{
    return can($permission) ? $showClass : $hideClass;
}
?>
