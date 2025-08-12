<?php

namespace Core;

use App\Models\Token;

class Request
{
    protected $data = [];
    protected $user;
    protected $token;
    public function __construct()
    {
        // Obtenemos los datos de la solicitud
        $this->data = json_decode(file_get_contents('php://input'), true);
    }
    /**
     * Obtiene el método HTTP de la solicitud.
     *
     * @return string
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Obtiene la URL de la solicitud.
     *
     * @return string
     */
    public function uri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Obtiene un parámetro de la solicitud (query string o cuerpo).
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key, $default = null)
    {
        if ($this->method() === 'GET') {
            return $_GET[$key] ?? $default;
        }

        return $_POST[$key] ?? $default;
    }

    /**
     * Obtiene todas las entradas de la solicitud.
     *
     * @return array
     */
    public function all()
    {
        if ($this->method() === 'GET') {
            return $_GET;
        }

        return $_POST;
    }

    /**
     * Obtiene una cabecera HTTP.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function header($key, $default = null)
    {
        $headers = getallheaders();
        return $headers[$key] ?? $default;
    }

    /**
     * Obtiene una cookie.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function cookie($key, $default = null)
    {
        return $_COOKIE[$key] ?? $default;
    }

    // escrifimos funciones los geters y seters
    public function __get($name)
    {
        // verificamos si $name existe en $this->data
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }
    /**
     * Obtiene el usuario autenticado.
     *
     * @return \App\Models\User|null
     */
    public function user()
    {
        return $this->user;
    }
    /**
     * Asigna el usuario autenticado.
     *
     * @param \App\Models\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
    /**
     * Asignan el token
     * @param \App\Models\Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }
    /**
     * Obtiene el token
     * @return \App\Models\Token|null
     */
    public function token()
    {
        return $this->token;
    }
    public function getHeaders()
    {
        return getallheaders();
    }
}
