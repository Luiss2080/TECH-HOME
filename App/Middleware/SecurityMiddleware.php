<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Logger;

class SecurityMiddleware implements Middleware
{
    // Headers de seguridad recomendados
    private $securityHeaders = [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data:; connect-src 'self';"
    ];

    public function handle(Request $request, $next, ...$params)
    {
        // Detectar patrones de ataque comunes
        $this->detectSuspiciousActivity($request);
        
        // Validar headers peligrosos
        $this->validateHeaders($request);
        
        // Continuar con la solicitud
        $response = $next($request);
        
        // Agregar headers de seguridad a la respuesta
        $this->addSecurityHeaders($response);
        
        return $response;
    }

    /**
     * Detectar actividad sospechosa
     */
    private function detectSuspiciousActivity(Request $request)
    {
        $uri = $request->uri();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip = $this->getClientIp($request);
        
        // Detectar intentos de SQL Injection
        $sqlPatterns = [
            '/union\s+select/i',
            '/select\s+.*\s+from/i',
            '/drop\s+table/i',
            '/insert\s+into/i',
            '/delete\s+from/i',
            '/update\s+.*\s+set/i',
            '/exec\s*\(/i',
            '/script\s*>/i'
        ];
        
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $uri) || preg_match($pattern, http_build_query($_GET))) {
                Logger::security("Posible intento de SQL Injection detectado", [
                    'ip' => $ip,
                    'uri' => $uri,
                    'get_params' => $_GET,
                    'user_agent' => $userAgent,
                    'severity' => 'HIGH'
                ]);
                break;
            }
        }
        
        // Detectar intentos de XSS
        $xssPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i'
        ];
        
        $allInputs = array_merge($_GET, $_POST);
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        Logger::security("Posible intento de XSS detectado", [
                            'ip' => $ip,
                            'uri' => $uri,
                            'field' => $key,
                            'value' => substr($value, 0, 200),
                            'user_agent' => $userAgent,
                            'severity' => 'HIGH'
                        ]);
                        break 2;
                    }
                }
            }
        }
        
        // Detectar bots/crawlers maliciosos
        $maliciousBots = [
            'sqlmap',
            'nikto',
            'nessus',
            'openvas',
            'nmap',
            'masscan',
            'zgrab'
        ];
        
        foreach ($maliciousBots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                Logger::security("Bot malicioso detectado", [
                    'ip' => $ip,
                    'uri' => $uri,
                    'user_agent' => $userAgent,
                    'bot_detected' => $bot,
                    'severity' => 'CRITICAL'
                ]);
                break;
            }
        }
        
        // Detectar intentos de acceso a archivos sensibles
        $sensitiveFiles = [
            '/\.env',
            '/config/',
            '/admin/',
            '/phpmyadmin/',
            '/backup/',
            '/\.git/',
            '/database/',
            '/logs/'
        ];
        
        foreach ($sensitiveFiles as $pattern) {
            if (stripos($uri, $pattern) !== false) {
                Logger::security("Intento de acceso a archivo sensible", [
                    'ip' => $ip,
                    'uri' => $uri,
                    'user_agent' => $userAgent,
                    'severity' => 'HIGH'
                ]);
                break;
            }
        }
    }

    /**
     * Validar headers peligrosos
     */
    private function validateHeaders(Request $request)
    {
        // Detectar headers que podrían indicar ataques
        $suspiciousHeaders = [
            'X-Forwarded-Host',
            'X-Forwarded-Server',
            'X-Rewrite-URL'
        ];
        
        foreach ($suspiciousHeaders as $header) {
            if (isset($_SERVER['HTTP_' . str_replace('-', '_', strtoupper($header))])) {
                Logger::security("Header sospechoso detectado", [
                    'header' => $header,
                    'value' => $_SERVER['HTTP_' . str_replace('-', '_', strtoupper($header))],
                    'ip' => $this->getClientIp($request),
                    'severity' => 'MEDIUM'
                ]);
            }
        }
    }

    /**
     * Agregar headers de seguridad
     */
    private function addSecurityHeaders($response)
    {
        foreach ($this->securityHeaders as $header => $value) {
            header("$header: $value");
        }
    }

    /**
     * Obtener IP real del cliente
     */
    private function getClientIp(Request $request): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
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
}
