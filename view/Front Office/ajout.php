<?php
include("../../controller/publicationC.php");


if ($_POST) {
    $contenuTexte = $_POST['contenuTexte'];
    $idUtilisateur = $_POST['idUtilisateur'];
    $nbLikes = $_POST['nbLikes'];

    if (!empty($contenuTexte)) {
        $publication = new Publication($contenuTexte, $idUtilisateur, $nbLikes);
        $publicationC = new PublicationC();
        $result = $publicationC->addPublication($publication);

        if ($result) {
            header("Location: liste.php?success=1");
        } else {
            header("Location: ajout.php?error=0");
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

    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@1.9.2/css/boxicons.min.css' rel='stylesheet'>
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/components.css" rel="stylesheet">
    <link href="./assets/css/media.css" rel="stylesheet">
    <link href="https://vjs.zencdn.net/7.4.1/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    <style>
        .form-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            padding: 10px 30px;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            color: white;
        }
        .media-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .btn-media {
            flex: 1;
            padding: 10px 15px;
            border: 2px dashed #dee2e6;
            background: #f8f9fa;
            color: #495057;
            transition: all 0.3s ease;
        }
        .btn-media:hover {
            background: #e9ecef;
            border-color: #007bff;
            color: #007bff;
        }
        .media-preview {
            margin-top: 15px;
        }
        .preview-item {
            position: relative;
            display: inline-block;
            margin: 5px;
            border-radius: 8px;
            overflow: hidden;
        }
        .preview-item img, .preview-item video {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .remove-media {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
        }
        .file-input {
            display: none;
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
                        <div class="form-container">
                            <h2 class="text-center mb-4">Nouvelle Publication</h2>
                            
                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger">
                                    <?php 
                                    if ($_GET['error'] == 0) echo "Erreur lors de l'ajout de la publication";
                                    if ($_GET['error'] == 2) echo "Le contenu ne peut pas être vide";
                                    ?>
                                </div>
                            <?php endif; ?>

                            <form action="ajout.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="contenuTexte" class="font-weight-bold">Contenu :</label>
                                    <textarea name="contenuTexte" id="contenuTexte" class="form-control" rows="6" placeholder="Quoi de neuf ?" required></textarea>
                                </div>

                                <!-- Boutons pour ajouter médias -->
                                <div class="media-buttons">
                                    <button type="button" class="btn btn-media" onclick="document.getElementById('imageInput').click()">
                                        <i class='bx bx-image'></i> Ajouter image(s)
                                    </button>
                                    <button type="button" class="btn btn-media" onclick="document.getElementById('videoInput').click()">
                                        <i class='bx bx-video'></i> Ajouter vidéo(s)
                                    </button>
                                </div>

                                <!-- Inputs fichiers cachés -->
                                <input type="file" id="imageInput" class="file-input" accept="image/*" multiple onchange="handleMediaSelection(this, 'image')">
                                <input type="file" id="videoInput" class="file-input" accept="video/*" multiple onchange="handleMediaSelection(this, 'video')">

                                <!-- Prévisualisation des médias -->
                                <div class="media-preview" id="mediaPreview"></div>

                                <input type="hidden" name="idUtilisateur" value="1">
                                <input type="hidden" name="nbLikes" value="0">

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-custom mr-3">Publier</button>
                                    <a href="liste.php" class="btn btn-secondary">Annuler</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ======================= FIN COLONNE CENTRALE ======================= -->

                    <!-- ======================= SIDEBAR DROIT ======================= -->
                    <div class="col-md-3 third-section">
                        <div class="p-3 bg-white rounded w-shadow">
                            <h6 class="card-title pb-3 mb-0">Gestion CRUD</h6>
                            <div class="list-group">
                                <a href="ajout.php" class="list-group-item list-group-item-action active">
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

    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>

    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

        // Gestion des médias
        let mediaFiles = [];

        function handleMediaSelection(input, type) {
            const files = Array.from(input.files);
            
            files.forEach(file => {
                // Vérifier la taille du fichier (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Le fichier ' + file.name + ' est trop volumineux. Maximum 10MB.');
                    return;
                }

                // Ajouter le fichier à la liste
                mediaFiles.push({
                    file: file,
                    type: type,
                    id: Date.now() + Math.random()
                });

                // Créer la prévisualisation
                createMediaPreview(file, type);
            });

            // Réinitialiser l'input
            input.value = '';
        }

        function createMediaPreview(file, type) {
            const preview = document.getElementById('mediaPreview');
            const reader = new FileReader();

            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.dataset.id = mediaFiles[mediaFiles.length - 1].id;

                if (type === 'image') {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-media" onclick="removeMedia('${previewItem.dataset.id}')">×</button>
                    `;
                } else {
                    previewItem.innerHTML = `
                        <video controls>
                            <source src="${e.target.result}" type="${file.type}">
                        </video>
                        <button type="button" class="remove-media" onclick="removeMedia('${previewItem.dataset.id}')">×</button>
                    `;
                }

                preview.appendChild(previewItem);
            };

            reader.readAsDataURL(file);
        }

        function removeMedia(id) {
            // Supprimer de la liste
            mediaFiles = mediaFiles.filter(media => media.id !== id);
            
            // Supprimer la prévisualisation
            const previewItem = document.querySelector(`.preview-item[data-id="${id}"]`);
            if (previewItem) {
                previewItem.remove();
            }
        }

        // Gestion de la soumission du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            // Ici vous pouvez ajouter la logique pour envoyer les fichiers
            // via AJAX ou les ajouter au FormData
            console.log('Médias à uploader:', mediaFiles);
            
            // Pour l'instant, on laisse le formulaire se soumettre normalement
            // Vous devrez adapter le backend pour gérer les fichiers
        });
    </script>
</body>
</html>