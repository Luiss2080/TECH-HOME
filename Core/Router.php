<?php

namespace Core;

class Router
{
    protected static $routes = [];

    /**
     * Define una ruta GET.
     *
     * @param string $uri
     * @param array $action
     * @return Route
     */
    public static function get($uri, $action)
    {
        return self::addRoute('GET', $uri, $action);
    }

    /**
     * Define una ruta POST.
     *
     * @param string $uri
     * @param array $action
     * @return Route
     */
    public static function post($uri, $action)
    {
        return self::addRoute('POST', $uri, $action);
    }
    /**
     * Define una ruta DELETE.
     * 
     * @param string $uri
     * @param array $action
     * @return Route
     */
    public static function delete($uri, $action)
    {
        return self::addRoute('DELETE', $uri, $action);
    }
    /**
     * Define una ruta PUT.
     * @param string $uri
     * @param array $action
     * @return Route
     */
    public static function put($uri, $action)
    {
        return self::addRoute('PUT', $uri, $action);
    }
    /**
     * Define una ruta PATCH.
     * @param string $uri
     * @param array $action
     * @return Route
     */
    public static function patch($uri, $action)
    {
        return self::addRoute('PATCH', $uri, $action);
    }

    /**
     * Agrega una ruta a la lista de rutas.
     *
     * @param string $method
     * @param string $uri
     * @param array $action
     * @return Route
     */
    protected static function addRoute($method, $uri, $action)
    {
        $route = new Route($method, $uri, $action);
        self::$routes[$method][$uri] = $route;
        return $route;
    }
    /**
     * Despacha la solicitud.
     *
     * @param Request $request
     * @return Response
     */
    public static function dispatch(Request $request)
    {
        $uri = $request->uri();
        $uri = self::normalizeUri($uri);
        $method = $request->method();
        // Buscar la ruta correspondiente
        $route = self::findRoute($method, $uri);
        if ($route) {
            // Ejecutar middlewares
            foreach ($route->getMiddleware() as $middleware) {
                // Si el middleware ya es una instancia, usarla directamente
                if (is_object($middleware)) {
                    $middlewareInstance = $middleware;
                } elseif (is_string($middleware)) {
                    // Si es un string, resolver usando el factory
                    $middlewareInstance = \Core\MiddlewareFactory::resolve($middleware);
                } else {
                    // Si es un string de clase, crear una nueva instancia (compatibilidad legacy)
                    $middlewareInstance = new $middleware;
                }

                $response = $middlewareInstance->handle($request, function ($request) use ($route) {
                    return self::resolveAction($route->getAction(), $request, $route->getParameters());
                });
                // Si el middleware devuelve una respuesta, la retornamos
                if ($response instanceof Response) {
                    return $response;
                }
            }
            // Si no hay middlewares o todos pasaron, ejecutar la acción
            return self::resolveAction($route->getAction(), $request, $route->getParameters());
        }
        // Manejar 404 para API
        if (str_starts_with($uri, '/api/')) {
            return Response::json([
                'success' => false,
                'message' => 'Endpoint no encontrado'
            ], 404);
        }
        // Si no se encuentra la ruta, devolver un error 404
        return view(view: 'errors.404', statusCode: 404);
    }
    protected static function findRoute($method, $uri): Route|null
    {
        foreach (self::$routes[$method] as $routeUri => $route) {
            if (self::uriMatchesRoute($routeUri, $uri)) {
                return $route->setParameters($uri);
            }
        }
        return null;
    }

    protected static function uriMatchesRoute($routeUri, $requestUri)
    {
        // Convertir la ruta definida en una expresión regular
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            array_shift($matches);

            return true;
        }

        return false;
    }
    /**
     * Normaliza la URI eliminando el BASE_URL
     */
    private static function normalizeUri(string $uri): string
    {
        $url = '/' . trim(str_replace(BASE_URL, '', $uri), '/');
        $url = explode('?', $url)[0];
        return $url;
    }
    /**
     * Resuelve la acción (controlador y método).
     *
     * @param array $action
     * @param Request $request
     * @return Response
     */
    protected static function resolveAction($action, Request $request, $parameters = [])
    {
        list($controller, $method) = $action;
        try {

            // Verificar si el controlador y el método existen
            if (class_exists($controller) && method_exists($controller, $method)) {
                $controllerInstance = new $controller;
                return $controllerInstance->$method($request, ...$parameters);
            }
        } catch (\Exception $e) {
            // si es api mostramos internal server error
            if (str_starts_with($request->uri(), '/api/')) {
                return Response::json([
                    'success' => false,
                    'message' => 'Error interno del servidor'
                ], 500);
            }
            // si no mostramos la vista de error
            throw $e;
        }
        // Si no existe, devolver un error 404
        return new Response('Página no encontrada', 404);
    }
    /**
     * Obtiene la URL de una ruta por su nombre.
     *
     * @param string $name
     * @param array $parameters
     * @return string
     * @throws \Exception Si la ruta no existe.
     */
    public static function route(string $name, array $parameters = []): string
    {


        $route = null;
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $r) {
                if ($r->getName() === $name) {
                    $route = $r;
                    break;
                
                }
            }
        }
        if (!isset($route)) {
            return "#$name";
        }
        $uri = $route->getUri();
        foreach ($parameters as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }
        return $uri;
    }
    /**
     * Carga las rutas desde un directorio
     */
    public static function loadRoutes(string $directory): void
    {
        foreach (glob($directory . '/*.php') as $file) {
            require_once $file;
        }
    }
}





