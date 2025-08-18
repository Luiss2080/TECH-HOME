<?php

namespace Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class MiddlewareFactory
{
    /**
     * Registry de middleware registrados
     *
     * @var array
     */
    protected static $middlewares = [
        'auth' => AuthMiddleware::class,
        'role' => RoleMiddleware::class,
    ];

    /**
     * Registra un nuevo middleware en el factory
     *
     * @param string $name
     * @param string $middlewareClass
     * @return void
     */
    public static function register($name, $middlewareClass)
    {
        self::$middlewares[$name] = $middlewareClass;
    }

    /**
     * Resuelve un middleware desde un string
     * Formatos soportados:
     * - 'auth' -> AuthMiddleware
     * - 'role:administrador,docente' -> RoleMiddleware con roles
     * - 'role:administrador|docente' -> RoleMiddleware con roles
     *
     * @param string $middlewareString
     * @return object
     * @throws \Exception
     */
    public static function resolve($middlewareString)
    {
        // Si contiene ':', tiene parámetros
        if (strpos($middlewareString, ':') !== false) {
            list($name, $parameters) = explode(':', $middlewareString, 2);
            return self::createWithParameters($name, $parameters);
        }

        // Si no tiene parámetros, crear instancia simple
        return self::createSimple($middlewareString);
    }

    /**
     * Crea una instancia simple de middleware sin parámetros
     *
     * @param string $name
     * @return object
     * @throws \Exception
     */
    protected static function createSimple($name)
    {
        if (!isset(self::$middlewares[$name])) {
            throw new \Exception("Middleware '{$name}' no está registrado.");
        }

        $middlewareClass = self::$middlewares[$name];
        return new $middlewareClass();
    }

    /**
     * Crea una instancia de middleware con parámetros
     *
     * @param string $name
     * @param string $parameters
     * @return object
     * @throws \Exception
     */
    protected static function createWithParameters($name, $parameters)
    {
        if (!isset(self::$middlewares[$name])) {
            throw new \Exception("Middleware '{$name}' no está registrado.");
        }

        $middlewareClass = self::$middlewares[$name];

        // Manejar diferentes tipos de middleware según el nombre
        switch ($name) {
            case 'role':
                return self::createRoleMiddleware($parameters);

            case 'auth':
                // Auth no necesita parámetros, pero si los hay, los ignoramos
                return new $middlewareClass();

            default:
                // Para middleware personalizados, pasar parámetros como array
                $paramArray = self::parseParameters($parameters);
                return new $middlewareClass(...$paramArray);
        }
    }

    /**
     * Crea una instancia de RoleMiddleware con roles específicos
     *
     * @param string $parameters
     * @return RoleMiddleware
     */
    protected static function createRoleMiddleware($parameters)
    {
        // Soportar tanto comas como pipes como separadores
        $roles = preg_split('/[,|]/', $parameters);

        // Limpiar espacios en blanco
        $roles = array_map('trim', $roles);

        // Filtrar elementos vacíos
        $roles = array_filter($roles, function ($role) {
            return !empty($role);
        });

        return new RoleMiddleware($roles);
    }

    /**
     * Parsea parámetros separados por coma
     *
     * @param string $parameters
     * @return array
     */
    protected static function parseParameters($parameters)
    {
        $params = explode(',', $parameters);
        return array_map('trim', $params);
    }

    /**
     * Obtiene todos los middleware registrados
     *
     * @return array
     */
    public static function getRegistered()
    {
        return self::$middlewares;
    }

    // ==================== MÉTODOS LEGACY (COMPATIBILIDAD) ====================

    /**
     * Crea una instancia de middleware con parámetros (método legacy)
     *
     * @param string $middlewareClass
     * @param array $parameters
     * @return object
     */
    public static function create($middlewareClass, ...$parameters)
    {
        return new $middlewareClass(...$parameters);
    }

    /**
     * Crea middleware de rol con roles específicos (método legacy)
     *
     * @param string|array $roles
     * @return RoleMiddleware
     */
    public static function role($roles)
    {
        return new RoleMiddleware($roles);
    }

    /**
     * Middleware de autenticación (método legacy)
     *
     * @return AuthMiddleware
     */
    public static function auth()
    {
        return new AuthMiddleware();
    }
}
