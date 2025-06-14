<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - LOGOUT ULTRA SIMPLE CON DEBUG
 * Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
 * ============================================================================
 */

// Evitar cualquier output antes de headers
ob_start();

// Función de debug
function debugLog($mensaje) {
    error_log("[LOGOUT DEBUG] " . $mensaje);
    // También escribir a un archivo específico
    file_put_contents('logs/logout_debug.log', date('Y-m-d H:i:s') . " - " . $mensaje . PHP_EOL, FILE_APPEND | LOCK_EX);
}

try {
    debugLog("=== INICIO LOGOUT ===");
    
    // Verificar si hay sesión iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        debugLog("Sesión iniciada");
    } else {
        debugLog("Sesión ya estaba activa");
    }

    // Obtener datos del usuario para logs
    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'no-definido';
    $session_id = session_id();
    
    debugLog("Usuario ID: $usuario_id, Session ID: $session_id");

    // Limpiar variables de sesión
    $_SESSION = array();
    debugLog("Variables de sesión limpiadas");

    // Eliminar cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        
        // Intentar eliminar la cookie de varias formas
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        setcookie(session_name(), '', time() - 42000, '/');
        setcookie('PHPSESSID', '', time() - 42000, '/');
        
        debugLog("Cookies eliminadas");
    }

    // Destruir sesión
    if (session_destroy()) {
        debugLog("Sesión destruida exitosamente");
    } else {
        debugLog("ERROR: No se pudo destruir la sesión");
    }

    // Limpiar buffer
    ob_clean();
    
    // Verificar si los headers ya fueron enviados
    if (headers_sent($file, $line)) {
        debugLog("ERROR: Headers ya enviados en $file línea $line");
        
        // Usar redirección por JavaScript como fallback
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Redirigiendo...</title>
        </head>
        <body>
            <script>
                console.log("Redirigiendo via JavaScript...");
                window.location.href = "login.php?logout=1&t=' . time() . '";
            </script>
            <noscript>
                <meta http-equiv="refresh" content="0;url=login.php?logout=1&t=' . time() . '">
                <p>Redirigiendo... <a href="login.php?logout=1&t=' . time() . '">Clic aquí si no se redirige automáticamente</a></p>
            </noscript>
        </body>
        </html>';
    } else {
        // Headers normales
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        $redirect_url = "login.php?logout=1&t=" . time();
        debugLog("Redirigiendo a: $redirect_url");
        
        header("Location: $redirect_url");
    }
    
    debugLog("=== FIN LOGOUT ===");
    
} catch (Exception $e) {
    debugLog("ERROR EXCEPCIÓN: " . $e->getMessage());
    
    // En caso de error, redirección de emergencia
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Error - Redirigiendo...</title>
    </head>
    <body>
        <script>
            console.log("Error en logout, redirigiendo...");
            window.location.href = "login.php?error=logout&t=' . time() . '";
        </script>
        <noscript>
            <meta http-equiv="refresh" content="2;url=login.php?error=logout&t=' . time() . '">
            <p>Error en logout. Redirigiendo... <a href="login.php?error=logout&t=' . time() . '">Clic aquí</a></p>
        </noscript>
    </body>
    </html>';
}

exit();
?>