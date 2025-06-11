<?php
/**
 * Sistema de Sesiones y Autenticación - Tech Home Bolivia
 * Manejo completo de sesiones, autenticación y seguridad
 */

// Prevenir acceso directo
if (!defined('TECH_HOME_INIT')) {
    die('Acceso directo no permitido');
}

// ============================================================================
// CONFIGURACIÓN INICIAL DE SESIONES
// ============================================================================

/**
 * Configurar parámetros de sesión antes de iniciarla
 */
function configurarSesion() {
    // Solo configurar si la sesión no está activa
    if (session_status() === PHP_SESSION_NONE) {
        // Configuraciones de seguridad
        ini_set('session.cookie_httponly', SESSION_COOKIE_HTTPONLY);
        ini_set('session.cookie_secure', SESSION_COOKIE_SECURE);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', SESSION_COOKIE_SAMESITE);
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        ini_set('session.cookie_lifetime', SESSION_LIFETIME);
        
        // Nombre personalizado de sesión
        session_name(SESSION_NAME);
        
        // Regenerar ID de sesión periódicamente
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 100);
    }
}

/**
 * Iniciar sesión de forma segura
 */
function iniciarSesion() {
    configurarSesion();
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        
        // Regenerar ID de sesión si es una nueva sesión o cada 30 minutos
        if (!isset($_SESSION['iniciada']) || (time() - ($_SESSION['ultima_regeneracion'] ?? 0)) > 1800) {
            session_regenerate_id(true);
            $_SESSION['ultima_regeneracion'] = time();
        }
        
        $_SESSION['iniciada'] = true;
    }
}

// ============================================================================
// FUNCIONES DE AUTENTICACIÓN
// ============================================================================

/**
 * Verificar si el usuario está autenticado
 */
function estaAutenticado() {
    return isset($_SESSION['usuario_id']) && 
           isset($_SESSION['email']) && 
           isset($_SESSION['rol']);
}

/**
 * Verificar si el usuario tiene un rol específico
 */
function tieneRol($rol) {
    return estaAutenticado() && $_SESSION['rol'] === $rol;
}

/**
 * Verificar si el usuario es administrador
 */
function esAdministrador() {
    return tieneRol('Administrador');
}

/**
 * Verificar si el usuario es invitado
 */
function esInvitado() {
    return tieneRol('Invitado');
}

/**
 * Obtener información del usuario actual
 */
function usuarioActual() {
    if (!estaAutenticado()) return null;
    
    return [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['nombre'] ?? '',
        'apellido' => $_SESSION['apellido'] ?? '',
        'email' => $_SESSION['email'],
        'rol' => $_SESSION['rol'],
        'avatar' => $_SESSION['avatar'] ?? null
    ];
}

// ============================================================================
// GESTIÓN DE SESIONES EN BASE DE DATOS
// ============================================================================

/**
 * Registrar sesión activa en la base de datos
 */
function registrarSesionActiva($usuario_id) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        // Obtener información del navegador y sistema
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ipAddress = obtenerIPReal();
        $navegador = detectarNavegador($userAgent);
        $sistemaOperativo = detectarSistemaOperativo($userAgent);
        $dispositivo = detectarDispositivo($userAgent);
        
        // Verificar si ya existe una sesión activa para este usuario
        $restriccionSesion = obtenerConfiguracion('session_restriction', true);
        
        if ($restriccionSesion) {
            // Desactivar otras sesiones del mismo usuario
            $stmt = $pdo->prepare("UPDATE sesiones_activas SET activa = 0 WHERE usuario_id = ? AND activa = 1");
            $stmt->execute([$usuario_id]);
        }
        
        // Insertar nueva sesión
        $stmt = $pdo->prepare("
            INSERT INTO sesiones_activas 
            (usuario_id, session_id, dispositivo, ip_address, user_agent, navegador, sistema_operativo) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $usuario_id,
            session_id(),
            $dispositivo,
            $ipAddress,
            $userAgent,
            $navegador,
            $sistemaOperativo
        ]);
        
        // Guardar ID de sesión en la sesión PHP
        $_SESSION['sesion_db_id'] = $pdo->lastInsertId();
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error registrando sesión activa: " . $e->getMessage());
        return false;
    }
}

/**
 * Actualizar actividad de la sesión
 */
function actualizarActividadSesion() {
    if (!isset($_SESSION['sesion_db_id'])) return false;
    
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("
            UPDATE sesiones_activas 
            SET fecha_actividad = CURRENT_TIMESTAMP 
            WHERE id = ? AND activa = 1
        ");
        
        return $stmt->execute([$_SESSION['sesion_db_id']]);
        
    } catch (Exception $e) {
        error_log("Error actualizando actividad de sesión: " . $e->getMessage());
        return false;
    }
}

/**
 * Cerrar sesión activa en la base de datos
 */
function cerrarSesionActiva($usuario_id = null) {
    if (!$usuario_id && isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
    }
    
    if (!$usuario_id) return false;
    
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        if (isset($_SESSION['sesion_db_id'])) {
            // Cerrar sesión específica
            $stmt = $pdo->prepare("UPDATE sesiones_activas SET activa = 0 WHERE id = ?");
            $stmt->execute([$_SESSION['sesion_db_id']]);
        } else {
            // Cerrar todas las sesiones del usuario
            $stmt = $pdo->prepare("UPDATE sesiones_activas SET activa = 0 WHERE usuario_id = ?");
            $stmt->execute([$usuario_id]);
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error cerrando sesión activa: " . $e->getMessage());
        return false;
    }
}

// ============================================================================
// GESTIÓN DE ACCESO PARA INVITADOS
// ============================================================================

/**
 * Verificar acceso de usuario invitado
 */
function verificarAccesoInvitado($usuario_id) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("
            SELECT ai.*, DATEDIFF(ai.fecha_vencimiento, CURRENT_DATE) as dias_restantes_calc
            FROM acceso_invitados ai 
            WHERE ai.usuario_id = ? AND ai.acceso_bloqueado = 0
        ");
        $stmt->execute([$usuario_id]);
        $acceso = $stmt->fetch();
        
        if (!$acceso) {
            // No tiene registro de acceso de invitado, crear uno
            $diasAcceso = obtenerConfiguracion('invitado_dias_acceso', INVITADO_DIAS_ACCESO);
            
            $stmt = $pdo->prepare("
                INSERT INTO acceso_invitados (usuario_id, fecha_inicio, fecha_vencimiento, dias_restantes)
                VALUES (?, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL ? DAY), ?)
            ");
            $stmt->execute([$usuario_id, $diasAcceso, $diasAcceso]);
            
            return [
                'acceso_valido' => true,
                'dias_restantes' => $diasAcceso,
                'mensaje' => "Bienvenido! Tienes $diasAcceso días de acceso gratuito."
            ];
        }
        
        // Verificar si el acceso ha vencido
        if ($acceso['dias_restantes_calc'] < 0) {
            // Bloquear acceso
            $stmt = $pdo->prepare("UPDATE acceso_invitados SET acceso_bloqueado = 1 WHERE usuario_id = ?");
            $stmt->execute([$usuario_id]);
            
            return [
                'acceso_valido' => false,
                'dias_restantes' => 0,
                'mensaje' => 'Tu período de acceso como invitado ha vencido. Contacta con el administrador.'
            ];
        }
        
        // Actualizar días restantes
        $stmt = $pdo->prepare("
            UPDATE acceso_invitados 
            SET dias_restantes = DATEDIFF(fecha_vencimiento, CURRENT_DATE)
            WHERE usuario_id = ?
        ");
        $stmt->execute([$usuario_id]);
        
        return [
            'acceso_valido' => true,
            'dias_restantes' => $acceso['dias_restantes_calc'],
            'mensaje' => $acceso['dias_restantes_calc'] <= 1 ? 
                'Este es tu último día de acceso gratuito.' : 
                "Te quedan {$acceso['dias_restantes_calc']} días de acceso gratuito."
        ];
        
    } catch (Exception $e) {
        error_log("Error verificando acceso de invitado: " . $e->getMessage());
        return [
            'acceso_valido' => false,
            'dias_restantes' => 0,
            'mensaje' => 'Error verificando el acceso.'
        ];
    }
}

// ============================================================================
// CONTROL DE INTENTOS DE LOGIN
// ============================================================================

/**
 * Registrar intento de login
 */
function registrarIntentoLogin($email, $exito = false) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("
            INSERT INTO intentos_login (email, ip_address, user_agent, exito)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $email,
            obtenerIPReal(),
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $exito ? 1 : 0
        ]);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error registrando intento de login: " . $e->getMessage());
        return false;
    }
}

/**
 * Verificar si la IP/email está bloqueada por demasiados intentos
 */
function verificarBloqueoPorIntentos($email) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        $maxIntentos = obtenerConfiguracion('max_login_attempts', LOGIN_MAX_ATTEMPTS);
        $tiempoBloqueo = obtenerConfiguracion('lockout_time', LOGIN_LOCKOUT_TIME);
        
        // Verificar intentos por email en la última hora
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as intentos_fallidos
            FROM intentos_login 
            WHERE email = ? 
            AND exito = 0 
            AND fecha_intento > DATE_SUB(NOW(), INTERVAL ? SECOND)
        ");
        $stmt->execute([$email, $tiempoBloqueo]);
        $result = $stmt->fetch();
        
        if ($result['intentos_fallidos'] >= $maxIntentos) {
            return [
                'bloqueado' => true,
                'intentos' => $result['intentos_fallidos'],
                'tiempo_restante' => $tiempoBloqueo / 60 // en minutos
            ];
        }
        
        return [
            'bloqueado' => false,
            'intentos' => $result['intentos_fallidos'],
            'intentos_restantes' => $maxIntentos - $result['intentos_fallidos']
        ];
        
    } catch (Exception $e) {
        error_log("Error verificando bloqueo por intentos: " . $e->getMessage());
        return ['bloqueado' => false, 'intentos' => 0];
    }
}

// ============================================================================
// FUNCIONES DE LOGIN Y LOGOUT
// ============================================================================

/**
 * Realizar login del usuario
 */
function login($email, $password) {
    // Verificar bloqueo por intentos
    $bloqueo = verificarBloqueoPorIntentos($email);
    if ($bloqueo['bloqueado']) {
        registrarIntentoLogin($email, false);
        return [
            'success' => false,
            'message' => "Demasiados intentos fallidos. Intenta de nuevo en {$bloqueo['tiempo_restante']} minutos."
        ];
    }
    
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        // Buscar usuario por email
        $stmt = $pdo->prepare("
            SELECT u.*, r.nombre as rol_nombre
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE u.email = ? AND u.estado = 1
        ");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            registrarIntentoLogin($email, false);
            return [
                'success' => false,
                'message' => 'Email o contraseña incorrectos.',
                'intentos_restantes' => $bloqueo['intentos_restantes'] ?? 0
            ];
        }
        
        // Login exitoso
        registrarIntentoLogin($email, true);
        
        // Configurar sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['rol'] = $usuario['rol_nombre'];
        $_SESSION['avatar'] = $usuario['avatar'];
        $_SESSION['login_time'] = time();
        
        // Registrar sesión en BD
        registrarSesionActiva($usuario['id']);
        
        // Verificar acceso de invitado si aplica
        if ($usuario['rol_nombre'] === 'Invitado') {
            $accesoInvitado = verificarAccesoInvitado($usuario['id']);
            $_SESSION['acceso_invitado'] = $accesoInvitado;
            
            if (!$accesoInvitado['acceso_valido']) {
                logout();
                return [
                    'success' => false,
                    'message' => $accesoInvitado['mensaje']
                ];
            }
        }
        
        return [
            'success' => true,
            'message' => 'Login exitoso',
            'usuario' => $usuario,
            'rol' => $usuario['rol_nombre']
        ];
        
    } catch (Exception $e) {
        error_log("Error en login: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error del sistema. Intenta más tarde.'
        ];
    }
}

/**
 * Cerrar sesión del usuario
 */
function logout() {
    // Cerrar sesión en BD
    if (isset($_SESSION['usuario_id'])) {
        cerrarSesionActiva($_SESSION['usuario_id']);
    }
    
    // Limpiar variables de sesión
    $_SESSION = [];
    
    // Destruir cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destruir sesión
    session_destroy();
    
    return true;
}

// ============================================================================
// MIDDLEWARE DE VERIFICACIÓN DE SESIÓN
// ============================================================================

/**
 * Verificar sesión y permisos antes de cargar una página
 */
function verificarSesion($requiereAuth = true, $rolesPermitidos = []) {
    iniciarSesion();
    
    // Actualizar actividad de sesión
    if (estaAutenticado()) {
        actualizarActividadSesion();
        
        // Verificar tiempo de sesión
        $tiempoSesion = time() - ($_SESSION['login_time'] ?? 0);
        if ($tiempoSesion > SESSION_LIFETIME) {
            logout();
            redirigirA('login', ['mensaje' => 'Sesión expirada']);
        }
        
        // Verificar acceso de invitado si aplica
        if (esInvitado() && isset($_SESSION['acceso_invitado'])) {
            $acceso = $_SESSION['acceso_invitado'];
            if (!$acceso['acceso_valido']) {
                logout();
                redirigirA('login', ['mensaje' => $acceso['mensaje']]);
            }
        }
    }
    
    // Verificar si requiere autenticación
    if ($requiereAuth && !estaAutenticado()) {
        $_SESSION['return_url'] = $_SERVER['REQUEST_URI'] ?? '';
        redirigirA('login');
    }
    
    // Verificar roles permitidos
    if (!empty($rolesPermitidos) && estaAutenticado()) {
        $rolActual = $_SESSION['rol'] ?? '';
        if (!in_array($rolActual, $rolesPermitidos)) {
            redirigirA('error.403');
        }
    }
    
    return estaAutenticado();
}

/**
 * Requerir autenticación (shortcut)
 */
function requiereAuth($rolesPermitidos = []) {
    return verificarSesion(true, $rolesPermitidos);
}

/**
 * Requerir rol de administrador
 */
function requiereAdmin() {
    return verificarSesion(true, ['Administrador']);
}

// ============================================================================
// FUNCIONES AUXILIARES
// ============================================================================

/**
 * Obtener IP real del usuario
 */
function obtenerIPReal() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Detectar navegador del user agent
 */
function detectarNavegador($userAgent) {
    $navegadores = [
        'Chrome' => '/Chrome/i',
        'Firefox' => '/Firefox/i',
        'Safari' => '/Safari/i',
        'Edge' => '/Edge/i',
        'Opera' => '/Opera|OPR/i',
        'Internet Explorer' => '/Trident/i'
    ];
    
    foreach ($navegadores as $nombre => $patron) {
        if (preg_match($patron, $userAgent)) {
            return $nombre;
        }
    }
    
    return 'Desconocido';
}

/**
 * Detectar sistema operativo del user agent
 */
function detectarSistemaOperativo($userAgent) {
    $sistemas = [
        'Windows' => '/Windows/i',
        'Mac' => '/Mac/i',
        'Linux' => '/Linux/i',
        'Android' => '/Android/i',
        'iOS' => '/iPhone|iPad/i'
    ];
    
    foreach ($sistemas as $nombre => $patron) {
        if (preg_match($patron, $userAgent)) {
            return $nombre;
        }
    }
    
    return 'Desconocido';
}

/**
 * Detectar tipo de dispositivo
 */
function detectarDispositivo($userAgent) {
    if (preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)) {
        return 'Móvil';
    } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
        return 'Tablet';
    } else {
        return 'Escritorio';
    }
}

// ============================================================================
// INICIALIZACIÓN AUTOMÁTICA
// ============================================================================

// Iniciar sesión automáticamente al cargar este archivo
iniciarSesion();
?>