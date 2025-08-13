<?php

namespace App\Controllers;

use Core\Session;

class Controller
{

    public function __construct()
    {
        Session::startSession();
        if ('GET' !== $_SERVER['REQUEST_METHOD']) {
            // Validar CSRF Token
            if (!csrf_verify($_POST['_token'] ?? '')) {
                Session::flash('errors', ['general' => 'Token CSRF inválido']);
                Session::flash('old', $_POST);
                redirect(route('login'));
            }
        }
    }
}
