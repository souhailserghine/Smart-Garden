<?php 
require_once 'check_session.php'; 
require_once '../../controller/publicationC.php';
require_once '../../config.php';

// Get current user's publications
$publicationC = new PublicationC();
$currentUserId = $_SESSION['idUtilisateur'];

// Debug: Afficher l'ID utilisateur
// echo "Current User ID: " . $currentUserId . "<br>";

// Get publications for this specific user
$db = config::getConnexion();
$query = $db->prepare("SELECT * FROM publication WHERE idUtilisateur = :userId ORDER BY datePublication DESC");
$query->execute(['userId' => $currentUserId]);
$userPublications = $query->fetchAll(PDO::FETCH_ASSOC);

// Debug: Afficher le nombre de publications
// echo "Nombre de publications: " . count($userPublications) . "<br>";
// echo "<pre>";
// print_r($userPublications);
// echo "</pre>";
// exit(); // Décommenter pour arrêter l'exécution ici
?>
<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    
    <title>SmartGarden</title>

    
    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@1.9.2/css/boxicons.min.css' rel='stylesheet'>

    
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/components.css" rel="stylesheet">
    <link href="./assets/css/profile.css" rel="stylesheet">
    <link href="./assets/css/media.css" rel="stylesheet">
    <script src="./assets/js/load.js" type="text/javascript"></script>
</head>

<body class="profile">
    <div class="container-fluid newsfeed d-flex" id="wrapper">
        <div class="row newsfeed-size" style="max-width: 100%; width: 100%; margin: 0;">
            <div class="col-md-12 p-0">
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
                            
                            <form class="w-30 mx-2 my-auto d-inline form-inline mr-5">
                                <div class="input-group">
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
                                    <a class="dropdown-item d-flex align-items-center dark-mode" href="#">
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
                        </ul>
                    </div>
                </nav>
                <div class="row profile-right-side-content">
                    <div class="user-profile" style="max-width: 100%; width: 100%;">
                        <div class="profile-header-background">
                            <a href="#" class="profile-cover">
                                <img src="./assets/images/users/cover/cover-1.gif" alt="Profile Header Background">
                                <div class="cover-overlay">
                                    <a href="#" class="btn btn-update-cover"><i class='bx bxs-camera'></i> Update Cover Photo</a>
                                </div>
                            </a>
                        </div>
                        <div class="row profile-rows">
                            <div class="col-md-3">
                                <div class="profile-info-left">
                                    <div class="text-center">
                                        <div class="profile-img w-shadow">
                                            <div class="profile-img-overlay"></div>
                                            <img src="./assets/images/users/user-4.jpg" alt="Avatar" class="avatar img-circle">
                                            <div class="profile-img-caption">
                                                <label for="updateProfilePic" class="upload">
                                                    <i class='bx bxs-camera'></i> Update
                                                    <input type="file" id="updateProfilePicInput" class="text-center upload">
                                                </label>
                                            </div>
                                        </div>
                                        <p class="profile-fullname mt-3"><?php echo $_SESSION['user_name']; ?></p>
                                        <p class="profile-username mb-3 text-muted">@<?php echo strtolower(str_replace(' ', '_', $_SESSION['user_name'])); ?></p>
                                    </div>
                                    <div class="intro mt-4">
                                        <div class="d-flex">
                                            <button type="button" class="btn btn-follow mr-3"><i class='bx bx-plus'></i> Follow</button>
                                            <button type="button" class="btn btn-start-chat" data-toggle="modal" data-target="#newMessageModal"><i class='bx bxs-message-rounded'></i> <span class="fs-8">Message</span></button>
                                            <button type="button" class="btn btn-follow" id="moreMobile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class='bx bx-dots-horizontal-rounded'></i> <span class="fs-8">More</span></button>
                                            <div class="dropdown-menu dropdown-menu-right profile-ql-dropdown" aria-labelledby="moreMobile">
                                                <a href="newsfeed.php" class="dropdown-item">Timeline</a>
                                                <a href="followers.php" class="dropdown-item">Followers</a>
                                                <a href="following.php" class="dropdown-item">Following</a>
                                                <a href="videos.php" class="dropdown-item">Videos</a>
                                                <a href="check-ins.php" class="dropdown-item">Check-Ins</a>
                                                <a href="events.php" class="dropdown-item">Events</a>
                                                <a href="likes.php" class="dropdown-item">Likes</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="intro mt-5 mv-hidden">
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <h3 class="intro-about">Intro</h3>
                                        </div>
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <p class="intro-title text-muted"><i class='bx bx-briefcase text-primary'></i> Web Developer at <a href="#">Company Name</a></p>
                                        </div>
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <p class="intro-title text-muted"><i class='bx bx-map text-primary'></i> Lives in <a href="#">City, Country</a></p>
                                        </div>
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <p class="intro-title text-muted"><i class='bx bx-time text-primary'></i> Last Login <a href="#">Online <span class="ml-1 online-status bg-success"></span></a></p>
                                        </div>
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <a href="#" class="btn btn-quick-link join-group-btn border w-100">Edit Details</a>
                                        </div>
                                    </div>
                                    <div class="intro mt-5 row mv-hidden">
                                        <div class="col-md-4">
                                            <img src="./assets/images/users/album/album-1.jpg" width="95" alt="">
                                        </div>
                                        <div class="col-md-4">
                                            <img src="./assets/images/users/album/album-2.jpg" width="95" alt="">
                                        </div>
                                        <div class="col-md-4">
                                            <img src="./assets/images/users/album/album-3.jpg" width="95" alt="">
                                        </div>
                                    </div>
                                    <div class="intro mt-5 mv-hidden">
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <h3 class="intro-about">Other Social Accounts</h3>
                                        </div>
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <p class="intro-title text-muted"><i class='bx bxl-facebook-square facebook-color'></i> <a href="#" target="_blank">facebook.com/username</a></p>
                                        </div>
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <p class="intro-title text-muted"><i class='bx bxl-twitter twitter-color'></i> <a href="#" target="_blank">twitter.com/username</a></p>
                                        </div>
                                        <div class="intro-item d-flex justify-content-between align-items-center">
                                            <p class="intro-title text-muted"><i class='bx bxl-instagram instagram-color'></i> <a href="#" target="_blank">instagram.com/username</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9 p-0">
                                <div class="profile-info-right">

                                    
                                    <div class="row">
                                        <div class="col-md-12 profile-center">
                                            <ul class="list-inline profile-links d-flex justify-content-between w-shadow rounded">
                                                <li class="list-inline-item profile-active">
                                                    <a href="#">Timeline</a>
                                                </li>
                                                <li class="list-inline-item dropdown">
                                                    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class='bx bx-dots-vertical-rounded'></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right profile-ql-dropdown">
                                                        <a href="#" class="dropdown-item">Activity Log</a>
                                                        <a href="#" class="dropdown-item">Videos</a>
                                                        <a href="#" class="dropdown-item">Check-Ins</a>
                                                        <a href="#" class="dropdown-item">Events</a>
                                                        <a href="#" class="dropdown-item">Likes</a>
                                                    </div>
                                                </li>
                                            </ul>
                                            <ul class="list-unstyled" style="margin-bottom: 0;">
                                                <li class="media post-form w-shadow">
                                                    <div class="media-body">
                                                        <div class="form-group post-input">
                                                            <textarea class="form-control" id="postForm" rows="2" placeholder="What's on your mind, <?php echo $_SESSION['user_name']; ?>?"></textarea>
                                                        </div>
                                                        <div class="row post-form-group">
                                                            <div class="col-md-9">
                                                                <button type="button" class="btn btn-link post-form-btn btn-sm">
                                                                    <i class='bx bx-images'></i> <span>Photo/Video</span>
                                                                </button>
                                                                <button type="button" class="btn btn-link post-form-btn btn-sm">
                                                                    <i class='bx bxs-group'></i> <span>Tag Friends</span>
                                                                </button>
                                                                <button type="button" class="btn btn-link post-form-btn btn-sm">
                                                                    <i class='bx bxs-map'></i> <span>Check In</span>
                                                                </button>
                                                            </div>
                                                            <div class="col-md-3 text-right">
                                                                <button type="button" class="btn btn-primary btn-sm">Publish</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="bg-white profile-posts-options mt-5 mb-4 py-3 d-flex justify-content-between shadow-sm">
                                                <div class="col-md-3 col-sm-12">
                                                    <h6 class="timeline-title">Posts</h6>
                                                </div>
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="timeline-manage">
                                                        <button type="button" class="btn btn-quick-link join-group-btn border btn-sm tmo-buttons"><i class='bx bxs-cog'></i> Manage Posts</button>
                                                        <button type="button" class="btn btn-quick-link join-group-btn border btn-sm tmo-buttons"><i class='bx bx-align-middle'></i> List View</button>
                                                        <button type="button" class="btn btn-quick-link join-group-btn border btn-sm tmo-buttons"><i class='bx bxs-grid-alt'></i> Grid View</button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php if (empty($userPublications)): ?>
                                                <!-- No publications message -->
                                                <div class="post border-bottom p-5 bg-white w-shadow text-center">
                                                    <i class='bx bx-message-square-x' style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                                                    <h4 class="mt-3 text-muted">Aucune publication</h4>
                                                    <p class="text-muted">Vous n'avez pas encore publié de contenu.</p>
                                                    <a href="publications.php" class="btn btn-primary mt-2">
                                                        <i class='bx bx-plus'></i> Créer votre première publication
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <?php foreach ($userPublications as $pub): ?>
                                                    <!-- User Publication -->
                                                    <div class="post border-bottom p-3 bg-white w-shadow mb-3">
                                                        <div class="media text-muted pt-3">
                                                            <img src="./assets/images/users/user-4.jpg" alt="Online user" class="mr-3 post-user-image">
                                                            <div class="media-body pb-3 mb-0 small lh-125">
                                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                                    <span class="post-type text-muted">
                                                                        <a href="#" class="text-gray-dark post-user-name mr-2"><?php echo $_SESSION['user_name']; ?></a> 
                                                                        a publié
                                                                    </span>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="post-more-settings" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <i class='bx bx-dots-horizontal-rounded'></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left post-dropdown-menu">
                                                                            <a href="modifier_publication.php?id=<?= $pub['idPublication'] ?>" class="dropdown-item">
                                                                                <div class="row">
                                                                                    <div class="col-md-2">
                                                                                        <i class='bx bx-edit post-option-icon'></i>
                                                                                    </div>
                                                                                    <div class="col-md-10">
                                                                                        <span class="fs-9">Modifier</span>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                            <a href="supprimer_publication.php?id=<?= $pub['idPublication'] ?>" class="dropdown-item" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">
                                                                                <div class="row">
                                                                                    <div class="col-md-2">
                                                                                        <i class='bx bx-trash post-option-icon'></i>
                                                                                    </div>
                                                                                    <div class="col-md-10">
                                                                                        <span class="fs-9">Supprimer</span>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="d-block">
                                                                    <?php
                                                                    $date = new DateTime($pub['datePublication']);
                                                                    $now = new DateTime();
                                                                    $diff = $now->diff($date);
                                                                    
                                                                    if ($diff->d == 0) {
                                                                        if ($diff->h == 0) {
                                                                            if ($diff->i == 0) {
                                                                                echo 'à l\'instant';
                                                                            } else {
                                                                                echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                                                                            }
                                                                        } else {
                                                                            echo $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
                                                                        }
                                                                    } elseif ($diff->d == 1) {
                                                                        echo 'Hier';
                                                                    } elseif ($diff->d < 7) {
                                                                        echo $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
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
                                                            <img src="<?= htmlspecialchars($pub['images']) ?>" class="w-100 mb-3" alt="post image" style="border-radius: 8px;">
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                        <?php if (!empty($pub['videos'])): ?>
                                                        <div class="d-block mt-3">
                                                            <video controls class="w-100 mb-3" style="border-radius: 8px;">
                                                                <source src="<?= htmlspecialchars($pub['videos']) ?>" type="video/mp4">
                                                                Votre navigateur ne supporte pas la vidéo.
                                                            </video>
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="mb-2">
                                                            <div class="argon-reaction">
                                                                <span class="like-btn">
                                                                    <a href="javascript:void(0)" class="post-card-buttons like-publication" data-id="<?= $pub['idPublication'] ?>">
                                                                        <i class='bx bxs-like mr-2'></i> 
                                                                        <span class="like-count-<?= $pub['idPublication'] ?>"><?= $pub['nbLikes'] ?? 0 ?></span>
                                                                    </a>
                                                                </span>
                                                            </div>
                                                            <a href="javascript:void(0)" class="post-card-buttons">
                                                                <i class='bx bx-message-rounded mr-2'></i> Commenter
                                                            </a>
                                                            <div class="dropdown dropup share-dropup d-inline">
                                                                <a href="#" class="post-card-buttons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <i class='bx bx-share-alt mr-2'></i> Partager
                                                                </a>
                                                                <div class="dropdown-menu post-dropdown-menu">
                                                                    <a href="#" class="dropdown-item">
                                                                        <div class="row">
                                                                            <div class="col-md-2">
                                                                                <i class='bx bx-share-alt'></i>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <span>Partager maintenant (Public)</span>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                    <a href="#" class="dropdown-item">
                                                                        <div class="row">
                                                                            <div class="col-md-2">
                                                                                <i class='bx bx-message'></i>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <span>Envoyer en message</span>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-3 profile-quick-media">
                                            <h6 class="text-muted timeline-title">Recent Media</h6>
                                            <div class="quick-media">
                                                <div class="media-overlay"></div>
                                                <a href="#" class="quick-media-img"><img src="./assets/images/users/album/album-1.jpg" alt="Quick media"></a>
                                                <div class="media-overlay-content">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="media-overlay-owner">
                                                            <img src="./assets/images/users/user-12.png" alt="Media owner image">
                                                            <span class="overlay-owner-name fs-9">Irwin M. Spelle</span>
                                                        </div>
                                                        <div class="dropdown">
                                                            <a href="#" class="overlay-more" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class='bx bx-dots-horizontal-rounded'></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right nav-drop dropdown-shadow">
                                                                <a class="dropdown-item" href="#">Save post</a>
                                                                <a class="dropdown-item" href="#">Turn on notifications</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="overlay-bottom d-flex justify-content-between align-items-center">
                                                        <div class="argon-reaction">
                                                            <span class="like-btn">
                                                                <a href="#" class="post-card-buttons" id="reactions"><i class='bx bxs-like mr-1'></i> 67</a>
                                                                <ul class="reactions-box dropdown-shadow">
                                                                    <li class="reaction reaction-like" data-reaction="Like"></li>
                                                                    <li class="reaction reaction-love" data-reaction="Love"></li>
                                                                    <li class="reaction reaction-haha" data-reaction="HaHa"></li>
                                                                    <li class="reaction reaction-wow" data-reaction="Wow"></li>
                                                                    <li class="reaction reaction-sad" data-reaction="Sad"></li>
                                                                    <li class="reaction reaction-angry" data-reaction="Angry"></li>
                                                                </ul>
                                                            </span>
                                                        </div>
                                                        <div class="liked-users">
                                                            <img src="./assets/images/users/user-9.png" alt="Liked users">
                                                            <img src="./assets/images/users/user-6.png" alt="Liked users">
                                                            <img src="./assets/images/users/user-12.png" alt="Liked users">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="quick-media">
                                                <div class="media-overlay"></div>
                                                <a href="#" class="quick-media-img"><img src="./assets/images/users/album/album-2.jpg" alt="Quick media"></a>
                                                <div class="media-overlay-content">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="media-overlay-owner">
                                                            <img src="./assets/images/users/user-12.png" alt="Media owner image">
                                                            <span class="overlay-owner-name fs-9">Irwin M. Spelle</span>
                                                        </div>
                                                        <div class="dropdown">
                                                            <a href="#" class="overlay-more" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class='bx bx-dots-horizontal-rounded'></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right nav-drop dropdown-shadow">
                                                                <a class="dropdown-item" href="#">Save post</a>
                                                                <a class="dropdown-item" href="#">Turn on notifications</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="overlay-bottom d-flex justify-content-between align-items-center">
                                                        <div class="argon-reaction">
                                                            <span class="like-btn">
                                                                <a href="#" class="post-card-buttons" id="reactions"><i class='bx bxs-like mr-1'></i> 67</a>
                                                                <ul class="reactions-box dropdown-shadow">
                                                                    <li class="reaction reaction-like" data-reaction="Like"></li>
                                                                    <li class="reaction reaction-love" data-reaction="Love"></li>
                                                                    <li class="reaction reaction-haha" data-reaction="HaHa"></li>
                                                                    <li class="reaction reaction-wow" data-reaction="Wow"></li>
                                                                    <li class="reaction reaction-sad" data-reaction="Sad"></li>
                                                                    <li class="reaction reaction-angry" data-reaction="Angry"></li>
                                                                </ul>
                                                            </span>
                                                        </div>
                                                        <div class="liked-users">
                                                            <img src="./assets/images/users/user-9.png" alt="Liked users">
                                                            <img src="./assets/images/users/user-6.png" alt="Liked users">
                                                            <img src="./assets/images/users/user-12.png" alt="Liked users">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="quick-media">
                                                <div class="media-overlay"></div>
                                                <a href="#" class="quick-media-img"><img src="./assets/images/users/album/album-3.jpg" alt="Quick media"></a>
                                                <div class="media-overlay-content">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="media-overlay-owner">
                                                            <img src="./assets/images/users/user-12.png" alt="Media owner image">
                                                            <span class="overlay-owner-name fs-9">Irwin M. Spelle</span>
                                                        </div>
                                                        <div class="dropdown">
                                                            <a href="#" class="overlay-more" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class='bx bx-dots-horizontal-rounded'></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right nav-drop dropdown-shadow">
                                                                <a class="dropdown-item" href="#">Save post</a>
                                                                <a class="dropdown-item" href="#">Turn on notifications</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="overlay-bottom d-flex justify-content-between align-items-center">
                                                        <div class="argon-reaction">
                                                            <span class="like-btn">
                                                                <a href="#" class="post-card-buttons" id="reactions"><i class='bx bxs-like mr-1'></i> 67</a>
                                                                <ul class="reactions-box dropdown-shadow">
                                                                    <li class="reaction reaction-like" data-reaction="Like"></li>
                                                                    <li class="reaction reaction-love" data-reaction="Love"></li>
                                                                    <li class="reaction reaction-haha" data-reaction="HaHa"></li>
                                                                    <li class="reaction reaction-wow" data-reaction="Wow"></li>
                                                                    <li class="reaction reaction-sad" data-reaction="Sad"></li>
                                                                    <li class="reaction reaction-angry" data-reaction="Angry"></li>
                                                                </ul>
                                                            </span>
                                                        </div>
                                                        <div class="liked-users">
                                                            <img src="./assets/images/users/user-9.png" alt="Liked users">
                                                            <img src="./assets/images/users/user-6.png" alt="Liked users">
                                                            <img src="./assets/images/users/user-12.png" alt="Liked users">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="newMessageModal" tabindex="-1" role="dialog" aria-labelledby="newMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header new-msg-header">
                    <h5 class="modal-title" id="newMessageModalLabel">Start new conversation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body new-msg-body">
                    <form action="" method="" class="new-msg-form">
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Message:</label>
                            <textarea class="form-control search-input" rows="5" id="message-text" placeholder="Type a message..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer new-msg-footer">
                    <button type="button" class="btn btn-primary btn-sm">Send message</button>
                </div>
            </div>
        </div>
    </div>

    
    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/components/components.js"></script>
    
    <script>
        // Track liked publications in local storage
        var likedPublications = JSON.parse(localStorage.getItem('likedPublications') || '[]');
        
        // Like publication handler
        $(document).on('click', '.like-publication', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const publicationId = button.data('id');
            const likeCountSpan = button.find('.like-count-' + publicationId);
            
            // Check if already liked
            if (likedPublications.includes(publicationId)) {
                alert('Vous avez déjà aimé cette publication !');
                return;
            }
            
            // Send AJAX request to increment likes
            $.ajax({
                url: 'like_publication.php',
                method: 'POST',
                data: { idPublication: publicationId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update like count
                        likeCountSpan.text(response.nbLikes);
                        
                        // Mark as liked
                        likedPublications.push(publicationId);
                        localStorage.setItem('likedPublications', JSON.stringify(likedPublications));
                        
                        // Visual feedback
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
        
        // Mark already liked publications on page load
        $(document).ready(function() {
            likedPublications.forEach(function(id) {
                var btn = $('.like-publication[data-id="' + id + '"]');
                btn.find('i').removeClass('bx-like').addClass('bxs-like');
                btn.css('color', '#007bff');
            });
        });
    </script>
</body></html>
