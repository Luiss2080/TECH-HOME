<?php

namespace App\Controllers;

use App\Models\User;
use Core\Request;
use Core\Session;
use Core\Validation;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login', ['title' => 'Bienvenido'], false);
    }



    public function loginForm(Request $request)
    {
        // Validar datos
        $validator = new Validation();
        $rules = [
            'email' => 'required|string|max:50',
            'password' => 'required|min:8|max:16'
        ];
        if (!$validator->validate($request->all(), $rules)) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $request->all());
            return redirect(route('login'));
        }
        $user = $request->all()['email'];
        $password = $request->all()['password'];
        // Autenticar usuario
        $user = $this->attempt($user, $password);

        if ($user) {
            Session::set('user', $user);
            $route = route('home');
            if (Session::has('back')) {
                $route = Session::get('back');
                Session::remove('back');
            }
            return redirect($route);
        }

        Session::flash('errors', ['general' => ['Credenciales Incorrecta']]);
        Session::flash('old', $_POST);
        return redirect(route('login'));
    }


    private function attempt($user, $password)
    {
        return User::attempt($user, $password);
    }
    public function logout()
    {
        Session::destroy();
        return redirect(route('login'));
    }
}
