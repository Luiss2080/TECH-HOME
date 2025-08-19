<?php

namespace App\Controllers;

use Core\Controller;
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
            $route = route(Dashboard());
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
    public function verify_session()
    {
        $user = auth();
        if ($user) {
            $roles = $user->roles();
            $firstRole = !empty($roles) ? $roles[0]['nombre'] : null;
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'email' => $user->email,
                    'rol' => $firstRole
                ]
            ]);
        }
        return response()->json([
            'authenticated' => false,
            'error' => 'No authenticated user'
        ], 401);
    }

    /**
     * Mostrar formulario de solicitud de recuperación
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password', ['title' => 'Recuperar Contraseña'], false);
    }

    /**
     * Enviar enlace de recuperación por email
     */
    public function sendResetLink(Request $request)
    {
        // Validar email
        $validator = new Validation();
        $rules = [
            'email' => 'required|email|max:150'
        ];

        if (!$validator->validate($request->all(), $rules)) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $request->all());
            return redirect(route('password.forgot'));
        }

        $email = $request->input('email');

        // Verificar que el usuario existe
        $users = User::where('email', $email);
        if (empty($users)) {
            Session::flash('error', 'No encontramos una cuenta con ese email.');
            return redirect(route('password.forgot'));
        }

        try {
            // Crear token de recuperación
            $token = \App\Models\PasswordResetToken::createToken($email);

            // Enviar email
            $emailService = new \App\Services\EmailService();
            $sent = $emailService->sendPasswordResetEmail($email, $token);

            if ($sent) {
                Session::flash('success', 'Te hemos enviado un enlace de recuperación por email. Revisa tu bandeja de entrada.');
            } else {
                Session::flash('error', 'Hubo un problema enviando el email. Intenta de nuevo.');
            }
        } catch (\Exception $e) {
            throw $e;
            Session::flash('error', 'Error interno. Intenta de nuevo más tarde.');
            error_log('Error en recuperación de contraseña: ' . $e->getMessage());
        }
        return redirect(route('login'));
    }

    /**
     * Mostrar formulario para restablecer contraseña
     */
    public function resetPassword(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            Session::flash('error', 'Token de recuperación requerido.');
            return redirect(route('login'));
        }

        // Validar token
        $tokenData = \App\Models\PasswordResetToken::validateToken($token);

        if (!$tokenData) {
            Session::flash('error', 'El enlace de recuperación es inválido o ha expirado.');
            return redirect(route('login'));
        }

        return view('auth.reset-password', [
            'title' => 'Restablecer Contraseña',
            'token' => $token,
            'email' => $tokenData['email']
        ],false);
    }

    /**
     * Actualizar la contraseña
     */
    public function updatePassword(Request $request)
    {
        // Validar datos
        $validator = new Validation();
        $rules = [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8|max:50',
            'password_confirmation' => 'required|same:password'
        ];

        if (!$validator->validate($request->all(), $rules)) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $request->all());
            return redirect(route('password.reset') . '?token=' . $request->input('token'));
        }

        $token = $request->input('token');
        $email = $request->input('email');
        $password = $request->input('password');

        // Validar token nuevamente
        $tokenData = \App\Models\PasswordResetToken::validateToken($token);

        if (!$tokenData || $tokenData['email'] !== $email) {
            Session::flash('error', 'El enlace de recuperación es inválido o ha expirado.');
            return redirect(route('login'));
        }

        try {
            // Actualizar contraseña del usuario
            $users = User::where('email', $email);
            if (empty($users)) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('login'));
            }

            $user = $users[0];
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->save();

            // Marcar token como usado
            \App\Models\PasswordResetToken::markAsUsed($token);

            Session::flash('success', 'Tu contraseña ha sido actualizada exitosamente. Ya puedes iniciar sesión.');
            return redirect(route('login'));
        } catch (\Exception $e) {
            Session::flash('error', 'Error actualizando la contraseña. Intenta de nuevo.');
            error_log('Error actualizando contraseña: ' . $e->getMessage());
            return redirect(route('password.reset') . '?token=' . $token);
        }
    }
}
