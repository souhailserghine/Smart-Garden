<?php
session_start();

include '../../controller/CapteurC.php';
include '../../model/Capteur.php';

$capteurC = new CapteurC();
$error = "";
$success = "";

// Charger les catégories et plantes
try {
    $pdo = config::getConnexion();
    $stmt = $pdo->query("SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->query("SELECT id_plante, nom_plante FROM plante ORDER BY nom_plante");
    $plantes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur de chargement : " . $e->getMessage();
    $categories = [];
    $plantes = [];
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !empty($_POST['id_categorie']) &&
        !empty($_POST['unite']) &&
        !empty($_POST['etat']) &&
        !empty($_POST['emplacement']) &&
        !empty($_POST['dateInstallation']) &&
        !empty($_POST['id_plante'])
    ) {
        $capteur = new Capteur(
            trim($_POST['etat']),
            trim($_POST['unite']),
            trim($_POST['emplacement']),
            $_POST['dateInstallation'],
            (int)$_POST['id_categorie'],
            (int)$_POST['id_plante']
        );

        try {
            $capteurC->addCapteur($capteur);
            $success = "✅ Capteur ajouté avec succès !";
        } catch (Exception $e) {
            $error = "❌ Erreur : " . $e->getMessage();
        }
    } else {
        $error = "❌ Tous les champs sont obligatoires !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Capteur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e8f5f3 0%, #d4f1f4 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .main-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.1);
            border: 1px solid #d1fae5;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        h1 {
            background: linear-gradient(135deg, #10b981 0%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.95em;
        }

        .header-divider {
            height: 4px;
            background: linear-gradient(90deg, transparent, #34d399, transparent);
            margin: 20px auto;
            border-radius: 2px;
            max-width: 300px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #f87171;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #34d399;
        }

        .alert i {
            font-size: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #065f46;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group label i {
            color: #10b981;
            font-size: 16px;
        }

        .required {
            color: #dc2626;
            margin-left: 2px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #d1fae5;
            border-radius: 10px;
            font-size: 15px;
            background: #f9fafb;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-control:focus {
            border-color: #34d399;
            background: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.1);
        }

        .form-control.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .form-control.success {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background: #e5e7eb;
            cursor: not-allowed;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .error-message i {
            font-size: 14px;
        }

        .success-message {
            color: #059669;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .input-hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
            font-style: italic;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f0fffe;
        }

        .btn {
            padding: 14px 35px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn i {
            font-size: 18px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(107, 114, 128, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .progress-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
            padding: 0 10px;
        }

        .progress-step {
            flex: 1;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            margin: 0 4px;
            transition: all 0.4s ease;
            position: relative;
        }

        .progress-step.active {
            background: linear-gradient(90deg, #10b981, #34d399);
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.3);
        }

        .progress-step.active::after {
            content: '✓';
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            background: #10b981;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .container {
                padding: 25px;
            }

            h1 {
                font-size: 1.8em;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <div class="container">
        <div class="form-header">
            <h1><i class="fas fa-sensor"></i> Ajouter un nouveau Capteur</h1>
            <div class="header-divider"></div>
            <p class="subtitle">Remplissez tous les champs pour enregistrer un nouveau capteur</p>
        </div>

        <div class="progress-indicator">
            <div class="progress-step active"></div>
            <div class="progress-step"></div>
            <div class="progress-step"></div>
            <div class="progress-step"></div>
            <div class="progress-step"></div>
            <div class="progress-step"></div>
        </div>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" id="capteurForm" novalidate>
        <div class="form-grid">
            <!-- Colonne Gauche -->
            <div class="form-group">
                <label for="id_categorie">
                    <i class="fas fa-list"></i>
                    Catégorie <span class="required">*</span>
                </label>
                <select name="id_categorie" id="id_categorie" class="form-control" required>
                    <option value="">-- Sélectionner une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message" id="error_categorie"></span>
            </div>

            <div class="form-group">
                <label for="unite">
                    <i class="fas fa-ruler"></i>
                    Unité de mesure <span class="required">*</span>
                </label>
                <input type="text" name="unite" id="unite" class="form-control" placeholder="Automatique selon la catégorie" readonly required>
                <span class="input-hint">Se remplit automatiquement</span>
            </div>

            <div class="form-group">
                <label for="etat">
                    <i class="fas fa-power-off"></i>
                    État <span class="required">*</span>
                </label>
                <select name="etat" id="etat" class="form-control" required>
                    <option value="">-- Sélectionner un état --</option>
                    <option value="actif">✅ Actif</option>
                    <option value="inactif">⭕ Inactif</option>
                    <option value="maintenance">🔧 Maintenance</option>
                </select>
                <span class="error-message" id="error_etat"></span>
            </div>

            <!-- Colonne Droite -->
            <div class="form-group">
                <label for="emplacement">
                    <i class="fas fa-map-marker-alt"></i>
                    Emplacement <span class="required">*</span>
                </label>
                <input type="text" name="emplacement" id="emplacement" class="form-control" placeholder="Ex : Serre A - Zone 1" maxlength="100" required>
                <span class="error-message" id="error_emplacement"></span>
                <span class="success-message" id="success_emplacement"></span>
            </div>

            <div class="form-group">
                <label for="dateInstallation">
                    <i class="fas fa-calendar-alt"></i>
                    Date d'installation <span class="required">*</span>
                </label>
                <input type="date" name="dateInstallation" id="dateInstallation" class="form-control" required>
                <span class="error-message" id="error_date"></span>
            </div>

            <div class="form-group">
                <label for="id_plante">
                    <i class="fas fa-seedling"></i>
                    Plante associée <span class="required">*</span>
                </label>
                <select name="id_plante" id="id_plante" class="form-control" required>
                    <option value="">-- Sélectionner une plante --</option>
                    <?php foreach ($plantes as $pl): ?>
                        <option value="<?= $pl['id_plante'] ?>"><?= htmlspecialchars($pl['nom_plante']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message" id="error_plante"></span>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i>
                Enregistrer le capteur
            </button>
            <a href="listCapteur.php" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Annuler
            </a>
        </div>
    </form>
    </div>
</div>

<script>
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
let validatedFields = 0;

// Date max = aujourd'hui
const today = new Date().toISOString().split('T')[0];
fields.date.setAttribute('max', today);

// Auto-remplir l'unité
fields.categorie.addEventListener('change', function() {
    const selectedText = this.options[this.selectedIndex].text;
    fields.unite.value = categoryUnits[selectedText] || "";
    validate('categorie');
    updateProgress();
});

function showError(field, message) {
    const errorSpan = document.getElementById('error_' + field);
    const successSpan = document.getElementById('success_' + field);
    
    if (errorSpan) {
        errorSpan.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
        fields[field].classList.add('error');
        fields[field].classList.remove('success');
        if (successSpan) successSpan.innerHTML = '';
    }
}

function showSuccess(field) {
    const errorSpan = document.getElementById('error_' + field);
    const successSpan = document.getElementById('success_' + field);
    
    if (errorSpan) errorSpan.innerHTML = '';
    fields[field].classList.remove('error');
    fields[field].classList.add('success');
    
    if (successSpan && field === 'emplacement') {
        successSpan.innerHTML = '<i class="fas fa-check-circle"></i> Emplacement valide';
    }
}

function validate(field) {
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
    validatedFields = 0;
    Object.keys(fields).forEach(field => {
        if (fields[field].classList.contains('success')) {
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
    if (field !== 'unite') {
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

// Soumission
form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    let isValid = true;
    Object.keys(fields).forEach(field => {
        if (field !== 'unite' && !validate(field)) {
            isValid = false;
        }
    });
    
    if (isValid) {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
        this.submit();
    } else {
        const firstError = document.querySelector('.form-control.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }
});
</script>
</body>
</html>