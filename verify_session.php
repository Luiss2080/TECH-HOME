<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - VERIFICADOR DE SESIÓN
 * Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
 * ============================================================================
 * 
 * Este archivo verifica el estado de la sesión del usuario
 * Utilizado por el header component para verificación automática
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar headers para JSON y evitar cache
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Verificar si es una petición AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

try {
    // Verificar si el usuario está autenticado
    $authenticated = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    
    // Información adicional de la sesión
    $sessionInfo = [
        'authenticated' => $authenticated,
        'session_id' => session_id(),
        'timestamp' => time(),
        'last_activity' => $_SESSION['last_activity'] ?? null
    ];
    
    // Si está autenticado, agregar información del usuario
    if ($authenticated) {
        $sessionInfo['user'] = [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nombre' => $_SESSION['usuario_nombre'] ?? '',
            'apellido' => $_SESSION['usuario_apellido'] ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'rol' => $_SESSION['usuario_rol'] ?? ''
        ];
        
        // Actualizar última actividad
        $_SESSION['last_activity'] = time();
    }
    
    // Log para debug (opcional)
    if (function_exists('logDebug')) {
        logDebug("Verificación de sesión - Autenticado: " . ($authenticated ? 'Sí' : 'No'));
    }
    
    // Enviar respuesta JSON
    echo json_encode($sessionInfo);
    
} catch (Exception $e) {
    // En caso de error, enviar respuesta de error
    http_response_code(500);
    echo json_encode([
        'authenticated' => false,
        'error' => 'Error verificando sesión',
        'timestamp' => time()
    ]);
    
    // Log del error
    error_log("[VERIFY SESSION] Error: " . $e->getMessage());
}

// Terminar ejecución
exit();
?>