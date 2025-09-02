<?php
$user = auth();
if (!$user) {
    header('Location: ' . route('login'));
    exit;
}
?>

<link rel="stylesheet" href="<?= asset('css/admin/admin.css'); ?>">

<div class="dashboard-content">
    <!-- Header -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Gestión de Suscripciones
                </h2>
                <p class="section-subtitle">Administra las suscripciones de usuarios</p>
            </div>
            <a href="<?= route('admin.suscripciones.crear') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Suscripción
            </a>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= $estadisticas['por_estado']['activas'] ?? 0 ?></div>
                        <div class="stat-label">Suscripciones Activas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= $estadisticas['por_estado']['suspendidas'] ?? 0 ?></div>
                        <div class="stat-label">Suspendidas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">$<?= number_format($estadisticas['ingresos']['este_mes'] ?? 0, 2) ?></div>
                        <div class="stat-label">Ingresos este mes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= $estadisticas['proximas_vencer'] ?? 0 ?></div>
                        <div class="stat-label">Por vencer</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de distribución por planes -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Distribución por Plan</h6>
                    </div>
                    <div class="card-body">
                        <div class="plan-stats">
                            <?php
                            $planes = $estadisticas['por_plan'] ?? [];
                            $total = array_sum($planes);
                            ?>
                            <?php foreach ($planes as $plan => $cantidad): ?>
                                <?php if ($cantidad > 0): ?>
                                    <div class="plan-stat-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="plan-name"><?= ucfirst($plan) ?></span>
                                            <span class="plan-count"><?= $cantidad ?></span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-<?= $plan === 'basico' ? 'info' : ($plan === 'premium' ? 'warning' : 'success') ?>" 
                                                 style="width: <?= $total > 0 ? ($cantidad / $total * 100) : 0 ?>%"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Resumen de Ingresos</h6>
                    </div>
                    <div class="card-body">
                        <div class="ingreso-stats">
                            <div class="ingreso-item">
                                <span class="ingreso-label">Total Acumulado:</span>
                                <span class="ingreso-valor">$<?= number_format($estadisticas['ingresos']['total'] ?? 0, 2) ?></span>
                            </div>
                            <div class="ingreso-item">
                                <span class="ingreso-label">Este Mes:</span>
                                <span class="ingreso-valor">$<?= number_format($estadisticas['ingresos']['este_mes'] ?? 0, 2) ?></span>
                            </div>
                            <div class="ingreso-item">
                                <span class="ingreso-label">Promedio Mensual:</span>
                                <span class="ingreso-valor">$<?= number_format(($estadisticas['ingresos']['total'] ?? 0) / 12, 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section mb-4">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="activa">Activa</option>
                        <option value="suspendida">Suspendida</option>
                        <option value="cancelada">Cancelada</option>
                        <option value="expirada">Expirada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="filtroPlan">
                        <option value="">Todos los planes</option>
                        <option value="basico">Básico</option>
                        <option value="premium">Premium</option>
                        <option value="profesional">Profesional</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscarSuscripcion" placeholder="Buscar por usuario o email...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de suscripciones -->
        <div class="table-responsive">
            <table class="table table-hover" id="tablaSuscripciones">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Plan</th>
                        <th>Estado</th>
                        <th>Inicio</th>
                        <th>Vencimiento</th>
                        <th>Precio</th>
                        <th>Método Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suscripciones as $suscripcion): ?>
                        <?php $usuario = \App\Models\User::find($suscripcion->usuario_id); ?>
                        <tr data-estado="<?= htmlspecialchars($suscripcion->estado) ?>" 
                            data-plan="<?= htmlspecialchars($suscripcion->tipo_plan) ?>"
                            data-usuario="<?= $usuario ? htmlspecialchars(strtolower($usuario->nombre . ' ' . $usuario->apellido . ' ' . $usuario->email)) : '' ?>">
                            <td><?= $suscripcion->id ?></td>
                            <td>
                                <?php if ($usuario): ?>
                                    <div class="usuario-info">
                                        <strong><?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?></strong>
                                        <small class="text-muted d-block"><?= htmlspecialchars($usuario->email) ?></small>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Usuario no encontrado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $suscripcion->tipo_plan === 'basico' ? 'info' : ($suscripcion->tipo_plan === 'premium' ? 'warning' : 'success') ?>">
                                    <?= ucfirst(htmlspecialchars($suscripcion->tipo_plan)) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $suscripcion->estado === 'activa' ? 'success' : ($suscripcion->estado === 'suspendida' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst(htmlspecialchars($suscripcion->estado)) ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($suscripcion->fecha_inicio)) ?>
                                </small>
                            </td>
                            <td>
                                <small class="<?= strtotime($suscripcion->fecha_vencimiento) < time() ? 'text-danger' : (strtotime($suscripcion->fecha_vencimiento) < strtotime('+7 days') ? 'text-warning' : 'text-muted') ?>">
                                    <?= date('d/m/Y', strtotime($suscripcion->fecha_vencimiento)) ?>
                                </small>
                            </td>
                            <td>
                                <strong>$<?= number_format($suscripcion->precio, 2) ?></strong>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($suscripcion->metodo_pago) ?></small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <?php if ($suscripcion->estado === 'activa'): ?>
                                        <button class="btn btn-sm btn-outline-warning" 
                                                onclick="suspenderSuscripcion(<?= $suscripcion->id ?>)"
                                                title="Suspender">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="cancelarSuscripcion(<?= $suscripcion->id ?>)"
                                                title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php elseif ($suscripcion->estado === 'suspendida'): ?>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="reactivarSuscripcion(<?= $suscripcion->id ?>)"
                                                title="Reactivar">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="cancelarSuscripcion(<?= $suscripcion->id ?>)"
                                                title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="verDetalles(<?= $suscripcion->id ?>)"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($suscripciones)): ?>
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay suscripciones registradas</h5>
                <p class="text-muted">Comienza creando la primera suscripción</p>
                <a href="<?= route('admin.suscripciones.crear') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primera Suscripción
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtros
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroPlan = document.getElementById('filtroPlan');
    const buscarSuscripcion = document.getElementById('buscarSuscripcion');
    const tabla = document.getElementById('tablaSuscripciones');

    function aplicarFiltros() {
        const estadoSeleccionado = filtroEstado.value;
        const planSeleccionado = filtroPlan.value;
        const textoBusqueda = buscarSuscripcion.value.toLowerCase();
        
        const filas = tabla.querySelectorAll('tbody tr');
        
        filas.forEach(fila => {
            const estado = fila.dataset.estado;
            const plan = fila.dataset.plan;
            const usuario = fila.dataset.usuario;
            
            let mostrar = true;
            
            if (estadoSeleccionado && estado !== estadoSeleccionado) {
                mostrar = false;
            }
            
            if (planSeleccionado && plan !== planSeleccionado) {
                mostrar = false;
            }
            
            if (textoBusqueda && !usuario.includes(textoBusqueda)) {
                mostrar = false;
            }
            
            fila.style.display = mostrar ? '' : 'none';
        });
    }

    filtroEstado.addEventListener('change', aplicarFiltros);
    filtroPlan.addEventListener('change', aplicarFiltros);
    buscarSuscripcion.addEventListener('input', aplicarFiltros);
});

function cancelarSuscripcion(id) {
    if (confirm('¿Estás seguro de que quieres cancelar esta suscripción?')) {
        fetch(`<?= route('admin.suscripciones.cancelar', ['id' => '']) ?>${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Suscripción cancelada exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelando suscripción');
        });
    }
}

function suspenderSuscripcion(id) {
    const motivo = prompt('Motivo de la suspensión (opcional):');
    if (motivo !== null) {
        // Implementar lógica de suspensión
        alert('Funcionalidad de suspensión en desarrollo');
    }
}

function reactivarSuscripcion(id) {
    if (confirm('¿Deseas reactivar esta suscripción?')) {
        // Implementar lógica de reactivación
        alert('Funcionalidad de reactivación en desarrollo');
    }
}

function verDetalles(id) {
    // Implementar modal de detalles
    alert('Modal de detalles en desarrollo');
}
</script>

<style>
.plan-stats {
    space-y: 15px;
}

.plan-stat-item {
    margin-bottom: 15px;
}

.plan-name {
    font-weight: 500;
}

.plan-count {
    font-weight: 600;
    color: #495057;
}

.ingreso-stats {
    space-y: 10px;
}

.ingreso-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.ingreso-label {
    color: #6c757d;
}

.ingreso-valor {
    font-weight: 600;
    color: #28a745;
}

.usuario-info strong {
    display: block;
}

.usuario-info small {
    font-size: 0.8em;
}
</style>