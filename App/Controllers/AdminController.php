<?php

namespace App\Controllers;

use App\Services\AdminService;
use Core\Controller;
use Core\Request;
use Core\Session;
use Core\Validation;
use Exception;

class AdminController extends Controller
{
    private $adminService;

    public function __construct()
    {
        parent::__construct();
        $this->adminService = new AdminService();
    }

    public function index()
    {
        try {
            // Obtener datos del dashboard usando el servicio
            $data = $this->adminService->showDashboard();
            return view('admin.dashboard', $data);
        } catch (Exception $e) {
            return view('errors.500', ['message' => $e->getMessage()]);
        }
    }

    public function ajaxStats()
    {
        try {
            $type = $_GET['tipo'] ?? 'general';
            $data = $this->adminService->getStatsForAjax($type);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function refreshMetrics()
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $stats = $this->adminService->showDashboard();

            echo json_encode([
                'success' => true,
                'estadisticas' => $stats['estadisticas'],
                'resumen_sistema' => $stats['resumen_sistema']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function reportes()
    {
        return view('admin.reportes', [
            'title' => 'Reportes - Panel de Administración'
        ]);
    }

    public function configuracion()
    {
        return view('admin.configuracion', [
            'title' => 'Configuración - Panel de Administración'
        ]);
    }

    // === MÉTODOS PARA GESTIÓN DE USUARIOS ===

    public function usuarios()
    {
        try {
            $usuarios = $this->adminService->getAllUsers();
            return view('admin.usuarios', [
                'title' => 'Gestión de Usuarios - Panel de Administración',
                'usuarios' => $usuarios
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar usuarios: ' . $e->getMessage());
            return view('admin.usuarios', [
                'title' => 'Gestión de Usuarios - Panel de Administración',
                'usuarios' => []
            ]);
        }
    }

    public function crearUsuario()
    {
        try {
            $roles = $this->adminService->getAllRoles();
            return view('admin.usuarios.crear', [
                'title' => 'Crear Usuario - Panel de Administración',
                'roles' => $roles
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            return redirect(route('usuarios'));
        }
    }

    public function guardarUsuario(Request $request)
    {
        try {
            // Validaciones
            $rules = [
                'nombre' => 'required|min:2|max:100',
                'apellido' => 'required|min:2|max:100',
                'email' => 'required|email|max:255',
                'password' => 'required|min:8|max:255',
                'password_confirmation' => 'required|same:password',
                'telefono' => 'nullable|max:20',
                'fecha_nacimiento' => 'nullable|date',
                'roles' => 'required|array|min:1'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->except(['password', 'password_confirmation']));
                return redirect(route('usuarios.crear'));
            }

            // Verificar que el email no esté en uso
            if ($this->adminService->emailExists($request->input('email'))) {
                Session::flash('error', 'El email ya está en uso por otro usuario.');
                Session::flash('old', $request->except(['password', 'password_confirmation']));
                return redirect(route('usuarios.crear'));
            }
            // Crear usuario
            $userData = [
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'email' => $request->input('email'),
                'password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
                'telefono' => $request->input('telefono'),
                'fecha_nacimiento' => $request->input('fecha_nacimiento'),
                'estado' => 1 // 1 = activo por defecto
            ];

            $userId = $this->adminService->createUser($userData, $request->input('roles'));

            Session::flash('success', 'Usuario creado exitosamente.');
            return redirect(route('usuarios'));
        } catch (Exception $e) {
            throw $e;

            Session::flash('error', 'Error al crear usuario: ' . $e->getMessage());
            Session::flash('old', $request->except(['password', 'password_confirmation']));
            return redirect(route('usuarios.crear'));
        }
    }

    public function editarUsuario(Request $request, $id)
    {
        try {
            $usuario = $this->adminService->getUserById($id);
            if (!$usuario) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('usuarios'));
            }

            $roles = $this->adminService->getAllRoles();
            $usuarioRoles = $this->adminService->getUserRoles($id);

            return view('admin.usuarios.editar', [
                'title' => 'Editar Usuario - Panel de Administración',
                'usuario' => $usuario,
                'roles' => $roles,
                'usuarioRoles' => array_column($usuarioRoles, 'id')
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar usuario: ' . $e->getMessage());
            return redirect(route('usuarios'));
        }
    }

    public function actualizarUsuario(Request $request, $id)
    {
        try {
            // Verificar que el usuario existe
            $usuario = $this->adminService->getUserById($id);
            if (!$usuario) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('usuarios'));
            }

            // Validaciones
            $rules = [
                'nombre' => 'required|min:2|max:100',
                'apellido' => 'required|min:2|max:100',
                'email' => 'required|email|max:255',
                'telefono' => 'nullable|max:20',
                'fecha_nacimiento' => 'nullable|date',
                'roles' => 'required|array|min:1',
                'estado' => 'required|in:activo,inactivo'
            ];

            // Si se proporciona password, validarlo
            if ($request->input('password')) {
                $rules['password'] = 'min:8|max:255';
                $rules['password_confirmation'] = 'same:password';
            }

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->getErrors());
                Session::flash('old', $request->all());
                return redirect(route('usuarios.editar', ['id' => $id]));
            }            // Verificar que el email no esté en uso por otro usuario
            if ($this->adminService->emailExistsForOtherUser($request->input('email'), $id)) {
                Session::flash('error', 'El email ya está en uso por otro usuario.');
                Session::flash('old', $request->except(['password', 'password_confirmation']));
                return redirect(route('usuarios.editar', ['id' => $id]));
            }

            // Actualizar usuario
            $userData = [
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'email' => $request->input('email'),
                'telefono' => $request->input('telefono'),
                'fecha_nacimiento' => $request->input('fecha_nacimiento'),
                'estado' => $request->input('estado') === 'activo' ? 1 : 0
            ];

            // Si se proporciona nueva contraseña
            if ($request->input('password')) {
                $userData['password'] = password_hash($request->input('password'), PASSWORD_DEFAULT);
            }
            $this->adminService->updateUser($id, $userData, $request->input('roles'));

            Session::flash('success', 'Usuario actualizado exitosamente.');
            return redirect(route('usuarios'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al actualizar usuario: ' . $e->getMessage());
            Session::flash('old', $request->except(['password', 'password_confirmation']));
            return redirect(route('usuarios.editar', ['id' => $id]));
        }
    }

    public function eliminarUsuario(Request $request, $id)
    {
        try {
            // Verificar que el usuario existe
            $usuario = $this->adminService->getUserById($id);
            if (!$usuario) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('usuarios'));
            }
            // No permitir eliminar al usuario actual
            $currentUserId = Session::get('user_id') ?? Session::get('auth_user_id');
            if ($id == $currentUserId) {
                Session::flash('error', 'No puedes eliminar tu propia cuenta.');
                return redirect(route('usuarios'));
            }
            // Eliminar usuario
            $this->adminService->deleteUser($id);

            Session::flash('success', 'Usuario eliminado exitosamente.');
            return redirect(route('usuarios'));
        } catch (Exception $e) {
            // DEBUG: Log para verificar que se está ejecutando
            error_log("DEBUG AdminController::eliminarUsuario - Error: " . $e->getMessage());
            
            Session::flash('error', $e->getMessage());
            return redirect(route('usuarios'));
        }
    }

    /**
     * Mostrar formulario para editar roles de un usuario
     */
    public function editarRolesUsuario(Request $request, $id)
    {
        try {
            // Verificar que el usuario existe
            $usuario = $this->adminService->getUserById($id);
            if (!$usuario) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('usuarios'));
            }

            // Obtener todos los roles disponibles y los roles del usuario
            $rolesDisponibles = $this->adminService->getAllRoles();
            $rolesUsuario = $this->adminService->getUserRoles($id);

            // Convertir roles del usuario a un array de IDs para facilitar la comparación
            $rolesUsuarioIds = array_column($rolesUsuario, 'id');
            $title = 'Editar Roles - Usuario: ' . $usuario->nombre . ' ' . $usuario->apellido;
            return view('admin.usuarios.roles', [
                'usuario' => $usuario,
                'rolesDisponibles' => $rolesDisponibles,
                'rolesUsuario' => $rolesUsuarioIds,
                'title' => $title
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar la página: ' . $e->getMessage());
            return redirect(route('usuarios'));
        }
    }

    /**
     * Actualizar roles de un usuario
     */
    public function actualizarRolesUsuario(Request $request, $id)
    {
        try {
            // Verificar que el usuario existe
            $usuario = $this->adminService->getUserById($id);
            if (!$usuario) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('usuarios'));
            }

            // Validaciones usando la clase Validation
            $rules = [
                'roles' => 'array' // Los roles pueden ser un array vacío
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->getErrors());
                return redirect(route('usuarios.roles', ['id' => $id]));
            }

            // Obtener roles del request
            $roles = $request->input('roles', []);

            // Actualizar roles del usuario
            $this->adminService->updateUserRoles($id, $roles);

            Session::flash('success', 'Roles actualizados exitosamente.');
            return redirect(route('usuarios'));
        } catch (Exception $e) {
            throw $e;
            Session::flash('error', 'Error al actualizar roles: ' . $e->getMessage());
            return redirect(route('usuarios.roles', ['id' => $id]));
        }
    }

    public function editarPermisosUsuario(Request $request, $id)
    {
        try {
            // Verificar que el usuario existe
            $usuario = $this->adminService->getUserById($id);
            if (!$usuario) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('usuarios'));
            }

            // Obtener todos los permisos disponibles
            $permisos = $this->adminService->getAllPermissions();

            // Obtener permisos actuales del usuario
            $permisosUsuario = $this->adminService->getUserPermissions($id);

            return view('admin.usuarios.permisos', [
                'title' => 'Editar Permisos de Usuario',
                'usuario' => $usuario,
                'permisos' => $permisos,
                'permisosUsuario' => $permisosUsuario
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar permisos: ' . $e->getMessage());
            return redirect(route('usuarios'));
        }
    }

    public function actualizarPermisosUsuario(Request $request, $id)
    {
        try {
            // Verificar que el usuario existe
            $usuario = $this->adminService->getUserById($id);
            if (!$usuario) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('usuarios'));
            }

            // Validaciones usando la clase Validation
            $rules = [
                'permisos' => 'array' // Los permisos pueden ser un array vacío
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->getErrors());
                return redirect(route('usuarios.permisos', ['id' => $id]));
            }

            // Obtener permisos del request
            $permisos = $request->input('permisos', []);

            // Actualizar permisos del usuario
            $this->adminService->updateUserPermissions($id, $permisos);

            Session::flash('success', 'Permisos actualizados exitosamente.');
            return redirect(route('usuarios'));
        } catch (Exception $e) {
            throw $e;
            Session::flash('error', 'Error al actualizar permisos: ' . $e->getMessage());
            return redirect(route('usuarios.permisos', ['id' => $id]));
        }
    }

    public function cambiarEstadoUsuario(Request $request, $id)
    {
        try {
            $usuario = $this->adminService->getUserById($id);
            if (!$usuario) {
                Session::flash('error', 'Usuario no encontrado.');
                return redirect(route('usuarios'));
            }

            $nuevoEstado = $request->input('estado');
            if (!in_array($nuevoEstado, ['0', '1'])) {
                Session::flash('error', 'Estado inválido.');
                return redirect(route('usuarios'));
            }

            $this->adminService->changeUserStatus($id, $nuevoEstado == '1' ? 'activo' : 'inactivo');

            $estadoTexto = $nuevoEstado == '1' ? 'activado' : 'desactivado';
            Session::flash('success', "Usuario {$estadoTexto} exitosamente.");
            return redirect(route('usuarios'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al cambiar estado: ' . $e->getMessage());
            return redirect(route('usuarios'));
        }
    }

    public function ventas()
    {
        return view('admin.ventas', [
            'title' => 'Gestión de Ventas - Panel de Administración'
        ]);
    }

    public function crearVenta()
    {
        return view('admin.ventas.crear', [
            'title' => 'Crear Venta - Panel de Administración'
        ]);
    }

    // === MÉTODOS PARA GESTIÓN DE ROLES ===

    public function roles()
    {
        try {
            $roles = $this->adminService->getAllRoles();
            return view('admin.configuracion.roles.index', [
                'title' => 'Gestión de Roles - Configuración',
                'roles' => $roles
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function crearRol()
    {
        return view('admin.configuracion.roles.crear', [
            'title' => 'Crear Rol - Configuración'
        ]);
    }

    public function guardarRol(Request $request)
    {
        try {
            $data = $request->all();
            $validator = new Validation();
            $rules = [
                'nombre' => 'required|string|min:2|max:50',
                'descripcion' => 'string|max:255'
            ];

            if (!$validator->validate($data, $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $data);
                return redirect(route('admin.roles.crear'));
            }

            $this->adminService->createRole($data);
            Session::flash('success', 'Rol creado exitosamente');
            return redirect(route('admin.roles'));
        } catch (Exception $e) {
            Session::flash('errors', ['general' => [$e->getMessage()]]);
            Session::flash('old', $request->all());
            return redirect(route('admin.roles.crear'));
        }
    }

    public function editarRol(Request $request, $id)
    {
        try {
            $role = $this->adminService->getRoleById($id);
            return view('admin.configuracion.roles.editar', [
                'title' => 'Editar Rol - Configuración',
                'role' => $role
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actualizarRol($request, $id)
    {
        try {
            $data = $request->all();

            // Validación de datos
            $validator = new Validation();
            $rules = [
                'nombre' => 'required|string|min:2|max:50',
                'descripcion' => 'string|max:255'
            ];

            if (!$validator->validate($data, $rules)) {
                // Guardar errores y datos old en flash
                Session::flash('errors', $validator->errors());
                Session::flash('old', $data);

                return redirect(route('admin.roles.editar', ['id' => $id]));
            }

            $this->adminService->updateRole($id, $data);

            // Usar flash message en lugar de $_GET
            Session::flash('success', 'Rol actualizado exitosamente');

            return redirect(route('admin.roles'));
        } catch (Exception $e) {
            // En caso de error del servidor
            Session::flash('errors', ['general' => [$e->getMessage()]]);
            Session::flash('old', $request->all());

            return redirect(route('admin.roles.editar', ['id' => $id]));
        }
    }

    public function eliminarRol(Request $request, $id)
    {
        try {
            $this->adminService->deleteRole($id);

            Session::flash('success', 'Rol eliminado exitosamente');
            return redirect(route('admin.roles'));
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('admin.roles'));
        }
    }

    // === MÉTODOS PARA GESTIÓN DE PERMISOS ===

    public function permisos()
    {
        try {
            $permisos = $this->adminService->getAllPermissions();
            return view('admin.configuracion.permisos.index', [
                'title' => 'Gestión de Permisos - Configuración',
                'permisos' => $permisos
            ]);
        } catch (Exception $e) {
            return view('errors.500', ['message' => $e->getMessage()]);
        }
    }

    public function asignarPermisos($request, $id)
    {
        try {
            $role = $this->adminService->getRoleById($id);
            $permisos = $this->adminService->getAllPermissions();
            $permisosAsignados = $this->adminService->getPermissionsForRole($id);

            return view('admin.configuracion.roles.permisos', [
                'title' => 'Asignar Permisos - Configuración',
                'role' => $role,
                'permisos' => $permisos,
                'permisosAsignados' => $permisosAsignados
            ]);
        } catch (Exception $e) {
            return view('errors.404', ['message' => 'Rol no encontrado']);
        }
    }

    public function guardarPermisosRol($request, $id)
    {
        try {
            $permisos = $request->input('permisos', []);
            $this->adminService->syncRolePermissions($id, $permisos);

            Session::flash('success', 'Permisos asignados exitosamente');
            return redirect(route('admin.roles'));
        } catch (Exception $e) {
            return $this->asignarPermisos($request, $id);
        }
    }
}
