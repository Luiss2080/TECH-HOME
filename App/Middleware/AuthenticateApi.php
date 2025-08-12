<?php

namespace App\Middleware;

use App\Enums\StatusCode;
use Core\Request;
use Core\Response;
use App\Models\Token;

class AuthenticateApi
{
    public function handle(Request $request, $next)
    {
        // Obtener el token del encabezado Authorization
        $token = $request->header('Authorization');
        if (empty($token)) {
            $token = $request->getHeaders()['X-Mi-Token'] ?? '';
        }

        // Verificar el formato del token
        if (empty($token) || !is_string($token) || !str_starts_with($token, 'Bearer ')) {
            return Response::json([
                'success' => false,
                'message' => 'Formato de token inválido'
            ], StatusCode::UNAUTHORIZED);
        }

        // Extraer el token (eliminar 'Bearer ')
        $token = str_replace('Bearer ', '', $token);
        // Verificar si el token es válido
        if (!Token::isValid($token)) {
            return Response::json([
                'success' => false,
                'message' => 'Token no válido o expirado'
            ], StatusCode::UNAUTHORIZED);
        }

        // Obtener el usuario asociado al token
        $user = Token::getUserByToken($token);
        if (!$user) {
            return Response::json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], StatusCode::NOT_FOUND);
        }

        // Asignar el usuario a la solicitud
        $request->setUser($user);
        $request->setToken(Token::where('token', '=', $token)->first());

        return $next($request);
    }
}
