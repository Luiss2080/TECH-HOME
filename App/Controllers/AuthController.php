<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivationToken;
use Core\DB;
use Core\Request;
use Core\Session;
use Core\Validation;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login', ['title' => 'Bienvenido'], false);
    }

    /**
     * Mostrar formulario de registro
     */
    public function register()
    {
        return view('auth.register', ['title' => 'Crear Cuenta'], false);
    }

    /**
     * Procesar registro de usuario
     */
    public function registerForm(Request $request)
    {
        // Validar datos del formulario
        $validator = new Validation();
        $rules = [
            'nombre' => 'required|string|min:2|max:50',
            'apellido' => 'required|string|min:2|max:50',
            'email' => 'required|email|max:150',
            'password' => 'required|min:8|max:50',
            'password_confirmation' => 'required|same:password',
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date'
        ];

        if (!$validator->validate($request->all(), $rules)) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $request->except(['password', 'password_confirmation']));
            return redirect(route('register'));
        }

        $data = $request->all();

        // Verificar si el email ya existe
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            Session::flash('errors', ['email' => ['Este correo electrónico ya está registrado.']]);
            Session::flash('old', $request->except(['password', 'password_confirmation']));
            return redirect(route('register'));
        }

        try {
            DB::beginTransaction();
            // Crear el usuario
            $user = new User([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'telefono' => $data['telefono'] ?? null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                'estado' => 0, // Usuario inactivo hasta que valide el token
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            $user->save();
            // Asegurar que existe el rol "Invitado"
            $this->ensureGuestRoleExists();

            // Asignar rol de Invitado por defecto
            $guestRole = Role::findByName('Invitado');
            if ($guestRole) {
                $user->assignRole($guestRole->id);
            }
            DB::commit();
            
            // Crear token de activación
            $activationToken = ActivationToken::createToken($user->email);
            
            // Enviar email de bienvenida con token
            try {
                $emailService = mailService();
                $emailService->sendWelcomeEmail($user, $activationToken);
            } catch (\Exception $e) {
                error_log('Error enviando email de bienvenida: ' . $e->getMessage());
                // No fallar el registro si hay error en el email
            }

            Session::flash('success', '¡Tu cuenta ha sido creada exitosamente! Te hemos enviado un email con un enlace para activar tu cuenta. Revisa tu bandeja de entrada.');
            return redirect(route('login'));
        } catch (\Exception $e) {
            error_log('Error en registro de usuario: ' . $e->getMessage());
            DB::rollBack();
            Session::flash('error', 'Error interno. Intenta de nuevo más tarde.');
            Session::flash('old', $request->except(['password', 'password_confirmation']));
            return redirect(route('register'));
        }
    }

    /**
     * Asegurar que el rol "Invitado" existe
     */
    private function ensureGuestRoleExists()
    {
        $guestRole = Role::findByName('Invitado');

        if (!$guestRole) {
            // Crear el rol Invitado si no existe
            $role = new Role([
                'nombre' => 'Invitado',
                'descripcion' => 'Acceso temporal de 3 días a todo el material',
                'estado' => 1
            ]);
            $role->save();

            // Asignar permisos básicos al rol Invitado
            $basicPermissions = [
                'login',
                'logout',
                'cursos.ver',
                'libros.ver',
                'libros.descargar',
                'materiales.ver',
                'laboratorios.ver',
                'api.verify_session'
            ];

            foreach ($basicPermissions as $permission) {
                try {
                    $role->givePermissionTo($permission);
                } catch (\Exception $e) {
                    error_log("Error asignando permiso {$permission} al rol Invitado: " . $e->getMessage());
                }
            }
        }
    }



    public function loginForm(Request $request)
    {
        // Validar datos
        $validator = new Validation();
        $rules = [
            'email' => 'required|email|max:150',
            'password' => 'required|min:8|max:50'
        ];
        if (!$validator->validate($request->all(), $rules)) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $request->except(['password']));
            return redirect(route('login'));
        }

        $email = $request->all()['email'];
        $password = $request->all()['password'];

        // Autenticar usuario
        $user = $this->attempt($email, $password);
        if ($user) {
            // Verificar si la cuenta está activa
            if ($user->estado == 0) {
                Session::flash('errors', ['general' => ['Tu cuenta no está activada. Revisa tu email para activar tu cuenta.']]);
                Session::flash('old', $request->except(['password']));
                return redirect(route('login'));
            }
            
            Session::set('user', $user);

            // Determinar a qué dashboard redirigir según el rol
            $roles = $user->roles();
            $route = route(Dashboard()); // fallback
            // Si hay una URL de retorno guardada, usarla
            if (Session::has('back')) {
                $route = Session::get('back');
                Session::remove('back');
            }

            return redirect($route);
        }

        Session::flash('errors', ['general' => ['Credenciales incorrectas']]);
        Session::flash('old', $request->except(['password']));
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
    
    /**
     * Activar cuenta con token
     */
    public function activateAccount(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            Session::flash('error', 'Token de activación requerido.');
            return redirect(route('login'));
        }

        // Validar token
        $tokenData = ActivationToken::validateToken($token);

        if (!$tokenData) {
            Session::flash('error', 'El enlace de activación es inválido o ya ha sido usado.');
            return redirect(route('login'));
        }

        try {
            // Activar usuario
            $user = User::where('email', $tokenData['email'])->first();
            if (!$user) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('login'));
            }

            // Cambiar estado del usuario a activo
            $user->estado = 1;
            $user->save();

            // Marcar token como usado
            ActivationToken::markAsUsed($token);

            Session::flash('success', '¡Tu cuenta ha sido activada exitosamente! Ya puedes iniciar sesión y acceder a todo el contenido.');
            return redirect(route('login'));
        } catch (\Exception $e) {
            Session::flash('error', 'Error activando la cuenta. Intenta de nuevo.');
            error_log('Error activando cuenta: ' . $e->getMessage());
            return redirect(route('login'));
        }
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

            // Enviar email usando el helper
            $emailService = mailService();
            $sent = $emailService->sendPasswordResetEmail($email, $token);

            if ($sent) {
                Session::flash('success', 'Te hemos enviado un enlace de recuperación por email. Revisa tu bandeja de entrada.');
            } else {
                Session::flash('error', 'Hubo un problema enviando el email. Intenta de nuevo.');
            }
        } catch (\Exception $e) {
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
        ], false);
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
            $user = User::where('email', $email)->first();
            if (empty($user)) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('login'));
            }

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
