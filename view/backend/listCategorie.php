<?php
session_start();
include '../../controller/CategorieC.php';

$categorieC = new CategorieC();
// Utilisation de la méthode avec JOIN pour avoir le nombre de capteurs
$listeCategories = $categorieC->showCategorieWithDetails();

// Gestion des messages de session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="../public/assets/images/logo.jpg" />
    <title>Catégories - SmartGarden AI</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="../public/assets/css/boxicons.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            padding-top: 70px;
        }

        /* ========== NAVBAR STYLES ========== */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 30px;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .hamburger-menu {
            font-size: 24px;
            color: #4a5568;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .hamburger-menu:hover {
            background: #f0f4f8;
            color: #2d3748;
        }

        .navbar-search {
            flex: 1;
            max-width: 500px;
        }

        .navbar-search input {
            width: 100%;
            padding: 10px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .navbar-search input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-icon {
            position: relative;
            font-size: 20px;
            color: #4a5568;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .nav-icon:hover {
            background: #f0f4f8;
            color: #3b82f6;
        }

        .nav-icon .badge-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid white;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .navbar-user:hover {
            background: #f0f4f8;
        }

        .navbar-user img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid #3b82f6;
        }

        .navbar-user-name {
            font-size: 15px;
            font-weight: 600;
            color: #2d3748;
        }

        .navbar-user i {
            font-size: 14px;
            color: #718096;
        }

        /* ========== SIDEBAR STYLES ========== */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            width: 260px;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
            z-index: 999;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .sidebar-logo img {
            width: 36px;
            height: 36px;
        }

        .sidebar-logo h3 {
            font-size: 20px;
            font-weight: 700;
            color: #10b981;
            margin: 0;
        }

        .sidebar-user {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .sidebar-user img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #3b82f6;
            position: relative;
        }

        .sidebar-user-name {
            font-size: 16px;
            font-weight: 700;
            color: #1a202c;
        }

        .sidebar-user-role {
            font-size: 14px;
            color: #718096;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            color: #4a5568;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu-item:hover {
            background: #f0f9ff;
            color: #3b82f6;
            border-left-color: #3b82f6;
        }

        .sidebar-menu-item.active {
            background: #eff6ff;
            color: #10b981;
            border-left-color: #10b981;
            font-weight: 600;
        }

        .sidebar-menu-item i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        /* ========== CONTENT AREA ========== */
        .content {
            margin-left: 260px;
            padding: 30px;
            min-height: calc(100vh - 70px);
            transition: all 0.3s;
        }

        .main-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h1 i {
            color: #10b981;
        }

        .header-divider {
            height: 3px;
            background: linear-gradient(90deg, #10b981 0%, #3b82f6 100%);
            margin-bottom: 30px;
            border-radius: 2px;
        }

        .alert {
            padding: 16px 20px;
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
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #065f46;
            border: 1px solid #bbf7d0;
        }

        .alert i {
            font-size: 20px;
        }

        .actions {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 11px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-edit:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .search-box-wrapper {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-box {
            width: 100%;
            padding: 11px 45px 11px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            background: #f8fafc;
        }

        .search-box:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
        }

        /* Table Styling */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8fafc;
        }

        th {
            color: #475569;
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        th i {
            margin-right: 8px;
            font-size: 16px;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 16px 20px;
            font-size: 14px;
            color: #334155;
        }

        td i.category-icon {
            margin-right: 8px;
            color: #10b981;
            font-size: 20px;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
        }

        .no-data p {
            color: #64748b;
            font-size: 16px;
            font-weight: 500;
            margin: 0;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .badge {
            background: #3b82f6;
            color: white;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge i {
            font-size: 14px;
        }

        .badge.zero {
            background: #94a3b8;
        }

        .stats-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #dbeafe 100%);
            border: 1px solid #d1fae5;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
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
            color: #10b981;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #065f46;
        }

        .stat-label {
            font-size: 13px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Sidebar Toggle */
        .sidebar-toggle {
            position: fixed;
            top: 80px;
            left: 10px;
            z-index: 1001;
            background: #10b981;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
            display: none;
            align-items: center;
            justify-content: center;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                left: -260px;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: flex;
            }

            .navbar-search {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
            }

            h1 {
                font-size: 22px;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box-wrapper {
                max-width: 100%;
            }

            .navbar-user-name {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- ========== NAVBAR TOP ========== -->
    <nav class="top-navbar">
        <div class="navbar-left">
            <div class="hamburger-menu" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </div>
            <div class="navbar-search">
                <input type="search" placeholder="Search">
            </div>
        </div>
        <div class="navbar-right">
            <div class="nav-icon">
                <i class="far fa-envelope"></i>
                <span class="badge-dot"></span>
            </div>
            <div class="nav-icon">
                <i class="far fa-bell"></i>
                <span class="badge-dot"></span>
            </div>
            <div class="navbar-user">
                <img src="../public/assets/images/users/user-4.jpg" alt="User">
                <span class="navbar-user-name">John Doe</span>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </nav>

    <!-- Sidebar Toggle Button (Mobile) -->
    <button class="sidebar-toggle" id="sidebarToggleMobile">
        <i class="fas fa-bars"></i>
    </button>

    <!-- ========== SIDEBAR ========== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="../public/assets/images/logo.jpg" alt="Logo">
                <h3>SmartGarden</h3>
            </div>
            <div class="sidebar-user">
                <img src="../public/assets/images/users/user-4.jpg" alt="User">
                <div class="sidebar-user-name">Jhon Doe</div>
                <div class="sidebar-user-role">Admin</div>
            </div>
        </div>
        <nav class="sidebar-menu">
            <a href="publications.php" class="sidebar-menu-item">
                <i class="fas fa-file-alt"></i>
                <span>Publication</span>
            </a>
            <a href="plantes.php" class="sidebar-menu-item">
                <i class="fas fa-leaf"></i>
                <span>Plantes</span>
            </a>
            <a href="evenements.php" class="sidebar-menu-item">
                <i class="fas fa-calendar"></i>
                <span>Evenements</span>
            </a>
            <a href="utilisateur.php" class="sidebar-menu-item">
                <i class="fas fa-user"></i>
                <span>Utilisateur</span>
            </a>
            <a href="listCapteur.php" class="sidebar-menu-item active">
                <i class="fas fa-microchip"></i>
                <span>Capteurs</span>
            </a>
        </nav>
    </aside>

    <!-- ========== CONTENT ========== -->
    <div class="content">
        <div class="main-container">
            <h1>
                <i class='bx bx-category'></i>
                Gestion des Catégories
            </h1>
            <div class="header-divider"></div>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success_message) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error_message) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($listeCategories)): ?>
                <div class="stats-card">
                    <div class="stat-item">
                        <i class='bx bx-category-alt'></i>
                        <div class="stat-value"><?= count($listeCategories) ?></div>
                        <div class="stat-label">Catégories</div>
                    </div>
                    <div class="stat-item">
                        <i class='bx bx-chip'></i>
                        <div class="stat-value"><?= array_sum(array_column($listeCategories, 'nombre_capteurs')) ?></div>
                        <div class="stat-label">Capteurs Total</div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="actions">
                <div class="search-box-wrapper">
                    <input type="text" id="searchBox" class="search-box" placeholder="🔍 Rechercher une catégorie...">
                    <i class='bx bx-search search-icon'></i>
                </div>
                <a href="addCategorie.php" class="btn btn-primary">
                    <i class='bx bx-plus-circle'></i> Ajouter une catégorie
                </a>
            </div>

            <?php if (empty($listeCategories)): ?>
                <div class="no-data">
                    <p>📊 Aucune catégorie trouvée. Commencez par en ajouter une !</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table id="categorieTable">
                        <thead>
                            <tr>
                                <th><i class='bx bx-hash'></i>ID</th>
                                <th><i class='bx bx-category'></i>Nom de la Catégorie</th>
                                <th><i class='bx bx-chip'></i>Nombre de Capteurs</th>
                                <th><i class='bx bx-cog'></i>Actions</th>
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
                                            <a href="updateCategorie.php?id=<?= $categorie['id_categorie'] ?>" class="btn btn-edit">
                                                <i class='bx bx-edit-alt'></i> Modifier
                                            </a>
                                            <a href="deleteCategorie.php?id=<?= $categorie['id_categorie'] ?>" 
                                               class="btn btn-delete" 
                                               onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette catégorie ?\n<?= $categorie['nombre_capteurs'] > 0 ? 'Attention : ' . $categorie['nombre_capteurs'] . ' capteur(s) sont associés à cette catégorie !' : '' ?>')">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function(e) {
            document.getElementById('sidebar').classList.toggle('show');
        });

        document.getElementById('sidebarToggleMobile').addEventListener('click', function(e) {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside (Mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const toggleMobile = document.getElementById('sidebarToggleMobile');
            
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && 
                    !toggleBtn.contains(event.target) && 
                    !toggleMobile.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Fonction de recherche
        document.getElementById('searchBox').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let table = document.getElementById('categorieTable');
            let tr = table.getElementsByTagName('tr');
            let visibleCount = 0;

            for (let i = 1; i < tr.length; i++) {
                let td = tr[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < td.length; j++) {
                    if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    tr[i].style.display = '';
                    visibleCount++;
                } else {
                    tr[i].style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>