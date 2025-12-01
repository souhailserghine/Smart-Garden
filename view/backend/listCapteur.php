<?php
session_start();

include '../../controller/CapteurC.php';

$capteurC = new CapteurC();
$listeCapteurs = [];
$filteredCategoryName = "";

// Gestion des messages de session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Statistiques
$totalCapteurs = 0;
$capteursActifs = 0;
$capteursMaintenance = 0;
$capteursDefectueux = 0;

// Gestion du filtre par catégorie
try {
    if (isset($_GET['id_categorie']) && !empty($_GET['id_categorie'])) {
        $id_categorie = (int)$_GET['id_categorie'];
        $listeCapteurs = $capteurC->showCapteurByCategorie($id_categorie);
        
        if (!empty($listeCapteurs)) {
            $filteredCategoryName = $listeCapteurs[0]['nom_categorie'];
        }
    } else {
        $listeCapteurs = $capteurC->showCapteurWithDetails();
    }

    // Calculer les statistiques
    $totalCapteurs = count($listeCapteurs);
    foreach ($listeCapteurs as $capteur) {
        $etat = $capteur['etatCapteur'] ?? 'inactif';
        switch ($etat) {
            case 'actif':
                $capteursActifs++;
                break;
            case 'maintenance':
                $capteursMaintenance++;
                break;
            case 'defectueux':
                $capteursDefectueux++;
                break;
        }
    }
} catch (Exception $e) {
    $error_message = "Erreur lors du chargement des capteurs : " . $e->getMessage();
}

// Recherche
$searchQuery = $_GET['search'] ?? '';
if ($searchQuery) {
    $listeCapteurs = array_filter($listeCapteurs, function($capteur) use ($searchQuery) {
        $searchIn = strtolower(
            $capteur['nom_categorie'] . ' ' . 
            $capteur['uniteCapteur'] . ' ' . 
            $capteur['etatCapteur'] . ' ' . 
            $capteur['emplacement'] . ' ' . 
            ($capteur['nom_plante'] ?? '')
        );
        return stripos($searchIn, strtolower($searchQuery)) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Gestion Capteurs - SmartGarden</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/capteur.css" rel="stylesheet">
</head>

<body>
    <div class="app-container">
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
               <h3 style="font-size: 1.2rem;padding-left: 20px; color: #2ecc71;">
                        <img src="img/logo-64x64.png" alt="Logo" class="me-2" style="width: 32px; height: 32px; vertical-align: middle;">SmartGarden
                    </h3>
            </div>

            <div class="sidebar-user">
                <img src="img/user.jpg" alt="Admin" class="user-avatar">
                <div class="user-info">
                    <h6 class="user-name">Admin</h6>
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

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content">
            <!-- Top Navbar -->
            <nav class="top-navbar">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-right">
                    <div class="user-dropdown">
                        <img src="img/user.jpg" alt="Admin" class="user-avatar-small">
                        <span class="user-name-nav">Admin</span>
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

            <!-- Messages de notification -->
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success_message) ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error_message) ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Bandeau de filtre -->
            <?php if ($filteredCategoryName): ?>
                <div class="alert" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border: 2px solid #60a5fa; margin: 20px 30px;">
                    <i class="fas fa-filter"></i>
                    <span>Affichage des capteurs de la catégorie : <strong><?= htmlspecialchars($filteredCategoryName) ?></strong></span>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Capteurs</p>
                        <h3 class="stat-value"><?= $totalCapteurs ?></h3>
                    </div>
                </div>

                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Capteurs Actifs</p>
                        <h3 class="stat-value"><?= $capteursActifs ?></h3>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">En Maintenance</p>
                        <h3 class="stat-value"><?= $capteursMaintenance ?></h3>
                    </div>
                </div>

                <div class="stat-card stat-danger">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Défectueux</p>
                        <h3 class="stat-value"><?= $capteursDefectueux ?></h3>
                    </div>
                </div>
            </div>

            <!-- Sensor Management Section -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        Gestion des Capteurs
                        <?php if ($filteredCategoryName): ?>
                            <span style="font-size: 0.7em; color: #0ea5e9;"> - <?= htmlspecialchars($filteredCategoryName) ?></span>
                        <?php endif; ?>
                    </h2>
                    <div class="card-actions">
                        <?php if ($filteredCategoryName): ?>
                            <a href="listCapteur.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                <span>Tous les capteurs</span>
                            </a>
                        <?php endif; ?>
                        <a href="addCapteur.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Ajouter Capteur</span>
                        </a>
                    </div>
                </div>

                <!-- Search Section -->
                <form method="GET" action="listCapteur.php" class="search-form" id="searchForm">
                    <?php if (isset($_GET['id_categorie'])): ?>
                        <input type="hidden" name="id_categorie" value="<?= htmlspecialchars($_GET['id_categorie']) ?>">
                    <?php endif; ?>
                    <div class="search-grid-single">
                        <input type="text" 
                               class="form-input search-input" 
                               name="search" 
                               placeholder="🔍 Rechercher un capteur..." 
                               value="<?= htmlspecialchars($searchQuery) ?>">
                        
                        <button type="submit" class="btn btn-search">
                            <i class="fas fa-search"></i>
                            <span>Rechercher</span>
                        </button>
                    </div>
                </form>

                <!-- Sensors Table -->
                <div class="table-container">
                    <?php if (empty($listeCapteurs)): ?>
                        <div class="no-data">
                            <i class="fas fa-inbox fa-3x"></i>
                            <p>Aucun capteur trouvé<?= $filteredCategoryName ? ' pour cette catégorie' : '' ?></p>
                            <?php if (!$filteredCategoryName): ?>
                                <a href="addCapteur.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Ajouter un capteur
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Catégorie</th>
                                    <th>Unité</th>
                                    <th>État</th>
                                    <th>Emplacement</th>
                                    <th>Plante</th>
                                    <th>Date Installation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $index = 1;
                                foreach ($listeCapteurs as $capteur):
                                    // Icônes par catégorie
                                    $categoryIcons = [
                                        'Température' => 'fa-temperature-high',
                                        'Humidité' => 'fa-droplet',
                                        'Luminosité' => 'fa-sun',
                                        'Pression' => 'fa-wind',
                                        'pH' => 'fa-flask',
                                        'Débit' => 'fa-water',
                                        'CO2' => 'fa-smog'
                                    ];
                                    
                                    $categorieName = $capteur['nom_categorie'] ?? 'Non défini';
                                    $icon = 'fa-microchip';
                                    foreach ($categoryIcons as $key => $iconClass) {
                                        if (stripos($categorieName, $key) !== false) {
                                            $icon = $iconClass;
                                            break;
                                        }
                                    }

                                    // Classes de statut
                                    $etat = $capteur['etatCapteur'] ?? 'inactif';
                                    $statutClass = match($etat) {
                                        'actif' => 'badge-success',
                                        'maintenance' => 'badge-warning',
                                        'defectueux' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                ?>
                                <tr>
                                    <td><?= $index ?></td>
                                    <td>
                                        <div class="category-name">
                                            <i class="fas <?= $icon ?> category-icon"></i>
                                            <strong><?= htmlspecialchars($categorieName) ?></strong>
                                        </div>
                                    </td>
                                    <td><strong><?= htmlspecialchars($capteur['uniteCapteur'] ?? 'N/A') ?></strong></td>
                                    <td>
                                        <span class="badge <?= $statutClass ?>">
                                            <?php
                                            $statusIcons = [
                                                'actif' => 'fa-check',
                                                'inactif' => 'fa-times',
                                                'maintenance' => 'fa-wrench',
                                                'defectueux' => 'fa-exclamation-triangle'
                                            ];
                                            ?>
                                            <i class="fas <?= $statusIcons[$etat] ?? 'fa-circle' ?>"></i>
                                            <?= ucfirst($etat) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt" style="color: #10b981; margin-right: 5px;"></i>
                                        <?= htmlspecialchars($capteur['emplacement'] ?? 'Non défini') ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($capteur['nom_plante'])): ?>
                                            <span class="badge badge-success">
                                                <i class="fas fa-leaf"></i>
                                                <?= htmlspecialchars($capteur['nom_plante']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #999; font-style: italic;">Aucune plante</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar-alt" style="color: #10b981; margin-right: 5px;"></i>
                                        <?= !empty($capteur['dateInstallation']) ? date('d/m/Y', strtotime($capteur['dateInstallation'])) : 'N/A' ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-edit" 
                                                    title="Modifier"
                                                    onclick="window.location.href='updateCapteur.php?id=<?= $capteur['id_capteur'] ?>'">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <button class="btn-action btn-delete" 
                                                    title="Supprimer"
                                                    onclick="confirmDeleteCapteur(<?= $capteur['id_capteur'] ?>, '<?= htmlspecialchars($categorieName) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                $index++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/capteur.js"></script>
    
    <script>
    function confirmDeleteCapteur(id, categorie) {
        const message = `⚠️ Êtes-vous sûr de vouloir supprimer le capteur "${categorie}" ?\n\nCette action est irréversible.`;
        
        if (confirm(message)) {
            window.location.href = 'deleteCapteur.php?id=' + id;
        }
    }
    </script>
</body>
</html>