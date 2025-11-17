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

function validerConnexion() {
    effacerErreurs();
    return validerEmail() && validerMotDePasse();
}

function validerEmail() {
    let email = document.querySelector('input[type="text"]');
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

function validerMotDePasse() {
    let motDePasse = document.querySelector('input[type="password"]');
    if (!motDePasse) return true;
    
    let parent = motDePasse.parentNode;
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
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    let form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validerConnexion()) {
                e.preventDefault();
            }
        });
    }

    let email = document.querySelector('input[type="text"]');
    if (email) {
        email.addEventListener('blur', validerEmail);
    }

    let motDePasse = document.querySelector('input[type="password"]');
    if (motDePasse) {
        motDePasse.addEventListener('blur', validerMotDePasse);
    }
});
