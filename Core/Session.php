<?php

namespace Core;

class Session
{
    /**
     * Inicia la sesión si no está activa.
     */
    public static function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configurar parámetros de sesión para mayor robustez
            ini_set('session.gc_maxlifetime', 3600); // 1 hora
            ini_set('session.cookie_lifetime', 3600); // 1 hora
            session_start();
        }
    }

    /**
     * Almacena datos en la sesión de forma persistente.
     */
    public static function set($key, $value)
    {
        self::startSession();
        $_SESSION[$key] = $value;
        
        // Forzar escribir cambios inmediatamente para evitar pérdida de datos
        session_write_close();
        session_start();
    }

    /**
     * Obtiene datos de la sesión.
     */
    public static function get($key, $default = null)
    {
        self::startSession();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Almacena datos temporales para la próxima solicitud (flash data).
     */
    public static function flash($key, $value)
    {
        self::startSession();
        $_SESSION['_flash'][$key] = $value;
        
        // Escribir cambios inmediatamente
        session_write_close();
        session_start();
    }

    /**
     * Elimina la sesión y todos sus datos.
     */
    public static function destroy()
    {
        self::startSession();
        session_unset();
        session_destroy();
    }

    /**
     * Elimina los mensajes flash después de ser consumidos.
     */
    public static function clearFlash()
    {
        self::startSession();
        unset($_SESSION['_flash']);
    }
    
    public static function flashGet($key)
    {
        self::startSession();
        return $_SESSION['_flash'][$key] ?? null;
    }
    
    public static function flashSet($key, $value)
    {
        self::startSession();
        $_SESSION['_flash'][$key] = $value;
    }
    
    public static function has($key)
    {
        self::startSession();
        return isset($_SESSION[$key]);
    }
    
    public static function hasFlash($key)
    {
        self::startSession();
        return isset($_SESSION['_flash'][$key]);
    }
    
    public static function remove($key)
    {
        self::startSession();
        unset($_SESSION[$key]);
        
        // Escribir cambios inmediatamente
        session_write_close();
        session_start();
    }
    
    public static function removeFlash($key)
    {
        self::startSession();
        unset($_SESSION['_flash'][$key]);
    }

    /**
     * Regenerar ID de sesión para prevenir session hijacking
     */
    public static function regenerateId($deleteOldSession = true)
    {
        self::startSession();
        session_regenerate_id($deleteOldSession);
    }

    /**
     * Obtener todas las variables de sesión
     */
    public static function all()
    {
        self::startSession();
        return $_SESSION;
    }

    /**
     * Verificar si la sesión está activa
     */
    public static function isActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
