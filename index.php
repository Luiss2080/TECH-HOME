<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - PÁGINA DE INICIO
 * Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
 * ============================================================================
 */

// Iniciar sesión
session_start();

// Mostrar errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si el usuario está logueado
if (isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id'])) {
    // Usuario logueado - redirigir según rol
    $rol = strtolower($_SESSION['usuario_rol'] ?? 'Estudiante');
    
    switch ($rol) {
        case 'administrador':
            header("Location: vistas/dashboard/admin.php");
            exit();
        case 'docente':
            header("Location: vistas/dashboard/docente.php");
            exit();
        case 'estudiante':
        default:
            header("Location: vistas/dashboard/estudiante.php");
            exit();
    }
} else {
    // Usuario no logueado - redirigir al login
    header("Location: login.php");
    exit();
}
?>