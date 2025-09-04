<?php

namespace Core\Exceptions;

class QueryException extends \Exception
{
    protected $sql;

    public function __construct($message = "", $sql = "", $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->sql = $sql;
    }

    public function getSql()
    {
        return $this->sql;
    }
}
