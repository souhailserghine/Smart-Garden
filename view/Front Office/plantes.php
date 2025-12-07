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

// Créer une map des plantes pour accès rapide au nom
$plantesMap = [];
foreach($mesPlantes as $plante) {
    $plantesMap[$plante['id_plante']] = $plante['nom_plante'];
}

// Récupérer toutes les tâches
$listTaches = $tacheController->listTaches();

// Calculer les statistiques pour l'utilisateur
$totalPlantes = count($mesPlantes);
$today = date('Y-m-d');
$plantesToday = 0;
foreach($mesPlantes as $p){
    if($p['date_ajout'] === $today) $plantesToday++;
}

// Pour les tâches de l'utilisateur (filtrer par ses plantes)
$plantesIds = array_column($mesPlantes, 'id_plante');
$tachesUtilisateur = [];
$tachesToday = 0;
foreach($listTaches as $t){
    if(in_array($t['id_plante'], $plantesIds)) {
        $tachesUtilisateur[] = $t;
        if($t['date_dosage'] === $today) $tachesToday++;
    }
}
$totalTaches = count($tachesUtilisateur);

// Statistiques avancées
$tachesCompletees = 0;
$tachesEnCours = 0;
$plantes_etat_bon = 0;
$plantes_etat_moyen = 0;
$plantes_etat_mauvais = 0;
$taches_priorite_haute = 0;
$taches_priorite_moyenne = 0;
$taches_priorite_basse = 0;

foreach($tachesUtilisateur as $t) {
    if($t['estComplete'] == 1) $tachesCompletees++;
    else $tachesEnCours++;
    
    if($t['priorite'] == 'Élevée' || $t['priorite'] == 3) $taches_priorite_haute++;
    elseif($t['priorite'] == 'Moyen' || $t['priorite'] == 2) $taches_priorite_moyenne++;
    else $taches_priorite_basse++;
}

foreach($mesPlantes as $p) {
    if($p['etat_sante'] == 'Bon état') $plantes_etat_bon++;
    elseif($p['etat_sante'] == 'Moyen') $plantes_etat_moyen++;
    else $plantes_etat_mauvais++;
}

?>

<!DOCTYPE html>
<html lang="en">>

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
    
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    
    <!-- Toast Notification System -->
    <script src="./assets/js/toast-notification.js"></script>
    
    <style>
        /* Chatbot styles */
        .chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 380px;
            max-height: 600px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 40px rgba(0,0,0,0.16);
            display: flex;
            flex-direction: column;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .chatbot-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chatbot-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .chatbot-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 20px;
            padding: 0;
        }
        
        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .chatbot-message {
            display: flex;
            gap: 10px;
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .message-user {
            justify-content: flex-end;
        }
        
        .message-bubble {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .message-bubble.user {
            background: #667eea;
            color: white;
            border-radius: 12px 12px 2px 12px;
        }
        
        .message-bubble.ai {
            background: #f0f0f0;
            color: #333;
            border-radius: 12px 12px 12px 2px;
        }
        
        .message-bubble.ai strong {
            color: #667eea;
        }
        
        .chatbot-input-area {
            padding: 12px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 8px;
        }
        
        .chatbot-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        
        .chatbot-input:focus {
            border-color: #667eea;
        }
        
        .chatbot-send {
            background: #667eea;
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        
        .chatbot-send:hover {
            background: #764ba2;
        }
        
        .chatbot-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            z-index: 9998;
            transition: transform 0.2s;
        }
        
        .chatbot-toggle:hover {
            transform: scale(1.1);
        }
        
        .chatbot-toggle.hidden {
            display: none;
        }
        
        @media (max-width: 480px) {
            .chatbot-container {
                width: calc(100% - 20px);
                max-height: 400px;
            }
        }
    </style>
</head>

<body class="newsfeed" style="background: transparent;">
    <!-- Subtle Background Pattern & Decorations -->
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; opacity: 1; z-index: -1; background: linear-gradient(135deg, rgba(245,247,250,0.98) 0%, rgba(232,240,247,0.98) 100%); pointer-events: none;"></div>
    
    <!-- Animated Gradient Blobs -->
    <div style="position: fixed; top: -50px; right: -50px; width: 350px; height: 350px; background: radial-gradient(circle, rgba(46, 204, 113, 0.08) 0%, transparent 70%); border-radius: 50%; z-index: -1; pointer-events: none; animation: float 6s ease-in-out infinite;"></div>
    <div style="position: fixed; bottom: -100px; left: -100px; width: 450px; height: 450px; background: radial-gradient(circle, rgba(52, 152, 219, 0.08) 0%, transparent 70%); border-radius: 50%; z-index: -1; pointer-events: none; animation: float-reverse 8s ease-in-out infinite;"></div>
    
    <!-- SVG Pattern Overlay -->
    <svg style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none; opacity: 0.03;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800">
        <defs>
            <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#000" stroke-width="0.5"/>
            </pattern>
        </defs>
        <rect width="1200" height="800" fill="url(#grid-pattern)"/>
    </svg>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(-20px) translateX(-10px); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(20px) translateX(10px); }
        }
    </style>
    
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
                        

                        <!-- Statistiques Start -->
                        <div class="container-fluid pt-2 px-0">
                            <div class="row g-3 mb-4">
                                <!-- Total Plantes -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="d-flex align-items-center p-4 rounded-3 shadow-sm" 
                                         style="background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); gap: 20px;">
                                        <img src="../image/plant.png" width="50" height="50" class="flex-shrink-0" alt="Plante" style="object-fit: contain;">
                                        <div style="flex-grow: 1;">
                                            <h6 class="mb-2" style="font-size: 0.85rem; color: #999; font-weight: 500;">Total Plantes</h6>
                                            <h4 class="mb-0" style="font-size: 1.8rem; color: #2ecc71; font-weight: bold;"><?= $totalPlantes ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plantes ajoutées aujourd'hui -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="d-flex align-items-center p-4 rounded-3 shadow-sm" 
                                         style="background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); gap: 20px;">
                                        <img src="../image/day-to-plant-a-tree-reminder-daily-calendar-page-interface-symbol.png" width="50" height="50" class="flex-shrink-0" alt="Plante aujourd'hui" style="object-fit: contain;">
                                        <div style="flex-grow: 1;">
                                            <h6 class="mb-2" style="font-size: 0.85rem; color: #999; font-weight: 500;">Plantes aujourd'hui</h6>
                                            <h4 class="mb-0" style="font-size: 1.8rem; color: #2ecc71; font-weight: bold;"><?= $plantesToday ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Tâches -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="d-flex align-items-center p-4 rounded-3 shadow-sm" 
                                         style="background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); gap: 20px;">
                                        <img src="../image/clipboard.png" width="50" height="50" class="flex-shrink-0" alt="Tâches" style="object-fit: contain;">
                                        <div style="flex-grow: 1;">
                                            <h6 class="mb-2" style="font-size: 0.85rem; color: #999; font-weight: 500;">Total Tâches</h6>
                                            <h4 class="mb-0" style="font-size: 1.8rem; color: #2ecc71; font-weight: bold;"><?= $totalTaches ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tâches prévues aujourd'hui -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="d-flex align-items-center p-4 rounded-3 shadow-sm" 
                                         style="background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); gap: 20px;">
                                        <img src="../image/search.png" width="50" height="50" class="flex-shrink-0" alt="Tâches aujourd'hui" style="object-fit: contain;">
                                        <div style="flex-grow: 1;">
                                            <h6 class="mb-2" style="font-size: 0.85rem; color: #999; font-weight: 500;">Tâches aujourd'hui</h6>
                                            <h4 class="mb-0" style="font-size: 1.8rem; color: #2ecc71; font-weight: bold;"><?= $tachesToday ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Statistiques End -->

                        <!-- Suggestions de Tâches Start -->
                        <div class="container-fluid pt-4 px-0">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, rgba(46, 204, 113, 0.95) 0%, rgba(52, 152, 219, 0.95) 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 8px 25px rgba(46, 204, 113, 0.2); backdrop-filter: blur(10px);">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h5 class="mb-2" style="font-weight: 700; font-size: 1.2rem;">💡 Suggestion du moment</h5>
                                                <p class="mb-0" id="taskSuggestion" style="font-size: 1.05rem; font-weight: 500; min-height: 30px;">Chargement des suggestions...</p>
                                            </div>
                                            <button class="btn btn-light btn-sm rounded-pill px-4" onclick="loadNextSuggestion()" style="font-weight: 600;">
                                                <i class="bx bx-refresh me-2"></i>Suivant
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Suggestions de Tâches End -->

                        <script>
                        // Suggestions dynamiques de tâches
                        const taskSuggestions = [
                            { icon: '💧', text: 'Arroser vos plantes - Elles ont besoin d\'eau!' },
                            { icon: '🌱', text: 'Ajouter une nouvelle plante à votre collection' },
                            { icon: '✂️', text: 'Tailler les feuilles mortes et les branches' },
                            { icon: '🍃', text: 'Rempoter une plante qui devient trop grande' },
                            { icon: '🌞', text: 'Vérifier l\'exposition au soleil de vos plantes' },
                            { icon: '🔍', text: 'Inspecter les feuilles pour détecter des maladies' },
                            { icon: '🧴', text: 'Nettoyer les feuilles avec un chiffon humide' },
                            { icon: '📊', text: 'Vérifier l\'humidité du sol de chaque plante' },
                            { icon: '🎯', text: 'Ajuster la température pour vos plantes' },
                            { icon: '📋', text: 'Créer un planning d\'entretien régulier' },
                            { icon: '🌿', text: 'Ajouter du compost pour fertiliser le sol' },
                            { icon: '💪', text: 'Déplacer une plante vers un meilleur endroit' },
                            { icon: '🏥', text: 'Traiter une plante présentant des signes de faiblesse' },
                            { icon: '📱', text: 'Mettre à jour l\'état de santé de vos plantes' },
                            { icon: '🎨', text: 'Documenter la croissance de vos plantes avec une photo' }
                        ];

                        let currentSuggestionIndex = Math.floor(Math.random() * taskSuggestions.length);

                        function displaySuggestion() {
                            const suggestion = taskSuggestions[currentSuggestionIndex];
                            document.getElementById('taskSuggestion').innerHTML = 
                                '<span style="font-size: 1.3rem; margin-right: 10px;">' + suggestion.icon + '</span>' + suggestion.text;
                        }

                        function loadNextSuggestion() {
                            currentSuggestionIndex = (currentSuggestionIndex + 1) % taskSuggestions.length;
                            displaySuggestion();
                            // Petite animation
                            const elem = document.getElementById('taskSuggestion');
                            elem.style.animation = 'none';
                            setTimeout(() => {
                                elem.style.animation = 'fadeIn 0.3s ease-in';
                            }, 10);
                        }

                        // Afficher une suggestion au chargement
                        document.addEventListener('DOMContentLoaded', () => {
                            displaySuggestion();
                            // Change suggestion toutes les 30 secondes
                            setInterval(() => {
                                loadNextSuggestion();
                            }, 30000);
                            
                            // Afficher les notifications flash s'il y en a
                            <?php if (isset($_SESSION['successMsg'])): ?>
                                toastManager.success('✓ Succès!', '<?= addslashes($_SESSION['successMsg']) ?>');
                                <?php unset($_SESSION['successMsg']); ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['errorMsg'])): ?>
                                toastManager.error('✕ Erreur!', '<?= addslashes($_SESSION['errorMsg']) ?>');
                                <?php unset($_SESSION['errorMsg']); ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['infoMsg'])): ?>
                                toastManager.info('ℹ Info', '<?= addslashes($_SESSION['infoMsg']) ?>');
                                <?php unset($_SESSION['infoMsg']); ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['warningMsg'])): ?>
                                toastManager.warning('⚠ Attention', '<?= addslashes($_SESSION['warningMsg']) ?>');
                                <?php unset($_SESSION['warningMsg']); ?>
                            <?php endif; ?>
                        });
                        </script>

                         <div class="container-fluid shadow-sm rounded-4 p-4 mt-4 mb-4">

                        
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-2"><i class='bx bx-leaf me-2' style="color: #2575fc;"></i>Mes Plantes</h4>
            <p class="text-muted small">Consultez vos plantes et leurs informations.</p>
        </div>
        <div>
            <button class="btn btn-success rounded-pill px-4 me-2" id="btnSuggestionPlante" title="Suggérer une plante">
                <i class='bx bx-plus me-2'></i>Suggérer une plante
            </button>
            <button class="btn btn-info rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#statsAdvancedModal">
                <i class='bx bx-bar-chart-alt-2 me-2'></i>Statistiques
            </button>
        </div>
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
            <button class="btn btn-info rounded-pill px-4 me-2" id="btnSuggestionTache" title="Suggérer une tâche">
                <i class='bx bx-plus me-2'></i>Suggérer une tâche
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead style="background: #e0f0ff; color: #1a1a1a; font-weight: 600;">
    <tr>
        <th>Type de Dosage</th>
        <th>Quantité</th>
        <th>Mode</th>
        <th>Date</th>
        <th>Dernière Exécution</th>
        <th>Prochaine Exécution</th>
        <th>État</th>
        <th>Priorité</th>
        <th>Plante</th>
        <th>Action</th>
    </tr>
</thead>

                <tbody>
                    <?php if(!empty($listTaches)): ?>
                        <?php foreach($listTaches as $dosage): ?>
                            <tr class="align-middle">
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
                                        if($dosage['priorite'] == 'Faible' || $dosage['priorite'] == 1) echo '<span class="badge bg-success">Faible</span>';
                                        elseif($dosage['priorite'] == 'Moyen' || $dosage['priorite'] == 2) echo '<span class="badge bg-warning text-dark">Moyen</span>';
                                        else echo '<span class="badge bg-danger">Élevée</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        $planteName = isset($plantesMap[$dosage['id_plante']]) ? htmlspecialchars($plantesMap[$dosage['id_plante']]) : 'N/A';
                                        echo $dosage['id_plante'] . ' - ' . $planteName;
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm rounded-pill" onclick="afficherDetailsTache(<?= htmlspecialchars(json_encode($dosage)) ?>)">
                                        <i class='bx bx-show'></i> Voir
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Aucune tâche trouvée</td>
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

<!-- Modal Suggérer une plante -->
<div class="modal fade" id="addSuggestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(245,247,255,0.98)); border-radius: 15px; overflow: hidden;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0; padding: 25px; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);">
                <h5 class="modal-title text-white fw-bold" style="font-size: 1.3rem; margin: 0;">
                    <i class='bx bx-leaf me-2' style="font-size: 1.5rem;"></i>Suggérer une plante
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(245,247,255,0.98));">
                <form id="suggestionForm" enctype="multipart/form-data">
                    <!-- Nom de la plante -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-leaf' style="color: #667eea; margin-right: 8px; font-size: 1.1rem;"></i>Nom de la plante <span class="text-danger ms-1">*</span>
                        </label>
                        <input type="text" class="form-control" id="nomPlante" name="nom_plante" 
                               placeholder="Rose, Monstera, Orchidée..." required
                               style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white;">
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Le nom de la plante que vous souhaitez suggérer</small>
                    </div>
                    
                    <!-- Type de plante -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-category' style="color: #667eea; margin-right: 8px; font-size: 1.1rem;"></i>Type de plante <span class="text-danger ms-1">*</span>
                        </label>
                        <input type="text" class="form-control" name="type_plante" 
                               placeholder="Succulente, Orchidée, Fougère..." required
                               style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white;">
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Catégorie ou famille de la plante</small>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-align-left' style="color: #667eea; margin-right: 8px; font-size: 1.1rem;"></i>Description
                        </label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Décrivez les caractéristiques, les soins nécessaires..."
                               style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white; resize: vertical;"></textarea>
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Informations supplémentaires sur la plante</small>
                    </div>
                    
                    <!-- Niveau d'humidité avec slider -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center; margin-bottom: 12px;">
                            <i class='bx bx-water' style="color: #667eea; margin-right: 8px; font-size: 1.1rem;"></i>Niveau d'humidité
                        </label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="range" class="form-range" id="niveauHumiditeRange" name="niveau_humidite_range" 
                                   min="0" max="100" value="50"
                                   style="flex: 1; height: 8px; cursor: pointer; border-radius: 10px;">
                            <span id="humiditeValue" style="min-width: 70px; padding: 8px 14px; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; font-size: 0.9rem; text-align: center;">50%</span>
                        </div>
                        <input type="hidden" id="niveau_humidite" name="niveau_humidite" value="50">
                        <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 0.8rem; color: #999;">
                            <span>🏜️ Sec</span>
                            <span>💧 Humide</span>
                        </div>
                    </div>
                    
                    <!-- Besoin en eau -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-droplet' style="color: #667eea; margin-right: 8px; font-size: 1.1rem;"></i>Besoin en eau
                        </label>
                        <div class="input-group" style="border-radius: 10px; overflow: hidden;">
                            <input type="number" class="form-control" id="besoin_eau" name="besoin_eau" 
                                   min="0" max="50" step="0.5" value="0" placeholder="0.5"
                                   style="border: 2px solid #e0e0e0; border-radius: 10px 0 0 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem;">
                            <span class="input-group-text" style="border: 2px solid #e0e0e0; border-radius: 0 10px 10px 0; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); color: #667eea; font-weight: 600; border-left: none;">L/jour</span>
                        </div>
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Quantité moyenne d'eau par jour</small>
                    </div>
                    
                    <!-- État de santé -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-heart' style="color: #667eea; margin-right: 8px; font-size: 1.1rem;"></i>État de santé
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 12px;">
                            <label style="cursor: pointer; padding: 14px 12px; border: 2px solid #e0e0e0; border-radius: 10px; text-align: center; transition: all 0.3s ease; background: white; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <input type="radio" name="etat_sante" value="Bon état" checked style="accent-color: #667eea; cursor: pointer; transform: scale(1.2);">
                                <span style="font-size: 0.9rem; color: #333; font-weight: 500;">✓ Bon</span>
                            </label>
                            <label style="cursor: pointer; padding: 14px 12px; border: 2px solid #e0e0e0; border-radius: 10px; text-align: center; transition: all 0.3s ease; background: white; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <input type="radio" name="etat_sante" value="Moyen" style="accent-color: #667eea; cursor: pointer; transform: scale(1.2);">
                                <span style="font-size: 0.9rem; color: #333; font-weight: 500;">~ Moyen</span>
                            </label>
                            <label style="cursor: pointer; padding: 14px 12px; border: 2px solid #e0e0e0; border-radius: 10px; text-align: center; transition: all 0.3s ease; background: white; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <input type="radio" name="etat_sante" value="Mauvais état" style="accent-color: #667eea; cursor: pointer; transform: scale(1.2);">
                                <span style="font-size: 0.9rem; color: #333; font-weight: 500;">✗ Mauvais</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Température -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-thermometer' style="color: #667eea; margin-right: 8px; font-size: 1.1rem;"></i>Température idéale (°C)
                        </label>
                        <div class="input-group" style="border-radius: 10px; overflow: hidden;">
                            <input type="number" class="form-control" name="temperature" 
                                   min="0" max="50" step="0.5" value="20" placeholder="20"
                                   style="border: 2px solid #e0e0e0; border-radius: 10px 0 0 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem;">
                            <span class="input-group-text" style="border: 2px solid #e0e0e0; border-radius: 0 10px 10px 0; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); color: #667eea; font-weight: 600; border-left: none;">°C</span>
                        </div>
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Température moyenne pour cette plante</small>
                    </div>
                    
                    <!-- Image -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-image' style="color: #667eea; margin-right: 8px; font-size: 1.1rem;"></i>Photo de la plante
                        </label>
                        <input type="file" class="form-control" name="image" accept="image/*"
                               style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white;">
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">JPG, PNG (max 5MB)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-5" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(245,247,255,0.98)); border-radius: 0 0 15px 15px; gap: 10px; border-top: 1px solid #e0e0e0;">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-5" data-bs-dismiss="modal" style="border: 2px solid #ddd; color: #666; transition: all 0.3s; font-weight: 500;">Annuler</button>
                <button type="button" class="btn rounded-pill px-5" id="submitSuggestion" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);">
                    <i class='bx bx-check me-2'></i>Soumettre
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #addSuggestionModal .form-control:focus,
    #addSuggestionModal .form-range:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
        outline: none;
    }
    
    #addSuggestionModal .form-range {
        accent-color: #667eea;
    }
    
    #addSuggestionModal input[type="radio"] {
        accent-color: #667eea;
        cursor: pointer;
    }
    
    #addSuggestionModal label:has(input[type="radio"]:checked) {
        border-color: #667eea !important;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)) !important;
        transform: scale(1.02);
    }
    
    #submitSuggestion:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
    }
    
    #submitSuggestion:active {
        transform: translateY(-1px);
    }
</style>

<!-- Modal Suggérer une tâche -->
<div class="modal fade" id="addSuggestionTacheModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(245,247,255,0.98)); border-radius: 15px; overflow: hidden;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #17a2b8 0%, #0c5460 100%); border-radius: 15px 15px 0 0; padding: 25px; box-shadow: 0 5px 15px rgba(23, 162, 184, 0.2);">
                <h5 class="modal-title text-white fw-bold" style="font-size: 1.3rem; margin: 0;">
                    <i class='bx bx-task me-2' style="font-size: 1.5rem;"></i>Suggérer une tâche
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(245,247,255,0.98));">
                <form id="suggestionTacheForm" enctype="multipart/form-data">
                    <!-- Type de dosage -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-pill' style="color: #17a2b8; margin-right: 8px; font-size: 1.1rem;"></i>Type de dosage <span class="text-danger ms-1">*</span>
                        </label>
                        <input type="text" class="form-control" name="type_dosage" 
                               placeholder="Arrosage, Fertilisant, Traitement..." required
                               style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white;">
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Le type de tâche à effectuer</small>
                    </div>
                    
                    <!-- Sélection de la plante -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-leaf' style="color: #17a2b8; margin-right: 8px; font-size: 1.1rem;"></i>Plante <span class="text-danger ms-1">*</span>
                        </label>
                        <select class="form-select" id="id_plante" name="id_plante" required
                                style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white;">
                            <option value="">-- Choisir une plante --</option>
                        </select>
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Sélectionnez la plante pour laquelle cette tâche s'applique</small>
                    </div>
                    
                    <!-- Quantité -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-package' style="color: #17a2b8; margin-right: 8px; font-size: 1.1rem;"></i>Quantité
                        </label>
                        <div class="input-group" style="border-radius: 10px; overflow: hidden;">
                            <input type="number" class="form-control" name="quantite" 
                                   min="0" max="1000" value="0" placeholder="0"
                                   style="border: 2px solid #e0e0e0; border-radius: 10px 0 0 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem;">
                            <span class="input-group-text" style="border: 2px solid #e0e0e0; border-radius: 0 10px 10px 0; background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(12, 84, 96, 0.1)); color: #17a2b8; font-weight: 600; border-left: none;">ml/g</span>
                        </div>
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Quantité à utiliser</small>
                    </div>
                    
                    <!-- Mode de dosage -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-water' style="color: #17a2b8; margin-right: 8px; font-size: 1.1rem;"></i>Mode de dosage
                        </label>
                        <input type="text" class="form-control" name="mode_dosage" 
                               placeholder="Par pulvérisation, Par arrosage, Par injection..." 
                               style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white;">
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Comment appliquer le dosage</small>
                    </div>
                    
                    <!-- Date dosage -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-calendar' style="color: #17a2b8; margin-right: 8px; font-size: 1.1rem;"></i>Date de dosage
                        </label>
                        <input type="date" class="form-control" name="date_dosage" 
                               style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white;">
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Date prévue pour la tâche</small>
                    </div>
                    
                    <!-- Prochaine exécution -->
                    <div class="mb-5">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-time-five' style="color: #17a2b8; margin-right: 8px; font-size: 1.1rem;"></i>Prochaine exécution
                        </label>
                        <input type="datetime-local" class="form-control" name="prochaineExecution" 
                               style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s ease; font-size: 0.95rem; background: white;">
                        <small class="text-muted" style="display: block; margin-top: 6px; font-size: 0.85rem;">Quand relancer cette tâche</small>
                    </div>
                    
                    <!-- Priorité -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #333; font-size: 0.95rem; display: flex; align-items: center;">
                            <i class='bx bx-trending-up' style="color: #17a2b8; margin-right: 8px; font-size: 1.1rem;"></i>Priorité
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 12px;">
                            <label style="cursor: pointer; padding: 14px 12px; border: 2px solid #e0e0e0; border-radius: 10px; text-align: center; transition: all 0.3s ease; background: white; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <input type="radio" name="priorite" value="Faible" style="accent-color: #17a2b8; cursor: pointer; transform: scale(1.2);">
                                <span style="font-size: 0.9rem; color: #333; font-weight: 500;">↓ Faible</span>
                            </label>
                            <label style="cursor: pointer; padding: 14px 12px; border: 2px solid #e0e0e0; border-radius: 10px; text-align: center; transition: all 0.3s ease; background: white; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <input type="radio" name="priorite" value="Moyen" checked style="accent-color: #17a2b8; cursor: pointer; transform: scale(1.2);">
                                <span style="font-size: 0.9rem; color: #333; font-weight: 500;">= Moyen</span>
                            </label>
                            <label style="cursor: pointer; padding: 14px 12px; border: 2px solid #e0e0e0; border-radius: 10px; text-align: center; transition: all 0.3s ease; background: white; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <input type="radio" name="priorite" value="Élevée" style="accent-color: #17a2b8; cursor: pointer; transform: scale(1.2);">
                                <span style="font-size: 0.9rem; color: #333; font-weight: 500;">↑ Élevée</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-5" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(245,247,255,0.98)); border-radius: 0 0 15px 15px; gap: 10px; border-top: 1px solid #e0e0e0;">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-5" data-bs-dismiss="modal" style="border: 2px solid #ddd; color: #666; transition: all 0.3s; font-weight: 500;">Annuler</button>
                <button type="button" class="btn rounded-pill px-5" id="submitSuggestionTache" style="background: linear-gradient(135deg, #17a2b8 0%, #0c5460 100%); color: white; border: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);">
                    <i class='bx bx-check me-2'></i>Soumettre
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #addSuggestionTacheModal .form-control:focus {
        border-color: #17a2b8 !important;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25) !important;
        outline: none;
    }
    
    #addSuggestionTacheModal .form-select:focus {
        border-color: #17a2b8 !important;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25) !important;
        outline: none;
    }
    
    #addSuggestionTacheModal .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        background: white;
    }
    
    #addSuggestionTacheModal input[type="radio"] {
        accent-color: #17a2b8;
        cursor: pointer;
    }
    
    #addSuggestionTacheModal label:has(input[type="radio"]:checked) {
        border-color: #17a2b8 !important;
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(12, 84, 96, 0.1)) !important;
        transform: scale(1.02);
    }
    
    #submitSuggestionTache:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4) !important;
    }
    
    #submitSuggestionTache:active {
        transform: translateY(-1px);
    }
</style>

<!-- Modal détails tâche -->
<div class="modal fade" id="tacheDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-gradient p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class='bx bx-check-double' style="font-size: 1.5rem; margin-right: 10px;"></i>
                    Détails de la tâche
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3" style="background: #f8f9fa; border-left: 4px solid #667eea;">
                            <small class="text-muted d-block mb-2">
                                <i class='bx bx-tag'></i> Type de tâche
                            </small>
                            <p id="detailType" class="fw-bold" style="font-size: 1.1rem; color: #333;">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3" style="background: #f8f9fa; border-left: 4px solid #764ba2;">
                            <small class="text-muted d-block mb-2">
                                <i class='bx bx-package'></i> Quantité
                            </small>
                            <p id="detailQuantite" class="fw-bold" style="font-size: 1.1rem; color: #333;">-</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3" style="background: #f8f9fa; border-left: 4px solid #f093fb;">
                            <small class="text-muted d-block mb-2">
                                <i class='bx bx-droplet'></i> Mode
                            </small>
                            <p id="detailMode" class="fw-bold" style="font-size: 1.1rem; color: #333;">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3" style="background: #f8f9fa; border-left: 4px solid #fa709a;">
                            <small class="text-muted d-block mb-2">
                                <i class='bx bx-trending-up'></i> Priorité
                            </small>
                            <p id="detailPriorite" class="fw-bold" style="font-size: 1.1rem; color: #333;">-</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3" style="background: #f8f9fa; border-left: 4px solid #4facfe;">
                            <small class="text-muted d-block mb-2">
                                <i class='bx bx-calendar-alt'></i> Date dosage
                            </small>
                            <p id="detailDate" class="fw-bold" style="font-size: 1.1rem; color: #333;">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3" style="background: #f8f9fa;">
                            <small class="text-muted d-block mb-2">
                                <i class='bx bx-check-circle'></i> Statut
                            </small>
                            <p id="detailStatut" class="fw-bold" style="font-size: 1.1rem; color: #333;">-</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3" style="background: #f8f9fa; border-left: 4px solid #43e97b;">
                            <small class="text-muted d-block mb-2">
                                <i class='bx bx-time-five'></i> Dernière exécution
                            </small>
                            <p id="detailDerniere" class="fw-bold" style="font-size: 0.95rem; color: #333;">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3" style="background: #f8f9fa; border-left: 4px solid #fa709a;">
                            <small class="text-muted d-block mb-2">
                                <i class='bx bx-calendar'></i> Prochaine exécution
                            </small>
                            <p id="detailProchaine" class="fw-bold" style="font-size: 0.95rem; color: #333;">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4" style="background: #f8f9fa;">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<div id="calendar" style="max-width: 900px; margin: 40px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);"></div>

<!-- Modal Statistiques Avancées -->
<div class="modal fade" id="statsAdvancedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: rgba(255,255,255,0.98);">
            <div class="modal-header border-0 bg-gradient p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class='bx bx-bar-chart-alt-2' style="font-size: 1.5rem; margin-right: 10px;"></i>
                    Statistiques Avancées
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- État des plantes -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="background: rgba(255,255,255,0.8);">
                            <div class="card-body">
                                <h6 class="card-title mb-3 text-dark">État de santé des plantes</h6>
                                <canvas id="chartetatPlantes" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Priorité des tâches -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="background: rgba(255,255,255,0.8);">
                            <div class="card-body">
                                <h6 class="card-title mb-3 text-dark">Tâches par priorité</h6>
                                <canvas id="chartePriorite" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <!-- Statut des tâches -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="background: rgba(255,255,255,0.8);">
                            <div class="card-body">
                                <h6 class="card-title mb-3 text-dark">Statut des tâches</h6>
                                <canvas id="charteStatutTaches" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé rapide -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Résumé Rapide</h6>
                                <div class="mb-3">
                                    <small class="text-light opacity-75">Plantes en bon état</small>
                                    <p class="mb-2"><strong style="font-size: 1.5rem;"><?= $plantes_etat_bon ?></strong> / <?= $totalPlantes ?></p>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($totalPlantes > 0) ? ($plantes_etat_bon / $totalPlantes * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-light opacity-75">Tâches complètées</small>
                                    <p class="mb-2"><strong style="font-size: 1.5rem;"><?= $tachesCompletees ?></strong> / <?= $totalTaches ?></p>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($totalTaches > 0) ? ($tachesCompletees / $totalTaches * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                                <hr style="border-color: rgba(255,255,255,0.3);">
                                <small class="text-light opacity-75">Tâches haute priorité</small>
                                <p><strong style="font-size: 1.3rem;"><?= $taches_priorite_haute ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4" style="background: #f8f9fa;">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<style>
    /* Background dégradé uniquement sur le contenu principal */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8f0f7 100%) !important;
        background-attachment: fixed !important;
    }
    
    /* Wrapper principal avec gradient */
    .wrapper {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
        background-attachment: fixed !important;
        min-height: 100vh !important;
    }
    
    /* Sidebar reste normal */
    .sidebar {
        background: white !important;
        z-index: 1000;
    }
    
    /* Contenu principal */
    #content {
        background: transparent !important;
    }
    
    /* Tables et sections */
    .table-responsive {
        background: rgba(255, 255, 255, 0.98) !important;
        border-radius: 15px !important;
        padding: 25px !important;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15) !important;
        backdrop-filter: blur(10px) !important;
        margin-bottom: 30px !important;
    }
    
    .table {
        margin-bottom: 0 !important;
        color: #333 !important;
    }
    
    .table thead {
        background: linear-gradient(135deg, #667eea, #764ba2) !important;
    }
    
    .table thead th {
        color: white !important;
        font-weight: 600 !important;
        border: none !important;
        padding: 15px !important;
    }
    
    .table tbody tr {
        transition: all 0.3s ease !important;
        border-bottom: 1px solid #e0e0e0 !important;
    }
    
    .table tbody tr:hover {
        background-color: #f5f7ff !important;
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1) !important;
    }
    
    .table tbody td {
        padding: 15px !important;
        color: #333 !important;
        vertical-align: middle !important;
    }
    
    /* Boutons */
    .btn-success {
        background: linear-gradient(135deg, #667eea, #764ba2) !important;
        border: none !important;
        border-radius: 8px !important;
        color: white !important;
        transition: all 0.3s ease !important;
        font-weight: 500 !important;
    }
    
    .btn-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4) !important;
        color: white !important;
    }
    
    .btn-warning, .btn-info {
        border-radius: 6px !important;
        transition: all 0.3s ease !important;
        border: none !important;
        color: white !important;
        font-weight: 500 !important;
    }
    
    .btn-warning:hover, .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
        color: white !important;
    }
    
    .btn-danger {
        border-radius: 6px !important;
        transition: all 0.3s ease !important;
        border: none !important;
        color: white !important;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4) !important;
    }
    
    /* Badges */
    .badge {
        border-radius: 20px !important;
        padding: 8px 14px !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
    }
    
    /* Calendrier FullCalendar */
    #calendar {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        background: rgba(255, 255, 255, 0.98) !important;
        border-radius: 15px !important;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15) !important;
        backdrop-filter: blur(10px) !important;
        color: #333 !important;
    }
    
    .fc-button-primary {
        background-color: #667eea !important;
        border-color: #667eea !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        transition: all 0.3s ease !important;
        color: white !important;
    }
    
    .fc-button-primary:hover {
        background-color: #764ba2 !important;
        border-color: #764ba2 !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4) !important;
    }
    
    .fc-button-primary.fc-button-active {
        background-color: #764ba2 !important;
        border-color: #764ba2 !important;
    }
    
    .fc-daygrid-day:hover {
        background-color: #f5f7ff !important;
        cursor: pointer;
    }
    
    .fc-event {
        border-radius: 8px !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        padding: 4px 8px !important;
    }
    
    .fc-event:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
    }
    
    .fc-col-header-cell {
        background-color: #f8f9fa !important;
        color: #667eea !important;
        font-weight: 600 !important;
        padding: 12px 4px !important;
    }
    
    .fc-daygrid-day-number {
        padding: 8px !important;
        font-weight: 500 !important;
        color: #333 !important;
    }
    
    .fc-daygrid-day-frame {
        min-height: 120px !important;
    }
    
    .fc-toolbar-title {
        font-size: 1.5rem !important;
        color: #333 !important;
        font-weight: 700 !important;
    }
    
    .fc-daygrid-day-bg {
        background: white !important;
    }
    
    /* Modal */
    .modal-content {
        border-radius: 15px !important;
        border: none !important;
        background: rgba(255, 255, 255, 0.98) !important;
        color: #333 !important;
    }
    
    .detail-card {
        transition: all 0.3s ease !important;
        background: linear-gradient(135deg, #f8f9fa, #f0f2ff) !important;
        border-radius: 12px !important;
        color: #333 !important;
    }
    
    .detail-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    .detail-card small {
        color: #666 !important;
    }
    
    .detail-card p {
        color: #333 !important;
    }


    }
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
    
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <!-- Chart.js -->
    <script src='https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js'></script>
    
    <script type="text/javascript">
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

    </script>
    <script>
    let chartsCreated = false;
    let chartInstances = {};
    
    // Fonction pour afficher les détails des tâches
    function afficherDetailsTache(tache) {
        document.getElementById('detailType').textContent = tache.type_dosage || 'N/A';
        document.getElementById('detailQuantite').textContent = tache.quantite + ' ' + tache.mode_dosage || 'N/A';
        document.getElementById('detailMode').textContent = tache.mode_dosage || 'N/A';
        document.getElementById('detailPriorite').textContent = tache.priorite || 'N/A';
        document.getElementById('detailDate').textContent = tache.date_dosage || 'N/A';
        document.getElementById('detailStatut').textContent = (tache.estComplete == 1) ? '✓ Complètée' : '◯ En attente';
        document.getElementById('detailDerniere').textContent = tache.derniereExecution || 'N/A';
        document.getElementById('detailProchaine').textContent = tache.prochaineExecution || 'N/A';
        
        // Ouvrir le modal
        const modal = new bootstrap.Modal(document.getElementById('tacheDetailModal'));
        modal.show();
    }

    // Créer les graphiques
    function createCharts() {
        console.log('createCharts appelée');
        console.log('Chart disponible:', typeof Chart);
        
        if (chartsCreated) {
            console.log('Graphiques déjà créés');
            return;
        }
        
        // Attendre que Chart soit chargé
        if (typeof Chart === 'undefined') {
            console.log('Chart.js n\'est pas encore chargé');
            return;
        }
        
        // Données PHP
        const plantesBonEtat = <?= $plantes_etat_bon ?>;
        const plantesMoyenEtat = <?= $plantes_etat_moyen ?>;
        const plantessMauvaisEtat = <?= $plantes_etat_mauvais ?>;
        
        const tachesPrioriteHaute = <?= $taches_priorite_haute ?>;
        const tachesPrioriteMoyenne = <?= $taches_priorite_moyenne ?>;
        const tachesPrioriteBasse = <?= $taches_priorite_basse ?>;
        
        const tachesCompletees = <?= $tachesCompletees ?>;
        const tachesEnCours = <?= $tachesEnCours ?>;

        console.log('Création des graphiques avec données:', { plantesBonEtat, tachesCompletees });

        // Détruire les graphiques précédents s'ils existent
        Object.values(chartInstances).forEach(chart => {
            if (chart) chart.destroy();
        });

        // Graphique État des plantes
        try {
            const ctxEtat = document.getElementById('chartetatPlantes');
            if (ctxEtat) {
                console.log('Création graphique État des plantes');
                chartInstances.etat = new Chart(ctxEtat, {
                    type: 'doughnut',
                    data: {
                        labels: ['Bon état', 'Moyen', 'Mauvais état'],
                        datasets: [{
                            data: [plantesBonEtat, plantesMoyenEtat, plantessMauvaisEtat],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        } catch(e) {
            console.error('Erreur graphique État:', e);
        }

        // Graphique Priorité des tâches
        try {
            const ctxPriorite = document.getElementById('chartePriorite');
            if (ctxPriorite) {
                console.log('Création graphique Priorité');
                chartInstances.priorite = new Chart(ctxPriorite, {
                    type: 'doughnut',
                    data: {
                        labels: ['Élevée', 'Moyen', 'Faible'],
                        datasets: [{
                            data: [tachesPrioriteHaute, tachesPrioriteMoyenne, tachesPrioriteBasse],
                            backgroundColor: ['#dc3545', '#ffc107', '#28a745'],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        } catch(e) {
            console.error('Erreur graphique Priorité:', e);
        }

        // Graphique Statut des tâches
        try {
            const ctxStatut = document.getElementById('charteStatutTaches');
            if (ctxStatut) {
                console.log('Création graphique Statut');
                chartInstances.statut = new Chart(ctxStatut, {
                    type: 'bar',
                    data: {
                        labels: ['Complètées', 'En cours'],
                        datasets: [{
                            label: 'Nombre de tâches',
                            data: [tachesCompletees, tachesEnCours],
                            backgroundColor: ['#28a745', '#ffc107'],
                            borderColor: ['#28a745', '#ffc107'],
                            borderWidth: 2,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        } catch(e) {
            console.error('Erreur graphique Statut:', e);
        }
        
        chartsCreated = true;
        console.log('Graphiques créés avec succès');
    }

    function initializeStatsModal() {
        // Écouter l'ouverture du modal stats
        const btnStats = document.querySelector('[data-bs-target="#statsAdvancedModal"]');
        const statsModal = document.getElementById('statsAdvancedModal');
        console.log('Bouton stats trouvé:', btnStats ? 'Oui' : 'Non');
        console.log('Modal trouvée:', statsModal ? 'Oui' : 'Non');
        console.log('Bootstrap disponible:', typeof bootstrap !== 'undefined' ? 'Oui' : 'Non');
        
        if (btnStats && statsModal && typeof bootstrap !== 'undefined') {
            btnStats.addEventListener('click', function(e) {
                console.log('Bouton stats cliqué - affichage du modal');
                e.preventDefault();
                
                // Créer une nouvelle instance de modal à chaque fois
                let modalInstance = new bootstrap.Modal(statsModal);
                console.log('Instance modal créée - affichage...');
                
                // Afficher le modal
                modalInstance.show();
                
                // Créer les graphiques après affichage
                setTimeout(createCharts, 350);
            });
        } else if (!btnStats || !statsModal) {
            console.log('Éléments DOM manquants');
        } else {
            console.log('Bootstrap pas encore disponible, nouvelle tentative...');
            setTimeout(initializeStatsModal, 500);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM chargé');
        setTimeout(initializeStatsModal, 100);
    
        // Calendrier FullCalendar
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: 'calendrierP.php',
                locale: 'fr',
                eventClick: function(info) {
                    const event = info.event;
                    const props = event.extendedProps;
                    
                    document.getElementById('detailType').textContent = props.type || 'N/A';
                    document.getElementById('detailQuantite').textContent = props.quantite + ' ' + props.mode || 'N/A';
                    document.getElementById('detailMode').textContent = props.mode || 'N/A';
                    document.getElementById('detailPriorite').textContent = props.priorite || 'N/A';
                    document.getElementById('detailDate').textContent = props.date_dosage || 'N/A';
                    document.getElementById('detailStatut').textContent = (props.complete == 1) ? '✓ Complètée' : '◯ En attente';
                    document.getElementById('detailDerniere').textContent = props.derniere_execution || 'N/A';
                    document.getElementById('detailProchaine').textContent = props.prochaine_execution || 'N/A';
                    
                    const modal = new bootstrap.Modal(document.getElementById('tacheDetailModal'));
                    modal.show();
                }
            });
            calendar.render();
        }
    });
    </script>
    
    <!-- Chatbot Toggle Button -->
    <button class="chatbot-toggle" id="chatbotToggle" title="Assistant IA">
        <i class='bx bxs-bot'></i>
    </button>
    
    <!-- Chatbot Container -->
    <div class="chatbot-container" id="chatbotContainer" style="display: none;">
        <div class="chatbot-header">
            <h5>🤖 Assistant Jardin IA</h5>
            <button class="chatbot-close" id="chatbotClose">&times;</button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message">
                <div class="message-bubble ai">
                    👋 Bonjour! Je suis votre assistant IA. Je peux vous aider avec vos plantes et tâches. Posez-moi vos questions!
                </div>
            </div>
        </div>
        <div class="chatbot-input-area">
            <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Tapez votre question...">
            <button class="chatbot-send" id="chatbotSend">
                <i class='bx bxs-send' style="font-size: 18px;"></i>
            </button>
        </div>
    </div>
    
    <script>
        // Chatbot functionality
        const chatbotToggle = document.getElementById('chatbotToggle');
        const chatbotContainer = document.getElementById('chatbotContainer');
        const chatbotClose = document.getElementById('chatbotClose');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotSend = document.getElementById('chatbotSend');
        const chatbotMessages = document.getElementById('chatbotMessages');
        
        // Toggle chatbot
        chatbotToggle.addEventListener('click', function() {
            if (chatbotContainer.style.display === 'none') {
                chatbotContainer.style.display = 'flex';
                chatbotToggle.classList.add('hidden');
            }
        });
        
        // Close chatbot
        chatbotClose.addEventListener('click', function() {
            chatbotContainer.style.display = 'none';
            chatbotToggle.classList.remove('hidden');
        });
        
        // Send message
        function sendChatbotMessage() {
            const message = chatbotInput.value.trim();
            if (!message) return;
            
            // Add user message to chat
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'chatbot-message message-user';
            userMessageDiv.innerHTML = `<div class="message-bubble user">${escapeHtml(message)}</div>`;
            chatbotMessages.appendChild(userMessageDiv);
            
            // Clear input
            chatbotInput.value = '';
            
            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'chatbot-message';
            loadingDiv.innerHTML = `<div class="message-bubble ai">⏳ Réflexion en cours...</div>`;
            chatbotMessages.appendChild(loadingDiv);
            
            // Scroll to bottom
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            
            // Send to OpenAI API via our endpoint
            fetch('../../chatgpt_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                // Remove loading indicator
                loadingDiv.remove();
                
                if (data.success) {
                    // Add AI response
                    const aiMessageDiv = document.createElement('div');
                    aiMessageDiv.className = 'chatbot-message';
                    aiMessageDiv.innerHTML = `<div class="message-bubble ai">${escapeHtml(data.message)}</div>`;
                    chatbotMessages.appendChild(aiMessageDiv);
                } else {
                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'chatbot-message';
                    errorDiv.innerHTML = `<div class="message-bubble ai">❌ ${escapeHtml(data.message)}</div>`;
                    chatbotMessages.appendChild(errorDiv);
                }
                
                // Scroll to bottom
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            })
            .catch(error => {
                console.error('Erreur:', error);
                loadingDiv.innerHTML = `<div class="message-bubble ai">❌ Erreur de connexion. Réessayez.</div>`;
            });
        }
        
        // Send button click
        chatbotSend.addEventListener('click', sendChatbotMessage);
        
        // Enter key to send
        chatbotInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendChatbotMessage();
            }
        });
        
        // Helper functions
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function formatMessage(text) {
            // Convertir Markdown simple en HTML
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\n/g, '<br>')
                .replace(/- /g, '• ');
        }
        
        // Suggestion Plant Form
        function initSuggestionForm() {
            console.log('Initialisation formulaire suggestion');
            
            // Bouton pour ouvrir le modal
            const btnSuggestion = document.getElementById('btnSuggestionPlante');
            const submitSuggestionBtn = document.getElementById('submitSuggestion');
            const suggestionForm = document.getElementById('suggestionForm');
            const suggestionModal = document.getElementById('addSuggestionModal');
            const niveauHumiditeRange = document.getElementById('niveauHumiditeRange');
            const humiditeValue = document.getElementById('humiditeValue');
            const niveauHumiditeInput = document.getElementById('niveau_humidite');
            
            console.log('Éléments trouvés - btnSuggestion:', btnSuggestion ? '✓' : '✗', 'submitSuggestionBtn:', submitSuggestionBtn ? '✓' : '✗');
            
            // Gestion du slider d'humidité
            if (niveauHumiditeRange && humiditeValue && niveauHumiditeInput) {
                niveauHumiditeRange.addEventListener('input', function(e) {
                    const value = e.target.value;
                    humiditeValue.textContent = value + '%';
                    niveauHumiditeInput.value = value;
                    
                    // Animation du badge
                    humiditeValue.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        humiditeValue.style.transform = 'scale(1)';
                    }, 150);
                });
            }
            
            // Ouvrir le modal au clic du bouton
            if (btnSuggestion && suggestionModal) {
                btnSuggestion.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Bouton suggestion cliqué - ouverture du modal');
                    const modal = new bootstrap.Modal(suggestionModal);
                    modal.show();
                });
            }
            
            // Soumettre le formulaire
            if (submitSuggestionBtn && suggestionForm) {
                submitSuggestionBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Bouton suggérer cliqué');
                    
                    const formData = new FormData(suggestionForm);
                    formData.append('action', 'add');
                    
                    // Validation simple
                    const nom = formData.get('nom_plante').trim();
                    
                    console.log('Nom:', nom);
                    
                    if (!nom) {
                        alert('⚠️ Veuillez entrer le nom de la plante');
                        return;
                    }
                    
                    // Désactiver le bouton pendant l'envoi
                    submitSuggestionBtn.disabled = true;
                    submitSuggestionBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Envoi...';
                    
                    console.log('Envoi de la requête à ajoutSuggestionSimple.php');
                    
                    // Submit
                    fetch('ajoutSuggestionSimple.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('Réponse reçue, status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Données reçues:', data);
                        
                        // Réactiver le bouton
                        submitSuggestionBtn.disabled = false;
                        submitSuggestionBtn.innerHTML = '<i class="bx bx-check me-2"></i>Soumettre';
                        
                        if (data.success) {
                            alert('✅ ' + data.message);
                            suggestionForm.reset();
                            niveauHumiditeRange.value = 50;
                            humiditeValue.textContent = '50%';
                            niveauHumiditeInput.value = 50;
                            
                            // Fermer le modal en supprimant les classes Bootstrap
                            suggestionModal.classList.remove('show');
                            suggestionModal.style.display = 'none';
                            document.body.classList.remove('modal-open');
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) backdrop.remove();
                        } else {
                            alert('❌ ' + (data.message || 'Erreur inconnue'));
                        }
                    })
                    .catch(error => {
                        console.error('Erreur complète:', error);
                        
                        // Réactiver le bouton en cas d'erreur
                        submitSuggestionBtn.disabled = false;
                        submitSuggestionBtn.innerHTML = '<i class="bx bx-check me-2"></i>Soumettre';
                        
                        alert('❌ Erreur: ' + error.message);
                    });
                });
            } else {
                console.warn('Éléments de formulaire pas trouvés');
            }
        }
        
        // Suggestion Tache Form
        function initSuggestionTacheForm() {
            console.log('Initialisation formulaire suggestion tâche');
            
            const btnSuggestionTache = document.getElementById('btnSuggestionTache');
            const submitSuggestionTacheBtn = document.getElementById('submitSuggestionTache');
            const suggestionTacheForm = document.getElementById('suggestionTacheForm');
            const suggestionTacheModal = document.getElementById('addSuggestionTacheModal');
            
            console.log('Éléments trouvés - btnSuggestionTache:', btnSuggestionTache ? '✓' : '✗', 'submitSuggestionTacheBtn:', submitSuggestionTacheBtn ? '✓' : '✗');
            
            // Ouvrir le modal au clic du bouton
            if (btnSuggestionTache && suggestionTacheModal) {
                btnSuggestionTache.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Bouton suggestion tâche cliqué - ouverture du modal');
                    
                    // Charger les plantes
                    loadPlantesForTache();
                    
                    const modal = new bootstrap.Modal(suggestionTacheModal);
                    modal.show();
                });
            }
            
            // Fonction pour charger les plantes
            function loadPlantesForTache() {
                const selectPlante = document.getElementById('id_plante');
                
                fetch('getPlantes.php')
                    .then(response => response.json())
                    .then(plantes => {
                        console.log('Plantes reçues:', plantes);
                        
                        // Réinitialiser le select (garder l'option vide)
                        selectPlante.innerHTML = '<option value="">-- Choisir une plante --</option>';
                        
                        // Ajouter les plantes
                        if (plantes && plantes.length > 0) {
                            plantes.forEach(plante => {
                                const option = document.createElement('option');
                                option.value = plante.id_plante;
                                option.textContent = plante.nom_plante;
                                selectPlante.appendChild(option);
                            });
                        } else {
                            const option = document.createElement('option');
                            option.disabled = true;
                            option.textContent = 'Aucune plante trouvée';
                            selectPlante.appendChild(option);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des plantes:', error);
                        selectPlante.innerHTML = '<option value="" disabled>Erreur de chargement</option>';
                    });
            }
            
            // Soumettre le formulaire
            if (submitSuggestionTacheBtn && suggestionTacheForm) {
                submitSuggestionTacheBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Bouton suggérer tâche cliqué');
                    
                    const formData = new FormData(suggestionTacheForm);
                    formData.append('action', 'add');
                    
                    // Validation simple
                    const typeDosage = formData.get('type_dosage').trim();
                    const idPlante = formData.get('id_plante');
                    
                    console.log('Type dosage:', typeDosage, 'ID Plante:', idPlante);
                    
                    if (!typeDosage) {
                        alert('⚠️ Veuillez entrer le type de dosage');
                        return;
                    }
                    
                    if (!idPlante) {
                        alert('⚠️ Veuillez sélectionner une plante');
                        return;
                    }
                    
                    // Désactiver le bouton pendant l'envoi
                    submitSuggestionTacheBtn.disabled = true;
                    submitSuggestionTacheBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Envoi...';
                    
                    console.log('Envoi de la requête à ajoutSuggestionTache.php');
                    
                    // Submit
                    fetch('ajoutSuggestionTache.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('Réponse reçue, status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Données reçues:', data);
                        
                        // Réactiver le bouton
                        submitSuggestionTacheBtn.disabled = false;
                        submitSuggestionTacheBtn.innerHTML = '<i class="bx bx-check me-2"></i>Soumettre';
                        
                        if (data.success) {
                            if (typeof toastManager !== 'undefined') {
                                toastManager.success(data.message);
                            } else {
                                alert('✅ ' + data.message);
                            }
                            suggestionTacheForm.reset();
                            
                            // Fermer le modal
                            suggestionTacheModal.classList.remove('show');
                            suggestionTacheModal.style.display = 'none';
                            document.body.classList.remove('modal-open');
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) backdrop.remove();
                        } else {
                            if (typeof toastManager !== 'undefined') {
                                toastManager.error(data.message || 'Erreur inconnue');
                            } else {
                                alert('❌ ' + (data.message || 'Erreur inconnue'));
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erreur complète:', error);
                        
                        // Réactiver le bouton en cas d'erreur
                        submitSuggestionTacheBtn.disabled = false;
                        submitSuggestionTacheBtn.innerHTML = '<i class="bx bx-check me-2"></i>Soumettre';
                        
                        alert('❌ Erreur: ' + error.message);
                    });
                });
            } else {
                console.warn('Éléments de formulaire tâche pas trouvés');
            }
        }
        
        // Attendre que tout soit chargé
        setTimeout(initSuggestionForm, 500);
        setTimeout(initSuggestionTacheForm, 500);
    </script>
</body>
