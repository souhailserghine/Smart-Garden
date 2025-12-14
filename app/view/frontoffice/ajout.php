<?php
require_once 'check_session.php';
include("../../controller/publicationC.php");

if ($_POST) {
    $contenuTexte = $_POST['contenuTexte'];
    // CORRECTION: Utiliser l'ID utilisateur de la session au lieu du POST
    $idUtilisateur = isset($_SESSION['idUtilisateur']) ? $_SESSION['idUtilisateur'] : 1;
    $nbLikes = $_POST['nbLikes'];
    
    // Récupérer l'option de planification
    $planification = $_POST['planification'];
    $datePublication = isset($_POST['datePublication']) ? $_POST['datePublication'] : '';
    $heurePublication = isset($_POST['heurePublication']) ? $_POST['heurePublication'] : '';
    
    // Si planification est choisie
    if ($planification === 'schedule' && !empty($datePublication) && !empty($heurePublication)) {
        // Convertir JJ/MM/AAAA en AAAA-MM-JJ pour MySQL
        $dateParts = explode('/', $datePublication);
        if (count($dateParts) === 3) {
            $dateMySQL = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
            $dateTimeMySQL = $dateMySQL . ' ' . $heurePublication . ':00';
        } else {
            $dateTimeMySQL = date('Y-m-d H:i:s');
        }
    } else {
        // Publication immédiate
        $dateTimeMySQL = date('Y-m-d H:i:s');
    }

    // Initialiser les tableaux pour les médias
    $images = [];
    $videos = [];

    // Traiter les fichiers uploadés
    if (isset($_FILES['medias']) && !empty($_FILES['medias']['name'][0])) {
        $uploadDir = 'uploads/';
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Parcourir tous les fichiers uploadés
        foreach ($_FILES['medias']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['medias']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = basename($_FILES['medias']['name'][$key]);
                $fileType = $_FILES['medias']['type'][$key];
                
                // Générer un nom unique pour éviter les conflits
                $uniqueName = uniqid() . '_' . $fileName;
                $filePath = $uploadDir . $uniqueName;
                
                // Déplacer le fichier uploadé
                if (move_uploaded_file($tmp_name, $filePath)) {
                    // Déterminer si c'est une image ou une vidéo
                    if (strpos($fileType, 'image/') === 0) {
                        $images[] = $filePath;
                    } elseif (strpos($fileType, 'video/') === 0) {
                        $videos[] = $filePath;
                    }
                }
            }
        }
    }
    
    // Convertir les tableaux en chaînes séparées par des virgules
    $imagesStr = implode(',', $images);
    $videosStr = implode(',', $videos);

    if (!empty($contenuTexte)) {
        // Modifier l'appel au constructeur pour inclure les médias et la date de publication
        $publication = new Publication($contenuTexte, $idUtilisateur, $nbLikes, $imagesStr, $videosStr, $dateTimeMySQL);
        $publicationC = new PublicationC();
        
        // Vérifier si la date est dans le futur
        $datePublicationObj = new DateTime($dateTimeMySQL);
        $dateNow = new DateTime();
        
        if ($datePublicationObj > $dateNow) {
            // La publication a une date future - planifiée
            $result = $publicationC->addPublication($publication);
            
            if ($result) {
                // Enregistrer dans la session pour affichage
                if (!isset($_SESSION['publications_planifiees'])) {
                    $_SESSION['publications_planifiees'] = [];
                }
                $_SESSION['publications_planifiees'][] = [
                    'date' => $dateTimeMySQL,
                    'message' => 'Publication planifiée pour le ' . $datePublication . ' à ' . $heurePublication
                ];
                
                header("Location: liste.php?success=1&planifiee=1");
            } else {
                header("Location: ajout.php?error=0");
            }
        } else {
            // Publication immédiate
            $result = $publicationC->addPublication($publication);
            
            if ($result) {
                header("Location: liste.php?success=1");
            } else {
                header("Location: ajout.php?error=0");
            }
        }
    } else {
        header("Location: ajout.php?error=2");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    <title>SmartGarden - Nouvelle Publication</title>

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
        /* Votre CSS existant reste inchangé */
        .form-container {
            max-width: 100%;
            margin: 0 auto;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .form-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #28a745, #20c997, #17a2b8);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 20px;
        }
        
        .form-header h2 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .form-header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #28a745, #20c997);
            border-radius: 2px;
        }
        
        .form-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-top: 10px;
        }
        
        /* Amélioration du champ textarea */
        .textarea-container {
            position: relative;
            margin-bottom: 30px;
        }
        
        .textarea-container label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 12px;
            font-size: 1.1rem;
        }
        
        .textarea-container textarea {
            width: 100%;
            min-height: 200px;
            padding: 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            line-height: 1.6;
            transition: all 0.3s ease;
            background: #ffffff;
            resize: vertical;
        }
        
        .textarea-container textarea:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            outline: none;
        }
        
        .textarea-container textarea::placeholder {
            color: #adb5bd;
            font-style: italic;
        }
        
        .char-counter {
            position: absolute;
            bottom: -25px;
            right: 10px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        /* Amélioration des boutons médias */
        .media-section {
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            border: 2px dashed #e9ecef;
        }
        
        .media-section-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .media-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .btn-media {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
            border: 2px dashed #ced4da;
            background: #ffffff;
            color: #495057;
            border-radius: 12px;
            transition: all 0.3s ease;
            height: 120px;
        }
        
        .btn-media:hover {
            background: #e9ecef;
            border-color: #28a745;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.1);
        }
        
        .btn-media i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #28a745;
        }
        
        .btn-media span {
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        /* Amélioration de la prévisualisation */
        .media-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 20px;
            min-height: 100px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
        }
        
        .preview-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .preview-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .preview-item img, .preview-item video {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }
        
        .remove-media {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .remove-media:hover {
            background: rgba(220, 53, 69, 1);
            transform: scale(1.1);
        }
        
        /* Section planification */
        .planification-section {
            margin-bottom: 30px;
            background: linear-gradient(135deg, #f0f8ff 0%, #e6f7ff 100%);
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #e1f5fe;
        }
        
        .planification-section-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .planification-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .planification-option {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }
        
        .planification-option:hover {
            border-color: #28a745;
            background: #f8fff9;
            transform: translateY(-2px);
        }
        
        .planification-option.active {
            border-color: #28a745;
            background: #f0fff4;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.1);
        }
        
        .planification-option input[type="radio"] {
            margin-right: 15px;
            width: 18px;
            height: 18px;
        }
        
        .planification-option label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            width: 100%;
            margin: 0;
        }
        
        .planification-option i {
            font-size: 1.5rem;
            color: #28a745;
        }
        
        .datetime-inputs {
            display: none;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            animation: fadeIn 0.3s ease;
        }
        
        .datetime-inputs.show {
            display: grid;
        }
        
        .datetime-group {
            display: flex;
            flex-direction: column;
        }
        
        .datetime-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .datetime-input {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .datetime-input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            outline: none;
        }
        
        .planification-info {
            background: #e9ecef;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Amélioration des boutons d'action */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-publish {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 14px 45px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        }
        
        .btn-publish:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-3px);
            color: white;
            text-decoration: none;
        }
        
        /* Alerts améliorés */
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        /* Section tips */
        .tips-section {
            background: linear-gradient(135deg, #e3f2fd, #e8f5e9);
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
            border-left: 5px solid #17a2b8;
        }
        
        .tips-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tips-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .tips-list li {
            padding: 8px 0;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tips-list li i {
            color: #28a745;
            font-size: 1.1rem;
        }
        
        /* Input pour fichiers */
        .file-inputs-container {
            display: none;
        }
        
        /* Message d'information pour les médias */
        .media-info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 14px;
            color: #495057;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                padding: 25px 20px;
            }
            
            .media-buttons {
                grid-template-columns: 1fr;
            }
            
            .planification-options {
                grid-template-columns: 1fr;
            }
            
            .datetime-inputs {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-publish, .btn-cancel {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body class="newsfeed">
    <div class="container-fluid" id="wrapper">
        <div class="row newsfeed-size">
            <div class="col-md-12 newsfeed-right-side">

                <!-- En-tête de navigation -->
                <nav id="navbar-main" class="navbar navbar-expand-lg shadow-sm sticky-top">
                    <ul class="navbar-nav mr-5" id="main_menu">
                        <a class="navbar-brand nav-item mr-lg-5" href="publications.php">`n                            <img src="./assets/images/logo-128x128.png" width="70" height="70" class="mr-3" alt="Logo">
                        </a>

                        <form class="w-30 mx-2 my-auto d-inline form-inline mr-5">
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
                                <a class="dropdown-item" href="publications.php">Publications</a>
                                <a class="dropdown-item" href="sign-in.php">Sign in</a>
                                <a class="dropdown-item" href="sign-up.php">Sign up</a>
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
                                <a class="dropdown-item" href="profile.php"><i class='bx bx-user mr-2'></i> Account</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class='bx bx-undo mr-2'></i> Logout</a>
                            </div>
                        </li>

                        <!-- Settings -->
                        <li class="nav-item nav-icon">
                            <a href="settings.php" class="nav-link"><img src="./assets/images/icons/navbar/settings.png" alt="navbar icon"></a>
                        </li>
                    </ul>
                    <button type="button" class="btn btn-primary mr-3" id="menu-toggle"><i class='bx bx-align-left'></i></button>
                </nav>

                <div class="row newsfeed-right-side-content mt-3">

                    <!-- Sidebar gauche -->
                    <div class="col-md-3 newsfeed-left-side sticky-top shadow-sm" id="sidebar-wrapper">
                        <div class="card newsfeed-user-card h-100">
                            <ul class="list-group list-group-flush newsfeed-left-sidebar">
                                <li class="list-group-item"><h6>Home</h6></li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="profile.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/newsfeed.png"> Profile</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center sd-active">
                                    <a href="publications.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/message.png"> Publications</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="plantes.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/group.png"> Plantes</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="evenements.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/event.png"> Evenements</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="listCategorie.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/saved.png"> Capteurs</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Colonne centrale - Formulaire de publication -->
                    <div class="col-md-6 second-section" id="page-content-wrapper">
                        <div class="form-container">
                            <div class="form-header">
                                <h2>Créer une nouvelle publication</h2>
                                <p class="form-subtitle">Partagez vos idées, astuces et expériences avec la communauté</p>
                            </div>
                            
                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger alert-custom">
                                    <i class='bx bx-error-circle mr-2'></i>
                                    <?php 
                                    if ($_GET['error'] == 0) echo "Erreur lors de l'ajout de la publication. Veuillez réessayer.";
                                    if ($_GET['error'] == 2) echo "Le contenu de la publication ne peut pas être vide.";
                                    ?>
                                </div>
                            <?php endif; ?>

                            <form action="ajout.php" method="POST" enctype="multipart/form-data" novalidate>
                                <!-- Champ de contenu -->
                                <div class="textarea-container">
                                    <label for="contenuTexte"><i class='bx bx-edit'></i> Votre message :</label>
                                    <textarea name="contenuTexte" id="contenuTexte" 
                                              placeholder="Partagez vos conseils, vos découvertes, vos astuces de jardinage... Que souhaitez-vous partager avec la communauté SmartGarden ?" 
                                              oninput="updateCharCount(this)"></textarea>
                                    <div class="char-counter">
                                        <span id="charCount">0</span>/1000 caractères
                                    </div>
                                </div>

                                <!-- Section médias -->
                                <div class="media-section">
                                    <div class="media-section-title">
                                        <i class='bx bx-photo-album'></i>
                                        Ajouter des médias
                                    </div>
                                    
                                    <div class="media-info">
                                        <i class='bx bx-info-circle'></i> Sélectionnez des images ou vidéos (max 10 fichiers, 10MB chacun)
                                    </div>
                                    
                                    <div class="media-buttons">
                                        <button type="button" class="btn btn-media" onclick="document.getElementById('mediasInput').click()">
                                            <i class='bx bx-image'></i>
                                            <span>Ajouter des médias</span>
                                            <small class="text-muted mt-2">Images et vidéos</small>
                                        </button>
                                    </div>

                                    <div class="file-inputs-container">
                                        <input type="file" id="mediasInput" name="medias[]" multiple 
                                               accept="image/*,video/*" 
                                               onchange="handleMediaSelection(this)">
                                    </div>

                                    <!-- Prévisualisation des médias -->
                                    <div class="media-preview" id="mediaPreview">
                                        <div class="preview-placeholder">
                                            <p class="text-muted text-center">Aucun média sélectionné</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section: Planification de la publication -->
                                <div class="planification-section">
                                    <div class="planification-section-title">
                                        <i class='bx bx-calendar'></i>
                                        Planifier la publication
                                    </div>
                                    
                                    <div class="planification-options">
                                        <div class="planification-option active" onclick="selectOption('publish-now')">
                                            <input type="radio" id="publish-now" name="planification" value="now" checked>
                                            <label for="publish-now">
                                                <i class='bx bx-send'></i>
                                                <div>
                                                    <strong>Publier maintenant</strong>
                                                    <small class="d-block text-muted">La publication sera visible immédiatement</small>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <div class="planification-option" onclick="selectOption('schedule-publish')">
                                            <input type="radio" id="schedule-publish" name="planification" value="schedule">
                                            <label for="schedule-publish">
                                                <i class='bx bx-time'></i>
                                                <div>
                                                    <strong>Planifier pour plus tard</strong>
                                                    <small class="d-block text-muted">Choisissez une date et heure de publication</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="datetime-inputs" id="datetime-inputs">
                                        <div class="datetime-group">
                                            <label for="datePublication"><i class='bx bx-calendar-alt'></i> Date de publication :</label>
                                            <input type="text" id="datePublication" name="datePublication" 
                                                   class="datetime-input" 
                                                   placeholder="JJ/MM/AAAA"
                                                   onfocus="showDatePicker(this)">
                                        </div>
                                        
                                        <div class="datetime-group">
                                            <label for="heurePublication"><i class='bx bx-time-five'></i> Heure de publication :</label>
                                            <input type="text" id="heurePublication" name="heurePublication" 
                                                   class="datetime-input" 
                                                   placeholder="HH:MM"
                                                   onfocus="showTimePicker(this)">
                                        </div>
                                    </div>
                                    
                                    <div class="planification-info">
                                        <i class='bx bx-info-circle'></i> 
                                        <span>La publication sera automatiquement publiée à la date et heure spécifiées.</span>
                                    </div>
                                </div>

                                <!-- Champs cachés -->
                                <input type="hidden" name="idUtilisateur" value="<?php echo isset($_SESSION['idUtilisateur']) ? $_SESSION['idUtilisateur'] : 1; ?>">
                                <input type="hidden" name="nbLikes" value="0">

                                <!-- Section de conseils -->
                                <div class="tips-section">
                                    <h6 class="tips-title"><i class='bx bx-bulb'></i> Conseils pour une bonne publication :</h6>
                                    <ul class="tips-list">
                                        <li><i class='bx bx-check-circle'></i> Soyez clair et concis dans votre message</li>
                                        <li><i class='bx bx-check-circle'></i> Ajoutez des photos pour illustrer vos propos</li>
                                        <li><i class='bx bx-check-circle'></i> Partagez des expériences personnelles</li>
                                        <li><i class='bx bx-check-circle'></i> Posez des questions pour engager la communauté</li>
                                    </ul>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="action-buttons">
                                    <button type="submit" class="btn btn-publish">
                                        <i class='bx bx-send'></i>
                                        Publier maintenant
                                    </button>
                                    <a href="liste.php" class="btn btn-cancel">
                                        <i class='bx bx-x'></i>
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sidebar droit -->
                    <div class="col-md-3 third-section">
                        <div class="p-3 bg-white rounded w-shadow" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                            <h6 class="card-title pb-3 mb-0" style="color: #2c3e50; font-weight: 600;">
                                <i class='bx bx-cog mr-2'></i>Gestion CRUD
                            </h6>
                            <div class="list-group">
                                <a href="ajout.php" class="list-group-item list-group-item-action active" 
                                   style="background: linear-gradient(135deg, #28a745, #20c997); border: none; border-radius: 8px; margin-bottom: 5px;">
                                    <i class='bx bx-plus mr-2'></i> Ajouter une publication
                                </a>
                                <a href="liste.php" class="list-group-item list-group-item-action" 
                                   style="border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 5px;">
                                    <i class='bx bx-list-ul mr-2'></i> Liste des publications
                                </a>
                            </div>

                            <h6 class="card-title pb-3 mb-0 mt-4" style="color: #2c3e50; font-weight: 600;">
                                <i class='bx bx-stats mr-2'></i>Statistiques
                            </h6>
                            <div class="bg-white rounded contacts">
                                <div class="media text-muted pt-2">
                                    <i class='bx bx-file mr-3 text-primary' style="font-size: 1.5rem;"></i>
                                    <div class="media-body">
                                        <strong>Publications totales</strong>
                                        <div class="text-muted small">15 publications</div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-title pb-3 mb-0 mt-4" style="color: #2c3e50; font-weight: 600;">
                                <i class='bx bx-group mr-2'></i>Contacts
                            </h5>
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
                                    <a href="#" style="color: #28a745; font-weight: 500;">Voir plus</a>
                                </small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    
    <!-- Script pour le datepicker manuel (sans HTML5) -->
    <script src="./assets/js/datepicker-custom.js"></script>
    
    <script>
        // Toggle sidebar
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

        // Variables pour gérer les médias
        let mediaFiles = [];
        let maxFiles = 10;
        let maxFileSize = 10 * 1024 * 1024; // 10MB

        // Compteur de caractères
        function updateCharCount(textarea) {
            const charCount = textarea.value.length;
            document.getElementById('charCount').textContent = charCount;
            
            // Changer la couleur si on dépasse 80% de la limite
            const charCounter = document.querySelector('.char-counter');
            if (charCount > 800) {
                charCounter.style.color = '#dc3545';
            } else if (charCount > 500) {
                charCounter.style.color = '#ffc107';
            } else {
                charCounter.style.color = '#6c757d';
            }
        }

        // Gestion de la sélection des médias
        function handleMediaSelection(input) {
            const files = Array.from(input.files);
            
            // Vérifier le nombre total de fichiers
            if (mediaFiles.length + files.length > maxFiles) {
                alert(`Vous ne pouvez ajouter que ${maxFiles} fichiers maximum.`);
                return;
            }
            
            files.forEach(file => {
                // Vérifier la taille du fichier
                if (file.size > maxFileSize) {
                    alert(`Le fichier "${file.name}" est trop volumineux. Maximum 10MB.`);
                    return;
                }

                // Vérifier le type de fichier
                const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                const validVideoTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/webm'];
                
                if (!validImageTypes.includes(file.type) && !validVideoTypes.includes(file.type)) {
                    alert(`Le fichier "${file.name}" n'est pas un format valide. Formats acceptés: images (JPG, PNG, GIF) et vidéos (MP4, AVI, MOV).`);
                    return;
                }

                // Ajouter le fichier à la liste
                const mediaId = Date.now() + Math.random();
                mediaFiles.push({
                    file: file,
                    type: validImageTypes.includes(file.type) ? 'image' : 'video',
                    id: mediaId
                });

                // Créer la prévisualisation
                createMediaPreview(file, mediaId);
            });

            // Réinitialiser l'input
            input.value = '';
        }

        // Création de la prévisualisation
        function createMediaPreview(file, mediaId) {
            const preview = document.getElementById('mediaPreview');
            const reader = new FileReader();

            // Supprimer le placeholder s'il existe
            const placeholder = preview.querySelector('.preview-placeholder');
            if (placeholder) {
                placeholder.remove();
            }

            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.dataset.id = mediaId;

                // Déterminer si c'est une image ou une vidéo
                const isImage = file.type.startsWith('image/');
                
                if (isImage) {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Image preview">
                        <button type="button" class="remove-media" onclick="removeMedia('${mediaId}')" title="Supprimer">
                            <i class='bx bx-x'></i>
                        </button>
                    `;
                } else {
                    previewItem.innerHTML = `
                        <video controls style="width: 100%; height: 120px; background: #000;">
                            <source src="${e.target.result}" type="${file.type}">
                        </video>
                        <button type="button" class="remove-media" onclick="removeMedia('${mediaId}')" title="Supprimer">
                            <i class='bx bx-x'></i>
                        </button>
                    `;
                }

                preview.appendChild(previewItem);
                
                // Ajouter une animation
                previewItem.style.opacity = '0';
                previewItem.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    previewItem.style.transition = 'all 0.3s ease';
                    previewItem.style.opacity = '1';
                    previewItem.style.transform = 'scale(1)';
                }, 10);
            };

            reader.readAsDataURL(file);
        }

        // Suppression d'un média
        function removeMedia(id) {
            // Supprimer de la liste
            mediaFiles = mediaFiles.filter(media => media.id !== id);
            
            // Supprimer la prévisualisation avec animation
            const previewItem = document.querySelector(`.preview-item[data-id="${id}"]`);
            if (previewItem) {
                previewItem.style.transform = 'scale(0.8)';
                previewItem.style.opacity = '0';
                setTimeout(() => {
                    previewItem.remove();
                    
                    // Afficher le placeholder si plus de médias
                    const preview = document.getElementById('mediaPreview');
                    if (preview.children.length === 0) {
                        preview.innerHTML = `
                            <div class="preview-placeholder">
                                <p class="text-muted text-center">Aucun média sélectionné</p>
                            </div>
                        `;
                    }
                }, 300);
            }
            
            // Mettre à jour l'input file
            updateFileInput();
        }

        // Mettre à jour l'input file après suppression
        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            
            mediaFiles.forEach(media => {
                dataTransfer.items.add(media.file);
            });
            
            document.getElementById('mediasInput').files = dataTransfer.files;
        }

        // Gestion de la planification
        function selectOption(optionId) {
            // Désactiver toutes les options
            document.querySelectorAll('.planification-option').forEach(option => {
                option.classList.remove('active');
            });
            
            // Activer l'option sélectionnée
            const selectedOption = document.querySelector(`.planification-option[onclick*="${optionId}"]`);
            selectedOption.classList.add('active');
            
            // Cocher le radio button correspondant
            const radioBtn = document.getElementById(optionId);
            radioBtn.checked = true;
            
            // Afficher ou masquer les champs de date/heure
            const datetimeInputs = document.getElementById('datetime-inputs');
            if (optionId === 'schedule-publish') {
                datetimeInputs.classList.add('show');
                // Changer le texte du bouton de publication
                document.querySelector('.btn-publish').innerHTML = '<i class="bx bx-time"></i> Planifier la publication';
            } else {
                datetimeInputs.classList.remove('show');
                // Réinitialiser le texte du bouton de publication
                document.querySelector('.btn-publish').innerHTML = '<i class="bx bx-send"></i> Publier maintenant';
            }
        }

        // Fonction pour afficher un datepicker manuel
        function showDatePicker(input) {
            // Vérifier si un datepicker existe déjà
            let datepicker = document.getElementById('custom-datepicker');
            if (!datepicker) {
                datepicker = document.createElement('div');
                datepicker.id = 'custom-datepicker';
                datepicker.className = 'custom-datepicker';
                
                // Ajouter le datepicker au DOM
                document.body.appendChild(datepicker);
                
                // Positionner le datepicker
                const rect = input.getBoundingClientRect();
                datepicker.style.position = 'absolute';
                datepicker.style.top = (rect.bottom + window.scrollY) + 'px';
                datepicker.style.left = (rect.left + window.scrollX) + 'px';
                datepicker.style.zIndex = '9999';
                
                // Créer le contenu du datepicker
                datepicker.innerHTML = createDatePickerHTML();
                
                // Gérer la sélection de date
                datepicker.querySelectorAll('.date-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const dateValue = this.getAttribute('data-date');
                        input.value = dateValue;
                        document.body.removeChild(datepicker);
                    });
                });
                
                // Fermer le datepicker en cliquant à l'extérieur
                document.addEventListener('click', function closeDatepicker(e) {
                    if (!datepicker.contains(e.target) && e.target !== input) {
                        document.body.removeChild(datepicker);
                        document.removeEventListener('click', closeDatepicker);
                    }
                });
            }
        }

        // Fonction pour créer le HTML du datepicker manuel
        function createDatePickerHTML() {
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(today.getDate() + 1);
            
            const dates = [
                { label: 'Aujourd\'hui', date: formatDate(today) },
                { label: 'Demain', date: formatDate(tomorrow) }
            ];
            
            // Ajouter les 5 prochains jours
            for (let i = 2; i <= 6; i++) {
                const futureDate = new Date(today);
                futureDate.setDate(today.getDate() + i);
                dates.push({
                    label: futureDate.toLocaleDateString('fr-FR', { weekday: 'long' }),
                    date: formatDate(futureDate)
                });
            }
            
            let html = '<div class="datepicker-container">';
            html += '<div class="datepicker-header">Sélectionnez une date</div>';
            html += '<div class="datepicker-list">';
            
            dates.forEach(item => {
                html += `
                    <div class="date-option" data-date="${item.date}">
                        <strong>${item.label}</strong>
                        <small>${item.date}</small>
                    </div>
                `;
            });
            
            html += '</div></div>';
            
            // Ajouter le CSS du datepicker
            html += `
                <style>
                    .custom-datepicker {
                        background: white;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        padding: 10px;
                        width: 200px;
                    }
                    .datepicker-header {
                        font-weight: bold;
                        margin-bottom: 10px;
                        padding-bottom: 5px;
                        border-bottom: 1px solid #eee;
                    }
                    .date-option {
                        padding: 8px 10px;
                        margin: 2px 0;
                        border-radius: 4px;
                        cursor: pointer;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    .date-option:hover {
                        background-color: #f0f0f0;
                    }
                    .date-option small {
                        color: #666;
                    }
                </style>
            `;
            
            return html;
        }

        // Fonction pour formater la date
        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        // Fonction pour afficher un timepicker manuel
        function showTimePicker(input) {
            // Vérifier si un timepicker existe déjà
            let timepicker = document.getElementById('custom-timepicker');
            if (!timepicker) {
                timepicker = document.createElement('div');
                timepicker.id = 'custom-timepicker';
                timepicker.className = 'custom-timepicker';
                
                // Ajouter le timepicker au DOM
                document.body.appendChild(timepicker);
                
                // Positionner le timepicker
                const rect = input.getBoundingClientRect();
                timepicker.style.position = 'absolute';
                timepicker.style.top = (rect.bottom + window.scrollY) + 'px';
                timepicker.style.left = (rect.left + window.scrollX) + 'px';
                timepicker.style.zIndex = '9999';
                
                // Créer le contenu du timepicker
                timepicker.innerHTML = createTimePickerHTML();
                
                // Gérer la sélection d'heure
                timepicker.querySelectorAll('.time-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const timeValue = this.getAttribute('data-time');
                        input.value = timeValue;
                        document.body.removeChild(timepicker);
                    });
                });
                
                // Fermer le timepicker en cliquant à l'extérieur
                document.addEventListener('click', function closeTimepicker(e) {
                    if (!timepicker.contains(e.target) && e.target !== input) {
                        document.body.removeChild(timepicker);
                        document.removeEventListener('click', closeTimepicker);
                    }
                });
            }
        }

        // Fonction pour créer le HTML du timepicker manuel
        function createTimePickerHTML() {
            const times = [];
            
            // Créer des heures de 8h à 20h par pas de 30 minutes
            for (let hour = 8; hour <= 20; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    const hourStr = String(hour).padStart(2, '0');
                    const minuteStr = String(minute).padStart(2, '0');
                    times.push(`${hourStr}:${minuteStr}`);
                }
            }
            
            let html = '<div class="timepicker-container">';
            html += '<div class="timepicker-header">Sélectionnez une heure</div>';
            html += '<div class="timepicker-list" style="max-height: 200px; overflow-y: auto;">';
            
            times.forEach(time => {
                html += `
                    <div class="time-option" data-time="${time}">
                        ${time}
                    </div>
                `;
            });
            
            html += '</div></div>';
            
            // Ajouter le CSS du timepicker
            html += `
                <style>
                    .custom-timepicker {
                        background: white;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        padding: 10px;
                        width: 150px;
                    }
                    .timepicker-header {
                        font-weight: bold;
                        margin-bottom: 10px;
                        padding-bottom: 5px;
                        border-bottom: 1px solid #eee;
                    }
                    .time-option {
                        padding: 8px 10px;
                        margin: 2px 0;
                        border-radius: 4px;
                        cursor: pointer;
                        text-align: center;
                    }
                    .time-option:hover {
                        background-color: #f0f0f0;
                    }
                </style>
            `;
            
            return html;
        }

        // Validation du formulaire avant soumission
        function validateForm() {
            const planificationOption = document.querySelector('input[name="planification"]:checked').value;
            
            if (planificationOption === 'schedule') {
                const dateInput = document.getElementById('datePublication').value;
                const timeInput = document.getElementById('heurePublication').value;
                
                if (!dateInput || !timeInput) {
                    alert('Veuillez spécifier une date et une heure pour la publication planifiée.');
                    return false;
                }
                
                // Validation du format de date (JJ/MM/AAAA)
                const dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
                if (!dateRegex.test(dateInput)) {
                    alert('Format de date invalide. Utilisez JJ/MM/AAAA.');
                    return false;
                }
                
                // Validation du format d'heure (HH:MM)
                const timeRegex = /^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/;
                if (!timeRegex.test(timeInput)) {
                    alert('Format d\'heure invalide. Utilisez HH:MM (24h).');
                    return false;
                }
            }
            
            return true;
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser le compteur de caractères
            const textarea = document.getElementById('contenuTexte');
            if (textarea) {
                updateCharCount(textarea);
            }
            
            // Ajouter des effets visuels
            const mediaButtons = document.querySelectorAll('.btn-media');
            mediaButtons.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Ajouter la validation au formulaire
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
