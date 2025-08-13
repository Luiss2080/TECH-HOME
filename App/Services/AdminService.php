<?php

namespace App\Services;

use App\Models\AdminModelo;
use Exception;

class AdminService
{
    private $modelo;
    private $usuario;

    public function __construct()
    {
        $this->modelo = new AdminModelo();
        $this->verificarPermisos();
        $this->obtenerDatosUsuario();
    }

    /**
     * Función principal para mostrar el dashboard
     */
    public function mostrarDashboard()
    {
        try {
            // Obtener todos los datos necesarios del modelo
            $datos = $this->prepararDatosDashboard();

            // Log de acceso exitoso
            $this->logAccesoAdmin();

            // Incluir la vista con los datos
            $this->cargarVista($datos);
        } catch (Exception $e) {
            error_log("Error en AdminControlador::mostrarDashboard: " . $e->getMessage());
            $this->manejarError("Error al cargar el dashboard: " . $e->getMessage());
        }
    }

    /**
     * Preparar todos los datos necesarios para el dashboard
     */
    public function prepararDatosDashboard()
    {
        try {
            // Obtener estadísticas generales
            $estadisticas = $this->modelo->obtenerEstadisticasGenerales();

            // Obtener actividades recientes
            $actividades_recientes = $this->modelo->obtenerActividadesRecientes(5);

            // Obtener sesiones activas
            $sesiones_activas = $this->modelo->obtenerSesionesActivas(5);

            // Obtener ventas recientes
            $ventas_recientes = $this->modelo->obtenerVentasRecientes(5);

            // Obtener libros recientes (últimos 5)
            $libros_recientes = $this->modelo->obtenerLibrosRecientes(5);

            // Obtener componentes recientes (últimos 5)
            $componentes_recientes = $this->modelo->obtenerComponentesRecientes(5);

            // Obtener datos para resumen del sistema
            $resumen_sistema = $this->modelo->obtenerResumenSistema();

            // Preparar datos del usuario
            $usuario_datos = [
                'nombre' => $this->usuario['nombre'],
                'apellido' => $this->usuario['apellido'],
                'email' => $this->usuario['email'],
                'rol' => $this->usuario['rol']
            ];

            return [
                'estadisticas' => $estadisticas,
                'actividades_recientes' => $actividades_recientes,
                'sesiones_activas' => $sesiones_activas,
                'ventas_recientes' => $ventas_recientes,
                'libros_recientes' => $libros_recientes,
                'componentes_recientes' => $componentes_recientes,
                'resumen_sistema' => $resumen_sistema,
                'usuario' => $usuario_datos
            ];
        } catch (Exception $e) {
            error_log("Error al preparar datos del dashboard: " . $e->getMessage());
            throw new Exception("No se pudieron cargar los datos del dashboard");
        }
    }

    /**
     * Iniciar sesión de forma segura
     */
    private function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Verificar que el usuario esté autenticado
     */
    private function verificarAutenticacion()
    {
        $user = auth();
        if (!$user) {
            return redirect(route('login'));
        }
        return true;
    }

    /**
     * Verificar permisos de administrador
     */
    private function verificarPermisos()
    {
        $user = auth();
        if (!$user) {
            return redirect(route('login'));
        }
        $rol = $user->rol;
        die(var_dump($rol));


        if ($rol !== 'administrador') {
            $this->logDebug("Usuario sin permisos de administrador, redirigiendo según rol");
            $this->redirigirSegunRol($rol);
        }
    }

    /**
     * Redireccionar según el rol del usuario
     */
    private function redirigirSegunRol($rol)
    {
        switch ($rol) {
            case 'docente':
                header("Location: docente.php");
                break;
            case 'estudiante':
                header("Location: estudiante.php");
                break;
            case 'vendedor':
                header("Location: vendedor.php");
                break;
            case 'invitado':
                header("Location: invitado.php");
                break;
            default:
                header("Location: estudiante.php");
                break;
        }
        exit();
    }

    /**
     * Obtener datos completos del usuario autenticado
     */
    private function obtenerDatosUsuario()
    {
        $this->usuario = [
            'id' => $_SESSION['usuario_id'] ?? '',
            'nombre' => $_SESSION['usuario_nombre'] ?? '',
            'apellido' => $_SESSION['usuario_apellido'] ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'rol' => $_SESSION['usuario_rol'] ?? ''
        ];

        $this->logDebug("Usuario admin cargado: " . $this->usuario['nombre']);
    }

    /**
     * Cargar la vista del dashboard con los datos
     */
    private function cargarVista($datos)
    {
        // Extraer variables para usar en la vista
        $estadisticas = $datos['estadisticas'];
        $actividades_recientes = $datos['actividades_recientes'];
        $sesiones_activas = $datos['sesiones_activas'];
        $ventas_recientes = $datos['ventas_recientes'];
        $libros_recientes = $datos['libros_recientes'];
        $componentes_recientes = $datos['componentes_recientes'];
        $resumen_sistema = $datos['resumen_sistema'];
        $usuario = $datos['usuario'];

        // Incluir la vista
        include __DIR__ . '/../vistas/admin_dashboard_view.php';
    }

    /**
     * Registrar acceso administrativo para auditoría
     */
    private function logAccesoAdmin()
    {
        $this->logDebug("Dashboard admin accedido");
        $this->logDebug("Session ID: " . session_id());
        $this->logDebug("Usuario en sesión: " . $this->usuario['id']);

        // Aquí se podría implementar un log más detallado en base de datos
        // para auditoría de accesos administrativos
    }

    /**
     * Manejar errores del dashboard
     */
    private function manejarError($mensaje)
    {
        // Log del error
        error_log("[ADMIN DASHBOARD ERROR] " . $mensaje);

        // Mostrar página de error o redireccionar
        $_SESSION['error_mensaje'] = $mensaje;
        header("Location: ../../error.php");
        exit();
    }

    /**
     * Función de debug
     */
    private function logDebug($mensaje)
    {
        error_log("[ADMIN DASHBOARD] " . $mensaje);
    }

    /**
     * Método para obtener estadísticas específicas (AJAX)
     */
    public function obtenerEstadisticasAjax()
    {
        try {
            header('Content-Type: application/json');

            $tipo = $_GET['tipo'] ?? 'general';

            switch ($tipo) {
                case 'general':
                    $datos = $this->modelo->obtenerEstadisticasGenerales();
                    break;
                case 'ventas':
                    $datos = $this->modelo->obtenerVentasRecientes(10);
                    break;
                case 'actividades':
                    $datos = $this->modelo->obtenerActividadesRecientes(10);
                    break;
                case 'sesiones':
                    $datos = $this->modelo->obtenerSesionesActivas(10);
                    break;
                case 'libros_recientes':
                    $datos = $this->modelo->obtenerLibrosRecientes(5);
                    break;
                case 'componentes_recientes':
                    $datos = $this->modelo->obtenerComponentesRecientes(5);
                    break;
                default:
                    throw new Exception("Tipo de estadística no válido");
            }

            echo json_encode([
                'success' => true,
                'data' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit();
    }

    /**
     * Método para actualizar métricas en tiempo real (AJAX)
     */
    public function actualizarMetricas()
    {
        try {
            header('Content-Type: application/json');

            // Verificar que sea una petición AJAX
            if (
                !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
            ) {
                throw new Exception("Acceso no autorizado");
            }

            // Obtener estadísticas actualizadas
            $estadisticas = $this->modelo->obtenerEstadisticasGenerales();
            $resumen = $this->modelo->obtenerResumenSistema();
            $libros_recientes = $this->modelo->obtenerLibrosRecientes(5);
            $componentes_recientes = $this->modelo->obtenerComponentesRecientes(5);

            echo json_encode([
                'success' => true,
                'estadisticas' => $estadisticas,
                'resumen' => $resumen,
                'libros_recientes' => $libros_recientes,
                'componentes_recientes' => $componentes_recientes,
                'timestamp' => time()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit();
    }

    /**
     * Validar datos de entrada
     */
    private function validarDatos($datos, $reglas)
    {
        $errores = [];

        foreach ($reglas as $campo => $regla) {
            if ($regla['requerido'] && empty($datos[$campo])) {
                $errores[] = "El campo {$campo} es requerido";
            }

            if (!empty($datos[$campo]) && isset($regla['tipo'])) {
                switch ($regla['tipo']) {
                    case 'email':
                        if (!filter_var($datos[$campo], FILTER_VALIDATE_EMAIL)) {
                            $errores[] = "El campo {$campo} debe ser un email válido";
                        }
                        break;
                    case 'numero':
                        if (!is_numeric($datos[$campo])) {
                            $errores[] = "El campo {$campo} debe ser un número";
                        }
                        break;
                }
            }
        }

        return $errores;
    }

    /**
     * Formatear números para visualización
     */
    public static function formatearNumero($numero, $decimales = 2)
    {
        return number_format($numero, $decimales, '.', ',');
    }

    /**
     * Formatear moneda boliviana
     */
    public static function formatearMoneda($monto)
    {
        return 'Bs. ' . number_format($monto, 2, '.', ',');
    }

    /**
     * Obtener clase CSS según el estado
     */
    public static function obtenerClaseEstado($estado)
    {
        $clases = [
            'Disponible' => 'disponible',
            'Stock bajo' => 'stock-bajo',
            'Sin Stock' => 'sin-stock',
            'Completada' => 'completada',
            'Procesando' => 'procesando',
            'Enviado' => 'enviado',
            'Cancelada' => 'cancelada'
        ];

        return $clases[$estado] ?? 'default';
    }

    /**
     * Destructor para limpiar recursos
     */
    public function __destruct()
    {
        $this->modelo = null;
    }
}

// Manejo de peticiones AJAX
if (isset($_GET['action']) && $_GET['action'] === 'ajax') {
    $controlador = new AdminService();

    switch ($_GET['method'] ?? '') {
        case 'estadisticas':
            $controlador->obtenerEstadisticasAjax();
            break;
        case 'actualizar':
            $controlador->actualizarMetricas();
            break;
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Método no encontrado']);
            exit();
    }
}
