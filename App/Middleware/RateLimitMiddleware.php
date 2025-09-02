<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\DB;

class RateLimitMiddleware implements Middleware
{
    // Configuraciones de rate limiting
    const DEFAULT_MAX_ATTEMPTS = 5;
    const DEFAULT_TIMEOUT_MINUTES = 15;
    const CLEANUP_PROBABILITY = 10; // 10% de probabilidad de ejecutar limpieza

    // Tipos de acciones con sus límites específicos
    const ACTION_LIMITS = [
        'login' => ['max' => 5, 'timeout' => 15],
        'otp' => ['max' => 3, 'timeout' => 5],
        'password_reset' => ['max' => 3, 'timeout' => 60],
        'registration' => ['max' => 3, 'timeout' => 60],
        'api' => ['max' => 100, 'timeout' => 1] // Por minuto
    ];

    public function handle(Request $request, $next, ...$params)
    {
        // Extraer parámetros del middleware
        $action = $params[0] ?? 'default';
        $customMaxAttempts = isset($params[1]) ? (int)$params[1] : null;
        $customTimeout = isset($params[2]) ? (int)$params[2] : null;

        // Obtener configuración para la acción
        $config = self::ACTION_LIMITS[$action] ?? [
            'max' => $customMaxAttempts ?? self::DEFAULT_MAX_ATTEMPTS,
            'timeout' => $customTimeout ?? self::DEFAULT_TIMEOUT_MINUTES
        ];

        // Identificar al cliente
        $clientId = $this->getClientIdentifier($request, $action);
        
        // Verificar rate limit
        $limitResult = $this->checkRateLimit($clientId, $action, $config);
        
        if (!$limitResult['allowed']) {
            return $this->handleRateLimitExceeded($limitResult, $action, $request);
        }

        // Registrar el intento
        $this->recordAttempt($clientId, $action);

        // Ejecutar siguiente middleware/controlador
        $response = $next($request);

        // Limpiar registros antiguos ocasionalmente
        if (rand(1, 100) <= self::CLEANUP_PROBABILITY) {
            $this->cleanupOldAttempts();
        }

        return $response;
    }

    /**
     * Obtener identificador único del cliente
     */
    private function getClientIdentifier(Request $request, string $action): string
    {
        $ip = $this->getClientIp($request);
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Para acciones específicas de usuario, incluir email si está disponible
        $email = '';
        if (in_array($action, ['login', 'otp', 'password_reset'])) {
            $email = $request->input('email', '');
        }

        // Generar hash único basado en IP, User Agent y email (si aplica)
        $identifier = hash('sha256', $ip . '|' . $userAgent . '|' . $email . '|' . $action);
        
        return $identifier;
    }

    /**
     * Obtener IP real del cliente
     */
    private function getClientIp(Request $request): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Default
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Verificar rate limit
     */
    private function checkRateLimit(string $clientId, string $action, array $config): array
    {
        try {
            $db = DB::getInstance();
            
            // Verificar intentos en la ventana de tiempo
            $windowStart = date('Y-m-d H:i:s', time() - ($config['timeout'] * 60));
            
            $query = "SELECT COUNT(*) as attempts, 
                             MIN(created_at) as first_attempt,
                             MAX(created_at) as last_attempt
                      FROM rate_limit_attempts 
                      WHERE client_id = ? AND action = ? AND created_at >= ?";
            
            $result = $db->query($query, [$clientId, $action, $windowStart]);
            $data = $result ? $result->fetch(\PDO::FETCH_ASSOC) : null;

            if (!$data || $data['attempts'] < $config['max']) {
                return [
                    'allowed' => true,
                    'attempts' => (int)($data['attempts'] ?? 0),
                    'max_attempts' => $config['max'],
                    'reset_time' => time() + ($config['timeout'] * 60)
                ];
            }

            // Rate limit excedido
            $resetTime = strtotime($data['first_attempt']) + ($config['timeout'] * 60);
            
            return [
                'allowed' => false,
                'attempts' => (int)$data['attempts'],
                'max_attempts' => $config['max'],
                'reset_time' => $resetTime,
                'time_remaining' => max(0, $resetTime - time()),
                'first_attempt' => $data['first_attempt'],
                'last_attempt' => $data['last_attempt']
            ];

        } catch (\Exception $e) {
            error_log('Error en rate limiting: ' . $e->getMessage());
            // En caso de error, permitir la solicitud (fail open)
            return ['allowed' => true, 'attempts' => 0, 'max_attempts' => $config['max']];
        }
    }

    /**
     * Registrar intento
     */
    private function recordAttempt(string $clientId, string $action): void
    {
        try {
            $db = DB::getInstance();
            $query = "INSERT INTO rate_limit_attempts (client_id, action, ip_address, user_agent, created_at) 
                      VALUES (?, ?, ?, ?, NOW())";
            
            $ip = $this->getClientIp(Request::getInstance());
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $db->query($query, [$clientId, $action, $ip, $userAgent]);
        } catch (\Exception $e) {
            error_log('Error registrando intento de rate limit: ' . $e->getMessage());
        }
    }

    /**
     * Manejar rate limit excedido
     */
    private function handleRateLimitExceeded(array $limitResult, string $action, Request $request): Response
    {
        $timeRemaining = ceil($limitResult['time_remaining'] / 60); // En minutos
        $resetTime = date('H:i:s', $limitResult['reset_time']);
        
        // Log del bloqueo por rate limiting
        error_log(sprintf(
            'Rate limit excedido - Acción: %s, IP: %s, Intentos: %d/%d, Reset en: %d minutos',
            $action,
            $this->getClientIp($request),
            $limitResult['attempts'],
            $limitResult['max_attempts'],
            $timeRemaining
        ));

        // Respuesta según el tipo de solicitud
        if ($this->isApiRequest($request)) {
            return Response::json([
                'error' => 'Rate limit excedido',
                'message' => "Demasiados intentos. Intenta de nuevo en {$timeRemaining} minutos.",
                'details' => [
                    'attempts' => $limitResult['attempts'],
                    'max_attempts' => $limitResult['max_attempts'],
                    'reset_time' => $resetTime,
                    'time_remaining_minutes' => $timeRemaining,
                    'action' => $action
                ]
            ], 429); // Too Many Requests
        }

        // Respuesta para solicitudes web
        $errorMessage = $this->getErrorMessage($action, $timeRemaining, $resetTime);
        
        Session::flash('error', $errorMessage);
        Session::flash('rate_limit', [
            'blocked' => true,
            'action' => $action,
            'time_remaining' => $timeRemaining,
            'reset_time' => $resetTime,
            'attempts' => $limitResult['attempts'],
            'max_attempts' => $limitResult['max_attempts']
        ]);

        // Redirigir según la acción
        return redirect($this->getRedirectUrl($action));
    }

    /**
     * Verificar si es una solicitud API
     */
    private function isApiRequest(Request $request): bool
    {
        $uri = $request->uri();
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        
        return strpos($uri, '/api/') === 0 || 
               strpos($contentType, 'application/json') !== false ||
               strpos($accept, 'application/json') !== false;
    }

    /**
     * Obtener mensaje de error personalizado
     */
    private function getErrorMessage(string $action, int $timeRemaining, string $resetTime): string
    {
        $messages = [
            'login' => "🔒 Demasiados intentos de inicio de sesión. Por seguridad, tu acceso ha sido bloqueado temporalmente. Podrás intentar de nuevo en {$timeRemaining} minutos (a las {$resetTime}).",
            'otp' => "🔐 Has excedido el límite de intentos de verificación OTP. Por seguridad, debes esperar {$timeRemaining} minutos antes de poder intentar nuevamente.",
            'password_reset' => "📧 Has solicitado demasiados restablecimientos de contraseña. Espera {$timeRemaining} minutos antes de solicitar uno nuevo.",
            'registration' => "👤 Demasiados intentos de registro. Espera {$timeRemaining} minutos antes de intentar crear una cuenta nuevamente.",
        ];

        return $messages[$action] ?? "⚠️ Has excedido el límite de solicitudes. Intenta de nuevo en {$timeRemaining} minutos.";
    }

    /**
     * Obtener URL de redirección según la acción
     */
    private function getRedirectUrl(string $action): string
    {
        $redirects = [
            'login' => route('login'),
            'otp' => route('login'),
            'password_reset' => route('password.forgot'),
            'registration' => route('register'),
        ];

        return $redirects[$action] ?? route('home');
    }

    /**
     * Limpiar intentos antiguos
     */
    private function cleanupOldAttempts(): void
    {
        try {
            $db = DB::getInstance();
            
            // Eliminar registros más antiguos que el timeout más largo (1 hora por defecto)
            $maxTimeout = max(array_column(self::ACTION_LIMITS, 'timeout'));
            $cleanupTime = date('Y-m-d H:i:s', time() - ($maxTimeout * 60 * 2)); // Doble del timeout más largo
            
            $query = "DELETE FROM rate_limit_attempts WHERE created_at < ?";
            $result = $db->query($query, [$cleanupTime]);
            
            if ($result) {
                $deletedRows = $result->rowCount();
                if ($deletedRows > 0) {
                    error_log("Rate Limit: Limpiados {$deletedRows} registros antiguos");
                }
            }
        } catch (\Exception $e) {
            error_log('Error en limpieza de rate limit: ' . $e->getMessage());
        }
    }

    /**
     * Crear tabla de rate limiting si no existe
     */
    public static function createTable(): bool
    {
        try {
            $db = DB::getInstance();
            $query = "
                CREATE TABLE IF NOT EXISTS `rate_limit_attempts` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `client_id` varchar(64) NOT NULL,
                  `action` varchar(50) NOT NULL,
                  `ip_address` varchar(45) NOT NULL,
                  `user_agent` text,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_client_action_time` (`client_id`, `action`, `created_at`),
                  KEY `idx_created_at` (`created_at`),
                  KEY `idx_ip_action` (`ip_address`, `action`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            $result = $db->query($query);
            return $result !== false;
        } catch (\Exception $e) {
            error_log('Error creando tabla de rate limit: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener estadísticas de rate limiting
     */
    public static function getStats(int $hours = 24): array
    {
        try {
            $db = DB::getInstance();
            $since = date('Y-m-d H:i:s', time() - ($hours * 3600));
            
            $query = "SELECT 
                        action,
                        COUNT(*) as total_attempts,
                        COUNT(DISTINCT client_id) as unique_clients,
                        COUNT(DISTINCT ip_address) as unique_ips,
                        MIN(created_at) as first_attempt,
                        MAX(created_at) as last_attempt
                      FROM rate_limit_attempts 
                      WHERE created_at >= ?
                      GROUP BY action
                      ORDER BY total_attempts DESC";
            
            $result = $db->query($query, [$since]);
            return $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {
            error_log('Error obteniendo stats de rate limit: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar estado de rate limit para un cliente específico
     */
    public static function checkClientStatus(string $email, string $action = 'login'): array
    {
        $request = Request::getInstance();
        $middleware = new self();
        $clientId = $middleware->getClientIdentifier($request, $action);
        $config = self::ACTION_LIMITS[$action] ?? [
            'max' => self::DEFAULT_MAX_ATTEMPTS,
            'timeout' => self::DEFAULT_TIMEOUT_MINUTES
        ];
        
        return $middleware->checkRateLimit($clientId, $action, $config);
    }

    /**
     * Resetear rate limit para un cliente (uso administrativo)
     */
    public static function resetClientLimit(string $email, string $action = 'login'): bool
    {
        try {
            $request = Request::getInstance();
            $middleware = new self();
            $clientId = $middleware->getClientIdentifier($request, $action);
            
            $db = DB::getInstance();
            $query = "DELETE FROM rate_limit_attempts WHERE client_id = ? AND action = ?";
            $result = $db->query($query, [$clientId, $action]);
            
            return $result !== false;
        } catch (\Exception $e) {
            error_log('Error reseteando rate limit: ' . $e->getMessage());
            return false;
        }
    }
}