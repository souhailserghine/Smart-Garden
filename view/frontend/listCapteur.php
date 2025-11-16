<?php
include '../../controller/CapteurC.php';

$capteurC = new CapteurC();
// Utilisation de la méthode avec JOIN pour avoir les noms de catégories et plantes
$listeCapteurs = $capteurC->showCapteurWithDetails();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="../public/assets/images/logo.jpg" />
    <title>Capteurs - SmartGarden AI</title>

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../public/assets/css/boxicons.min.css" rel="stylesheet">

    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Heebo', 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e8f5f3 0%, #d4f1f4 100%);
            min-height: 100vh;
            padding-top: 70px;
        }

        /* ========== NAVBAR ORIGINAL STYLES ========== */
        #navbar-main {
            background: linear-gradient(135deg, #ffffff 0%, #f0fffe 100%);
            border-bottom: 3px solid #34d399;
            box-shadow: 0 4px 20px rgba(52, 211, 153, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 6px 0;
        }

        #navbar-main .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }

        /* Logo et Brand */
        #navbar-main .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 14px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);
        }

        #navbar-main .navbar-brand:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        }

        #navbar-main .navbar-brand img {
            border-radius: 50%;
            border: 3px solid #34d399;
            width: 42px;
            height: 42px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(52, 211, 153, 0.3);
            transition: all 0.3s;
        }

        #navbar-main .navbar-brand:hover img {
            transform: rotate(5deg) scale(1.05);
            border-color: #10b981;
        }

        #navbar-main .brand-text {
            font-size: 1.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.5px;
        }

        /* Barre de recherche navbar */
        .navbar-search {
            position: relative;
            margin-left: 20px;
            flex: 1;
            max-width: 400px;
        }

        .navbar-search input {
            width: 100%;
            padding: 8px 40px 8px 16px;
            border: 2px solid #d1fae5;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s;
            background: white;
        }

        .navbar-search input:focus {
            border-color: #34d399;
            box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.1);
            outline: none;
        }

        .navbar-search .search-icon-nav {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #10b981;
            font-size: 18px;
            pointer-events: none;
        }

        /* Espacement entre les éléments navbar */
        #navbar-main .navbar-nav {
            gap: 20px;
        }

        #navbar-main .nav-item {
            margin: 0 4px;
        }

        #navbar-main .nav-link {
            color: #0d9488 !important;
            transition: all 0.3s;
            padding: 6px 10px !important;
            border-radius: 12px;
            position: relative;
        }

        #navbar-main .nav-link:hover {
            color: #10b981 !important;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            transform: translateY(-2px);
        }

        #navbar-main .badge {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 10px;
            font-weight: 700;
            position: absolute;
            top: 3px;
            right: 3px;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        #navbar-main .menu-user-img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 3px solid #34d399;
            object-fit: cover;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(52, 211, 153, 0.3);
            background: white;
            padding: 4px;
        }

        #navbar-main .nav-link:hover .menu-user-img {
            transform: scale(1.1);
            border-color: #10b981;
        }

        /* Icônes navbar avec espacement */
        #navbar-main .nav-link img {
            transition: all 0.3s;
        }

        #navbar-main .nav-link:hover img {
            transform: scale(1.15);
            filter: brightness(1.2);
        }

        .dropdown-menu {
            border: 1px solid #d1fae5;
            box-shadow: 0 5px 20px rgba(16, 185, 129, 0.15);
            border-radius: 12px;
            margin-top: 10px;
        }

        .dropdown-item {
            color: #0d9488;
            transition: all 0.3s;
            padding: 10px 20px;
        }

        .dropdown-item:hover {
            background: linear-gradient(90deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        /* ========== SIDEBAR STYLES ========== */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            width: 250px;
            height: calc(100vh - 70px);
            overflow-y: auto;
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            z-index: 999;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 2px solid #f0f0f0;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .sidebar-header h4 {
            color: #065f46;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .sidebar .navbar-nav {
            padding: 20px 0;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 25px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 15px;
            font-weight: 500;
            border-left: 4px solid transparent;
        }

        .sidebar .nav-link:hover {
            background: linear-gradient(90deg, #f0fdf4 0%, #dcfce7 100%);
            color: #059669;
            padding-left: 30px;
            border-left-color: #10b981;
        }

        .sidebar .nav-link.active {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            color: white;
            padding-left: 30px;
            border-left-color: #047857;
            font-weight: 600;
        }

        .sidebar .nav-link i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            position: fixed;
            top: 80px;
            left: 10px;
            z-index: 1001;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.3s;
            display: none;
        }

        .sidebar-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
        }

        /* ========== CONTENT AREA ========== */
        .content {
            margin-left: 250px;
            padding: 20px;
            min-height: calc(100vh - 70px);
            transition: all 0.3s;
        }

        /* ========== MAIN CONTAINER ========== */
        .main-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.1);
            border: 1px solid #d1fae5;
        }

        h1 {
            background: linear-gradient(135deg, #10b981 0%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            font-size: 2.5em;
            font-weight: 700;
        }

        .header-divider {
            height: 4px;
            background: linear-gradient(90deg, transparent, #34d399, transparent);
            margin-bottom: 30px;
            border-radius: 2px;
        }

        .actions {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .search-box-wrapper {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-box {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid #d1fae5;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-box:focus {
            border-color: #34d399;
            box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.1);
            outline: none;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #10b981;
            font-size: 20px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-edit {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: scale(1.05);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: scale(1.05);
        }

        /* ========== TABLE STYLES ========== */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead tr {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        thead th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
            animation: fadeIn 0.5s ease-in;
        }

        tbody tr:hover {
            background: linear-gradient(90deg, #f0fdf4 0%, #dcfce7 100%);
            transform: translateX(5px);
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.1);
        }

        tbody td {
            padding: 15px;
            font-size: 14px;
            color: #333;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-actif {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactif {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .status-defectueux {
            background: #ffedd5;
            color: #9a3412;
        }

        .plant-badge {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .no-plant {
            color: #999;
            font-style: italic;
        }

        .actions-cell {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .category-icon, .location-icon, .date-icon {
            color: #10b981;
            margin-right: 5px;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 15px;
            margin: 30px 0;
        }

        .no-data p {
            font-size: 16px;
            color: #059669;
            margin: 0;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 992px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            h1 {
                font-size: 1.8em;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box-wrapper {
                max-width: 100%;
            }
        }

        @media (max-width: 576px) {
            body {
                padding-top: 70px;
            }

            .sidebar {
                top: 70px;
                height: calc(100vh - 70px);
            }

            .main-container {
                padding: 20px;
            }

            h1 {
                font-size: 1.5em;
            }
        }
    </style>
</head>

<body>
    <!-- ========== NAVBAR ORIGINAL START ========== -->
    <nav class="navbar navbar-expand-lg navbar-light" id="navbar-main">
        <div class="container">
            <ul class="navbar-nav flex-row align-items-center w-100" id="main_menu">
                <a class="navbar-brand nav-item mr-4" href="index.html">
                    <img src="../public/assets/images/logo.jpg" alt="Logo">
                    <span class="brand-text">Smart Garden</span>
                </a>
                
                <!-- Barre de recherche navbar -->
                <div class="navbar-search">
                    <input type="search" placeholder="Rechercher..." id="navbarSearchInput">
                    <i class='bx bx-search search-icon-nav'></i>
                </div>
                
                <li class="nav-item s-nav dropdown message-drop-li ml-auto">
                    <a href="#" class="nav-link nav-links message-drop" data-toggle="dropdown" role="button">
                        <img src="../public/assets/images/icons/navbar/message.png" alt="messages" style="width: 26px; height: 26px;"> 
                        <span class="badge badge-pill badge-primary">1</span>
                    </a>
                </li>
                <li class="nav-item s-nav dropdown notification">
                    <a href="#" class="nav-link nav-links" data-toggle="dropdown" role="button">
                        <img src="../public/assets/images/icons/navbar/notification.png" alt="notifications" style="width: 26px; height: 26px;"> 
                        <span class="badge badge-pill badge-primary">3</span>
                    </a>
                </li>
                <li class="nav-item s-nav nav-icon dropdown">
                    <a href="#" data-toggle="dropdown" class="nav-link settings-link" id="settings-dropdown">
                        <img src="../public/assets/images/icons/navbar/settings.png" class="nav-settings" alt="settings" style="width: 26px; height: 26px;">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right settings-dropdown shadow-sm">
                        <a class="dropdown-item" href="#"><i class='bx bx-help-circle mr-2'></i> Centre d'aide</a>
                        <a class="dropdown-item" href="#"><i class='bx bx-cog mr-2'></i> Paramètres</a>
                        <a class="dropdown-item logout-btn" href="#"><i class='bx bx-log-out mr-2'></i> Déconnexion</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- ========== NAVBAR ORIGINAL END ========== -->

    <!-- Sidebar Toggle Button (Mobile) -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fa fa-bars"></i>
    </button>

    <div class="container-fluid position-relative d-flex p-0">
        <!-- ========== SIDEBAR START ========== -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h4>Home</h4>
            </div>
            <nav class="navbar navbar-light">
                <div class="navbar-nav w-100">
                    <a href="profile.html" class="nav-item nav-link">
                        <i class="fa fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="index.html" class="nav-item nav-link">
                        <i class="fa fa-file-alt"></i>
                        <span>Publications</span>
                    </a>
                    <a href="plantes.html" class="nav-item nav-link">
                        <i class="fa fa-leaf"></i>
                        <span>Plantes</span>
                    </a>
                    <a href="evenements.html" class="nav-item nav-link">
                        <i class="fa fa-calendar"></i>
                        <span>Evenements</span>
                    </a>
                    <a href="listCapteur.php" class="nav-item nav-link active">
                        <i class="fa fa-microchip"></i>
                        <span>Capteurs</span>
                    </a>
                </div>
            </nav>
        </div>
        <!-- ========== SIDEBAR END ========== -->

        <!-- ========== CONTENT START ========== -->
        <div class="content">
            <!-- Main Content -->
            <div class="main-container">
                <h1><i class='bx bx-chip'></i> Gestion des Capteurs</h1>
                <div class="header-divider"></div>
                
                <div class="actions">
                    <div class="search-box-wrapper">
                        <input type="text" id="searchBox" class="search-box" placeholder="🔍 Rechercher un capteur...">
                        <i class='bx bx-search search-icon'></i>
                    </div>
                    <a href="addCapteur.php" class="btn btn-primary">
                        <i class='bx bx-plus-circle'></i> Ajouter un capteur
                    </a>
                </div>

                <?php if (empty($listeCapteurs)): ?>
                    <div class="no-data">
                        <p>📊 Aucun capteur trouvé. Commencez par en ajouter un !</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table id="capteurTable">
                            <thead>
                                <tr>
                                    <th><i class='bx bx-hash'></i> ID</th>
                                    <th><i class='bx bx-category'></i> Catégorie</th>
                                    <th><i class='bx bx-ruler'></i> Unité</th>
                                    <th><i class='bx bx-radio-circle-marked'></i> État</th>
                                    <th><i class='bx bx-map-pin'></i> Emplacement</th>
                                    <th><i class='bx bx-leaf'></i> Plante</th>
                                    <th><i class='bx bx-calendar'></i> Date Installation</th>
                                    <th><i class='bx bx-cog'></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listeCapteurs as $capteur): ?>
                                    <tr>
                                        <td><strong>#<?= $capteur['id_capteur'] ?></strong></td>
                                        <td>
                                            <?php 
                                            $categoryIcons = [
                                                'Température' => 'bx-thermometer',
                                                'Humidité' => 'bx-droplet',
                                                'Luminosité' => 'bx-sun',
                                                'Pression' => 'bx-wind',
                                                'pH' => 'bx-test-tube'
                                            ];
                                            $categorieName = $capteur['nom_categorie'] ?? 'Non défini';
                                            $icon = $categoryIcons[$categorieName] ?? 'bx-chip';
                                            ?>
                                            <i class='bx <?= $icon ?> category-icon'></i>
                                            <?= htmlspecialchars($categorieName) ?>
                                        </td>
                                        <td><strong><?= htmlspecialchars($capteur['uniteCapteur'] ?? 'N/A') ?></strong></td>
                                        <td>
                                            <?php $etat = $capteur['etatCapteur'] ?? 'inactif'; ?>
                                            <span class="status status-<?= $etat ?>">
                                                <?php
                                                $statusIcons = [
                                                    'actif' => '✓',
                                                    'inactif' => '✕',
                                                    'maintenance' => '⚙',
                                                    'defectueux' => '⚠'
                                                ];
                                                echo $statusIcons[$etat] ?? '•';
                                                ?>
                                                <?= ucfirst($etat) ?>
                                            </span>
                                        </td>
                                        <td><i class='bx bx-map location-icon'></i><?= htmlspecialchars($capteur['emplacement'] ?? 'Non défini') ?></td>
                                        <td>
                                            <?php if (!empty($capteur['nom_plante'])): ?>
                                                <span class="plant-badge">
                                                    <i class='bx bx-leaf'></i> <?= htmlspecialchars($capteur['nom_plante']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="no-plant">Aucune plante</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class='bx bx-time date-icon'></i>
                                            <?= !empty($capteur['dateInstallation']) ? date('d/m/Y', strtotime($capteur['dateInstallation'])) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <div class="actions-cell">
                                                <a href="updateCapteur.php?id=<?= $capteur['id_capteur'] ?>" class="btn btn-edit">
                                                    <i class='bx bx-edit-alt'></i> Modifier
                                                </a>
                                                <a href="deleteCapteur.php?id=<?= $capteur['id_capteur'] ?>" 
                                                   class="btn btn-delete" 
                                                   onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce capteur ?')">
                                                   <i class='bx bx-trash'></i> Supprimer
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
        <!-- ========== CONTENT END ========== -->
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar (Mobile)
        document.getElementById('sidebarToggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside (Mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Fonction de recherche
        document.getElementById('searchBox').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let table = document.getElementById('capteurTable');
            let tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let td = tr[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < td.length; j++) {
                    if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }

                tr[i].style.display = found ? '' : 'none';
            }
        });

        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
            });
        });
    </script>
</body>
</html>