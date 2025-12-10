<?php
session_start();

// Inclure vos fichiers existants
require_once '../../config.php';
require_once '../../model/publication.php';
require_once '../../model/commentaire.php';

try {
    // Connexion à la base de données (adaptez avec vos paramètres)
    $db = new PDO("mysql:host=localhost;dbname=smartgarden", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Gérer les actions de modération
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;
    $type = $_GET['type'] ?? null; // 'publication' ou 'commentaire'
    
    if ($id && $type) {
        switch($action) {
            case 'supprimer':
                if ($type === 'publication') {
                    // Supprimer d'abord les commentaires associés
                    $sqlDeleteComments = "DELETE FROM commentaire WHERE idPublication = ?";
                    $stmtComments = $db->prepare($sqlDeleteComments);
                    $stmtComments->execute([$id]);
                    
                    // Puis supprimer la publication
                    $sql = "DELETE FROM publication WHERE idPublication = ?";
                } else {
                    $sql = "DELETE FROM commentaire WHERE idCommentaire = ?";
                }
                
                $stmt = $db->prepare($sql);
                $stmt->execute([$id]);
                $_SESSION['message'] = "Contenu supprimé avec succès";
                $_SESSION['message_type'] = 'danger';
                header('Location: backoffice.php');
                exit;
                break;
                
            case 'voir_detail':
                if ($type === 'publication') {
                    $sql = "SELECT * FROM publication WHERE idPublication = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$id]);
                    $detail = $stmt->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['detail_content'] = $detail;
                    $_SESSION['detail_type'] = 'publication';
                } else {
                    $sql = "SELECT c.*, p.contenuTexte as publication_contenu 
                           FROM commentaire c 
                           LEFT JOIN publication p ON c.idPublication = p.idPublication 
                           WHERE c.idCommentaire = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$id]);
                    $detail = $stmt->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['detail_content'] = $detail;
                    $_SESSION['detail_type'] = 'commentaire';
                }
                header('Location: backoffice.php?section=detail');
                exit;
                break;
        }
    }
}

// Récupérer les données selon la section
$section = $_GET['section'] ?? 'dashboard';

switch($section) {
    case 'publications':
        $sql = "SELECT * FROM publication ORDER BY datePublication DESC";
        $stmt = $db->query($sql);
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
        
    case 'commentaires':
        $sql = "SELECT c.*, p.contenuTexte as publication_contenu 
               FROM commentaire c 
               LEFT JOIN publication p ON c.idPublication = p.idPublication 
               ORDER BY c.dateCommentaire DESC";
        $stmt = $db->query($sql);
        $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
        
    case 'detail':
        $detail = $_SESSION['detail_content'] ?? null;
        $detail_type = $_SESSION['detail_type'] ?? null;
        break;
        
    default: // dashboard
        // Récupérer les publications en attente
        $sqlPublications = "SELECT * FROM publication WHERE statut_moderation = 'en_attente' ORDER BY datePublication DESC";
        $stmtPublications = $db->query($sqlPublications);
        $publicationsEnAttente = $stmtPublications->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les commentaires en attente
        $sqlCommentaires = "SELECT c.*, p.contenuTexte as publication_contenu 
                           FROM commentaire c 
                           LEFT JOIN publication p ON c.idPublication = p.idPublication 
                           WHERE c.statut_moderation = 'en_attente' 
                           ORDER BY c.dateCommentaire DESC";
        $stmtCommentaires = $db->query($sqlCommentaires);
        $commentairesEnAttente = $stmtCommentaires->fetchAll(PDO::FETCH_ASSOC);
        
        // Statistiques
        $sqlStats = "SELECT 
            (SELECT COUNT(*) FROM publication WHERE statut_moderation = 'en_attente') as publications_attente,
            (SELECT COUNT(*) FROM commentaire WHERE statut_moderation = 'en_attente') as commentaires_attente,
            (SELECT COUNT(*) FROM publication WHERE statut_moderation = 'approuve') as publications_approuvees,
            (SELECT COUNT(*) FROM commentaire WHERE statut_moderation = 'approuve') as commentaires_approuves,
            (SELECT COUNT(*) FROM publication WHERE statut_moderation = 'rejete') as publications_rejetees,
            (SELECT COUNT(*) FROM commentaire WHERE statut_moderation = 'rejete') as commentaires_rejetes";
        $stmtStats = $db->query($sqlStats);
        $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);
        break;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Modération SmartGarden</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        .stat-card {
            border-radius: 10px;
            margin-bottom: 20px;
            transition: transform 0.3s;
            border: none;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .badge-status {
            font-size: 0.8em;
            padding: 5px 10px;
        }
        .action-buttons .btn {
            margin: 2px;
        }
        .table th {
            font-weight: 600;
            color: #2c3e50;
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container-fluid position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center" style="display: none;">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-2 mt-2">
                    <h3 style="font-size: 1.2rem;padding-left: 20px; color: #2ecc71;">
                        <img src="img/logo-64x64.png" alt="Logo" class="me-2" style="width: 32px; height: 32px;">
                        SmartGarden Admin
                    </h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">Jean Dupont</h6>
                        <span>Administrateur</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="backoffice.php" class="nav-item nav-link <?php echo ($section == 'dashboard') ? 'active' : ''; ?>">
                        <i class="fa fa-tachometer-alt me-2"></i>Tableau de bord
                    </a>
                    <a href="backoffice.php?section=publications" class="nav-item nav-link <?php echo ($section == 'publications') ? 'active' : ''; ?>">
                        <i class="fa fa-file-alt me-2"></i>Toutes les publications
                    </a>
                    <a href="backoffice.php?section=commentaires" class="nav-item nav-link <?php echo ($section == 'commentaires') ? 'active' : ''; ?>">
                        <i class="fa fa-comments me-2"></i>Tous les commentaires
                    </a>
                    <hr class="my-3">
                    <a href="index.html" class="nav-item nav-link"><i class="fa fa-home me-2"></i>Retour au site</a>
                    <a href="logout.php" class="nav-item nav-link"><i class="fa fa-sign-out-alt me-2"></i>Déconnexion</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="backoffice.php" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><img src="img/logo-64x64.png" alt="Logo" style="width: 32px; height: 32px;"></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="d-none d-md-flex ms-4">
                    <h4 class="mb-0">
                        <?php 
                        $titles = [
                            'dashboard' => 'Tableau de bord',
                            'publications' => 'Toutes les publications',
                            'commentaires' => 'Tous les commentaires',
                            'detail' => 'Détail du contenu'
                        ];
                        echo $titles[$section] ?? 'Modération';
                        ?>
                    </h4>
                </div>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notifications</span>
                            <?php if (isset($stats) && ($stats['publications_attente'] > 0 || $stats['commentaires_attente'] > 0)): ?>
                                <span class="badge bg-danger rounded-pill"><?php echo ($stats['publications_attente'] + $stats['commentaires_attente']); ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">
                                <h6 class="fw-normal mb-0"><?php echo $stats['publications_attente'] ?? 0; ?> publications en attente</h6>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <h6 class="fw-normal mb-0"><?php echo $stats['commentaires_attente'] ?? 0; ?> commentaires en attente</h6>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="backoffice.php" class="dropdown-item text-center">Voir le tableau de bord</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">Jean Dupont</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">Mon Profil</a>
                            <a href="#" class="dropdown-item">Paramètres</a>
                            <a href="logout.php" class="dropdown-item">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Zone de contenu principale -->
            <div class="container-fluid pt-4 px-4">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'success'; ?> alert-dismissible fade show mb-4">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <?php if ($section == 'dashboard'): ?>
                    <!-- DASHBOARD -->
                    <!-- Statistiques -->
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6 col-xl-4">
                            <div class="stat-card bg-primary rounded d-flex align-items-center justify-content-between p-4">
                                <div>
                                    <h6 class="mb-2">Publications en attente</h6>
                                    <h2 class="mb-0"><?php echo $stats['publications_attente'] ?? 0; ?></h2>
                                </div>
                                <i class="fa fa-file-alt fa-3x text-white opacity-50"></i>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="stat-card bg-warning rounded d-flex align-items-center justify-content-between p-4">
                                <div>
                                    <h6 class="mb-2">Commentaires en attente</h6>
                                    <h2 class="mb-0"><?php echo $stats['commentaires_attente'] ?? 0; ?></h2>
                                </div>
                                <i class="fa fa-comments fa-3x text-white opacity-50"></i>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="stat-card bg-success rounded d-flex align-items-center justify-content-between p-4">
                                <div>
                                    <h6 class="mb-2">Contenu approuvé</h6>
                                    <h2 class="mb-0"><?php echo ($stats['publications_approuvees'] ?? 0) + ($stats['commentaires_approuves'] ?? 0); ?></h2>
                                </div>
                                <i class="fa fa-check-circle fa-3x text-white opacity-50"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Publications en attente -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa fa-file-alt me-2"></i>Publications en attente de modération
                            </h5>
                            <span class="badge bg-warning rounded-pill"><?php echo count($publicationsEnAttente); ?></span>
                        </div>
                        <div class="card-body">
                            <?php if (empty($publicationsEnAttente)): ?>
                                <div class="alert alert-success mb-0">
                                    <i class="fa fa-check-circle me-2"></i>Aucune publication en attente de modération.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Contenu</th>
                                                <th>Auteur</th>
                                                <th>Date</th>
                                                <th>Likes</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($publicationsEnAttente as $pub): ?>
                                            <tr>
                                                <td><strong>#<?php echo $pub['idPublication']; ?></strong></td>
                                                <td>
                                                    <div style="max-width: 300px;">
                                                        <?php echo htmlspecialchars(substr($pub['contenuTexte'], 0, 100)); ?>
                                                        <?php if (strlen($pub['contenuTexte']) > 100): ?>...<?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>Utilisateur #<?php echo $pub['idUtilisateur']; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($pub['datePublication'])); ?></td>
                                                <td><span class="badge bg-info"><?php echo $pub['nbLikes'] ?? 0; ?> likes</span></td>
                                                <td class="action-buttons">
                                                    <a href="backoffice.php?action=voir_detail&id=<?php echo $pub['idPublication']; ?>&type=publication" 
                                                       class="btn btn-info btn-sm" title="Voir détail">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="backoffice.php?action=supprimer&id=<?php echo $pub['idPublication']; ?>&type=publication" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('⚠️ Supprimer définitivement cette publication et ses commentaires ?')" title="Supprimer">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Commentaires en attente -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa fa-comments me-2"></i>Commentaires en attente de modération
                            </h5>
                            <span class="badge bg-warning rounded-pill"><?php echo count($commentairesEnAttente); ?></span>
                        </div>
                        <div class="card-body">
                            <?php if (empty($commentairesEnAttente)): ?>
                                <div class="alert alert-success mb-0">
                                    <i class="fa fa-check-circle me-2"></i>Aucun commentaire en attente de modération.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Commentaire</th>
                                                <th>Publication</th>
                                                <th>Auteur</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($commentairesEnAttente as $com): ?>
                                            <tr>
                                                <td><strong>#<?php echo $com['idCommentaire']; ?></strong></td>
                                                <td>
                                                    <div style="max-width: 250px;">
                                                        <?php echo htmlspecialchars(substr($com['contenuCommentaire'], 0, 80)); ?>
                                                        <?php if (strlen($com['contenuCommentaire']) > 80): ?>...<?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php echo htmlspecialchars(substr($com['publication_contenu'] ?? 'Publication supprimée', 0, 50)); ?>
                                                        <?php if (strlen($com['publication_contenu'] ?? '') > 50): ?>...<?php endif; ?>
                                                    </small>
                                                </td>
                                                <td>Utilisateur #<?php echo $com['idUtilisateur']; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($com['dateCommentaire'])); ?></td>
                                                <td class="action-buttons">
                                                    <a href="backoffice.php?action=voir_detail&id=<?php echo $com['idCommentaire']; ?>&type=commentaire" 
                                                       class="btn btn-info btn-sm" title="Voir détail">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="backoffice.php?action=supprimer&id=<?php echo $com['idCommentaire']; ?>&type=commentaire" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Supprimer définitivement ce commentaire ?')" title="Supprimer">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($section == 'publications'): ?>
                    <!-- TOUTES LES PUBLICATIONS -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>
                            <i class="fa fa-file-alt me-2"></i>Toutes les publications
                        </h4>
                        <a href="backoffice.php" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i>Retour au tableau de bord
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Contenu</th>
                                            <th>Statut</th>
                                            <th>Auteur</th>
                                            <th>Date</th>
                                            <th>Modération</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($publications as $pub): ?>
                                        <tr>
                                            <td><strong>#<?php echo $pub['idPublication']; ?></strong></td>
                                            <td>
                                                <div style="max-width: 250px;">
                                                    <?php echo htmlspecialchars(substr($pub['contenuTexte'], 0, 80)); ?>
                                                    <?php if (strlen($pub['contenuTexte']) > 80): ?>...<?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                $badge_class = [
                                                    'en_attente' => 'bg-warning',
                                                    'approuve' => 'bg-success',
                                                    'rejete' => 'bg-danger'
                                                ][$pub['statut_moderation']] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?> badge-status">
                                                    <?php echo $pub['statut_moderation']; ?>
                                                </span>
                                            </td>
                                            <td>Utilisateur #<?php echo $pub['idUtilisateur']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pub['datePublication'])); ?></td>
                                            <td>
                                                <?php if ($pub['date_moderation']): ?>
                                                    <?php echo date('d/m/Y H:i', strtotime($pub['date_moderation'])); ?>
                                                    <?php if ($pub['raison_moderation']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($pub['raison_moderation'], 0, 50)); ?>...</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Non modéré</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="action-buttons">
                                                <a href="backoffice.php?action=voir_detail&id=<?php echo $pub['idPublication']; ?>&type=publication" 
                                                   class="btn btn-info btn-sm" title="Voir détail">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="backoffice.php?action=supprimer&id=<?php echo $pub['idPublication']; ?>&type=publication" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Supprimer définitivement ?')" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($section == 'commentaires'): ?>
                    <!-- TOUS LES COMMENTAIRES -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>
                            <i class="fa fa-comments me-2"></i>Tous les commentaires
                        </h4>
                        <a href="backoffice.php" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i>Retour au tableau de bord
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Commentaire</th>
                                            <th>Statut</th>
                                            <th>Publication</th>
                                            <th>Auteur</th>
                                            <th>Date</th>
                                            <th>Modération</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($commentaires as $com): ?>
                                        <tr>
                                            <td><strong>#<?php echo $com['idCommentaire']; ?></strong></td>
                                            <td>
                                                <div style="max-width: 200px;">
                                                    <?php echo htmlspecialchars(substr($com['contenuCommentaire'], 0, 60)); ?>
                                                    <?php if (strlen($com['contenuCommentaire']) > 60): ?>...<?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                $badge_class = [
                                                    'en_attente' => 'bg-warning',
                                                    'approuve' => 'bg-success',
                                                    'rejete' => 'bg-danger'
                                                ][$com['statut_moderation']] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?> badge-status">
                                                    <?php echo $com['statut_moderation']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <?php echo htmlspecialchars(substr($com['publication_contenu'] ?? 'Publication supprimée', 0, 40)); ?>
                                                    <?php if (strlen($com['publication_contenu'] ?? '') > 40): ?>...<?php endif; ?>
                                                </small>
                                            </td>
                                            <td>Utilisateur #<?php echo $com['idUtilisateur']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($com['dateCommentaire'])); ?></td>
                                            <td>
                                                <?php if ($com['date_moderation']): ?>
                                                    <?php echo date('d/m/Y H:i', strtotime($com['date_moderation'])); ?>
                                                    <?php if ($com['raison_moderation']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($com['raison_moderation'], 0, 40)); ?>...</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Non modéré</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="action-buttons">
                                                <a href="backoffice.php?action=voir_detail&id=<?php echo $com['idCommentaire']; ?>&type=commentaire" 
                                                   class="btn btn-info btn-sm" title="Voir détail">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="backoffice.php?action=supprimer&id=<?php echo $com['idCommentaire']; ?>&type=commentaire" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Supprimer définitivement ?')" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($section == 'detail' && isset($detail)): ?>
                    <!-- DÉTAIL DU CONTENU -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>
                            <i class="fa fa-eye me-2"></i>Détail du contenu
                        </h4>
                        <a href="backoffice.php" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i>Retour
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <?php if ($detail_type == 'publication'): ?>
                                <h5 class="card-title mb-3">Publication #<?php echo $detail['idPublication']; ?></h5>
                                <div class="mb-3">
                                    <span class="badge bg-info">Utilisateur #<?php echo $detail['idUtilisateur']; ?></span>
                                    <span class="badge bg-secondary ms-2"><?php echo date('d/m/Y à H:i', strtotime($detail['datePublication'])); ?></span>
                                    <span class="badge bg-primary ms-2"><?php echo $detail['nbLikes'] ?? 0; ?> likes</span>
                                </div>
                                
                                <div class="bg-light p-3 rounded mb-3">
                                    <h6>Contenu :</h6>
                                    <p style="white-space: pre-line;"><?php echo htmlspecialchars($detail['contenuTexte']); ?></p>
                                </div>
                                
                                <?php if ($detail['images']): ?>
                                    <div class="mb-3">
                                        <h6>Images :</h6>
                                        <p class="text-muted"><?php echo $detail['images']; ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($detail['videos']): ?>
                                    <div class="mb-3">
                                        <h6>Vidéos :</h6>
                                        <p class="text-muted"><?php echo $detail['videos']; ?></p>
                                    </div>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <h5 class="card-title mb-3">Commentaire #<?php echo $detail['idCommentaire']; ?></h5>
                                <div class="mb-3">
                                    <span class="badge bg-info">Utilisateur #<?php echo $detail['idUtilisateur']; ?></span>
                                    <span class="badge bg-secondary ms-2"><?php echo date('d/m/Y à H:i', strtotime($detail['dateCommentaire'])); ?></span>
                                </div>
                                
                                <div class="bg-light p-3 rounded mb-3">
                                    <h6>Commentaire :</h6>
                                    <p style="white-space: pre-line;"><?php echo htmlspecialchars($detail['contenuCommentaire']); ?></p>
                                </div>
                                
                                <?php if (isset($detail['publication_contenu'])): ?>
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Publication associée :</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><?php echo htmlspecialchars(substr($detail['publication_contenu'], 0, 200)); ?>...</p>
                                            <a href="backoffice.php?action=voir_detail&id=<?php echo $detail['idPublication']; ?>&type=publication" 
                                               class="btn btn-sm btn-outline-primary">
                                                Voir la publication
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <h6>Actions :</h6>
                                <a href="backoffice.php?action=supprimer&id=<?php echo $detail[$detail_type == 'publication' ? 'idPublication' : 'idCommentaire']; ?>&type=<?php echo $detail_type; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Supprimer définitivement ?')">
                                    <i class="fa fa-trash me-1"></i>Supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <?php 
                    // Nettoyer les données de session
                    unset($_SESSION['detail_content'], $_SESSION['detail_type']);
                    ?>
                    
                <?php endif; ?>
            </div>

        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>