<?php
/**
 * Controlador para gestión de usuarios - Tech Home
 * Maneja la lógica de negocio y validaciones del módulo de usuarios
 */

require_once __DIR__ . '/../modelos/UsuarioModelo.php';

class UsuarioControlador {
    private $modelo;
    
    public function __construct() {
        $this->modelo = new UsuarioModelo();
    }
    
    /**
     * Mostrar formulario de creación
     */
    public function mostrarCrear() {
        $roles = $this->modelo->obtenerRoles();
        
        return [
            'roles' => $roles,
            'form_data' => [],
            'errores' => [],
            'success' => '',
            'error' => ''
        ];
    }
    
    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        // Obtener datos necesarios para la vista
        $roles = $this->modelo->obtenerRoles();
        
        // Validar datos
        $errores = $this->validarDatosUsuario($datos);
        
        // Si hay errores, devolver con los datos del formulario
        if (!empty($errores)) {
            return [
                'roles' => $roles,
                'form_data' => $datos,
                'errores' => $errores,
                'success' => '',
                'error' => ''
            ];
        }
        
        // Hashear la contraseña
        $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);
        
        // Preparar datos para el modelo
        $datosUsuario = [
            'nombre' => trim($datos['nombre']),
            'apellido' => trim($datos['apellido']),
            'email' => trim($datos['email']),
            'password' => $passwordHash,
            'rol_id' => intval($datos['rol_id']),
            'telefono' => !empty($datos['telefono']) ? trim($datos['telefono']) : null,
            'fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
            'estado' => isset($datos['estado']) && $datos['estado'] == '1'
        ];
        
        // Crear usuario
        $resultado = $this->modelo->crear($datosUsuario);
        
        if ($resultado['success']) {
            // Redirigir después de crear exitosamente
            header('Location: index.php?success=' . urlencode('Usuario creado exitosamente'));
            exit();
        } else {
            return [
                'roles' => $roles,
                'form_data' => $datos,
                'errores' => [],
                'success' => '',
                'error' => $resultado['message']
            ];
        }
    }
    
    /**
     * Mostrar lista de usuarios
     */
    public function index($filtros = [], $pagina = 1) {
        $limite = 12; // Usuarios por página
        $offset = ($pagina - 1) * $limite;
        
        // Obtener usuarios
        $resultado = $this->modelo->listar($filtros, $limite, $offset);
        
        // Obtener datos para filtros
        $roles = $this->modelo->obtenerRoles();
        
        // Calcular información de paginación
        $totalPaginas = ceil($resultado['total'] / $limite);
        
        return [
            'usuarios' => $resultado['usuarios'],
            'roles' => $roles,
            'filtros' => $filtros,
            'paginacion' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_registros' => $resultado['total'],
                'tiene_anterior' => $resultado['tiene_anterior'],
                'tiene_siguiente' => $resultado['tiene_siguiente']
            ]
        ];
    }
    
    /**
     * Mostrar usuario específico
     */
    public function ver($user_id) {
        $usuario = $this->modelo->obtenerPorId($user_id);
        
        if (!$usuario) {
            header('Location: index.php?error=' . urlencode('Usuario no encontrado'));
            exit();
        }
        
        // Obtener actividad del usuario
        $actividad = $this->modelo->obtenerActividadUsuarios($user_id, 10);
        
        return [
            'usuario' => $usuario,
            'actividad' => $actividad
        ];
    }
    
    /**
     * Mostrar formulario de edición
     */
    public function mostrarEditar($user_id) {
        $usuario = $this->modelo->obtenerPorId($user_id);
        
        if (!$usuario) {
            header('Location: index.php?error=' . urlencode('Usuario no encontrado'));
            exit();
        }
        
        $roles = $this->modelo->obtenerRoles();
        
        return [
            'usuario' => $usuario,
            'roles' => $roles,
            'form_data' => $usuario,
            'errores' => [],
            'success' => '',
            'error' => ''
        ];
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar($user_id, $datos) {
        // Verificar que el usuario existe
        $usuario = $this->modelo->obtenerPorId($user_id);
        if (!$usuario) {
            header('Location: index.php?error=' . urlencode('Usuario no encontrado'));
            exit();
        }
        
        // Obtener datos necesarios para la vista
        $roles = $this->modelo->obtenerRoles();
        
        // Validar datos
        $errores = $this->validarDatosUsuario($datos, $user_id);
        
        // Si hay errores, devolver con los datos del formulario
        if (!empty($errores)) {
            return [
                'usuario' => $usuario,
                'roles' => $roles,
                'form_data' => array_merge($usuario, $datos),
                'errores' => $errores,
                'success' => '',
                'error' => ''
            ];
        }
        
        // Preparar datos para el modelo
        $datosUsuario = [
            'nombre' => trim($datos['nombre']),
            'apellido' => trim($datos['apellido']),
            'email' => trim($datos['email']),
            'rol_id' => intval($datos['rol_id']),
            'telefono' => !empty($datos['telefono']) ? trim($datos['telefono']) : null,
            'fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
            'estado' => isset($datos['estado']) && $datos['estado'] == '1'
        ];
        
        // Agregar contraseña solo si se proporcionó una nueva
        if (!empty($datos['password'])) {
            $datosUsuario['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }
        
        // Actualizar usuario
        $resultado = $this->modelo->actualizar($user_id, $datosUsuario);
        
        if ($resultado['success']) {
            // Redirigir después de actualizar exitosamente
            header('Location: ver.php?id=' . $user_id . '&success=' . urlencode('Usuario actualizado exitosamente'));
            exit();
        } else {
            return [
                'usuario' => $usuario,
                'roles' => $roles,
                'form_data' => array_merge($usuario, $datos),
                'errores' => [],
                'success' => '',
                'error' => $resultado['message']
            ];
        }
    }
    
    /**
     * Eliminar usuario
     */
    public function eliminar($user_id) {
        $usuario = $this->modelo->obtenerPorId($user_id);
        
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }
        
        $resultado = $this->modelo->eliminar($user_id);
        
        return $resultado;
    }
    
    /**
     * Obtener estadísticas del dashboard
     */
    public function obtenerEstadisticas() {
        $estadisticas = $this->modelo->obtenerEstadisticas();
        $masActivos = $this->modelo->obtenerMasActivos(5);
        $usuariosRecientes = $this->modelo->obtenerUsuariosRecientes(5);
        
        return [
            'estadisticas' => $estadisticas,
            'mas_activos' => $masActivos,
            'usuarios_recientes' => $usuariosRecientes
        ];
    }
    
    /**
     * Buscar usuarios por término
     */
    public function buscar($termino, $limite = 10) {
        $filtros = ['busqueda' => $termino];
        $resultado = $this->modelo->listar($filtros, $limite, 0);
        
        return $resultado['usuarios'];
    }
    
    /**
     * Autenticar usuario (para login)
     */
    public function autenticar($email, $password) {
        try {
            $usuarioData = $this->modelo->obtenerPorEmail($email);
            
            if (!$usuarioData) {
                return [
                    'success' => false,
                    'message' => 'Email o contraseña incorrectos'
                ];
            }
            
            if (!password_verify($password, $usuarioData['password'])) {
                return [
                    'success' => false,
                    'message' => 'Email o contraseña incorrectos'
                ];
            }
            
            // Actualizar último acceso
            $this->modelo->actualizarUltimoAcceso($usuarioData['id']);
            
            return [
                'success' => true,
                'usuario' => $usuarioData,
                'message' => 'Autenticación exitosa'
            ];
            
        } catch (Exception $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en el sistema de autenticación'
            ];
        }
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarPassword($user_id, $passwordActual, $passwordNuevo) {
        $usuario = $this->modelo->obtenerPorId($user_id);
        
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }
        
        // Verificar contraseña actual
        if (!password_verify($passwordActual, $usuario['password'])) {
            return [
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ];
        }
        
        // Validar nueva contraseña
        $errores = $this->validarPassword($passwordNuevo);
        
        if (!empty($errores)) {
            return [
                'success' => false,
                'message' => implode(', ', $errores)
            ];
        }
        
        // Hashear nueva contraseña
        $passwordHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
        
        // Actualizar en base de datos
        $datosActualizacion = ['password' => $passwordHash];
        $resultado = $this->modelo->actualizar($user_id, $datosActualizacion);
        
        if ($resultado['success']) {
            return [
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar la contraseña'
            ];
        }
    }
    
    /**
     * Activar/Desactivar usuario
     */
    public function toggleActivo($user_id) {
        $usuario = $this->modelo->obtenerPorId($user_id);
        
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }
        
        // Si se está desactivando un administrador, verificar que no sea el último
        if ($usuario['estado'] && $usuario['rol_nombre'] === 'Administrador') {
            $estadisticas = $this->modelo->obtenerEstadisticas();
            
            if ($estadisticas['administradores'] <= 1) {
                return [
                    'success' => false,
                    'message' => 'No se puede desactivar el último administrador del sistema'
                ];
            }
        }
        
        $nuevoEstado = !$usuario['estado'];
        $datosActualizacion = ['estado' => $nuevoEstado];
        $resultado = $this->modelo->actualizar($user_id, $datosActualizacion);
        
        if ($resultado['success']) {
            $accion = $nuevoEstado ? 'activado' : 'desactivado';
            return [
                'success' => true,
                'message' => "Usuario {$accion} exitosamente",
                'nuevo_estado' => $nuevoEstado
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al cambiar el estado del usuario'
            ];
        }
    }
    
    /**
     * Validar permisos de usuario
     */
    public function validarPermisos($user_id, $accion) {
        $usuario = $this->modelo->obtenerPorId($user_id);
        
        if (!$usuario || !$usuario['estado']) {
            return false;
        }
        
        $permisos = [
            'Administrador' => ['crear', 'ver', 'editar', 'eliminar', 'exportar', 'estadisticas'],
            'Docente' => ['crear', 'ver', 'editar', 'exportar', 'estadisticas'],
            'Vendedor' => ['crear', 'ver', 'editar', 'exportar'],
            'Estudiante' => ['ver'],
            'Invitado' => ['ver']
        ];
        
        return in_array($accion, $permisos[$usuario['rol_nombre']] ?? []);
    }
    
    /**
     * Validar datos de usuario
     */
    private function validarDatosUsuario($datos, $user_id = null) {
        $errores = [];
        
        // Validar nombre
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen($datos['nombre']) < 2) {
            $errores['nombre'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'El nombre no puede exceder 100 caracteres';
        }
        
        // Validar apellido
        if (empty($datos['apellido'])) {
            $errores['apellido'] = 'El apellido es obligatorio';
        } elseif (strlen($datos['apellido']) < 2) {
            $errores['apellido'] = 'El apellido debe tener al menos 2 caracteres';
        } elseif (strlen($datos['apellido']) > 100) {
            $errores['apellido'] = 'El apellido no puede exceder 100 caracteres';
        }
        
        // Validar email
        if (empty($datos['email'])) {
            $errores['email'] = 'El email es obligatorio';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El formato del email no es válido';
        } elseif (strlen($datos['email']) > 150) {
            $errores['email'] = 'El email no puede exceder 150 caracteres';
        } elseif ($this->modelo->existeEmail($datos['email'], $user_id)) {
            $errores['email'] = 'Ya existe un usuario con este email';
        }
        
        // Validar contraseña (solo si se está creando o si se proporciona una nueva)
        if (!$user_id || !empty($datos['password'])) {
            $passwordErrors = $this->validarPassword($datos['password']);
            if (!empty($passwordErrors)) {
                $errores['password'] = implode(', ', $passwordErrors);
            }
        }
        
        // Validar rol_id
        if (empty($datos['rol_id'])) {
            $errores['rol_id'] = 'El rol es obligatorio';
        } elseif (!is_numeric($datos['rol_id'])) {
            $errores['rol_id'] = 'El rol seleccionado no es válido';
        }
        
        // Validar teléfono (opcional)
        if (!empty($datos['telefono'])) {
            if (strlen($datos['telefono']) > 20) {
                $errores['telefono'] = 'El teléfono no puede exceder 20 caracteres';
            }
        }
        
        // Validar fecha de nacimiento (opcional)
        if (!empty($datos['fecha_nacimiento'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_nacimiento']);
            if (!$fecha) {
                $errores['fecha_nacimiento'] = 'El formato de fecha no es válido';
            } else {
                $hoy = new DateTime();
                if ($fecha > $hoy) {
                    $errores['fecha_nacimiento'] = 'La fecha de nacimiento no puede ser futura';
                }
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar contraseña
     */
    private function validarPassword($password) {
        $errores = [];
        
        if (empty($password)) {
            $errores[] = 'La contraseña es obligatoria';
            return $errores;
        }
        
        if (strlen($password) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if (strlen($password) > 255) {
            $errores[] = 'La contraseña no puede exceder 255 caracteres';
        }
        
        if (!preg_match('/[A-Za-z]/', $password)) {
            $errores[] = 'La contraseña debe contener al menos una letra';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errores[] = 'La contraseña debe contener al menos un número';
        }
        
        return $errores;
    }
    
    /**
     * Exportar usuarios a CSV
     */
    public function exportarCSV($filtros = []) {
        try {
            // Obtener todos los usuarios que coincidan con los filtros
            $resultado = $this->modelo->listar($filtros, 1000, 0);
            $usuarios = $resultado['usuarios'];
            
            // Configurar headers para descarga
            $filename = 'usuarios_techhome_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            // Crear archivo CSV
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($output, [
                'ID',
                'Nombre',
                'Apellido',
                'Email',
                'Rol',
                'Teléfono',
                'Fecha Nacimiento',
                'Estado',
                'Fecha Registro'
            ]);
            
            // Datos
            foreach ($usuarios as $usuario) {
                fputcsv($output, [
                    $usuario['id'],
                    $usuario['nombre'],
                    $usuario['apellido'],
                    $usuario['email'],
                    $usuario['rol_nombre'] ?? 'Sin rol',
                    $usuario['telefono'] ?? 'Sin teléfono',
                    $usuario['fecha_nacimiento'] ? date('d/m/Y', strtotime($usuario['fecha_nacimiento'])) : 'No especificada',
                    $usuario['estado'] ? 'Activo' : 'Inactivo',
                    date('d/m/Y H:i', strtotime($usuario['fecha_creacion']))
                ]);
            }
            
            fclose($output);
            exit();
            
        } catch (Exception $e) {
            error_log("Error al exportar usuarios: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al exportar los usuarios'
            ];
        }
    }
}
?>