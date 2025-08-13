<?php

namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('auth.login', ['title' => 'Bienvenido'], false);
    }
}
