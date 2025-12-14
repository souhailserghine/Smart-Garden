/* ==========================================
   SMARTGARDEN DASHBOARD JAVASCRIPT
   ========================================== */

// ========== SIDEBAR TOGGLE (pour les pages avec sidebar) ==========
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    }

    // Alert close buttons
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.opacity = '0';
            this.parentElement.style.transform = 'translateY(-20px)';
            setTimeout(() => this.parentElement.remove(), 300);
        });
    });

    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    });
});

// ========== CAPTEUR FORM VALIDATION ==========
if (document.getElementById('capteurForm')) {
    const categoryUnits = {
        "Température": "°C",
        "Humidité": "%",
        "Luminosité": "lux",
        "Pression": "hPa",
        "pH": "pH",
        "Débit": "L/min",
        "CO2": "ppm"
    };

    const form = document.getElementById('capteurForm');
    const fields = {
        categorie: document.getElementById('id_categorie'),
        unite: document.getElementById('unite'),
        etat: document.getElementById('etat'),
        emplacement: document.getElementById('emplacement'),
        date: document.getElementById('dateInstallation'),
        plante: document.getElementById('id_plante')
    };

    const progressSteps = document.querySelectorAll('.progress-step');

    // Date max = aujourd'hui
    const today = new Date().toISOString().split('T')[0];
    if (fields.date) {
        fields.date.setAttribute('max', today);
    }

    // Auto-remplir l'unité selon la catégorie
    if (fields.categorie) {
        fields.categorie.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex].text;
            fields.unite.value = categoryUnits[selectedText] || "";
            validate('categorie');
            updateProgress();
        });
    }

    function showError(field, message) {
        const errorSpan = document.getElementById('error_' + field);
        const successSpan = document.getElementById('success_' + field);
        
        if (errorSpan) {
            errorSpan.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
            errorSpan.style.display = 'flex';
            fields[field].classList.add('error');
            fields[field].classList.remove('success');
            if (successSpan) successSpan.style.display = 'none';
        }
    }

    function showSuccess(field) {
        const errorSpan = document.getElementById('error_' + field);
        const successSpan = document.getElementById('success_' + field);
        
        if (errorSpan) errorSpan.style.display = 'none';
        fields[field].classList.remove('error');
        fields[field].classList.add('success');
        
        if (successSpan && field === 'emplacement') {
            successSpan.innerHTML = '<i class="fas fa-check-circle"></i> Emplacement valide';
            successSpan.style.display = 'flex';
        }
    }

    function validate(field) {
        if (!fields[field]) return true;
        
        const value = fields[field].value.trim();
        
        switch(field) {
            case 'categorie':
            case 'etat':
            case 'plante':
                if (!value) {
                    showError(field, 'Ce champ est obligatoire');
                    return false;
                }
                break;
                
            case 'emplacement':
                if (!value) {
                    showError(field, 'L\'emplacement est obligatoire');
                    return false;
                }
                if (value.length < 3) {
                    showError(field, 'Minimum 3 caractères');
                    return false;
                }
                if (/[<>{}[\]\\]/.test(value)) {
                    showError(field, 'Caractères non autorisés');
                    return false;
                }
                break;
                
            case 'date':
                if (!value) {
                    showError(field, 'La date est obligatoire');
                    return false;
                }
                const selectedDate = new Date(value);
                const todayDate = new Date(today);
                if (selectedDate > todayDate) {
                    showError(field, 'Date future non autorisée');
                    return false;
                }
                break;
        }
        
        showSuccess(field);
        return true;
    }

    function updateProgress() {
        let validatedFields = 0;
        Object.keys(fields).forEach(field => {
            if (fields[field] && fields[field].classList.contains('success')) {
                validatedFields++;
            }
        });
        
        progressSteps.forEach((step, index) => {
            if (index < validatedFields) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }

    // Validation en temps réel
    Object.keys(fields).forEach(field => {
        if (field !== 'unite' && fields[field]) {
            fields[field].addEventListener('blur', () => {
                validate(field);
                updateProgress();
            });
            
            fields[field].addEventListener('change', () => {
                validate(field);
                updateProgress();
            });
            
            if (field === 'emplacement') {
                fields[field].addEventListener('input', () => {
                    if (fields[field].value.length >= 3) {
                        validate(field);
                        updateProgress();
                    }
                });
            }
        }
    });

    // Soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        Object.keys(fields).forEach(field => {
            if (field !== 'unite' && !validate(field)) {
                isValid = false;
            }
        });
        
        if (isValid) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
            }
            this.submit();
        } else {
            const firstError = document.querySelector('.form-input.error, .form-select.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
}

// ========== UTILITY FUNCTIONS ==========
// Format date to French format
function formatDateFR(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Debounce function for inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ========== SMOOTH SCROLL ==========
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

console.log('SmartGarden Dashboard JS loaded successfully!');