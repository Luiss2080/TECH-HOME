<?php

namespace App\Services;

use App\Models\DashboardStats;
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
        $usersCount = $db->query("SELECT COUNT(*) as count FROM model_has_roles WHERE role_id = ?", [$id])->fetch();
        if ($usersCount['count'] > 0) {
            throw new Exception('No se puede eliminar el rol porque tiene usuarios asignados');
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

    public function deleteUser(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }

        // El modelo debería manejar las relaciones en cascada
        return $user->delete();
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
