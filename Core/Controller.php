<?php

namespace Core;

use Core\Session;
use Core\Request;

class Controller
{

    public function __construct()
    {
        Session::startSession();
        if ('GET' !== request()->method()) {
            // Validar CSRF Token
            if (!csrf_verify(request()->input('_token', ''))) {
                Session::flash('errors', ['general' => 'Token CSRF inválido']);
                Session::flash('old', request()->all());
                redirect(route('login'));
            }
        }
    }
}
