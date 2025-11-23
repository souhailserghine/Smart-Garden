console.log('=== Sign-in validation script START ===');

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
    let email = document.querySelector('input[type="email"]');
    if (!email) return true;
    
    let parent = email.parentNode;
    effacerMessagesChamp(parent);

    let emailValue = email.value.trim();
    
    if (emailValue.length === 0) {
        afficherMessage(parent, 'L\'adresse email est obligatoire.', true);
        return false;
    }
    
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
        afficherMessage(parent, "L\'adresse email n\'est pas valide.", true);
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
    console.log('Sign-in validation script loaded');
    
    let form = document.querySelector('form');
    if (form) {
        console.log('Form found');
        form.addEventListener('submit', function(e) {
            console.log('Form submitted');
            if (!validerConnexion()) {
                e.preventDefault();
                console.log('Form validation failed');
            }
        });
    }

    let email = document.querySelector('input[type="email"]');
    if (email) {
        console.log('Email input found:', email);
        email.addEventListener('blur', validerEmail);
        email.addEventListener('input', validerEmail);
    } else {
        console.log('Email input NOT found');
    }

    let motDePasse = document.querySelector('input[type="password"]');
    if (motDePasse) {
        console.log('Password input found');
        motDePasse.addEventListener('blur', validerMotDePasse);
        motDePasse.addEventListener('input', validerMotDePasse);
    } else {
        console.log('Password input NOT found');
    }
});
