<?php 
session_start();

// Charger config d'abord une seule fois
include_once '../../config.php';
include_once '../../Controller/tacheC.php';
include_once '../../Controller/planteC.php';


// Initialisation
$planteC = new planteC();
$tacheController = new tacheC();

// À des fins de test: simuler l'ID utilisateur 18 connecté
if (!isset($_SESSION['idUtilisateur'])) {
    $_SESSION['idUtilisateur'] = 18;
}

$userId = $_SESSION['idUtilisateur']; 

// Récupérer les plantes de l'utilisateur
$mesPlantes = $planteC->listPlantesByUser($userId);

// Récupérer toutes les tâches
$listTaches = $tacheController->listTaches();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>SmartGarden</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.1/css/boxicons.min.css' rel='stylesheet'>

    <!-- Styles -->
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/components.css" rel="stylesheet">
    <link href="./assets/css/media.css" rel="stylesheet">
    <link href="./assets/css/chat.css" rel="stylesheet">
    <link href="https://vjs.zencdn.net/7.4.1/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
</head>

<body class="newsfeed">
    <!--
    <div class="spinner d-flex align-items-center justify-content-center" id="loader">
        <div class="row">
            <div class="col-md-12">
                <img src="./assets/images/logo-128x128.png" class="mb-5" alt="Loader image">
            </div>
            <div class="col-md-12 loader">
                <div class="bounce bounce1"></div>
                <div class="bounce bounce2"></div>
                <div class="bounce bounce3"></div>
            </div>
        </div>
    </div>
-->
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
                            <a class="navbar-brand nav-item mr-lg-5" href="index.php"><img src="./assets/images/logo-128x128.png" width="70" height="70" class="mr-3" alt="Logo"></a>
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <form class="w-30 mx-2 my-auto d-inline form-inline mr-5">
                                <div class="input-group">
                                    <input type="text" class="form-control search-input w-75" placeholder="Search for people, companies, events and more..." aria-label="Search" aria-describedby="search-addon">
                                    <div class="input-group-append">
                                        <button class="btn search-button" type="button"><i class='bx bx-search'></i></button>
                                    </div>
                                </div>
                            </form>
                            <li class="nav-item s-nav dropdown d-mobile">
                                <a href="#" class="nav-link nav-icon nav-links drop-w-tooltip" data-toggle="dropdown" data-placement="bottom" data-title="Create" role="button" aria-haspopup="true" aria-expanded="false">
                                    <img src="./assets/images/icons/navbar/create.png" alt="navbar icon">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right nav-dropdown-menu">
                                    <a href="#" class="dropdown-item" aria-describedby="createGroup">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <i class='bx bx-group post-option-icon'></i>
                                            </div>
                                            <div class="col-md-10">
                                                <span class="fs-9">Group</span>
                                                <small id="createGroup" class="form-text text-muted">Find people with shared interests</small>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="dropdown-item" aria-describedby="createEvent">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <i class='bx bx-calendar post-option-icon'></i>
                                            </div>
                                            <div class="col-md-10">
                                                <span class="fs-9">Event</span>
                                                <small id="createEvent" class="form-text text-muted">bring people together with a public or private event</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item s-nav dropdown message-drop-li">
                                <a href="#" class="nav-link nav-links message-drop drop-w-tooltip" data-toggle="dropdown" data-placement="bottom" data-title="Messages" role="button" aria-haspopup="true" aria-expanded="false">
                                    <img src="./assets/images/icons/navbar/message.png" class="message-dropdown" alt="navbar icon"> <span class="badge badge-pill badge-primary">1</span>
                                </a>
                                <ul class="dropdown-menu notify-drop dropdown-menu-right nav-drop shadow-sm">
                                    <div class="notify-drop-title">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-6 fs-8">Messages | <a href="#">Requests</a></div>
                                            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                                                <a href="#" class="notify-right-icon">
                                                    Mark All as Read
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end notify title -->
                                    <!-- notify content -->
                                    <div class="drop-content">
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-6.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Susan P. Jarvis</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    <i class='bx bx-check'></i> This party is going to have a DJ, food, and drinks.
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-5.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Ruth D. Greene</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    Great, I’ll see you tomorrow!.
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-7.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Kimberly R. Hatfield</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    yeah, I will be there.
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-8.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Joe S. Feeney</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    I would really like to bring my friend Jake, if...
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-9.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">William S. Willmon</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    Sure, what can I help you with?
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-10.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Sean S. Smith</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    Which of those two is best?
                                                </p>
                                            </div>
                                        </li>
                                    </div>
                                    <div class="notify-drop-footer text-center">
                                        <a href="#">See More</a>
                                    </div>
                                </ul>
                            </li>
                            <li class="nav-item s-nav dropdown notification">
                                <a href="#" class="nav-link nav-links rm-drop-mobile drop-w-tooltip" data-toggle="dropdown" data-placement="bottom" data-title="Notifications" role="button" aria-haspopup="true" aria-expanded="false">
                                    <img src="./assets/images/icons/navbar/notification.png" class="notification-bell" alt="navbar icon"> <span class="badge badge-pill badge-primary">3</span>
                                </a>
                                <ul class="dropdown-menu notify-drop dropdown-menu-right nav-drop shadow-sm">
                                    <div class="notify-drop-title">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-6 fs-8">Notifications <span class="badge badge-pill badge-primary ml-2">3</span></div>
                                            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                                                <a href="#" class="notify-right-icon">
                                                    Mark All as Read
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end notify title -->
                                    <!-- notify content -->
                                    <div class="drop-content">
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-10.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Sean</a> <span class="notification-type">replied to your comment on a post in </span><a href="#" class="notification-for">PHP</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    <span class="badge badge-pill badge-primary"><i class='bx bxs-group'></i></span> 3h
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-7.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Kimberly</a> <span class="notification-type">likes your comment "I would really... </span>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    <span class="badge badge-pill badge-primary"><i class='bx bxs-like'></i></span> 7h
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-8.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <span class="notification-type">10 people saw your story before it disappeared. See who saw it.</span>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    <span class="badge badge-pill badge-primary"><i class='bx bx-images'></i></span> 23h
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-11.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Michelle</a> <span class="notification-type">posted in </span><a href="#" class="notification-for">Argon Social Design System</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    <span class="badge badge-pill badge-primary"><i class='bx bxs-quote-right'></i></span> 1d
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-5.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Karen</a> <span class="notification-type">likes your comment "Sure, here... </span>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    <span class="badge badge-pill badge-primary"><i class='bx bxs-like'></i></span> 2d
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col-md-2 col-sm-2 col-xs-2">
                                                <div class="notify-img">
                                                    <img src="./assets/images/users/user-12.png" alt="notification user image">
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-10">
                                                <a href="#" class="notification-user">Irwin</a> <span class="notification-type">posted in </span><a href="#" class="notification-for">Themeforest</a>
                                                <a href="#" class="notify-right-icon">
                                                    <i class='bx bx-radio-circle-marked'></i>
                                                </a>
                                                <p class="time">
                                                    <span class="badge badge-pill badge-primary"><i class='bx bxs-quote-right'></i></span> 3d
                                                </p>
                                            </div>
                                        </li>
                                    </div>
                                    <div class="notify-drop-footer text-center">
                                        <a href="#">See More</a>
                                    </div>
                                </ul>
                            </li>
                            <li class="nav-item s-nav dropdown d-mobile">
                                <a href="#" class="nav-link nav-links nav-icon drop-w-tooltip" data-toggle="dropdown" data-placement="bottom" data-title="Pages" role="button" aria-haspopup="true" aria-expanded="false">
                                    <img src="./assets/images/icons/navbar/flag.png" alt="navbar icon">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right nav-drop">
                                    <a class="dropdown-item" href="publications.php">Publications</a>
                                    <a class="dropdown-item" href="sign-in.php">Sign in</a>
                                    <a class="dropdown-item" href="sign-up.php">Sign up</a>
                                </div>
                            </li>
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
                    <div class="col-md-2 newsfeed-left-side sticky-top shadow-sm" id="sidebar-wrapper">
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
                                <li class="list-group-item d-flex justify-content-between align-items-center sd-active">
                                    <a href="plantes.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/group.png" alt="plantes"> Plantes</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="evenements.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/event.png" alt="evenements"> Evenements</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="capteurs.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/saved.png" alt="capteurs"> Capteurs</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-10 second-section" id="page-content-wrapper">
                        

                        <!-- Mes Plantes Section -->
                         <div class="container-fluid shadow-sm rounded-4 p-4 mt-4 mb-4">

                        
    <div class="mb-4">
        <h4 class="mb-2"><i class='bx bx-leaf me-2' style="color: #2575fc;"></i>Mes Plantes</h4>
        <p class="text-muted small">Consultez vos plantes et leurs informations.</p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0" style="border-radius: 12px; overflow: hidden;">
            <thead style="background-color: #e0f0ff; color: #1a1a1a; font-weight: 600;">
                <tr>
                    <th scope="col" class="text-center">#</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Date d'ajout</th>
                    <th scope="col" class="text-center">Humidité</th>
                    <th scope="col" class="text-center">Eau</th>
                    <th scope="col" class="text-center">État</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($mesPlantes) == 0) {
                    echo '<tr><td colspan="6" class="text-center py-5">';
                    echo '<div>';
                    echo '<i class="bx bx-leaf" style="font-size: 3rem; color: #cfd8e0;"></i>';
                    echo '<p class="text-muted mt-3 mb-0">Vous n\'avez pas encore de plantes</p>';
                    echo '</div>';
                    echo '</td></tr>';
                } else {
                    $idx = 1;
                    foreach($mesPlantes as $plante) {
                        $hum = $plante['niveau_humidite'];
                        $humColor = ($hum > 70) ? 'bg-success text-dark' : (($hum > 40) ? 'bg-warning text-dark' : 'bg-danger text-white');

                        if ($plante['etat_sante'] == 'Bon état') {
                            $etatBadge = '<span class="badge bg-primary"><i class="bx bx-check-circle"></i> Bon</span>';
                        } else if ($plante['etat_sante'] == 'Moyen') {
                            $etatBadge = '<span class="badge bg-warning text-dark"><i class="bx bx-minus"></i> Moyen</span>';
                        } else {
                            $etatBadge = '<span class="badge bg-danger"><i class="bx bx-x-circle"></i> Mauvais</span>';
                        }

                        echo '<tr class="align-middle border-bottom">';
                        echo '<td class="text-center text-muted small">'.$idx.'</td>';
                        echo '<td><strong class="text-dark">'.htmlspecialchars($plante['nom_plante']).'</strong></td>';
                        echo '<td><small class="text-muted">'.htmlspecialchars($plante['date_ajout']).'</small></td>';
                        echo '<td class="text-center"><span class="badge '.$humColor.'">'.$hum.'%</span></td>';
                        echo '<td class="text-center"><small class="text-muted">'.$plante['besoin_eau'].' ml</small></td>';
                        echo '<td class="text-center">'.$etatBadge.'</td>';
                        echo '</tr>';
                        $idx++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    table.table-hover tbody tr:hover {
        background-color: rgba(37, 117, 252, 0.1);
    }
</style>

<!-----Plantees------->


<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded-4 shadow-sm p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="fw-bold mb-0">Gestion des Tâches</h5>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead style="background: #e0f0ff; color: #1a1a1a; font-weight: 600;">
    <tr>
        <th>ID Tâche</th>
        <th>Type de Dosage</th>
        <th>Quantité</th>
        <th>Mode</th>
        <th>Date</th>
        <th>Dernière Exécution</th>
        <th>Prochaine Exécution</th>
        <th>État</th>
        <th>Priorité</th>
        <th>ID Plante</th>
    </tr>
</thead>

                <tbody>
                    <?php if(!empty($listTaches)): ?>
                        <?php foreach($listTaches as $dosage): ?>
                            <tr class="align-middle">
                                <td><?= $dosage['id_dosage'] ?></td>
                                <td><?= htmlspecialchars($dosage['type_dosage']) ?></td>
                                <td><?= $dosage['quantite'] ?></td>
                                <td><?= htmlspecialchars($dosage['mode_dosage']) ?></td>
                                <td><?= $dosage['date_dosage'] ?></td>
                                <td><?= $dosage['derniereExecution'] ?></td>
                                <td><?= $dosage['prochaineExecution'] ?></td>
                                <td>
                                    <?php 
                                        if($dosage['estComplete'] == 0) echo '<span class="badge bg-secondary">Non commencé</span>';
                                        elseif($dosage['estComplete'] == 1) echo '<span class="badge bg-warning text-dark">En cours</span>';
                                        else echo '<span class="badge bg-success">Complète</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        if($dosage['priorite'] == 1) echo '<span class="text-success">Basse</span>';
                                        elseif($dosage['priorite'] == 2) echo '<span class="text-warning">Moyenne</span>';
                                        else echo '<span class="text-danger fw-bold">Haute</span>';
                                    ?>
                                </td>
                                <td><?= $dosage['id_plante'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">Aucune tâche trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal stylé -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel">Modal Title</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Contenu de la modal ici...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-gradient btn-sm rounded-pill">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Bouton gradient personnalisé */
    .btn-gradient {
        background: linear-gradient(45deg, #6a11cb, #2575fc);
        color: white;
        border: none;
        transition: all 0.2s;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.25);
    }

    /* Table hover */
    table.table-hover tbody tr:hover {
        background-color: rgba(37, 117, 252, 0.1);
    }

    /* Modal gradient header */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
    }
</style>


    <!-- Core -->
    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    <!-- Optional -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script type="text/javascript">
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

    </script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/components/components.js"></script>
</body>

</html>
