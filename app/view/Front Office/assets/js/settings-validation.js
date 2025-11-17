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

function validerSettings() {
    effacerErreurs();
    return validerNom() && validerLocalisation() && validerEmail();
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
                       (nomValue[i] === ' ' || nomValue[i] === '-');
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

document.addEventListener('DOMContentLoaded', function() {
    let form = document.querySelector('.settings-form form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validerSettings()) {
                e.preventDefault();
            }
        });
    }

    let nom = document.querySelector('[name="nom"]');
    if (nom) {
        nom.addEventListener('blur', validerNom);
    }

    let localisation = document.querySelector('[name="localisation"]');
    if (localisation) {
        localisation.addEventListener('blur', validerLocalisation);
    }

    let email = document.querySelector('[name="email"]');
    if (email) {
        email.addEventListener('blur', validerEmail);
    }
});
