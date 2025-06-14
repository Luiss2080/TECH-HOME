<?php
/**
 * Modelo para gestión de usuarios - Tech Home
 * Maneja todas las operaciones CRUD y consultas relacionadas con usuarios
 */

require_once __DIR__ . '/../config/database.php';

class UsuarioModelo {
    private $pdo;
    
    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }
    
    /**
     * Obtener todos los roles activos
     */
    public function obtenerRoles() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, nombre, descripcion 
                FROM roles 
                WHERE estado = 1 
                ORDER BY nombre ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener roles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function crear($datos) {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios (
                    nombre, apellido, email, password, rol_id, telefono, fecha_nacimiento, estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $resultado = $stmt->execute([
                $datos['nombre'],
                $datos['apellido'],
                $datos['email'],
                $datos['password'], // Ya debe venir hasheado desde el controlador
                $datos['rol_id'],
                $datos['telefono'] ?? null,
                $datos['fecha_nacimiento'] ?? null,
                $datos['estado'] ? 1 : 0
            ]);
            
            if ($resultado) {
                $user_id = $this->pdo->lastInsertId();
                $this->pdo->commit();
                return [
                    'success' => true,
                    'user_id' => $user_id,
                    'message' => 'Usuario creado exitosamente'
                ];
            } else {
                $this->pdo->rollback();
                return [
                    'success' => false,
                    'message' => 'Error al crear el usuario'
                ];
            }
            
        } catch (PDOException $e) {
            $this->pdo->rollback();
            error_log("Error al crear usuario: " . $e->getMessage());
            
            // Verificar errores específicos
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'email') !== false) {
                    return [
                        'success' => false,
                        'message' => 'Ya existe un usuario con este email'
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'Error de base de datos al crear el usuario'
            ];
        }
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.*, r.nombre as rol_nombre, r.descripcion as rol_descripcion,
                       CONCAT(u.nombre, ' ', u.apellido) as nombre_completo
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                WHERE u.id = ?
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener usuario por email (para login)
     */
    public function obtenerPorEmail($email) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.*, r.nombre as rol_nombre, r.descripcion as rol_descripcion,
                       CONCAT(u.nombre, ' ', u.apellido) as nombre_completo
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                WHERE u.email = ? AND u.estado = 1
            ");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar($user_id, $datos) {
        try {
            $this->pdo->beginTransaction();
            
            // Determinar si se actualiza la contraseña
            if (!empty($datos['password'])) {
                $sql = "
                    UPDATE usuarios SET 
                        nombre = ?, 
                        apellido = ?,
                        email = ?, 
                        password = ?,
                        rol_id = ?, 
                        telefono = ?,
                        fecha_nacimiento = ?,
                        estado = ?,
                        fecha_actualizacion = CURRENT_TIMESTAMP
                    WHERE id = ?
                ";
                $params = [
                    $datos['nombre'],
                    $datos['apellido'],
                    $datos['email'],
                    $datos['password'], // Ya viene hasheado
                    $datos['rol_id'],
                    $datos['telefono'] ?? null,
                    $datos['fecha_nacimiento'] ?? null,
                    $datos['estado'] ? 1 : 0,
                    $user_id
                ];
            } else {
                $sql = "
                    UPDATE usuarios SET 
                        nombre = ?, 
                        apellido = ?,
                        email = ?, 
                        rol_id = ?, 
                        telefono = ?,
                        fecha_nacimiento = ?,
                        estado = ?,
                        fecha_actualizacion = CURRENT_TIMESTAMP
                    WHERE id = ?
                ";
                $params = [
                    $datos['nombre'],
                    $datos['apellido'],
                    $datos['email'],
                    $datos['rol_id'],
                    $datos['telefono'] ?? null,
                    $datos['fecha_nacimiento'] ?? null,
                    $datos['estado'] ? 1 : 0,
                    $user_id
                ];
            }
            
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute($params);
            
            if ($resultado) {
                $this->pdo->commit();
                return [
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente'
                ];
            } else {
                $this->pdo->rollback();
                return [
                    'success' => false,
                    'message' => 'Error al actualizar el usuario'
                ];
            }
            
        } catch (PDOException $e) {
            $this->pdo->rollback();
            error_log("Error al actualizar usuario: " . $e->getMessage());
            
            // Verificar errores específicos
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'email') !== false) {
                    return [
                        'success' => false,
                        'message' => 'Ya existe un usuario con este email'
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'Error de base de datos al actualizar el usuario'
            ];
        }
    }
    
    /**
     * Eliminar usuario (soft delete)
     */
    public function eliminar($user_id) {
        try {
            // Verificar si es el último administrador
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total_admins
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE r.nombre = 'Administrador' AND u.estado = 1 AND u.id != ?
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total_admins'] == 0) {
                // Verificar si el usuario a eliminar es administrador
                $stmt = $this->pdo->prepare("
                    SELECT r.nombre FROM usuarios u
                    INNER JOIN roles r ON u.rol_id = r.id
                    WHERE u.id = ?
                ");
                $stmt->execute([$user_id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuario && $usuario['nombre'] == 'Administrador') {
                    return [
                        'success' => false,
                        'message' => 'No se puede eliminar el último administrador del sistema'
                    ];
                }
            }
            
            // Verificar si el usuario tiene ventas asociadas
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM ventas v
                WHERE v.cliente_id = ? OR v.vendedor_id = ?
            ");
            $stmt->execute([$user_id, $user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el usuario porque tiene ventas asociadas'
                ];
            }
            
            // Verificar si el usuario tiene progreso de cursos
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM progreso_estudiantes p
                WHERE p.estudiante_id = ?
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el usuario porque tiene progreso de cursos asociado'
                ];
            }
            
            // Desactivar el usuario
            $stmt = $this->pdo->prepare("UPDATE usuarios SET estado = 0 WHERE id = ?");
            $resultado = $stmt->execute([$user_id]);
            
            if ($resultado) {
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al eliminar el usuario'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos al eliminar el usuario'
            ];
        }
    }
    
    /**
     * Listar usuarios con filtros y paginación
     */
    public function listar($filtros = [], $limite = 20, $offset = 0) {
        try {
            $where = ["u.id IS NOT NULL"];
            $params = [];
            
            // Aplicar filtros
            if (!empty($filtros['busqueda'])) {
                $where[] = "(u.nombre LIKE ? OR u.apellido LIKE ? OR u.email LIKE ? OR CONCAT(u.nombre, ' ', u.apellido) LIKE ?)";
                $busqueda = "%{$filtros['busqueda']}%";
                $params[] = $busqueda;
                $params[] = $busqueda;
                $params[] = $busqueda;
                $params[] = $busqueda;
            }
            
            if (!empty($filtros['rol'])) {
                $where[] = "r.nombre = ?";
                $params[] = $filtros['rol'];
            }
            
            if (!empty($filtros['rol_id'])) {
                $where[] = "u.rol_id = ?";
                $params[] = $filtros['rol_id'];
            }
            
            if (isset($filtros['estado'])) {
                $where[] = "u.estado = ?";
                $params[] = $filtros['estado'] ? 1 : 0;
            }
            
            $whereClause = implode(" AND ", $where);
            $orderBy = $filtros['orden'] ?? 'u.fecha_creacion DESC';
            
            // Consulta principal
            $sql = "
                SELECT u.*, r.nombre as rol_nombre, r.descripcion as rol_descripcion,
                       CONCAT(u.nombre, ' ', u.apellido) as nombre_completo
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $limite;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Contar total de registros para paginación
            $sqlCount = "
                SELECT COUNT(*) as total
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                WHERE {$whereClause}
            ";
            $paramsCount = array_slice($params, 0, -2); // Remover LIMIT y OFFSET
            $stmtCount = $this->pdo->prepare($sqlCount);
            $stmtCount->execute($paramsCount);
            $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
            
            return [
                'usuarios' => $usuarios,
                'total' => $total,
                'tiene_siguiente' => ($offset + $limite) < $total,
                'tiene_anterior' => $offset > 0
            ];
            
        } catch (PDOException $e) {
            error_log("Error al listar usuarios: " . $e->getMessage());
            return [
                'usuarios' => [],
                'total' => 0,
                'tiene_siguiente' => false,
                'tiene_anterior' => false
            ];
        }
    }
    
    /**
     * Obtener usuarios más activos
     */
    public function obtenerMasActivos($limite = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.*, r.nombre as rol_nombre,
                       CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                       COALESCE(COUNT(DISTINCT pe.id), 0) as total_progreso_cursos,
                       COALESCE(COUNT(DISTINCT v.id), 0) as total_ventas,
                       u.fecha_actualizacion as ultima_actividad
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                LEFT JOIN progreso_estudiantes pe ON u.id = pe.estudiante_id
                LEFT JOIN ventas v ON (u.id = v.cliente_id OR u.id = v.vendedor_id)
                WHERE u.estado = 1
                GROUP BY u.id
                ORDER BY (total_progreso_cursos + total_ventas) DESC, u.fecha_actualizacion DESC
                LIMIT ?
            ");
            $stmt->execute([$limite]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios más activos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener usuarios recientes
     */
    public function obtenerUsuariosRecientes($limite = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.*, r.nombre as rol_nombre,
                       CONCAT(u.nombre, ' ', u.apellido) as nombre_completo
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                WHERE u.estado = 1
                ORDER BY u.fecha_creacion DESC
                LIMIT ?
            ");
            $stmt->execute([$limite]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios recientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Validar si existe un usuario con el mismo email
     */
    public function existeEmail($email, $user_id = null) {
        try {
            if ($user_id) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND id != ?");
                $stmt->execute([$email, $user_id]);
            } else {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
            }
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar email de usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener estadísticas de usuarios
     */
    public function obtenerEstadisticas() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_usuarios,
                    COUNT(CASE WHEN u.estado = 1 THEN 1 END) as usuarios_activos,
                    COUNT(CASE WHEN u.estado = 0 THEN 1 END) as usuarios_inactivos,
                    COUNT(CASE WHEN r.nombre = 'Administrador' THEN 1 END) as administradores,
                    COUNT(CASE WHEN r.nombre = 'Docente' THEN 1 END) as docentes,
                    COUNT(CASE WHEN r.nombre = 'Estudiante' THEN 1 END) as estudiantes,
                    COUNT(CASE WHEN r.nombre = 'Invitado' THEN 1 END) as invitados,
                    COUNT(CASE WHEN r.nombre = 'Vendedor' THEN 1 END) as vendedores,
                    COUNT(CASE WHEN u.fecha_creacion >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as nuevos_mes
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener actividad de usuarios (para reportes)
     */
    public function obtenerActividadUsuarios($user_id = null, $limite = 10) {
        try {
            $whereClause = $user_id ? "WHERE u.id = ?" : "";
            $params = $user_id ? [$user_id, $limite] : [$limite];
            
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.nombre, u.apellido, u.email, r.nombre as rol,
                       CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                       COALESCE(COUNT(DISTINCT pe.id), 0) as cursos_inscritos,
                       COALESCE(COUNT(CASE WHEN pe.completado = 1 THEN 1 END), 0) as cursos_completados,
                       COALESCE(COUNT(DISTINCT v.id), 0) as total_ventas,
                       COALESCE(SUM(CASE WHEN v.cliente_id = u.id THEN v.total ELSE 0 END), 0) as monto_compras,
                       COALESCE(SUM(CASE WHEN v.vendedor_id = u.id THEN v.total ELSE 0 END), 0) as monto_ventas_realizadas,
                       MAX(pe.ultima_actividad) as ultima_actividad_curso,
                       u.fecha_actualizacion as ultima_actualizacion
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                LEFT JOIN progreso_estudiantes pe ON u.id = pe.estudiante_id
                LEFT JOIN ventas v ON (u.id = v.cliente_id OR u.id = v.vendedor_id)
                {$whereClause}
                GROUP BY u.id
                ORDER BY cursos_inscritos DESC, total_ventas DESC, u.fecha_actualizacion DESC
                LIMIT ?
            ");
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener actividad de usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar usuarios por término
     */
    public function buscar($termino, $limite = 10) {
        $filtros = ['busqueda' => $termino];
        $resultado = $this->listar($filtros, $limite, 0);
        
        return $resultado['usuarios'];
    }
    
    /**
     * Actualizar último acceso
     */
    public function actualizarUltimoAcceso($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE usuarios 
                SET fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Error al actualizar último acceso: " . $e->getMessage());
            return false;
        }
    }
}
?>