<?php
session_start();

include '../../controller/CategorieC.php';
include '../../model/Categorie.php';

$categorieC = new CategorieC();
$error = "";
$success = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !empty($_POST['nom_categorie']) &&
        !empty($_POST['unite']) &&
        !empty($_POST['description'])
    ) {
        $categorie = new Categorie(
            trim($_POST['nom_categorie']),
            trim($_POST['unite']),
            trim($_POST['description'])
        );

        try {
            $categorieC->addCategorie($categorie);
            $success = "Catégorie ajoutée avec succès !";
        } catch (Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    } else {
        $error = "Tous les champs sont obligatoires !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ajouter une Catégorie - SmartGarden</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/capteur.css" rel="stylesheet">
</head>

<body>
    <div class="main-content-wrapper">
        <!-- Messages de notification -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
                <button class="alert-close">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($success) ?></span>
                <button class="alert-close">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Form Section -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i>
                    Ajouter une nouvelle Catégorie
                </h2>
                <div class="card-actions">
                    <a href="listCategorie.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour à la liste</span>
                    </a>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="progress-container">
                <div class="progress-indicator">
                    <div class="progress-step active"></div>
                    <div class="progress-step"></div>
                    <div class="progress-step"></div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-content">
                <form method="POST" id="categorieForm" novalidate>
                    <div class="form-grid">
                        <!-- Nom de la catégorie -->
                        <div class="form-group">
                            <label for="nom_categorie" class="form-label">
                                <i class="fas fa-tag"></i>
                                Nom de la catégorie <span class="required">*</span>
                            </label>
                            <input type="text" name="nom_categorie" id="nom_categorie" class="form-input" placeholder="Ex : Température, Humidité..." maxlength="50" required>
                            <span class="error-message" id="error_nom"></span>
                            <span class="success-message" id="success_nom"></span>
                        </div>

                        <!-- Unité de mesure -->
                        <div class="form-group">
                            <label for="unite" class="form-label">
                                <i class="fas fa-ruler"></i>
                                Unité de mesure <span class="required">*</span>
                            </label>
                            <input type="text" name="unite" id="unite" class="form-input" placeholder="Ex : °C, %, lux..." maxlength="20" required>
                            <span class="error-message" id="error_unite"></span>
                            <span class="success-message" id="success_unite"></span>
                        </div>

                        <!-- Description (full width) -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left"></i>
                                Description <span class="required">*</span>
                            </label>
                            <textarea name="description" id="description" class="form-input form-textarea" rows="4" placeholder="Décrivez la catégorie de capteur..." maxlength="500" required></textarea>
                            <span class="error-message" id="error_description"></span>
                            <span class="success-message" id="success_description"></span>
                            <span class="input-hint">Maximum 500 caractères</span>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="listCategorie.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            <span>Annuler</span>
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span>Enregistrer la catégorie</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/capteur.js"></script>
    
    <script>
        // Validation spécifique pour le formulaire catégorie
        const categorieForm = document.getElementById('categorieForm');
        const categorieFields = {
            nom: document.getElementById('nom_categorie'),
            unite: document.getElementById('unite'),
            description: document.getElementById('description')
        };

        const categorieProgressSteps = document.querySelectorAll('.progress-step');

        // Validation en temps réel
        categorieFields.nom.addEventListener('blur', () => validateCategorieField('nom'));
        categorieFields.nom.addEventListener('input', () => {
            if (categorieFields.nom.value.length >= 3) validateCategorieField('nom');
        });

        categorieFields.unite.addEventListener('blur', () => validateCategorieField('unite'));
        categorieFields.unite.addEventListener('input', () => {
            if (categorieFields.unite.value.length >= 1) validateCategorieField('unite');
        });

        categorieFields.description.addEventListener('blur', () => validateCategorieField('description'));
        categorieFields.description.addEventListener('input', () => {
            if (categorieFields.description.value.length >= 10) validateCategorieField('description');
        });

        function validateCategorieField(field) {
            const value = categorieFields[field].value.trim();

            switch(field) {
                case 'nom':
                    if (!value) {
                        showFieldError(field, 'Le nom est obligatoire');
                        return false;
                    }
                    if (value.length < 3) {
                        showFieldError(field, 'Minimum 3 caractères');
                        return false;
                    }
                    if (/[<>{}[\]\\]/.test(value)) {
                        showFieldError(field, 'Caractères non autorisés');
                        return false;
                    }
                    break;

                case 'unite':
                    if (!value) {
                        showFieldError(field, 'L\'unité est obligatoire');
                        return false;
                    }
                    if (value.length > 20) {
                        showFieldError(field, 'Maximum 20 caractères');
                        return false;
                    }
                    break;

                case 'description':
                    if (!value) {
                        showFieldError(field, 'La description est obligatoire');
                        return false;
                    }
                    if (value.length < 10) {
                        showFieldError(field, 'Minimum 10 caractères');
                        return false;
                    }
                    if (value.length > 500) {
                        showFieldError(field, 'Maximum 500 caractères');
                        return false;
                    }
                    break;
            }

            showFieldSuccess(field);
            updateCategorieProgress();
            return true;
        }

        function showFieldError(field, message) {
            const errorSpan = document.getElementById('error_' + field);
            const successSpan = document.getElementById('success_' + field);
            
            if (errorSpan) {
                errorSpan.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
                errorSpan.style.display = 'flex';
                categorieFields[field].classList.add('error');
                categorieFields[field].classList.remove('success');
            }
            if (successSpan) successSpan.style.display = 'none';
        }

        function showFieldSuccess(field) {
            const errorSpan = document.getElementById('error_' + field);
            const successSpan = document.getElementById('success_' + field);
            
            if (errorSpan) errorSpan.style.display = 'none';
            categorieFields[field].classList.remove('error');
            categorieFields[field].classList.add('success');
            
            if (successSpan) {
                successSpan.innerHTML = '<i class="fas fa-check-circle"></i> Valide';
                successSpan.style.display = 'flex';
            }
        }

        function updateCategorieProgress() {
            let validatedFields = 0;
            
            Object.keys(categorieFields).forEach(field => {
                if (categorieFields[field].classList.contains('success')) {
                    validatedFields++;
                }
            });
            
            categorieProgressSteps.forEach((step, index) => {
                if (index < validatedFields) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });
        }

        // Soumission du formulaire
        categorieForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            Object.keys(categorieFields).forEach(field => {
                if (!validateCategorieField(field)) {
                    isValid = false;
                }
            });
            
            if (isValid) {
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
                this.submit();
            } else {
                const firstError = document.querySelector('.form-input.error, .form-select.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    </script>
</body>
</html>