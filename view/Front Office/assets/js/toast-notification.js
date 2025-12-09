/**
 * Système de Notifications Élégantes - ToastNotification
 * Remplace les alert() par de belles notifications
 */

class ToastNotification {
    constructor() {
        this.notifications = [];
    }

    /**
     * Affiche une notification
     * @param {string} type - 'success', 'error', 'info', 'warning'
     * @param {string} title - Titre de la notification
     * @param {string} message - Message de la notification
     * @param {number} duration - Durée en ms (0 = permanent)
     */
    show(type, title, message, duration = 4000) {
        const id = `toast-${Date.now()}`;
        
        // Créer l'élément de notification
        const notification = document.createElement('div');
        notification.id = id;
        notification.className = `toast-notification ${type}`;
        
        // Icônes par type
        const icons = {
            success: '✓',
            error: '✕',
            info: 'ℹ',
            warning: '⚠'
        };
        
        notification.innerHTML = `
            <div class="toast-icon">${icons[type]}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="toastManager.remove('${id}')">&times;</button>
        `;
        
        document.body.appendChild(notification);
        this.notifications.push(id);
        
        // Auto-supprimer après duration
        if (duration > 0) {
            setTimeout(() => this.remove(id), duration);
        }
        
        // Réajuster la position des notifications
        this.updatePositions();
    }

    /**
     * Supprime une notification
     */
    remove(id) {
        const notification = document.getElementById(id);
        if (notification) {
            notification.classList.add('removing');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
                this.notifications = this.notifications.filter(n => n !== id);
                this.updatePositions();
            }, 300);
        }
    }

    /**
     * Ajuste la position vertical des notifications
     */
    updatePositions() {
        const notifications = document.querySelectorAll('.toast-notification');
        notifications.forEach((notif, index) => {
            notif.style.top = (20 + index * 110) + 'px';
        });
    }

    // Méthodes raccourci
    success(title, message, duration = 4000) {
        this.show('success', title, message, duration);
    }

    error(title, message, duration = 5000) {
        this.show('error', title, message, duration);
    }

    info(title, message, duration = 3000) {
        this.show('info', title, message, duration);
    }

    warning(title, message, duration = 4000) {
        this.show('warning', title, message, duration);
    }
}

// Créer une instance globale
const toastManager = new ToastNotification();

/**
 * Remplacer les alertes standards
 */
window.showNotification = function(type, title, message, duration = 4000) {
    toastManager.show(type, title, message, duration);
};

// Compatibilité avec les anciens appels
window.showSuccess = function(title, message) {
    toastManager.success(title, message);
};

window.showError = function(title, message) {
    toastManager.error(title, message);
};

window.showInfo = function(title, message) {
    toastManager.info(title, message);
};

window.showWarning = function(title, message) {
    toastManager.warning(title, message);
};
