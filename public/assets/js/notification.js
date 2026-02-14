/**
 * Modern Toast Notification System for SIMAKS
 * Provides consistent, beautiful notifications across all modules
 * 
 * @author SIMAKS Development Team
 * @version 1.0.0
 */

class NotificationManager {
    constructor() {
        // No complex init needed for Swal
    }

    /**
     * Show SweetAlert2 
     * @param {string} type - 'success', 'error', 'warning', 'info'
     * @param {string} title 
     * @param {string} message 
     * @param {object} options - Custom Swal options
     */
    show(type, title, message, options = {}) {
        const isToast = options.toast !== undefined ? options.toast : true;

        return Swal.fire({
            icon: type,
            title: title,
            text: message,
            toast: isToast,
            position: isToast ? 'top-end' : 'center',
            showConfirmButton: !isToast,
            timer: isToast ? (options.duration || 3000) : null,
            timerProgressBar: isToast,
            confirmButtonColor: '#6366f1',
            ...options
        });
    }

    // Standard SIMAKS Methods
    success(title, message, options = {}) {
        return this.show('success', title, message, options);
    }

    error(title, message, options = {}) {
        // For errors, we might want a non-toast by default for visibility
        const ops = { toast: false, ...options };
        return this.show('error', title, message, ops);
    }

    warning(title, message, options = {}) {
        return this.show('warning', title, message, options);
    }

    info(title, message, options = {}) {
        return this.show('info', title, message, options);
    }

    // Utility for confirmation dialogs
    confirm(title, text, confirmText = 'Ya, Lanjutkan') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        });
    }
}

// Create global instance
window.Notify = new NotificationManager();

// Auto-initialize and handle PHP session messages
// This runs AFTER Notify is created, so it's safe to use
document.addEventListener('DOMContentLoaded', function () {
    // Check if there are PHP session messages to display
    if (typeof window.phpSessionMessages !== 'undefined') {
        const messages = window.phpSessionMessages;

        if (messages.success) {
            window.Notify.success('Berhasil!', messages.success);
        }
        if (messages.error) {
            window.Notify.error('Gagal!', messages.error);
        }
        if (messages.warning) {
            window.Notify.warning('Peringatan!', messages.warning);
        }
        if (messages.info) {
            window.Notify.info('Informasi', messages.info);
        }
    }
});
