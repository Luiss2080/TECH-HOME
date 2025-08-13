<?php

namespace App\Controllers;

use Core\Request;

class AuthController extends Controller
{
    public function loginForm(Request $request)
    {
        dd($request->all());
    }
}
