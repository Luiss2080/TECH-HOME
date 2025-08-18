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

    public function usuarios()
    {
        return view('admin.usuarios', [
            'title' => 'Gestión de Usuarios - Panel de Administración'
        ]);
    }

    public function crearUsuario()
    {
        return view('admin.usuarios.crear', [
            'title' => 'Crear Usuario - Panel de Administración'
        ]);
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

    public function eliminarRol($request, $id)
    {
        try {
            $this->adminService->deleteRole($id);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Rol eliminado exitosamente']);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
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

    public function crearPermiso()
    {
        return view('admin.configuracion.permisos.crear', [
            'title' => 'Crear Permiso - Configuración'
        ]);
    }

    public function guardarPermiso($request)
    {
        try {
            $data = $request->all();
            
            // Validación de datos
            $validator = new Validation();
            $rules = [
                'nombre' => 'required|string|min:2|max:50|unique:Permission,nombre',
                'descripcion' => 'string|max:255'
            ];
            
            if (!$validator->validate($data, $rules)) {
                // Guardar errores y datos old en flash
                Session::flash('errors', $validator->errors());
                Session::flash('old', $data);
                
                return redirect(route('admin.permisos.crear'));
            }
            
            $this->adminService->createPermission($data);

            Session::flash('success', 'Permiso creado exitosamente');
            return redirect(route('admin.permisos'));
        } catch (Exception $e) {
            // En caso de error del servidor
            Session::flash('errors', ['general' => [$e->getMessage()]]);
            Session::flash('old', $request->all());
            
            return redirect(route('admin.permisos.crear'));
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
