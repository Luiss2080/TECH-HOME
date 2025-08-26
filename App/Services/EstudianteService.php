<?php

namespace App\Services;

use App\Models\EstudianteModelo;
use Exception;

/**
 * EstudianteService - Lógica de negocio simplificada para estudiantes
 */
class EstudianteService
{
    /**
     * Obtiene datos del dashboard del estudiante
     */
    public function getDashboardData(int $estudianteId): array
    {
        try {
            $data = EstudianteModelo::getDashboardStats($estudianteId);
            return $data;
        } catch (Exception $e) {
            return $this->getDefaultDashboardData();
        }
    }

    /**
     * Datos por defecto en caso de error
     */
    private function getDefaultDashboardData(): array
    {
        return [
            'metricas_estudiante' => [
                'cursos_inscritos' => 0,
                'progreso_general' => 0,
                'certificados_obtenidos' => 0,
                'tiempo_estudio_total' => 0
            ],
            'cursos_actuales' => [],
            'libros_disponibles' => [],
            'actividad_reciente' => [],
            'progreso_cursos' => []
        ];
    }

    /**
     * Obtiene cursos del estudiante
     */
    public function getCursosInscritos(int $estudianteId): array
    {
        return EstudianteModelo::getCursosConProgreso($estudianteId);
    }

    /**
     * Obtiene libros disponibles para el estudiante
     */
    public function getLibrosDisponibles(): array
    {
        return EstudianteModelo::getLibrosActivos();
    }

    /**
     * Registra descarga de libro
     */
    public function registrarDescarga(int $estudianteId, int $libroId, string $ip): bool
    {
        try {
            return EstudianteModelo::registrarDescargaLibro($estudianteId, $libroId, $ip);
        } catch (Exception $e) {
            throw new Exception("Error al registrar descarga: " . $e->getMessage());
        }
    }

    /**
     * Obtiene progreso detallado de cursos
     */
    public function getProgresoDetallado(int $estudianteId): array
    {
        return EstudianteModelo::getProgresoCompleto($estudianteId);
    }

    /**
     * Actualiza progreso de un curso
     */
    public function actualizarProgreso(int $estudianteId, int $cursoId, float $progreso): bool
    {
        try {
            return EstudianteModelo::actualizarProgresoEstudiante($estudianteId, $cursoId, $progreso);
        } catch (Exception $e) {
            throw new Exception("Error al actualizar progreso: " . $e->getMessage());
        }
    }

    /**
     * Obtiene métricas para AJAX
     */
    public function getMetricasAjax(int $estudianteId, string $tipo = 'general'): array
    {
        switch ($tipo) {
            case 'cursos':
                return EstudianteModelo::getMetricasCursos($estudianteId);
            case 'progreso':
                return EstudianteModelo::getMetricasProgreso($estudianteId);
            default:
                return EstudianteModelo::getMetricasGenerales($estudianteId);
        }
    }

    /**
     * Valida acceso del estudiante a un curso
     */
    public function validarAccesoCurso(int $estudianteId, int $cursoId): bool
    {
        return EstudianteModelo::tieneAccesoCurso($estudianteId, $cursoId);
    }

    /**
     * Obtiene estadísticas resumidas del estudiante
     */
    public function getEstadisticasResumen(int $estudianteId): array
    {
        try {
            $metricas = EstudianteModelo::getMetricasGenerales($estudianteId);
            $cursos = EstudianteModelo::getCursosConProgreso($estudianteId);
            
            return [
                'metricas' => $metricas,
                'cursos_activos' => count(array_filter($cursos, fn($c) => $c['estado'] !== 'Completado')),
                'cursos_completados' => count(array_filter($cursos, fn($c) => $c['estado'] === 'Completado')),
                'promedio_calificaciones' => $this->calcularPromedioCalificaciones($cursos),
                'tiempo_total_minutos' => array_sum(array_column($cursos, 'tiempo_estudiado'))
            ];
        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }

    /**
     * Calcula el promedio de calificaciones (simulado por ahora)
     */
    private function calcularPromedioCalificaciones(array $cursos): float
    {
        if (empty($cursos)) return 0;
        
        // Por ahora usamos el progreso como base para calcular "calificación"
        $promedios = array_column($cursos, 'progreso');
        return count($promedios) > 0 ? array_sum($promedios) / count($promedios) : 0;
    }

    /**
     * Obtiene alertas personalizadas para el estudiante
     */
    public function getAlertasEstudiante(int $estudianteId): array
    {
        $alertas = [];
        
        try {
            $metricas = EstudianteModelo::getMetricasGenerales($estudianteId);
            $cursos = EstudianteModelo::getCursosConProgreso($estudianteId);
            
            // Alertas por cursos sin progreso
            $cursosSinProgreso = array_filter($cursos, fn($c) => $c['progreso'] < 10);
            if (!empty($cursosSinProgreso)) {
                $alertas[] = [
                    'tipo' => 'warning',
                    'titulo' => 'Cursos sin progreso',
                    'mensaje' => 'Tienes ' . count($cursosSinProgreso) . ' cursos con menos del 10% de progreso',
                    'accion' => 'Continuar aprendiendo'
                ];
            }
            
            // Alertas por tiempo de estudio
            if ($metricas['tiempo_estudio_total'] < 5) {
                $alertas[] = [
                    'tipo' => 'info',
                    'titulo' => 'Tiempo de estudio',
                    'mensaje' => 'Has estudiado menos de 5 horas este mes. ¡Dedica más tiempo!',
                    'accion' => 'Ver mis cursos'
                ];
            }
            
            return $alertas;
            
        } catch (Exception $e) {
            return [];
        }
    }
}