<?php

namespace Core;

class Response
{
    protected $statusCode = 200;
    protected $headers = [];
    protected $content;

    /**
     * Constructor de la clase Response.
     *
     * @param mixed $content
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct($content = '', $statusCode = 200, $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Establece el código de estado HTTP.
     *
     * @param int $statusCode
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Agrega una cabecera HTTP.
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function header($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Establece el contenido de la respuesta.
     *
     * @param mixed $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Redirige a una URL específica.
     *
     * @param string $url
     * @return self
     */
    public function redirect($url)
    {
        $this->statusCode = 302; // Código de redirección
        $this->header('Location', $url);
        return $this;
    }

    /**
     * Envía la respuesta al cliente.
     */
    public function send()
    {
        // Establecer el código de estado HTTP
        http_response_code($this->statusCode);

        // Enviar las cabeceras HTTP
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Enviar el contenido de la respuesta
        echo $this->content;
    }
    public function with($key, $value)
    {
        Session::flashSet($key, $value);
        return $this;
    }
    /**
     * Crea una respuesta JSON
     * 
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     * @return self
     */
    public static function json($data, $statusCode = 200, $headers = [])
    {
        $response = new static(json_encode($data), $statusCode, $headers);
        $response->header('Content-Type', 'application/json');
        return $response;
    }
    /**
     * Crea una respuesta de error
     * 
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return self
     */
    public static function error($message, $statusCode = 400, $errors = [])
    {
        return static::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
}
