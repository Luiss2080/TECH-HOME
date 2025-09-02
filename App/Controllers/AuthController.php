<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivationToken;
use App\Models\CodigoOTP;
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
            'password' => 'required|securePassword|max:50',
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
        // Validar datos del primer paso
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

        $email = $request->input('email');
        $password = $request->input('password');

        // PASO 1: Verificar credenciales (email + password)
        $user = $this->attempt($email, $password);
        if (!$user) {
            // Verificar si el usuario existe para mostrar mensaje específico de bloqueo
            $existingUser = User::where('email', '=', $email)->where('estado', '=', 1)->first();
            if ($existingUser && $existingUser->isBlocked()) {
                $timeRemaining = $existingUser->getBlockTimeRemaining();
                Session::flash('errors', ['general' => ["Tu cuenta está bloqueada por intentos fallidos. Intenta nuevamente en {$timeRemaining} minutos."]]);
                Session::flash('blocked', [
                    'time_remaining' => $timeRemaining,
                    'unlock_time' => $existingUser->bloqueado_hasta,
                    'attempts_made' => $existingUser->intentos_fallidos
                ]);
            } else {
                Session::flash('errors', ['general' => ['Credenciales incorrectas']]);
            }

            // Registrar intento fallido
            $this->logFailedAttempt($email, 'Invalid credentials');
            Session::flash('old', $request->except(['password']));
            return redirect(route('login'));
        }

        // Verificar si la cuenta está activa
        if ($user->estado == 0) {
            Session::flash('errors', ['general' => ['Tu cuenta no está activada. Revisa tu email para activar tu cuenta.']]);
            Session::flash('old', $request->except(['password']));
            return redirect(route('login'));
        }

        // PASO 2: Credenciales correctas - Iniciar proceso 2FA
        return $this->initiate2FA($user, $email);
    }

    /**
     * Iniciar proceso de autenticación 2FA
     */
    private function initiate2FA(User $user, string $email)
    {
        try {
            // Verificar si el usuario puede generar un nuevo código
            $canGenerate = CodigoOTP::canGenerateNewCode($user->id);
            if (!$canGenerate['can_generate']) {
                if (isset($canGenerate['bloqueado'])) {
                    Session::flash('error', $canGenerate['reason']);
                    return redirect(route('login'));
                }

                // Si hay código activo, ir directo a verificación
                if (isset($canGenerate['codigo_existente'])) {
                    Session::set('2fa_user_id', $user->id);
                    Session::set('2fa_email', $email);
                    Session::set('2fa_start_time', time());
                    return redirect(route('auth.otp.verify'));
                }
            }

            // Generar nuevo código OTP
            $otpResult = CodigoOTP::generateOTP($user->id);
            if (!$otpResult['success']) {
                error_log('Error generando OTP para usuario ' . $user->id . ': ' . $otpResult['error']);
                Session::flash('error', 'Error interno generando código de verificación. Intenta de nuevo.');
                return redirect(route('login'));
            }

            // Enviar código por email
            $emailService = mailService();
            $emailSent = $emailService->sendOTPEmail(
                $email,
                $otpResult['codigo'],
                $user->nombre . ' ' . $user->apellido,
                1 // 1 minuto de expiración
            );

            if (!$emailSent) {
                error_log('Error enviando email OTP a: ' . $email);
                Session::flash('error', 'Error enviando código de verificación. Intenta de nuevo.');
                return redirect(route('login'));
            }

            // Guardar datos de sesión 2FA
            Session::set('2fa_user_id', $user->id);
            Session::set('2fa_email', $email);
            Session::set('2fa_start_time', time());
            Session::set('2fa_attempts', 0);

            // Log del proceso 2FA iniciado
            $this->log2FAEvent($user->id, $email, '2FA_INITIATED', [
                'codigo_enviado' => true,
                'expira_en' => $otpResult['expira_en'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            return redirect(route('auth.otp.verify'));
        } catch (\Exception $e) {
            error_log('Error en initiate2FA: ' . $e->getMessage());
            Session::flash('error', 'Error interno. Intenta de nuevo más tarde.');
            return redirect(route('login'));
        }
    }

    /**
     * Mostrar vista de verificación OTP
     */
    public function showOTPVerification()
    {
        // Verificar que hay una sesión 2FA activa
        if (!Session::has('2fa_user_id') || !Session::has('2fa_email')) {
            Session::flash('error', 'Sesión de verificación expirada. Inicia sesión nuevamente.');
            return redirect(route('login'));
        }

        // Verificar timeout de sesión 2FA (5 minutos máximo)
        $startTime = Session::get('2fa_start_time', 0);
        if (time() - $startTime > 300) { // 5 minutos
            Session::remove('2fa_user_id');
            Session::remove('2fa_email');
            Session::remove('2fa_start_time');
            Session::flash('error', 'Sesión de verificación expirada. Inicia sesión nuevamente.');
            return redirect(route('login'));
        }

        $email = Session::get('2fa_email');

        return view('auth.otp-verification', [
            'title' => 'Verificación 2FA',
            'email' => $email,
            'timer_duration' => 60, // 60 segundos
            'attempts_left' => max(0, 3 - Session::get('2fa_attempts', 0))
        ], false);
    }

    /**
     * Verificar código OTP
     */
    public function verifyOTP(Request $request)
    {
        // Verificar sesión 2FA
        if (!Session::has('2fa_user_id') || !Session::has('2fa_email')) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión de verificación expirada.',
                'redirect' => route('login')
            ], 401);
        }

        $userId = Session::get('2fa_user_id');
        $email = Session::get('2fa_email');

        // Validar código OTP
        $validator = new Validation();
        $rules = [
            'otp_code' => 'required|string|min:6|max:6'
        ];

        if (!$validator->validate($request->all(), $rules)) {
            return $this->handle2FAError('Código inválido. Debe tener 6 dígitos.', $userId, $email);
        }

        $otpCode = $request->input('otp_code');

        // Verificar código con el modelo
        $verificationResult = CodigoOTP::validateOTP($userId, $otpCode);

        if (!$verificationResult['success']) {
            return $this->handle2FAError($verificationResult['error'], $userId, $email, $verificationResult);
        }

        // ¡CÓDIGO VÁLIDO! Completar inicio de sesión
        return $this->complete2FALogin($userId, $email);
    }

    /**
     * Manejar error en verificación 2FA
     */
    private function handle2FAError(string $message, int $userId, string $email, array $verificationResult = [])
    {
        // Incrementar contador de intentos
        $attempts = Session::get('2fa_attempts', 0) + 1;
        Session::set('2fa_attempts', $attempts);

        // Log del intento fallido
        $this->log2FAEvent($userId, $email, '2FA_FAILED', [
            'attempt' => $attempts,
            'error' => $message,
            'locked' => isset($verificationResult['locked']) ? $verificationResult['locked'] : false
        ]);

        // Verificar si se excedieron los intentos
        if ($attempts >= 3) {
            // Limpiar sesión 2FA
            $this->clear2FASession();

            Session::flash('error', 'Se excedieron los intentos de verificación. Tu cuenta ha sido bloqueada temporalmente.');
            return redirect(route('login'));
        }

        // Si es una solicitud AJAX, responder con JSON
        if ($this->isAjaxRequest()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'attempts_left' => 3 - $attempts,
                'locked' => isset($verificationResult['locked']) ? $verificationResult['locked'] : false
            ], 400);
        }

        // Si es solicitud normal, redirigir con error
        Session::flash('error', $message);
        return redirect(route('auth.otp.verify'));
    }

    /**
     * Completar inicio de sesión 2FA
     */
    private function complete2FALogin(int $userId, string $email)
    {
        try {
            // Obtener usuario
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }

            // Crear sesión de usuario
            Session::set('user', $user);
            Session::set('user_id', $user->id);

            // Log del login exitoso
            $this->log2FAEvent($userId, $email, '2FA_SUCCESS', [
                'login_completed' => true,
                'session_created' => true
            ]);

            // Limpiar datos de 2FA
            $this->clear2FASession();

            // Determinar redirection
            $roles = $user->roles();
            $redirectRoute = route('home'); // fallback

            if (!empty($roles)) {
                $firstRole = strtolower($roles[0]['nombre']);
                switch ($firstRole) {
                    case 'administrador':
                        $redirectRoute = route('admin.dashboard');
                        break;
                    case 'estudiante':
                        $redirectRoute = route('estudiante.dashboard');
                        break;
                    case 'docente':
                        $redirectRoute = route('docente.dashboard');
                        break;
                    case 'invitado':
                        $redirectRoute = route('home');
                        break;
                }
            }

            // Si hay URL de retorno guardada, usarla solo si no es una ruta genérica
            if (Session::has('back')) {
                $backUrl = Session::get('back');
                Session::remove('back');
                
                // Solo usar la URL de retorno si no es home, login, register, o rutas genéricas
                $genericRoutes = ['/', '/login', '/register', '/auth/otp-verify', '', '/TECH-HOME', '/TECH-HOME/', '/TECH-HOME/login', '/TECH-HOME/register'];
                $parsedUrl = parse_url($backUrl, PHP_URL_PATH);
                
                if (!in_array($parsedUrl, $genericRoutes) && $parsedUrl !== null) {
                    $redirectRoute = $backUrl;
                }
            }

            // Si es AJAX, responder con JSON
            if ($this->isAjaxRequest()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Inicio de sesión exitoso!',
                    'redirect' => $redirectRoute
                ]);
            }

            Session::flash('success', '¡Bienvenido! Has iniciado sesión correctamente.');
            return redirect($redirectRoute);
        } catch (\Exception $e) {
            error_log('Error completando 2FA login: ' . $e->getMessage());

            // Limpiar sesión en caso de error
            $this->clear2FASession();

            if ($this->isAjaxRequest()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error completando el inicio de sesión.',
                    'redirect' => route('login')
                ], 500);
            }

            Session::flash('error', 'Error completando el inicio de sesión. Intenta de nuevo.');
            return redirect(route('login'));
        }
    }

    /**
     * Reenviar código OTP
     */
    public function resendOTP(Request $request)
    {
        // Verificar sesión 2FA
        if (!Session::has('2fa_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión expirada'
            ], 401);
        }

        $userId = Session::get('2fa_user_id');
        $email = Session::get('2fa_email');

        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }

            // Generar nuevo código
            $otpResult = CodigoOTP::resendOTP($userId);
            if (!$otpResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $otpResult['reason'] ?? 'No se pudo generar un nuevo código'
                ], 400);
            }

            // Enviar por email
            $emailService = mailService();
            $emailSent = $emailService->sendOTPEmail(
                $email,
                $otpResult['codigo'],
                $user->nombre . ' ' . $user->apellido,
                1
            );

            if (!$emailSent) {
                throw new \Exception('Error enviando email');
            }

            // Log del reenvío
            $this->log2FAEvent($userId, $email, '2FA_RESENT', [
                'new_expiration' => $otpResult['expira_en']
            ]);

            // Reset attempts counter
            Session::set('2fa_attempts', 0);

            return response()->json([
                'success' => true,
                'message' => 'Código reenviado exitosamente'
            ]);
        } catch (\Exception $e) {
            error_log('Error reenviando OTP: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error reenviando código. Intenta de nuevo.'
            ], 500);
        }
    }

    /**
     * Limpiar sesión 2FA
     */
    private function clear2FASession()
    {
        Session::remove('2fa_user_id');
        Session::remove('2fa_email');
        Session::remove('2fa_start_time');
        Session::remove('2fa_attempts');
    }

    /**
     * Verificar si es solicitud AJAX
     */
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Log de intento fallido
     */
    private function logFailedAttempt(string $email, string $reason)
    {
        try {
            $db = DB::getInstance();
            $query = "INSERT INTO intentos_login (email, ip_address, user_agent, exito, fecha_intento) 
                      VALUES (?, ?, ?, 0, NOW())";
            $db->query($query, [
                $email,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);

            error_log("Failed login attempt for: {$email} - Reason: {$reason}");
        } catch (\Exception $e) {
            error_log('Error logging failed attempt: ' . $e->getMessage());
        }
    }

    /**
     * Log de eventos 2FA
     */
    private function log2FAEvent(int $userId, string $email, string $event, array $data = [])
    {
        $logData = [
            'user_id' => $userId,
            'email' => $email,
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $data
        ];

        error_log('2FA Event: ' . json_encode($logData));
    }


    private function attempt($user, $password)
    {
        return User::attempt($user, $password);
    }
    public function logout()
    {
        // Limpiar cualquier URL de retorno antes del destroy
        Session::remove('back');
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
            'password' => 'required|securePassword|max:50',
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
