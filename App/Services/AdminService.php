<?php

namespace App\Services;

use App\Models\DashboardStats;
use App\Models\ModelHasPermissions;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RoleHasPermissions;
use Core\DB;
use PDO;
use Exception;

class AdminService
{
    public function showDashboard(): array
    {
        return [
            'estadisticas' => DashboardStats::getGeneralStats(),
            'actividades_recientes' => DashboardStats::getRecentActivities(5),
            'sesiones_activas' => DashboardStats::getActiveSessions(5),
            'ventas_recientes' => DashboardStats::getRecentSales(5),
            'libros_recientes' => DashboardStats::getRecentBooks(5),
            'componentes_recientes' => DashboardStats::getRecentComponents(5),
            'resumen_sistema' => DashboardStats::getSystemSummary(),
            'usuario' => $this->getCurrentUserData()
        ];
    }

    public function getStatsForAjax(string $type = 'general'): array
    {

        switch ($type) {
            case 'general':
                return DashboardStats::getGeneralStats();
            case 'ventas':
                return DashboardStats::getRecentSales(10);
            case 'actividades':
                return DashboardStats::getRecentActivities(10);
            case 'sesiones':
                return DashboardStats::getActiveSessions(10);
            case 'libros':
                return DashboardStats::getRecentBooks(10);
            case 'componentes':
                return DashboardStats::getRecentComponents(10);
            default:
                throw new Exception("Tipo de estadística no válido: $type");
        }
    }

    public function updateMetrics(): array
    {

        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
            throw new Exception('Solo se permiten peticiones AJAX');
        }

        return [
            'estadisticas' => DashboardStats::getGeneralStats(),
            'resumen_sistema' => DashboardStats::getSystemSummary()
        ];
    }


    private function redirectByRole(string $role): void
    {
        $routes = [
            'docente' => 'docente.dashboard',
            'estudiante' => 'estudiante.dashboard',
            'vendedor' => 'vendedor.dashboard',
            'invitado' => 'home'
        ];
        $routeName = $routes[$role] ?? 'home';
        redirect(route($routeName));
    }

    private function getCurrentUserData(): array
    {
        $user = auth();
        $roles = $user->roles();

        return [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'email' => $user->email,
            'roles' => $roles ? array_column($roles, 'nombre') : ['Sin rol']
        ];
    }

    public static function formatNumber(float $number, int $decimals = 2): string
    {
        return number_format($number, $decimals, '.', ',');
    }

    public static function formatCurrency(float $amount): string
    {
        return 'Bs. ' . self::formatNumber($amount, 2);
    }

    public static function getStatusClass(string $status): string
    {
        $classes = [
            'Activo' => 'success',
            'Inactivo' => 'secondary',
            'Pendiente' => 'warning',
            'Completada' => 'success',
            'Cancelada' => 'danger',
            'Publicado' => 'success',
            'Borrador' => 'secondary',
            'Archivado' => 'warning'
        ];

        return $classes[$status] ?? 'secondary';
    }

    // === MÉTODOS PARA GESTIÓN DE ROLES ===

    public function getAllRoles(): array
    {
        // Usar el modelo en lugar de consulta SQL directa
        return Role::all();
    }

    public function getRoleById(int $id): Role|null
    {
        return Role::find($id);
    }

    public function createRole(array $data): bool
    {
        if (Role::findByName($data['nombre'])) {
            throw new Exception('Ya existe un rol con ese nombre');
        }

        $roleData = [
            'nombre' => trim($data['nombre']),
            'descripcion' => trim($data['descripcion'] ?? ''),
            'estado' => 1
        ];

        $role = new Role($roleData);
        $role->save();
        return true;
    }

    public function updateRole(int $id, array $data): bool
    {
        $role = Role::find($id);
        if (!$role) {
            throw new Exception('Rol no encontrado');
        }

        if (in_array($role->nombre, ['administrador', 'docente', 'estudiante'])) {
            throw new Exception('No se puede modificar este rol del sistema');
        }

        if ($role->nombre !== trim($data['nombre'])) {
            if (Role::findByName($data['nombre'])) {
                throw new Exception('Ya existe un rol con ese nombre');
            }
        }

        $role->fill([
            'nombre' => trim($data['nombre']),
            'descripcion' => trim($data['descripcion'] ?? ''),
            'estado' => $data['estado'] ?? 1
        ]);

        $role->save();
        return true;
    }

    public function deleteRole(int $id): bool
    {
        $role = Role::find($id);
        if (!$role) {
            throw new Exception('Rol no encontrado');
        }

        // Verificar que no sea un rol protegido
        if (in_array($role->nombre, ['administrador', 'docente', 'estudiante'])) {
            throw new Exception('No se puede eliminar este rol del sistema');
        }

        // Verificar que no tenga usuarios asignados
        $db = DB::getInstance();
        try {
            DB::beginTransaction();

            $usersCount = $db->query("SELECT COUNT(*) as count FROM model_has_roles WHERE role_id = ?", [$id])->fetch();
            if ($usersCount->count > 0) {
                // borrar asignaciones de roles
                $db->query("DELETE FROM model_has_roles WHERE role_id = ?", [$id]);
            }
            $role->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }


        // Eliminar primero los permisos del rol
        $db->query("DELETE FROM role_has_permissions WHERE role_id = ?", [$id]);

        $role->delete();
        return true;
    }

    // === MÉTODOS PARA GESTIÓN DE USUARIOS ===

    public function getAllUsers(): array
    {
        // Usar el modelo User en lugar de consulta SQL directa
        $users = User::all();
        $usersData = [];

        foreach ($users as $user) {
            $userData = $user->getAttributes();
            $roles = $user->roles();
            $userData['roles_nombres'] = implode(', ', array_column($roles, 'nombre'));
            $userData['roles_ids'] = implode(',', array_column($roles, 'id'));
            $usersData[] = $userData;
        }

        return $usersData;
    }
    public function getUserById(int $id)
    {
        return User::find($id);
    }

    public function getUserRoles(int $userId): array
    {
        $user = User::find($userId);
        return $user ? $user->roles() : [];
    }

    public function emailExists(string $email): bool
    {
        $user = User::where('email', '=', $email)->first();
        return $user !== null;
    }

    public function emailExistsForOtherUser(string $email, int $userId): bool
    {
        $user = User::where('email', '=', $email)->where('id', '!=', $userId)->first();
        return $user !== null;
    }

    public function createUser(array $userData, array $roleIds): int
    {
        // Crear nuevo usuario usando el modelo
        $user = new User([
            'nombre' => $userData['nombre'],
            'apellido' => $userData['apellido'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'telefono' => $userData['telefono'],
            'fecha_nacimiento' => $userData['fecha_nacimiento'],
            'estado' => $userData['estado'] ?? 1
        ]);

        $user->save();
        $userId = $user->getKey();

        // Asignar roles usando el método del modelo
        foreach ($roleIds as $roleId) {
            $user->assignRole((int)$roleId);
        }

        return $userId;
    }

    public function updateUser(int $id, array $userData, array $roleIds): bool
    {
        $user = User::find($id);
        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }

        // Actualizar datos del usuario
        foreach ($userData as $field => $value) {
            if ($value !== null && in_array($field, $user->getFillable())) {
                $user->$field = $value;
            }
        }

        $user->save();

        // Sincronizar roles
        $user->syncRoles($roleIds);

        return true;
    }

    /**
     * Actualizar solo los roles de un usuario
     */
    public function updateUserRoles(int $id, array $roleIds): bool
    {
        $user = User::find($id);
        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }
        // Sincronizar roles (elimina roles anteriores y asigna los nuevos)
        $user->syncRoles($roleIds);

        return true;
    }

    public function getUserPermissions(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }

        // Obtener permisos directos del usuario usando el mismo método que el modelo User
        return ModelHasPermissions::getPermissionsForModel('App\\Models\\User', $userId);
    }

    public function updateUserPermissions(int $id, array $permissionIds): bool
    {
        $user = User::find($id);
        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }

        // Sincronizar permisos (elimina permisos anteriores y asigna los nuevos)
        $user->syncPermissions($permissionIds);

        return true;
    }

    /**
     * Verificar si un usuario tiene dependencias que impiden su eliminación
     */
    private function checkUserDependencies(int $userId): array
    {
        $dependencies = [];
        
        try {
            // Verificar ventas como vendedor
            $ventas = DB::getInstance()->query("SELECT COUNT(*) as count FROM ventas WHERE vendedor_id = ?", [$userId])->fetch();
            if ($ventas->count > 0) {
                $dependencies[] = "Tiene {$ventas->count} venta(s) asociada(s) como vendedor";
            }
            
            // Verificar otras posibles dependencias
            $sesiones = DB::getInstance()->query("SELECT COUNT(*) as count FROM sesiones_activas WHERE usuario_id = ?", [$userId])->fetch();
            if ($sesiones->count > 0) {
                $dependencies[] = "Tiene {$sesiones->count} sesión(es) activa(s)";
            }
            
            $descargas = DB::getInstance()->query("SELECT COUNT(*) as count FROM descargas_libros WHERE usuario_id = ?", [$userId])->fetch();
            if ($descargas->count > 0) {
                $dependencies[] = "Tiene {$descargas->count} descarga(s) de libros registrada(s)";
            }
            
        } catch (Exception $e) {
            // Si hay error verificando, mejor ser conservador
            $dependencies[] = "Error verificando dependencias: " . $e->getMessage();
        }
        
        return $dependencies;
    }

    public function deleteUser(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }
        
        // Verificar dependencias antes de intentar eliminar
        $dependencies = $this->checkUserDependencies($id);
        if (!empty($dependencies)) {
            $message = "No se puede eliminar el usuario '{$user->nombre}' porque:\n";
            foreach ($dependencies as $dependency) {
                $message .= "• " . $dependency . "\n";
            }
            $message .= "\nPrimero debe resolver estas dependencias o reasignar los registros a otro usuario.";
            throw new Exception($message);
        }
        
        try {
            // Sin transacciones manuales, dejar que el modelo se encargue
            $user->syncRoles([]);
            $user->syncPermissions([]);
            $user->delete();
        } catch (Exception $e) {
            // Capturar errores de clave foránea y hacer el mensaje más amigable
            if (strpos($e->getMessage(), 'foreign key constraint') !== false || 
                strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                throw new Exception("No se puede eliminar el usuario '{$user->nombre}' porque está referenciado en otros registros del sistema. Contacte al administrador del sistema.");
            }
            throw new Exception('Error al eliminar usuario: ' . $e->getMessage());
        }
        
        return true;
    }

    public function changeUserStatus(int $id, string $status): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        // Convertir string a integer para la base de datos
        $user->estado = $status === 'activo' ? 1 : 0;
        $user->save();

        return true;
    }

    // === MÉTODOS PARA GESTIÓN DE PERMISOS ===

    public function getAllPermissions(): array
    {
        return Permission::all();
    }

    public function getPermissionsForRole(int $roleId): array
    {
        // Usar el modelo Role para obtener permisos
        $role = Role::find($roleId);
        return $role ? $role->permissions() : [];
    }

    public function syncRolePermissions(int $roleId, array $permissionIds): bool
    {
        // Verificar que el rol existe usando el modelo
        $role = Role::find($roleId);
        if (!$role) {
            throw new Exception('Rol no encontrado');
        }

        // Usar el modelo RoleHasPermissions para sincronizar
        return RoleHasPermissions::syncPermissionsForRole($roleId, $permissionIds);
    }
}
