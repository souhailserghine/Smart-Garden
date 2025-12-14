<?php require_once 'check_session.php'; ?>
<!DOCTYPE html>
<html lang="fr" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    <title>SmartGarden - Événements</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Styles -->
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/components.css" rel="stylesheet">
    <link href="./assets/css/media.css" rel="stylesheet">
    <link href="./assets/css/frontoffice-improvements.css" rel="stylesheet">
    <link href="https://vjs.zencdn.net/7.4.1/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    
    <style>
        body.newsfeed {
            background: #f5f5f5 !important;
        }

        .newsfeed-right-side-content {
            background: transparent !important;
        }

        #page-content-wrapper {
            padding: 0;
            background: transparent;
        }

        .second-section {
            padding: 20px;
            overflow-y: auto;
            max-height: calc(100vh - 100px);
        }

        /* Loading Spinner */
        #eventsLoading {
            text-align: center;
            padding: 3rem 0;
        }

        #eventsLoading .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
            border-color: #4CAF50;
            border-right-color: transparent;
        }

        #eventsLoading p {
            color: #2E7D32;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Modal */
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            border: none;
            padding: 1.5rem 2rem;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .modal-footer {
            border: none;
            padding: 1rem 1.5rem;
        }

        /* Third Section (Right Sidebar) Styles */
        .third-section {
            background: transparent;
            padding: 20px;
        }

        .third-section .bg-white {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(76, 175, 80, 0.1);
        }

        /* Adjustments for template */
        .newsfeed-left-side {
            background: transparent;
        }

        .newsfeed-user-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(76, 175, 80, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .second-section {
                padding: 15px;
            }
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

                        <form class="w-30 mx-2 my-auto d-inline form-inline mr-5">
                            <div class="input-group">
                                <input type="text" class="form-control search-input w-75"
                                    placeholder="Search for people, companies, events and more..." aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn search-button" type="button"><i class='bx bx-search'></i></button>
                                </div>
                            </div>
                        </form>

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
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="plantes.php" class="sidebar-item"><img src="./assets/images/icons/left-sidebar/group.png" alt="plantes"> Plantes</a>
                                    <span class="badge badge-primary badge-pill"><i class='bx bx-chevron-right'></i></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center sd-active">
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
                    <!-- ======================= FIN SIDEBAR GAUCHE ======================= -->

                    <!-- ======================= CONTENU PRINCIPAL - ÉVÉNEMENTS ======================= -->
                    <div class="col-md-9 second-section" id="page-content-wrapper" style="padding: 20px;">
                        <div class="events-page">
                            <div class="hero-banner-improved">
                                <h1>🎉 Événements SmartGarden</h1>
                                <p>Découvrez nos ateliers, formations et animations autour de l'agriculture durable et écologique</p>
                            </div>

                            <div id="eventsLoading" class="text-center py-5">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p class="mt-4 fs-5 text-white fw-bold">Chargement des événements...</p>
                            </div>

                            <div id="eventsByCategory"></div>
                        </div>

                        <!-- MODAL RÉSERVATION -->
                        <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="reservationModalTitle">
                                            <i class='bx bx-calendar-check me-2'></i>Réserver ma place
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form id="reservationForm" novalidate>
                                        <div class="modal-body">
                                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                                <i class='bx bx-info-circle me-2'></i>
                                                <div><strong>Nom et prénom uniquement requis</strong></div>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="reservation_nom" class="form-label fw-semibold">Nom *</label>
                                                    <input type="text" id="reservation_nom" class="form-control" placeholder="Votre nom" required>
                                                    <div class="invalid-feedback">Le nom est obligatoire</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="reservation_prenom" class="form-label fw-semibold">Prénom *</label>
                                                    <input type="text" id="reservation_prenom" class="form-control" placeholder="Votre prénom" required>
                                                    <div class="invalid-feedback">Le prénom est obligatoire</div>
                                                </div>
                                                <div class="col-12">
                                                    <label for="reservation_telephone" class="form-label fw-semibold">Téléphone <small class="text-muted">(facultatif)</small></label>
                                                    <input type="tel" id="reservation_telephone" class="form-control" placeholder="Votre numéro de téléphone">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class='bx bx-x me-2'></i>Annuler
                                            </button>
                                            <button type="submit" class="btn btn-reserver">
                                                <i class='bx bx-check me-2'></i>Confirmer la réservation
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ======================= FIN CONTENU PRINCIPAL ======================= -->

                    <!-- ======================= SIDEBAR DROIT - HIDDEN FOR MORE SPACE ======================= -->
                    <div class="col-md-3 third-section" style="display: none;">
                        <div class="p-3 bg-white rounded w-shadow">
                            <h6 class="card-title pb-3 mb-0">Mes Réservations</h6>
                            <div class="list-group" id="myReservationsList">
                                <div class="list-group-item list-group-item-action text-center">
                                    <i class='bx bx-time-five text-muted mb-2' style="font-size: 2rem;"></i>
                                    <div class="text-muted small">Chargement...</div>
                                </div>
                            </div>

                            <h6 class="card-title pb-3 mb-0 mt-4">Statistiques</h6>
                            <div class="bg-white rounded contacts">
                                <div class="media text-muted pt-2">
                                    <i class='bx bx-calendar-event mr-3 text-success'></i>
                                    <div class="media-body">
                                        <strong>Événements à venir</strong>
                                        <div class="text-muted small" id="upcomingCount">Chargement...</div>
                                    </div>
                                </div>
                                <div class="media text-muted pt-2">
                                    <i class='bx bx-check-circle mr-3 text-primary'></i>
                                    <div class="media-body">
                                        <strong>Mes réservations</strong>
                                        <div class="text-muted small" id="myReservationsCount">Chargement...</div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-title pb-3 mb-0 mt-4">Catégories</h5>
                            <div class="bg-white rounded contacts">
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge badge-culturel">Culturel</span>
                                    <span class="badge badge-sport">Sport</span>
                                    <span class="badge badge-divertissement">Divertissement</span>
                                    <span class="badge badge-educatif">Éducatif</span>
                                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Constantes API
        const API_EVENTS = './events.php';
        const API_CATEGORIES = './categories.php';
        const API_RESERVATIONS = './reservations.php';
        const CURRENT_USER_ID = 999; // À remplacer par l'ID utilisateur réel
        let reservationModal = null;
        let currentEventId = null;

        // Initialisation au chargement
        document.addEventListener('DOMContentLoaded', () => {
            // Initialiser le modal Bootstrap
            reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));

            // Gestion du formulaire de réservation
            document.getElementById('reservationForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const nom = document.getElementById('reservation_nom');
                const prenom = document.getElementById('reservation_prenom');
                
                // Validation
                let isValid = true;
                if (!nom.value.trim()) {
                    nom.classList.add('is-invalid');
                    isValid = false;
                } else {
                    nom.classList.remove('is-invalid');
                }
                
                if (!prenom.value.trim()) {
                    prenom.classList.add('is-invalid');
                    isValid = false;
                } else {
                    prenom.classList.remove('is-invalid');
                }
                
                if (!isValid) return;

                try {
                    // Envoyer la réservation
                    const response = await fetch(API_RESERVATIONS + '?action=create', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            id_event: currentEventId,
                            idUtilisateur: CURRENT_USER_ID,
                            date_reservation: new Date().toISOString().split('T')[0] + ' ' + new Date().toTimeString().split(' ')[0],
                            nom: nom.value.trim(),
                            prenom: prenom.value.trim(),
                            email: 'agri@smartgarden.local',
                            telephone: document.getElementById('reservation_telephone').value || null
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === "success") {
                        showSuccessToast('Réservation confirmée !');
                        reservationModal.hide();
                        loadEvents();
                        loadMyReservations();
                    } else {
                        alert('Erreur : ' + (data.message || 'Impossible de réserver'));
                    }
                } catch (error) {
                    console.error('Erreur réseau:', error);
                    alert('Erreur réseau. Veuillez réessayer.');
                }
            });

            // Charger les événements et réservations
            loadEvents();
            loadMyReservations();

            // Initialiser le toggle du sidebar
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });
        });

        // Fonction pour réserver un événement
        window.reserveEvent = function(id, title) {
            currentEventId = id;
            document.getElementById('reservationModalTitle').innerHTML = 
                `<i class='bx bx-calendar-check me-2'></i>Réserver ma place - ${title}`;
            document.getElementById('reservationForm').reset();
            document.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
            reservationModal.show();
        };

        // Fonction pour annuler une réservation
        window.cancelReservation = function(eventId) {
            showConfirmToast(eventId);
        };

        // Fonction pour confirmer l'annulation
        async function confirmCancel(eventId) {
            document.getElementById('confirmToast')?.remove();

            try {
                const res = await fetch(API_RESERVATIONS + '?action=delete&id=' + eventId, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                const data = await res.json();

                if (data.status === "success") {
                    showSuccessToast('Réservation annulée !');
                    loadEvents();
                    loadMyReservations();
                } else {
                    alert('Erreur : ' + (data.message || ''));
                }
            } catch {
                alert('Erreur réseau');
            }
        }

        // Toast de confirmation
        function showConfirmToast(eventId) {
            const old = document.getElementById('confirmToast');
            if (old) old.remove();

            const toast = document.createElement('div');
            toast.id = 'confirmToast';
            toast.innerHTML = `
                <div style="position:fixed;bottom:30px;right:30px;z-index:9999;animation:slideInRight 0.4s ease;">
                    <div style="background:linear-gradient(135deg, #2d3436, #1a1a2e);color:white;border-radius:20px;padding:25px 30px;min-width:380px;box-shadow:0 20px 50px rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.1);">
                        <div style="font-weight:700;font-size:1.3rem;margin-bottom:12px;display:flex;align-items:center;gap:10px;">
                            <i class='bx bx-error-circle' style="font-size:1.5rem;color:#E74C3C;"></i>
                            Confirmer l'annulation
                        </div>
                        <div style="margin-bottom:25px;opacity:0.95;font-size:1rem;line-height:1.5;">Voulez-vous vraiment annuler cette réservation ?</div>
                        <div style="display:flex;justify-content:flex-end;gap:15px;">
                            <button onclick="this.closest('#confirmToast').remove()" style="background:transparent;border:2px solid #636e72;color:#bdc3c7;padding:10px 25px;border-radius:50px;cursor:pointer;font-weight:600;transition:all 0.3s;">Annuler</button>
                            <button onclick="confirmCancel(${eventId})" style="background:linear-gradient(135deg, #E74C3C, #C0392B);border:none;color:white;padding:10px 30px;border-radius:50px;cursor:pointer;font-weight:600;box-shadow:0 5px 20px rgba(231,76,60,0.4);transition:all 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">Oui, annuler</button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(toast);
        }

        // Toast de succès
        function showSuccessToast(message) {
            const toast = document.createElement('div');
            toast.innerHTML = `
                <div style="position:fixed;bottom:30px;right:30px;z-index:9999;animation:slideInRight 0.4s ease;">
                    <div style="background:linear-gradient(135deg, #4CAF50, #2E7D32);color:white;border-radius:20px;padding:20px 30px;display:flex;align-items:center;gap:15px;box-shadow:0 15px 40px rgba(76,175,80,0.4);min-width:300px;">
                        <i class='bx bx-check-circle' style="font-size:2.5rem;"></i>
                        <div style="font-weight:600;font-size:1.1rem;">${message}</div>
                    </div>
                </div>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Charger les événements
        async function loadEvents() {
            const container = document.getElementById('eventsByCategory');
            
            try {
                const res = await fetch(API_EVENTS + '?action=list');
                const {data: events} = await res.json();

                // Récupérer les réservations de l'utilisateur
                const reserved = await fetch(API_RESERVATIONS + '?action=by-user&id=' + CURRENT_USER_ID, {
                    method: 'GET',
                    headers: {'Content-Type': 'application/json'}
                }).then(r => r.json())
                  .then(d => d.status === "success" ? d.data.map(x => +x) : []);

                // Grouper par catégorie
                const grouped = {};
                events.forEach(e => {
                    const c = e.nom_categorie || "Autres";
                    if (!grouped[c]) grouped[c] = [];
                    grouped[c].push(e);
                });

                // Mapper les catégories
                const catMap = { 
                    "Culturel": "culturel", 
                    "Sport": "sport", 
                    "Divertissement": "divertissement", 
                    "Éducatif": "educatif",
                    "Autres": "educatif"
                };

                // Icônes pour les catégories
                const icons = {
                    "Culturel": "ri-building-2-fill",
                    "Sport": "ri-run-line",
                    "Divertissement": "ri-music-2-fill",
                    "Éducatif": "ri-book-open-fill",
                    "Autres": "ri-leaf-line"
                };

                // Générer le HTML avec les cartes modernes
                let html = '';
                Object.keys(grouped).sort().forEach(cat => {
                    const key = catMap[cat] || "educatif";
                    const icon = icons[cat] || "ri-leaf-line";

                    html += `
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="me-3">
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #22c55e, #16a34a); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="${icon}" style="font-size: 24px; color: white;"></i>
                                </div>
                            </div>
                            <div>
                                <h2 style="margin: 0; font-size: 1.75rem; font-weight: 700; color: #1a1a1a;">${cat}</h2>
                                <p style="margin: 0; color: #666; font-size: 0.95rem;">
                                    <i class='bx bx-calendar-event'></i> ${grouped[cat].length} événement${grouped[cat].length>1?'s':''}
                                </p>
                            </div>
                        </div>

                        <div class="events-grid">
                            ${grouped[cat].map(e => {
                                const id = +e.id_event;
                                const isReserved = reserved.includes(id);
                                const safeTitle = (e.type_event||"").replace(/'/g,"\\'");
                                
                                // Determine status badge
                                const today = new Date();
                                const eventDate = new Date(e.date_event);
                                let statusBadge = '';
                                
                                if (isReserved) {
                                    statusBadge = '<div class="event-badge badge-green">✓ Réservé</div>';
                                } else if (eventDate < today) {
                                    statusBadge = '<div class="event-badge badge-blue">Terminé</div>';
                                } else {
                                    const daysUntil = Math.ceil((eventDate - today) / (1000 * 60 * 60 * 24));
                                    if (daysUntil <= 7) {
                                        statusBadge = '<div class="event-badge badge-orange">Bientôt!</div>';
                                    } else {
                                        statusBadge = '<div class="event-badge badge-purple">À venir</div>';
                                    }
                                }
                                
                                // Determine button
                                let buttonHtml = '';
                                if (isReserved) {
                                    buttonHtml = `<button class="event-btn" style="background: linear-gradient(135deg, #ef4444, #dc2626);" onclick="cancelReservation(${id})">
                                        <i class='bx bx-x-circle'></i> Annuler ma réservation
                                    </button>`;
                                } else if (eventDate >= today) {
                                    buttonHtml = `<button class="event-btn" onclick="reserveEvent(${id}, '${safeTitle}')">
                                        <i class='bx bx-check-circle'></i> Réserver ma place
                                    </button>`;
                                } else {
                                    buttonHtml = `<button class="event-btn" style="background: #9ca3af; cursor: not-allowed;" disabled>
                                        <i class='bx bx-time'></i> Événement terminé
                                    </button>`;
                                }
                                
                                return `
                                <div class="event-card-improved">
                                    <div class="event-image-wrapper">
                                        <img src="https://picsum.photos/400/250?random=${id}&category=nature" alt="${e.type_event}">
                                        ${statusBadge}
                                    </div>
                                    <div class="event-content">
                                        <h3 class="event-title">${e.type_event}</h3>
                                        <div class="event-meta">
                                            <i class='bx bx-calendar'></i>
                                            <span>${new Date(e.date_event).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                                        </div>
                                        <p class="event-description">${e.description || 'Rejoignez-nous pour cet événement passionnant autour du jardinage et de l\'agriculture durable !'}</p>
                                    </div>
                                    <div class="event-footer">
                                        ${buttonHtml}
                                    </div>
                                </div>`;
                            }).join('')}
                        </div>
                    </div>`;
                });

                container.innerHTML = html;
                document.getElementById('eventsLoading').style.display = 'none';
                
                // Mettre à jour le compteur d'événements à venir
                document.getElementById('upcomingCount').textContent = events.length + ' événements';
                
            } catch (error) {
                console.error('Erreur lors du chargement des événements:', error);
                container.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class='bx bx-error-alt' style="font-size: 2rem;"></i>
                        <h4 class="mt-3">Erreur de chargement</h4>
                        <p>Impossible de charger les événements. Veuillez réessayer plus tard.</p>
                        <button class="btn btn-primary mt-2" onclick="loadEvents()">Réessayer</button>
                    </div>`;
                document.getElementById('eventsLoading').style.display = 'none';
            }
        }

        // Charger mes réservations
        async function loadMyReservations() {
            const listContainer = document.getElementById('myReservationsList');
            
            try {
                const response = await fetch(API_RESERVATIONS + '?action=by-user&id=' + CURRENT_USER_ID, {
                    method: 'GET',
                    headers: {'Content-Type': 'application/json'}
                });
                
                const data = await response.json();
                
                if (data.status === "success" && data.data.length > 0) {
                    // Récupérer les détails des événements réservés
                    const eventsResponse = await fetch(API_EVENTS + '?action=list');
                    const {data: events} = await eventsResponse.json();
                    
                    const myEvents = events.filter(event => data.data.includes(+event.id_event));
                    
                    let html = '';
                    myEvents.slice(0, 3).forEach(event => {
                        html += `
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${event.type_event}</h6>
                                <small>${event.date_event}</small>
                            </div>
                            <p class="mb-1 text-muted small">${event.nom_categorie || 'Événement'}</p>
                        </a>`;
                    });
                    
                    if (myEvents.length > 3) {
                        html += `<a href="#" class="list-group-item list-group-item-action text-center small text-primary">
                            Voir les ${myEvents.length - 3} autres...
                        </a>`;
                    }
                    
                    listContainer.innerHTML = html;
                    document.getElementById('myReservationsCount').textContent = myEvents.length + ' réservations';
                } else {
                    listContainer.innerHTML = `
                        <div class="list-group-item list-group-item-action text-center">
                            <i class='bx bx-calendar-x text-muted mb-2' style="font-size: 2rem;"></i>
                            <div class="text-muted small">Aucune réservation</div>
                        </div>`;
                    document.getElementById('myReservationsCount').textContent = '0 réservation';
                }
            } catch (error) {
                console.error('Erreur lors du chargement des réservations:', error);
                listContainer.innerHTML = `
                    <div class="list-group-item list-group-item-action text-center">
                        <i class='bx bx-error text-danger mb-2' style="font-size: 2rem;"></i>
                        <div class="text-danger small">Erreur de chargement</div>
                    </div>`;
            }
        }
    </script>
</body>
</html>

