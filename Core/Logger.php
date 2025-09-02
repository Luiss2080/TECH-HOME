<?php

namespace Core;

class Logger
{
    const EMERGENCY = 0;
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;

    private static $levels = [
        0 => 'EMERGENCY',
        1 => 'ALERT',
        2 => 'CRITICAL',
        3 => 'ERROR',
        4 => 'WARNING',
        5 => 'NOTICE',
        6 => 'INFO',
        7 => 'DEBUG'
    ];

    private static $logFile;
    private static $securityLogFile;
    private static $auditLogFile;

    public static function init()
    {
        $logDir = BASE_PATH . 'storage/logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        self::$logFile = $logDir . 'app-' . date('Y-m-d') . '.log';
        self::$securityLogFile = $logDir . 'security-' . date('Y-m-d') . '.log';
        self::$auditLogFile = $logDir . 'audit-' . date('Y-m-d') . '.log';
    }

    /**
     * Log de seguridad - para eventos críticos de seguridad
     */
    public static function security(string $message, array $context = [])
    {
        self::writeLog(self::$securityLogFile, 'SECURITY', $message, $context);
    }

    /**
     * Log de auditoría - para acciones de usuarios
     */
    public static function audit(string $action, array $context = [])
    {
        $user = Session::get('user');
        $context['user_id'] = $user->id ?? 'anonymous';
        $context['user_email'] = $user->email ?? 'anonymous';
        $context['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $context['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        self::writeLog(self::$auditLogFile, 'AUDIT', $action, $context);
    }

    /**
     * Log general de errores
     */
    public static function error(string $message, array $context = [])
    {
        self::log(self::ERROR, $message, $context);
    }

    /**
     * Log de información
     */
    public static function info(string $message, array $context = [])
    {
        self::log(self::INFO, $message, $context);
    }

    /**
     * Log de advertencias
     */
    public static function warning(string $message, array $context = [])
    {
        self::log(self::WARNING, $message, $context);
    }

    /**
     * Log general
     */
    public static function log(int $level, string $message, array $context = [])
    {
        self::writeLog(self::$logFile, self::$levels[$level], $message, $context);
    }

    /**
     * Escribir al archivo de log
     */
    private static function writeLog(string $file, string $level, string $message, array $context = [])
    {
        if (!self::$logFile) {
            self::init();
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[$timestamp] [$level] $message";
        
        if ($contextStr) {
            $logEntry .= " | Context: $contextStr";
        }
        
        $logEntry .= PHP_EOL;
        
        file_put_contents($file, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log de intento de login
     */
    public static function loginAttempt(string $email, bool $success, string $reason = '')
    {
        $context = [
            'email' => $email,
            'success' => $success,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'reason' => $reason
        ];
        
        $message = $success ? "Login exitoso para $email" : "Login fallido para $email: $reason";
        self::security($message, $context);
    }

    /**
     * Log de acceso denegado
     */
    public static function accessDenied(string $resource, string $reason = '')
    {
        $user = Session::get('user');
        $context = [
            'resource' => $resource,
            'user_id' => $user->id ?? 'anonymous',
            'user_email' => $user->email ?? 'anonymous',
            'reason' => $reason,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        self::security("Acceso denegado a $resource", $context);
    }

    /**
     * Log de operaciones CRUD
     */
    public static function crudOperation(string $operation, string $table, $recordId, array $changes = [])
    {
        $context = [
            'operation' => strtoupper($operation),
            'table' => $table,
            'record_id' => $recordId,
            'changes' => $changes
        ];
        
        self::audit("$operation en $table (ID: $recordId)", $context);
    }
}

// Inicializar el logger
Logger::init();
