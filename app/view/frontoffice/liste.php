<?php
require_once 'check_session.php';
include '../../controller/publicationC.php';
include '../../controller/CommentaireC.php';

$publicationC = new PublicationC();
$commentaireC = new CommentaireC();

// Récupérer TOUTES les publications
$allPublications = $publicationC->listePublicationsTrieesParLikes();

$idUtilisateur = isset($_SESSION['idUtilisateur']) ? $_SESSION['idUtilisateur'] : 1;
$postsLiked = isset($_SESSION['postsLiked']) ? $_SESSION['postsLiked'] : [];

// Récupérer le nombre de commentaires pour chaque publication
$commentCounts = [];
$allComments = [];
$allMedias = [];

foreach ($allPublications as $pub) {
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
        /* AJOUT: Style pour les publications futures */
        .publication-future {
            opacity: 0.7;
            position: relative;
            border-left: 6px solid #ffc107 !important;
        }
        
        .publication-future::before {
            content: "⏰ PLANIFIÉE";
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            z-index: 1;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
        }
        
        .future-date-info {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 10px 15px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #856404;
        }
        
        .future-date-info i {
            font-size: 18px;
        }
        
        .publication-future .publication-content {
            filter: blur(1px);
        }
        
        .publication-future .like-btn,
        .publication-future .comment-btn {
            pointer-events: none;
            opacity: 0.5;
        }
        
        .publication-future .publication-actions {
            opacity: 0.7;
        }

        /* Styles améliorés pour les publications */
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
            margin-bottom: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .header-section h1 {
            font-weight: 700;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-section .btn-custom {
            position: relative;
            z-index: 1;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border: none;
        }
        
        .header-section .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        
        .header-section .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        
        .header-section .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .publication-card {
            background: white;
            padding: 0;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .publication-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }
        
        .publication-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px 0 0 20px;
        }
        
        .publication-content {
            padding: 25px 30px 15px;
        }
        
        .publication-content p {
            font-size: 16px;
            line-height: 1.7;
            color: #2d3748;
            margin-bottom: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .publication-meta {
            padding: 15px 30px;
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }
        
        .publication-meta .col-md-6 {
            display: flex;
            align-items: center;
            height: 100%;
        }
        
        .publication-actions {
            padding: 20px 30px;
            background: #ffffff;
            border-radius: 0 0 20px 20px;
        }
        
        .like-btn, .comment-btn {
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
        }
        
        .like-btn {
            position: relative;
            overflow: hidden;
        }
        
        .like-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(231, 76, 60, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .like-btn:active::before {
            width: 200px;
            height: 200px;
        }
        
        .like-btn.liked {
            color: #e74c3c;
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.15);
            transform: scale(1.05);
        }
        
        .like-btn:not(.liked) {
            color: #64748b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        
        .like-btn:hover:not(.liked) {
            color: #e74c3c;
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
            border-color: rgba(231, 76, 60, 0.2);
            transform: translateY(-2px);
        }
        
        .comment-btn {
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
        
        .like-count, .comment-count {
            margin-left: 8px;
            font-weight: 700;
            font-size: 16px;
        }
        
        .like-btn.liked .like-count {
            color: #e74c3c;
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
        
        /* Scrollbar personnalisée pour la liste des commentaires */
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
        }
        
        .badge {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        .badge-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        
        .badge-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #000;
        }
        
        /* Styles pour les médias */
        .publication-media {
            margin: 25px 0;
        }
        
        .media-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .media-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            aspect-ratio: 1/1;
        }
        
        .media-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .media-item img,
        .media-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.5s ease;
            border-radius: 12px;
        }
        
        .media-item img:hover,
        .media-item video:hover {
            transform: scale(1.08);
        }
        
        .media-type-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, rgba(0,0,0,0.8), rgba(0,0,0,0.6));
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .media-count-badge {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            z-index: 2;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        
        .media-count-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        
        /* Modal amélioré */
        .media-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,0,0,0.95), rgba(0,0,0,0.85));
            justify-content: center;
            align-items: center;
            flex-direction: column;
            backdrop-filter: blur(10px);
            animation: modalFadeIn 0.3s ease;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                backdrop-filter: blur(0);
            }
            to {
                opacity: 1;
                backdrop-filter: blur(10px);
            }
        }
        
        .modal-content {
            max-width: 90%;
            max-height: 80%;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            animation: zoomIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .modal-video {
            width: 90%;
            max-height: 80%;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        
        .close-modal {
            position: absolute;
            top: 25px;
            right: 30px;
            color: white;
            font-size: 35px;
            cursor: pointer;
            z-index: 10001;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.8), rgba(220, 38, 38, 0.8));
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.2);
        }
        
        .close-modal:hover {
            transform: rotate(90deg) scale(1.1);
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 5px 20px rgba(239, 68, 68, 0.4);
        }
        
        .modal-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 35px;
            cursor: pointer;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.8), rgba(29, 78, 216, 0.8));
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.2);
            opacity: 0.8;
        }
        
        .modal-nav:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            box-shadow: 0 5px 25px rgba(59, 130, 246, 0.4);
        }
        
        .prev-modal {
            left: 30px;
        }
        
        .next-modal {
            right: 30px;
        }
        
        .modal-info {
            color: white;
            text-align: center;
            margin-top: 25px;
            font-size: 20px;
            font-weight: 600;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        
        .modal-counter {
            color: #cbd5e1;
            margin-top: 10px;
            font-size: 16px;
            letter-spacing: 1px;
        }
        
        /* Alerts améliorés */
        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 20px 25px;
            margin-bottom: 25px;
            animation: slideDown 0.4s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-left: 5px solid #047857;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-left: 5px solid #b91c1c;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            border-left: 5px solid #0e7490;
            text-align: center;
        }
        
        .alert .close {
            color: white;
            opacity: 0.8;
            transition: all 0.3s ease;
            position: relative;
            top: -2px;
        }
        
        .alert .close:hover {
            opacity: 1;
            transform: scale(1.2);
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
                        <a class="navbar-brand nav-item mr-lg-5" href="publications.php">`n                            <img src="./assets/images/logo-128x128.png" width="70" height="70" class="mr-3" alt="Logo">
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
                <!-- ======================= FIN EN-TÊTE ======================= -->

                <div class="row newsfeed-right-side-content mt-3">

                    <!-- ======================= SIDEBAR GAUCHE ======================= -->
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
                                    <a href="publications.php" class="btn btn-secondary btn-custom">
                                        ← Retour
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php 
                                if ($_GET['success'] == 1) {
                                    echo "Publication ajoutée avec succès!";
                                    if (isset($_GET['planifiee'])) {
                                        echo " (Planifiée)";
                                    }
                                }
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

                        <?php 
                        // Séparer les publications passées et futures
                        $dateNow = new DateTime();
                        $publicationsPubliees = [];
                        $publicationsPlanifiees = [];
                        
                        foreach ($allPublications as $pub) {
                            $datePublication = new DateTime($pub['datePublication']);
                            if ($datePublication <= $dateNow) {
                                $publicationsPubliees[] = $pub;
                            } else {
                                $publicationsPlanifiees[] = $pub;
                            }
                        }
                        
                        $totalPublications = count($publicationsPubliees) + count($publicationsPlanifiees);
                        ?>
                        
                        <?php if ($totalPublications == 0): ?>
                            <div class="alert alert-info text-center">
                                <h4>Aucune publication pour le moment</h4>
                                <p>Soyez le premier à partager quelque chose !</p>
                                <a href="ajout.php" class="btn btn-primary">Créer une publication</a>
                            </div>
                        <?php else: ?>
                            
                            <!-- Publications publiées (visibles) -->
                            <?php if (!empty($publicationsPubliees)): ?>
                                <?php foreach ($publicationsPubliees as $pub): 
                                    $userAlreadyLiked = in_array($pub['idPublication'], $postsLiked);
                                    $nbCommentaires = isset($commentCounts[$pub['idPublication']]) ? $commentCounts[$pub['idPublication']] : 0;
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
                            
                            <!-- Publications planifiées (futures) -->
                            <?php if (!empty($publicationsPlanifiees)): ?>
                                <div class="alert alert-info text-center">
                                    <h5>📅 Publications planifiées</h5>
                                    <p>Ces publications seront visibles à la date indiquée</p>
                                </div>
                                
                                <?php foreach ($publicationsPlanifiees as $pub): ?>
                                    <div class="publication-card publication-future">
                                        <div class="publication-content">
                                            <p class="mb-2" style="font-size: 16px; line-height: 1.6; opacity: 0.7;"><?= nl2br(htmlspecialchars($pub['contenuTexte'])) ?></p>
                                            
                                            <div class="future-date-info">
                                                <i class='bx bx-time'></i>
                                                <div>
                                                    <strong>Publication planifiée pour le :</strong>
                                                    <div><?= date('d/m/Y à H:i', strtotime($pub['datePublication'])) ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="publication-meta text-muted small" style="opacity: 0.7;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        👤 Utilisateur <?= $pub['idUtilisateur'] ?> 
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        ❤️ <span class="like-count">0</span> likes
                                                        | 💬 0 commentaires
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="publication-actions" style="opacity: 0.7;">
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <span class="badge badge-warning">⏰ EN ATTENTE</span>
                                                    <small class="text-muted ml-2">Publication automatique</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
                                            <?php echo $totalPublications ?> publications
                                        </div>
                                    </div>
                                </div>
                                <div class="media text-muted pt-2">
                                    <i class='bx bx-check-circle mr-3 text-success'></i>
                                    <div class="media-body">
                                        <strong>Publiées</strong>
                                        <div class="text-muted small">
                                            <?php echo count($publicationsPubliees) ?> visibles
                                        </div>
                                    </div>
                                </div>
                                <div class="media text-muted pt-2">
                                    <i class='bx bx-time mr-3 text-warning'></i>
                                    <div class="media-body">
                                        <strong>Planifiées</strong>
                                        <div class="text-muted small">
                                            <?php echo count($publicationsPlanifiees) ?> en attente
                                        </div>
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
                                    <a href="#" style="color: #28a745; font-weight: 500;">Voir plus</a>
                                </small>
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
