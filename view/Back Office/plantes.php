<?php 
include '../../Controller/planteC.php';
include '../../Controller/tacheC.php';
$pl = new planteC();
$tacheController = new tacheC();

// Gestion de la modification plante
$editPlante = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    foreach ($pl->listPlantes() as $p) {
        if ($p['id_plante'] == $editId) {
            $editPlante = $p;
            break;
        }
    }
}
$editTache = null;
if (isset($_GET['editTache'])) {
    $editTacheId = $_GET['editTache'];
    foreach ($tacheController->listTaches() as $t) {
        if ($t['id_tache'] == $editTacheId) {
            $editTache = $t;
            break;
        }
    }
}
$listePlantes = $pl->listPlantes();
$listeTaches = $tacheController->listTaches();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>SmartGarden</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid position-relative bg-white d-flex p-0">
    <!-- Spinner Start -->
    <div id="spinner" class="bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center" style="display: none;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Sidebar Start -->
    <div class="sidebar pe-4 pb-3">
        <nav class="navbar bg-light navbar-light">
            <a href="index.html" class="navbar-brand mx-4 mb-2 mt-2">
                <h3 style="font-size: 1.2rem;padding-left: 20px; color: #2ecc71;">
                    <img src="img/logo-64x64.png" alt="Logo" class="me-2" style="width: 32px; height: 32px;">SmartGarden
                </h3>
            </a>
            <div class="d-flex align-items-center ms-4 mb-4">
                <div class="position-relative">
                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                    <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                </div>
                <div class="ms-3">
                    <h6 class="mb-0">Jhon Doe</h6>
                    <span>Admin</span>
                </div>
            </div>
            <div class="navbar-nav w-100">
                <a href="index.html" class="nav-item nav-link"><i class="fa fa-file-alt me-2"></i>Publication</a>
                <a href="plantes.php" class="nav-item nav-link active"><i class="fa fa-leaf me-2"></i>Plantes</a>
                <a href="evenements.html" class="nav-item nav-link"><i class="fa fa-calendar me-2"></i>Evenements</a>
                <a href="utilisateur.html" class="nav-item nav-link"><i class="fa fa-user me-2"></i>Utilisateur</a>
                <a href="capteurs.html" class="nav-item nav-link"><i class="fa fa-microchip me-2"></i>Capteurs</a>
            </div>
        </nav>
    </div>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
            <a href="#" class="sidebar-toggler flex-shrink-0">
                <i class="fa fa-bars"></i>
            </a>
        </nav>
        <!-- Navbar End -->

        <br>
        <!-- Plantes Management Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="bg-light text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Gestion des Plantes</h6>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanteModal">
                        <i class="fa fa-plus me-2"></i>Ajouter Plante
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead class="table-success">
                            <tr class="text-dark">
                                <th>Nom</th>
                                <th>ID</th>
                                <th>ID Utilisateur</th>
                                <th>Date</th>
                                <th>Niveau Humidité</th>
                                <th>Besoin Eau</th>
                                <th>État Santé</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($listePlantes as $plante) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($plante['nom_plante']); ?></td>
                                <td><?php echo $plante['id_plante']; ?></td>
                                <td><?php echo $plante['idUtilisateur']; ?></td>
                                <td><?php echo $plante['date_ajout']; ?></td>
                                <td><?php echo $plante['niveau_humidite']; ?></td>
                                <td><?php echo $plante['besoin_eau']; ?></td>
                                <td><?php echo htmlspecialchars($plante['etat_sante']); ?></td>
                                <td>
                                    <a href="plantes.php?edit=<?php echo $plante['id_plante']; ?>" class="btn btn-warning btn-sm" title="Modifier">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="supprimerPlante.php?id=<?php echo $plante['id_plante']; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Voulez-vous vraiment supprimer cette plante ?');">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Plante Modal -->
        <div class="modal fade" id="addPlanteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-light">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-plus"></i> Ajouter une Plante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addPlanteForm" method="POST" action="ajout.php">
                        <div class="modal-body">
                            <div id="addError" style="display:none;" class="alert alert-danger"></div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom plante</label>
                                    <input type="text" class="form-control" name="nom_plante" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date d'ajout</label>
                                    <input type="date" class="form-control" name="date_ajout" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Niveau humidité (%)</label>
                                    <input type="number" class="form-control" name="niveau_humidite" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Besoin en eau (ml)</label>
                                    <input type="number" class="form-control" name="besoin_eau" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">État de santé</label>
                                    <select class="form-select" name="etat_sante">
                                        <option value="Bon état">Bon état</option>
                                        <option value="Moyen">Moyen</option>
                                        <option value="Mauvais état">Mauvais état</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ID Utilisateur</label>
                                    <input type="number" class="form-control" name="idUtilisateur" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Plante Modal -->
        <?php if ($editPlante): ?>
        <div class="modal fade show" id="editPlanteModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-light">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-edit"></i> Modifier la Plante</h5>
                        <a href="plantes.php" class="btn-close"></a>
                    </div>
                    <form method="POST" action="modifierPlante.php">
                        <input type="hidden" name="id_plante" value="<?php echo $editPlante['id_plante']; ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom plante</label>
                                    <input type="text" class="form-control" name="nom_plante" value="<?php echo htmlspecialchars($editPlante['nom_plante']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date d'ajout</label>
                                    <input type="date" class="form-control" name="date_ajout" value="<?php echo $editPlante['date_ajout']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Niveau humidité (%)</label>
                                    <input type="number" class="form-control" name="niveau_humidite" value="<?php echo $editPlante['niveau_humidite']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Besoin en eau (ml)</label>
                                    <input type="number" class="form-control" name="besoin_eau" value="<?php echo $editPlante['besoin_eau']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">État de santé</label>
                                    <select class="form-select" name="etat_sante">
                                        <option value="Bon état" <?php if($editPlante['etat_sante'] == 'Bon état') echo 'selected'; ?>>Bon état</option>
                                        <option value="Moyen" <?php if($editPlante['etat_sante'] == 'Moyen') echo 'selected'; ?>>Moyen</option>
                                        <option value="Mauvais état" <?php if($editPlante['etat_sante'] == 'Mauvais état') echo 'selected'; ?>>Mauvais état</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ID Utilisateur</label>
                                    <input type="number" class="form-control" name="idUtilisateur" value="<?php echo $editPlante['idUtilisateur']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="plantes.php" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <!-- Tâches Management Start -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Gestion des Tâches</h6>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTacheModal">
                <i class="fa fa-plus me-2"></i>Ajouter Tâche
            </button>
        </div>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead class="table-info">
                    <tr class="text-dark">
                        <th>ID Tâche</th>
                        <th>Nom Tâche</th>
                        <th>ID Plante</th>
                        <th>Date</th>
                        <th>Dernière Exécution</th>
                        <th>Prochaine Exécution</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listeTaches as $tache) { ?>
                    <tr>
                        <td><?php echo $tache['id_tache']; ?></td>
                        <td><?php echo htmlspecialchars($tache['type_tache']); ?></td>
                        <td><?php echo $tache['id_plante']; ?></td>
                        <td><?php echo $tache['date_tache']; ?></td>
                        <td><?php echo $tache['derniereExecution']; ?></td>
                        <td><?php echo $tache['prochaineExecution']; ?></td>
                        <td><?php echo $tache['estComplete'] ? 'Complète' : 'En cours'; ?></td>
                        <td>
                            <a href="plantes.php?editTache=<?php echo $tache['id_tache']; ?>" class="btn btn-warning btn-sm" title="Modifier">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a href="supprimerTache.php?id=<?php echo $tache['id_tache']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Voulez-vous vraiment supprimer cette tâche ?');">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Tache Modal -->
<div class="modal fade" id="addTacheModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus"></i> Ajouter une Tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
<form id="addTacheForm" method="POST" action="ajoutT.php">



                <div class="modal-body">
                    <div id="addTacheError" style="display:none;" class="alert alert-danger"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom Tâche</label>
                            <input type="text" class="form-control" name="type_tache" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Plante</label>
                            <input type="number" class="form-control" name="id_plante" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Tâche</label>
                            <input type="date" class="form-control" name="date_tache" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dernière Exécution</label>
                            <input type="date" class="form-control" name="derniereExecution">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prochaine Exécution</label>
                            <input type="date" class="form-control" name="prochaineExecution">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">État</label>
                            <select class="form-select" name="estComplete">
                                <option value="0">En cours</option>
                                <option value="1">Complète</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    (function() {
        const addTacheForm = document.getElementById('addTacheForm');
        const addTacheError = document.getElementById('addTacheError');

        addTacheForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(addTacheForm);

            fetch('ajoutTache.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert('Tâche ajoutée avec succès !');
                        window.location.href = 'plantes.php';
                    } else {
                        addTacheError.textContent = 'Erreur: ' + (data.message || 'Impossible d\'ajouter la tâche');
                        addTacheError.style.display = 'block';
                    }
                } catch (e) {
                    addTacheError.textContent = 'Erreur serveur: ' + text;
                    addTacheError.style.display = 'block';
                }
            })
            .catch(error => {
                addTacheError.textContent = 'Erreur réseau: ' + error.message;
                addTacheError.style.display = 'block';
            });
        });
    })();
</script>


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function() {
        const addForm = document.getElementById('addPlanteForm');
        const errorDiv = document.getElementById('addError');

        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(addForm);

            console.log('Envoi du formulaire à ajout.php...');

            fetch('ajout.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Réponse reçue, statut:', response.status);
                console.log('Content-Type:', response.headers.get('content-type'));
                return response.text(); // D'abord en texte pour debug
            })
            .then(text => {
                console.log('Texte brut reçu:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert('Plante ajoutée avec succès !');
                        window.location.href = 'plantes.php';
                    } else {
                        errorDiv.textContent = 'Erreur: ' + (data.message || 'Impossible d\'ajouter la plante');
                        errorDiv.style.display = 'block';
                    }
                } catch (e) {
                    console.error('Erreur parsing JSON:', e);
                    errorDiv.textContent = 'Erreur serveur: ' + text;
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Erreur fetch:', error);
                errorDiv.textContent = 'Erreur réseau lors de l\'ajout: ' + error.message;
                errorDiv.style.display = 'block';
            });
        });
    })();
</script>

</body>
</html>
