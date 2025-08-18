<?php

namespace Core;

class Route
{
    protected $uri;
    protected $action;
    protected $method;
    protected $middleware = [];
    protected $name;
    protected $routeParameters = [];

    public function __construct($method, $uri, $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
    }

    /**
     * Agrega un middleware a la ruta.
     *
     * @param string|array|object $middleware
     * @return self
     */
    public function middleware($middleware)
    {
        if (is_array($middleware)) {
            $this->middleware = array_merge($this->middleware, $middleware);
        } else {
            $this->middleware[] = $middleware;
        }
        return $this;
    }

    /**
     * Asigna un nombre a la ruta.
     *
     * @param string $name
     * @return self
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Obtiene el método HTTP de la ruta.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Obtiene la URI de la ruta.
     *
     * @return string
     */
    public function getUri()
    {
        return BASE_URL . $this->uri;
    }

    /**
     * Obtiene la acción de la ruta.
     *
     * @return array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Obtiene los middlewares de la ruta.
     *
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Obtiene el nombre de la ruta.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Asigna parámetros a la ruta.
     * @param string $parameters 
     * @return self
     */
    public function setParameters(string $parameters)
    {
        // verificar si la ruta tiene parámetros
        if (strpos($this->uri, '{') !== false) {
            $as = explode('/', $parameters);
            $as2 = explode('/', $this->uri);
            for ($i = 0; $i < count($as); $i++) {
                if (strpos($as2[$i], '{') !== false) {

                    $param = $as[$i];
                    $param = trim($param, '');
                    if (is_numeric($param)) {
                        $param = (int) $param;
                    }
                    $this->routeParameters[] = $param;
                }
            }
        }
        return $this;
    }
    /**
     * Obtiene los parámetros de la ruta.
     * @return array
     */
    public function getParameters()
    {
        return $this->routeParameters;
    }
}
