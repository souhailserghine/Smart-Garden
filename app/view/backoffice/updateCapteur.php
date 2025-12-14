<?php
require_once 'check_session.php';

include '../../controller/capteurC.php';
include '../../model/capteur.php';

$capteurC = new CapteurC();
$error = "";

// Récupérer l'ID du capteur
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: listCapteur.php");
    exit();
}

$id = (int)$_GET['id'];
$capteurData = $capteurC->getCapteur($id);

if (!$capteurData) {
    $_SESSION['error_message'] = "Capteur introuvable !";
    header("Location: listCapteur.php");
    exit();
}

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
            $capteurC->updateCapteur($capteur, $id);
            $_SESSION['success_message'] = "Capteur #$id modifié avec succès !";
            header("Location: listCapteur.php");
            exit();
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
    <link rel="icon" type="image/png" href="img/logo-16x16.png" />
    <title>SmartGarden</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/capteur.css" rel="stylesheet">
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3 style="font-size: 1.2rem;padding-left: 20px; color: #2ecc71;">
                <img src="img/logo-64x64.png" alt="Logo" class="me-2" style="width: 32px; height: 32px; vertical-align: middle;">SmartGarden
            </h3>
        </div>

        <div class="sidebar-user">
            <img src="img/user.jpg" alt="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>" class="user-avatar">
            <div class="user-info">
                <h6 class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></h6>
                <span class="user-role">Administrateur</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="index.php" class="menu-item">
                <i class="fas fa-file-alt"></i>
                <span>Publication</span>
            </a>
            <a href="plantes.php" class="menu-item">
                <i class="fas fa-leaf"></i>
                <span>Plantes</span>
            </a>
            <a href="evenements.php" class="menu-item">
                <i class="fas fa-calendar"></i>
                <span>Evenements</span>
            </a>
            <a href="utilisateurs.php" class="menu-item">
                <i class="fas fa-user"></i>
                <span>Utilisateur</span>
            </a>
            <a href="listCapteur.php" class="menu-item active">
                <i class="fas fa-microchip"></i>
                <span>Capteurs</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="navbar-right">
                <div class="user-dropdown">
                    <img src="img/user.jpg" alt="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>" class="user-avatar-small">
                    <span class="user-name-nav"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                    <i class="fas fa-chevron-down"></i>
                    
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user"></i> Mon Profil
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog"></i> Paramètres
                        </a>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Wrapper -->
        <div class="main-content-wrapper">
            <!-- Messages de notification -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Form Section -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-edit"></i>
                        Modifier le Capteur #<?= htmlspecialchars($capteurData['id_capteur']) ?>
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
                                        <option value="<?= $cat['id_categorie'] ?>" <?= ($capteurData['id_categorie']==$cat['id_categorie'])?'selected':'' ?>>
                                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                                        </option>
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
                                <input type="text" name="unite" id="unite" class="form-input" value="<?= htmlspecialchars($capteurData['uniteCapteur']) ?>" placeholder="Automatique selon la catégorie" readonly required>
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
                                    <option value="actif" <?= ($capteurData['etatCapteur']=='actif')?'selected':'' ?>>✅ Actif</option>
                                    <option value="inactif" <?= ($capteurData['etatCapteur']=='inactif')?'selected':'' ?>>⭕ Inactif</option>
                                    <option value="maintenance" <?= ($capteurData['etatCapteur']=='maintenance')?'selected':'' ?>>🔧 Maintenance</option>
                                </select>
                                <span class="error-message" id="error_etat"></span>
                            </div>

                            <!-- Emplacement -->
                            <div class="form-group">
                                <label for="emplacement" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Emplacement <span class="required">*</span>
                                </label>
                                <input type="text" name="emplacement" id="emplacement" class="form-input" value="<?= htmlspecialchars($capteurData['emplacement']) ?>" placeholder="Ex : Serre A - Zone 1" maxlength="100" required>
                                <span class="error-message" id="error_emplacement"></span>
                                <span class="success-message" id="success_emplacement"></span>
                            </div>

                            <!-- Date d'installation -->
                            <div class="form-group">
                                <label for="dateInstallation" class="form-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Date d'installation <span class="required">*</span>
                                </label>
                                <input type="date" name="dateInstallation" id="dateInstallation" class="form-input" value="<?= htmlspecialchars($capteurData['dateInstallation']) ?>" required>
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
                                        <option value="<?= $pl['id_plante'] ?>" <?= ($capteurData['id_plante']==$pl['id_plante'])?'selected':'' ?>>
                                            <?= htmlspecialchars($pl['nom_plante']) ?>
                                        </option>
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
                                <span>Enregistrer les modifications</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/capteur.js"></script>
    
    <script>
        // Toggle Sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('expanded');
        });

        // User Dropdown Toggle
        document.querySelector('.user-dropdown').addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            document.querySelector('.user-dropdown').classList.remove('active');
        });

        // Close alerts
        document.querySelectorAll('.alert-close').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });

        // Validation initiale des champs pré-remplis
        window.addEventListener('DOMContentLoaded', function() {
            const fields = {
                categorie: document.getElementById('id_categorie'),
                etat: document.getElementById('etat'),
                emplacement: document.getElementById('emplacement'),
                date: document.getElementById('dateInstallation'),
                plante: document.getElementById('id_plante')
            };
            
            // Valider les champs déjà remplis
            if (typeof validate === 'function') {
                Object.keys(fields).forEach(field => {
                    if (fields[field] && fields[field].value) {
                        validate(field);
                    }
                });
                
                if (typeof updateProgress === 'function') {
                    updateProgress();
                }
            }
        });
    </script>
</body>
</html>