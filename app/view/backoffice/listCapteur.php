<?php
require_once 'check_session.php';

include '../../controller/capteurC.php';

$capteurC = new CapteurC();
$listeCapteurs = [];
$error_message = "";
$success_message = "";

// Gestion des messages de session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Récupération des paramètres de filtrage
$searchTerm = isset($_GET['search']) && !empty(trim($_GET['search'])) ? trim($_GET['search']) : '';
$filterEtat = isset($_GET['etat']) ? $_GET['etat'] : 'tous';
$filterCategorie = isset($_GET['categorie']) && !empty($_GET['categorie']) ? $_GET['categorie'] : null;
$filterPlante = isset($_GET['plante']) ? $_GET['plante'] : 'tous';
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'id';
$orderDir = isset($_GET['orderDir']) ? $_GET['orderDir'] : 'ASC';

// Récupérer toutes les catégories pour le filtre
$categories = [];
try {
    $categories = $capteurC->getAllCategories();
} catch (Exception $e) {
    $error_message = "Erreur lors du chargement des catégories : " . $e->getMessage();
}

// Appliquer les filtres
try {
    $listeCapteurs = $capteurC->filterCapteurs(
        $searchTerm,
        $filterEtat,
        $filterCategorie,
        $filterPlante,
        $orderBy,
        $orderDir
    );
} catch (Exception $e) {
    $error_message = "Erreur lors du chargement des capteurs : " . $e->getMessage();
}

// Récupérer le nom de la catégorie filtrée
$filteredCategoryName = '';
if ($filterCategorie && !empty($listeCapteurs)) {
    $filteredCategoryName = $listeCapteurs[0]['nom_categorie'] ?? '';
}

// Statistiques
$totalCapteurs = count($listeCapteurs);
$capteursActifs = 0;
$capteursMaintenance = 0;
$capteursDefectueux = 0;
$capteursInactifs = 0;

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
        case 'inactif':
            $capteursInactifs++;
            break;
    }
}

// Compter les filtres actifs
$activeFilters = 0;
if (!empty($searchTerm)) $activeFilters++;
if ($filterEtat !== 'tous') $activeFilters++;
if ($filterCategorie !== null && $filterCategorie !== '') $activeFilters++;
if ($filterPlante !== 'tous') $activeFilters++;
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
    
    <style>
        /* Styles pour le panneau de filtres */
        .filters-panel {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin: 20px 30px;
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f2f5;
        }

        .filters-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-input,
        .filter-select {
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .filters-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid #f0f2f5;
        }

        .btn-apply-filters {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-apply-filters:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-reset-filters {
            background: white;
            color: #6b7280;
            padding: 12px 28px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-reset-filters:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #374151;
        }

        .active-filters-banner {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 2px solid #60a5fa;
            border-radius: 12px;
            padding: 15px 20px;
            margin: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .active-filters-content {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .active-filters-label {
            font-weight: 600;
            color: #1e40af;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-tag {
            background: white;
            border: 1px solid #93c5fd;
            color: #1e40af;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .toggle-filters-btn {
            background: white;
            border: 2px solid #e5e7eb;
            color: #6b7280;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin: 20px 30px 0;
        }

        .toggle-filters-btn:hover {
            background: #f9fafb;
            border-color: #10b981;
            color: #10b981;
        }

        .filters-panel.collapsed {
            display: none;
        }

        /* Styles pour la modale de recommandation */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f2f5;
        }

        .modal-title {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.3s;
        }

        .modal-close:hover {
            color: #ef4444;
        }

        .modal-body {
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            transition: all 0.3s;
        }

        .form-select:focus {
            outline: none;
            border-color: #10b981;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .capteur-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }

        .capteur-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #1e40af;
        }

        .capteur-info strong {
            color: #1e3a8a;
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-modal {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-modal-cancel {
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }

        .btn-modal-cancel:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .btn-modal-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-modal-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-modal-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .alert-info {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .btn-ai {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }

        .btn-ai:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        }

        /* Style pour les messages d'erreur détaillés */
        .error-details {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 13px;
            color: #991b1b;
        }

        .error-details pre {
            background: white;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
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
                <img src="img/user.jpg" alt="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>" class="user-avatar">
                <div class="user-info">
                    <h6 class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></h6>
                    <span class="user-role">Administrateur</span>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="backoffice.php" class="menu-item">
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
                <a href="reservations.php" class="menu-item">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Réservations</span>
                </a>
                <a href="utilisateurs.php" class="menu-item">
                    <i class="fas fa-user"></i>
                    <span>Utilisateur</span>
                </a>
                <a href="listCategorie.php" class="menu-item active">
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

            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label"><?= $activeFilters > 0 ? 'Résultats trouvés' : 'Total Capteurs' ?></p>
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

            <!-- Bouton toggle filtres -->
            <button class="toggle-filters-btn" onclick="toggleFilters()">
                <i class="fas fa-filter"></i>
                <span id="toggleFiltersText">
                    <?= $activeFilters > 0 ? "Modifier les filtres ($activeFilters actifs)" : "Afficher les filtres" ?>
                </span>
                <i class="fas fa-chevron-down" id="toggleFiltersIcon"></i>
            </button>

            <!-- Panneau de filtres -->
            <div class="filters-panel" id="filtersPanel" <?= $activeFilters > 0 ? '' : 'style="display:none;"' ?>>
                <div class="filters-header">
                    <h3 class="filters-title">
                        <i class="fas fa-sliders-h"></i> Filtres Avancés
                        <?php if ($activeFilters > 0): ?>
                            <span class="filter-badge"><?= $activeFilters ?></span>
                        <?php endif; ?>
                    </h3>
                </div>

                <form method="GET" action="listCapteur.php" id="filterForm">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-search"></i> Recherche
                            </label>
                            <input type="text" 
                                   name="search" 
                                   class="filter-input"
                                   placeholder="Rechercher un capteur..." 
                                   value="<?= htmlspecialchars($searchTerm) ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-layer-group"></i> Catégorie
                            </label>
                            <select name="categorie" class="filter-select">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>" 
                                            <?= $filterCategorie == $cat['id_categorie'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-power-off"></i> État
                            </label>
                            <select name="etat" class="filter-select">
                                <option value="tous" <?= $filterEtat == 'tous' ? 'selected' : '' ?>>Tous les états</option>
                                <option value="actif" <?= $filterEtat == 'actif' ? 'selected' : '' ?>>✓ Actif</option>
                                <option value="inactif" <?= $filterEtat == 'inactif' ? 'selected' : '' ?>>○ Inactif</option>
                                <option value="maintenance" <?= $filterEtat == 'maintenance' ? 'selected' : '' ?>>⚙ Maintenance</option>
                                <option value="defectueux" <?= $filterEtat == 'defectueux' ? 'selected' : '' ?>>⚠ Défectueux</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-leaf"></i> Plantes
                            </label>
                            <select name="plante" class="filter-select">
                                <option value="tous" <?= $filterPlante == 'tous' ? 'selected' : '' ?>>Tous</option>
                                <option value="avec" <?= $filterPlante == 'avec' ? 'selected' : '' ?>>Avec plante</option>
                                <option value="sans" <?= $filterPlante == 'sans' ? 'selected' : '' ?>>Sans plante</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-sort"></i> Trier par
                            </label>
                            <select name="orderBy" class="filter-select">
                                <option value="id" <?= $orderBy == 'id' ? 'selected' : '' ?>>ID</option>
                                <option value="categorie" <?= $orderBy == 'categorie' ? 'selected' : '' ?>>Catégorie</option>
                                <option value="etat" <?= $orderBy == 'etat' ? 'selected' : '' ?>>État</option>
                                <option value="emplacement" <?= $orderBy == 'emplacement' ? 'selected' : '' ?>>Emplacement</option>
                                <option value="date" <?= $orderBy == 'date' ? 'selected' : '' ?>>Date installation</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-sort-amount-down"></i> Ordre
                            </label>
                            <select name="orderDir" class="filter-select">
                                <option value="ASC" <?= $orderDir == 'ASC' ? 'selected' : '' ?>>↑ Croissant</option>
                                <option value="DESC" <?= $orderDir == 'DESC' ? 'selected' : '' ?>>↓ Décroissant</option>
                            </select>
                        </div>
                    </div>

                    <div class="filters-actions">
                        <button type="submit" class="btn-apply-filters">
                            <i class="fas fa-check"></i> Appliquer les filtres
                        </button>
                        <a href="listCapteur.php" class="btn-reset-filters">
                            <i class="fas fa-redo"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Affichage des filtres actifs -->
            <?php if ($activeFilters > 0): ?>
                <div class="active-filters-banner">
                    <div class="active-filters-content">
                        <span class="active-filters-label">
                            <i class="fas fa-filter"></i> Filtres actifs:
                        </span>
                        <?php if (!empty($searchTerm)): ?>
                            <span class="filter-tag">
                                <i class="fas fa-search"></i> "<?= htmlspecialchars($searchTerm) ?>"
                            </span>
                        <?php endif; ?>
                        <?php if ($filterEtat !== 'tous'): ?>
                            <span class="filter-tag">
                                <i class="fas fa-power-off"></i> État: <?= ucfirst($filterEtat) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($filterCategorie && $filteredCategoryName): ?>
                            <span class="filter-tag">
                                <i class="fas fa-layer-group"></i> <?= htmlspecialchars($filteredCategoryName) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($filterPlante !== 'tous'): ?>
                            <span class="filter-tag">
                                <i class="fas fa-leaf"></i> <?= $filterPlante == 'avec' ? 'Avec plante' : 'Sans plante' ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <a href="listCapteur.php" class="btn btn-secondary" style="margin: 0;">
                        <i class="fas fa-times"></i> Effacer tous
                    </a>
                </div>
            <?php endif; ?>

            <!-- Sensor Management Section -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        Gestion des Capteurs
                        <?php if ($filteredCategoryName): ?>
                            <span style="font-size: 0.7em; color: #10b981;"> - <?= htmlspecialchars($filteredCategoryName) ?></span>
                        <?php endif; ?>
                    </h2>
                    <div class="card-actions">
                        <a href="listCategorie.php" class="btn btn-secondary">
                            <i class="fas fa-layer-group"></i>
                            <span>Catégories</span>
                        </a>
                        <a href="addCapteur.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Ajouter Capteur</span>
                        </a>
                    </div>
                </div>

                <!-- Sensors Table -->
                <div class="table-container">
                    <?php if (empty($listeCapteurs)): ?>
                        <div class="no-data">
                            <i class="fas fa-inbox fa-3x"></i>
                            <p>
                                <?php if ($activeFilters > 0): ?>
                                    Aucun capteur ne correspond aux critères de recherche.
                                <?php else: ?>
                                    Aucun capteur trouvé.
                                <?php endif; ?>
                            </p>
                            <?php if ($activeFilters > 0): ?>
                                <a href="listCapteur.php" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Voir tous les capteurs
                                </a>
                            <?php else: ?>
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
                                            <button class="btn-action btn-ai" 
                                                    title="Recommandation IA"
                                                    onclick="ouvrirModaleRecommandation(<?= $capteur['id_capteur'] ?>, '<?= htmlspecialchars($categorieName, ENT_QUOTES) ?>', '<?= htmlspecialchars($capteur['etatCapteur'], ENT_QUOTES) ?>', '<?= htmlspecialchars($capteur['emplacement'], ENT_QUOTES) ?>')">
                                                <i class="fas fa-robot"></i>
                                            </button>
                                            
                                            <button class="btn-action btn-edit" 
                                                    title="Modifier"
                                                    onclick="window.location.href='updateCapteur.php?id=<?= $capteur['id_capteur'] ?>'">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <button class="btn-action btn-delete" 
                                                    title="Supprimer"
                                                    onclick="confirmDeleteCapteur(<?= $capteur['id_capteur'] ?>, '<?= htmlspecialchars($categorieName, ENT_QUOTES) ?>')">
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

    <!-- Modale pour recommandation IA -->
    <div class="modal-overlay" id="modalRecommandation">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-robot"></i>
                    Recommandation IA
                </h3>
                <button class="modal-close" onclick="fermerModaleRecommandation()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="alert-info">
                    <i class="fas fa-info-circle"></i>
                    L'IA va analyser votre capteur et envoyer une recommandation par email.
                </div>
                
                <div class="capteur-info" id="capteurInfoModal">
                    <!-- Sera rempli dynamiquement -->
                </div>
                
                <form id="formRecommandation">
                    <input type="hidden" id="capteur_id" name="id_capteur">
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            Sélectionner un utilisateur
                        </label>
                        <select class="form-select" id="utilisateur_select" name="id_utilisateur" required>
                            <option value="">-- Choisir un destinataire --</option>
                            <?php
                            try {
                                $utilisateurs = $capteurC->getAllUtilisateurs();
                                foreach ($utilisateurs as $user):
                            ?>
                                <option value="<?= $user['idUtilisateur'] ?>">
                                    <?= htmlspecialchars($user['nom']) ?> 
                                    (<?= htmlspecialchars($user['email']) ?>)
                                </option>
                            <?php 
                                endforeach;
                            } catch (Exception $e) {
                                echo '<option value="">Erreur chargement utilisateurs</option>';
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button class="btn-modal btn-modal-cancel" onclick="fermerModaleRecommandation()">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
                <button class="btn-modal btn-modal-submit" id="btnGenerer" onclick="genererRecommandation()">
                    <i class="fas fa-magic"></i>
                    Générer & Envoyer
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/capteur.js"></script>
    
    <script>
    let capteurSelectionne = null;

    function ouvrirModaleRecommandation(id, categorie, etat, emplacement) {
        capteurSelectionne = {
            id: id,
            categorie: categorie,
            etat: etat,
            emplacement: emplacement
        };
        
        // Remplir les infos du capteur
        document.getElementById('capteurInfoModal').innerHTML = `
            <p><strong>Capteur #${id}</strong></p>
            <p><i class="fas fa-layer-group"></i> Catégorie: ${categorie}</p>
            <p><i class="fas fa-power-off"></i> État: ${etat}</p>
            <p><i class="fas fa-map-marker-alt"></i> Emplacement: ${emplacement}</p>
        `;
        
        document.getElementById('capteur_id').value = id;
        document.getElementById('modalRecommandation').classList.add('active');
    }

    function fermerModaleRecommandation() {
        document.getElementById('modalRecommandation').classList.remove('active');
        document.getElementById('formRecommandation').reset();
        capteurSelectionne = null;
    }

    function genererRecommandation() {
        const utilisateurId = document.getElementById('utilisateur_select').value;
        
        if (!utilisateurId) {
            alert('⚠️ Veuillez sélectionner un utilisateur');
            return;
        }
        
        const btnGenerer = document.getElementById('btnGenerer');
        const btnAnnuler = document.querySelector('.btn-modal-cancel');
        
        // Désactiver les boutons
        btnGenerer.disabled = true;
        btnAnnuler.disabled = true;
        btnGenerer.innerHTML = '<span class="loading-spinner"></span> Génération en cours...';
        
        // Préparer les données
        const formData = new FormData();
        formData.append('id_capteur', capteurSelectionne.id);
        formData.append('id_utilisateur', utilisateurId);
        
        // Log pour debugging
        console.log('📤 Envoi de la requête:', {
            id_capteur: capteurSelectionne.id,
            id_utilisateur: utilisateurId
        });
        
        // Appel AJAX
        fetch('genererRecommandation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('📥 Réponse HTTP:', response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`Erreur serveur: HTTP ${response.status}`);
            }
            
            // Lire d'abord en text pour débugger
            return response.text();
        })
        .then(text => {
            console.log('📄 Réponse brute (200 premiers caractères):', text.substring(0, 200));
            
            // Essayer de parser le JSON
            let data;
            try {
                data = JSON.parse(text);
                console.log('✅ JSON parsé avec succès:', data);
            } catch (e) {
                console.error('❌ Erreur de parsing JSON:', e);
                console.error('📄 Contenu complet:', text);
                throw new Error('Réponse invalide du serveur. Vérifiez la console pour plus de détails.');
            }
            
            if (data.success) {
                console.log('✅ Succès!', data);
                
                let message = '✅ Recommandation générée et envoyée avec succès!\n\n';
                
                if (data.data) {
                    message += '📧 Email envoyé à: ' + data.data.destinataire.email + '\n';
                    message += '🤖 Recommandation: ' + data.data.recommandation.titre;
                } else if (data.destinataire) {
                    message += '📧 Email envoyé à: ' + data.destinataire;
                }
                
                alert(message);
                fermerModaleRecommandation();
                afficherMessageSucces('Recommandation IA générée et envoyée par email avec succès!');
            } else {
                console.error('❌ Échec de l\'opération:', data);
                
                let errorMessage = '❌ Erreur: ' + (data.error || 'Une erreur est survenue');
                
                if (data.debug) {
                    errorMessage += '\n\n📍 Debug: ' + data.debug.file + ' ligne ' + data.debug.line;
                }
                
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('❌ Erreur complète:', error);
            
            let errorMessage = '❌ Erreur lors de la génération de la recommandation:\n\n' + error.message;
            errorMessage += '\n\n💡 Vérifiez:\n';
            errorMessage += '- La console du navigateur (F12)\n';
            errorMessage += '- Les logs du serveur PHP\n';
            errorMessage += '- Le fichier genererRecommandation.php';
            
            alert(errorMessage);
        })
        .finally(() => {
            // Réactiver les boutons
            btnGenerer.disabled = false;
            btnAnnuler.disabled = false;
            btnGenerer.innerHTML = '<i class="fas fa-magic"></i> Générer & Envoyer';
        });
    }

    function afficherMessageSucces(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success';
        alertDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        `;
        
        const mainContent = document.querySelector('.main-content');
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    function confirmDeleteCapteur(id, categorie) {
        const message = `⚠️ Êtes-vous sûr de vouloir supprimer le capteur "${categorie}" ?\n\nCette action est irréversible.`;
        
        if (confirm(message)) {
            window.location.href = 'deleteCapteur.php?id=' + id;
        }
    }

    function toggleFilters() {
        const panel = document.getElementById('filtersPanel');
        const icon = document.getElementById('toggleFiltersIcon');
        
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            panel.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

    // Fermer la modale en cliquant sur l'overlay
    document.getElementById('modalRecommandation').addEventListener('click', function(e) {
        if (e.target === this) {
            fermerModaleRecommandation();
        }
    });

    // Gestion des alertes
    document.addEventListener('DOMContentLoaded', function() {
        const alertCloses = document.querySelectorAll('.alert-close');
        alertCloses.forEach(btn => {
            btn.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });
    });
    </script>
</body>
</html>