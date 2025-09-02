<?php

namespace App\Models;

use Core\Model;

class ReporteAcceso extends Model
{
    protected static $table = 'reportes_acceso';
    protected static $primaryKey = 'id';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'usuario_id',
        'recurso_tipo',
        'recurso_id',
        'recurso_nombre',
        'accion',
        'ip_address',
        'user_agent',
        'duracion_sesion',
        'datos_adicionales'
    ];

    // Tipos de recursos
    const RECURSO_CURSO = 'curso';
    const RECURSO_MATERIAL = 'material';
    const RECURSO_LIBRO = 'libro';
    const RECURSO_LABORATORIO = 'laboratorio';
    const RECURSO_COMPONENTE = 'componente';

    // Tipos de acciones
    const ACCION_VISUALIZAR = 'visualizar';
    const ACCION_DESCARGAR = 'descargar';
    const ACCION_COMPLETAR = 'completar';
    const ACCION_INSCRIBIR = 'inscribir';
    const ACCION_ACCEDER = 'acceder';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Obtener el usuario asociado al reporte
     */
    public function usuario()
    {
        return User::find($this->usuario_id);
    }

    /**
     * Registrar un nuevo acceso
     */
    public static function registrarAcceso($usuarioId, $recursoTipo, $recursoId, $accion, $datosAdicionales = [])
    {
        // Obtener información del request
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Obtener nombre del recurso según el tipo
        $recursoNombre = static::obtenerNombreRecurso($recursoTipo, $recursoId);

        $reporte = new static([
            'usuario_id' => $usuarioId,
            'recurso_tipo' => $recursoTipo,
            'recurso_id' => $recursoId,
            'recurso_nombre' => $recursoNombre,
            'accion' => $accion,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'datos_adicionales' => !empty($datosAdicionales) ? json_encode($datosAdicionales) : null
        ]);

        return $reporte->save();
    }

    /**
     * Obtener nombre del recurso según su tipo e ID
     */
    private static function obtenerNombreRecurso($tipo, $id)
    {
        try {
            switch ($tipo) {
                case self::RECURSO_CURSO:
                    $curso = Curso::find($id);
                    return $curso ? $curso->titulo : "Curso #{$id}";
                
                case self::RECURSO_MATERIAL:
                    $material = Material::find($id);
                    return $material ? $material->titulo : "Material #{$id}";
                
                case self::RECURSO_LIBRO:
                    $libro = Libro::find($id);
                    return $libro ? $libro->titulo : "Libro #{$id}";
                
                case self::RECURSO_LABORATORIO:
                    $laboratorio = Laboratorio::find($id);
                    return $laboratorio ? $laboratorio->nombre : "Laboratorio #{$id}";
                
                case self::RECURSO_COMPONENTE:
                    $componente = Componente::find($id);
                    return $componente ? $componente->nombre : "Componente #{$id}";
                
                default:
                    return "Recurso #{$id}";
            }
        } catch (\Exception $e) {
            return "Recurso #{$id}";
        }
    }

    /**
     * Obtener reportes por usuario
     */
    public static function porUsuario($usuarioId, $fechaInicio = null, $fechaFin = null)
    {
        $query = static::where('usuario_id', '=', $usuarioId);

        if ($fechaInicio) {
            $query->where('fecha_acceso', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha_acceso', '<=', $fechaFin);
        }

        return $query->orderBy('fecha_acceso', 'DESC')->get();
    }

    /**
     * Obtener reportes por recurso
     */
    public static function porRecurso($recursoTipo, $recursoId, $fechaInicio = null, $fechaFin = null)
    {
        $query = static::where('recurso_tipo', '=', $recursoTipo)
                       ->where('recurso_id', '=', $recursoId);

        if ($fechaInicio) {
            $query->where('fecha_acceso', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha_acceso', '<=', $fechaFin);
        }

        return $query->orderBy('fecha_acceso', 'DESC')->get();
    }

    /**
     * Obtener estadísticas de acceso
     */
    public static function estadisticas($fechaInicio = null, $fechaFin = null)
    {
        $query = static::query();

        if ($fechaInicio) {
            $query->where('fecha_acceso', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha_acceso', '<=', $fechaFin);
        }

        $stats = [];

        // Total de accesos
        $stats['total_accesos'] = $query->count();

        // Por tipo de recurso
        $stats['por_recurso'] = [];
        foreach ([self::RECURSO_CURSO, self::RECURSO_MATERIAL, self::RECURSO_LIBRO, self::RECURSO_LABORATORIO, self::RECURSO_COMPONENTE] as $tipo) {
            $stats['por_recurso'][$tipo] = static::where('recurso_tipo', '=', $tipo)->count();
        }

        // Por acción
        $stats['por_accion'] = [];
        foreach ([self::ACCION_VISUALIZAR, self::ACCION_DESCARGAR, self::ACCION_COMPLETAR, self::ACCION_INSCRIBIR, self::ACCION_ACCEDER] as $accion) {
            $stats['por_accion'][$accion] = static::where('accion', '=', $accion)->count();
        }

        // Usuarios más activos
        $stats['usuarios_activos'] = static::query()
            ->select('usuario_id', 'COUNT(*) as total_accesos')
            ->groupBy('usuario_id')
            ->orderBy('total_accesos', 'DESC')
            ->limit(10)
            ->get();

        // Recursos más populares
        $stats['recursos_populares'] = static::query()
            ->select('recurso_tipo', 'recurso_id', 'recurso_nombre', 'COUNT(*) as total_accesos')
            ->groupBy('recurso_tipo', 'recurso_id')
            ->orderBy('total_accesos', 'DESC')
            ->limit(10)
            ->get();

        return $stats;
    }

    /**
     * Obtener accesos recientes
     */
    public static function recientes($limite = 50)
    {
        return static::orderBy('fecha_acceso', 'DESC')->limit($limite)->get();
    }

    /**
     * Obtener reportes con paginación y filtros
     */
    public static function paginados($pagina = 1, $porPagina = 20, $filtros = [])
    {
        $query = static::query();

        // Aplicar filtros
        if (!empty($filtros['usuario_id'])) {
            $query->where('usuario_id', '=', $filtros['usuario_id']);
        }

        if (!empty($filtros['recurso_tipo'])) {
            $query->where('recurso_tipo', '=', $filtros['recurso_tipo']);
        }

        if (!empty($filtros['accion'])) {
            $query->where('accion', '=', $filtros['accion']);
        }

        if (!empty($filtros['fecha_inicio'])) {
            $query->where('fecha_acceso', '>=', $filtros['fecha_inicio']);
        }

        if (!empty($filtros['fecha_fin'])) {
            $query->where('fecha_acceso', '<=', $filtros['fecha_fin']);
        }

        if (!empty($filtros['ip_address'])) {
            $query->where('ip_address', '=', $filtros['ip_address']);
        }

        // Ordenar por más reciente
        $query->orderBy('fecha_acceso', 'DESC');

        // Calcular offset
        $offset = ($pagina - 1) * $porPagina;
        
        return $query->limit($porPagina, $offset)->get();
    }

    /**
     * Obtener datos adicionales como array
     */
    public function getDatosAdicionales()
    {
        return $this->datos_adicionales ? json_decode($this->datos_adicionales, true) : [];
    }

    /**
     * Establecer datos adicionales
     */
    public function setDatosAdicionales(array $datos)
    {
        $this->datos_adicionales = json_encode($datos);
    }

    /**
     * Generar reporte de actividad diaria
     */
    public static function actividadDiaria($dias = 30)
    {
        $fechaInicio = date('Y-m-d', strtotime("-{$dias} days"));
        
        return static::query()
            ->select('DATE(fecha_acceso) as fecha', 'COUNT(*) as total_accesos')
            ->where('fecha_acceso', '>=', $fechaInicio)
            ->groupBy('DATE(fecha_acceso)')
            ->orderBy('fecha', 'ASC')
            ->get();
    }

    /**
     * Limpiar reportes antiguos (más de X días)
     */
    public static function limpiarAntiguos($diasConservar = 365)
    {
        $fechaLimite = date('Y-m-d', strtotime("-{$diasConservar} days"));
        
        return static::where('fecha_acceso', '<', $fechaLimite)->delete();
    }
}