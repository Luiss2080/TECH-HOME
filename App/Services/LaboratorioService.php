<?php

namespace App\Services;

use App\Models\Laboratorio;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Componente;
use Core\DB;
use Exception;

class LaboratorioService
{
    /**
     * Obtener todos los laboratorios con información relacionada
     */
    public function getAllLaboratorios(): array
    {
        $laboratorios = Laboratorio::all();
        $laboratoriosData = [];

        foreach ($laboratorios as $laboratorio) {
            $laboratorioData = $laboratorio->getAttributes();
            
            // Obtener información del docente responsable
            $docente = $laboratorio->docenteResponsable();
            if ($docente) {
                $laboratorioData['docente_nombre'] = $docente->nombre . ' ' . $docente->apellido;
            } else {
                $laboratorioData['docente_nombre'] = 'No asignado';
            }

            // Obtener información de la categoría
            $categoria = $laboratorio->categoria();
            if ($categoria) {
                $laboratorioData['categoria_nombre'] = $categoria->nombre;
                $laboratorioData['categoria_color'] = $categoria->color;
                $laboratorioData['categoria_icono'] = $categoria->icono;
            } else {
                $laboratorioData['categoria_nombre'] = 'Sin categoría';
                $laboratorioData['categoria_color'] = '#6c757d';
                $laboratorioData['categoria_icono'] = 'fas fa-folder';
            }

            // Agregar información adicional procesada
            $laboratorioData['duracion_formateada'] = $laboratorio->getDuracionFormateada();
            $laboratorioData['progreso'] = $laboratorio->getProgreso();
            $laboratorioData['clase_estado'] = $laboratorio->getClaseEstado();
            $laboratorioData['clase_nivel'] = $laboratorio->getClaseNivel();
            $laboratorioData['participantes_array'] = $laboratorio->getParticipantes();
            $laboratorioData['componentes_array'] = $laboratorio->getComponentesUtilizados();
            $laboratorioData['tecnologias_array'] = $laboratorio->getTecnologias();
            $laboratorioData['total_participantes'] = count($laboratorio->getParticipantes());
            
            $laboratoriosData[] = $laboratorioData;
        }

        return $laboratoriosData;
    }

    /**
     * Obtener laboratorio por ID
     */
    public function getLaboratorioById(int $id): ?Laboratorio
    {
        return Laboratorio::find($id);
    }

    /**
     * Crear nuevo laboratorio
     */
    public function createLaboratorio(array $laboratorioData): int
    {
        // Procesar arrays JSON
        if (isset($laboratorioData['participantes']) && is_array($laboratorioData['participantes'])) {
            $laboratorioData['participantes'] = json_encode($laboratorioData['participantes']);
        }

        if (isset($laboratorioData['componentes_utilizados']) && is_array($laboratorioData['componentes_utilizados'])) {
            $laboratorioData['componentes_utilizados'] = json_encode($laboratorioData['componentes_utilizados']);
        }

        if (isset($laboratorioData['tecnologias']) && is_array($laboratorioData['tecnologias'])) {
            $laboratorioData['tecnologias'] = json_encode($laboratorioData['tecnologias']);
        }

        // Establecer timestamps
        $laboratorioData['fecha_creacion'] = date('Y-m-d H:i:s');
        $laboratorioData['fecha_actualizacion'] = date('Y-m-d H:i:s');

        // Crear el laboratorio
        $laboratorio = new Laboratorio([
            'nombre' => $laboratorioData['nombre'],
            'descripcion' => $laboratorioData['descripcion'],
            'objetivos' => $laboratorioData['objetivos'] ?? null,
            'categoria_id' => $laboratorioData['categoria_id'],
            'docente_responsable_id' => $laboratorioData['docente_responsable_id'],
            'participantes' => $laboratorioData['participantes'] ?? null,
            'componentes_utilizados' => $laboratorioData['componentes_utilizados'] ?? null,
            'tecnologias' => $laboratorioData['tecnologias'] ?? null,
            'resultado' => $laboratorioData['resultado'] ?? null,
            'conclusiones' => $laboratorioData['conclusiones'] ?? null,
            'nivel_dificultad' => $laboratorioData['nivel_dificultad'] ?? 'Básico',
            'duracion_dias' => $laboratorioData['duracion_dias'] ?? null,
            'fecha_inicio' => $laboratorioData['fecha_inicio'] ?? null,
            'fecha_fin' => $laboratorioData['fecha_fin'] ?? null,
            'estado' => $laboratorioData['estado'] ?? 'Planificado',
            'publico' => $laboratorioData['publico'] ?? 1,
            'destacado' => $laboratorioData['destacado'] ?? 0,
            'fecha_creacion' => $laboratorioData['fecha_creacion'],
            'fecha_actualizacion' => $laboratorioData['fecha_actualizacion']
        ]);

        // Validar datos antes de guardar
        $errores = $laboratorio->validarDatos();
        if (!empty($errores)) {
            throw new Exception('Errores de validación: ' . implode(', ', $errores));
        }

        $laboratorio->save();
        return $laboratorio->getKey();
    }

    /**
     * Actualizar laboratorio existente
     */
    public function updateLaboratorio(int $id, array $laboratorioData): bool
    {
        $laboratorio = Laboratorio::find($id);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        // Procesar arrays JSON
        if (isset($laboratorioData['participantes']) && is_array($laboratorioData['participantes'])) {
            $laboratorioData['participantes'] = json_encode($laboratorioData['participantes']);
        }

        if (isset($laboratorioData['componentes_utilizados']) && is_array($laboratorioData['componentes_utilizados'])) {
            $laboratorioData['componentes_utilizados'] = json_encode($laboratorioData['componentes_utilizados']);
        }

        if (isset($laboratorioData['tecnologias']) && is_array($laboratorioData['tecnologias'])) {
            $laboratorioData['tecnologias'] = json_encode($laboratorioData['tecnologias']);
        }

        // Actualizar timestamp
        $laboratorioData['fecha_actualizacion'] = date('Y-m-d H:i:s');

        // Actualizar campos permitidos
        $camposPermitidos = [
            'nombre', 'descripcion', 'objetivos', 'categoria_id', 
            'docente_responsable_id', 'participantes', 'componentes_utilizados',
            'tecnologias', 'resultado', 'conclusiones', 'nivel_dificultad',
            'duracion_dias', 'fecha_inicio', 'fecha_fin', 'estado',
            'publico', 'destacado', 'fecha_actualizacion'
        ];

        foreach ($camposPermitidos as $campo) {
            if (array_key_exists($campo, $laboratorioData)) {
                $laboratorio->$campo = $laboratorioData[$campo];
            }
        }

        // Validar datos antes de guardar
        $errores = $laboratorio->validarDatos();
        if (!empty($errores)) {
            throw new Exception('Errores de validación: ' . implode(', ', $errores));
        }

        $laboratorio->save();
        return true;
    }

    /**
     * Eliminar laboratorio
     */
    public function deleteLaboratorio(int $id): bool
    {
        $laboratorio = Laboratorio::find($id);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        try {
            // Verificar si puede eliminarse según el estado
            if ($laboratorio->estado === 'En Progreso') {
                throw new Exception('No se puede eliminar un laboratorio en progreso. Suspéndelo o márcalo como completado primero.');
            }

            // Eliminar de la base de datos
            $laboratorio->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar laboratorio: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del laboratorio
     */
    public function changeStatus(int $id, string $estado): bool
    {
        $laboratorio = Laboratorio::find($id);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        $estadosPermitidos = ['Planificado', 'En Progreso', 'Completado', 'Suspendido', 'Cancelado'];
        if (!in_array($estado, $estadosPermitidos)) {
            throw new Exception('Estado no válido');
        }

        $laboratorio->estado = $estado;
        $laboratorio->fecha_actualizacion = date('Y-m-d H:i:s');
        $laboratorio->save();

        return true;
    }

    /**
     * Cambiar visibilidad pública del laboratorio
     */
    public function changePublicStatus(int $id, int $publico): bool
    {
        $laboratorio = Laboratorio::find($id);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        $laboratorio->publico = $publico;
        $laboratorio->fecha_actualizacion = date('Y-m-d H:i:s');
        $laboratorio->save();

        return true;
    }

    /**
     * Cambiar estado destacado del laboratorio
     */
    public function changeDestacadoStatus(int $id, int $destacado): bool
    {
        $laboratorio = Laboratorio::find($id);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        $laboratorio->destacado = $destacado;
        $laboratorio->fecha_actualizacion = date('Y-m-d H:i:s');
        $laboratorio->save();

        return true;
    }

    /**
     * Obtener todas las categorías disponibles
     */
    public function getAllCategories(): array
    {
        return Categoria::where('estado', '=', 1)->get();
    }

    /**
     * Obtener todos los docentes disponibles
     */
    public function getAllDocentes(): array
    {
        $docentes = [];
        $usuarios = User::where('estado', '=', 1)->get();
        
        foreach ($usuarios as $usuario) {
            if ($usuario->hasRole('docente') || $usuario->hasRole('administrador')) {
                $docentes[] = $usuario;
            }
        }
        
        return $docentes;
    }

    /**
     * Obtener todos los componentes disponibles
     */
    public function getAllComponentes(): array
    {
        // Si existe el modelo Componente, usarlo
        if (class_exists('App\Models\Componente')) {
            return Componente::where('estado', '!=', 'Descontinuado')->get();
        }
        return [];
    }

    /**
     * Buscar laboratorios con filtros
     */
    public function searchLaboratorios(array $filters): array
    {
        $query = Laboratorio::query();

        // Filtro por término de búsqueda
        if (!empty($filters['buscar'])) {
            $termino = $filters['buscar'];
            $query = $query->whereRaw(
                "(nombre LIKE ? OR descripcion LIKE ? OR objetivos LIKE ?)", 
                ["%{$termino}%", "%{$termino}%", "%{$termino}%"]
            );
        }

        // Filtro por estado
        if (!empty($filters['estado']) && $filters['estado'] !== 'todos') {
            $query = $query->where('estado', '=', $filters['estado']);
        }

        // Filtro por nivel de dificultad
        if (!empty($filters['nivel']) && $filters['nivel'] !== 'todos') {
            $query = $query->where('nivel_dificultad', '=', $filters['nivel']);
        }

        // Filtro por categoría
        if (!empty($filters['categoria']) && $filters['categoria'] !== 'todas') {
            $query = $query->where('categoria_id', '=', $filters['categoria']);
        }

        // Filtro por docente responsable
        if (!empty($filters['docente']) && $filters['docente'] !== 'todos') {
            $query = $query->where('docente_responsable_id', '=', $filters['docente']);
        }

        // Filtro por visibilidad
        if (isset($filters['publico']) && $filters['publico'] !== '') {
            $query = $query->where('publico', '=', $filters['publico']);
        }

        // Filtro por destacado
        if (isset($filters['destacado']) && $filters['destacado'] !== '') {
            $query = $query->where('destacado', '=', $filters['destacado']);
        }

        // Filtro por fechas
        if (!empty($filters['fecha_desde'])) {
            $query = $query->where('fecha_inicio', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query = $query->where('fecha_fin', '<=', $filters['fecha_hasta']);
        }

        // Ordenamiento
        $orden = $filters['orden'] ?? 'fecha_desc';
        switch ($orden) {
            case 'nombre_asc':
                $query = $query->orderBy('nombre', 'ASC');
                break;
            case 'nombre_desc':
                $query = $query->orderBy('nombre', 'DESC');
                break;
            case 'estado_asc':
                $query = $query->orderBy('estado', 'ASC');
                break;
            case 'nivel_asc':
                $query = $query->orderBy('nivel_dificultad', 'ASC');
                break;
            case 'fecha_asc':
                $query = $query->orderBy('fecha_creacion', 'ASC');
                break;
            case 'fecha_desc':
            default:
                $query = $query->orderBy('fecha_creacion', 'DESC');
                break;
        }

        return $query->get();
    }

    /**
     * Obtener estadísticas generales de laboratorios
     */
    public function getGeneralStats(): array
    {
        return [
            'total_laboratorios' => Laboratorio::count(),
            'publicos' => Laboratorio::where('publico', '=', 1)->count(),
            'destacados' => Laboratorio::where('destacado', '=', 1)->count(),
            'activos' => Laboratorio::whereIn('estado', ['Planificado', 'En Progreso'])->count(),
            'completados' => Laboratorio::where('estado', '=', 'Completado')->count(),
            'por_estado' => Laboratorio::contarPorEstado(),
            'por_nivel' => Laboratorio::contarPorNivel(),
            'recientes_30_dias' => Laboratorio::recientes(30)->count(),
            'mas_destacados' => Laboratorio::masDestacados(5),
            'proximos_a_vencer' => Laboratorio::proximosAVencer(7),
            'en_curso' => Laboratorio::where('estado', '=', 'En Progreso')->count()
        ];
    }

    /**
     * Gestionar participantes del laboratorio
     */
    public function addParticipante(int $laboratorioId, int $userId): bool
    {
        $laboratorio = Laboratorio::find($laboratorioId);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        if (!$laboratorio->puedeParticipar($userId)) {
            throw new Exception('El usuario no puede participar en este laboratorio');
        }

        if ($laboratorio->agregarParticipante($userId)) {
            $laboratorio->fecha_actualizacion = date('Y-m-d H:i:s');
            $laboratorio->save();
            return true;
        }

        return false; // Ya era participante
    }

    /**
     * Remover participante del laboratorio
     */
    public function removeParticipante(int $laboratorioId, int $userId): bool
    {
        $laboratorio = Laboratorio::find($laboratorioId);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        if ($laboratorio->removerParticipante($userId)) {
            $laboratorio->fecha_actualizacion = date('Y-m-d H:i:s');
            $laboratorio->save();
            return true;
        }

        return false; // No era participante
    }

    /**
     * Validar permisos de acceso para un usuario
     */
    public function canAccess(int $laboratorioId, ?int $userId = null): bool
    {
        $laboratorio = Laboratorio::find($laboratorioId);
        if (!$laboratorio) {
            return false;
        }

        return $laboratorio->puedeVer($userId);
    }

    /**
     * Obtener laboratorios por docente
     */
    public function getLaboratoriosByDocente(int $docenteId): array
    {
        return Laboratorio::where('docente_responsable_id', '=', $docenteId)
            ->orderBy('fecha_creacion', 'DESC')
            ->get();
    }

    /**
     * Obtener laboratorios por categoría
     */
    public function getLaboratoriosByCategoria(int $categoriaId): array
    {
        return Laboratorio::where('categoria_id', '=', $categoriaId)
            ->where('publico', '=', 1)
            ->orderBy('fecha_creacion', 'DESC')
            ->get();
    }

    /**
     * Obtener laboratorios por estado
     */
    public function getLaboratoriosByEstado(string $estado): array
    {
        return Laboratorio::where('estado', '=', $estado)
            ->orderBy('fecha_creacion', 'DESC')
            ->get();
    }

    /**
     * Duplicar laboratorio
     */
    public function duplicateLaboratorio(int $id, int $nuevoDocenteId): int
    {
        $laboratorio = Laboratorio::find($id);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        $nuevoLaboratorioData = $laboratorio->getAttributes();
        
        // Limpiar campos que no deben duplicarse
        unset($nuevoLaboratorioData['id']);
        $nuevoLaboratorioData['nombre'] = 'Copia de ' . $nuevoLaboratorioData['nombre'];
        $nuevoLaboratorioData['docente_responsable_id'] = $nuevoDocenteId;
        $nuevoLaboratorioData['estado'] = 'Planificado';
        $nuevoLaboratorioData['destacado'] = 0;
        $nuevoLaboratorioData['fecha_creacion'] = date('Y-m-d H:i:s');
        $nuevoLaboratorioData['fecha_actualizacion'] = date('Y-m-d H:i:s');
        
        // Limpiar fechas específicas
        $nuevoLaboratorioData['fecha_inicio'] = null;
        $nuevoLaboratorioData['fecha_fin'] = null;
        $nuevoLaboratorioData['resultado'] = null;
        $nuevoLaboratorioData['conclusiones'] = null;
        
        // Limpiar participantes (el nuevo docente deberá asignarlos)
        $nuevoLaboratorioData['participantes'] = null;

        return $this->createLaboratorio($nuevoLaboratorioData);
    }

    /**
     * Actualizar fechas del laboratorio
     */
    public function updateFechas(int $id, ?string $fechaInicio = null, ?string $fechaFin = null): bool
    {
        $laboratorio = Laboratorio::find($id);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        if ($fechaInicio) {
            $laboratorio->fecha_inicio = $fechaInicio;
        }

        if ($fechaFin) {
            $laboratorio->fecha_fin = $fechaFin;
        }

        // Validar fechas
        $errores = $laboratorio->validarDatos();
        if (!empty($errores)) {
            throw new Exception('Errores de validación: ' . implode(', ', $errores));
        }

        $laboratorio->fecha_actualizacion = date('Y-m-d H:i:s');
        $laboratorio->save();

        return true;
    }

    /**
     * Obtener dashboard data para docente
     */
    public function getDashboardDataForDocente(int $docenteId): array
    {
        return [
            'estadisticas' => Laboratorio::estadisticasPorDocente($docenteId),
            'laboratorios_activos' => $this->getLaboratoriosByDocente($docenteId),
            'proximos_a_vencer' => Laboratorio::where('docente_responsable_id', '=', $docenteId)
                ->whereIn('estado', ['En Progreso'])
                ->whereRaw('fecha_fin BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY)')
                ->get(),
            'recientes' => Laboratorio::where('docente_responsable_id', '=', $docenteId)
                ->where('fecha_creacion', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
                ->orderBy('fecha_creacion', 'DESC')
                ->limit(5)
                ->get()
        ];
    }

    /**
     * Exportar datos del laboratorio
     */
    public function exportLaboratorioData(int $id): array
    {
        $laboratorio = Laboratorio::find($id);
        if (!$laboratorio) {
            throw new Exception('Laboratorio no encontrado');
        }

        $docente = $laboratorio->docenteResponsable();
        $categoria = $laboratorio->categoria();

        return [
            'laboratorio' => $laboratorio->getAttributes(),
            'docente' => $docente ? $docente->getAttributes() : null,
            'categoria' => $categoria ? $categoria->getAttributes() : null,
            'participantes_data' => $this->getParticipantesData($laboratorio->getParticipantes()),
            'estadisticas' => $laboratorio->getEstadisticas(),
            'exportado_en' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Obtener datos de participantes
     */
    private function getParticipantesData(array $participantesIds): array
    {
        if (empty($participantesIds)) {
            return [];
        }

        $participantes = [];
        foreach ($participantesIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $participantes[] = [
                    'id' => $user->id,
                    'nombre' => $user->nombre . ' ' . $user->apellido,
                    'email' => $user->email
                ];
            }
        }

        return $participantes;
    }
}
