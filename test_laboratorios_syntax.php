<?php

/**
 * Script simple de verificación del módulo Laboratorios
 * 
 * Este script verifica:
 * - Existencia de las clases del módulo
 * - Validación de sintaxis
 * - Estructura básica de métodos
 */

require_once 'bootstrap.php';

class LaboratorioSyntaxTest
{
    private $errors = [];
    private $warnings = [];
    
    public function runTests()
    {
        echo "<h1>🧪 Verificación de Sintaxis - Módulo Laboratorios</h1>\n";
        echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px;'>\n";
        
        $this->testClassExistence();
        $this->testSyntax();
        $this->testMethodStructure();
        $this->showResults();
        
        echo "</div>\n";
        echo "<h2>✅ Verificación completada</h2>\n";
    }
    
    private function testClassExistence()
    {
        echo "<h3>1. ⚙️ Verificando existencia de clases</h3>\n";
        
        $classes = [
            'App\\Models\\Laboratorio' => 'c:/xampp/htdocs/TECH-HOME/App/Models/Laboratorio.php',
            'App\\Services\\LaboratorioService' => 'c:/xampp/htdocs/TECH-HOME/App/Services/LaboratorioService.php',
            'App\\Controllers\\LaboratorioController' => 'c:/xampp/htdocs/TECH-HOME/App/Controllers/LaboratorioController.php'
        ];
        
        foreach ($classes as $className => $filePath) {
            if (file_exists($filePath)) {
                echo "✅ Archivo encontrado: {$className}\n";
                
                // Verificar que la clase se puede cargar
                try {
                    if (class_exists($className)) {
                        echo "✅ Clase cargada correctamente: {$className}\n";
                    } else {
                        $this->errors[] = "Clase no se pudo cargar: {$className}";
                        echo "❌ Clase no se pudo cargar: {$className}\n";
                    }
                } catch (Exception $e) {
                    $this->errors[] = "Error cargando clase {$className}: " . $e->getMessage();
                    echo "❌ Error cargando clase {$className}: " . $e->getMessage() . "\n";
                }
            } else {
                $this->errors[] = "Archivo no encontrado: {$filePath}";
                echo "❌ Archivo no encontrado: {$filePath}\n";
            }
        }
        echo "\n";
    }
    
    private function testSyntax()
    {
        echo "<h3>2. 📝 Verificando sintaxis PHP</h3>\n";
        
        $files = [
            'App/Models/Laboratorio.php',
            'App/Services/LaboratorioService.php',
            'App/Controllers/LaboratorioController.php'
        ];
        
        foreach ($files as $file) {
            $fullPath = "c:/xampp/htdocs/TECH-HOME/{$file}";
            if (file_exists($fullPath)) {
                $output = [];
                $return_var = 0;
                exec("php -l \"{$fullPath}\" 2>&1", $output, $return_var);
                
                if ($return_var === 0) {
                    echo "✅ Sintaxis correcta: {$file}\n";
                } else {
                    $this->errors[] = "Error de sintaxis en {$file}: " . implode(' ', $output);
                    echo "❌ Error de sintaxis en {$file}: " . implode(' ', $output) . "\n";
                }
            }
        }
        echo "\n";
    }
    
    private function testMethodStructure()
    {
        echo "<h3>3. 🏗️ Verificando estructura de métodos</h3>\n";
        
        try {
            // Test del modelo
            if (class_exists('App\\Models\\Laboratorio')) {
                $reflection = new ReflectionClass('App\\Models\\Laboratorio');
                $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                
                $expectedMethods = [
                    'getParticipantes', 'getComponentesUtilizados', 'getTecnologias',
                    'getProgreso', 'getClaseEstado', 'getClaseNivel'
                ];
                
                $foundMethods = array_map(function($method) { return $method->name; }, $methods);
                
                foreach ($expectedMethods as $method) {
                    if (in_array($method, $foundMethods)) {
                        echo "✅ Método encontrado en Laboratorio: {$method}\n";
                    } else {
                        $this->warnings[] = "Método faltante en Laboratorio: {$method}";
                        echo "⚠️ Método faltante en Laboratorio: {$method}\n";
                    }
                }
            }
            
            // Test del servicio
            if (class_exists('App\\Services\\LaboratorioService')) {
                $reflection = new ReflectionClass('App\\Services\\LaboratorioService');
                $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                
                $expectedMethods = [
                    'getAllLaboratorios', 'getLaboratorioById', 'createLaboratorio',
                    'updateLaboratorio', 'deleteLaboratorio', 'searchLaboratorios'
                ];
                
                $foundMethods = array_map(function($method) { return $method->name; }, $methods);
                
                foreach ($expectedMethods as $method) {
                    if (in_array($method, $foundMethods)) {
                        echo "✅ Método encontrado en LaboratorioService: {$method}\n";
                    } else {
                        $this->warnings[] = "Método faltante en LaboratorioService: {$method}";
                        echo "⚠️ Método faltante en LaboratorioService: {$method}\n";
                    }
                }
            }
            
            // Test del controlador
            if (class_exists('App\\Controllers\\LaboratorioController')) {
                $reflection = new ReflectionClass('App\\Controllers\\LaboratorioController');
                $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                
                $expectedMethods = [
                    'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
                ];
                
                $foundMethods = array_map(function($method) { return $method->name; }, $methods);
                
                foreach ($expectedMethods as $method) {
                    if (in_array($method, $foundMethods)) {
                        echo "✅ Método encontrado en LaboratorioController: {$method}\n";
                    } else {
                        $this->warnings[] = "Método faltante en LaboratorioController: {$method}";
                        echo "⚠️ Método faltante en LaboratorioController: {$method}\n";
                    }
                }
            }
            
        } catch (Exception $e) {
            $this->errors[] = "Error verificando métodos: " . $e->getMessage();
            echo "❌ Error verificando métodos: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function showResults()
    {
        echo "<h3>📊 Resumen de Resultados</h3>\n";
        
        if (empty($this->errors) && empty($this->warnings)) {
            echo "🎉 ¡Perfecto! El módulo Laboratorios está correctamente implementado.\n";
            echo "✅ 0 errores encontrados\n";
            echo "✅ 0 advertencias encontradas\n";
        } else {
            if (!empty($this->errors)) {
                echo "❌ Errores encontrados (" . count($this->errors) . "):\n";
                foreach ($this->errors as $error) {
                    echo "   • {$error}\n";
                }
                echo "\n";
            }
            
            if (!empty($this->warnings)) {
                echo "⚠️ Advertencias encontradas (" . count($this->warnings) . "):\n";
                foreach ($this->warnings as $warning) {
                    echo "   • {$warning}\n";
                }
                echo "\n";
            }
        }
        
        echo "📁 Archivos verificados:\n";
        echo "   • App/Models/Laboratorio.php\n";
        echo "   • App/Services/LaboratorioService.php\n";
        echo "   • App/Controllers/LaboratorioController.php\n";
        echo "\n";
        
        echo "🚀 Estado del módulo: " . (empty($this->errors) ? "LISTO PARA USO" : "REQUIERE CORRECCIONES") . "\n";
    }
}

// ==================== EJECUCIÓN DE PRUEBAS ====================

try {
    $test = new LaboratorioSyntaxTest();
    $test->runTests();
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
