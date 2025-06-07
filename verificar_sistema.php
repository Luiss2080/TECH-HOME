<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - VERIFICADOR DEL SISTEMA
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
    <title>Verificador del Sistema - Tech Home</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; }
        .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Verificador del Sistema Tech Home</h1>
        
        <!-- Información de Sesión -->
        <div class="section">
            <h2>📋 Estado de Sesión</h2>
            <div class="code-block">
                <strong>Session Status:</strong> <?php echo session_status(); ?> 
                <?php 
                $status_text = [
                    PHP_SESSION_DISABLED => 'PHP_SESSION_DISABLED (0)',
                    PHP_SESSION_NONE => 'PHP_SESSION_NONE (1)',
                    PHP_SESSION_ACTIVE => 'PHP_SESSION_ACTIVE (2)'
                ];
                echo "(" . ($status_text[session_status()] ?? 'Unknown') . ")";
                ?><br>
                
                <strong>Session ID:</strong> <?php echo session_id() ?: 'No session ID'; ?><br>
                <strong>Session Name:</strong> <?php echo session_name(); ?><br>
                <strong>Session Save Path:</strong> <?php echo session_save_path(); ?><br>
                
                <strong>Variables de Sesión:</strong><br>
                <?php if (!empty($_SESSION)): ?>
                    <?php foreach ($_SESSION as $key => $value): ?>
                        • <?php echo htmlspecialchars($key); ?>: <?php echo htmlspecialchars(is_array($value) ? print_r($value, true) : $value); ?><br>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="status-warning">No hay variables de sesión</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Verificación de Archivos -->
        <div class="section">
            <h2>📁 Verificación de Archivos</h2>
            <?php
            $archivos_requeridos = [
                'config/database.php' => 'Configuración de base de datos',
                'config/sesion.php' => 'Manejo de sesiones',
                'autoload.php' => 'Autoloader principal',
                'modelos/UsuarioModelo.php' => 'Modelo de usuario',
                'controladores/UsuarioControlador.php' => 'Controlador de usuario',
                'login.php' => 'Página de login',
                'logout.php' => 'Logout del sistema',
                '.htaccess' => 'Configuración de Apache'
            ];

            foreach ($archivos_requeridos as $archivo => $descripcion) {
                $existe = file_exists($archivo);
                $legible = $existe ? is_readable($archivo) : false;
                
                echo "<div>";
                echo "<strong>$archivo</strong> ($descripcion): ";
                
                if ($existe && $legible) {
                    echo "<span class='status-ok'>✅ Disponible</span>";
                } elseif ($existe && !$legible) {
                    echo "<span class='status-error'>❌ Existe pero no es legible</span>";
                } else {
                    echo "<span class='status-error'>❌ No encontrado</span>";
                }
                
                echo "</div>";
            }
            ?>
        </div>

        <!-- Verificación de Base de Datos -->
        <div class="section">
            <h2>🗄️ Verificación de Base de Datos</h2>
            <?php
            try {
                if (file_exists('config/database.php')) {
                    require_once 'config/database.php';
                    $conexion = Database::getConnection();
                    
                    if ($conexion) {
                        echo "<span class='status-ok'>✅ Conexión a base de datos exitosa</span><br>";
                        
                        // Verificar tablas principales
                        $tablas = ['usuarios', 'roles', 'cursos', 'categorias'];
                        foreach ($tablas as $tabla) {
                            $stmt = $conexion->query("SHOW TABLES LIKE '$tabla'");
                            if ($stmt->rowCount() > 0) {
                                echo "<span class='status-ok'>✅ Tabla '$tabla' existe</span><br>";
                            } else {
                                echo "<span class='status-error'>❌ Tabla '$tabla' no existe</span><br>";
                            }
                        }
                    } else {
                        echo "<span class='status-error'>❌ No se pudo conectar a la base de datos</span>";
                    }
                } else {
                    echo "<span class='status-error'>❌ Archivo database.php no encontrado</span>";
                }
            } catch (Exception $e) {
                echo "<span class='status-error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</span>";
            }
            ?>
        </div>

        <!-- Información del Servidor -->
        <div class="section">
            <h2>🖥️ Información del Servidor</h2>
            <div class="code-block">
                <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?><br>
                <strong>Apache Version:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'No disponible'; ?><br>
                <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'No disponible'; ?><br>
                <strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'No disponible'; ?><br>
                <strong>HTTP Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'No disponible'; ?><br>
            </div>
        </div>

        <!-- Estado de Usuario -->
        <div class="section">
            <h2>👤 Estado de Usuario</h2>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <span class="status-ok">✅ Usuario autenticado</span><br>
                <div class="code-block">
                    <strong>ID:</strong> <?php echo htmlspecialchars($_SESSION['usuario_id']); ?><br>
                    <strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'No definido'); ?><br>
                    <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email'] ?? 'No definido'); ?><br>
                    <strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION['usuario_rol'] ?? 'No definido'); ?><br>
                    <strong>Login Time:</strong> <?php echo isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : 'No definido'; ?><br>
                </div>
            <?php else: ?>
                <span class="status-warning">⚠️ Usuario no autenticado</span>
            <?php endif; ?>
        </div>

        <!-- Acciones de Prueba -->
        <div class="section">
            <h2>🧪 Acciones de Prueba</h2>
            <a href="login.php" class="btn btn-primary">🔑 Ir al Login</a>
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
                <a href="vistas/dashboard/admin.php" class="btn btn-success">📊 Dashboard Admin</a>
                <a href="vistas/dashboard/docente.php" class="btn btn-success">📊 Dashboard Docente</a>
                <a href="vistas/dashboard/estudiante.php" class="btn btn-success">📊 Dashboard Estudiante</a>
            <?php endif; ?>
            
            <a href="?clear_session=1" class="btn btn-danger">🧹 Limpiar Sesión</a>
        </div>

        <?php
        // Acción para limpiar sesión
        if (isset($_GET['clear_session'])) {
            $_SESSION = array();
            session_destroy();
            echo "<div class='section'><span class='status-ok'>✅ Sesión limpiada. <a href='verificar_sistema.php'>Recargar página</a></span></div>";
        }
        ?>

        <!-- Logs Recientes -->
        <div class="section">
            <h2>📜 Logs Recientes</h2>
            <?php
            $log_files = ['logs/app.log', 'logs/sesiones.log'];
            foreach ($log_files as $log_file) {
                if (file_exists($log_file)) {
                    echo "<h3>" . basename($log_file) . "</h3>";
                    $lines = file($log_file);
                    $recent_lines = array_slice($lines, -10); // Últimas 10 líneas
                    
                    echo "<div class='code-block'>";
                    foreach ($recent_lines as $line) {
                        echo htmlspecialchars(trim($line)) . "<br>";
                    }
                    echo "</div>";
                } else {
                    echo "<p>Archivo $log_file no encontrado</p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>