<?php
require_once 'check_session.php';
include '../../controller/categorieC.php';

$categorieC = new CategorieC();
$listeCategories = [];

// Gestion des messages de session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
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

// Compter les filtres actifs
$activeFilters = 0;
if (!empty($searchTerm)) $activeFilters++;
if ($filterCapteurs !== 'tous') $activeFilters++;
?>

<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    <title>SmartGarden</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href="./assets/css/boxicons.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/components.css" rel="stylesheet">
    <link href="./assets/css/media.css" rel="stylesheet">
    <link href="./assets/css/chat.css" rel="stylesheet">
    <link href="https://vjs.zencdn.net/7.4.1/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js" type="text/javascript"></script>
    <script src="./assets/js/load.js" type="text/javascript"></script>
    <style>
        /* Styles personnalisés pour le dashboard */
        .dashboard-content {
            padding: 20px;
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #050505;
            margin-bottom: 10px;
        }

        .header-divider {
            height: 3px;
            background: linear-gradient(90deg, #0866ff 0%, #00a400 100%);
            margin-bottom: 20px;
            border-radius: 2px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: #060;
            border: 1px solid #cfc;
        }

        /* Panneau de filtres */
        .filters-panel {
            background: white;
            border: 1px solid #e4e6eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
            color: #050505;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-badge {
            background: #0866ff;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #65676b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-input, .filter-select {
            padding: 10px 14px;
            border: 1px solid #ced0d4;
            border-radius: 8px;
            font-size: 14px;
            background: #f0f2f5;
            outline: none;
            transition: all 0.3s ease;
        }

        .filter-input:focus, .filter-select:focus {
            background: white;
            border-color: #0866ff;
            box-shadow: 0 0 0 3px rgba(8, 102, 255, 0.1);
        }

        .filters-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            padding-top: 15px;
            border-top: 1px solid #f0f2f5;
        }

        .btn-apply-filters {
            background: #0866ff;
            color: white;
            padding: 10px 24px;
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
            background: #0052cc;
            transform: translateY(-1px);
        }

        .btn-reset-filters {
            background: #e4e6eb;
            color: #050505;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-reset-filters:hover {
            background: #d8dadf;
            text-decoration: none;
        }

        .active-filters-banner {
            background: linear-gradient(135deg, #e7f3ff 0%, #bfdbfe 100%);
            border: 2px solid #60a5fa;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .toggle-filters-btn {
            background: white;
            border: 1px solid #e4e6eb;
            color: #65676b;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            width: fit-content;
        }

        .toggle-filters-btn:hover {
            background: #f0f2f5;
            border-color: #0866ff;
            color: #0866ff;
        }

        .dashboard-actions {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: #0866ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #0052cc;
            color: white;
            text-decoration: none;
        }

        .btn-info {
            background: #0866ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-info:hover {
            background: #0052cc;
            color: white;
            text-decoration: none;
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e4e6eb;
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: #f0f2f5;
        }

        thead th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #65676b;
            border-bottom: 1px solid #e4e6eb;
        }

        tbody tr {
            border-bottom: 1px solid #e4e6eb;
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #f0f2f5;
        }

        tbody td {
            padding: 12px 16px;
            font-size: 14px;
            color: #050505;
        }

        td i.category-icon {
            margin-right: 8px;
            color: #0866ff;
            font-size: 20px;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            background: #f0f2f5;
            border-radius: 8px;
            border: 2px dashed #ced0d4;
        }

        .no-data p {
            color: #65676b;
            font-size: 15px;
            margin: 0;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .badge {
            background: #0866ff;
            color: white;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge.zero {
            background: #65676b;
        }

        .stats-card {
            background: linear-gradient(135deg, #e7f3ff 0%, #b3d9ff 100%);
            border: 1px solid #80b3ff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .stat-item i {
            font-size: 32px;
            color: #0866ff;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #050505;
        }

        .stat-label {
            font-size: 13px;
            color: #003a75;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sd-active {
            background-color: #f0f2f5 !important;
        }

        /* Réduction de la taille des flèches bleues dans le sidebar */
        .newsfeed-left-sidebar .badge-pill {
            font-size: 10px;
            padding: 2px 6px;
        }

        .newsfeed-left-sidebar .badge-pill i {
            font-size: 12px;
        }

        /* Réduction de la taille des badges (chiffres) dans le navbar */
        .navbar .badge-pill {
            font-size: 9px;
            padding: 2px 5px;
            min-width: 16px;
            height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body class="newsfeed">
    <div class="container-fluid" id="wrapper">
        <div class="row newsfeed-size">
            <div class="col-md-12 newsfeed-right-side">
                <nav id="navbar-main" class="navbar navbar-expand-lg shadow-sm sticky-top">
                    <div class="w-100 justify-content-md-center">
                        <ul class="nav navbar-nav enable-mobile px-2">
                            <li class="nav-item">
                                <button type="button" class="btn nav-link p-0"><img src="./assets/images/icons/theme/post-image.png" class="f-nav-icon" alt="Quick make post"></button>
                            </li>
                            <li class="nav-item w-100 py-2">
                                <form class="d-inline form-inline w-100 px-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control search-input" placeholder="Search for people, companies, events and more..." aria-label="Search" aria-describedby="search-addon">
                                        <div class="input-group-append">
                                            <button class="btn search-button" type="button"><i class='bx bx-search'></i></button>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <li class="nav-item">
                                <a href="messages.php" class="nav-link nav-icon nav-links message-drop drop-w-tooltip" data-placement="bottom" data-title="Messages">
                                    <img src="./assets/images/icons/navbar/message.png" class="message-dropdown f-nav-icon" alt="navbar icon">
                                </a>
                            </li>
                        </ul>
                        <ul class="navbar-nav mr-5 flex-row" id="main_menu">
                            <a class="navbar-brand nav-item mr-lg-5" href="publications.php"><img src="./assets/images/logo-128x128.png" width="70" height="70" class="mr-3" alt="Logo"></a>
                            <form class="w-30 mx-2 my-auto d-inline form-inline mr-5 dropdown search-form">
                                <div class="input-group" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="searchDropdown">
                                    <input type="text" class="form-control search-input w-75" placeholder="Search for people, companies, events and more..." aria-label="Search" aria-describedby="search-addon">
                                    <div class="input-group-append">
                                        <button class="btn search-button" type="button"><i class='bx bx-search'></i></button>
                                    </div>
                                </div>
                            </form>
                            <li class="nav-item s-nav">
                                <a href="profile.php" class="nav-link nav-links">
                                    <div class="menu-user-image">
                                        <img src="./assets/images/users/user-4.jpg" class="menu-user-img ml-1" alt="Menu Image">
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item s-nav nav-icon dropdown">
                                <a href="settings.php" data-toggle="dropdown" data-placement="bottom" data-title="Settings" class="nav-link settings-link rm-drop-mobile drop-w-tooltip" id="settings-dropdown"><img src="./assets/images/icons/navbar/settings.png" class="nav-settings" alt="navbar icon"></a>
                                <div class="dropdown-menu dropdown-menu-right settings-dropdown shadow-sm" aria-labelledby="settings-dropdown">
                                    <a class="dropdown-item" href="#">
                                        <img src="./assets/images/icons/navbar/help.png" alt="Navbar icon"> Help Center</a>
                                    <a class="dropdown-item d-flex align-items-center dark-mode" onClick="event.stopPropagation();" href="#">
                                        <img src="./assets/images/icons/navbar/moon.png" alt="Navbar icon"> Dark Mode
                                        <button type="button" class="btn btn-lg btn-toggle ml-auto" data-toggle="button" aria-pressed="false" autocomplete="off">
                                            <div class="handle"></div>
                                        </button>
                                    </a>
                                    <a class="dropdown-item" href="settings.php">
                                        <img src="./assets/images/icons/navbar/gear-1.png" alt="Navbar icon"> Settings</a>
                                    <a class="dropdown-item logout-btn" href="logout.php">
                                        <img src="./assets/images/icons/navbar/logout.png" alt="Navbar icon"> Log Out</a>
                                </div>
                            </li>
                            <button type="button" class="btn nav-link" id="menu-toggle"><img src="./assets/images/icons/theme/navs.png" alt="Navbar navs"></button>
                        </ul>
                    </div>
                </nav>
                
                <div class="row newsfeed-right-side-content mt-3">
                    <div class="col-md-3 newsfeed-left-side sticky-top shadow-sm" id="sidebar-wrapper">
                        <div class="card newsfeed-user-card h-100">
                            <ul class="list-group list-group-flush newsfeed-left-sidebar">
                                <li class="list-group-item">
                                    <h6>Home</h6>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="profile.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/newsfeed.png" alt="profile"> Profile</a>
                                    <a href="#" class="newsfeedListicon"><i class='bx bx-dots-horizontal-rounded'></i></a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="publications.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/message.png" alt="publications"> Publications</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="plantes.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/group.png" alt="plantes"> Plantes</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="evenements.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/event.png" alt="evenements"> Evenements</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center sd-active">
                                    <a href="listCategorie.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/saved.png" alt="capteurs"> Capteurs</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9 second-section" id="page-content-wrapper">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h1>
                                    <i class='bx bx-category'></i> Gestion des Catégories
                                </h1>
                                <div class="header-divider"></div>
                            </div>

                            <?php if ($success_message): ?>
                                <div class="alert alert-success">
                                    <i class='bx bx-check-circle'></i>
                                    <span><?= htmlspecialchars($success_message) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($error_message): ?>
                                <div class="alert alert-error">
                                    <i class='bx bx-error-circle'></i>
                                    <span><?= htmlspecialchars($error_message) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($listeCategories)): ?>
                                <div class="stats-card">
                                    <div class="stat-item">
                                        <i class='bx bx-category-alt'></i>
                                        <div class="stat-value"><?= count($listeCategories) ?></div>
                                        <div class="stat-label"><?= $activeFilters > 0 ? 'Résultats' : 'Catégories' ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <i class='bx bx-chip'></i>
                                        <div class="stat-value"><?= array_sum(array_column($listeCategories, 'nombre_capteurs')) ?></div>
                                        <div class="stat-label">Capteurs Total</div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="dashboard-actions">
                                <button class="toggle-filters-btn" onclick="toggleFilters()">
                                    <i class='bx bx-filter'></i>
                                    <span id="toggleFiltersText">
                                        <?= $activeFilters > 0 ? "Filtres ($activeFilters actifs)" : "Afficher les filtres" ?>
                                    </span>
                                    <i class='bx bx-chevron-down' id="toggleFiltersIcon"></i>
                                </button>
                                
                                <a href="listCapteur.php" class="btn-primary">
                                    <i class='bx bx-chip'></i> Tous les capteurs
                                </a>
                            </div>

                            <!-- Panneau de filtres -->
                            <div class="filters-panel" id="filtersPanel" <?= $activeFilters > 0 ? '' : 'style="display:none;"' ?>>
                                <div class="filters-header">
                                    <h3 class="filters-title">
                                        <i class='bx bx-sliders-h'></i> Filtres et Recherche
                                        <?php if ($activeFilters > 0): ?>
                                            <span class="filter-badge"><?= $activeFilters ?></span>
                                        <?php endif; ?>
                                    </h3>
                                </div>

                                <form method="GET" action="listCategorie.php" id="filterForm">
                                    <div class="filters-grid">
                                        <div class="filter-group">
                                            <label class="filter-label"><i class='bx bx-search'></i> Recherche</label>
                                            <input type="text" name="search" class="filter-input" placeholder="Rechercher une catégorie..." value="<?= htmlspecialchars($searchTerm) ?>">
                                        </div>

                                        <div class="filter-group">
                                            <label class="filter-label"><i class='bx bx-chip'></i> Capteurs</label>
                                            <select name="capteurs" class="filter-select">
                                                <option value="tous" <?= $filterCapteurs == 'tous' ? 'selected' : '' ?>>Toutes</option>
                                                <option value="avec" <?= $filterCapteurs == 'avec' ? 'selected' : '' ?>>✓ Avec capteurs</option>
                                                <option value="sans" <?= $filterCapteurs == 'sans' ? 'selected' : '' ?>>✕ Sans capteurs</option>
                                            </select>
                                        </div>

                                        <div class="filter-group">
                                            <label class="filter-label"><i class='bx bx-sort'></i> Trier par</label>
                                            <select name="orderBy" class="filter-select">
                                                <option value="id" <?= $orderBy == 'id' ? 'selected' : '' ?>>ID</option>
                                                <option value="nom" <?= $orderBy == 'nom' ? 'selected' : '' ?>>Nom</option>
                                                <option value="capteurs" <?= $orderBy == 'capteurs' ? 'selected' : '' ?>>Nombre de capteurs</option>
                                            </select>
                                        </div>

                                        <div class="filter-group">
                                            <label class="filter-label"><i class='bx bx-sort-alt-2'></i> Ordre</label>
                                            <select name="orderDir" class="filter-select">
                                                <option value="ASC" <?= $orderDir == 'ASC' ? 'selected' : '' ?>>↑ Croissant</option>
                                                <option value="DESC" <?= $orderDir == 'DESC' ? 'selected' : '' ?>>↓ Décroissant</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="filters-actions">
                                        <button type="submit" class="btn-apply-filters">
                                            <i class='bx bx-check'></i> Appliquer
                                        </button>
                                        <a href="listCategorie.php" class="btn-reset-filters">
                                            <i class='bx bx-redo'></i> Réinitialiser
                                        </a>
                                    </div>
                                </form>
                            </div>

                            <!-- Filtres actifs -->
                            <?php if ($activeFilters > 0): ?>
                                <div class="active-filters-banner">
                                    <span class="active-filters-label">
                                        <i class='bx bx-filter-alt'></i> Filtres actifs:
                                    </span>
                                    <?php if (!empty($searchTerm)): ?>
                                        <span class="filter-tag">
                                            <i class='bx bx-search'></i> "<?= htmlspecialchars($searchTerm) ?>"
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($filterCapteurs !== 'tous'): ?>
                                        <span class="filter-tag">
                                            <i class='bx bx-chip'></i> <?= $filterCapteurs == 'avec' ? 'Avec capteurs' : 'Sans capteurs' ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (empty($listeCategories)): ?>
                                <div class="no-data">
                                    <p>📊 Aucune catégorie ne correspond aux critères.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-wrapper">
                                    <table id="categorieTable">
                                        <thead>
                                            <tr>
                                                <th><i class='bx bx-hash'></i> ID</th>
                                                <th><i class='bx bx-category'></i> Nom de la Catégorie</th>
                                                <th><i class='bx bx-chip'></i> Nombre de Capteurs</th>
                                                <th style="text-align: center;"><i class='bx bx-show'></i> Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($listeCategories as $categorie): ?>
                                                <tr>
                                                    <td><strong>#<?= $categorie['id_categorie'] ?></strong></td>
                                                    <td>
                                                        <?php 
                                                        $categoryIcons = [
                                                            'Température' => 'bx-thermometer',
                                                            'Humidité' => 'bx-droplet',
                                                            'Luminosité' => 'bx-sun',
                                                            'Pression' => 'bx-wind',
                                                            'pH' => 'bx-test-tube',
                                                            'Débit' => 'bx-water',
                                                            'CO2' => 'bx-gas-pump'
                                                        ];
                                                        $categorieName = $categorie['nom_categorie'];
                                                        $icon = 'bx-category-alt';
                                                        foreach ($categoryIcons as $key => $iconClass) {
                                                            if (stripos($categorieName, $key) !== false) {
                                                                $icon = $iconClass;
                                                                break;
                                                            }
                                                        }
                                                        ?>
                                                        <i class='bx <?= $icon ?> category-icon'></i>
                                                        <strong><?= htmlspecialchars($categorieName) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?= $categorie['nombre_capteurs'] == 0 ? 'zero' : '' ?>">
                                                            <i class='bx bx-chip'></i>
                                                            <?= $categorie['nombre_capteurs'] ?> capteur<?= $categorie['nombre_capteurs'] > 1 ? 's' : '' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="actions-cell">
                                                            <a href="listCapteur.php?categorie=<?= $categorie['id_categorie'] ?>" 
                                                               class="btn-info" 
                                                               title="Consulter les capteurs de cette catégorie">
                                                               <i class='bx bx-show'></i> Consulter les capteurs
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core -->
    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script type="text/javascript">
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

        function toggleFilters() {
            const panel = document.getElementById('filtersPanel');
            const icon = document.getElementById('toggleFiltersIcon');
            
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
                icon.classList.remove('bx-chevron-down');
                icon.classList.add('bx-chevron-up');
            } else {
                panel.style.display = 'none';
                icon.classList.remove('bx-chevron-up');
                icon.classList.add('bx-chevron-down');
            }
        }
    </script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/components/components.js"></script>
</body>

</html>