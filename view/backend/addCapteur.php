<?php
session_start();

include '../../controller/CapteurC.php';
include '../../model/Capteur.php';

$capteurC = new CapteurC();
$error = "";
$success = "";

// Utilisation des méthodes du contrôleur
try {
    $categories = $capteurC->getAllCategories();
    $plantes = $capteurC->getAllPlantes();
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
            $success = "Capteur ajouté avec succès !";
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
    <title>Ajouter un Capteur - SmartGarden</title>
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
                    <i class="fas fa-microchip"></i>
                    Ajouter un nouveau Capteur
                </h2>
                <div class="card-actions">
                    <a href="listCapteur.php" class="btn btn-secondary">
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
                    <div class="progress-step"></div>
                    <div class="progress-step"></div>
                    <div class="progress-step"></div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-content">
                <form method="POST" id="capteurForm" novalidate>
                    <div class="form-grid">
                        <!-- Catégorie -->
                        <div class="form-group">
                            <label for="id_categorie" class="form-label">
                                <i class="fas fa-list"></i>
                                Catégorie <span class="required">*</span>
                            </label>
                            <select name="id_categorie" id="id_categorie" class="form-select" required>
                                <option value="">-- Sélectionner une catégorie --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-message" id="error_categorie"></span>
                        </div>

                        <!-- Unité -->
                        <div class="form-group">
                            <label for="unite" class="form-label">
                                <i class="fas fa-ruler"></i>
                                Unité de mesure <span class="required">*</span>
                            </label>
                            <input type="text" name="unite" id="unite" class="form-input" placeholder="Automatique selon la catégorie" readonly required>
                            <span class="input-hint">Se remplit automatiquement</span>
                        </div>

                        <!-- État -->
                        <div class="form-group">
                            <label for="etat" class="form-label">
                                <i class="fas fa-power-off"></i>
                                État <span class="required">*</span>
                            </label>
                            <select name="etat" id="etat" class="form-select" required>
                                <option value="">-- Sélectionner un état --</option>
                                <option value="actif">✅ Actif</option>
                                <option value="inactif">⭕ Inactif</option>
                                <option value="maintenance">🔧 Maintenance</option>
                            </select>
                            <span class="error-message" id="error_etat"></span>
                        </div>

                        <!-- Emplacement -->
                        <div class="form-group">
                            <label for="emplacement" class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Emplacement <span class="required">*</span>
                            </label>
                            <input type="text" name="emplacement" id="emplacement" class="form-input" placeholder="Ex : Serre A - Zone 1" maxlength="100" required>
                            <span class="error-message" id="error_emplacement"></span>
                            <span class="success-message" id="success_emplacement"></span>
                        </div>

                        <!-- Date d'installation -->
                        <div class="form-group">
                            <label for="dateInstallation" class="form-label">
                                <i class="fas fa-calendar-alt"></i>
                                Date d'installation <span class="required">*</span>
                            </label>
                            <input type="date" name="dateInstallation" id="dateInstallation" class="form-input" required>
                            <span class="error-message" id="error_date"></span>
                        </div>

                        <!-- Plante associée -->
                        <div class="form-group">
                            <label for="id_plante" class="form-label">
                                <i class="fas fa-seedling"></i>
                                Plante associée <span class="required">*</span>
                            </label>
                            <select name="id_plante" id="id_plante" class="form-select" required>
                                <option value="">-- Sélectionner une plante --</option>
                                <?php foreach ($plantes as $pl): ?>
                                    <option value="<?= $pl['id_plante'] ?>"><?= htmlspecialchars($pl['nom_plante']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-message" id="error_plante"></span>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="listCapteur.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            <span>Annuler</span>
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span>Enregistrer le capteur</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/capteur.js"></script>
</body>
</html>