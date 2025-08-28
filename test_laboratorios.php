<?php

/**
 * Script de prueba para el módulo Laboratorios
 * 
 * Este script verifica:
 * - Conexión a la base de datos
 * - Existencia de la tabla laboratorios
 * - Funcionamiento del modelo Laboratorio
 * - Funcionamiento del servicio LaboratorioService
 * - Pruebas básicas de CRUD
 */

require_once 'bootstrap.php';

use Core\DB;
use App\Models\Laboratorio;
use App\Services\LaboratorioService;

class LaboratorioTestSuite
{
    private $db;
    private $service;
    
    public function __construct()
    {
        $this->db = DB::getInstance();
        $this->service = new LaboratorioService();
    }
    
    public function runAllTests()
    {
        echo "<h1>🧪 Suite de Pruebas - Módulo Laboratorios</h1>\n";
        echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px;'>\n";
        
        $this->testDatabaseConnection();
        $this->testTableStructure();
        $this->testBasicModelFunctionality();
        $this->testServiceFunctionality();
        $this->testJSONFieldHandling();
        $this->testBusinessLogic();
        
        echo "</div>\n";
        echo "<h2>✅ Suite de pruebas completada</h2>\n";
    }
    
    private function testDatabaseConnection()
    {
        echo "<h3>1. ⚡ Probando conexión a la base de datos</h3>\n";
        
        try {
            $pdo = $this->db->getConnection();
            echo "✅ Conexión exitosa\n";
            echo "📊 Base de datos: " . $pdo->query("SELECT DATABASE()")->fetchColumn() . "\n";
        } catch (Exception $e) {
            echo "❌ Error de conexión: " . $e->getMessage() . "\n";
            return false;
        }
        
        echo "\n";
        return true;
    }
    
    private function testTableStructure()
    {
        echo "<h3>2. 🏗️ Verificando estructura de tabla laboratorios</h3>\n";
        
        try {
            // Verificar existencia de tabla
            $stmt = $this->db->getConnection()->query("SHOW TABLES LIKE 'laboratorios'");
            if ($stmt->rowCount() === 0) {
                echo "❌ La tabla 'laboratorios' no existe\n";
                return false;
            }
            echo "✅ Tabla 'laboratorios' encontrada\n";
            
            // Verificar estructura
            $stmt = $this->db->getConnection()->query("DESCRIBE laboratorios");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $expectedColumns = [
                'id', 'nombre', 'descripcion', 'objetivos', 'categoria_id',
                'docente_responsable_id', 'participantes', 'componentes_utilizados',
                'tecnologias', 'resultado', 'conclusiones', 'nivel_dificultad',
                'duracion_dias', 'fecha_inicio', 'fecha_fin', 'estado',
                'publico', 'destacado', 'fecha_creacion', 'fecha_actualizacion'
            ];
            
            $missingColumns = array_diff($expectedColumns, $columns);
            
            if (!empty($missingColumns)) {
                echo "❌ Columnas faltantes: " . implode(', ', $missingColumns) . "\n";
                return false;
            }
            
            echo "✅ Estructura de tabla correcta (" . count($columns) . " columnas)\n";
            echo "📋 Columnas: " . implode(', ', $columns) . "\n";
            
        } catch (Exception $e) {
            echo "❌ Error verificando estructura: " . $e->getMessage() . "\n";
            return false;
        }
        
        echo "\n";
        return true;
    }
    
    private function testBasicModelFunctionality()
    {
        echo "<h3>3. 🏷️ Probando funcionalidad básica del modelo</h3>\n";
        
        try {
            // Test 1: Creación de instancia
            $laboratorio = new Laboratorio();
            echo "✅ Instancia de modelo creada\n";
            
            // Test 2: Atributos básicos
            $laboratorio->nombre = 'Test Laboratorio PHP';
            $laboratorio->descripcion = 'Laboratorio de prueba para testing';
            echo "✅ Asignación de atributos básicos\n";
            
            // Test 3: Métodos de utilidad
            $laboratorio->nivel_dificultad = 'Intermedio';
            $claseNivel = $laboratorio->getClaseNivel();
            echo "✅ Método getClaseNivel(): {$claseNivel}\n";
            
            $laboratorio->estado = 'En Progreso';
            $claseEstado = $laboratorio->getClaseEstado();
            echo "✅ Método getClaseEstado(): {$claseEstado}\n";
            
            // Test 4: Métodos JSON
            $participantes = [1, 2, 3];
            $laboratorio->participantes = json_encode($participantes);
            $participantesArray = $laboratorio->getParticipantes();
            echo "✅ Método getParticipantes(): " . count($participantesArray) . " participantes\n";
            
            // Test 5: Validación
            $errores = $laboratorio->validarDatos();
            echo "✅ Método validarDatos(): " . count($errores) . " errores encontrados\n";
            
        } catch (Exception $e) {
            echo "❌ Error en modelo: " . $e->getMessage() . "\n";
            return false;
        }
        
        echo "\n";
        return true;
    }
    
    private function testServiceFunctionality()
    {
        echo "<h3>4. 🔧 Probando funcionalidad del servicio</h3>\n";
        
        try {
            // Test 1: Obtener todos los laboratorios
            $laboratorios = $this->service->getAllLaboratorios();
            echo "✅ getAllLaboratorios(): " . count($laboratorios) . " laboratorios encontrados\n";
            
            // Test 2: Obtener categorías
            $categorias = $this->service->getAllCategories();
            echo "✅ getAllCategories(): " . count($categorias) . " categorías encontradas\n";
            
            // Test 3: Obtener docentes
            $docentes = $this->service->getAllDocentes();
            echo "✅ getAllDocentes(): " . count($docentes) . " docentes encontrados\n";
            
            // Test 4: Estadísticas generales
            $estadisticas = $this->service->getGeneralStats();
            echo "✅ getGeneralStats(): estadísticas generadas\n";
            if (isset($estadisticas['total_laboratorios'])) {
                echo "   📊 Total laboratorios: " . $estadisticas['total_laboratorios'] . "\n";
            }
            if (isset($estadisticas['publicos'])) {
                echo "   📊 Públicos: " . $estadisticas['publicos'] . "\n";
            }
            
            // Test 5: Búsqueda
            $filtros = ['buscar' => 'test', 'estado' => 'todos'];
            $resultados = $this->service->searchLaboratorios($filtros);
            echo "✅ searchLaboratorios(): " . count($resultados) . " resultados para 'test'\n";
            
        } catch (Exception $e) {
            echo "❌ Error en servicio: " . $e->getMessage() . "\n";
            return false;
        }
        
        echo "\n";
        return true;
    }
    
    private function testJSONFieldHandling()
    {
        echo "<h3>5. 📝 Probando manejo de campos JSON</h3>\n";
        
        try {
            $laboratorio = new Laboratorio();
            
            // Test participantes
            $participantesTest = [1, 2, 3, 4];
            $laboratorio->participantes = json_encode($participantesTest);
            $participantesRecuperados = $laboratorio->getParticipantes();
            echo "✅ Participantes JSON: " . count($participantesRecuperados) . " participantes\n";
            
            // Test componentes utilizados
            $componentesTest = ['Arduino Uno', 'Sensor DHT22', 'LED RGB'];
            $laboratorio->componentes_utilizados = json_encode($componentesTest);
            $componentesRecuperados = $laboratorio->getComponentesUtilizados();
            echo "✅ Componentes JSON: " . count($componentesRecuperados) . " componentes\n";
            
            // Test tecnologías
            $tecnologiasTest = ['C++', 'Python', 'JavaScript'];
            $laboratorio->tecnologias = json_encode($tecnologiasTest);
            $tecnologiasRecuperadas = $laboratorio->getTecnologias();
            echo "✅ Tecnologías JSON: " . count($tecnologiasRecuperadas) . " tecnologías\n";
            
            // Test agregar y remover participante
            $result1 = $laboratorio->agregarParticipante(5);
            echo "✅ Agregar participante: " . ($result1 ? 'éxito' : 'ya existe') . "\n";
            
            $result2 = $laboratorio->removerParticipante(1);
            echo "✅ Remover participante: " . ($result2 ? 'éxito' : 'no existe') . "\n";
            
        } catch (Exception $e) {
            echo "❌ Error en manejo JSON: " . $e->getMessage() . "\n";
            return false;
        }
        
        echo "\n";
        return true;
    }
    
    private function testBusinessLogic()
    {
        echo "<h3>6. 💼 Probando lógica de negocio</h3>\n";
        
        try {
            $laboratorio = new Laboratorio([
                'nombre' => 'Test Business Logic',
                'descripcion' => 'Prueba de lógica de negocio',
                'estado' => 'En Progreso',
                'fecha_inicio' => date('Y-m-d'),
                'fecha_fin' => date('Y-m-d', strtotime('+7 days')),
                'nivel_dificultad' => 'Avanzado',
                'publico' => 1,
                'destacado' => 0
            ]);
            
            // Test cálculo de progreso
            $progreso = $laboratorio->getProgreso();
            echo "✅ Cálculo de progreso: {$progreso}%\n";
            
            // Test duración formateada
            $duracion = $laboratorio->getDuracionFormateada();
            echo "✅ Duración formateada: {$duracion}\n";
            
            // Test estado activo
            $esActivo = $laboratorio->estaActivo();
            echo "✅ Estado activo: " . ($esActivo ? 'sí' : 'no') . "\n";
            
            // Test puede ver (público)
            $puedeVer = $laboratorio->puedeVer(1);
            echo "✅ Puede ver (usuario 1): " . ($puedeVer ? 'sí' : 'no') . "\n";
            
            // Test métodos estáticos
            $contadorEstados = Laboratorio::contarPorEstado();
            echo "✅ Contador por estado: " . count($contadorEstados) . " estados\n";
            
            $contadorNiveles = Laboratorio::contarPorNivel();
            echo "✅ Contador por nivel: " . count($contadorNiveles) . " niveles\n";
            
        } catch (Exception $e) {
            echo "❌ Error en lógica de negocio: " . $e->getMessage() . "\n";
            return false;
        }
        
        echo "\n";
        return true;
    }
}

// ==================== EJECUCIÓN DE PRUEBAS ====================

try {
    $testSuite = new LaboratorioTestSuite();
    $testSuite->runAllTests();
} catch (Exception $e) {
    echo "<h2>❌ Error crítico en las pruebas</h2>\n";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>\n";
    echo "<p><strong>Stack trace:</strong></p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1, h2, h3 { color: #333; }
    h1 { color: #2c5282; }
    h3 { color: #2d3748; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
    pre { background: #f7fafc; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>
