<?php require_once 'check_session.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="img/logo-16x16.png" />
    <title>SmartGarden</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/capteur.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <style>
        /* Grid compatibility */
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

        #mapPreview {
            height: 320px !important;
            width: 100%;
            border-radius: 12px;
            border: 2px solid var(--primary-green);
            margin-top: 10px;
        }

        .modal.show #mapPreview {
            height: 320px !important;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            color: white;
            border: none;
        }

        mark {
            background: #fef3c7;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3 style="font-size: 1.2rem;padding-left: 20px; color: #2ecc71;">
                    <img src="img/logo-64x64.png" alt="Logo" class="me-2" style="width: 32px; height: 32px; vertical-align: middle;">SmartGarden
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
                <a href="evenements.php" class="menu-item active">
                    <i class="fas fa-calendar"></i>
                    <span>Evenements</span>
                </a>
                <a href="reservations.php" class="menu-item">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Réservations</span>
                </a>
                <a href="utilisateurs.php" class="menu-item">
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
                <!-- Content -->
    <div class="w-100">
        <!-- Header -->
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-0" style="color: var(--text-primary); font-weight: 700;">Gestion des événements</h4>
                    <small style="color: var(--text-secondary);">Créer, modifier et visualiser les événements</small>
                </div>
                <div>
                    <button class="btn" style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; margin-right: 10px;" data-bs-toggle="modal" data-bs-target="#eventModal">
                        <i class="fa fa-plus me-2"></i> Nouvel événement
                    </button>
                    <button id="refreshBtn" class="btn" style="border: 2px solid var(--border-color); color: var(--text-secondary); padding: 10px 20px; border-radius: 8px; font-weight: 600; background: white;">
                        <i class="fa fa-sync me-2"></i> Rafraîchir
                    </button>
                </div>
            </div>

            <!-- Système de recherche et tri avancé -->
            <div class="bg-white rounded-4 p-4 shadow-lg mb-4 border" style="border-color: rgba(76, 175, 80, 0.2) !important;">
                <div class="row g-3">
                    <div class="col-md-12 mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: var(--primary-dark);">
                                <i class="fa fa-search me-2"></i>Recherche et tri avancés
                            </h5>
                            <small class="text-muted">
                                <i class="fa fa-info-circle me-1"></i>
                                Recherchez et triez les événements selon plusieurs critères
                            </small>
                        </div>
                        <button type="button" id="toggleAdvancedSearch" class="btn btn-outline-success btn-sm">
                            <i class="fa fa-cog me-1"></i>Options avancées
                        </button>
                    </div>
                    
                    <!-- Barre de recherche principale améliorée -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-search me-2"></i>Recherche intelligente
                        </label>
                        <div class="input-group position-relative">
                            <span class="input-group-text bg-light">
                                <i class="fa fa-search text-success"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control" 
                                   placeholder="Rechercher par titre, description, lieu, catégorie... (ex: 'formation jardin')"
                                   autocomplete="off">
                            <button type="button" id="clearSearch" class="btn btn-outline-secondary" title="Effacer">
                                <i class="fa fa-times"></i>
                            </button>
                            <!-- Suggestions de recherche -->
                            <div id="searchSuggestions" class="position-absolute w-100 bg-white border rounded-bottom shadow-lg" 
                                 style="top: 100%; z-index: 1000; display: none; max-height: 300px; overflow-y: auto;">
                                <div class="list-group list-group-flush">
                                    <!-- Suggestions dynamiques -->
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fa fa-lightbulb me-1"></i>
                            Astuce : Utilisez plusieurs mots-clés séparés par des espaces
                        </small>
                    </div>
                    
                    <!-- Statistiques rapides -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-chart-bar me-2"></i>Vue d'ensemble
                        </label>
                        <div class="d-flex gap-2">
                            <div class="flex-fill text-center p-2 rounded" style="background: rgba(76, 175, 80, 0.1);">
                                <div class="fw-bold text-success" id="quickStatTotal">0</div>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="flex-fill text-center p-2 rounded" style="background: rgba(46, 125, 50, 0.1);">
                                <div class="fw-bold" style="color: var(--primary-dark);" id="quickStatFiltered">0</div>
                                <small class="text-muted">Filtrés</small>
                            </div>
                        </div>
                    </div>

                    <!-- Filtre par catégorie -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-tags me-2"></i>Catégorie
                        </label>
                        <select id="filterCategory" class="form-select">
                            <option value="">Toutes les catégories</option>
                            <!-- Options chargées dynamiquement -->
                        </select>
                    </div>

                    <!-- Filtre par statut -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-info-circle me-2"></i>Statut
                        </label>
                        <select id="filterStatus" class="form-select">
                            <option value="all">Tous</option>
                            <option value="upcoming">À venir</option>
                            <option value="past">Passés</option>
                            <option value="today">Aujourd'hui</option>
                        </select>
                    </div>

                    <!-- Options de tri améliorées -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-sort me-2"></i>Trier par
                        </label>
                        <select id="sortBy" class="form-select">
                            <option value="date_desc">📅 Date (récent → ancien)</option>
                            <option value="date_asc">📅 Date (ancien → récent)</option>
                            <option value="title_asc">🔤 Titre (A → Z)</option>
                            <option value="title_desc">🔤 Titre (Z → A)</option>
                            <option value="category_asc">🏷️ Catégorie (A → Z)</option>
                        </select>
                    </div>

                    <!-- Nombre de résultats par page -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-list me-2"></i>Par page
                        </label>
                        <select id="perPageSelect" class="form-select">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <!-- Actions améliorées -->
                    <div class="col-md-7 d-flex align-items-end gap-2">
                        <div class="btn-group flex-fill">
                            <button type="button" id="exportBtn" class="btn btn-success" title="Exporter les résultats">
                                <i class="fa fa-download me-2"></i>Exporter
                            </button>
                            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Options d'export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="exportCSV"><i class="fa fa-file-csv me-2"></i>CSV</a></li>
                                <li><a class="dropdown-item" href="#" id="exportJSON"><i class="fa fa-file-code me-2"></i>JSON</a></li>
                            </ul>
                        </div>
                        <button type="button" id="resetSearchBtn" class="btn btn-outline-secondary" title="Réinitialiser tous les filtres">
                            <i class="fa fa-redo me-2"></i>Réinitialiser
                        </button>
                    </div>
                    
                    <!-- Options avancées (masquées par défaut) -->
                    <div id="advancedSearchOptions" class="col-md-12 mt-3" style="display: none;">
                        <div class="card border-success">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fa fa-cog me-2"></i>Options avancées de recherche</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fa fa-calendar me-2"></i>Période
                                        </label>
                                        <div class="input-group">
                                            <input type="date" id="filterDateStart" class="form-control" placeholder="Début">
                                            <input type="date" id="filterDateEnd" class="form-control" placeholder="Fin">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fa fa-map-marker-alt me-2"></i>Recherche par lieu
                                        </label>
                                        <input type="text" id="filterLocation" class="form-control" 
                                               placeholder="Filtrer par lieu...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fa fa-filter me-2"></i>Opérateur de recherche
                                        </label>
                                        <select id="searchOperator" class="form-select">
                                            <option value="OR">OU (au moins un mot)</option>
                                            <option value="AND">ET (tous les mots)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Résultats de recherche -->
                    <div class="col-md-12">
                        <div id="searchResultsInfo" class="alert alert-success mb-0 d-none">
                            <i class="fa fa-check-circle me-2"></i>
                            <strong id="searchResultsText"></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="container-fluid px-4 mb-5">
            <div class="row g-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stats-card">
                        <i class="fa fa-calendar-alt mb-3" style="font-size: 3.5rem;"></i>
                        <h3 id="statTotal" class="fw-bold">0</h3>
                        <p class="mb-0">Total événements</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stats-card">
                        <i class="fa fa-clock mb-3" style="font-size: 3.5rem;"></i>
                        <h3 id="statUpcoming" class="fw-bold">0</h3>
                        <p class="mb-0">À venir</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stats-card">
                        <i class="fa fa-check-circle mb-3" style="font-size: 3.5rem;"></i>
                        <h3 id="statPast" class="fw-bold">0</h3>
                        <p class="mb-0">Passés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table + Calendar -->
        <div class="container-fluid px-4">
            <div class="row g-4">
                <!-- Table Full Width -->
                <div class="col-12">
                    <div class="bg-white rounded-4 p-4 shadow-lg card-custom">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 fw-bold text-dark">Liste des événements</h5>
                            <small id="listMessage" class="text-muted fw-medium">Chargement...</small>
                        </div>
                      <div class="table-responsive">
    <table class="table text-start align-middle table-bordered table-hover mb-0">
        <thead>
            <tr class="text-dark">
                <th scope="col">#</th>
                <th scope="col">Titre</th>
                <th scope="col">Date</th>
                <th scope="col">Type</th>
                <th scope="col">Catégorie</th>
                <th scope="col">Statut</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody id="eventsBody"></tbody>
    </table>
</div>

                        <!-- PAGINATION SIMPLE ET PROPRE -->
                        <nav aria-label="Pagination" class="mt-4">
                            <ul class="pagination justify-content-center" id="pagination"></ul>
                        </nav>
                    </div>
                </div>

                <!-- Calendar + Chart -->
                <div class="col-lg-6">
                    <div class="bg-white rounded-4 p-4 shadow-lg card-custom mb-4">
                        <h5 class="mb-4 fw-bold text-dark">Calendrier</h5>
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-white rounded-4 p-4 shadow-lg card-custom">
                        <h5 class="mb-4 fw-bold text-dark">Statistiques mensuelles</h5>
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL AVEC CARTE QUI MARCHE -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="eventForm" class="modal-content" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">
                    <i class="fa fa-calendar-plus me-2"></i>Nouvel événement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Titre <span class="text-danger">*</span></label>
                            <input id="title" class="form-control" required>
                            <div class="invalid-feedback">Le titre est obligatoire</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                            <input id="date" type="date" class="form-control" required>
                            <div class="invalid-feedback" id="dateError">La date doit être dans le futur</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                            <input id="type" class="form-control" required>
                            <div class="invalid-feedback">Le type est obligatoire</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catégorie <span class="text-danger">*</span></label>
                            <select id="id_categorie" class="form-select" required>
                                <option value="">-- Choisir une catégorie --</option>
                                <!-- options chargées dynamiquement -->
                            </select>
                            <div class="invalid-feedback">Veuillez choisir une catégorie</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Le reste de ton code (lieu, carte, description) reste identique -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lieu de l'événement</label>
                            <div class="input-group">
                                <input type="text" id="location" class="form-control" placeholder="Recherchez un lieu...">
                                <button class="btn btn-outline-success" type="button" id="btnMyLocation" title="Ma position">
                                    <i class="fa fa-location-arrow"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Cliquez sur la carte pour placer le marqueur</label>
                            <div id="mapPreview" style="height: 200px;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea id="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="latitude">
                <input type="hidden" id="longitude">
            </div>
            <div class="modal-footer">
                <button id="btnDeleteModal" type="button" class="btn btn-outline-danger" style="display:none;">
                    <i class="fa fa-trash me-2"></i>Supprimer
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Annuler
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save me-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div class="toast-position">
    <div id="appToast" class="toast align-items-center text-white border-0 bg-success" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastBody">Succès !</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<!-- À la fin de ton <body>, juste avant </body> -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./admin-lib/chart/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- LEAFLET D'ABORD (obligatoire) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<!-- TON JS EN DERNIER -->
<script src="./admin-js/evenements.js?v=4.0"></script>
<script>
// Validation du formulaire au moment de la soumission
document.getElementById('eventForm').addEventListener('submit', function(e) {
    const form = this;
    let hasError = false;

    // Réinitialise les bordures rouges
    form.querySelectorAll('.form-control, .form-select').forEach(field => {
        field.classList.remove('is-invalid');
    });

    // Champ Titre
    const title = document.getElementById('title');
    if (!title.value.trim()) {
        title.classList.add('is-invalid');
        hasError = true;
    }

    // Champ Type
    const type = document.getElementById('type');
    if (!type.value.trim()) {
        type.classList.add('is-invalid');
        hasError = true;
    }

    // Champ Catégorie
    const categorie = document.getElementById('id_categorie');
    if (!categorie.value) {
        categorie.classList.add('is-invalid');
        hasError = true;
    }

    // Champ Date → doit être dans le futur (pas aujourd’hui ni avant)
    const dateInput = document.getElementById('date');
    const selectedDate = dateInput.value;

    if (!selectedDate) {
        dateInput.classList.add('is-invalid');
        hasError = true;
    } else {
        const today = new Date();
        today.setHours(0, 0, 0, 0); // on ignore l’heure

        const chosenDate = new Date(selectedDate);
        if (chosenDate <= today) {
            dateInput.classList.add('is-invalid');
            document.getElementById('dateError').textContent = 
                "La date doit être strictement dans le futur (pas aujourd’hui ni avant)";
            hasError = true;
        }
    }

    // Si au moins une erreur → on bloque l’envoi
    if (hasError) {
        e.preventDefault();
        e.stopPropagation();
    }
});

// Validation en temps réel pour la date (optionnel mais très pratique)
document.getElementById('date').addEventListener('change', function() {
    this.classList.remove('is-invalid');

    if (this.value) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (new Date(this.value) <= today) {
            this.classList.add('is-invalid');
        }
    }
});
</script>
</body>
</html>

