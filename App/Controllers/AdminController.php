<?php

namespace App\Controllers;

use App\Services\AdminService;
use Core\Controller;
use Exception;

class AdminController extends Controller
{
    private $adminService;

    public function __construct()
    {
        parent::__construct();
        $this->adminService = new AdminService();
    }

    public function dashboard()
    {
        try {
            $data = $this->adminService->showDashboard();
            return view('admin.dashboard', $data);
        } catch (Exception $e) {
            return view('errors.500', ['message' => $e->getMessage()]);
        }
    }

    public function ajaxStats()
    {
        try {
            header('Content-Type: application/json');
            
            $type = $_GET['tipo'] ?? 'general';
            $data = $this->adminService->getStatsForAjax($type);
            
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function refreshMetrics()
    {
        try {
            header('Content-Type: application/json');
            
            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }
            
            $stats = $this->adminService->showDashboard();
            
            echo json_encode([
                'success' => true,
                'estadisticas' => $stats['estadisticas'],
                'resumen_sistema' => $stats['resumen_sistema']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
}
