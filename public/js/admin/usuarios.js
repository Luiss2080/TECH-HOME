document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de gestión de usuarios cargado');
    
    // Inicializar componentes
    initializeUserModals();
    initializeAlerts();
    initializeTooltips();
    initializeTableFeatures();
    
    console.log('✅ Todos los componentes de usuarios inicializados');
});

/**
 * Inicializar modales de usuari/**
 * Utilidad para debug
 */
function debugUserModal(action, data) {
    if (typeof console !== 'undefined' && console.log) {
        console.log(`🐛 Debug Modal - ${action}:`, data);
    }
}

// ============================================
// FUNCIONES CRUD PERSONALIZADAS
// ============================================

/**
 * Cerrar modal CRUD personalizado
 */
function crudCloseModal(modalId) {
    console.log(`🔐 Cerrando modal CRUD: ${modalId}`);
    
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById('customModalOverlay');
    
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
    }
    
    if (overlay) {
        overlay.classList.remove('active');
        overlay.style.display = 'none';
    }
    
    // Remover clase del body si existe
    document.body.classList.remove('crud-modal-open');
}

/**
 * Abrir modal CRUD personalizado
 */
function crudOpenModal(modalId) {
    console.log(`🔓 Abriendo modal CRUD: ${modalId}`);
    
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById('customModalOverlay');
    
    if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
    }
    
    if (overlay) {
        overlay.style.display = 'block';
        overlay.classList.add('active');
    }
    
    // Agregar clase al body
    document.body.classList.add('crud-modal-open');
}

/**
 * Abrir modal de eliminación CRUD
 */
function crudOpenDeleteModal(userId, userName, deleteUrl) {
    console.log(`🗑️ Abriendo modal CRUD de eliminación para usuario: ${userName} (ID: ${userId})`);
    
    try {
        // Actualizar contenido del modal
        document.getElementById('userNameToDelete').textContent = userName;
        
        // Configurar formulario
        const form = document.getElementById('deleteUserForm');
        if (form) {
            form.action = deleteUrl;
            console.log(`📋 Formulario configurado para: ${form.action}`);
        } else {
            throw new Error('Formulario de eliminación no encontrado');
        }
        
        // Mostrar modal
        crudOpenModal('deleteUserModal');
        
    } catch (error) {
        console.error('❌ Error al abrir modal de eliminación:', error);
        showErrorMessage('Error al abrir el modal de eliminación');
    }
}

/**
 * Abrir modal de cambio de estado CRUD
 */
function crudOpenStatusModal(userId, userName, currentStatus, statusUrl) {
    console.log(`🔄 Abriendo modal CRUD de estado para usuario: ${userName} (Estado actual: ${currentStatus})`);
    
    try {
        const isActive = currentStatus == '1';
        const action = isActive ? 'desactivar' : 'activar';
        const newStatus = isActive ? '0' : '1';
        
        // Actualizar contenido del modal
        document.getElementById('userNameToToggle').textContent = userName;
        document.getElementById('statusModalTitle').textContent = 
            `¿${action.charAt(0).toUpperCase() + action.slice(1)} este usuario?`;
        document.getElementById('statusModalDescription').textContent = 
            `El usuario será ${action}do en el sistema.`;
        
        // Configurar botón
        const confirmButton = document.getElementById('confirmStatusToggle');
        if (confirmButton) {
            confirmButton.innerHTML = `
                <i class="fas fa-${isActive ? 'ban' : 'check'}"></i>
                ${action.charAt(0).toUpperCase() + action.slice(1)} Usuario
            `;
            confirmButton.className = `crud-btn crud-btn-outline-${isActive ? 'warning' : 'success'}`;
        }
        
        // Configurar formulario
        const form = document.getElementById('toggleStatusForm');
        if (form) {
            form.action = statusUrl;
            
            // Agregar campo hidden para el nuevo estado
            let statusInput = form.querySelector('input[name="estado"]');
            if (!statusInput) {
                statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'estado';
                form.appendChild(statusInput);
            }
            statusInput.value = newStatus;
            
            console.log(`📋 Formulario de estado configurado: ${form.action}, nuevo estado: ${newStatus}`);
        } else {
            throw new Error('Formulario de cambio de estado no encontrado');
        }
        
        // Mostrar modal
        crudOpenModal('toggleStatusModal');
        
    } catch (error) {
        console.error('❌ Error al abrir modal de estado:', error);
        showErrorMessage('Error al abrir el modal de cambio de estado');
    }
}

// Event listeners para el sistema CRUD
document.addEventListener('DOMContentLoaded', function() {
    // Overlay click para cerrar
    const overlay = document.getElementById('customModalOverlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            const activeModal = document.querySelector('.crud-modal.show');
            if (activeModal) {
                crudCloseModal(activeModal.id);
            }
        });
    }
    
    // ESC para cerrar modales
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.crud-modal.show');
            if (activeModal) {
                crudCloseModal(activeModal.id);
            }
        }
    });
});
function initializeUserModals() {
    console.log('🔧 Inicializando modales de usuarios...');
    
    // Referencias a modales
    const deleteModal = document.getElementById('deleteUserModal');
    const statusModal = document.getElementById('toggleStatusModal');
    const customOverlay = document.getElementById('customModalOverlay');
    
    if (!deleteModal || !statusModal) {
        console.error('❌ No se encontraron los modales de usuarios');
        return;
    }
    
    // Botones de eliminación
    const deleteButtons = document.querySelectorAll('.btn-delete-user');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🗑️ Clic en eliminar usuario');
            
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const deleteUrl = this.getAttribute('data-delete-url');
            
            if (!userId || !userName || !deleteUrl) {
                console.error('❌ Datos incompletos del usuario');
                showErrorMessage('Error: Datos del usuario incompletos');
                return;
            }
            
            openDeleteModal(userId, userName, deleteUrl);
        });
    });
    
    // Botones de cambio de estado
    const statusButtons = document.querySelectorAll('.btn-toggle-status');
    statusButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔄 Clic en cambiar estado de usuario');
            
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const currentStatus = this.getAttribute('data-current-status');
            const statusUrl = this.getAttribute('data-status-url');
            
            if (!userId || !userName || currentStatus === null || !statusUrl) {
                console.error('❌ Datos incompletos del usuario');
                showErrorMessage('Error: Datos del usuario incompletos');
                return;
            }
            
            openStatusModal(userId, userName, currentStatus, statusUrl);
        });
    });
    
    // Eventos de cierre de modales
    setupModalCloseEvents(deleteModal, statusModal, customOverlay);
    
    console.log('✅ Modales de usuarios configurados correctamente');
}

/**
 * Abrir modal de eliminación
 */
function openDeleteModal(userId, userName, deleteUrl) {
    console.log(`🗑️ Abriendo modal de eliminación para usuario: ${userName} (ID: ${userId})`);
    
    try {
        // Actualizar contenido del modal
        document.getElementById('userNameToDelete').textContent = userName;
        
        // Configurar formulario con la URL correcta que viene del PHP
        const form = document.getElementById('deleteUserForm');
        if (form) {
            form.action = deleteUrl;
            console.log(`📋 Formulario configurado para: ${form.action}`);
        } else {
            throw new Error('Formulario de eliminación no encontrado');
        }
        
        // Mostrar modal con overlay
        showModalWithOverlay('deleteUserModal');
        
    } catch (error) {
        console.error('❌ Error al abrir modal de eliminación:', error);
        showErrorMessage('Error al abrir el modal de eliminación');
    }
}

/**
 * Abrir modal de cambio de estado
 */
function openStatusModal(userId, userName, currentStatus, statusUrl) {
    console.log(`🔄 Abriendo modal de estado para usuario: ${userName} (Estado actual: ${currentStatus})`);
    
    try {
        const isActive = currentStatus == '1';
        const action = isActive ? 'desactivar' : 'activar';
        const newStatus = isActive ? '0' : '1';
        
        // Actualizar contenido del modal
        document.getElementById('userNameToToggle').textContent = userName;
        document.getElementById('statusModalTitle').textContent = 
            `¿${action.charAt(0).toUpperCase() + action.slice(1)} este usuario?`;
        document.getElementById('statusModalDescription').textContent = 
            `El usuario será ${action}do en el sistema.`;
        
        // Configurar botón
        const confirmButton = document.getElementById('confirmStatusToggle');
        if (confirmButton) {
            confirmButton.innerHTML = `
                <i class="fas fa-${isActive ? 'ban' : 'check'} me-1"></i>
                ${action.charAt(0).toUpperCase() + action.slice(1)} Usuario
            `;
            confirmButton.className = `btn ${isActive ? 'btn-warning' : 'btn-success'}`;
        }
        
        // Configurar formulario con la URL correcta que viene del PHP
        const form = document.getElementById('toggleStatusForm');
        if (form) {
            form.action = statusUrl;
            
            // Agregar campo hidden para el nuevo estado
            let statusInput = form.querySelector('input[name="estado"]');
            if (!statusInput) {
                statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'estado';
                form.appendChild(statusInput);
            }
            statusInput.value = newStatus;
            
            console.log(`📋 Formulario de estado configurado: ${form.action}, nuevo estado: ${newStatus}`);
        } else {
            throw new Error('Formulario de cambio de estado no encontrado');
        }
        
        // Mostrar modal con overlay
        showModalWithOverlay('toggleStatusModal');
        
    } catch (error) {
        console.error('❌ Error al abrir modal de estado:', error);
        showErrorMessage('Error al abrir el modal de cambio de estado');
    }
}

/**
 * Mostrar modal con overlay personalizado
 */
function showModalWithOverlay(modalId) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById('customModalOverlay');
    
    if (!modal || !overlay) {
        console.error('❌ Modal u overlay no encontrado');
        return;
    }
    
    try {
        // Mostrar overlay
        overlay.style.display = 'block';
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
        
        // Mostrar modal usando Bootstrap
        const bsModal = new bootstrap.Modal(modal, {
            backdrop: false, // No usar el backdrop de Bootstrap
            keyboard: true,
            focus: true
        });
        
        bsModal.show();
        
        // Ajustar z-index
        modal.style.zIndex = '1055';
        
        console.log(`✅ Modal ${modalId} mostrado correctamente`);
        
    } catch (error) {
        console.error('❌ Error al mostrar modal:', error);
        // Fallback: mostrar modal sin overlay
        try {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } catch (fallbackError) {
            console.error('❌ Error en fallback:', fallbackError);
        }
    }
}

/**
 * Configurar eventos de cierre de modales
 */
function setupModalCloseEvents(deleteModal, statusModal, overlay) {
    console.log('🔧 Configurando eventos de cierre...');
    
    // Función para cerrar modal
    function closeModal(modalElement) {
        if (!modalElement) return;
        
        try {
            const bsModal = bootstrap.Modal.getInstance(modalElement);
            if (bsModal) {
                bsModal.hide();
            }
        } catch (error) {
            console.error('❌ Error al cerrar modal:', error);
        }
        
        // Ocultar overlay
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300);
        }
    }
    
    // Eventos de clic en overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            console.log('🖱️ Clic en overlay - cerrando modal');
            const activeModal = document.querySelector('.modal.show');
            if (activeModal) {
                closeModal(activeModal);
            }
        });
    }
    
    // Eventos de botones cerrar
    [deleteModal, statusModal].forEach(modal => {
        if (!modal) return;
        
        const closeButtons = modal.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                console.log('🖱️ Clic en botón cerrar');
                closeModal(modal);
            });
        });
        
        // Evento de teclado ESC
        modal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                console.log('⌨️ Tecla ESC presionada');
                closeModal(modal);
            }
        });
        
        // Evento cuando el modal se oculta
        modal.addEventListener('hidden.bs.modal', function() {
            console.log('📴 Modal ocultado');
            if (overlay) {
                overlay.style.display = 'none';
                overlay.style.opacity = '0';
            }
        });
    });
}

/**
 * Inicializar alertas auto-dismiss
 */
function initializeAlerts() {
    console.log('🔧 Inicializando alertas...');
    
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        // Auto-dismiss después de 5 segundos
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });
    
    console.log(`✅ ${alerts.length} alertas configuradas`);
}

/**
 * Inicializar tooltips
 */
function initializeTooltips() {
    console.log('🔧 Inicializando tooltips...');
    
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(element => {
        new bootstrap.Tooltip(element, {
            placement: 'top',
            trigger: 'hover'
        });
    });
    
    console.log(`✅ ${tooltipElements.length} tooltips inicializados`);
}

/**
 * Inicializar características de tabla
 */
function initializeTableFeatures() {
    console.log('🔧 Inicializando características de tabla...');
    
    const table = document.querySelector('.data-table');
    if (!table) {
        console.log('ℹ️ No se encontró tabla de datos');
        return;
    }
    
    // Efectos hover en filas
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    console.log(`✅ Efectos de tabla aplicados a ${rows.length} filas`);
}

/**
 * Mostrar mensaje de error
 */
function showErrorMessage(message) {
    console.error('❌ Error:', message);
    
    // Crear alerta temporal
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible';
    alertDiv.innerHTML = `
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
    `;
    
    // Insertar en la página
    const content = document.querySelector('.dashboard-content');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);
        
        // Auto-dismiss
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 3000);
    }
}

/**
 * Mostrar mensaje de éxito
 */
function showSuccessMessage(message) {
    console.log('✅ Éxito:', message);
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible';
    alertDiv.innerHTML = `
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <i class="fas fa-check-circle me-2"></i>
        ${message}
    `;
    
    const content = document.querySelector('.dashboard-content');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);
        
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 3000);
    }
}

/**
 * Utilidad para debug
 */
function debugUserModal(action, data) {
    if (typeof console !== 'undefined' && console.log) {
        console.log(`🐛 DEBUG Usuario Modal - ${action}:`, data);
    }
}

// Exponer funciones globales para debug
window.debugUserModal = debugUserModal;
window.showSuccessMessage = showSuccessMessage;
window.showErrorMessage = showErrorMessage;



