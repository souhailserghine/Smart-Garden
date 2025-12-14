<?php 
require_once 'check_session.php';
include_once '../../controller/publicationC.php';
include_once '../../controller/CommentaireC.php';

// Initialize controllers
$publicationC = new PublicationC();
$commentaireC = new CommentaireC();

// Get current user info
$currentUserId = $_SESSION['idUtilisateur'];

// Get all publications with user-specific like status
$publications = $publicationC->getPublicationsAvecStatutLike($currentUserId);

// Récupérer le nombre de commentaires pour chaque publication
$commentCounts = [];
$allComments = [];

foreach ($publications as $pub) {
    $commentCounts[$pub['idPublication']] = $commentaireC->getNombreCommentaires($pub['idPublication']);
    $allComments[$pub['idPublication']] = $commentaireC->getCommentairesByPublication($pub['idPublication']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    <title>SmartGarden - Publications</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@1.9.2/css/boxicons.min.css' rel='stylesheet'>

    <!-- Styles -->
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/components.css" rel="stylesheet">
    <link href="./assets/css/media.css" rel="stylesheet">
    <link href="https://vjs.zencdn.net/7.4.1/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    
    <style>
        .crud-actions {
            margin-top: 15px;
            text-align: center;
        }
        .btn-crud {
            margin: 5px;
            padding: 8px 20px;
        }
        .publication-example {
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }
        
        /* Comment Styles */
        .comment-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 8px 16px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
            color: #64748b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-left: 10px;
        }
        
        .comment-btn:hover {
            color: #3b82f6;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
            border-color: rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
        }
        
        .comment-count {
            margin-left: 8px;
            font-weight: 700;
            font-size: 16px;
        }
        
        .comment-btn:hover .comment-count {
            color: #3b82f6;
        }
        
        .comment-section {
            margin-top: 20px;
            padding: 25px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.02);
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .comment-form {
            margin-bottom: 20px;
        }
        
        .comment-form .input-group {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .comment-form .input-group:focus-within {
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
        }
        
        .comment-form input {
            border: none;
            padding: 15px 20px;
            font-size: 15px;
            background: white;
        }
        
        .comment-form input:focus {
            outline: none;
            box-shadow: none;
        }
        
        .comment-form .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            padding: 15px 25px;
            border-radius: 0 12px 12px 0;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .comment-form .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: scale(1.02);
        }
        
        .comment-list {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .comment-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .comment-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .comment-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 10px;
        }
        
        .comment-item {
            padding: 20px;
            margin-bottom: 15px;
            background: white;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .comment-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .comment-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .comment-item:hover::before {
            opacity: 1;
        }
        
        .comment-author {
            font-weight: 700;
            color: #1e293b;
            font-size: 15px;
            display: flex;
            align-items: center;
        }
        
        .comment-author::before {
            content: '👤';
            margin-right: 8px;
            font-size: 14px;
        }
        
        .comment-date {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 4px;
            display: flex;
            align-items: center;
        }
        
        .comment-date::before {
            content: '🕒';
            margin-right: 6px;
            font-size: 12px;
        }
        
        .comment-text {
            margin-top: 12px;
            color: #475569;
            line-height: 1.6;
            font-size: 15px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
        }
        
        .comment-header {
            margin-bottom: 8px;
        }
        
        .comment-actions {
            opacity: 0;
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        
        .comment-item:hover .comment-actions {
            opacity: 1;
            transform: translateY(0);
        }
        
        .comment-actions .btn {
            padding: 6px 12px;
            margin-left: 8px;
            font-size: 13px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .edit-comment-input {
            font-size: 14px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .edit-comment-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        
        .btn-outline-primary {
            border: 2px solid #3b82f6;
            color: #3b82f6;
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
            color: white;
        }
        
        .btn-outline-danger {
            border: 2px solid #ef4444;
            color: #ef4444;
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .btn-outline-danger:hover {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
            color: white;
        }
    </style>
</head>

<body class="newsfeed">
    <div class="container-fluid" id="wrapper">
        <div class="row newsfeed-size">
            <div class="col-md-12 newsfeed-right-side">
                <!-- En-tête complet avec navigation -->
                <nav id="navbar-main" class="navbar navbar-expand-lg shadow-sm sticky-top">
                    <ul class="navbar-nav mr-5" id="main_menu">
                        <a class="navbar-brand nav-item mr-lg-5" href="publications.php"><img src="./assets/images/logo-128x128.png" width="70" height="70" class="mr-3" alt="Logo"></a>
                        
                        <form class="w-30 mx-2 my-auto d-inline form-inline mr-5">
                            <div class="input-group">
                                <input type="text" class="form-control search-input w-75" placeholder="Search for people, companies, events and more..." aria-label="Search" aria-describedby="search-addon">
                                <div class="input-group-append">
                                    <button class="btn search-button" type="button"><i class='bx bx-search'></i></button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Profil utilisateur -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link nav-links" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <div class="menu-user-image">
                                    <img src="./assets/images/users/user-4.jpg" class="menu-user-img ml-1" alt="Menu Image">
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right nav-drop">
                                <a class="dropdown-item" href="profile.php"><i class='bx bx-user mr-2'></i> Account</a>
                                <div role="separator" class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class='bx bx-undo mr-2'></i> Logout</a>
                            </div>
                        </li>

                        <!-- Settings -->
                        <li class="nav-item nav-icon">
                            <a href="settings.php" data-toggle="tooltip" data-placement="bottom" data-title="Settings" class="nav-link"><img src="./assets/images/icons/navbar/settings.png" alt="navbar icon"></a>
                        </li>
                    </ul>
                    <button type="button" class="btn btn-primary mr-3" id="menu-toggle"><i class='bx bx-align-left'></i></button>
                </nav>
                
                <div class="row newsfeed-right-side-content mt-3">
                    <div class="col-md-3 newsfeed-left-side sticky-top shadow-sm" id="sidebar-wrapper">
                        <div class="card newsfeed-user-card h-100">
                            <ul class="list-group list-group-flush newsfeed-left-sidebar">
                                <li class="list-group-item">
                                    <h6>Home</h6>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ">
                                    <a href="profile.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/newsfeed.png" alt="profile"> Profile</a>
                                    <a href="#" class="newsfeedListicon"><i class='bx bx-dots-horizontal-rounded'></i></a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center sd-active">
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
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="listCategorie.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/saved.png" alt="capteurs"> Capteurs</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-6 second-section" id="page-content-wrapper">
                        <!-- Section CRUD -->
                        <div class="mb-4">
                            <div class="card newsfeed-user-card">
                                <div class="card-body">
                                    <h5 class="card-title">Gestion des Publications</h5>
                                    <p class="card-text">Creez et gerez vos publications facilement.</p>
                                    
                                    <div class="crud-actions">
                                        <a href="ajout.php" class="btn btn-primary btn-crud">
                                            <i class='bx bx-plus'></i> Nouvelle Publication
                                        </a>
                                        <a href="liste.php" class="btn btn-success btn-crud">
                                            <i class='bx bx-list-ul'></i> Voir toutes les Publications
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Publications from Database -->
                        <div class="posts-section mb-5">
                            <h6 class="mt-4 mb-3">Dernières Publications</h6>
                            
                            <?php if (empty($publications)): ?>
                                <!-- No publications message -->
                                <div class="post border-bottom p-4 bg-white w-shadow text-center">
                                    <i class='bx bx-message-square-x' style="font-size: 3rem; color: #6c757d;"></i>
                                    <h5 class="mt-3">Aucune publication pour le moment</h5>
                                    <p class="text-muted">Soyez le premier à partager quelque chose avec la communauté SmartGarden !</p>
                                    <a href="ajout.php" class="btn btn-primary mt-2">
                                        <i class='bx bx-plus'></i> Créer une publication
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php 
                                foreach ($publications as $pub): 
                                    // Get real user name from database
                                    $userName = !empty($pub['nom']) 
                                        ? htmlspecialchars($pub['nom']) 
                                        : 'Utilisateur #' . $pub['idUtilisateur'];
                                    
                                    // Use user ID to determine avatar (1-4)
                                    $userImage = (($pub['idUtilisateur'] - 1) % 4) + 1;
                                ?>
                                    <!-- Publication -->
                                    <div class="post border-bottom p-3 bg-white w-shadow publication-example">
                                        <div class="media text-muted pt-3">
                                            <img src="./assets/images/users/user-<?= $userImage ?>.jpg" 
                                                 alt="Online user" class="mr-3 post-user-image">
                                            <div class="media-body pb-3 mb-0 small lh-125">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <a href="#" class="text-gray-dark post-user-name"><?= $userName ?></a>
                                                    <?php if ($pub['idUtilisateur'] == $currentUserId): ?>
                                                    <div class="dropdown">
                                                        <a href="#" class="post-more-settings" role="button" data-toggle="dropdown">
                                                            <i class='bx bx-dots-horizontal-rounded'></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="modifier.php?id=<?= $pub['idPublication'] ?>">Modifier</a>
                                                            <a class="dropdown-item" href="supprimer.php?id=<?= $pub['idPublication'] ?>" 
                                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">Supprimer</a>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="d-block">
                                                    <?php
                                                    $date = new DateTime($pub['datePublication']);
                                                    $now = new DateTime();
                                                    $diff = $now->diff($date);
                                                    
                                                    if ($diff->d == 0) {
                                                        if ($diff->h == 0) {
                                                            if ($diff->i == 0) {
                                                                echo 'À l\'instant';
                                                            } else {
                                                                echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                                                            }
                                                        } else {
                                                            echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                                        }
                                                    } elseif ($diff->d == 1) {
                                                        echo '1 day ago';
                                                    } elseif ($diff->d < 7) {
                                                        echo $diff->d . ' days ago';
                                                    } else {
                                                        echo $date->format('d/m/Y');
                                                    }
                                                    ?>
                                                    <i class='bx bx-globe ml-3'></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <p><?= nl2br(htmlspecialchars($pub['contenuTexte'])) ?></p>
                                        </div>
                                        
                                        <?php if (!empty($pub['images'])): ?>
                                        <div class="d-block mt-3">
                                            <img src="<?= htmlspecialchars($pub['images']) ?>" 
                                                 class="post-content" 
                                                 alt="Publication image" 
                                                 style="max-width: 100%; border-radius: 8px;">
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($pub['videos'])): ?>
                                        <div class="d-block mt-3">
                                            <video controls class="post-content" style="max-width: 100%; border-radius: 8px;">
                                                <source src="<?= htmlspecialchars($pub['videos']) ?>" type="video/mp4">
                                                Votre navigateur ne supporte pas la vidéo.
                                            </video>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="mb-3">
                                            <div class="argon-reaction">
                                                <span class="like-btn">
                                                    <a href="javascript:void(0)" 
                                                       class="post-card-buttons like-publication <?= $pub['userHasLiked'] ? 'user-has-liked' : '' ?>" 
                                                       data-id="<?= $pub['idPublication'] ?>"
                                                       style="<?= $pub['userHasLiked'] ? 'color: #007bff;' : '' ?>">
                                                        <i class='bx <?= $pub['userHasLiked'] ? 'bxs-like' : 'bx-like' ?> mr-2'></i> 
                                                        <span class="like-count-<?= $pub['idPublication'] ?>"><?= $pub['nbLikes'] ?? 0 ?></span>
                                                    </a>
                                                </span>
                                                <button type="button" class="comment-btn" onclick="toggleComments(<?= $pub['idPublication'] ?>)">
                                                    <i class='bx bx-message-rounded'></i>
                                                    <span class="comment-count"><?= $commentCounts[$pub['idPublication']] ?? 0 ?></span>
                                                </button>
                                            </div>
                                            <a href="#" class="post-card-buttons">
                                                <i class='bx bx-share-alt mr-2'></i> Share
                                            </a>
                                        </div>
                                        
                                        <!-- Section des commentaires -->
                                        <div class="comment-section" id="comments-<?= $pub['idPublication'] ?>">
                                            <!-- Formulaire d'ajout de commentaire -->
                                            <div class="comment-form">
                                                <form method="POST" action="add_comment.php" id="commentForm-<?= $pub['idPublication'] ?>">
                                                    <input type="hidden" name="idPublication" value="<?= $pub['idPublication'] ?>">
                                                    <input type="hidden" name="idUtilisateur" value="<?= $currentUserId ?>">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="contenuCommentaire" 
                                                               placeholder="Ajouter un commentaire..." required>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="submit">
                                                                <i class='bx bx-send'></i> Publier
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <!-- Liste des commentaires -->
                                            <div class="comment-list">
                                                <?php 
                                                $comments = $allComments[$pub['idPublication']] ?? [];
                                                if (empty($comments)): 
                                                ?>
                                                    <p class="text-muted text-center">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                                                <?php else: ?>
                                                    <?php foreach ($comments as $comment): ?>
                                                        <div class="comment-item" id="comment-<?= $comment->getIdCommentaire() ?>">
                                                            <div class="comment-header">
                                                                <div class="comment-author"><?= htmlspecialchars($comment->getNom()) ?></div>
                                                                <div class="comment-date">
                                                                    <?php
                                                                    $commentDate = new DateTime($comment->getDateCommentaire());
                                                                    $now = new DateTime();
                                                                    $diff = $now->diff($commentDate);
                                                                    
                                                                    if ($diff->d == 0) {
                                                                        if ($diff->h == 0) {
                                                                            echo $diff->i . ' min';
                                                                        } else {
                                                                            echo $diff->h . 'h';
                                                                        }
                                                                    } else {
                                                                        echo $commentDate->format('d/m/Y H:i');
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="comment-text" id="commentText-<?= $comment->getIdCommentaire() ?>">
                                                                <?= nl2br(htmlspecialchars($comment->getContenuCommentaire())) ?>
                                                            </div>
                                                            <div class="comment-edit-form" id="commentEditForm-<?= $comment->getIdCommentaire() ?>" style="display: none;">
                                                                <input type="text" class="form-control edit-comment-input" 
                                                                       id="editInput-<?= $comment->getIdCommentaire() ?>"
                                                                       value="<?= htmlspecialchars($comment->getContenuCommentaire()) ?>">
                                                                <div class="mt-2">
                                                                    <button class="btn btn-sm btn-outline-primary" onclick="saveCommentEdit(<?= $comment->getIdCommentaire() ?>, <?= $pub['idPublication'] ?>)">
                                                                        <i class='bx bx-check'></i> Enregistrer
                                                                    </button>
                                                                    <button class="btn btn-sm btn-outline-danger" onclick="cancelCommentEdit(<?= $comment->getIdCommentaire() ?>)">
                                                                        <i class='bx bx-x'></i> Annuler
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <?php if ($comment->getIdUtilisateur() == $currentUserId): ?>
                                                            <div class="comment-actions mt-2">
                                                                <button class="btn btn-sm btn-outline-primary" onclick="enableCommentEdit(<?= $comment->getIdCommentaire() ?>)">
                                                                    <i class='bx bx-edit'></i> Modifier
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteComment(<?= $comment->getIdCommentaire() ?>, <?= $pub['idPublication'] ?>)">
                                                                    <i class='bx bx-trash'></i> Supprimer
                                                                </button>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                                    <a href="#" class="post-card-buttons"><i class='bx bx-share-alt mr-2'></i> Share</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 third-section">
                        <!-- Sidebar droit -->
                        <div class="p-3 bg-white rounded w-shadow">
                            <h6 class="card-title pb-3 mb-0">Gestion CRUD</h6>
                            <div class="list-group">
                                <a href="ajout.php" class="list-group-item list-group-item-action">
                                    <i class='bx bx-plus mr-2'></i> Ajouter une publication
                                </a>
                                <a href="liste.php" class="list-group-item list-group-item-action">
                                    <i class='bx bx-list-ul mr-2'></i> Liste des publications
                                </a>
                            </div>

                            <h6 class="card-title pb-3 mb-0 mt-4">Statistiques</h6>
                            <div class="bg-white rounded contacts">
                                <div class="media text-muted pt-2">
                                    <i class='bx bx-file mr-3 text-primary'></i>
                                    <div class="media-body">
                                        <strong>Publications totales</strong>
                                        <div class="text-muted small">15 publications</div>
                                    </div>
                                </div>
                                <div class="media text-muted pt-3">
                                    <i class='bx bx-like mr-3 text-success'></i>
                                    <div class="media-body">
                                        <strong>Likes reçus</strong>
                                        <div class="text-muted small">133 likes</div>
                                    </div>
                                </div>
                                <div class="media text-muted pt-3">
                                    <i class='bx bx-comment mr-3 text-info'></i>
                                    <div class="media-body">
                                        <strong>Commentaires</strong>
                                        <div class="text-muted small">25 commentaires</div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-title pb-3 mb-0 mt-4">Contacts</h5>
                            <div class="bg-white rounded contacts">
                                <div class="media text-muted">
                                    <img src="./assets/images/users/user-2.jpg" alt="Online user" class="online-user-image align-middle">
                                    <div class="media-body mb-0 small lh-125">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <strong class="text-gray-dark"><a href="#" class="smFLname">Karen Minas</a></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="media text-muted pt-3">
                                    <img src="./assets/images/users/user-3.jpg" alt="Online user" class="online-user-image">
                                    <div class="media-body mb-0 small lh-125">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <strong class="text-gray-dark"><a href="#" class="smFLname">Hakob Minasyan</a></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="media text-muted pt-3">
                                    <img src="./assets/images/users/user-1.jpg" alt="Online user" class="online-user-image">
                                    <div class="media-body mb-0 small lh-125">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <strong class="text-gray-dark"><a href="#" class="smFLname">Lina Adamyan</a></strong>
                                        </div>
                                    </div>
                                </div>
                                <small class="d-block text-right mt-3">
                                    <a href="#">See More</a>
                                </small>
                            </div>
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
    
    <script>
        // Toggle sidebar
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

        // Gestion des likes sur les publications
        $(document).on('click', '.like-publication', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const publicationId = button.data('id');
            const likeCountSpan = button.find('.like-count-' + publicationId);
            
            // Check if already liked (visual indicator)
            if (button.hasClass('user-has-liked')) {
                alert('Vous avez déjà aimé cette publication !');
                return;
            }
            
            // Send AJAX request to add like
            $.ajax({
                url: 'like_publication.php',
                method: 'POST',
                data: { idPublication: publicationId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update like count
                        likeCountSpan.text(response.nbLikes);
                        
                        // Mark as liked visually
                        button.addClass('user-has-liked');
                        button.find('i').removeClass('bx-like').addClass('bxs-like');
                        button.css('color', '#007bff');
                    } else {
                        alert(response.message || 'Erreur lors du like');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                }
            });
        });
        
        // Fonction pour afficher/masquer les commentaires
        function toggleComments(publicationId) {
            const commentsSection = document.getElementById('comments-' + publicationId);
            if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
                commentsSection.style.display = 'block';
                // Scroll to comments
                commentsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                commentsSection.style.display = 'none';
            }
        }
        
        // Fonction pour activer l'édition d'un commentaire
        function enableCommentEdit(commentId) {
            document.getElementById('commentText-' + commentId).style.display = 'none';
            document.getElementById('commentEditForm-' + commentId).style.display = 'block';
        }
        
        // Fonction pour annuler l'édition
        function cancelCommentEdit(commentId) {
            document.getElementById('commentText-' + commentId).style.display = 'block';
            document.getElementById('commentEditForm-' + commentId).style.display = 'none';
        }
        
        // Fonction pour sauvegarder l'édition d'un commentaire
        function saveCommentEdit(commentId, publicationId) {
            const newContent = document.getElementById('editInput-' + commentId).value;
            
            if (!newContent.trim()) {
                alert('Le commentaire ne peut pas être vide');
                return;
            }
            
            $.ajax({
                url: 'update_comment.php',
                method: 'POST',
                data: {
                    idCommentaire: commentId,
                    contenuCommentaire: newContent,
                    idPublication: publicationId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update comment text
                        document.getElementById('commentText-' + commentId).innerHTML = newContent.replace(/\n/g, '<br>');
                        cancelCommentEdit(commentId);
                        alert('Commentaire modifié avec succès');
                    } else {
                        alert(response.message || 'Erreur lors de la modification');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                }
            });
        }
        
        // Fonction pour supprimer un commentaire
        function deleteComment(commentId, publicationId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
                return;
            }
            
            $.ajax({
                url: 'delete_comment.php',
                method: 'POST',
                data: {
                    idCommentaire: commentId,
                    idPublication: publicationId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Remove comment from DOM
                        document.getElementById('comment-' + commentId).remove();
                        
                        // Update comment count
                        const commentBtn = document.querySelector('button[onclick="toggleComments(' + publicationId + ')"] .comment-count');
                        if (commentBtn) {
                            const currentCount = parseInt(commentBtn.textContent);
                            commentBtn.textContent = Math.max(0, currentCount - 1);
                        }
                        
                        alert('Commentaire supprimé avec succès');
                    } else {
                        alert(response.message || 'Erreur lors de la suppression');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                }
            });
        }
        
        // Gérer la soumission des formulaires de commentaires
        $(document).on('submit', '[id^="commentForm-"]', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const publicationId = form.find('input[name="idPublication"]').val();
            const commentInput = form.find('input[name="contenuCommentaire"]');
            const commentContent = commentInput.val().trim();
            
            if (!commentContent) {
                alert('Le commentaire ne peut pas être vide');
                return;
            }
            
            $.ajax({
                url: 'add_comment.php',
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Reload the page to show the new comment
                        location.reload();
                    } else {
                        alert(response.message || 'Erreur lors de l\'ajout du commentaire');
                    }
                },
                error: function() {
                    alert('Erreur de connexion');
                }
            });
        });
    </script>
</body>
</html>