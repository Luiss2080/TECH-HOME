<?php

namespace App\Models;

use Core\DB;
use Exception;
use PDO;

/**
 * DocenteModelo - Métodos específicos para funcionalidades del docente
 */
class DocenteModelo
{
    private static $db;

    private static function getDB()
    {
        if (!self::$db) {
            self::$db = DB::getInstance()->getConnection();
        }
        return self::$db;
    }
    /**
     * Obtiene estadísticas completas del dashboard del docente
     */
    public static function getDashboardStats(int $docenteId): array
    {
        try {
            return [
                'metricas_docente' => self::getDocenteMetrics($docenteId),
                'actividad_estudiantes' => self::getStudentActivity($docenteId),
                'estudiantes_conectados' => self::getConnectedStudents($docenteId),
                'rendimiento_cursos' => self::getCoursePerformance($docenteId),
                'comentarios_recientes' => self::getRecentComments($docenteId),
                'cursos_recientes' => self::getRecentCourses($docenteId),
                'materiales_recientes' => self::getRecentMaterials($docenteId)
            ];
        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas del docente: " . $e->getMessage());
        }
    }

    /**
     * Métricas principales del docente
     */
    private static function getDocenteMetrics(int $docenteId): array
    {
        return [
            'estudiantes_totales' => self::getTotalStudents($docenteId),
            'estudiantes_activos' => self::getActiveStudents($docenteId),
            'cursos_creados' => self::getTotalCourses($docenteId),
            'cursos_activos' => self::getActiveCourses($docenteId),
            'materiales_subidos' => self::getTotalMaterials($docenteId),
            'materiales_mes' => self::getMonthlyMaterials($docenteId),
            'tareas_pendientes' => self::getPendingTasks($docenteId),
            'tareas_urgentes' => self::getUrgentTasks($docenteId),
            'evaluaciones_creadas' => self::getTotalEvaluations($docenteId),
            'evaluaciones_activas' => self::getActiveEvaluations($docenteId),
            'progreso_promedio' => self::getAverageProgress($docenteId),
            'mejora_promedio' => self::getAverageImprovement($docenteId)
        ];
    }

    /**
     * Total de estudiantes únicos en cursos del docente
     */
    private static function getTotalStudents(int $docenteId): int
    {
        $db = self::getDB();
        
        $query = "SELECT COUNT(DISTINCT p.estudiante_id) as total 
                  FROM progreso_estudiantes p
                  INNER JOIN cursos c ON p.curso_id = c.id
                  WHERE c.docente_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    /**
     * Estudiantes activos (con actividad en los últimos 30 días)
     */
    private static function getActiveStudents(int $docenteId): int
    {
        $db = self::getDB();
        
        $query = "SELECT COUNT(DISTINCT p.estudiante_id) as total 
                  FROM progreso_estudiantes p
                  INNER JOIN cursos c ON p.curso_id = c.id
                  WHERE c.docente_id = ? 
                  AND p.ultima_actividad >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    /**
     * Total de cursos creados por el docente
     */
    private static function getTotalCourses(int $docenteId): int
    {
        $db = self::getDB();
        
        $query = "SELECT COUNT(*) as total FROM cursos WHERE docente_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    /**
     * Cursos activos (publicados) del docente
     */
    private static function getActiveCourses(int $docenteId): int
    {
        $db = self::getDB();
        
        $query = "SELECT COUNT(*) as total FROM cursos 
                  WHERE docente_id = ? AND estado = 'Publicado'";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    /**
     * Total de materiales subidos (por ahora será 0 hasta implementar tabla materiales)
     */
    private static function getTotalMaterials(int $docenteId): int
    {
        // Por ahora retornamos 0, cuando implementes la tabla de materiales puedes actualizar esto
        return 0;
    }

    /**
     * Materiales subidos este mes
     */
    private static function getMonthlyMaterials(int $docenteId): int
    {
        // Por ahora retornamos 0, cuando implementes la tabla de materiales puedes actualizar esto
        return 0;
    }

    /**
     * Tareas pendientes (simulado por ahora)
     */
    private static function getPendingTasks(int $docenteId): int
    {
        // Por ahora simulamos, cuando tengas tabla de tareas implementar consulta real
        return rand(10, 25);
    }

    /**
     * Tareas urgentes (simulado por ahora)
     */
    private static function getUrgentTasks(int $docenteId): int
    {
        // Por ahora simulamos, cuando tengas tabla de tareas implementar consulta real
        return rand(2, 8);
    }

    /**
     * Total de evaluaciones creadas (simulado por ahora)
     */
    private static function getTotalEvaluations(int $docenteId): int
    {
        // Por ahora simulamos, cuando tengas tabla de evaluaciones implementar consulta real
        return rand(15, 40);
    }

    /**
     * Evaluaciones activas (simulado por ahora)
     */
    private static function getActiveEvaluations(int $docenteId): int
    {
        // Por ahora simulamos, cuando tengas tabla de evaluaciones implementar consulta real
        return rand(5, 15);
    }

    /**
     * Progreso promedio de estudiantes en cursos del docente
     */
    private static function getAverageProgress(int $docenteId): float
    {
        $db = self::getDB();
        
        $query = "SELECT AVG(p.progreso_porcentaje) as promedio
                  FROM progreso_estudiantes p
                  INNER JOIN cursos c ON p.curso_id = c.id
                  WHERE c.docente_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        return round((float)($stmt->fetch(PDO::FETCH_ASSOC)['promedio'] ?? 0), 1);
    }

    /**
     * Mejora promedio (simulado por ahora - necesitará histórico de progreso)
     */
    private static function getAverageImprovement(int $docenteId): float
    {
        // Por ahora simulamos, cuando tengas histórico de progreso implementar cálculo real
        return rand(5, 20);
    }

    /**
     * Actividad reciente de estudiantes
     */
    private static function getStudentActivity(int $docenteId): array
    {
        $db = self::getDB();
        
        $actividades = [];
        
        // Obtener progreso reciente de estudiantes
        $query = "SELECT u.nombre, u.apellido, c.titulo as curso, p.progreso_porcentaje, p.ultima_actividad
                  FROM progreso_estudiantes p
                  INNER JOIN usuarios u ON p.estudiante_id = u.id
                  INNER JOIN cursos c ON p.curso_id = c.id
                  WHERE c.docente_id = ?
                  ORDER BY p.ultima_actividad DESC
                  LIMIT 10";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nombreCompleto = $row['nombre'] . ' ' . $row['apellido'];
            $progreso = round($row['progreso_porcentaje'], 1);
            
            $actividades[] = [
                'estudiante' => $nombreCompleto,
                'accion' => "Progreso en \"{$row['curso']}\" ({$progreso}%)",
                'fecha' => $row['ultima_actividad'],
                'icono' => 'chart-line',
                'color' => '#10b981'
            ];
        }
        
        // Si no hay actividad real, mostrar mensaje
        if (empty($actividades)) {
            $actividades[] = [
                'estudiante' => 'Sin actividad',
                'accion' => 'No hay actividad reciente de estudiantes',
                'fecha' => date('Y-m-d H:i:s'),
                'icono' => 'info-circle',
                'color' => '#6b7280'
            ];
        }
        
        return $actividades;
    }

    /**
     * Estudiantes conectados actualmente (basado en actividad reciente)
     */
    private static function getConnectedStudents(int $docenteId): array
    {
        $db = self::getDB();
        
        // Consideramos "conectados" a estudiantes con actividad en las últimas 2 horas
        $query = "SELECT u.nombre, u.apellido, c.titulo as curso_actual, p.ultima_actividad
                  FROM progreso_estudiantes p
                  INNER JOIN usuarios u ON p.estudiante_id = u.id
                  INNER JOIN cursos c ON p.curso_id = c.id
                  WHERE c.docente_id = ? 
                  AND p.ultima_actividad >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
                  ORDER BY p.ultima_actividad DESC
                  LIMIT 8";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        $estudiantes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estudiantes[] = [
                'nombre' => $row['nombre'] . ' ' . $row['apellido'],
                'curso_actual' => $row['curso_actual'],
                'ultima_actividad' => $row['ultima_actividad'],
                'leccion_actual' => 'Activo en el curso' // Simulado
            ];
        }
        
        return $estudiantes;
    }

    /**
     * Rendimiento de los cursos del docente
     */
    private static function getCoursePerformance(int $docenteId): array
    {
        return [
            'calificacion_promedio' => 4.7,
            'tasa_finalizacion' => 89,
            'visualizaciones' => 12450,
            'tiempo_promedio' => 52,
            'certificados' => 87
        ];
    }

    /**
     * Comentarios y preguntas recientes
     */
    private static function getRecentComments(int $docenteId): array
    {
        return [
            [
                'estudiante' => 'Andrea Castillo',
                'curso' => 'Arduino y Sensores',
                'fecha' => date('Y-m-d H:i:s', strtotime('-25 minutes')),
                'leccion' => 'Lección 3',
                'tipo' => 'pregunta',
                'estado' => 'pendiente'
            ],
            [
                'estudiante' => 'Roberto Silva',
                'curso' => 'Robótica Avanzada',
                'fecha' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'leccion' => 'Lección 7',
                'tipo' => 'comentario',
                'estado' => 'respondida'
            ],
            [
                'estudiante' => 'Valentina Morales',
                'curso' => 'Electrónica Digital',
                'fecha' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'leccion' => 'Proyecto Final',
                'tipo' => 'pregunta',
                'estado' => 'pendiente'
            ],
            [
                'estudiante' => 'Javier Herrera',
                'curso' => 'IoT con ESP32',
                'fecha' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'leccion' => 'Lección 5',
                'tipo' => 'comentario',
                'estado' => 'respondida'
            ]
        ];
    }

    /**
     * Cursos recientes del docente
     */
    private static function getRecentCourses(int $docenteId): array
    {
        $db = self::getDB();
        
        $query = "SELECT c.titulo, cat.nombre as categoria, c.nivel, c.estado,
                         COUNT(p.estudiante_id) as estudiantes_inscritos,
                         c.fecha_creacion
                  FROM cursos c
                  LEFT JOIN categorias cat ON c.categoria_id = cat.id
                  LEFT JOIN progreso_estudiantes p ON c.id = p.curso_id
                  WHERE c.docente_id = ?
                  GROUP BY c.id
                  ORDER BY c.fecha_creacion DESC
                  LIMIT 5";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        $cursos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cursos[] = [
                'titulo' => $row['titulo'],
                'categoria' => $row['categoria'] ?? 'Sin categoría',
                'nivel' => $row['nivel'],
                'estudiantes_inscritos' => (int)$row['estudiantes_inscritos'],
                'lecciones' => rand(5, 15), // Simulado hasta implementar lecciones
                'estado' => $row['estado'],
                'fecha_creacion' => $row['fecha_creacion']
            ];
        }
        
        // Si no hay cursos, mostrar mensaje
        if (empty($cursos)) {
            $cursos[] = [
                'titulo' => 'No has creado cursos aún',
                'categoria' => 'Info',
                'nivel' => '-',
                'estudiantes_inscritos' => 0,
                'lecciones' => 0,
                'estado' => 'Sin cursos'
            ];
        }
        
        return $cursos;
    }

    /**
     * Materiales educativos recientes
     */
    private static function getRecentMaterials(int $docenteId): array
    {
        return [
            [
                'nombre' => 'sensor_ultrasonico.ino',
                'tipo' => 'Código Arduino',
                'curso' => 'Arduino Básico',
                'tamaño' => 2560, // bytes
                'descargas' => 87,
                'estado' => 'Disponible',
                'icono' => 'file-code'
            ],
            [
                'nombre' => 'Guía ESP32 WiFi',
                'tipo' => 'Manual',
                'curso' => 'IoT Avanzado',
                'tamaño' => 1024000, // bytes
                'descargas' => 45,
                'estado' => 'Disponible',
                'icono' => 'file-pdf'
            ],
            [
                'nombre' => 'Proyecto Robot',
                'tipo' => 'Código Python',
                'curso' => 'Robótica Avanzada',
                'tamaño' => 5120, // bytes
                'descargas' => 23,
                'estado' => 'Disponible',
                'icono' => 'file-code'
            ],
            [
                'nombre' => 'Esquemas Electrónicos',
                'tipo' => 'Imagen',
                'curso' => 'Electrónica Básica',
                'tamaño' => 2048000, // bytes
                'descargas' => 67,
                'estado' => 'Disponible',
                'icono' => 'file-image'
            ]
        ];
    }

    // =========================================
    // MÉTODOS ESPECÍFICOS PARA VISTAS
    // =========================================

    /**
     * Obtiene cursos del docente con filtros
     */
    public static function getCoursesByDocente(int $docenteId, array $filters = []): array
    {
        $db = self::getDB();
        
        $query = "SELECT c.*, cat.nombre as categoria_nombre,
                         COUNT(p.estudiante_id) as estudiantes_inscritos
                  FROM cursos c
                  LEFT JOIN categorias cat ON c.categoria_id = cat.id
                  LEFT JOIN progreso_estudiantes p ON c.id = p.curso_id
                  WHERE c.docente_id = ?
                  GROUP BY c.id
                  ORDER BY c.fecha_actualizacion DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        $cursos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cursos[] = [
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'categoria' => $row['categoria_nombre'],
                'nivel' => $row['nivel'],
                'estudiantes_inscritos' => $row['estudiantes_inscritos'],
                'estado' => $row['estado'],
                'fecha_creacion' => $row['fecha_creacion']
            ];
        }
        
        return $cursos;
    }

    /**
     * Obtiene estudiantes inscritos en cursos del docente
     */
    public static function getStudentsByDocente(int $docenteId): array
    {
        $db = self::getDB();
        
        $query = "SELECT DISTINCT u.id, u.nombre, u.apellido, u.email,
                         p.progreso_porcentaje, p.ultima_actividad,
                         c.titulo as curso_actual,
                         CASE 
                           WHEN p.ultima_actividad >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 
                           ELSE 0 
                         END as activo
                  FROM usuarios u
                  INNER JOIN progreso_estudiantes p ON u.id = p.estudiante_id
                  INNER JOIN cursos c ON p.curso_id = c.id
                  WHERE c.docente_id = ?
                  ORDER BY p.ultima_actividad DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        $estudiantes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estudiantes[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre'] . ' ' . $row['apellido'],
                'email' => $row['email'],
                'progreso' => round($row['progreso_porcentaje'], 1),
                'curso' => $row['curso_actual'],
                'activo' => (bool)$row['activo'],
                'ultima_actividad' => $row['ultima_actividad']
            ];
        }
        
        return $estudiantes;
    }

    /**
     * Obtiene alertas para el docente
     */
    public static function getDocenteAlerts(int $docenteId): array
    {
        $alerts = [];

        // Verificar tareas pendientes
        $tareasPendientes = self::getPendingTasks($docenteId);
        if ($tareasPendientes > 10) {
            $alerts[] = [
                'tipo' => 'warning',
                'mensaje' => "Tienes {$tareasPendientes} tareas pendientes de revisar",
                'accion' => 'Revisar tareas pendientes'
            ];
        }

        // Verificar comentarios sin responder
        $comentariosPendientes = count(array_filter(self::getRecentComments($docenteId), 
            fn($c) => $c['estado'] === 'pendiente'));
        
        if ($comentariosPendientes > 0) {
            $alerts[] = [
                'tipo' => 'info',
                'mensaje' => "Tienes {$comentariosPendientes} preguntas sin responder",
                'accion' => 'Responder preguntas de estudiantes'
            ];
        }

        return $alerts;
    }

    /**
     * Genera reporte de actividad del docente
     */
    public static function getDocenteActivityReport(int $docenteId, int $days = 30): array
    {
        $db = self::getDB();
        
        // Obtener estadísticas del período
        $query = "SELECT COUNT(*) as materiales_mes FROM cursos 
                  WHERE docente_id = ? AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId, $days]);
        $cursosNuevos = $stmt->fetch(PDO::FETCH_ASSOC)['materiales_mes'] ?? 0;
        
        return [
            'periodo' => "{$days} días",
            'cursos_creados' => $cursosNuevos,
            'materiales_subidos' => self::getMonthlyMaterials($docenteId),
            'tareas_revisadas' => rand(30, 50), // Simulado hasta implementar tabla tareas
            'estudiantes_nuevos' => self::getNewStudentsCount($docenteId, $days),
            'promedio_calificaciones' => 8.5 // Simulado hasta implementar sistema calificaciones
        ];
    }

    /**
     * Obtiene número de estudiantes nuevos en el período
     */
    private static function getNewStudentsCount(int $docenteId, int $days): int
    {
        $db = self::getDB();
        
        $query = "SELECT COUNT(DISTINCT p.estudiante_id) as total
                  FROM progreso_estudiantes p
                  INNER JOIN cursos c ON p.curso_id = c.id
                  WHERE c.docente_id = ? 
                  AND p.fecha_inscripcion >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId, $days]);
        
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    // =========================================
    // NUEVOS MÉTODOS PARA FUNCIONALIDADES
    // =========================================

    /**
     * Obtiene materiales del docente
     */
    public static function getMaterialesByDocente(int $docenteId): array
    {
        $db = self::getDB();
        
        $query = "SELECT m.*, c.nombre as categoria_nombre
                  FROM materiales m
                  LEFT JOIN categorias c ON m.categoria_id = c.id
                  WHERE m.docente_id = ?
                  ORDER BY m.fecha_creacion DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        $materiales = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $materiales[] = [
                'id' => $row['id'],
                'nombre' => $row['titulo'],
                'tipo' => strtoupper($row['tipo']),
                'categoria' => $row['categoria_nombre'] ?? 'Sin categoría',
                'tamaño' => $row['tamaño_archivo'],
                'descargas' => $row['descargas'],
                'fecha_creacion' => $row['fecha_creacion']
            ];
        }
        
        return $materiales;
    }

    /**
     * Crear nuevo material
     */
    public static function crearMaterial(array $data): int
    {
        $db = self::getDB();
        
        $query = "INSERT INTO materiales (titulo, descripcion, tipo, archivo, categoria_id, docente_id, tamaño_archivo, publico)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $data['titulo'],
            $data['descripcion'] ?? '',
            $data['tipo'],
            $data['archivo'] ?? '',
            $data['categoria_id'],
            $data['docente_id'],
            $data['tamaño_archivo'] ?? 0,
            $data['publico'] ?? 1
        ]);
        
        return $db->lastInsertId();
    }

    /**
     * Crear nuevo curso
     */
    public static function crearCurso(array $data): int
    {
        $db = self::getDB();
        
        $query = "INSERT INTO cursos (titulo, descripcion, categoria_id, docente_id, nivel, duracion_estimada, precio, es_gratuito, estado)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Borrador')";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $data['titulo'],
            $data['descripcion'],
            $data['categoria_id'],
            $data['docente_id'],
            $data['nivel'],
            $data['duracion_estimada'],
            $data['precio'] ?? 0,
            $data['es_gratuito'] ?? 0
        ]);
        
        return $db->lastInsertId();
    }

    /**
     * Calificar estudiante
     */
    public static function calificarEstudiante(int $estudianteId, int $cursoId, float $nota): bool
    {
        $db = self::getDB();
        
        // Verificar si ya existe una nota
        $queryCheck = "SELECT id FROM notas WHERE estudiante_id = ? AND curso_id = ?";
        $stmtCheck = $db->prepare($queryCheck);
        $stmtCheck->execute([$estudianteId, $cursoId]);
        
        if ($stmtCheck->fetch()) {
            // Actualizar nota existente
            $query = "UPDATE notas SET nota = ?, fecha_calificacion = NOW() 
                      WHERE estudiante_id = ? AND curso_id = ?";
            $stmt = $db->prepare($query);
            return $stmt->execute([$nota, $estudianteId, $cursoId]);
        } else {
            // Crear nueva nota
            $query = "INSERT INTO notas (estudiante_id, curso_id, nota, fecha_calificacion)
                      VALUES (?, ?, ?, NOW())";
            $stmt = $db->prepare($query);
            return $stmt->execute([$estudianteId, $cursoId, $nota]);
        }
    }

    /**
     * Obtener notas de un curso
     */
    public static function getNotasCurso(int $cursoId): array
    {
        $db = self::getDB();
        
        $query = "SELECT n.*, u.nombre, u.apellido, u.email, c.titulo as curso
                  FROM notas n
                  INNER JOIN usuarios u ON n.estudiante_id = u.id
                  INNER JOIN cursos c ON n.curso_id = c.id
                  WHERE n.curso_id = ?
                  ORDER BY n.fecha_calificacion DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$cursoId]);
        
        $notas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notas[] = [
                'id' => $row['id'],
                'estudiante_id' => $row['estudiante_id'],
                'estudiante' => $row['nombre'] . ' ' . $row['apellido'],
                'email' => $row['email'],
                'curso' => $row['curso'],
                'nota' => (float)$row['nota'],
                'fecha_calificacion' => $row['fecha_calificacion']
            ];
        }
        
        return $notas;
    }

    /**
     * Obtener notas de todos los cursos del docente
     */
    public static function getNotasDocente(int $docenteId): array
    {
        $db = self::getDB();
        
        $query = "SELECT n.*, u.nombre, u.apellido, u.email, c.titulo as curso
                  FROM notas n
                  INNER JOIN usuarios u ON n.estudiante_id = u.id
                  INNER JOIN cursos c ON n.curso_id = c.id
                  WHERE c.docente_id = ?
                  ORDER BY n.fecha_calificacion DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$docenteId]);
        
        $notas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notas[] = [
                'id' => $row['id'],
                'estudiante_id' => $row['estudiante_id'],
                'estudiante' => $row['nombre'] . ' ' . $row['apellido'],
                'email' => $row['email'],
                'curso' => $row['curso'],
                'nota' => (float)$row['nota'],
                'fecha_calificacion' => $row['fecha_calificacion']
            ];
        }
        
        return $notas;
    }
}