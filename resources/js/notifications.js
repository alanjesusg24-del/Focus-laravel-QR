/**
 * ============================================
 * CETAM - Notifications Configuration
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        notifications.js
 * @description Configuración de SweetAlert2 y Notyf con colores institucionales
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 *
 * ============================================
 */

// Colores Institucionales CETAM
const CETAM_COLORS = {
    primary: '#1F2937',
    secondary: '#FB503B',
    tertiary: '#31316A',
    success: '#10B981',
    danger: '#EF4444',
    warning: '#FBA918',
    info: '#3B82F6',
    gray: '#6B7280'
};

/**
 * Configuración global de SweetAlert2
 */
if (typeof Swal !== 'undefined') {
    // Configurar valores por defecto
    Swal.mixin({
        confirmButtonColor: CETAM_COLORS.primary,
        cancelButtonColor: CETAM_COLORS.gray,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    });
}

/**
 * Helpers para SweetAlert2 con colores institucionales
 */
window.cetamAlert = {
    /**
     * Confirmación de eliminación
     */
    confirmDelete: function(title = '¿Eliminar registro?', text = 'Esta acción no se puede deshacer') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            iconColor: CETAM_COLORS.warning,
            showCancelButton: true,
            confirmButtonColor: CETAM_COLORS.danger,
            cancelButtonColor: CETAM_COLORS.gray,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });
    },

    /**
     * Mensaje de éxito
     */
    success: function(title, text = '') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'success',
            iconColor: CETAM_COLORS.success,
            confirmButtonColor: CETAM_COLORS.primary,
            confirmButtonText: 'Aceptar',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    },

    /**
     * Mensaje de error
     */
    error: function(title, text = '') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'error',
            iconColor: CETAM_COLORS.danger,
            confirmButtonColor: CETAM_COLORS.primary,
            confirmButtonText: 'Entendido',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    },

    /**
     * Mensaje de advertencia
     */
    warning: function(title, text = '') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            iconColor: CETAM_COLORS.warning,
            confirmButtonColor: CETAM_COLORS.primary,
            confirmButtonText: 'Aceptar',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    },

    /**
     * Mensaje informativo
     */
    info: function(title, text = '') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'info',
            iconColor: CETAM_COLORS.info,
            confirmButtonColor: CETAM_COLORS.primary,
            confirmButtonText: 'Aceptar',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    }
};

/**
 * Configuración de Notyf con colores institucionales
 *
 * NOTA: Las configuraciones de iconos han sido removidas temporalmente
 * debido a que no cumplen con los estándares CETAM que requieren
 * Font Awesome 6.4.0 con prefijo 'fa-solid'.
 * El proyecto actualmente usa Font Awesome 5.11.2.
 *
 * Para habilitar iconos, actualizar Font Awesome a 6.4.0 y usar:
 * className: 'fa-solid fa-circle-check' (en lugar de 'fas fa-check-circle')
 */
if (typeof Notyf !== 'undefined') {
    window.notyf = new Notyf({
        duration: 4000,
        position: {
            x: 'right',
            y: 'bottom',
        },
        dismissible: true,
        ripple: true,
        types: [
            {
                type: 'success',
                background: CETAM_COLORS.success,
            },
            {
                type: 'error',
                background: CETAM_COLORS.danger,
                duration: 5000,
            },
            {
                type: 'warning',
                background: CETAM_COLORS.warning,
                duration: 5000,
            },
            {
                type: 'info',
                background: CETAM_COLORS.primary,
                duration: 3000,
            }
        ]
    });
}

/**
 * Helper para mostrar notificaciones Notyf
 */
window.cetamNotify = {
    success: function(message) {
        if (window.notyf) {
            window.notyf.success(message);
        }
    },
    error: function(message) {
        if (window.notyf) {
            window.notyf.error(message);
        }
    },
    warning: function(message) {
        if (window.notyf) {
            window.notyf.open({
                type: 'warning',
                message: message
            });
        }
    },
    info: function(message) {
        if (window.notyf) {
            window.notyf.open({
                type: 'info',
                message: message
            });
        }
    }
};
