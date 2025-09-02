<?php

namespace App\Models;

use Core\Model;

class Suscripcion extends Model
{
    protected static $table = 'suscripciones';
    protected static $primaryKey = 'id';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'usuario_id',
        'tipo_plan',
        'fecha_inicio',
        'fecha_vencimiento',
        'estado',
        'precio',
        'metodo_pago',
        'descripcion',
        'caracteristicas'
    ];

    // Tipos de plan disponibles
    const PLAN_BASICO = 'basico';
    const PLAN_PREMIUM = 'premium';
    const PLAN_PROFESIONAL = 'profesional';

    // Estados de suscripción
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_SUSPENDIDA = 'suspendida';
    const ESTADO_CANCELADA = 'cancelada';
    const ESTADO_EXPIRADA = 'expirada';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Obtener el usuario asociado a la suscripción
     */
    public function usuario()
    {
        return User::find($this->usuario_id);
    }

    /**
     * Obtener todas las suscripciones activas
     */
    public static function activas()
    {
        return static::where('estado', '=', self::ESTADO_ACTIVA);
    }

    /**
     * Obtener suscripciones por tipo de plan
     */
    public static function porPlan($tipoPlan)
    {
        return static::where('tipo_plan', '=', $tipoPlan);
    }

    /**
     * Obtener suscripciones que expiran pronto (próximos 7 días)
     */
    public static function proximasAVencer($dias = 7)
    {
        $fechaLimite = date('Y-m-d', strtotime("+{$dias} days"));
        return static::where('fecha_vencimiento', '<=', $fechaLimite)
                     ->where('estado', '=', self::ESTADO_ACTIVA);
    }

    /**
     * Verificar si la suscripción está activa
     */
    public function estaActiva()
    {
        return $this->estado === self::ESTADO_ACTIVA && 
               strtotime($this->fecha_vencimiento) > time();
    }

    /**
     * Verificar si la suscripción ha expirado
     */
    public function haExpirado()
    {
        return strtotime($this->fecha_vencimiento) < time();
    }

    /**
     * Cancelar suscripción
     */
    public function cancelar($motivo = null)
    {
        $this->estado = self::ESTADO_CANCELADA;
        $this->descripcion = $motivo ? "Cancelada: {$motivo}" : "Cancelada por administrador";
        return $this->save();
    }

    /**
     * Suspender suscripción
     */
    public function suspender($motivo = null)
    {
        $this->estado = self::ESTADO_SUSPENDIDA;
        $this->descripcion = $motivo ? "Suspendida: {$motivo}" : "Suspendida por administrador";
        return $this->save();
    }

    /**
     * Reactivar suscripción
     */
    public function reactivar()
    {
        if (!$this->haExpirado()) {
            $this->estado = self::ESTADO_ACTIVA;
            return $this->save();
        }
        return false;
    }

    /**
     * Obtener características de la suscripción
     */
    public function getCaracteristicas()
    {
        return $this->caracteristicas ? json_decode($this->caracteristicas, true) : [];
    }

    /**
     * Establecer características de la suscripción
     */
    public function setCaracteristicas(array $caracteristicas)
    {
        $this->caracteristicas = json_encode($caracteristicas);
    }

    /**
     * Obtener estadísticas de suscripciones
     */
    public static function estadisticas()
    {
        $stats = [];
        
        // Total por estado
        $stats['por_estado'] = [
            'activas' => static::where('estado', '=', self::ESTADO_ACTIVA)->count(),
            'suspendidas' => static::where('estado', '=', self::ESTADO_SUSPENDIDA)->count(),
            'canceladas' => static::where('estado', '=', self::ESTADO_CANCELADA)->count(),
            'expiradas' => static::where('estado', '=', self::ESTADO_EXPIRADA)->count()
        ];

        // Total por plan
        $stats['por_plan'] = [
            'basico' => static::where('tipo_plan', '=', self::PLAN_BASICO)->count(),
            'premium' => static::where('tipo_plan', '=', self::PLAN_PREMIUM)->count(),
            'profesional' => static::where('tipo_plan', '=', self::PLAN_PROFESIONAL)->count()
        ];

        // Ingresos
        $stats['ingresos'] = [
            'total' => static::sum('precio'),
            'este_mes' => static::where('fecha_inicio', '>=', date('Y-m-01'))->sum('precio')
        ];

        // Próximas a vencer
        $stats['proximas_vencer'] = static::proximasAVencer()->count();

        return $stats;
    }

    /**
     * Renovar suscripción
     */
    public function renovar($meses = 1, $precio = null)
    {
        $nuevaFechaVencimiento = date('Y-m-d', strtotime("+{$meses} months", strtotime($this->fecha_vencimiento)));
        
        $this->fecha_vencimiento = $nuevaFechaVencimiento;
        $this->estado = self::ESTADO_ACTIVA;
        
        if ($precio !== null) {
            $this->precio = $precio;
        }

        return $this->save();
    }

    /**
     * Obtener suscripciones con paginación
     */
    public static function paginadas($pagina = 1, $porPagina = 20, $filtros = [])
    {
        $query = static::query();

        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $query->where('estado', '=', $filtros['estado']);
        }

        if (!empty($filtros['tipo_plan'])) {
            $query->where('tipo_plan', '=', $filtros['tipo_plan']);
        }

        if (!empty($filtros['usuario_id'])) {
            $query->where('usuario_id', '=', $filtros['usuario_id']);
        }

        // Ordenar por más reciente
        $query->orderBy('fecha_creacion', 'DESC');

        // Calcular offset
        $offset = ($pagina - 1) * $porPagina;
        
        return $query->limit($porPagina, $offset)->get();
    }

    /**
     * Marcar suscripciones expiradas
     */
    public static function marcarExpiradas()
    {
        $fechaActual = date('Y-m-d');
        
        return static::where('fecha_vencimiento', '<', $fechaActual)
                     ->where('estado', '=', self::ESTADO_ACTIVA)
                     ->update(['estado' => self::ESTADO_EXPIRADA]);
    }
}