<?php

namespace Core;

class Request
{
    private static ?Request $instance = null;
    protected $___data = [];

    private function __construct()
    {
        // Inicializar el array de datos
        $this->___data = [];
        
        // Obtener datos según el método HTTP
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        switch (strtoupper($method)) {
            case 'GET':
                $this->___data = $_GET;
                break;
                
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                // Para formularios normales, usar $_POST
                if (!empty($_POST)) {
                    $this->___data = array_merge($_GET, $_POST);
                } else {
                    // Para JSON o datos raw, intentar decodificar
                    $rawData = file_get_contents('php://input');
                    if (!empty($rawData)) {
                        $jsonData = json_decode($rawData, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $this->___data = array_merge($_GET, $jsonData);
                        } else {
                            // Si no es JSON válido, parsearlo como query string
                            parse_str($rawData, $parsedData);
                            $this->___data = array_merge($_GET, $parsedData);
                        }
                    } else {
                        $this->___data = $_GET;
                    }
                }
                break;
                
            default:
                $this->___data = $_GET;
                break;
        }
    }
    public static function getInstance(): Request
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * Retorna el método de la solicitud
     * @return string
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    /**
     * Retorna la URI (Uniform Resource Identifier) de la solicitud.
     * La URI es una cadena que identifica de manera única el recurso solicitado en el servidor, 
     * incluyendo la ruta y, opcionalmente, los parámetros de consulta.
     * @return string
     */
    public function uri()
    {
        return $_SERVER['REQUEST_URI'];
    }
    /**
     * Retorna una entrada de la solicitud
     * @param string $key
     * @param mixed $default
     */
    public function input(string $key, $default = null)
    {
        return $this->___data[$key] ?? $default;
    }
    /**
     * Retorna todas las entradas de la solicitud
     * @return array
     */
    public function all()
    {
        return $this->___data;
    }
    public function __get($name)
    {
        return $this->___data[$name] ?? null;
    }
    public function getHeaders()
    {
        return getallheaders();
    }
}
