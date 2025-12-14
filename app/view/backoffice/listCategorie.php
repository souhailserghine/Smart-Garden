<?php
require_once 'check_session.php';

include '../../controller/categorieC.php';

$categorieC = new CategorieC();
$listeCategories = [];
$error_message = '';
$success_message = '';

// Gestion des messages de session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Récupération des paramètres de filtrage
$searchTerm = isset($_GET['search']) && !empty(trim($_GET['search'])) ? trim($_GET['search']) : '';
$filterCapteurs = isset($_GET['capteurs']) ? $_GET['capteurs'] : 'tous';
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'id';
$orderDir = isset($_GET['orderDir']) ? $_GET['orderDir'] : 'ASC';

// Appliquer les filtres
try {
    $listeCategories = $categorieC->filterCategories(
        $searchTerm,
        $filterCapteurs,
        $orderBy,
        $orderDir
    );
} catch (Exception $e) {
    $error_message = "Erreur lors du chargement des catégories : " . $e->getMessage();
}

// Statistiques
$totalCategories = count($listeCategories);
$totalCapteurs = array_sum(array_column($listeCategories, 'nombre_capteurs'));
$categoriesActives = count(array_filter($listeCategories, fn($c) => $c['nombre_capteurs'] > 0));
$categoriesSansCapteurs = count(array_filter($listeCategories, fn($c) => $c['nombre_capteurs'] == 0));

// Compter les filtres actifs
$activeFilters = 0;
if (!empty($searchTerm)) $activeFilters++;
if ($filterCapteurs !== 'tous') $activeFilters++;
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
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label"><?= $activeFilters > 0 ? 'Résultats trouvés' : 'Total Catégories' ?></p>
                        <h3 class="stat-value"><?= $totalCategories ?></h3>
                    </div>
                </div>

                <div class="stat-card stat-info">
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
                        <p class="stat-label">Catégories Actives</p>
                        <h3 class="stat-value"><?= $categoriesActives ?></h3>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Sans Capteurs</p>
                        <h3 class="stat-value"><?= $categoriesSansCapteurs ?></h3>
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

                <form method="GET" action="listCategorie.php" id="filterForm">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-search"></i> Recherche
                            </label>
                            <input type="text" 
                                   name="search" 
                                   class="filter-input"
                                   placeholder="Rechercher une catégorie..." 
                                   value="<?= htmlspecialchars($searchTerm) ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-microchip"></i> Capteurs
                            </label>
                            <select name="capteurs" class="filter-select">
                                <option value="tous" <?= $filterCapteurs == 'tous' ? 'selected' : '' ?>>Toutes les catégories</option>
                                <option value="avec" <?= $filterCapteurs == 'avec' ? 'selected' : '' ?>>✓ Avec capteurs</option>
                                <option value="sans" <?= $filterCapteurs == 'sans' ? 'selected' : '' ?>>○ Sans capteurs</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fas fa-sort"></i> Trier par
                            </label>
                            <select name="orderBy" class="filter-select">
                                <option value="id" <?= $orderBy == 'id' ? 'selected' : '' ?>>ID</option>
                                <option value="nom" <?= $orderBy == 'nom' ? 'selected' : '' ?>>Nom</option>
                                <option value="capteurs" <?= $orderBy == 'capteurs' ? 'selected' : '' ?>>Nombre de capteurs</option>
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
                        <a href="listCategorie.php" class="btn-reset-filters">
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
                        <?php if ($filterCapteurs !== 'tous'): ?>
                            <span class="filter-tag">
                                <i class="fas fa-microchip"></i> <?= $filterCapteurs == 'avec' ? 'Avec capteurs' : 'Sans capteurs' ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <a href="listCategorie.php" class="btn btn-secondary" style="margin: 0;">
                        <i class="fas fa-times"></i> Effacer tous
                    </a>
                </div>
            <?php endif; ?>

            <!-- Category Management Section -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Gestion des Catégories</h2>
                    <div class="card-actions">
                        <a href="listCapteur.php" class="btn btn-secondary">
                            <i class="fas fa-microchip"></i>
                            <span>Tous les capteurs</span>
                        </a>
                        <a href="addCategorie.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Ajouter Catégorie</span>
                        </a>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="table-container">
                    <?php if (empty($listeCategories)): ?>
                        <div class="no-data">
                            <i class="fas fa-inbox fa-3x"></i>
                            <p>
                                <?php if ($activeFilters > 0): ?>
                                    Aucune catégorie ne correspond aux critères de recherche.
                                <?php else: ?>
                                    Aucune catégorie trouvée.
                                <?php endif; ?>
                            </p>
                            <?php if ($activeFilters > 0): ?>
                                <a href="listCategorie.php" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Voir toutes les catégories
                                </a>
                            <?php else: ?>
                                <a href="addCategorie.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Ajouter une catégorie
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom de la Catégorie</th>
                                    <th>Nombre de Capteurs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $index = 1;
                                foreach ($listeCategories as $categorie):
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
                                    
                                    $icon = 'fa-layer-group';
                                    foreach ($categoryIcons as $key => $iconClass) {
                                        if (stripos($categorie['nom_categorie'], $key) !== false) {
                                            $icon = $iconClass;
                                            break;
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?= $index ?></td>
                                    <td>
                                        <div class="category-name">
                                            <i class="fas <?= $icon ?> category-icon"></i>
                                            <strong><?= htmlspecialchars($categorie['nom_categorie']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $categorie['nombre_capteurs'] == 0 ? 'badge-warning' : 'badge-info' ?>">
                                            <i class="fas fa-microchip"></i>
                                            <?= $categorie['nombre_capteurs'] ?> capteur<?= $categorie['nombre_capteurs'] > 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" 
                                                    title="Voir les capteurs"
                                                    onclick="window.location.href='listCapteur.php?categorie=<?= $categorie['id_categorie'] ?>'">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button class="btn-action btn-edit" 
                                                    title="Modifier"
                                                    onclick="window.location.href='updateCategorie.php?id=<?= $categorie['id_categorie'] ?>'">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <button class="btn-action btn-delete" 
                                                    title="Supprimer"
                                                    onclick="confirmDelete(<?= $categorie['id_categorie'] ?>, '<?= htmlspecialchars($categorie['nom_categorie']) ?>', <?= $categorie['nombre_capteurs'] ?>)">
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
    function confirmDelete(id, name, nbCapteurs) {
        let message = `⚠️ Êtes-vous sûr de vouloir supprimer la catégorie "${name}" ?`;
        if (nbCapteurs > 0) {
            message += `\n\nAttention : ${nbCapteurs} capteur(s) sont associés à cette catégorie !`;
        }
        
        if (confirm(message)) {
            window.location.href = 'deleteCategorie.php?id=' + id;
        }
    }

    function toggleFilters() {
        const panel = document.getElementById('filtersPanel');
        const icon = document.getElementById('toggleFiltersIcon');
        const text = document.getElementById('toggleFiltersText');
        
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
    </script>
</body>
</html>