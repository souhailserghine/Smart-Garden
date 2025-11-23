function effacerErreurs() {
    document.querySelectorAll('.error-message, .success-message').forEach(msg => msg.remove());
}

function effacerMessagesChamp(parent) {
    parent.querySelectorAll('.error-message, .success-message').forEach(msg => msg.remove());
}

function afficherMessage(parent, message, estErreur) {
    const span = document.createElement('span');
    if (estErreur) {
        span.className = 'error-message';
        span.style.color = 'red';
    } else {
        span.className = 'success-message';
        span.style.color = 'green';
    }
    span.innerHTML = '<br>' + message;
    parent.appendChild(span);
}

function validerModificationUtilisateur() {
    effacerErreurs();
    return validerNomEdit() && validerEmailEdit() && validerLocalisationEdit() && validerStatutEdit();
}

function validerNomEdit() {
    let nom = document.querySelector('#editUserModal [name="nom"]');
    if (!nom) return true;
    
    let parent = nom.parentNode;
    effacerMessagesChamp(parent);

    let nomValue = nom.value.trim();
    
    if (nomValue.length === 0) {
        afficherMessage(parent, 'Le nom est obligatoire.', true);
        return false;
    }
    
    if (nomValue.length < 3) {
        afficherMessage(parent, 'Le nom doit contenir au moins 3 caractères.', true);
        return false;
    }
    
    for (let i = 0; i < nomValue.length; i++) {
        let charCode = nomValue.charCodeAt(i);
        let isLetter = (charCode >= 65 && charCode <= 90) ||
                       (charCode >= 97 && charCode <= 122) ||
                       (charCode >= 192 && charCode <= 255) ||
                       (nomValue[i] === ' ');
        if (!isLetter) {
            afficherMessage(parent, 'Le nom doit contenir uniquement des lettres et des espaces.', true);
            return false;
        }
    }
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

function validerEmailEdit() {
    let email = document.querySelector('#editUserModal [name="email"]');
    if (!email) return true;
    
    let parent = email.parentNode;
    effacerMessagesChamp(parent);

    let emailValue = email.value.trim();
    
    if (emailValue.length === 0) {
        afficherMessage(parent, 'L\'adresse email est obligatoire.', true);
        return false;
    }
    
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
        afficherMessage(parent, 'L\'adresse email n\'est pas valide.', true);
        return false;
    }
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

function validerLocalisationEdit() {
    let localisation = document.querySelector('#editUserModal [name="localisation"]');
    if (!localisation) return true;
    
    let parent = localisation.parentNode;
    effacerMessagesChamp(parent);

    let locValue = localisation.value.trim();
    if (locValue.length === 0) {
        afficherMessage(parent, 'Correct', false);
        return true;
    }

    if (locValue.length < 3) {
        afficherMessage(parent, 'La localisation doit contenir au moins 3 caractères.', true);
        return false;
    }
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

function validerStatutEdit() {
    let statut = document.querySelector('#editUserModal [name="statut"]');
    if (!statut) return true;
    
    let parent = statut.parentNode;
    effacerMessagesChamp(parent);

    let statutValue = statut.value;
    
    if (statutValue !== 'actif' && statutValue !== 'inactif' && statutValue !== 'bloque') {
        afficherMessage(parent, 'Veuillez sélectionner un statut valide.', true);
        return false;
    }
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    let editForm = document.querySelector('#editUserModal form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (!validerModificationUtilisateur()) {
                e.preventDefault();
            }
        });
    }

    let nom = document.querySelector('#editUserModal [name="nom"]');
    if (nom) {
        nom.addEventListener('blur', validerNomEdit);
    }

    let email = document.querySelector('#editUserModal [name="email"]');
    if (email) {
        email.addEventListener('blur', validerEmailEdit);
    }

    let localisation = document.querySelector('#editUserModal [name="localisation"]');
    if (localisation) {
        localisation.addEventListener('blur', validerLocalisationEdit);
    }

    let statut = document.querySelector('#editUserModal [name="statut"]');
    if (statut) {
        statut.addEventListener('change', validerStatutEdit);
    }
});
