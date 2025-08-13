<?php

namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        if (!auth()) {
            return redirect(route('login'));
        }
        return view('home.index', ['title' => 'Bienvenido'], false);
    }
}
