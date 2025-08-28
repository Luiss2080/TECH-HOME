<?php

namespace App\Models;

use Core\Model;

class Laboratorio extends Model
{
    protected $table = 'laboratorios';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'objetivos',
        'categoria_id',
        'docente_responsable_id',
        'participantes',
        'componentes_utilizados',
        'tecnologias',
        'resultado',
        'conclusiones',
        'nivel_dificultad',
        'duracion_dias',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'publico',
        'destacado',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    protected $hidden = [];
    protected $timestamps = false; // Manejo manual de timestamps

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Relación con Categoria
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    /**
     * Relación con Docente Responsable (Usuario)
     */
    public function docenteResponsable()
    {
        return $this->belongsTo(User::class, 'docente_responsable_id', 'id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Laboratorios públicos
     */
    public static function publicos()
    {
        return self::where('publico', '=', 1);
    }

    /**
     * Laboratorios destacados
     */
    public static function destacados()
    {
        return self::where('destacado', '=', 1);
    }

    /**
     * Laboratorios por estado
     */
    public static function porEstado($estado)
    {
        return self::where('estado', '=', $estado);
    }

    /**
     * Laboratorios por nivel de dificultad
     */
    public static function porNivel($nivel)
    {
        return self::where('nivel_dificultad', '=', $nivel);
    }

    /**
     * Laboratorios por categoría
     */
    public static function porCategoria($categoriaId)
    {
        return self::where('categoria_id', '=', $categoriaId);
    }

    /**
     * Laboratorios por docente responsable
     */
    public static function porDocente($docenteId)
    {
        return self::where('docente_responsable_id', '=', $docenteId);
    }

    /**
     * Laboratorios activos (en progreso o completados)
     */
    public static function activos()
    {
        return self::whereIn('estado', ['En Progreso', 'Completado']);
    }

    /**
     * Laboratorios disponibles (activos y públicos)
     */
    public static function disponibles()
    {
        return self::whereIn('estado', ['En Progreso', 'Completado'])->where('publico', '=', 1);
    }

    /**
     * Laboratorios recientes
     */
    public static function recientes(int $dias = 30)
    {
        return self::whereRaw('fecha_creacion >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
            ->orderBy('fecha_creacion', 'desc');
    }

    /**
     * Laboratorios en curso actual
     */
    public static function enCurso()
    {
        return self::where('estado', '=', 'En Progreso')
            ->whereRaw('(fecha_inicio IS NULL OR fecha_inicio <= CURRENT_DATE)')
            ->whereRaw('(fecha_fin IS NULL OR fecha_fin >= CURRENT_DATE)');
    }

    /**
     * Laboratorios próximos a iniciar
     */
    public static function proximosAIniciar($dias = 7)
    {
        return self::where('estado', '=', 'Planificado')
            ->whereRaw('fecha_inicio BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL ? DAY)', [$dias]);
    }

    /**
     * Buscar laboratorios por término
     */
    public static function buscar($termino)
    {
        return self::whereRaw("(nombre LIKE ? OR descripcion LIKE ? OR objetivos LIKE ?)", 
            ["%{$termino}%", "%{$termino}%", "%{$termino}%"]);
    }

    // ==========================================
    // MÉTODOS AUXILIARES PARA JSON
    // ==========================================

    /**
     * Obtener participantes como array
     */
    public function getParticipantes()
    {
        if (empty($this->participantes)) {
            return [];
        }
        return json_decode($this->participantes, true) ?: [];
    }

    /**
     * Establecer participantes desde array
     */
    public function setParticipantes(array $participantes)
    {
        $this->participantes = json_encode(array_values($participantes));
    }

    /**
     * Obtener componentes utilizados como array
     */
    public function getComponentesUtilizados()
    {
        if (empty($this->componentes_utilizados)) {
            return [];
        }
        return json_decode($this->componentes_utilizados, true) ?: [];
    }

    /**
     * Establecer componentes utilizados desde array
     */
    public function setComponentesUtilizados(array $componentes)
    {
        $this->componentes_utilizados = json_encode(array_values($componentes));
    }

    /**
     * Obtener tecnologías como array
     */
    public function getTecnologias()
    {
        if (empty($this->tecnologias)) {
            return [];
        }
        return json_decode($this->tecnologias, true) ?: [];
    }

    /**
     * Establecer tecnologías desde array
     */
    public function setTecnologias(array $tecnologias)
    {
        $this->tecnologias = json_encode(array_values($tecnologias));
    }

    // ==========================================
    // MÉTODOS DE INFORMACIÓN
    // ==========================================

    /**
     * Obtener duración formateada
     */
    public function getDuracionFormateada()
    {
        if (!$this->duracion_dias || $this->duracion_dias == 0) {
            return 'No especificada';
        }

        if ($this->duracion_dias == 1) {
            return '1 día';
        }

        if ($this->duracion_dias < 7) {
            return $this->duracion_dias . ' días';
        }

        $semanas = intval($this->duracion_dias / 7);
        $diasRestantes = $this->duracion_dias % 7;

        $texto = $semanas . ' semana' . ($semanas > 1 ? 's' : '');
        if ($diasRestantes > 0) {
            $texto .= ' y ' . $diasRestantes . ' día' . ($diasRestantes > 1 ? 's' : '');
        }

        return $texto;
    }

    /**
     * Obtener progreso del laboratorio (0-100)
     */
    public function getProgreso()
    {
        switch ($this->estado) {
            case 'Planificado':
                return 0;
            case 'En Progreso':
                return $this->calcularProgresoFechas();
            case 'Completado':
                return 100;
            case 'Suspendido':
            case 'Cancelado':
                return $this->calcularProgresoFechas();
            default:
                return 0;
        }
    }

    /**
     * Calcular progreso basado en fechas
     */
    private function calcularProgresoFechas()
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return 50; // Progreso estimado si no hay fechas
        }

        $inicio = strtotime($this->fecha_inicio);
        $fin = strtotime($this->fecha_fin);
        $actual = time();

        if ($actual < $inicio) {
            return 0;
        }

        if ($actual > $fin) {
            return 100;
        }

        $totalDuration = $fin - $inicio;
        $currentDuration = $actual - $inicio;

        return min(100, max(0, intval(($currentDuration / $totalDuration) * 100)));
    }

    /**
     * Obtener clase CSS según estado
     */
    public function getClaseEstado()
    {
        $clases = [
            'Planificado' => 'primary',
            'En Progreso' => 'warning',
            'Completado' => 'success',
            'Suspendido' => 'secondary',
            'Cancelado' => 'danger'
        ];

        return $clases[$this->estado] ?? 'secondary';
    }

    /**
     * Obtener clase CSS según nivel
     */
    public function getClaseNivel()
    {
        $clases = [
            'Básico' => 'success',
            'Intermedio' => 'info',
            'Avanzado' => 'warning',
            'Experto' => 'danger'
        ];

        return $clases[$this->nivel_dificultad] ?? 'secondary';
    }

    /**
     * Verificar si el laboratorio está activo
     */
    public function estaActivo()
    {
        return in_array($this->estado, ['Planificado', 'En Progreso']);
    }

    /**
     * Verificar si el laboratorio puede ser editado
     */
    public function puedeEditarse()
    {
        return $this->estado !== 'Completado';
    }

    /**
     * Verificar si un usuario puede ver el laboratorio
     */
    public function puedeVer($userId = null)
    {
        // Si es público, todos pueden verlo
        if ($this->publico == 1) {
            return true;
        }

        // Si no hay usuario, no puede ver laboratorios privados
        if (!$userId) {
            return false;
        }

        // El docente responsable siempre puede verlo
        if ($this->docente_responsable_id == $userId) {
            return true;
        }

        // Los participantes pueden verlo
        $participantes = $this->getParticipantes();
        if (in_array($userId, $participantes)) {
            return true;
        }

        // Los administradores siempre pueden verlo
        $user = User::find($userId);
        if ($user && $user->hasRole('administrador')) {
            return true;
        }

        return false;
    }

    /**
     * Verificar si un usuario puede participar
     */
    public function puedeParticipar($userId)
    {
        if (!$userId || !$this->estaActivo()) {
            return false;
        }

        // Ya es participante
        $participantes = $this->getParticipantes();
        if (in_array($userId, $participantes)) {
            return true;
        }

        // Solo si es público y está en progreso o planificado
        return $this->publico == 1;
    }

    /**
     * Agregar participante
     */
    public function agregarParticipante($userId)
    {
        $participantes = $this->getParticipantes();
        if (!in_array($userId, $participantes)) {
            $participantes[] = $userId;
            $this->setParticipantes($participantes);
            return true;
        }
        return false;
    }

    /**
     * Remover participante
     */
    public function removerParticipante($userId)
    {
        $participantes = $this->getParticipantes();
        $index = array_search($userId, $participantes);
        if ($index !== false) {
            unset($participantes[$index]);
            $this->setParticipantes($participantes);
            return true;
        }
        return false;
    }

    /**
     * Obtener estadísticas del laboratorio
     */
    public function getEstadisticas()
    {
        $participantes = $this->getParticipantes();
        $componentes = $this->getComponentesUtilizados();
        $tecnologias = $this->getTecnologias();

        return [
            'total_participantes' => count($participantes),
            'total_componentes' => count($componentes),
            'total_tecnologias' => count($tecnologias),
            'duracion_formateada' => $this->getDuracionFormateada(),
            'progreso' => $this->getProgreso(),
            'estado' => $this->estado,
            'nivel' => $this->nivel_dificultad,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'publico' => $this->publico ? 'Sí' : 'No',
            'destacado' => $this->destacado ? 'Sí' : 'No'
        ];
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS DE ESTADÍSTICAS
    // ==========================================

    /**
     * Contar laboratorios por estado
     */
    public static function contarPorEstado()
    {
        $estados = ['Planificado', 'En Progreso', 'Completado', 'Suspendido', 'Cancelado'];
        $estadisticas = [];

        foreach ($estados as $estado) {
            $estadisticas[$estado] = self::porEstado($estado)->count();
        }

        return $estadisticas;
    }

    /**
     * Contar laboratorios por nivel
     */
    public static function contarPorNivel()
    {
        $niveles = ['Básico', 'Intermedio', 'Avanzado', 'Experto'];
        $estadisticas = [];

        foreach ($niveles as $nivel) {
            $estadisticas[$nivel] = self::porNivel($nivel)->count();
        }

        return $estadisticas;
    }

    /**
     * Obtener laboratorios más destacados
     */
    public static function masDestacados($limit = 5)
    {
        return self::where('destacado', '=', 1)
            ->where('publico', '=', 1)
            ->orderBy('fecha_creacion', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas generales
     */
    public static function getEstadisticasGenerales()
    {
        return [
            'total' => self::count(),
            'publicos' => self::where('publico', '=', 1)->count(),
            'destacados' => self::where('destacado', '=', 1)->count(),
            'activos' => self::whereIn('estado', ['En Progreso', 'Completado'])->count(),
            'completados' => self::where('estado', '=', 'Completado')->count(),
            'en_progreso' => self::where('estado', '=', 'En Progreso')->count(),
            'por_estado' => self::contarPorEstado(),
            'por_nivel' => self::contarPorNivel()
        ];
    }

    /**
     * Validar datos antes de guardar
     */
    public function validarDatos()
    {
        $errores = [];

        // Validar fechas
        if ($this->fecha_inicio && $this->fecha_fin) {
            if (strtotime($this->fecha_inicio) > strtotime($this->fecha_fin)) {
                $errores[] = 'La fecha de inicio no puede ser posterior a la fecha de fin';
            }
        }

        // Validar duración
        if ($this->duracion_dias && $this->duracion_dias < 1) {
            $errores[] = 'La duración debe ser de al menos 1 día';
        }

        // Validar JSON
        if ($this->participantes && !json_decode($this->participantes)) {
            $errores[] = 'Formato de participantes inválido';
        }

        if ($this->componentes_utilizados && !json_decode($this->componentes_utilizados)) {
            $errores[] = 'Formato de componentes inválido';
        }

        if ($this->tecnologias && !json_decode($this->tecnologias)) {
            $errores[] = 'Formato de tecnologías inválido';
        }

        return $errores;
    }

    /**
     * Obtener próximos laboratorios a vencer
     */
    public static function proximosAVencer($dias = 7)
    {
        return self::where('estado', '=', 'En Progreso')
            ->whereRaw('fecha_fin BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL ? DAY)', [$dias])
            ->orderBy('fecha_fin', 'ASC')
            ->get();
    }

    /**
     * Obtener laboratorios por docente con estadísticas
     */
    public static function estadisticasPorDocente($docenteId)
    {
        $laboratorios = self::porDocente($docenteId)->get();
        
        $stats = [
            'total' => count($laboratorios),
            'completados' => 0,
            'en_progreso' => 0,
            'planificados' => 0,
            'participantes_totales' => 0
        ];

        foreach ($laboratorios as $lab) {
            switch ($lab->estado) {
                case 'Completado':
                    $stats['completados']++;
                    break;
                case 'En Progreso':
                    $stats['en_progreso']++;
                    break;
                case 'Planificado':
                    $stats['planificados']++;
                    break;
            }
            $stats['participantes_totales'] += count($lab->getParticipantes());
        }

        return $stats;
    }
}
