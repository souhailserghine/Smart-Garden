<?php
require_once 'check_session.php';

require_once 'get_users.php';

$controller = new UtilisateurC();
$totalUsers = $controller->compterUtilisateurs();
$activeUsers = $controller->compterParStatut('actif');
$inactiveUsers = $controller->compterParStatut('inactif');
$newToday = $controller->compterNouveauxAujourdhui();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="img/logo-16x16.png" />
    <title>SmartGarden</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS (for content grid) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/capteur.css" rel="stylesheet">
    
    <style>
        /* Ensure Bootstrap grid works properly with modern design */
        .container-fluid {
            width: 100%;
            max-width: 100%;
        }
        .row {
            margin-right: -12px;
            margin-left: -12px;
        }
        .row > * {
            padding-right: 12px;
            padding-left: 12px;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3 style="font-size: 1.2rem;padding-left: 20px; color: #2ecc71;">
                    <img src="img/logo-64x64.png" alt="Logo" style="width: 32px; height: 32px; vertical-align: middle; margin-right: 10px;">SmartGarden
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
                <a href="utilisateurs.php" class="menu-item active">
                    <i class="fas fa-user"></i>
                    <span>Utilisateur</span>
                </a>
                <a href="listCategorie.php" class="menu-item">
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

            <!-- Page Content -->
            <div class="container-fluid" style="padding: 30px;">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-users fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Utilisateurs</p>
                                <h6 class="mb-0"><?php echo $totalUsers; ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-user-plus fa-3x text-success"></i>
                            <div class="ms-3">
                                <p class="mb-2">Nouveaux (Aujourd'hui)</p>
                                <h6 class="mb-0"><?php echo $newToday; ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-user-check fa-3x text-info"></i>
                            <div class="ms-3">
                                <p class="mb-2">Utilisateurs Actifs</p>
                                <h6 class="mb-0"><?php echo $activeUsers; ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-user-times fa-3x text-warning"></i>
                            <div class="ms-3">
                                <p class="mb-2">Utilisateurs Inactifs</p>
                                <h6 class="mb-0"><?php echo $inactiveUsers; ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Gestion des Utilisateurs</h6>
                        <div>
                            <a href="exporter_excel.php" class="btn btn-success me-2">
                                <i class="fa fa-file-excel me-2"></i>Exporter Excel
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fa fa-plus me-2"></i>Ajouter Utilisateur
                            </button>
                        </div>
                    </div>
                    
                    <form method="GET" action="utilisateurs.php" class="row mb-4">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="searchNom" placeholder="Rechercher par nom..." value="<?php echo $searchNom; ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="email" class="form-control" name="searchEmail" placeholder="Rechercher par email..." value="<?php echo $searchEmail; ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="searchLocalisation" placeholder="Localisation..." value="<?php echo $searchLocalisation; ?>">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="filterStatut">
                                <option value="">Tous les statuts</option>
                                <option value="actif" <?php if($filterStatut == 'actif'){ echo 'selected'; } ?>>Actif</option>
                                <option value="inactif" <?php if($filterStatut == 'inactif'){ echo 'selected'; } ?>>Inactif</option>
                                <option value="bloque" <?php if($filterStatut == 'bloque'){ echo 'selected'; } ?>>Bloqué</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-search me-2"></i>Rechercher
                            </button>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">#</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Localisation</th>
                                    <th scope="col">Date Inscription</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (count($users) == 0) {
                                    echo '<tr><td colspan="7" class="text-center">Aucun utilisateur trouvé</td></tr>';
                                } else {
                                    $index = 1;
                                    foreach ($users as $user) {
                                        echo '<tr>';
                                        echo '<td>' . $index . '</td>';
                                        echo '<td>' . $user['nom'] . '</td>';
                                        echo '<td>' . $user['email'] . '</td>';
                                        
                                        if ($user['localisation']) {
                                            echo '<td>' . $user['localisation'] . '</td>';
                                        } else {
                                            echo '<td>-</td>';
                                        }
                                        
                                        echo '<td>' . date('d M Y', strtotime($user['dateInscription'])) . '</td>';
                                        
                                        if ($user['statut'] == 'bloque') {
                                            echo '<td><span class="badge bg-danger">Bloqué</span></td>';
                                        } else if ($user['statut'] == 'actif') {
                                            echo '<td><span class="badge bg-success">Actif</span></td>';
                                        } else {
                                            echo '<td><span class="badge bg-warning">Inactif</span></td>';
                                        }
                                        
                                        echo '<td>';
                                        echo '<a href="utilisateurs.php?view=' . $user['idUtilisateur'] . '" class="btn btn-sm btn-info" title="Voir détails">';
                                        echo '<i class="fa fa-eye"></i></a> ';
                                        
                                        echo '<a href="utilisateurs.php?edit=' . $user['idUtilisateur'] . '" class="btn btn-sm btn-warning" title="Modifier">';
                                        echo '<i class="fa fa-edit"></i></a> ';
                                        
                                        echo '<a href="utilisateurs.php?historique=' . $user['idUtilisateur'] . '" class="btn btn-sm btn-primary" title="Historique">';
                                        echo '<i class="fa fa-history"></i></a> ';
                                        
                                        echo '<form method="POST" action="toggle_user_status.php" style="display:inline;">';
                                        echo '<input type="hidden" name="id" value="' . $user['idUtilisateur'] . '">';
                                        
                                        if ($user['statut'] == 'bloque') {
                                            echo '<input type="hidden" name="statut" value="actif">';
                                            echo '<button type="submit" class="btn btn-sm btn-success" title="Débloquer" onclick="return confirm(\'Débloquer cet utilisateur?\');">';
                                            echo '<i class="fa fa-unlock"></i></button> ';
                                        } else {
                                            echo '<input type="hidden" name="statut" value="bloque">';
                                            echo '<button type="submit" class="btn btn-sm btn-secondary" title="Bloquer" onclick="return confirm(\'Bloquer cet utilisateur?\');">';
                                            echo '<i class="fa fa-ban"></i></button> ';
                                        }
                                        echo '</form>';
                                        
                                        echo '<form method="POST" action="delete_user.php" style="display:inline;">';
                                        echo '<input type="hidden" name="id" value="' . $user['idUtilisateur'] . '">';
                                        echo '<button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm(\'Supprimer cet utilisateur?\');">';
                                        echo '<i class="fa fa-trash"></i></button>';
                                        echo '</form>';
                                        
                                        echo '</td>';
                                        echo '</tr>';
                                        $index++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un Utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="add_user.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de Passe</label>
                            <input type="password" class="form-control" name="motDePasse">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Localisation</label>
                            <input type="text" class="form-control" name="localisation">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="statut">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                                <option value="bloque">Bloqué</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($editUser): ?>
    <div class="modal fade show" id="editUserModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'Utilisateur</h5>
                    <a href="utilisateurs.php" class="btn-close"></a>
                </div>
                <form method="POST" action="edit_user.php">
                    <input type="hidden" name="id" value="<?php echo $editUser['idUtilisateur']; ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom" value="<?php echo $editUser['nom']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo $editUser['email']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Localisation</label>
                            <input type="text" class="form-control" name="localisation" value="<?php echo $editUser['localisation']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="statut">
                                <option value="actif" <?php if($editUser['statut'] == 'actif'){ echo 'selected'; } ?>>Actif</option>
                                <option value="inactif" <?php if($editUser['statut'] == 'inactif'){ echo 'selected'; } ?>>Inactif</option>
                                <option value="bloque" <?php if($editUser['statut'] == 'bloque'){ echo 'selected'; } ?>>Bloqué</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="utilisateurs.php" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($viewUser): ?>
    <div class="modal fade show" id="viewUserModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de l'Utilisateur</h5>
                    <a href="utilisateurs.php" class="btn-close"></a>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <?php echo $viewUser['idUtilisateur']; ?></p>
                    <p><strong>Nom:</strong> <?php echo $viewUser['nom']; ?></p>
                    <p><strong>Email:</strong> <?php echo $viewUser['email']; ?></p>
                    <p><strong>Localisation:</strong> <?php if ($viewUser['localisation']) { echo $viewUser['localisation']; } else { echo '-'; } ?></p>
                    <p><strong>Date d'inscription:</strong> <?php echo date('d M Y', strtotime($viewUser['dateInscription'])); ?></p>
                    <p><strong>Statut:</strong> 
                        <?php 
                        if ($viewUser['statut'] == 'bloque') {
                            echo '<span class="badge bg-danger">Bloqué</span>';
                        } else if ($viewUser['statut'] == 'actif') {
                            echo '<span class="badge bg-success">Actif</span>';
                        } else {
                            echo '<span class="badge bg-warning">Inactif</span>';
                        }
                        ?>
                    </p>
                </div>
                <div class="modal-footer">
                    <a href="utilisateurs.php" class="btn btn-secondary">Fermer</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($historiqueUser): ?>
    <div class="modal fade show" id="historiqueModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title">Historique de <?php echo $historiqueUser['nom']; ?></h5>
                    <a href="utilisateurs.php" class="btn-close"></a>
                </div>
                <div class="modal-body">
                    <?php if (count($historiques) == 0): ?>
                        <p class="text-center text-muted">Aucun historique trouvé pour cet utilisateur.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="text-dark">
                                        <th>ID</th>
                                        <th>Date Connexion</th>
                                        <th>Date Déconnexion</th>
                                        <th>Tâche</th>
                                        <th>Durée (min)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historiques as $hist): ?>
                                    <tr>
                                        <td><?php echo $hist['idHistorique']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($hist['dateConnexion'])); ?></td>
                                        <td>
                                            <?php 
                                            if ($hist['dateDeconnexion'] == '0000-00-00 00:00:00') {
                                                echo '<span class="badge bg-success">Session active</span>';
                                            } else {
                                                echo date('d/m/Y H:i', strtotime($hist['dateDeconnexion']));
                                            }
                                            ?>
                                        </td>
                                        <td><span class="badge bg-info"><?php echo $hist['tache']; ?></span></td>
                                        <td><?php echo number_format($hist['duree'], 2); ?></td>
                                        <td>
                                            <a href="utilisateurs.php?editHistorique=<?php echo $hist['idHistorique']; ?>&historique=<?php echo $historiqueUser['idUtilisateur']; ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="delete_historique_action.php" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $hist['idHistorique']; ?>">
                                                <input type="hidden" name="userId" value="<?php echo $historiqueUser['idUtilisateur']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cet historique?');">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <a href="utilisateurs.php" class="btn btn-secondary">Fermer</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($editHistorique): ?>
    <div class="modal fade show" id="editHistoriqueModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content bg-light">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'Historique</h5>
                    <a href="utilisateurs.php?historique=<?php echo $editHistorique['idUtilisateur']; ?>" class="btn-close"></a>
                </div>
                <form method="POST" action="edit_historique_action.php">
                    <input type="hidden" name="id" value="<?php echo $editHistorique['idHistorique']; ?>">
                    <input type="hidden" name="idUtilisateur" value="<?php echo $editHistorique['idUtilisateur']; ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Date Connexion</label>
                            <input type="datetime-local" class="form-control" name="dateConnexion" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($editHistorique['dateConnexion'])); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date Déconnexion</label>
                            <input type="datetime-local" class="form-control" name="dateDeconnexion" 
                                   value="<?php if($editHistorique['dateDeconnexion'] != '0000-00-00 00:00:00'){ echo date('Y-m-d\TH:i', strtotime($editHistorique['dateDeconnexion'])); } ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tâche</label>
                            <input type="text" class="form-control" name="tache" value="<?php echo $editHistorique['tache']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durée (minutes)</label>
                            <input type="number" class="form-control" name="duree" value="<?php echo $editHistorique['duree']; ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="utilisateurs.php?historique=<?php echo $editHistorique['idUtilisateur']; ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/capteur.js"></script>
    <script src="js/add-user-validation.js"></script>
    <script src="js/edit-user-validation.js"></script>
</body>
</html>
