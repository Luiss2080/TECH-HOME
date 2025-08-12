<?php

namespace Core\Exceptions;

use Exception;

class RouteNotFoundException extends Exception
{
    public function __construct($route)
    {
        parent::__construct("La ruta '$route' no fue encontrada.", 404);
    }
}
