<?php
/**
 * Configuración de base de datos para Tech Home Bolivia
 * Configuración actualizada para la nueva estructura
 */

class Database {
    private $host = "localhost";
    private $dbname = "tech_home";
    private $username = "root";
    private $password = "";
    private $pdo = null;
    
    public function getConnection() {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];
                
                $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
                
                // Verificar que las tablas existen
                $this->verificarEstructura();
                
            } catch (PDOException $e) {
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        
        return $this->pdo;
    }
    
    /**
     * Verificar que existen las tablas necesarias
     */
    private function verificarEstructura() {
        try {
            $tablasRequeridas = [
                'usuarios', 'roles', 'categorias', 'cursos', 'libros', 
                'componentes', 'ventas', 'detalle_ventas', 'progreso_estudiantes',
                'descargas_libros', 'configuraciones', 'sesiones_activas', 
                'acceso_invitados', 'intentos_login'
            ];
            
            $stmt = $this->pdo->query("SHOW TABLES");
            $tablasExistentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $tablasFaltantes = array_diff($tablasRequeridas, $tablasExistentes);
            
            if (!empty($tablasFaltantes)) {
                error_log("[DATABASE WARNING] Tablas faltantes: " . implode(', ', $tablasFaltantes));
                // No lanzar excepción, solo registrar el warning
            }
            
        } catch (PDOException $e) {
            error_log("Error verificando estructura de BD: " . $e->getMessage());
        }
    }
    
    /**
     * Método para probar la conexión
     */
    public function testConnection() {
        try {
            $pdo = $this->getConnection();
            $stmt = $pdo->query("SELECT 1 as test");
            $result = $stmt->fetch();
            return $result['test'] === 1;
        } catch (Exception $e) {
            error_log("Test de conexión fallido: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener información de la base de datos
     */
    public function getInfo() {
        try {
            $pdo = $this->getConnection();
            
            // Información básica
            $info = [
                'host' => $this->host,
                'database' => $this->dbname,
                'charset' => 'utf8mb4',
                'connection_status' => 'Conectado'
            ];
            
            // Contar registros en tablas principales
            $tablas = ['usuarios', 'roles', 'cursos', 'libros', 'componentes'];
            foreach ($tablas as $tabla) {
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM `$tabla`");
                    $result = $stmt->fetch();
                    $info['tablas'][$tabla] = $result['total'];
                } catch (PDOException $e) {
                    $info['tablas'][$tabla] = 'N/A';
                }
            }
            
            return $info;
            
        } catch (Exception $e) {
            return [
                'host' => $this->host,
                'database' => $this->dbname,
                'connection_status' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}

// Función global para obtener la conexión (opcional)
function getDBConnection() {
    static $database = null;
    if ($database === null) {
        $database = new Database();
    }
    return $database->getConnection();
}
?>