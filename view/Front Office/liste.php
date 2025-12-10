<?php
include '../../controller/PublicationC.php';
include '../../controller/CommentaireC.php';


$publicationC = new PublicationC();
$commentaireC = new CommentaireC();

$publications = $publicationC->listePublications();

session_start();
$idUtilisateur = isset($_SESSION['idUtilisateur']) ? $_SESSION['idUtilisateur'] : 1;
$postsLiked = isset($_SESSION['postsLiked']) ? $_SESSION['postsLiked'] : [];

// Récupérer le nombre de commentaires pour chaque publication
$commentCounts = [];
$allComments = [];
$allMedias = []; // AJOUT: Tableau pour stocker les médias de chaque publication

foreach ($publications as $pub) {
    $commentCounts[$pub['idPublication']] = $commentaireC->getNombreCommentaires($pub['idPublication']);
    $allComments[$pub['idPublication']] = $commentaireC->getCommentairesByPublication($pub['idPublication']);
    
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    <title>SmartGarden - Liste des Publications</title>

    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@1.9.2/css/boxicons.min.css' rel='stylesheet'>
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/components.css" rel="stylesheet">
    <link href="./assets/css/media.css" rel="stylesheet">
    <link href="https://vjs.zencdn.net/7.4.1/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    <style>
        .header-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .publication-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .publication-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .btn-custom {
            padding: 5px 15px;
            font-size: 14px;
        }
        .like-btn, .comment-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .like-btn:hover, .comment-btn:hover {
            transform: scale(1.1);
            background-color: #f8f9fa;
        }
        .like-btn.liked {
            color: #e74c3c;
            background-color: #ffeaea;
        }
        .like-btn:not(.liked) {
            color: #6c757d;
        }
        .comment-btn {
            color: #6c757d;
        }
        .comment-btn:hover {
            color: #007bff;
            background-color: #e7f3ff;
        }
        .like-count, .comment-count {
            margin-left: 5px;
            font-weight: bold;
        }
        .comment-section {
            margin-top: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: none;
        }
        .comment-form {
            margin-bottom: 15px;
        }
        .comment-list {
            max-height: 200px;
            overflow-y: auto;
        }
        .comment-item {
            padding: 10px;
            margin-bottom: 10px;
            background: white;
            border-radius: 8px;
            border-left: 3px solid #007bff;
        }
        .comment-author {
            font-weight: bold;
            color: #007bff;
        }
        .comment-date {
            font-size: 12px;
            color: #6c757d;
        }
        .comment-text {
            margin-top: 5px;
        }
        .comment-header {
            margin-bottom: 5px;
        }
        .comment-actions {
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        .comment-item:hover .comment-actions {
            opacity: 1;
        }
        .comment-actions .btn {
            padding: 2px 6px;
            margin-left: 3px;
            font-size: 12px;
        }
        .edit-comment-input {
            font-size: 14px;
        }
        
        /* AJOUT: Styles pour les médias des publications */
        .publication-media {
            margin: 15px 0;
        }
        .media-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .media-item {
            flex: 0 0 calc(50% - 5px);
            max-width: 300px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        .media-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
            border-radius: 8px;
        }
        .media-item img:hover {
            transform: scale(1.03);
        }
        .media-item video {
            width: 100%;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
        }
        .media-type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 1;
        }
        .media-count-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 1;
        }
        
        /* Modal pour afficher les médias en grand */
        .media-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.95);
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .modal-content {
            max-width: 90%;
            max-height: 80%;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        .modal-video {
            width: 90%;
            max-height: 80%;
        }
        .close-modal {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 40px;
            cursor: pointer;
            z-index: 10001;
            background: rgba(0,0,0,0.5);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .close-modal:hover {
            background: rgba(255,0,0,0.7);
        }
        .modal-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 40px;
            cursor: pointer;
            background: rgba(0,0,0,0.5);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
        }
        .prev-modal {
            left: 30px;
        }
        .next-modal {
            right: 30px;
        }
        .modal-nav:hover {
            background: rgba(0,123,255,0.7);
        }
        .modal-info {
            color: white;
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
        }
        .modal-counter {
            color: #aaa;
            margin-top: 10px;
        }
    </style>
</head>

<body class="newsfeed">
    <div class="container-fluid" id="wrapper">
        <div class="row newsfeed-size">
            <div class="col-md-12 newsfeed-right-side">

                <!-- ======================= EN-TÊTE COMPLET ======================= -->
                <nav id="navbar-main" class="navbar navbar-expand-lg shadow-sm sticky-top">
                    <ul class="navbar-nav mr-5" id="main_menu">
                        <a class="navbar-brand nav-item mr-lg-5" href="index.html">
                            <img src="./assets/images/logo-128x128.png" width="70" height="70" class="mr-3" alt="Logo">
                        </a>

                        <form class="w-30 mx-2 my-auto d-inline form-inline mr-5" novalidate>
                            <div class="input-group">
                                <input type="text" class="form-control search-input w-75"
                                    placeholder="Search for people, companies, events and more..." aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn search-button" type="button"><i class='bx bx-search'></i></button>
                                </div>
                            </div>
                        </form>

                        <!-- Menu Create -->
                        <li class="nav-item dropdown d-mobile">
                            <a href="#" class="nav-link nav-icon nav-links drop-w-tooltip" data-toggle="dropdown">
                                <img src="./assets/images/icons/navbar/create.png" alt="navbar icon">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right nav-dropdown-menu">
                                <a href="#" class="dropdown-item">
                                    <div class="row">
                                        <div class="col-md-2"><i class='bx bx-group post-option-icon'></i></div>
                                        <div class="col-md-10">
                                            <span class="fs-9">Group</span>
                                            <small class="form-text text-muted">Find people with shared interests</small>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <div class="row">
                                        <div class="col-md-2"><i class='bx bx-calendar post-option-icon'></i></div>
                                        <div class="col-md-10">
                                            <span class="fs-9">Event</span>
                                            <small class="form-text text-muted">Bring people together with an event</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </li>

                        <!-- Messages -->
                        <li class="nav-item dropdown message-drop-li">
                            <a href="#" class="nav-link nav-links message-drop drop-w-tooltip" data-toggle="dropdown">
                                <img src="./assets/images/icons/navbar/message.png" class="message-dropdown" alt="navbar icon">
                                <span class="badge badge-pill badge-primary">1</span>
                            </a>
                        </li>

                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link nav-links drop-w-tooltip" data-toggle="dropdown">
                                <img src="./assets/images/icons/navbar/notification.png" class="notification-bell" alt="navbar icon">
                                <span class="badge badge-pill badge-primary">3</span>
                            </a>
                        </li>

                        <!-- Pages -->
                        <li class="nav-item dropdown d-mobile">
                            <a href="#" class="nav-link nav-links nav-icon drop-w-tooltip" data-toggle="dropdown">
                                <img src="./assets/images/icons/navbar/flag.png" alt="navbar icon">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right nav-drop">
                                <a class="dropdown-item" href="publications.html">Publications</a>
                                <a class="dropdown-item" href="sign-in.html">Sign in</a>
                                <a class="dropdown-item" href="sign-up.html">Sign up</a>
                            </div>
                        </li>

                        <!-- Profil utilisateur -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link nav-links" data-toggle="dropdown">
                                <div class="menu-user-image">
                                    <img src="./assets/images/users/user-4.jpg" class="menu-user-img ml-1" alt="Menu Image">
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right nav-drop">
                                <a class="dropdown-item" href="profile.html"><i class='bx bx-user mr-2'></i> Account</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class='bx bx-undo mr-2'></i> Logout</a>
                            </div>
                        </li>

                        <!-- Settings -->
                        <li class="nav-item nav-icon">
                            <a href="settings.html" class="nav-link"><img src="./assets/images/icons/navbar/settings.png" alt="navbar icon"></a>
                        </li>
                    </ul>
                    <button type="button" class="btn btn-primary mr-3" id="menu-toggle"><i class='bx bx-align-left'></i></button>
                </nav>
                <!-- ======================= FIN EN-TÊTE ======================= -->

                <div class="row newsfeed-right-side-content mt-3">

                    <!-- ======================= SIDEBAR GAUCHE ======================= -->
                    <div class="col-md-3 newsfeed-left-side sticky-top shadow-sm" id="sidebar-wrapper">
                        <div class="card newsfeed-user-card h-100">
                            <ul class="list-group list-group-flush newsfeed-left-sidebar">
                                <li class="list-group-item"><h6>Home</h6></li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="profile.html" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/newsfeed.png"> Profile</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center sd-active">
                                    <a href="publications.html" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/message.png"> Publications</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="plantes.html" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/group.png"> Plantes</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="evenements.html" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/event.png"> Evenements</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="capteurs.html" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/saved.png"> Capteurs</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- ======================= FIN SIDEBAR GAUCHE ======================= -->

                    <!-- ======================= COLONNE CENTRALE ======================= -->
                    <div class="col-md-6 second-section" id="page-content-wrapper">
                        <div class="header-section">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h1 class="mb-0">Publications</h1>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="ajout.php" class="btn btn-primary btn-custom">
                                        + Nouvelle Publication
                                    </a>
                                    <a href="publications.html" class="btn btn-secondary btn-custom">
                                        ← Retour
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php 
                                if ($_GET['success'] == 1) echo "Publication ajoutée avec succès!";
                                if ($_GET['success'] == 2) echo "Publication supprimée avec succès!";
                                if ($_GET['success'] == 3) echo "Publication modifiée avec succès!";
                                if ($_GET['success'] == 4) echo "Modification effectuée avec succès!";
                                if ($_GET['success'] == 5) echo "Commentaire modifié avec succès!";
                                if ($_GET['success'] == 6) echo "Commentaire supprimé avec succès!";
                                ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php 
                                if ($_GET['error'] == 0) echo "Erreur lors de la suppression";
                                if ($_GET['error'] == 1) echo "Publication non trouvée";
                                if ($_GET['error'] == 4) echo "Erreur lors de l'ajout du commentaire";
                                if ($_GET['error'] == 5) echo "Erreur lors de la modification du commentaire";
                                if ($_GET['error'] == 6) echo "Erreur lors de la suppression du commentaire";
                                ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['success_comment'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php 
                                if ($_GET['success_comment'] == 1) echo "Commentaire supprimé avec succès!";
                                ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['error_comment'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php 
                                if ($_GET['error_comment'] == 0) echo "Erreur lors de la suppression du commentaire";
                                if ($_GET['error_comment'] == 1) echo "Commentaire non trouvé";
                                ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($publications)): ?>
                            <div class="alert alert-info text-center">
                                <h4>Aucune publication pour le moment</h4>
                                <p>Soyez le premier à partager quelque chose !</p>
                                <a href="ajout.php" class="btn btn-primary">Créer une publication</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($publications as $pub): 
                                $userAlreadyLiked = in_array($pub['idPublication'], $postsLiked);
                                $nbCommentaires = $commentCounts[$pub['idPublication']];
                                $medias = isset($allMedias[$pub['idPublication']]) ? $allMedias[$pub['idPublication']] : [];
                                $mediaCount = count($medias);
                            ?>
                                <div class="publication-card">
                                    <div class="publication-content">
                                        <p class="mb-2" style="font-size: 16px; line-height: 1.6;"><?= nl2br(htmlspecialchars($pub['contenuTexte'])) ?></p>
                                        
                                        <!-- Section pour afficher les médias -->
                                        <?php if ($mediaCount > 0): ?>
                                        <div class="publication-media">
                                            <div class="media-gallery">
                                                <?php 
                                                $displayLimit = 4; // Limite d'affichage
                                                $displayed = 0;
                                                foreach ($medias as $index => $media): 
                                                    if ($displayed >= $displayLimit) break;
                                                    $displayed++;
                                                ?>
                                                    <div class="media-item" data-publication-id="<?= $pub['idPublication'] ?>" data-media-index="<?= $index ?>">
                                                        <?php if (strpos($media['type'], 'image') !== false): ?>
                                                            <span class="media-type-badge">📷</span>
                                                            <img src="<?= htmlspecialchars($media['chemin']) ?>" 
                                                                 alt="Image publication <?= $index + 1 ?>"
                                                                 onclick="openMediaModal(<?= $pub['idPublication'] ?>, <?= $index ?>)">
                                                        <?php elseif (strpos($media['type'], 'video') !== false): ?>
                                                            <span class="media-type-badge">🎬</span>
                                                            <video controls onclick="openMediaModal(<?= $pub['idPublication'] ?>, <?= $index ?>)">
                                                                <source src="<?= htmlspecialchars($media['chemin']) ?>" type="<?= htmlspecialchars($media['type']) ?>">
                                                                Votre navigateur ne supporte pas la vidéo.
                                                            </video>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($index === $displayLimit - 1 && $mediaCount > $displayLimit): ?>
                                                            <div class="media-count-badge" onclick="openMediaModal(<?= $pub['idPublication'] ?>, <?= $displayLimit ?>)">
                                                                +<?= $mediaCount - $displayLimit ?> plus
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="publication-meta text-muted small">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    📅 <?= date('d/m/Y H:i', strtotime($pub['datePublication'])) ?>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    👤 Utilisateur <?= $pub['idUtilisateur'] ?> 
                                                    | 
                                                    <form method="POST" action="like.php" style="display: inline;" novalidate>
                                                        <input type="hidden" name="idPublication" value="<?= $pub['idPublication'] ?>">
                                                        <input type="hidden" name="idUtilisateur" value="<?= $idUtilisateur ?>">
                                                        <button type="submit" class="like-btn <?= $userAlreadyLiked ? 'liked' : '' ?>">
                                                            ❤️ <span class="like-count"><?= $pub['nbLikes'] ?></span>
                                                        </button>
                                                    </form>
                                                    | 
                                                    <button type="button" class="comment-btn" onclick="toggleComments(<?= $pub['idPublication'] ?>)">
                                                        💬 <span class="comment-count"><?= $nbCommentaires ?></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section commentaires (cachée par défaut) -->
                                    <div class="comment-section" id="comments-<?= $pub['idPublication'] ?>">
                                        <!-- Formulaire d'ajout de commentaire -->
                                        <form method="POST" action="commenter.php" class="comment-form" novalidate>
                                            <input type="hidden" name="idPublication" value="<?= $pub['idPublication'] ?>">
                                            <input type="hidden" name="idUtilisateur" value="<?= $idUtilisateur ?>">
                                            <div class="input-group">
                                                <input type="text" name="contenuCommentaire" class="form-control" placeholder="Écrivez un commentaire..." required>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary">Envoyer</button>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Liste des commentaires -->
                                        <div class="comment-list">
                                            <?php if (!empty($allComments[$pub['idPublication']])): ?>
                                                <?php foreach ($allComments[$pub['idPublication']] as $comment): ?>
                                                    <div class="comment-item" id="comment-<?= $comment->getIdCommentaire() ?>">
                                                        <div class="comment-header d-flex justify-content-between align-items-center">
                                                            <div class="comment-author">
                                                                <?= htmlspecialchars($comment->getNom()) ?>
                                                            </div>
                                                            <?php if ($comment->getIdUtilisateur() == $idUtilisateur): ?>
                                                                <div class="comment-actions">
                                                                    <button class="btn btn-sm btn-outline-primary edit-comment-btn" 
                                                                            onclick="enableCommentEdit(<?= $comment->getIdCommentaire() ?>)">
                                                                        ✏️ Modifier
                                                                    </button>
                                                                    <button class="btn btn-sm btn-outline-danger delete-comment-btn" 
                                                                            onclick="deleteComment(<?= $comment->getIdCommentaire() ?>)">
                                                                        🗑️ Supprimer
                                                                    </button>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="comment-date">
                                                            <?= date('d/m/Y H:i', strtotime($comment->getDateCommentaire())) ?>
                                                        </div>
                                                        <div class="comment-text" id="comment-text-<?= $comment->getIdCommentaire() ?>">
                                                            <?= nl2br(htmlspecialchars($comment->getContenuCommentaire())) ?>
                                                        </div>
                                                        <!-- Formulaire d'édition (caché par défaut) -->
                                                        <div class="comment-edit-form" id="edit-form-<?= $comment->getIdCommentaire() ?>" style="display: none;">
                                                            <form method="POST" action="update_comment.php" class="mt-2" novalidate>
                                                                <input type="hidden" name="idCommentaire" value="<?= $comment->getIdCommentaire() ?>">
                                                                <input type="hidden" name="idPublication" value="<?= $pub['idPublication'] ?>">
                                                                <div class="input-group">
                                                                    <input type="text" name="contenuCommentaire" class="form-control edit-comment-input" 
                                                                           value="<?= htmlspecialchars($comment->getContenuCommentaire()) ?>" 
                                                                           required>
                                                                    <div class="input-group-append">
                                                                        <button type="submit" class="btn btn-success btn-sm">💾 Enregistrer</button>
                                                                        <button type="button" class="btn btn-secondary btn-sm" 
                                                                                onclick="cancelCommentEdit(<?= $comment->getIdCommentaire() ?>)">
                                                                            ❌ Annuler
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="text-muted text-center">Aucun commentaire pour le moment</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="publication-actions">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a href="update.php?id=<?= $pub['idPublication'] ?>" class="btn btn-outline-primary btn-sm">
                                                    ✏️ Modifier
                                                </a>
                                                <a href="delete.php?id=<?= $pub['idPublication'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ? Cette action supprimera aussi tous les commentaires et médias associés.')">
                                                    🗑️ Supprimer
                                                </a>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <span class="badge badge-primary">ID: <?= $pub['idPublication'] ?></span>
                                                <?php if ($mediaCount > 0): ?>
                                                    <span class="badge badge-info ml-2">📁 <?= $mediaCount ?> média<?= $mediaCount > 1 ? 's' : '' ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <!-- ======================= FIN COLONNE CENTRALE ======================= -->

                    <!-- ======================= SIDEBAR DROIT ======================= -->
                    <div class="col-md-3 third-section">
                        <div class="p-3 bg-white rounded w-shadow">
                            <h6 class="card-title pb-3 mb-0">Gestion CRUD</h6>
                            <div class="list-group">
                                <a href="ajout.php" class="list-group-item list-group-item-action">
                                    <i class='bx bx-plus mr-2'></i> Ajouter une publication
                                </a>
                                <a href="liste.php" class="list-group-item list-group-item-action active">
                                    <i class='bx bx-list-ul mr-2'></i> Liste des publications
                                </a>
                            </div>

                            <h6 class="card-title pb-3 mb-0 mt-4">Statistiques</h6>
                            <div class="bg-white rounded contacts">
                                <div class="media text-muted pt-2">
                                    <i class='bx bx-file mr-3 text-primary'></i>
                                    <div class="media-body">
                                        <strong>Publications totales</strong>
                                        <div class="text-muted small">
                                            <?php echo isset($publications) ? count($publications) : '0' ?> publications
                                        </div>
                                    </div>
                                </div>
                                <div class="media text-muted pt-2">
                                    <i class='bx bx-image mr-3 text-success'></i>
                                    <div class="media-body">
                                        <strong>Médias partagés</strong>
                                        <div class="text-muted small">
                                            <?php 
                                            $totalMedias = 0;
                                            if (isset($allMedias)) {
                                                foreach ($allMedias as $medias) {
                                                    $totalMedias += count($medias);
                                                }
                                            }
                                            echo $totalMedias . ' média' . ($totalMedias > 1 ? 's' : '');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-title pb-3 mb-0 mt-4">Contacts</h5>
                            <div class="bg-white rounded contacts">
                                <div class="media text-muted"> ... </div>
                                <small class="d-block text-right mt-3"><a href="#">See More</a></small>
                            </div>
                        </div>
                    </div>
                    <!-- ======================= FIN SIDEBAR DROIT ======================= -->

                </div>
            </div>
        </div>
    </div>

    <!-- AJOUT: Modal pour afficher les médias en grand -->
    <div class="media-modal" id="mediaModal">
        <span class="close-modal" onclick="closeMediaModal()">&times;</span>
        <div class="modal-nav prev-modal" onclick="prevMedia()">‹</div>
        <div class="modal-nav next-modal" onclick="nextMedia()">›</div>
        
        <div id="modalImageContainer" style="display: none;">
            <img id="modalImage" class="modal-content" src="" alt="">
        </div>
        <div id="modalVideoContainer" style="display: none;">
            <video id="modalVideo" class="modal-video" controls>
                Votre navigateur ne supporte pas la vidéo.
            </video>
        </div>
        
        <div class="modal-info">
            <div id="modalTitle">Media</div>
            <div class="modal-counter" id="modalCounter"></div>
        </div>
    </div>

    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="./assets/js/pulication-validation.js"></script>

    <script>
        // Variables globales pour la navigation des médias
        let currentPublicationId = null;
        let currentMediaIndex = 0;
        let currentMedias = [];

        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

        // Fonction pour ouvrir le modal des médias
        function openMediaModal(publicationId, mediaIndex) {
            // Récupérer tous les médias de cette publication
            const mediaElements = document.querySelectorAll(`.media-item[data-publication-id="${publicationId}"]`);
            currentMedias = [];
            
            // Récupérer les données des médias
            mediaElements.forEach(element => {
                const img = element.querySelector('img');
                const video = element.querySelector('video');
                const index = parseInt(element.dataset.mediaIndex);
                
                if (img) {
                    currentMedias[index] = {
                        type: 'image',
                        src: img.src,
                        alt: img.alt
                    };
                } else if (video) {
                    const source = video.querySelector('source');
                    currentMedias[index] = {
                        type: 'video',
                        src: source ? source.src : video.src,
                        alt: 'Video publication'
                    };
                }
            });
            
            // Supprimer les éléments vides
            currentMedias = currentMedias.filter(media => media !== undefined);
            
            if (currentMedias.length === 0) return;
            
            // Mettre à jour les variables globales
            currentPublicationId = publicationId;
            currentMediaIndex = Math.min(mediaIndex, currentMedias.length - 1);
            
            // Afficher le média courant
            showMedia(currentMediaIndex);
            
            // Afficher le modal
            document.getElementById('mediaModal').style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Empêcher le défilement
        }

        // Fonction pour afficher un média spécifique
        function showMedia(index) {
            if (index < 0 || index >= currentMedias.length) return;
            
            const media = currentMedias[index];
            const imageContainer = document.getElementById('modalImageContainer');
            const videoContainer = document.getElementById('modalVideoContainer');
            const modalImage = document.getElementById('modalImage');
            const modalVideo = document.getElementById('modalVideo');
            const modalTitle = document.getElementById('modalTitle');
            const modalCounter = document.getElementById('modalCounter');
            
            // Cacher tout d'abord
            imageContainer.style.display = 'none';
            videoContainer.style.display = 'none';
            
            if (media.type === 'image') {
                modalImage.src = media.src;
                modalImage.alt = media.alt;
                imageContainer.style.display = 'block';
                modalTitle.textContent = 'Image';
            } else if (media.type === 'video') {
                modalVideo.src = media.src;
                videoContainer.style.display = 'block';
                modalTitle.textContent = 'Vidéo';
                // Jouer la vidéo automatiquement
                modalVideo.play().catch(e => console.log('Auto-play prevented:', e));
            }
            
            // Mettre à jour le compteur
            modalCounter.textContent = `${index + 1} / ${currentMedias.length}`;
            currentMediaIndex = index;
        }

        // Fonction pour passer au média précédent
        function prevMedia() {
            if (currentMedias.length === 0) return;
            let newIndex = currentMediaIndex - 1;
            if (newIndex < 0) newIndex = currentMedias.length - 1;
            showMedia(newIndex);
        }

        // Fonction pour passer au média suivant
        function nextMedia() {
            if (currentMedias.length === 0) return;
            let newIndex = currentMediaIndex + 1;
            if (newIndex >= currentMedias.length) newIndex = 0;
            showMedia(newIndex);
        }

        // Fonction pour fermer le modal
        function closeMediaModal() {
            const modal = document.getElementById('mediaModal');
            const modalVideo = document.getElementById('modalVideo');
            
            // Arrêter la vidéo
            modalVideo.pause();
            modalVideo.currentTime = 0;
            
            // Fermer le modal
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Rétablir le défilement
            
            // Réinitialiser les variables
            currentPublicationId = null;
            currentMediaIndex = 0;
            currentMedias = [];
        }

        // Gestion des touches du clavier
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('mediaModal').style.display === 'flex') {
                if (e.key === 'Escape') {
                    closeMediaModal();
                } else if (e.key === 'ArrowLeft') {
                    prevMedia();
                } else if (e.key === 'ArrowRight') {
                    nextMedia();
                }
            }
        });

        // Fonction pour afficher/masquer les commentaires
        function toggleComments(publicationId) {
            const commentSection = document.getElementById('comments-' + publicationId);
            const isVisible = commentSection.style.display === 'block';
            
            // Masquer toutes les sections de commentaires
            document.querySelectorAll('.comment-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Afficher/masquer la section actuelle
            if (!isVisible) {
                commentSection.style.display = 'block';
            }
        }

        // Fonction pour activer l'édition d'un commentaire
        function enableCommentEdit(commentId) {
            document.getElementById('comment-text-' + commentId).style.display = 'none';
            document.getElementById('edit-form-' + commentId).style.display = 'block';
        }

        // Fonction pour annuler l'édition
        function cancelCommentEdit(commentId) {
            document.getElementById('edit-form-' + commentId).style.display = 'none';
            document.getElementById('comment-text-' + commentId).style.display = 'block';
        }

        // Fonction pour supprimer un commentaire
        function deleteComment(commentId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
                window.location.href = 'delete_comment.php?id=' + commentId;
            }
        }

        // Fermer les commentaires quand on clique en dehors
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.comment-section') && !e.target.closest('.comment-btn')) {
                document.querySelectorAll('.comment-section').forEach(section => {
                    section.style.display = 'none';
                });
            }
        });

        // Ouvrir automatiquement les commentaires après l'ajout
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const openCommentsId = urlParams.get('open_comments');
            
            if (openCommentsId) {
                toggleComments(parseInt(openCommentsId));
                
                const publicationElement = document.querySelector('.publication-card .comment-section[id="comments-' + openCommentsId + '"]');
                if (publicationElement) {
                    publicationElement.closest('.publication-card').scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>
</body>
</html>