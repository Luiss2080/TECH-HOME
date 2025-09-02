<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use App\Models\SecurityLog;
use App\Models\AuditLog;
use App\Middleware\RateLimitMiddleware;

class SecurityController extends Controller
{
    /**
     * Dashboard de seguridad
     */
    public function dashboard()
    {
        // Obtener estadísticas generales
        $stats = $this->getSecurityStats();
        
        // Obtener eventos recientes
        $recentEvents = $this->getRecentSecurityEvents();
        
        // Obtener IPs sospechosas
        $suspiciousIPs = SecurityLog::getSuspiciousIPs(5, 1);
        
        // Obtener estadísticas de rate limiting
        $rateLimitStats = RateLimitMiddleware::getStats(24);
        
        return view('admin.security.dashboard', [
            'title' => 'Dashboard de Seguridad',
            'stats' => $stats,
            'recentEvents' => $recentEvents,
            'suspiciousIPs' => $suspiciousIPs,
            'rateLimitStats' => $rateLimitStats
        ]);
    }

    /**
     * Lista de logs de seguridad
     */
    public function securityLogs(Request $request)
    {
        $page = (int)($request->input('page') ?? 1);
        $perPage = 50;
        $eventType = $request->input('event_type');
        $severity = $request->input('severity');
        
        $logs = $this->getFilteredSecurityLogs($page, $perPage, $eventType, $severity);
        
        return view('admin.security.logs', [
            'title' => 'Logs de Seguridad',
            'logs' => $logs,
            'currentPage' => $page,
            'filters' => [
                'event_type' => $eventType,
                'severity' => $severity
            ]
        ]);
    }

    /**
     * Lista de logs de auditoría
     */
    public function auditLogs(Request $request)
    {
        $page = (int)($request->input('page') ?? 1);
        $perPage = 50;
        $table = $request->input('table');
        $action = $request->input('action');
        
        $logs = $this->getFilteredAuditLogs($page, $perPage, $table, $action);
        
        return view('admin.security.audit', [
            'title' => 'Logs de Auditoría',
            'logs' => $logs,
            'currentPage' => $page,
            'filters' => [
                'table' => $table,
                'action' => $action
            ]
        ]);
    }

    /**
     * Análisis de IPs sospechosas
     */
    public function suspiciousIPs()
    {
        $ips = SecurityLog::getSuspiciousIPs(3, 7); // Mínimo 3 eventos en 7 días
        
        return view('admin.security.suspicious-ips', [
            'title' => 'IPs Sospechosas',
            'ips' => $ips
        ]);
    }

    /**
     * Exportar logs de seguridad
     */
    public function exportSecurityLogs(Request $request)
    {
        $days = (int)($request->input('days') ?? 7);
        $format = $request->input('format', 'csv');
        
        $logs = $this->getSecurityLogsForExport($days);
        
        if ($format === 'csv') {
            return $this->exportToCSV($logs, 'security_logs_' . date('Y-m-d'));
        } else {
            return $this->exportToJSON($logs, 'security_logs_' . date('Y-m-d'));
        }
    }

    /**
     * API para obtener estadísticas en tiempo real
     */
    public function apiStats()
    {
        $stats = $this->getSecurityStats();
        $recentEvents = $this->getRecentSecurityEvents(10);
        
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_events' => $recentEvents,
                'timestamp' => time()
            ]
        ]);
    }

    // Métodos privados de apoyo

    private function getSecurityStats()
    {
        try {
            $db = \Core\DB::getInstance();
            
            // Estadísticas de los últimos 7 días
            $since = date('Y-m-d H:i:s', strtotime('-7 days'));
            
            $stats = [];
            
            // Total de eventos
            $result = $db->query("SELECT COUNT(*) as total FROM security_log WHERE created_at >= ?", [$since]);
            $stats['total_events'] = $result->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Eventos por severidad
            $result = $db->query("
                SELECT severity, COUNT(*) as count 
                FROM security_log 
                WHERE created_at >= ? 
                GROUP BY severity", [$since]);
            $stats['by_severity'] = $result->fetchAll(\PDO::FETCH_ASSOC);
            
            // Eventos por tipo
            $result = $db->query("
                SELECT event_type, COUNT(*) as count 
                FROM security_log 
                WHERE created_at >= ? 
                GROUP BY event_type 
                ORDER BY count DESC", [$since]);
            $stats['by_type'] = $result->fetchAll(\PDO::FETCH_ASSOC);
            
            // IPs únicas
            $result = $db->query("SELECT COUNT(DISTINCT ip_address) as unique_ips FROM security_log WHERE created_at >= ?", [$since]);
            $stats['unique_ips'] = $result->fetch(\PDO::FETCH_ASSOC)['unique_ips'];
            
            return $stats;
        } catch (\Exception $e) {
            error_log("Error obteniendo stats de seguridad: " . $e->getMessage());
            return [];
        }
    }

    private function getRecentSecurityEvents($limit = 20)
    {
        try {
            $db = \Core\DB::getInstance();
            
            $query = "
                SELECT s.*, u.nombre, u.apellido
                FROM security_log s
                LEFT JOIN users u ON s.user_id = u.id
                ORDER BY s.created_at DESC
                LIMIT ?";
            
            $result = $db->query($query, [$limit]);
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error obteniendo eventos recientes: " . $e->getMessage());
            return [];
        }
    }

    private function getFilteredSecurityLogs($page, $perPage, $eventType, $severity)
    {
        try {
            $db = \Core\DB::getInstance();
            $offset = ($page - 1) * $perPage;
            
            $where = "WHERE 1=1";
            $params = [];
            
            if ($eventType) {
                $where .= " AND event_type = ?";
                $params[] = $eventType;
            }
            
            if ($severity) {
                $where .= " AND severity = ?";
                $params[] = $severity;
            }
            
            $params[] = $perPage;
            $params[] = $offset;
            
            $query = "
                SELECT s.*, u.nombre, u.apellido
                FROM security_log s
                LEFT JOIN users u ON s.user_id = u.id
                $where
                ORDER BY s.created_at DESC
                LIMIT ? OFFSET ?";
            
            $result = $db->query($query, $params);
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error obteniendo logs filtrados: " . $e->getMessage());
            return [];
        }
    }

    private function getFilteredAuditLogs($page, $perPage, $table, $action)
    {
        try {
            $db = \Core\DB::getInstance();
            $offset = ($page - 1) * $perPage;
            
            $where = "WHERE 1=1";
            $params = [];
            
            if ($table) {
                $where .= " AND table_name = ?";
                $params[] = $table;
            }
            
            if ($action) {
                $where .= " AND action = ?";
                $params[] = $action;
            }
            
            $params[] = $perPage;
            $params[] = $offset;
            
            $query = "
                SELECT a.*, u.nombre, u.apellido
                FROM audit_log a
                LEFT JOIN users u ON a.user_id = u.id
                $where
                ORDER BY a.created_at DESC
                LIMIT ? OFFSET ?";
            
            $result = $db->query($query, $params);
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error obteniendo audit logs: " . $e->getMessage());
            return [];
        }
    }

    private function getSecurityLogsForExport($days)
    {
        try {
            $db = \Core\DB::getInstance();
            $since = date('Y-m-d H:i:s', strtotime("-$days days"));
            
            $query = "
                SELECT s.*, u.nombre, u.apellido, u.email as user_email
                FROM security_log s
                LEFT JOIN users u ON s.user_id = u.id
                WHERE s.created_at >= ?
                ORDER BY s.created_at DESC";
            
            $result = $db->query($query, [$since]);
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error obteniendo logs para exportar: " . $e->getMessage());
            return [];
        }
    }

    private function exportToCSV($data, $filename)
    {
        $output = fopen('php://temp', 'w');
        
        // Headers CSV
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
        }
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        return $csv;
    }

    private function exportToJSON($data, $filename)
    {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
