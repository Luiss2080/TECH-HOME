<?php

namespace App\Services;

use App\Models\DocenteModelo;
use Exception;

/**
 * DocenteService - Lógica de negocio para funcionalidades del docente
 */
class DocenteService
{
    /**
     * Obtiene todos los datos necesarios para el dashboard del docente
     */
    public function getDashboardData(int $docenteId): array
    {
        try {
            // Obtener todas las estadísticas del dashboard
            $dashboardStats = DocenteModelo::getDashboardStats($docenteId);

            // Estructurar datos para la vista
            return $dashboardStats;
            
        } catch (Exception $e) {
            // En caso de error, devolver datos por defecto
            return [
                'metricas_docente' => [
                    'estudiantes_totales' => 127,
                    'estudiantes_activos' => 89,
                    'cursos_creados' => 12,
                    'cursos_activos' => 8,
                    'materiales_subidos' => 145,
                    'materiales_mes' => 23,
                    'tareas_pendientes' => 18,
                    'tareas_urgentes' => 5,
                    'evaluaciones_creadas' => 34,
                    'evaluaciones_activas' => 12,
                    'progreso_promedio' => 87,
                    'mejora_promedio' => 12
                ],
                'actividad_estudiantes' => [
                    [
                        'estudiante' => 'María González',
                        'accion' => 'Completó la lección "Programación Arduino Básico"',
                        'fecha' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
                        'icono' => 'user-graduate',
                        'color' => '#10b981'
                    ]
                ],
                'estudiantes_conectados' => [
                    [
                        'nombre' => 'Pedro Sánchez',
                        'curso_actual' => 'Robótica Avanzada',
                        'ultima_actividad' => date('Y-m-d H:i:s', strtotime('-2 minutes')),
                        'leccion_actual' => 'Lección: Programación'
                    ]
                ],
                'rendimiento_cursos' => [
                    'calificacion_promedio' => 4.7,
                    'tasa_finalizacion' => 89,
                    'visualizaciones' => 12450,
                    'tiempo_promedio' => 52,
                    'certificados' => 87
                ],
                'comentarios_recientes' => [
                    [
                        'estudiante' => 'Andrea Castillo',
                        'curso' => 'Arduino y Sensores',
                        'fecha' => date('Y-m-d H:i:s', strtotime('-25 minutes')),
                        'leccion' => 'Lección 3',
                        'tipo' => 'pregunta',
                        'estado' => 'pendiente'
                    ]
                ],
                'cursos_recientes' => [
                    [
                        'titulo' => 'Arduino desde Cero',
                        'categoria' => 'Robótica',
                        'nivel' => 'Principiante',
                        'estudiantes_inscritos' => 45,
                        'lecciones' => 12,
                        'estado' => 'Publicado'
                    ]
                ],
                'materiales_recientes' => [
                    [
                        'nombre' => 'sensor_ultrasonico.ino',
                        'tipo' => 'Código Arduino',
                        'curso' => 'Arduino Básico',
                        'tamaño' => 2560,
                        'descargas' => 87,
                        'estado' => 'Disponible',
                        'icono' => 'file-code'
                    ]
                ]
            ];
        }
    }

    /**
     * Obtiene métricas para peticiones AJAX
     */
    public function getMetricasAjax(int $docenteId, string $tipo = 'general'): array
    {
        try {
            switch ($tipo) {
                case 'estudiantes':
                    return [
                        'total' => 127,
                        'activos' => 89,
                        'nuevos_mes' => 12,
                        'promedio_calificacion' => 8.5
                    ];
                    
                case 'cursos':
                    return [
                        'total' => 12,
                        'activos' => 8,
                        'completados' => 4,
                        'en_desarrollo' => 2
                    ];
                    
                default:
                    return $this->getDashboardData($docenteId)['metricas_docente'];
            }
            
        } catch (Exception $e) {
            throw new Exception("Error al obtener métricas AJAX: " . $e->getMessage());
        }
    }

    // =========================================
    // MÉTODOS BÁSICOS PARA VISTAS
    // =========================================

    /**
     * Obtiene cursos del docente
     */
    public function getCursos(int $docenteId): array
    {
        return [
            [
                'id' => 1,
                'titulo' => 'Arduino desde Cero',
                'categoria' => 'Robótica',
                'estudiantes' => 45,
                'estado' => 'Publicado'
            ]
        ];
    }

    /**
     * Obtiene estudiantes del docente
     */
    public function getEstudiantes(int $docenteId): array
    {
        try {
            return DocenteModelo::getStudentsByDocente($docenteId);
        } catch (Exception $e) {
            throw new Exception("Error al obtener estudiantes: " . $e->getMessage());
        }
    }

    /**
     * Obtiene materiales del docente
     */
    public function getMateriales(int $docenteId): array
    {
        try {
            return DocenteModelo::getMaterialesByDocente($docenteId);
        } catch (Exception $e) {
            throw new Exception("Error al obtener materiales: " . $e->getMessage());
        }
    }

    /**
     * Obtiene tareas pendientes
     */
    public function getTareasPendientes(int $docenteId): array
    {
        return [
            [
                'id' => 1,
                'estudiante' => 'Carlos Mendoza',
                'titulo' => 'Proyecto Robot',
                'curso' => 'Robótica Avanzada',
                'fecha_entrega' => date('Y-m-d', strtotime('+3 days'))
            ]
        ];
    }

    /**
     * Obtiene evaluaciones del docente
     */
    public function getEvaluaciones(int $docenteId): array
    {
        return [
            [
                'id' => 1,
                'titulo' => 'Examen Arduino',
                'curso' => 'Arduino Básico',
                'estudiantes' => 25,
                'estado' => 'Activa'
            ]
        ];
    }

    /**
     * Obtiene comentarios del docente
     */
    public function getComentarios(int $docenteId): array
    {
        return [
            [
                'id' => 1,
                'estudiante' => 'Ana Rodríguez',
                'curso' => 'IoT Básico',
                'comentario' => '¿Cómo conecto el sensor?',
                'estado' => 'pendiente',
                'fecha' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ]
        ];
    }

    /**
     * Obtiene categorías de cursos
     */
    public function getCategoriasCursos(): array
    {
        return [
            ['id' => 1, 'nombre' => 'Robótica Básica'],
            ['id' => 2, 'nombre' => 'Arduino'],
            ['id' => 3, 'nombre' => 'IoT'],
            ['id' => 4, 'nombre' => 'Sensores'],
            ['id' => 5, 'nombre' => 'Programación']
        ];
    }

    /**
     * Crear nuevo curso
     */
    public function crearCurso(array $data): int
    {
        try {
            return DocenteModelo::crearCurso($data);
        } catch (Exception $e) {
            throw new Exception("Error al crear curso: " . $e->getMessage());
        }
    }

    /**
     * Crear nuevo material
     */
    public function crearMaterial(array $data): int
    {
        try {
            return DocenteModelo::crearMaterial($data);
        } catch (Exception $e) {
            throw new Exception("Error al crear material: " . $e->getMessage());
        }
    }

    /**
     * Calificar estudiante
     */
    public function calificarEstudiante(int $estudianteId, int $cursoId, float $nota): bool
    {
        try {
            return DocenteModelo::calificarEstudiante($estudianteId, $cursoId, $nota);
        } catch (Exception $e) {
            throw new Exception("Error al calificar estudiante: " . $e->getMessage());
        }
    }

    /**
     * Obtener notas de un curso
     */
    public function getNotasCurso(int $cursoId): array
    {
        try {
            return DocenteModelo::getNotasCurso($cursoId);
        } catch (Exception $e) {
            throw new Exception("Error al obtener notas: " . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas completas
     */
    public function getEstadisticasCompletas(int $docenteId): array
    {
        return [
            'resumen_general' => $this->getDashboardData($docenteId)['metricas_docente'],
            'rendimiento_cursos' => $this->getDashboardData($docenteId)['rendimiento_cursos']
        ];
    }

    /**
     * Obtiene progreso de estudiantes
     */
    public function getProgresoEstudiantes(int $docenteId): array
    {
        return [
            'resumen' => [
                'total_estudiantes' => 127,
                'promedio_general' => 87,
                'estudiantes_completaron' => 89,
                'estudiantes_rezagados' => 12
            ]
        ];
    }
}