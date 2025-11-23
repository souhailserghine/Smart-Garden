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

function validerResetPassword() {
    effacerErreurs();
    return validerMotDePasse() && validerConfirmationMotDePasse();
}

function validerMotDePasse() {
    let motDePasse = document.querySelector('[name="password"]');
    if (!motDePasse) return true;
    
    let parent = motDePasse.parentNode.parentNode;
    effacerMessagesChamp(parent);

    let passwordValue = motDePasse.value;
    
    if (passwordValue.length === 0) {
        afficherMessage(parent, 'Le mot de passe est obligatoire.', true);
        return false;
    }
    
    if (passwordValue.length < 8) {
        afficherMessage(parent, 'Le mot de passe doit contenir au moins 8 caractères.', true);
        return false;
    }
    
    let hasLetter = false;
    let hasNumber = false;
    for (let i = 0; i < passwordValue.length; i++) {
        let charCode = passwordValue.charCodeAt(i);
        if ((charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122)) {
            hasLetter = true;
        }
        if (charCode >= 48 && charCode <= 57) {
            hasNumber = true;
        }
    }
    
    if (!hasLetter || !hasNumber) {
        afficherMessage(parent, 'Le mot de passe doit contenir au moins une lettre et un chiffre.', true);
        return false;
    }
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

function validerConfirmationMotDePasse() {
    let motDePasse = document.querySelector('[name="password"]');
    let confirmation = document.querySelector('[name="password_confirm"]');
    if (!confirmation) return true;
    
    let parent = confirmation.parentNode.parentNode;
    effacerMessagesChamp(parent);

    let confirmValue = confirmation.value;
    
    if (confirmValue.length === 0) {
        afficherMessage(parent, 'Veuillez confirmer votre mot de passe.', true);
        return false;
    }
    
    if (confirmValue !== motDePasse.value) {
        afficherMessage(parent, 'Les mots de passe ne correspondent pas.', true);
        return false;
    }
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    let form = document.querySelector('#resetPasswordForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validerResetPassword()) {
                e.preventDefault();
            }
        });
    }

    let motDePasse = document.querySelector('[name="password"]');
    if (motDePasse) {
        motDePasse.addEventListener('blur', validerMotDePasse);
    }

    let confirmation = document.querySelector('[name="password_confirm"]');
    if (confirmation) {
        confirmation.addEventListener('blur', validerConfirmationMotDePasse);
    }
});
