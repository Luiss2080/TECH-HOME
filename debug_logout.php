<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - DEBUG ESPECÍFICO DEL LOGOUT
 * Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
 * ============================================================================
 */

session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Logout - Tech Home</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; font-size: 12px; }
        .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Debug Específico del Logout</h1>
        
        <!-- Estado Actual de Sesión -->
        <div class="section">
            <h2>📋 Estado Actual de Sesión</h2>
            <div class="code-block">
                <strong>Session Status:</strong> <?php echo session_status(); ?><br>
                <strong>Session ID:</strong> <?php echo session_id(); ?><br>
                <strong>Session Name:</strong> <?php echo session_name(); ?><br>
                <strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
                
                <br><strong>Variables de Sesión:</strong><br>
                <?php if (!empty($_SESSION)): ?>
                    <pre><?php print_r($_SESSION); ?></pre>
                <?php else: ?>
                    <span class="status-warning">No hay variables de sesión activas</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Test de Logout -->
        <div class="section">
            <h2>🧪 Tests de Logout</h2>
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <p><span class="status-ok">✅ Usuario logueado detectado</span></p>
                <div class="code-block">
                    <strong>Usuario ID:</strong> <?php echo $_SESSION['usuario_id']; ?><br>
                    <strong>Nombre:</strong> <?php echo $_SESSION['usuario_nombre'] ?? 'N/A'; ?><br>
                    <strong>Email:</strong> <?php echo $_SESSION['usuario_email'] ?? 'N/A'; ?><br>
                    <strong>Rol:</strong> <?php echo $_SESSION['usuario_rol'] ?? 'N/A'; ?><br>
                </div>
                
                <div style="margin: 15px 0;">
                    <a href="logout.php" class="btn btn-danger">🚪 Test Logout Normal</a>
                    <a href="?test_logout=simple" class="btn btn-warning">🧪 Test Logout Simple</a>
                    <a href="?test_logout=force" class="btn btn-danger">💥 Test Logout Forzado</a>
                </div>
            <?php else: ?>
                <p><span class="status-warning">⚠️ No hay usuario logueado</span></p>
                <a href="login.php" class="btn btn-primary">🔑 Ir al Login</a>
            <?php endif; ?>
        </div>

        <!-- Procesamiento de Tests -->
        <?php
        if (isset($_GET['test_logout'])) {
            echo '<div class="section">';
            echo '<h2>🔬 Resultado del Test</h2>';
            
            $tipo = $_GET['test_logout'];
            
            switch ($tipo) {
                case 'simple':
                    echo '<div class="code-block">';
                    echo "Ejecutando logout simple...<br>";
                    
                    $usuario_id = $_SESSION['usuario_id'] ?? 'unknown';
                    $_SESSION = array();
                    
                    echo "Variables limpiadas...<br>";
                    
                    if (session_destroy()) {
                        echo "<span class='status-ok'>✅ Sesión destruida</span><br>";
                    } else {
                        echo "<span class='status-error'>❌ Error al destruir sesión</span><br>";
                    }
                    
                    echo "Resultado: Logout completado para usuario $usuario_id<br>";
                    echo '<a href="debug_logout.php" class="btn btn-primary">🔄 Recargar para verificar</a>';
                    echo '</div>';
                    break;
                    
                case 'force':
                    echo '<div class="code-block">';
                    echo "Ejecutando logout forzado...<br>";
                    
                    // Método más agresivo
                    $_SESSION = array();
                    
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                        setcookie(session_name(), '', time() - 42000, '/');
                        echo "Cookies eliminadas...<br>";
                    }
                    
                    session_destroy();
                    session_start(); // Reiniciar sesión limpia
                    
                    echo "<span class='status-ok'>✅ Logout forzado completado</span><br>";
                    echo '<a href="debug_logout.php" class="btn btn-primary">🔄 Recargar para verificar</a>';
                    echo '</div>';
                    break;
            }
            echo '</div>';
        }
        ?>

        <!-- Logs de Logout -->
        <div class="section">
            <h2>📜 Logs de Logout</h2>
            <?php
            $log_files = [
                'logs/logout_debug.log' => 'Logs específicos de logout',
                'logs/app.log' => 'Logs generales de aplicación',
                'logs/sesiones.log' => 'Logs de sesiones'
            ];

            foreach ($log_files as $log_file => $descripcion) {
                echo "<h3>$descripcion</h3>";
                if (file_exists($log_file)) {
                    $lines = file($log_file);
                    $recent_lines = array_slice($lines, -15); // Últimas 15 líneas
                    
                    echo "<div class='code-block'>";
                    if (!empty($recent_lines)) {
                        foreach ($recent_lines as $line) {
                            echo htmlspecialchars(trim($line)) . "<br>";
                        }
                    } else {
                        echo "<span class='status-warning'>Archivo vacío</span>";
                    }
                    echo "</div>";
                } else {
                    echo "<div class='code-block'><span class='status-error'>Archivo $log_file no encontrado</span></div>";
                }
            }
            ?>
        </div>

        <!-- Información del Sistema -->
        <div class="section">
            <h2>💻 Información del Sistema</h2>
            <div class="code-block">
                <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?><br>
                <strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'No disponible'; ?><br>
                <strong>Session Save Path:</strong> <?php echo session_save_path(); ?><br>
                <strong>Session Cookie Params:</strong><br>
                <pre><?php print_r(session_get_cookie_params()); ?></pre>
                
                <strong>Headers Sent:</strong> <?php echo headers_sent() ? 'Sí' : 'No'; ?><br>
                <strong>Current URL:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?><br>
                <strong>Referer:</strong> <?php echo $_SERVER['HTTP_REFERER'] ?? 'N/A'; ?><br>
            </div>
        </div>

        <!-- Acciones Adicionales -->
        <div class="section">
            <h2>🛠️ Acciones Adicionales</h2>
            <a href="verificar_sistema.php" class="btn btn-primary">🔍 Verificador General</a>
            <a href="login.php" class="btn btn-success">🔑 Ir al Login</a>
            <a href="?clear_logs=1" class="btn btn-warning">🧹 Limpiar Logs</a>
            
            <?php
            if (isset($_GET['clear_logs'])) {
                $log_files = ['logs/logout_debug.log', 'logs/app.log', 'logs/sesiones.log'];
                $cleared = 0;
                
                foreach ($log_files as $log_file) {
                    if (file_exists($log_file)) {
                        file_put_contents($log_file, '');
                        $cleared++;
                    }
                }
                
                echo "<div style='margin-top: 10px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;'>";
                echo "<span class='status-ok'>✅ $cleared archivos de log limpiados</span>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script>
        // Auto-refresh cada 30 segundos si hay usuario logueado
        <?php if (isset($_SESSION['usuario_id'])): ?>
        setTimeout(function() {
            if (confirm('¿Refrescar página para ver estado actualizado?')) {
                window.location.reload();
            }
        }, 30000);
        <?php endif; ?>

        // Log en consola
        console.log('Debug Logout Page Loaded');
        console.log('Current session:', <?php echo json_encode($_SESSION); ?>);
    </script>
</body>
</html>