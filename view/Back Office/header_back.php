<?php
session_start();

// Vérifier si l'utilisateur est connecté (optionnel - à adapter selon votre système d'authentification)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login_admin.php');
    exit;
}

// Définir le titre de la page
$page_title = isset($page_title) ? $page_title . ' - SmartGarden Admin' : 'SmartGarden Admin';

// Récupérer les notifications (si existantes)
$notifications = isset($_SESSION['notifications']) ? $_SESSION['notifications'] : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, #1e7e34 100%);
            color: white;
            min-height: 100vh;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .sidebar-brand .logo-icon {
            background: white;
            color: var(--primary-color);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 10px;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.15);
            font-weight: 600;
        }
        
        .nav-link i {
            width: 24px;
            margin-right: 10px;
            text-align: center;
        }
        
        /* Main Content Area */
        .main-content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }
        
        /* Header Styles */
        .top-header {
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
        }
        
        .header-title p {
            color: var(--secondary-color);
            margin: 5px 0 0 0;
            font-size: 0.9rem;
        }
        
        /* User Profile Dropdown */
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .user-info h6 {
            margin: 0;
            font-weight: 600;
        }
        
        .user-info small {
            color: var(--secondary-color);
        }
        
        /* Card Styles */
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        /* Table Styles */
        .data-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .table-header {
            background: var(--light-color);
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        
        /* Badge Styles */
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-approved {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Button Styles */
        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 2px;
        }
        
        /* Alert Styles */
        .custom-alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .sidebar-brand h3 span {
                display: none;
            }
            
            .main-content {
                margin-left: 80px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .top-header {
                flex-direction: column;
                text-align: center;
            }
            
            .user-profile {
                margin-top: 15px;
            }
        }
        
        /* Animation for notifications */
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .notification-toast {
            animation: slideIn 0.3s ease-out;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar col-md-3 col-lg-2 d-none d-md-block">
        <div class="sidebar-brand d-flex align-items-center">
            <div class="logo-icon">
                <i class="fas fa-leaf"></i>
            </div>
            <h3><span>SmartGarden</span></h3>
        </div>
        
        <nav class="nav flex-column mt-4">
            <a href="backoffice.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'backoffice.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="backoffice.php?section=publications" class="nav-link <?php echo isset($_GET['section']) && $_GET['section'] == 'publications' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Publications</span>
            </a>
            <a href="backoffice.php?section=commentaires" class="nav-link <?php echo isset($_GET['section']) && $_GET['section'] == 'commentaires' ? 'active' : ''; ?>">
                <i class="fas fa-comments"></i>
                <span>Commentaires</span>
            </a>
            <a href="backoffice.php?section=utilisateurs" class="nav-link <?php echo isset($_GET['section']) && $_GET['section'] == 'utilisateurs' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Utilisateurs</span>
            </a>
            <a href="backoffice.php?section=statistiques" class="nav-link <?php echo isset($_GET['section']) && $_GET['section'] == 'statistiques' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
            </a>
            
            <div class="mt-5 pt-5">
                <hr class="bg-light">
                <a href="logout_admin.php" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div class="header-title">
                <h1>
                    <?php 
                    $title = 'Tableau de bord';
                    if (isset($_GET['section'])) {
                        switch($_GET['section']) {
                            case 'publications': $title = 'Gestion des publications'; break;
                            case 'commentaires': $title = 'Gestion des commentaires'; break;
                            case 'utilisateurs': $title = 'Gestion des utilisateurs'; break;
                            case 'statistiques': $title = 'Statistiques'; break;
                        }
                    }
                    echo $title;
                    ?>
                </h1>
                <p><?php echo date('l j F Y'); ?> | <span id="current-time"></span></p>
            </div>
            
            <div class="user-profile dropdown">
                <div class="d-flex align-items-center" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        <?php 
                        $initials = isset($_SESSION['admin_name']) ? strtoupper(substr($_SESSION['admin_name'], 0, 2)) : 'AD';
                        echo $initials;
                        ?>
                    </div>
                    <div class="user-info">
                        <h6><?php echo $_SESSION['admin_name'] ?? 'Administrateur'; ?></h6>
                        <small>Administrateur système</small>
                    </div>
                    <i class="fas fa-chevron-down ms-2"></i>
                </div>
                
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mon profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout_admin.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                </ul>
            </div>
        </div>

        <!-- Notifications -->
        <?php if (!empty($notifications)): ?>
            <div class="notification-container">
                <?php foreach ($notifications as $notification): ?>
                    <div class="alert alert-<?php echo $notification['type']; ?> custom-alert alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?php echo $notification['icon'] ?? 'info-circle'; ?> me-2"></i>
                        <?php echo $notification['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['notifications']); ?>
            </div>
        <?php endif; ?>

        <!-- Display session messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'success'; ?> custom-alert alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php 
                    switch($_SESSION['message_type'] ?? 'success') {
                        case 'danger': echo 'exclamation-triangle'; break;
                        case 'warning': echo 'exclamation-circle'; break;
                        case 'info': echo 'info-circle'; break;
                        default: echo 'check-circle';
                    }
                ?> me-2"></i>
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Page Content will be included here -->
        <div class="page-content">