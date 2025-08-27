<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\Categoria;
use App\Models\User;
use App\Models\ProgresoEstudiante;
use Core\DB;
use PDO;
use Exception;

class CursoService
{
    /**
     * Obtener todos los cursos con información adicional
     */
    public function getAllCursos(): array
    {
        $cursos = Curso::all();
        $cursosData = [];

        foreach ($cursos as $curso) {
            $cursoData = $curso->getAttributes();
            
            // Obtener información del docente
            $docente = User::find($curso->docente_id);
            $cursoData['docente_nombre'] = $docente ? $docente->nombre . ' ' . $docente->apellido : 'Sin docente';
            
            // Obtener información de la categoría
            $categoria = Categoria::find($curso->categoria_id);
            $cursoData['categoria_nombre'] = $categoria ? $categoria->nombre : 'Sin categoría';
            
            // Obtener estadísticas del curso
            $cursoData['total_estudiantes'] = $this->getTotalEstudiantes($curso->id);
            $cursoData['progreso_promedio'] = $this->getProgresoPromedio($curso->id);
            
            $cursosData[] = $cursoData;
        }

        return $cursosData;
    }

    /**
     * Obtener cursos por docente
     */
    public function getCursosByDocente(int $docenteId): array
    {
        $cursos = Curso::where('docente_id', '=', $docenteId)->get();
        $cursosData = [];

        foreach ($cursos as $curso) {
            $cursoData = $curso->getAttributes();
            
            // Obtener información de la categoría
            $categoria = Categoria::find($curso->categoria_id);
            $cursoData['categoria_nombre'] = $categoria ? $categoria->nombre : 'Sin categoría';
            
            // Obtener estadísticas del curso
            $cursoData['total_estudiantes'] = $this->getTotalEstudiantes($curso->id);
            $cursoData['progreso_promedio'] = $this->getProgresoPromedio($curso->id);
            
            $cursosData[] = $cursoData;
        }

        return $cursosData;
    }

    /**
     * Obtener curso por ID con información completa
     */
    public function getCursoById(int $id)
    {
        $curso = Curso::find($id);
        if (!$curso) {
            return null;
        }

        $cursoData = $curso->getAttributes();
        
        // Obtener información del docente
        $docente = User::find($curso->docente_id);
        $cursoData['docente'] = $docente ? [
            'id' => $docente->id,
            'nombre' => $docente->nombre,
            'apellido' => $docente->apellido,
            'email' => $docente->email
        ] : null;
        
        // Obtener información de la categoría
        $categoria = Categoria::find($curso->categoria_id);
        $cursoData['categoria'] = $categoria ? [
            'id' => $categoria->id,
            'nombre' => $categoria->nombre,
            'color' => $categoria->color,
            'icono' => $categoria->icono
        ] : null;
        
        // Obtener estadísticas del curso
        $cursoData['total_estudiantes'] = $this->getTotalEstudiantes($curso->id);
        $cursoData['estudiantes_activos'] = $this->getEstudiantesActivos($curso->id);
        $cursoData['progreso_promedio'] = $this->getProgresoPromedio($curso->id);
        $cursoData['completados'] = $this->getCursosCompletados($curso->id);

        return $cursoData;
    }

    /**
     * Crear nuevo curso
     */
    public function createCurso(array $cursoData): int
    {
        try {
            // Validar que el docente existe
            $docente = User::find($cursoData['docente_id']);
            if (!$docente) {
                throw new Exception('El docente especificado no existe');
            }

            // Validar que la categoría existe
            $categoria = Categoria::find($cursoData['categoria_id']);
            if (!$categoria) {
                throw new Exception('La categoría especificada no existe');
            }

            // Crear el curso
            $curso = new Curso([
                'titulo' => $cursoData['titulo'],
                'descripcion' => $cursoData['descripcion'],
                'contenido' => $cursoData['contenido'] ?? null,
                'docente_id' => $cursoData['docente_id'],
                'categoria_id' => $cursoData['categoria_id'],
                'imagen_portada' => $cursoData['imagen_portada'] ?? null,
                'precio' => $cursoData['precio'] ?? 0.00,
                'duracion_horas' => $cursoData['duracion_horas'] ?? 0,
                'nivel' => $cursoData['nivel'] ?? 'Principiante',
                'requisitos' => $cursoData['requisitos'] ?? null,
                'objetivos' => $cursoData['objetivos'] ?? null,
                'estado' => $cursoData['estado'] ?? 'Borrador'
            ]);

            $curso->save();
            return $curso->getKey();
        } catch (Exception $e) {
            throw new Exception('Error al crear curso: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar curso
     */
    public function updateCurso(int $id, array $cursoData): bool
    {
        try {
            $curso = Curso::find($id);
            if (!$curso) {
                throw new Exception('Curso no encontrado');
            }

            // Validar docente si se proporciona
            if (isset($cursoData['docente_id'])) {
                $docente = User::find($cursoData['docente_id']);
                if (!$docente) {
                    throw new Exception('El docente especificado no existe');
                }
            }

            // Validar categoría si se proporciona
            if (isset($cursoData['categoria_id'])) {
                $categoria = Categoria::find($cursoData['categoria_id']);
                if (!$categoria) {
                    throw new Exception('La categoría especificada no existe');
                }
            }

            // Actualizar campos
            foreach ($cursoData as $field => $value) {
                if ($value !== null && in_array($field, $curso->getFillable())) {
                    $curso->$field = $value;
                }
            }

            $curso->save();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar curso: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar curso con verificación de dependencias
     */
    public function deleteCurso(int $id): bool
    {
        try {
            $curso = Curso::find($id);
            if (!$curso) {
                throw new Exception('Curso no encontrado');
            }

            // Verificar dependencias
            $dependencies = $this->checkCursoDependencies($id);
            if (!empty($dependencies)) {
                $message = "No se puede eliminar el curso '{$curso->titulo}' porque:\n";
                foreach ($dependencies as $dependency) {
                    $message .= "• " . $dependency . "\n";
                }
                $message .= "\nPrimero debe resolver estas dependencias.";
                throw new Exception($message);
            }

            $curso->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar curso: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del curso
     */
    public function changeStatus(int $id, string $status): bool
    {
        try {
            $curso = Curso::find($id);
            if (!$curso) {
                throw new Exception('Curso no encontrado');
            }

            $validStatuses = ['Borrador', 'Publicado', 'Archivado'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Estado inválido');
            }

            $curso->estado = $status;
            $curso->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener todas las categorías de cursos
     */
    public function getAllCategoriasCursos(): array
    {
        return Categoria::where('tipo', '=', 'curso')->where('estado', '=', 1)->get();
    }

    /**
     * Obtener todos los docentes
     */
    public function getAllDocentes(): array
    {
        $db = DB::getInstance();
        $query = "SELECT u.* FROM usuarios u 
                  INNER JOIN model_has_roles mhr ON u.id = mhr.model_id 
                  WHERE mhr.role_id = 2 AND mhr.model_type = 'App\\Models\\User' 
                  AND u.estado = 1
                  ORDER BY u.nombre, u.apellido";
        
        $result = $db->query($query);
        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Inscribir estudiante a curso
     */
    public function inscribirEstudiante(int $cursoId, int $estudianteId): bool
    {
        try {
            // Verificar que el curso existe y está publicado
            $curso = Curso::find($cursoId);
            if (!$curso || $curso->estado !== 'Publicado') {
                throw new Exception('Curso no disponible para inscripción');
            }

            // Verificar que el estudiante existe
            $estudiante = User::find($estudianteId);
            if (!$estudiante) {
                throw new Exception('Estudiante no encontrado');
            }

            // Verificar si ya está inscrito
            $db = DB::getInstance();
            $existing = $db->query(
                "SELECT id FROM progreso_estudiantes WHERE estudiante_id = ? AND curso_id = ?",
                [$estudianteId, $cursoId]
            );

            if ($existing->fetch()) {
                throw new Exception('El estudiante ya está inscrito en este curso');
            }

            // Crear registro de progreso
            $db->query(
                "INSERT INTO progreso_estudiantes (estudiante_id, curso_id, progreso_porcentaje, tiempo_estudiado, completado) 
                 VALUES (?, ?, 0, 0, 0)",
                [$estudianteId, $cursoId]
            );

            return true;
        } catch (Exception $e) {
            throw new Exception('Error al inscribir estudiante: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas generales de cursos
     */
    public function getEstadisticasCursos(): array
    {
        try {
            return [
                'total' => Curso::count(),
                'publicados' => Curso::where('estado', '=', 'Publicado')->count(),
                'borradores' => Curso::where('estado', '=', 'Borrador')->count(),
                'archivados' => Curso::where('estado', '=', 'Archivado')->count(),
                'por_nivel' => [
                    'principiante' => Curso::where('nivel', '=', 'Principiante')->count(),
                    'intermedio' => Curso::where('nivel', '=', 'Intermedio')->count(),
                    'avanzado' => Curso::where('nivel', '=', 'Avanzado')->count()
                ],
                'recientes' => Curso::recientes(7)->count(),
                'promedio_duracion' => $this->getPromedioDuracion(),
                'promedio_precio' => $this->getPromedioPrecio()
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'publicados' => 0,
                'borradores' => 0,
                'archivados' => 0,
                'por_nivel' => ['principiante' => 0, 'intermedio' => 0, 'avanzado' => 0],
                'recientes' => 0,
                'promedio_duracion' => 0,
                'promedio_precio' => 0
            ];
        }
    }

    /**
     * Verificar dependencias del curso antes de eliminar
     */
    private function checkCursoDependencies(int $cursoId): array
    {
        $dependencies = [];
        
        try {
            $db = DB::getInstance();
            
            // Verificar estudiantes inscritos
            $estudiantes = $db->query("SELECT COUNT(*) as count FROM progreso_estudiantes WHERE curso_id = ?", [$cursoId])->fetch();
            if ($estudiantes->count > 0) {
                $dependencies[] = "Tiene {$estudiantes->count} estudiante(s) inscrito(s)";
            }
            
        } catch (Exception $e) {
            $dependencies[] = "Error verificando dependencias: " . $e->getMessage();
        }
        
        return $dependencies;
    }

    /**
     * Obtener total de estudiantes inscritos
     */
    private function getTotalEstudiantes(int $cursoId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(*) as count FROM progreso_estudiantes WHERE curso_id = ?", [$cursoId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener estudiantes activos (con progreso reciente)
     */
    private function getEstudiantesActivos(int $cursoId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM progreso_estudiantes 
                 WHERE curso_id = ? AND ultima_actividad >= DATE_SUB(NOW(), INTERVAL 30 DAY)", 
                [$cursoId]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener progreso promedio del curso
     */
    private function getProgresoPromedio(int $cursoId): float
    {
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT AVG(progreso_porcentaje) as promedio FROM progreso_estudiantes WHERE curso_id = ?", 
                [$cursoId]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return round($row['promedio'] ?? 0, 2);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener cursos completados
     */
    private function getCursosCompletados(int $cursoId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM progreso_estudiantes WHERE curso_id = ? AND completado = 1", 
                [$cursoId]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener promedio de duración de cursos
     */
    private function getPromedioDuracion(): float
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT AVG(duracion_horas) as promedio FROM cursos WHERE estado != 'Archivado'");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return round($row['promedio'] ?? 0, 1);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener promedio de precio de cursos
     */
    private function getPromedioPrecio(): float
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT AVG(precio) as promedio FROM cursos WHERE estado = 'Publicado' AND precio > 0");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return round($row['promedio'] ?? 0, 2);
        } catch (Exception $e) {
            return 0;
        }
    }

    // ==========================================
    // MÉTODOS PARA NUEVAS FUNCIONALIDADES
    // ==========================================

    /**
     * Toggle favorito para un usuario en un curso
     */
    public function toggleFavorito(int $cursoId, int $userId): array
    {
        $curso = Curso::find($cursoId);
        if (!$curso) {
            throw new Exception('Curso no encontrado');
        }

        return $curso->toggleFavorito($userId);
    }

    /**
     * Calificar un curso
     */
    public function calificarCurso(int $cursoId, int $userId, int $calificacion, ?string $comentario = null): array
    {
        $curso = Curso::find($cursoId);
        if (!$curso) {
            throw new Exception('Curso no encontrado');
        }

        // Verificar si el usuario está inscrito
        if (!$this->estaInscrito($cursoId, $userId)) {
            throw new Exception('Solo puedes calificar cursos en los que estés inscrito');
        }

        return $curso->calificar($userId, $calificacion, $comentario);
    }

    /**
     * Obtener calificaciones de un curso
     */
    public function getCalificacionesCurso(int $cursoId): array
    {
        $curso = Curso::find($cursoId);
        if (!$curso) {
            throw new Exception('Curso no encontrado');
        }

        return [
            'estadisticas' => $curso->getEstadisticasCalificaciones(),
            'calificaciones' => $curso->getCalificacionesDetalladas()
        ];
    }

    /**
     * Obtener cursos favoritos de un usuario
     */
    public function getFavoritosUsuario(int $userId): array
    {
        try {
            $db = DB::getInstance();
            $query = "
                SELECT c.*, u.nombre_completo as docente_nombre, cat.nombre as categoria_nombre, cat.color as categoria_color
                FROM cursos c
                INNER JOIN favoritos_cursos f ON c.id = f.curso_id
                LEFT JOIN usuarios u ON c.docente_id = u.id
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                WHERE f.usuario_id = ? AND c.estado = 'Publicado'
                ORDER BY f.fecha_agregado DESC
            ";
            
            $result = $db->query($query, [$userId]);
            $favoritos = $result->fetchAll(PDO::FETCH_ASSOC);

            // Enriquecer datos
            foreach ($favoritos as &$curso) {
                $curso['precio_formateado'] = $this->formatPriceCurso($curso['precio']);
                $curso['total_estudiantes'] = $this->getTotalEstudiantes($curso['id']);
                $curso['total_favoritos'] = $this->getTotalFavoritos($curso['id']);
            }

            return $favoritos;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener total de favoritos de un curso
     */
    public function getTotalFavoritos(int $cursoId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(*) as total FROM favoritos_cursos WHERE curso_id = ?", [$cursoId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Verificar si un usuario está inscrito en un curso
     */
    public function estaInscrito(int $cursoId, int $userId): bool
    {
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM inscripciones_cursos WHERE curso_id = ? AND usuario_id = ?",
                [$cursoId, $userId]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener progreso de un estudiante en un curso
     */
    public function getProgresoEstudiante(int $cursoId, int $userId): array
    {
        try {
            $db = DB::getInstance();
            
            // Obtener progreso general
            $queryProgreso = "
                SELECT i.fecha_inscripcion, i.fecha_completado, i.progreso_porcentaje
                FROM inscripciones_cursos i
                WHERE i.curso_id = ? AND i.usuario_id = ?
            ";
            
            $resultado = $db->query($queryProgreso, [$cursoId, $userId]);
            $progreso = $resultado->fetch(PDO::FETCH_ASSOC);
            
            if (!$progreso) {
                return [];
            }

            // Obtener progreso por módulos
            $queryModulos = "
                SELECT m.id, m.titulo, m.orden, 
                       COALESCE(p.completado, 0) as completado,
                       p.fecha_completado
                FROM modulos_curso m
                LEFT JOIN progreso_modulos p ON m.id = p.modulo_id AND p.usuario_id = ?
                WHERE m.curso_id = ?
                ORDER BY m.orden
            ";
            
            $resultadoModulos = $db->query($queryModulos, [$userId, $cursoId]);
            $modulos = $resultadoModulos->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'progreso_general' => $progreso,
                'modulos' => $modulos,
                'porcentaje_completado' => $progreso['progreso_porcentaje'] ?? 0
            ];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Completar módulo para un estudiante
     */
    public function completarModulo(int $cursoId, int $moduloId, int $userId): bool
    {
        try {
            $db = DB::getInstance();
            
            // Verificar que el estudiante esté inscrito
            if (!$this->estaInscrito($cursoId, $userId)) {
                throw new Exception('No estás inscrito en este curso');
            }
            
            // Marcar módulo como completado
            $db->query(
                "INSERT INTO progreso_modulos (modulo_id, usuario_id, completado, fecha_completado) 
                 VALUES (?, ?, 1, NOW()) 
                 ON DUPLICATE KEY UPDATE completado = 1, fecha_completado = NOW()",
                [$moduloId, $userId]
            );
            
            // Actualizar progreso general del curso
            $this->actualizarProgresoCurso($cursoId, $userId);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Actualizar progreso general de un curso para un estudiante
     */
    private function actualizarProgresoCurso(int $cursoId, int $userId): void
    {
        try {
            $db = DB::getInstance();
            
            // Calcular porcentaje de completado
            $query = "
                SELECT 
                    COUNT(*) as total_modulos,
                    COUNT(p.id) as modulos_completados
                FROM modulos_curso m
                LEFT JOIN progreso_modulos p ON m.id = p.modulo_id AND p.usuario_id = ? AND p.completado = 1
                WHERE m.curso_id = ?
            ";
            
            $resultado = $db->query($query, [$userId, $cursoId]);
            $stats = $resultado->fetch(PDO::FETCH_ASSOC);
            
            $porcentaje = $stats['total_modulos'] > 0 
                ? round(($stats['modulos_completados'] / $stats['total_modulos']) * 100, 2)
                : 0;
            
            $fechaCompletado = ($porcentaje >= 100) ? 'NOW()' : 'NULL';
            
            // Actualizar inscripción
            $db->query(
                "UPDATE inscripciones_cursos 
                 SET progreso_porcentaje = ?, fecha_completado = $fechaCompletado
                 WHERE curso_id = ? AND usuario_id = ?",
                [$porcentaje, $cursoId, $userId]
            );
            
        } catch (Exception $e) {
            // Silenciar error
        }
    }

    /**
     * Formatear precio para mostrar
     */
    private function formatPriceCurso($precio): string
    {
        if ($precio == 0 || $precio === null) {
            return 'Gratuito';
        }
        return '$' . number_format($precio, 2, '.', ',');
    }

    /**
     * Obtener un curso por ID
     */
    public function getCurso(int $id): ?array
    {
        $curso = Curso::find($id);
        if (!$curso) {
            return null;
        }

        $cursoData = $curso->getAttributes();
        
        // Obtener información del docente
        $docente = User::find($curso->docente_id);
        $cursoData['docente_nombre'] = $docente ? $docente->nombre_completo : 'Sin docente';
        
        // Obtener información de la categoría
        $categoria = Categoria::find($curso->categoria_id);
        $cursoData['categoria_nombre'] = $categoria ? $categoria->nombre : 'Sin categoría';
        
        // Estadísticas
        $cursoData['total_estudiantes'] = $this->getTotalEstudiantes($curso->id);
        $cursoData['total_favoritos'] = $this->getTotalFavoritos($curso->id);
        
        return $cursoData;
    }
}
