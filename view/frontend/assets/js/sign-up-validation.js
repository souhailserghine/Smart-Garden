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

function validerInscription() {
    effacerErreurs();
    return validerNom() && validerEmail() && validerLocalisation() && 
           validerMotDePasse() && validerConfirmationMotDePasse();
}

function validerNom() {
    let nom = document.querySelector('[name="nom"]');
    if (!nom) return true;
    
    let parent = nom.parentNode;
    effacerMessagesChamp(parent);

    let nomValue = nom.value.trim();
    
    if (nomValue.length < 3) {
        afficherMessage(parent, 'Le nom complet doit contenir au moins 3 caractères.', true);
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

function validerEmail() {
    let email = document.querySelector('[name="email"]');
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

function validerLocalisation() {
    let localisation = document.querySelector('[name="localisation"]');
    if (!localisation) return true;
    
    let parent = localisation.parentNode;
    effacerMessagesChamp(parent);

    let locValue = localisation.value.trim();
    if (locValue.length === 0) return true;

    if (locValue.length < 3) {
        afficherMessage(parent, 'La localisation doit contenir au moins 3 caractères.', true);
        return false;
    }
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

function validerMotDePasse() {
    let motDePasse = document.querySelector('[name="motDePasse"]');
    if (!motDePasse) return true;
    
    let parent = motDePasse.parentNode;
    effacerMessagesChamp(parent);

    let passwordValue = motDePasse.value;
    
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
    let confirmMotDePasse = document.querySelector('[name="confirmMotDePasse"]');
    if (!confirmMotDePasse) return true;
    
    let parent = confirmMotDePasse.parentNode;
    effacerMessagesChamp(parent);

    let confirmValue = confirmMotDePasse.value;
    let motDePasse = document.querySelector('[name="motDePasse"]');
    let passwordValue = '';
    if (motDePasse) {
        passwordValue = motDePasse.value;
    }

    if (confirmValue.length === 0) {
        afficherMessage(parent, 'Veuillez confirmer votre mot de passe.', true);
        return false;
    }
    
    if (confirmValue !== passwordValue) {
        afficherMessage(parent, 'Les mots de passe ne correspondent pas.', true);
        return false;
    }
    
    afficherMessage(parent, 'Correct', false);
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    let form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validerInscription()) {
                e.preventDefault();
            }
        });
    }

    let nom = document.querySelector('[name="nom"]');
    if (nom) {
        nom.addEventListener('blur', validerNom);
    }

    let email = document.querySelector('[name="email"]');
    if (email) {
        email.addEventListener('blur', validerEmail);
    }

    let localisation = document.querySelector('[name="localisation"]');
    if (localisation) {
        localisation.addEventListener('blur', validerLocalisation);
    }

    let motDePasse = document.querySelector('[name="motDePasse"]');
    if (motDePasse) {
        motDePasse.addEventListener('blur', validerMotDePasse);
    }

    let confirmMotDePasse = document.querySelector('[name="confirmMotDePasse"]');
    if (confirmMotDePasse) {
        confirmMotDePasse.addEventListener('blur', validerConfirmationMotDePasse);
    }
});
