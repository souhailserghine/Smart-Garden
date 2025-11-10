<!DOCTYPE html>
<html lang="fr" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="../../public/assets/images/logo-16x16.png" />
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Capteurs - Smart Garden</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@1.9.2/css/boxicons.min.css' rel='stylesheet'>

    <!-- Styles -->
    <link href="../../public/assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../../public/assets/css/style.css" rel="stylesheet">
    <link href="../../public/assets/css/components.css" rel="stylesheet">
    <link href="../../public/assets/css/profile.css" rel="stylesheet">
    <link href="../../public/assets/css/media.css" rel="stylesheet">
    <link href="../../public/assets/css/update.css" rel="stylesheet">
    <script src="../../public/assets/js/load.js" type="text/javascript"></script>
</head>
<body class="profile">
    <nav id="navbar-main" class="navbar navbar-expand-lg shadow-sm sticky-top">
        <div class="w-100 justify-content-md-center">
            <ul class="nav navbar-nav enable-mobile px-2">
                <li class="nav-item">
                    <button type="button" class="btn nav-link p-0"><img src="../../public/assets/images/icons/theme/post-image.png" class="f-nav-icon" alt="Quick make post"></button>
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
                    <a href="messages.html" class="nav-link nav-icon nav-links message-drop drop-w-tooltip" data-placement="bottom" data-title="Messages">
                        <img src="../../public/assets/images/icons/navbar/message.png" class="message-dropdown f-nav-icon" alt="navbar icon">
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav mr-5 flex-row" id="main_menu">
                <a class="navbar-brand nav-item mr-lg-5" href="index.html"><img src="../../public/assets/images/logo-64x64.png" width="40" height="40" class="mr-3" alt="Logo"></a>
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
                        <img src="../../public/assets/images/icons/navbar/create.png" alt="navbar icon">
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
                        <img src="../../public/assets/images/icons/navbar/message.png" class="message-dropdown" alt="navbar icon"> <span class="badge badge-pill badge-primary">1</span>
                    </a>
                    <ul class="dropdown-menu notify-drop dropdown-menu-right nav-drop">
                        <div class="notify-drop-title">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6 fs-8">Messages | <a href="#">Requests</a></div>
                                <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                                    <a href="#" class="notify-right-icon">Mark All as Read</a>
                                </div>
                            </div>
                        </div>
                        <div class="drop-content">
                            <li>
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <div class="notify-img"><img src="../../public/assets/images/users/user-6.png" alt="notification user image"></div>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <a href="#" class="notification-user">Susan P. Jarvis</a>
                                    <a href="#" class="notify-right-icon"><i class='bx bx-radio-circle-marked'></i></a>
                                    <p class="time"><i class='bx bx-check'></i> This party is going to have a DJ, food, and drinks.</p>
                                </div>
                            </li>
                        </div>
                        <div class="notify-drop-footer text-center"><a href="#">See More</a></div>
                    </ul>
                </li>
                <li class="nav-item s-nav dropdown notification">
                    <a href="#" class="nav-link nav-links rm-drop-mobile drop-w-tooltip" data-toggle="dropdown" data-placement="bottom" data-title="Notifications" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="../../public/assets/images/icons/navbar/notification.png" class="notification-bell" alt="navbar icon"> <span class="badge badge-pill badge-primary">3</span>
                    </a>
                </li>
                <li class="nav-item s-nav dropdown d-mobile">
                    <a href="#" class="nav-link nav-links nav-icon drop-w-tooltip" data-toggle="dropdown" data-placement="bottom" data-title="Pages" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="../../public/assets/images/icons/navbar/flag.png" alt="navbar icon">
                    </a>
                </li>
                <li class="nav-item s-nav"><a href="profile.html" class="nav-link nav-links"><div class="menu-user-image"><img src="../../public/assets/images/users/user-4.jpg" class="menu-user-img ml-1" alt="Menu Image"></div></a></li>
                <li class="nav-item s-nav nav-icon dropdown">
                    <a href="settings.html" data-toggle="dropdown" data-placement="bottom" data-title="Settings" class="nav-link settings-link rm-drop-mobile drop-w-tooltip" id="settings-dropdown"><img src="../../public/assets/images/icons/navbar/settings.png" class="nav-settings" alt="navbar icon"></a>
                    <div class="dropdown-menu dropdown-menu-right settings-dropdown shadow-sm" aria-labelledby="settings-dropdown">
                        <a class="dropdown-item" href="#"><img src="../../public/assets/images/icons/navbar/help.png" alt="Navbar icon"> Help Center</a>
                        <a class="dropdown-item d-flex align-items-center dark-mode" href="#"><img src="../../public/assets/images/icons/navbar/moon.png" alt="Navbar icon"> Dark Mode
                            <button type="button" class="btn btn-lg btn-toggle ml-auto" data-toggle="button" aria-pressed="false" autocomplete="off"><div class="handle"></div></button>
                        </a>
                        <a class="dropdown-item" href="#"><img src="../../public/assets/images/icons/navbar/gear-1.png" alt="Navbar icon"> Settings</a>
                        <a class="dropdown-item logout-btn" href="#"><img src="../../public/assets/images/icons/navbar/logout.png" alt="Navbar icon"> Log Out</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card p-3 w-shadow">
                    <h6>Menu</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.html">Dashboard</a></li>
                        <li><a href="taches.html">Tâches</a></li>
                        <li><a href="capteurs.html" class="sd-active">Capteurs</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 mb-3 w-shadow">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Capteurs</h5>
                        <div>
                            <button id="showData" class="btn btn-outline-primary btn-sm">Afficher données</button>
                            <button id="sendAnalyze" class="btn btn-primary btn-sm">Envoyer et analyser</button>
                        </div>
                    </div>

                    <div id="sensors" class="mb-3">
                        <div class="p-2 border-bottom d-flex justify-content-between">
                            <div><strong>Capteur #1</strong><br/><small class="text-muted">Humidité du sol</small></div>
                            <div><span class="badge badge-secondary">--</span></div>
                        </div>
                        <div class="p-2 border-bottom d-flex justify-content-between">
                            <div><strong>Capteur #2</strong><br/><small class="text-muted">Température</small></div>
                            <div><span class="badge badge-secondary">--</span></div>
                        </div>
                    </div>

                    <div class="card p-3 w-shadow">
                        <h6>Données récentes</h6>
                        <pre id="dataBox">Aucune donnée affichée.</pre>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 w-shadow">
                    <h6>Analyse</h6>
                    <p class="fs-8">Le bouton "Envoyer et analyser" redirigera ultérieurement vers une page dédiée d'analyse.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function(){
        const show = document.getElementById('showData');
        const send = document.getElementById('sendAnalyze');
        const dataBox = document.getElementById('dataBox');

        show.addEventListener('click', function(){
            const now = new Date().toLocaleString();
            const sample = {capteur_1:{humidite:'45%',time:now}, capteur_2:{temperature:'22.5°C',time:now}};
            dataBox.textContent = JSON.stringify(sample,null,2);
            document.querySelectorAll('#sensors .badge')[0].textContent = 'Dernière: 45%';
            document.querySelectorAll('#sensors .badge')[1].textContent = 'Dernière: 22.5°C';
        });

        send.addEventListener('click', function(){
            // en prod: redirection vers page d'analyse
            alert('Données envoyées. Redirection vers la page d\'analyse (à implémenter).');
        });
    })();
    </script>

    <!-- Core scripts (same as index) -->
    <script src="../../public/assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="../../public/assets/js/popper/popper.min.js"></script>
    <script src="../../public/assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="../../public/assets/js/app.js"></script>
    <script src="../../public/assets/js/components/components.js"></script>
</body>
</html>
