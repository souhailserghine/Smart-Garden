<?php
session_start();

include '../../controller/CategorieC.php';
include '../../model/Categorie.php';

$categorieC = new CategorieC();
$error = "";
$success = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nom_categorie'])) {
        $categorie = new Categorie(trim($_POST['nom_categorie']));

        try {
            $categorieC->addCategorie($categorie);
            $success = "✅ Catégorie ajoutée avec succès !";
        } catch (Exception $e) {
            $error = "❌ Erreur : " . $e->getMessage();
        }
    } else {
        $error = "❌ Le nom de la catégorie est obligatoire !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie</title>
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
            max-width: 800px;
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

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 30px;
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
            font-size: 18px;
        }

        .required {
            color: #dc2626;
            margin-left: 2px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
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
            justify-content: center;
            margin-bottom: 35px;
            padding: 0 10px;
            max-width: 200px;
            margin-left: auto;
            margin-right: auto;
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

        .examples-box {
            background: linear-gradient(135deg, #f0fffe 0%, #d4f1f4 50%);
            border: 2px dashed #34d399;
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
        }

        .examples-box h4 {
            color: #065f46;
            font-size: 13px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .examples-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .examples-box li {
            color: #0d9488;
            font-size: 13px;
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .examples-box li i {
            color: #10b981;
            font-size: 12px;
        }

        @media (max-width: 768px) {
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
            <h1><i class="fas fa-layer-group"></i> Ajouter une nouvelle Catégorie</h1>
            <div class="header-divider"></div>
            <p class="subtitle">Créez une catégorie pour classifier vos capteurs</p>
        </div>

        <div class="progress-indicator">
            <div class="progress-step active"></div>
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

    <form method="POST" id="categorieForm" novalidate>
        <div class="form-group">
            <label for="nom_categorie">
                <i class="fas fa-tag"></i>
                Nom de la catégorie <span class="required">*</span>
            </label>
            <input 
                type="text" 
                name="nom_categorie" 
                id="nom_categorie" 
                class="form-control" 
                placeholder="Ex : Température, Humidité, Luminosité..." 
                maxlength="100" 
                required
            >
            <span class="error-message" id="error_nom"></span>
            <span class="success-message" id="success_nom"></span>
            <span class="input-hint">Maximum 100 caractères</span>
        </div>

        <div class="examples-box">
            <h4><i class="fas fa-lightbulb"></i> Exemples de catégories</h4>
            <ul>
                <li><i class="fas fa-thermometer-half"></i> Température</li>
                <li><i class="fas fa-tint"></i> Humidité</li>
                <li><i class="fas fa-sun"></i> Luminosité</li>
                <li><i class="fas fa-wind"></i> Pression atmosphérique</li>
                <li><i class="fas fa-flask"></i> pH</li>
                <li><i class="fas fa-water"></i> Débit d'eau</li>
                <li><i class="fas fa-smog"></i> CO2</li>
            </ul>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i>
                Enregistrer la catégorie
            </button>
            <a href="listCategorie.php" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Annuler
            </a>
        </div>
    </form>
    </div>
</div>

<script>
const form = document.getElementById('categorieForm');
const nomInput = document.getElementById('nom_categorie');
const progressStep = document.querySelector('.progress-step');

function showError(message) {
    const errorSpan = document.getElementById('error_nom');
    const successSpan = document.getElementById('success_nom');
    
    errorSpan.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
    nomInput.classList.add('error');
    nomInput.classList.remove('success');
    successSpan.innerHTML = '';
    progressStep.classList.remove('active');
}

function showSuccess() {
    const errorSpan = document.getElementById('error_nom');
    const successSpan = document.getElementById('success_nom');
    
    errorSpan.innerHTML = '';
    nomInput.classList.remove('error');
    nomInput.classList.add('success');
    successSpan.innerHTML = '<i class="fas fa-check-circle"></i> Nom valide';
    progressStep.classList.add('active');
}

function validate() {
    const value = nomInput.value.trim();
    
    if (!value) {
        showError('Le nom de la catégorie est obligatoire');
        return false;
    }
    
    if (value.length < 2) {
        showError('Minimum 2 caractères');
        return false;
    }
    
    if (value.length > 100) {
        showError('Maximum 100 caractères');
        return false;
    }
    
    if (!/^[a-zA-ZÀ-ÿ0-9\s\-_.()]+$/.test(value)) {
        showError('Caractères non autorisés détectés');
        return false;
    }
    
    showSuccess();
    return true;
}

// Validation en temps réel
nomInput.addEventListener('input', function() {
    if (this.value.length >= 2) {
        validate();
    }
});

nomInput.addEventListener('blur', function() {
    if (this.value) {
        validate();
    }
});

// Soumission
form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (validate()) {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
        this.submit();
    } else {
        nomInput.focus();
    }
});
</script>
</body>
</html>