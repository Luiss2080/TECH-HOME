<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use App\Services\LaboratorioService;
use App\Models\Laboratorio;
use App\Models\User;
use Exception;

class LaboratorioController extends Controller
{
    private $laboratorioService;

    public function __construct(?LaboratorioService $laboratorioService = null)
    {
        $this->laboratorioService = $laboratorioService ?? new LaboratorioService();
    }

    /**
     * Mostrar lista de laboratorios
     */
    public function index()
    {
        try {
            $laboratorios = $this->laboratorioService->getAllLaboratorios();
            $categorias = $this->laboratorioService->getAllCategories();
            $docentes = $this->laboratorioService->getAllDocentes();

            // Obtener estadísticas generales
            $estadisticas = $this->laboratorioService->getGeneralStats();

            $data = [
                'title' => 'Laboratorios',
                'laboratorios' => $laboratorios,
                'categorias' => $categorias,
                'docentes' => $docentes,
                'estadisticas' => $estadisticas,
                'success' => session('success'),
                'error' => session('error'),
                'info' => session('info'),
                'warning' => session('warning')
            ];

            return view('admin.laboratorios.index', $data);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar los laboratorios: ' . $e->getMessage());
            return view('admin.laboratorios.index', [
                'title' => 'Laboratorios',
                'laboratorios' => [],
                'categorias' => [],
                'docentes' => [],
                'estadisticas' => [],
                'error' => session('error')
            ]);
        }
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        try {
            $categorias = $this->laboratorioService->getAllCategories();
            $docentes = $this->laboratorioService->getAllDocentes();
            $componentes = $this->laboratorioService->getAllComponentes();

            $data = [
                'title' => 'Crear Laboratorio',
                'categorias' => $categorias,
                'docentes' => $docentes,
                'componentes' => $componentes,
                'laboratorio' => null // Para el formulario
            ];

            return view('admin.laboratorios.create', $data);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar el formulario: ' . $e->getMessage());
            return redirect('/admin/laboratorios');
        }
    }

    /**
     * Procesar creación de laboratorio
     */
    public function store()
    {
        try {
            $data = $this->validateLaboratorioData();

            // Procesar campos especiales
            $data = $this->processFormData($data);

            $laboratorioId = $this->laboratorioService->createLaboratorio($data);

            Session::flash('success', 'Laboratorio creado exitosamente.');
            return redirect('/admin/laboratorios/' . $laboratorioId);
        } catch (Exception $e) {
            Session::flash('error', 'Error al crear el laboratorio: ' . $e->getMessage());
            Session::flash('old', $_POST);
            return redirect('/admin/laboratorios/create');
        }
    }

    /**
     * Mostrar detalle de laboratorio
     */
    public function show($id)
    {
        try {
            $laboratorio = $this->laboratorioService->getLaboratorioById($id);

            if (!$laboratorio) {
                Session::flash('error', 'Laboratorio no encontrado.');
                return redirect('/admin/laboratorios');
            }

            // Obtener información adicional
            $docente = $laboratorio->docenteResponsable();
            $categoria = $laboratorio->categoria();
            $participantesData = [];
            
            // Obtener datos de participantes
            foreach ($laboratorio->getParticipantes() as $participanteId) {
                $user = User::find($participanteId);
                if ($user) {
                    $participantesData[] = $user;
                }
            }

            $data = [
                'title' => 'Detalle del Laboratorio',
                'laboratorio' => $laboratorio,
                'docente' => $docente,
                'categoria' => $categoria,
                'participantes' => $participantesData,
                'estadisticas' => $laboratorio->getEstadisticas(),
                'success' => session('success'),
                'error' => session('error'),
                'info' => session('info'),
                'warning' => session('warning')
            ];

            return view('admin.laboratorios.show', $data);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar el laboratorio: ' . $e->getMessage());
            return redirect('/admin/laboratorios');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        try {
            $laboratorio = $this->laboratorioService->getLaboratorioById($id);

            if (!$laboratorio) {
                Session::flash('error', 'Laboratorio no encontrado.');
                return redirect('/admin/laboratorios');
            }

            $categorias = $this->laboratorioService->getAllCategories();
            $docentes = $this->laboratorioService->getAllDocentes();
            $componentes = $this->laboratorioService->getAllComponentes();

            $data = [
                'title' => 'Editar Laboratorio',
                'laboratorio' => $laboratorio,
                'categorias' => $categorias,
                'docentes' => $docentes,
                'componentes' => $componentes
            ];

            return view('admin.laboratorios.edit', $data);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar el laboratorio: ' . $e->getMessage());
            return redirect('/admin/laboratorios');
        }
    }

    /**
     * Procesar actualización de laboratorio
     */
    public function update($id)
    {
        try {
            $data = $this->validateLaboratorioData(false);

            // Procesar campos especiales
            $data = $this->processFormData($data);

            $this->laboratorioService->updateLaboratorio($id, $data);

            Session::flash('success', 'Laboratorio actualizado exitosamente.');
            return redirect('/admin/laboratorios/' . $id);
        } catch (Exception $e) {
            Session::flash('error', 'Error al actualizar el laboratorio: ' . $e->getMessage());
            Session::flash('old', $_POST);
            return redirect('/admin/laboratorios/' . $id . '/edit');
        }
    }

    /**
     * Eliminar laboratorio
     */
    public function destroy($id)
    {
        try {
            $laboratorio = $this->laboratorioService->getLaboratorioById($id);
            if (!$laboratorio) {
                Session::flash('error', 'Laboratorio no encontrado.');
                return redirect('/admin/laboratorios');
            }

            $this->laboratorioService->deleteLaboratorio($id);
            Session::flash('success', 'Laboratorio eliminado exitosamente.');
        } catch (Exception $e) {
            Session::flash('error', 'Error al eliminar el laboratorio: ' . $e->getMessage());
        }

        return redirect('/admin/laboratorios');
    }

    /**
     * Buscar laboratorios (AJAX)
     */
    public function search()
    {
        try {
            $filters = [
                'buscar' => $_GET['buscar'] ?? '',
                'estado' => $_GET['estado'] ?? 'todos',
                'nivel' => $_GET['nivel'] ?? 'todos',
                'categoria' => $_GET['categoria'] ?? 'todas',
                'docente' => $_GET['docente'] ?? 'todos',
                'publico' => $_GET['publico'] ?? '',
                'destacado' => $_GET['destacado'] ?? '',
                'fecha_desde' => $_GET['fecha_desde'] ?? '',
                'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
                'orden' => $_GET['orden'] ?? 'fecha_desc'
            ];

            $laboratorios = $this->laboratorioService->searchLaboratorios($filters);

            // Procesar datos para la vista
            $laboratoriosData = [];
            foreach ($laboratorios as $laboratorio) {
                $laboratorioData = $laboratorio->getAttributes();
                
                // Agregar información del docente
                $docente = $laboratorio->docenteResponsable();
                $laboratorioData['docente_nombre'] = $docente ? 
                    $docente->nombre . ' ' . $docente->apellido : 'No asignado';

                // Agregar información de la categoría
                $categoria = $laboratorio->categoria();
                if ($categoria) {
                    $laboratorioData['categoria_nombre'] = $categoria->nombre;
                    $laboratorioData['categoria_color'] = $categoria->color;
                } else {
                    $laboratorioData['categoria_nombre'] = 'Sin categoría';
                    $laboratorioData['categoria_color'] = '#6c757d';
                }

                // Agregar información procesada
                $laboratorioData['progreso'] = $laboratorio->getProgreso();
                $laboratorioData['clase_estado'] = $laboratorio->getClaseEstado();
                $laboratorioData['total_participantes'] = count($laboratorio->getParticipantes());
                $laboratorioData['duracion_formateada'] = $laboratorio->getDuracionFormateada();

                $laboratoriosData[] = $laboratorioData;
            }

            Response::json([
                'success' => true,
                'data' => $laboratoriosData,
                'total' => count($laboratoriosData)
            ]);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Error al buscar laboratorios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del laboratorio (AJAX)
     */
    public function changeStatus($id)
    {
        try {
            $estado = $_POST['estado'] ?? '';

            if (empty($estado)) {
                throw new Exception('Estado requerido');
            }

            $this->laboratorioService->changeStatus($id, $estado);

            Response::json([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cambiar visibilidad pública (AJAX)
     */
    public function changePublicStatus($id)
    {
        try {
            $publico = (int)($_POST['publico'] ?? 0);

            $this->laboratorioService->changePublicStatus($id, $publico);

            Response::json([
                'success' => true,
                'message' => 'Visibilidad actualizada correctamente'
            ]);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Error al cambiar la visibilidad: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cambiar estado destacado (AJAX)
     */
    public function changeDestacadoStatus($id)
    {
        try {
            $destacado = (int)($_POST['destacado'] ?? 0);

            $this->laboratorioService->changeDestacadoStatus($id, $destacado);

            Response::json([
                'success' => true,
                'message' => 'Estado destacado actualizado correctamente'
            ]);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Error al cambiar el estado destacado: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Agregar participante (AJAX)
     */
    public function addParticipante($id)
    {
        try {
            $userId = (int)($_POST['user_id'] ?? 0);

            if (!$userId) {
                throw new Exception('ID de usuario requerido');
            }

            $result = $this->laboratorioService->addParticipante($id, $userId);

            if ($result) {
                Response::json([
                    'success' => true,
                    'message' => 'Participante agregado exitosamente'
                ]);
            } else {
                Response::json([
                    'success' => false,
                    'message' => 'El usuario ya es participante del laboratorio'
                ], 400);
            }
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Error al agregar participante: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remover participante (AJAX)
     */
    public function removeParticipante($id)
    {
        try {
            $userId = (int)($_POST['user_id'] ?? 0);

            if (!$userId) {
                throw new Exception('ID de usuario requerido');
            }

            $result = $this->laboratorioService->removeParticipante($id, $userId);

            if ($result) {
                Response::json([
                    'success' => true,
                    'message' => 'Participante removido exitosamente'
                ]);
            } else {
                Response::json([
                    'success' => false,
                    'message' => 'El usuario no era participante del laboratorio'
                ], 400);
            }
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Error al remover participante: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Duplicar laboratorio
     */
    public function duplicate($id)
    {
        try {
            $docenteId = (int)($_POST['docente_id'] ?? Session::get('user_id'));

            $nuevoLaboratorioId = $this->laboratorioService->duplicateLaboratorio($id, $docenteId);

            Session::flash('success', 'Laboratorio duplicado exitosamente.');
            return redirect('/admin/laboratorios/' . $nuevoLaboratorioId);
        } catch (Exception $e) {
            Session::flash('error', 'Error al duplicar el laboratorio: ' . $e->getMessage());
            return redirect('/admin/laboratorios/' . $id);
        }
    }

    /**
     * Exportar datos del laboratorio
     */
    public function export($id)
    {
        try {
            $data = $this->laboratorioService->exportLaboratorioData($id);

            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="laboratorio_' . $id . '_' . date('Y-m-d') . '.json"');
            
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            Session::flash('error', 'Error al exportar el laboratorio: ' . $e->getMessage());
            return redirect('/admin/laboratorios/' . $id);
        }
    }

    /**
     * Actualizar fechas del laboratorio (AJAX)
     */
    public function updateFechas($id)
    {
        try {
            $fechaInicio = $_POST['fecha_inicio'] ?? null;
            $fechaFin = $_POST['fecha_fin'] ?? null;

            $this->laboratorioService->updateFechas($id, $fechaInicio, $fechaFin);

            Response::json([
                'success' => true,
                'message' => 'Fechas actualizadas correctamente'
            ]);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Error al actualizar las fechas: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Dashboard de laboratorios para docentes
     */
    public function dashboard()
    {
        try {
            $docenteId = Session::get('user_id');
            $dashboardData = $this->laboratorioService->getDashboardDataForDocente($docenteId);

            $data = [
                'title' => 'Dashboard de Laboratorios',
                'estadisticas' => $dashboardData['estadisticas'],
                'laboratorios_activos' => $dashboardData['laboratorios_activos'],
                'proximos_a_vencer' => $dashboardData['proximos_a_vencer'],
                'recientes' => $dashboardData['recientes']
            ];

            return view('docente.laboratorios.dashboard', $data);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar el dashboard: ' . $e->getMessage());
            return redirect('/');
        }
    }

    /**
     * Página principal de laboratorios (método original)
     */
    public function laboratorios()
    {
        return view('admin.laboratorios.index', [
            'title' => 'Laboratorios Virtuales',
            'ruta' => '/laboratorios'
        ]);
    }

    /**
     * Validar datos del laboratorio
     */
    private function validateLaboratorioData($isCreate = true): array
    {
        $rules = [
            'nombre' => 'required|max:255',
            'descripcion' => 'required',
            'categoria_id' => 'required|numeric',
            'docente_responsable_id' => 'required|numeric',
            'nivel_dificultad' => 'required|in:Básico,Intermedio,Avanzado',
            'estado' => 'required|in:Planificado,En Progreso,Completado,Suspendido,Cancelado'
        ];

        $data = [];
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $_POST[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[] = "El campo {$field} es requerido";
                continue;
            }

            if (!empty($value)) {
                if (strpos($rule, 'max:') !== false) {
                    preg_match('/max:(\d+)/', $rule, $matches);
                    $max = (int)$matches[1];
                    if (strlen($value) > $max) {
                        $errors[] = "El campo {$field} no puede tener más de {$max} caracteres";
                        continue;
                    }
                }

                if (strpos($rule, 'numeric') !== false && !is_numeric($value)) {
                    $errors[] = "El campo {$field} debe ser numérico";
                    continue;
                }

                if (strpos($rule, 'in:') !== false) {
                    preg_match('/in:([^|]+)/', $rule, $matches);
                    $options = explode(',', $matches[1]);
                    if (!in_array($value, $options)) {
                        $errors[] = "El campo {$field} debe ser uno de: " . implode(', ', $options);
                        continue;
                    }
                }
            }

            $data[$field] = $value;
        }

        // Campos opcionales
        $optionalFields = [
            'objetivos', 'participantes', 'componentes_utilizados', 
            'tecnologias', 'resultado', 'conclusiones', 'duracion_dias',
            'fecha_inicio', 'fecha_fin', 'publico', 'destacado'
        ];

        foreach ($optionalFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field];
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        return $data;
    }

    /**
     * Procesar datos del formulario
     */
    private function processFormData(array $data): array
    {
        // Procesar participantes
        if (isset($data['participantes']) && !empty($data['participantes'])) {
            if (is_string($data['participantes'])) {
                $data['participantes'] = array_filter(explode(',', $data['participantes']), 'strlen');
                $data['participantes'] = array_map('intval', $data['participantes']);
            }
        } else {
            $data['participantes'] = [];
        }

        // Procesar componentes utilizados
        if (isset($data['componentes_utilizados']) && !empty($data['componentes_utilizados'])) {
            if (is_string($data['componentes_utilizados'])) {
                $data['componentes_utilizados'] = array_filter(explode(',', $data['componentes_utilizados']), 'strlen');
            }
        } else {
            $data['componentes_utilizados'] = [];
        }

        // Procesar tecnologías
        if (isset($data['tecnologias']) && !empty($data['tecnologias'])) {
            if (is_string($data['tecnologias'])) {
                $data['tecnologias'] = array_filter(explode(',', $data['tecnologias']), 'strlen');
            }
        } else {
            $data['tecnologias'] = [];
        }

        // Valores por defecto para checkboxes
        $data['publico'] = isset($data['publico']) ? (int)$data['publico'] : 0;
        $data['destacado'] = isset($data['destacado']) ? (int)$data['destacado'] : 0;

        // Conversión de tipos
        if (isset($data['categoria_id'])) {
            $data['categoria_id'] = (int)$data['categoria_id'];
        }

        if (isset($data['docente_responsable_id'])) {
            $data['docente_responsable_id'] = (int)$data['docente_responsable_id'];
        }

        if (isset($data['duracion_dias']) && !empty($data['duracion_dias'])) {
            $data['duracion_dias'] = (int)$data['duracion_dias'];
        }

        return $data;
    }
}
