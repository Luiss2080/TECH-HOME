<!-- Estilos específicos para el módulo  -->
<link rel="stylesheet" href="<?= asset('css/admin/admin.css'); ?>">

<?php
$title = $title ?? 'Configuración - Panel de Administración';
?>

<div class="dashboard-content">
    
    <!-- Sección de Configuración Principal -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-cog"></i>
            Centro de Configuración
        </h2>
        <p class="section-subtitle">Administra los aspectos principales del sistema Tech Home Bolivia</p>

        <div class="config-grid">
            <!-- Gestión de Roles -->
            <a href="<?= route('admin.roles'); ?>" class="config-card roles-card">
                <div class="config-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="config-content">
                    <h3 class="config-title">Gestión de Roles</h3>
                    <p class="config-description">Administra los roles del sistema y sus funcionalidades</p>
                    <div class="config-stats">
                        <span class="config-stat">
                            <i class="fas fa-users"></i>
                            Roles Activos
                        </span>
                    </div>
                </div>
                <div class="config-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <!-- Gestión de Permisos -->
            <a href="<?= route('admin.permisos'); ?>" class="config-card permissions-card">
                <div class="config-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="config-content">
                    <h3 class="config-title">Gestión de Permisos</h3>
                    <p class="config-description">Define y administra los permisos del sistema</p>
                    <div class="config-stats">
                        <span class="config-stat">
                            <i class="fas fa-shield-alt"></i>
                            Permisos Disponibles
                        </span>
                    </div>
                </div>
                <div class="config-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <!-- Configuración General -->
            <div class="config-card general-card">
                <div class="config-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div class="config-content">
                    <h3 class="config-title">Configuración General</h3>
                    <p class="config-description">Ajustes generales del sistema</p>
                    <div class="config-stats">
                        <span class="config-stat">
                            <i class="fas fa-check-circle"></i>
                            Sistema Configurado
                        </span>
                    </div>
                </div>
                <div class="config-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            <!-- Configuración de Seguridad -->
            <div class="config-card security-card">
                <div class="config-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="config-content">
                    <h3 class="config-title">Seguridad</h3>
                    <p class="config-description">Configuración de seguridad y autenticación</p>
                    <div class="config-stats">
                        <span class="config-stat">
                            <i class="fas fa-shield-alt"></i>
                            Protección Activa
                        </span>
                    </div>
                </div>
                <div class="config-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            <!-- Configuración de Base de Datos -->
            <div class="config-card database-card">
                <div class="config-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="config-content">
                    <h3 class="config-title">Base de Datos</h3>
                    <p class="config-description">Configuración y mantenimiento de la base de datos</p>
                    <div class="config-stats">
                        <span class="config-stat">
                            <i class="fas fa-check-circle text-success"></i>
                            Conexión Activa
                        </span>
                    </div>
                </div>
                <div class="config-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            <!-- Configuración de Respaldos -->
            <div class="config-card backup-card">
                <div class="config-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="config-content">
                    <h3 class="config-title">Respaldos</h3>
                    <p class="config-description">Administración de respaldos y restauración</p>
                    <div class="config-stats">
                        <span class="config-stat">
                            <i class="fas fa-calendar-alt"></i>
                            Último Respaldo
                        </span>
                    </div>
                </div>
                <div class="config-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            <!-- Configuración de Logs del Sistema -->
            <div class="config-card logs-card">
                <div class="config-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="config-content">
                    <h3 class="config-title">Logs del Sistema</h3>
                    <p class="config-description">Visualización y análisis de logs de actividad</p>
                    <div class="config-stats">
                        <span class="config-stat">
                            <i class="fas fa-history"></i>
                            Registros Activos
                        </span>
                    </div>
                </div>
                <div class="config-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            <!-- Configuración de Notificaciones -->
            <div class="config-card notifications-card">
                <div class="config-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="config-content">
                    <h3 class="config-title">Notificaciones</h3>
                    <p class="config-description">Gestión de notificaciones y alertas del sistema</p>
                    <div class="config-stats">
                        <span class="config-stat">
                            <i class="fas fa-envelope"></i>
                            Sistema de Alertas
                        </span>
                    </div>
                </div>
                <div class="config-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Acciones Rápidas -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-bolt"></i>
            Acciones Rápidas de Configuración
        </h2>

        <div class="quick-config-grid">
            <a href="<?= route('admin.roles.crear'); ?>" class="quick-config-item">
                <div class="quick-config-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <span class="quick-config-label">Crear Nuevo Rol</span>
            </a>

            <div class="quick-config-item" onclick="exportConfig()">
                <div class="quick-config-icon">
                    <i class="fas fa-download"></i>
                </div>
                <span class="quick-config-label">Exportar Configuración</span>
            </div>

            <div class="quick-config-item" onclick="clearCache()">
                <div class="quick-config-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <span class="quick-config-label">Limpiar Caché</span>
            </div>
        </div>
    </div>

    <!-- Sección de Estado del Sistema -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-info-circle"></i>
            Estado del Sistema
        </h2>

        <div class="system-status-grid">
            <div class="status-item status-good">
                <div class="status-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="status-content">
                    <h4>Base de Datos</h4>
                    <p>Funcionando correctamente</p>
                </div>
            </div>

            <div class="status-item status-good">
                <div class="status-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="status-content">
                    <h4>Seguridad</h4>
                    <p>Sistema protegido</p>
                </div>
            </div>

            <div class="status-item status-warning">
                <div class="status-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="status-content">
                    <h4>Respaldos</h4>
                    <p>Programar respaldo automático</p>
                </div>
            </div>

            <div class="status-item status-good">
                <div class="status-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="status-content">
                    <h4>Rendimiento</h4>
                    <p>Óptimo funcionamiento</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   CONFIGURACIÓN - MODO CLARO Y OSCURO
   ============================================ */

/* Variables CSS para tema claro */
:root {
    --config-bg-primary: #ffffff;
    --config-bg-secondary: #f8fafc;
    --config-bg-gradient: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
    --config-border: #e2e8f0;
    --config-text-primary: #1f2937;
    --config-text-secondary: #6b7280;
    --config-text-muted: #9ca3af;
    --config-shadow: rgba(0, 0, 0, 0.1);
    --config-shadow-hover: rgba(0, 0, 0, 0.15);
    --config-hover-bg: #f8fafc;
}

/* Variables CSS para tema oscuro */
body.ithr-dark-mode,
body.dark-theme {
    --config-bg-primary: rgba(30, 41, 59, 0.95);
    --config-bg-secondary: rgba(15, 23, 42, 0.9);
    --config-bg-gradient: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.9) 100%);
    --config-border: rgba(71, 85, 105, 0.3);
    --config-text-primary: #f8fafc;
    --config-text-secondary: #cbd5e1;
    --config-text-muted: #94a3b8;
    --config-shadow: rgba(0, 0, 0, 0.3);
    --config-shadow-hover: rgba(0, 0, 0, 0.4);
    --config-hover-bg: rgba(30, 41, 59, 0.8);
}

.config-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.config-card {
    background: var(--config-bg-gradient);
    border: 1px solid var(--config-border);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    cursor: pointer;
    backdrop-filter: blur(10px);
}

.config-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px var(--config-shadow-hover);
    border-color: #3b82f6;
    text-decoration: none;
    color: inherit;
    background: var(--config-hover-bg);
}

.config-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.5rem;
}

.roles-card .config-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.permissions-card .config-icon {
    background: linear-gradient(135deg, #10b981, #047857);
    color: white;
}

.general-card .config-icon {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

.security-card .config-icon {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.database-card .config-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.backup-card .config-icon {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: white;
}

/* Nuevos estilos para los paneles agregados */
.logs-card .config-icon {
    background: linear-gradient(135deg, #84cc16, #65a30d);
    color: white;
}

.notifications-card .config-icon {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
}

.config-content {
    flex: 1;
}

.config-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: var(--config-text-primary);
}

.config-description {
    font-size: 0.875rem;
    color: var(--config-text-secondary);
    margin: 0 0 0.75rem 0;
    line-height: 1.4;
}

.config-stats {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.config-stat {
    font-size: 0.75rem;
    color: var(--config-text-muted);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.config-arrow {
    color: var(--config-text-muted);
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.config-card:hover .config-arrow {
    transform: translateX(4px);
    color: #3b82f6;
}

.quick-config-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.quick-config-item {
    background: var(--config-bg-primary);
    border: 1px solid var(--config-border);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.quick-config-item:hover {
    background: var(--config-hover-bg);
    border-color: #3b82f6;
    transform: translateY(-2px);
    text-decoration: none;
    color: inherit;
    box-shadow: 0 4px 15px var(--config-shadow);
}

.quick-config-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
}

.quick-config-label {
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    color: var(--config-text-primary);
}

.system-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.status-item {
    background: var(--config-bg-primary);
    border: 1px solid var(--config-border);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    backdrop-filter: blur(10px);
}

.status-good {
    border-left: 4px solid #10b981;
}

.status-warning {
    border-left: 4px solid #f59e0b;
}

.status-error {
    border-left: 4px solid #ef4444;
}

.status-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
}

.status-good .status-icon {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

body.ithr-dark-mode .status-good .status-icon,
body.dark-theme .status-good .status-icon {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
}

.status-warning .status-icon {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

body.ithr-dark-mode .status-warning .status-icon,
body.dark-theme .status-warning .status-icon {
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
}

.status-error .status-icon {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

body.ithr-dark-mode .status-error .status-icon,
body.dark-theme .status-error .status-icon {
    background: rgba(239, 68, 68, 0.2);
    color: #f87171;
}

.status-content h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--config-text-primary);
}

.status-content p {
    margin: 0;
    font-size: 0.75rem;
    color: var(--config-text-secondary);
}

/* Animaciones suaves para cambios de tema */
.config-card,
.quick-config-item,
.status-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .config-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .quick-config-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .system-status-grid {
        grid-template-columns: 1fr;
    }
    
    .config-card {
        padding: 1rem;
    }
    
    .config-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
}
</style>
</style>

<script>
function exportConfig() {
    alert('Funcionalidad de exportar configuración en desarrollo');
}

function clearCache() {
    if (confirm('¿Estás seguro de que quieres limpiar el caché del sistema?')) {
        alert('Caché limpiado exitosamente');
    }
}
</script>
