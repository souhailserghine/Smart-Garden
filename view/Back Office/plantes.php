<?php
session_start(); // Toujours en tout premier

// Inclure les controllers
require_once '../../Controller/planteC.php';
require_once '../../Controller/tacheC.php';

// Gestion des flash messages
$successMsg = '';
$errorMsg = '';

if (isset($_SESSION['successMsg'])) {
    $successMsg = $_SESSION['successMsg'];
    unset($_SESSION['successMsg']);
}

if (isset($_SESSION['errorMsg'])) {
    $errorMsg = $_SESSION['errorMsg'];
    unset($_SESSION['errorMsg']);
}

// Création des objets controllers
$pl = new planteC();
$tacheController = new tacheC();

// Gestion modification plante
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

// Gestion modification tâche
$editTache = null;
if (isset($_GET['editTache'])) {
    $editTacheId = $_GET['editTache'];
    foreach ($tacheController->listTaches() as $t) {
        if ($t['id_dosage'] == $editTacheId) {
            $editTache = $t;
            break;
        }
    }
}

// Listes
$listePlantes = $pl->listPlantes();
$listeTaches = $tacheController->listTaches();

// Suppression
if (isset($_GET['deleteTache'])) {
    $id = intval($_GET['deleteTache']);
    $tacheController->supprimerTache($id);
    header('Location: plantes.php');
    exit();
}

if (isset($_GET['deletePlante'])) {
    $id = intval($_GET['deletePlante']);
    $pl->supprimerPlante($id);
    header('Location: plantes.php');
    exit();
}
$taches = $tacheController->listTaches(); // ou la méthode qui liste toutes les tâches

// Vérifier si on a choisi une plante
if (isset($_GET['plante_id']) && !empty($_GET['plante_id'])) {
    $plante_id = $_GET['plante_id'];
    $taches = $tacheController->getTachesByPlante($plante_id); // nouvelle méthode à créer
}

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
        <?php


// Gestion du message d'erreur venant de ajout.php
$errorMsg = '';
if (isset($_GET['error'])) {
    $errorMsg = htmlspecialchars($_GET['error']);
}
$successMsg = '';
if (isset($_GET['success'])) {
    $successMsg = "Plante ajoutée avec succès !";
}
?>

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
                        <td><?= htmlspecialchars($plante['nom_plante']); ?></td>
                        <td><?= $plante['id_plante']; ?></td>
                        <td><?= $plante['idUtilisateur']; ?></td>
                        <td><?= $plante['date_ajout']; ?></td>
                        <td><?= $plante['niveau_humidite']; ?></td>
                        <td><?= $plante['besoin_eau']; ?></td>
                        <td><?= htmlspecialchars($plante['etat_sante']); ?></td>
                        <td>
                            <a href="plantes.php?edit=<?= $plante['id_plante']; ?>" class="btn btn-warning btn-sm" title="Modifier">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a href="supprimerPlante.php?id=<?= $plante['id_plante']; ?>" class="btn btn-danger btn-sm"
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
                    <div id="addError" class="alert alert-danger" style="<?= $errorMsg ? 'display:block;' : 'display:none;' ?>">
                        <?= htmlspecialchars($errorMsg) ?>
                    </div>
                    <?php if ($successMsg): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom plante</label>
                            <input type="text" class="form-control" name="nom_plante" value="<?= htmlspecialchars($formData['nom_plante'] ?? '') ?>">
                            <small class="error-msg"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date d'ajout</label>
                            <input type="date" class="form-control" name="date_ajout" value="<?= htmlspecialchars($formData['date_ajout'] ?? '') ?>">
                            <small class="error-msg"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Niveau humidité (%)</label>
                            <input type="number" class="form-control" name="niveau_humidite" value="<?= htmlspecialchars($formData['niveau_humidite'] ?? '') ?>">
                            <small class="error-msg"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Besoin en eau (ml)</label>
                            <input type="number" class="form-control" name="besoin_eau" value="<?= htmlspecialchars($formData['besoin_eau'] ?? '') ?>">
                            <small class="error-msg"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">État de santé</label>
                            <select class="form-select" name="etat_sante">
                                <?php
                                $etatOptions = ['Bon état', 'Moyen', 'Mauvais état'];
                                $selectedEtat = $formData['etat_sante'] ?? '';
                                foreach ($etatOptions as $option) {
                                    $selectedAttr = ($option === $selectedEtat) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($option) . "\" $selectedAttr>$option</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Utilisateur</label>
                            <input type="number" class="form-control" name="idUtilisateur" value="<?= htmlspecialchars($formData['idUtilisateur'] ?? '') ?>">
                            <small class="error-msg"></small>
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
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("addPlanteForm");
    const errorDiv = document.getElementById("addError");
    const fields = form.querySelectorAll("input[name], select[name]");
    let isSubmitting = false;

    function checkField(input) {
        const msg = input.parentElement.querySelector(".error-msg");
        let error = "";

        if (input.value.trim() === "") {
            error = "Ce champ est obligatoire.";
        } else {
            switch (input.name) {
                case "nom_plante": 
                    if (input.value.trim().length < 3) 
                        error = "Le nom doit contenir au moins 3 caractères."; 
                    break;
                case "date_ajout": 
                    if (!input.value) 
                        error = "Veuillez choisir une date."; 
                    break;
                case "niveau_humidite": 
                    if (Number(input.value) < 0 || Number(input.value) > 100) 
                        error = "L'humidité doit être entre 0 et 100."; 
                    break;
                case "besoin_eau": 
                    if (Number(input.value) <= 0) 
                        error = "Le besoin en eau doit être supérieur à 0."; 
                    break;
                case "idUtilisateur": 
                    if (Number(input.value) <= 0) 
                        error = "L'ID utilisateur doit être positif."; 
                    break;
                case "etat_sante":
                    if (input.value.trim() === "") 
                        error = "État de santé requis.";
                    break;
            }
        }

        if (error) {
            msg.textContent = error;
            msg.classList.add("text-danger");
            msg.classList.remove("text-success");
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            return false;
        } else {
            msg.textContent = "Valide ✔";
            msg.classList.add("text-success");
            msg.classList.remove("text-danger");
            input.classList.add("is-valid");
            input.classList.remove("is-invalid");
            return true;
        }
    }

    fields.forEach(input => {
        input.addEventListener("input", () => checkField(input));
        input.addEventListener("change", () => checkField(input));
    });

    form.addEventListener("submit", (e) => {
        if (isSubmitting) return;

        let valid = true;
        fields.forEach(input => {
            if (!checkField(input)) valid = false;
        });

        if (!valid) {
            e.preventDefault();
            errorDiv.textContent = "Veuillez corriger tous les champs en rouge.";
            errorDiv.style.display = "block";
            return;
        } else {
            errorDiv.style.display = "none";
            isSubmitting = true;
        }
    });
<?php if ($errorMsg): ?>
    var addPlanteModal = new bootstrap.Modal(document.getElementById('addPlanteModal'));
    addPlanteModal.show();
<?php endif; ?>

    // Ouvrir modal si erreur serveur ou en édition
    <?php if($errorMsg || $editPlante): ?>
        var addPlanteModal = new bootstrap.Modal(document.getElementById('addPlanteModal'));
        addPlanteModal.show();
    <?php endif; ?>
});
</script>



        <!-- Edit Plante Modal -->
<?php if ($editPlante): ?>
<div class="modal fade show" id="editPlanteModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-edit"></i> Modifier la Plante</h5>
                <a href="plantes.php" class="btn-close"></a>
            </div>
            <form id="editPlanteForm" method="POST" action="modifierPlante.php">
                <input type="hidden" name="id_plante" value="<?php echo $editPlante['id_plante']; ?>">
                <div class="modal-body">
                    <div id="editError" class="text-danger mb-3"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom plante</label>
                            <input type="text" class="form-control" name="nom_plante" value="<?php echo htmlspecialchars($editPlante['nom_plante']); ?>" >
                            <div class="error-msg"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date d'ajout</label>
                            <input type="date" class="form-control" name="date_ajout" value="<?php echo $editPlante['date_ajout']; ?>" >
                            <div class="error-msg"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Niveau humidité (%)</label>
                            <input type="number" class="form-control" name="niveau_humidite" value="<?php echo $editPlante['niveau_humidite']; ?>" >
                            <div class="error-msg"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Besoin en eau (ml)</label>
                            <input type="number" class="form-control" name="besoin_eau" value="<?php echo $editPlante['besoin_eau']; ?>" >
                            <div class="error-msg"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">État de santé</label>
                            <select class="form-select" name="etat_sante">
                                <option value="Bon état" <?php if($editPlante['etat_sante'] == 'Bon état') echo 'selected'; ?>>Bon état</option>
                                <option value="Moyen" <?php if($editPlante['etat_sante'] == 'Moyen') echo 'selected'; ?>>Moyen</option>
                                <option value="Mauvais état" <?php if($editPlante['etat_sante'] == 'Mauvais état') echo 'selected'; ?>>Mauvais état</option>
                            </select>
                            <div class="error-msg"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Utilisateur</label>
                            <input type="number" class="form-control" name="idUtilisateur" value="<?php echo $editPlante['idUtilisateur']; ?>" >
                            <div class="error-msg"></div>
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

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("editPlanteForm");
    const errorDiv = document.getElementById("editError");
    const fields = form.querySelectorAll("input[name], select[name]");
    let isSubmitting = false;

    function checkField(input) {
        const msg = input.parentElement.querySelector(".error-msg");
        let error = "";

        if (input.value.trim() === "") {
            error = "Ce champ est obligatoire.";
        } else {
            switch (input.name) {
                case "nom_plante": if (input.value.trim().length < 3) error = "Le nom doit contenir au moins 3 caractères."; break;
                case "date_ajout": if (!input.value) error = "Veuillez choisir une date."; break;
                case "niveau_humidite": 
                    if (Number(input.value) < 0 || Number(input.value) > 100) 
                        error = "L'humidité doit être entre 0 et 100."; 
                    break;
                case "besoin_eau": if (Number(input.value) <= 0) error = "Le besoin en eau doit être > 0."; break;
                case "idUtilisateur": if (Number(input.value) <= 0) error = "L'ID utilisateur doit être positif."; break;
            }
        }

        if (error) {
            msg.textContent = error;
            msg.classList.add("text-danger");
            msg.classList.remove("text-success");
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            return false;
        } else {
            msg.textContent = "Valide ✔";
            msg.classList.add("text-success");
            msg.classList.remove("text-danger");
            input.classList.add("is-valid");
            input.classList.remove("is-invalid");
            return true;
        }
    }

    fields.forEach(input => {
        input.addEventListener("input", () => checkField(input));
        input.addEventListener("change", () => checkField(input));
    });

    form.addEventListener("submit", (e) => {
        if (isSubmitting) return;

        let valid = true;
        fields.forEach(input => {
            if (!checkField(input)) valid = false;
        });

        if (!valid) {
            e.preventDefault();
            errorDiv.textContent = "Veuillez corriger les champs en rouge avant de soumettre.";
            errorDiv.style.display = "block";
            return;
        } else {
            errorDiv.style.display = "none";
            isSubmitting = true;
        }
    });

    // Ouvrir modal automatiquement si $editPlante existe
    var editPlanteModal = new bootstrap.Modal(document.getElementById('editPlanteModal'));
    editPlanteModal.show();
});
</script>
<?php endif; ?>
<br>    </br>
        <!-- Tâches Management Start -->
         <form method="GET" action="" class="d-flex justify-content-center align-items-center gap-2 mb-3">
    <select name="plante_id" id="plante" class="form-select form-select-sm" style="max-width: 200px;">
        <option value="">-- Toutes les plantes --</option>
        <?php foreach ($listePlantes as $p):
            $selected = (isset($_GET['plante_id']) && $_GET['plante_id'] == $p['id_plante']) ? 'selected' : '';
        ?>
            <option value="<?= $p['id_plante'] ?>" <?= $selected ?>>
                <?= $p['nom_plante'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-gradient btn-sm rounded-pill px-3 py-1">
        <i class="fa fa-filter me-1"></i> Filtrer
    </button>
</form>


<head>
    <!-- tes liens Bootstrap / FontAwesome ici -->
    <style>
        .btn-gradient {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border: none;
            color: white;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>


<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Gestion des Dosages</h6>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTacheModal">
                <i class="fa fa-plus me-2"></i>Ajouter Dosage
            </button>
        </div>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead class="table-info">
                    <tr class="text-dark">
                        <th>ID Dosage</th>
                        <th>Type de Dosage</th>
                        <th>Quantité</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Dernière Exécution</th>
                        <th>Prochaine Exécution</th>
                        <th>État</th>
                        <th>Priorité</th>
                        <th>ID Plante</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($taches as $dosage) { ?>
<tr>
    <td><?php echo $dosage['id_dosage']; ?></td>
    <td><?php echo htmlspecialchars($dosage['type_dosage']); ?></td>
    <td><?php echo $dosage['quantite']; ?></td>
    <td><?php echo htmlspecialchars($dosage['mode_dosage']); ?></td>
    <td><?php echo $dosage['date_dosage']; ?></td>
    <td><?php echo $dosage['derniereExecution']; ?></td>
    <td><?php echo $dosage['prochaineExecution']; ?></td>
    <td>
        <?php 
            if($dosage['estComplete'] == 0) echo 'Non commencé';
            elseif($dosage['estComplete'] == 1) echo 'En cours';
            else echo 'Complète';
        ?>
    </td>
    <td>
        <?php 
            if($dosage['priorite'] == 1) echo 'Basse';
            elseif($dosage['priorite'] == 2) echo 'Moyenne';
            else echo 'Haute';
        ?>
    </td>
    <td><?php echo $dosage['id_plante']; ?></td>
    <td>
        <a href="plantes.php?editTache=<?php echo $dosage['id_dosage']; ?>" class="btn btn-warning btn-sm" title="Modifier">
            <i class="fa fa-edit"></i>
        </a>
        <a href="supprimerTache.php?id=<?php echo $dosage['id_dosage']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce dosage ?');">
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



<!-- Add Dosage Modal -->
<div class="modal fade" id="addTacheModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus"></i> Ajouter un Dosage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addTacheForm" method="POST" action="ajoutT.php">

                <div class="modal-body">
                    <div id="addTacheError" style="display:none;" class="alert alert-danger"></div>

                    <div class="row">

                        <!-- Type de dosage -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de dosage</label>
                            <select class="form-select" name="type_dosage">
                                <option value="Arrosage">Arrosage</option>
                                <option value="Fertilisation">Fertilisation</option>
                                <option value="Taille">Taille</option>
                            </select>
                        </div>

                        <!-- Quantité -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantité</label>
                            <input type="number" class="form-control" name="quantite">
                        </div>

                        <!-- Mode dosage -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mode dosage</label>
                            <select class="form-select" name="mode_dosage">
                                <option value="Automatique">Automatique</option>
                                <option value="Manuel">Manuel</option>
                                <option value="Semi-automatique">Semi-automatique</option>
                            </select>
                        </div>

                        <!-- Date dosage -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date dosage</label>
                            <input type="date" class="form-control" name="date_dosage">
                        </div>

                        <!-- Dernière exécution -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dernière exécution</label>
                            <input type="date" class="form-control" name="derniereExecution">
                        </div>

                        <!-- Prochaine exécution -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prochaine exécution</label>
                            <input type="date" class="form-control" name="prochaineExecution">
                        </div>

                        <!-- État -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">État</label>
                            <select class="form-select" name="estComplete">
                                <option value="0">Non commencé</option>
                                <option value="1">En cours</option>
                                <option value="2">Complète</option>
                            </select>
                        </div>

                        <!-- Priorité -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priorité</label>
                            <select class="form-select" name="priorite">
                                <option value="1">Basse</option>
                                <option value="2">Moyenne</option>
                                <option value="3">Haute</option>
                            </select>
                        </div>

                        <!-- ID Plante -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Plante associée</label>
                            <select name="id_plante" class="form-select">
                                <?php foreach($listePlantes as $plante): ?>
                                    <option value="<?= $plante['id_plante'] ?>">
                                        <?= htmlspecialchars($plante['id_plante']) ?> - <?= htmlspecialchars($plante['nom_plante']) ?>
                                    </option>  
                                <?php endforeach; ?>
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
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("addTacheForm");
    const errorDiv = document.getElementById("addTacheError");
    const fields = form.querySelectorAll("input[name], select[name]");
    let isSubmitting = false;

    // --- Fonction de validation d’un champ ---
    function checkField(input) {
        let error = "";
        const value = input.value.trim();

        // Vérification obligatoire
        if (!value) {
            error = "Ce champ est obligatoire.";
        } else {
            switch(input.name) {
                case "quantite":
                    if (Number(value) <= 0) error = "La quantité doit être > 0.";
                    break;
                case "estComplete":
                case "priorite":
                case "id_plante":
                    if (!Number.isInteger(Number(value)) || Number(value) < 0)
                        error = "Valeur invalide.";
                    break;
                case "prochaineExecution":
                    const derniere = form.querySelector("input[name='derniereExecution']").value;
                    if (derniere && value <= derniere) {
                        error = "La date prochaine exécution doit être après la dernière exécution.";
                    }
                    break;
            }
        }

        // Supprimer ancien message
        const msg = input.parentElement.querySelector(".error-msg");
        if (msg) msg.remove();

        if (error) {
            const span = document.createElement("div");
            span.className = "error-msg text-danger mt-1";
            span.textContent = error;
            input.parentElement.appendChild(span);
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            return false;
        } else {
            input.classList.remove("is-invalid");
            input.classList.add("is-valid");
            return true;
        }
    }

    // --- Cacher la bande rouge ---
    function hideErrorDiv() {
        errorDiv.style.display = "none";
        errorDiv.textContent = "";
    }

    // --- Validation en temps réel ---
    fields.forEach(input => {
        input.addEventListener("input", () => { checkField(input); hideErrorDiv(); });
        input.addEventListener("change", () => { checkField(input); hideErrorDiv(); });
    });

    // --- Submit form ---
    form.addEventListener("submit", (e) => {
        if (isSubmitting) return;

        hideErrorDiv();
        let valid = true;
        fields.forEach(input => {
            if (!checkField(input)) valid = false;
        });

        if (!valid) {
            e.preventDefault();
            errorDiv.textContent = "Veuillez corriger les erreurs ci-dessus.";
            errorDiv.style.display = "block";
            return;
        }

        isSubmitting = true;
    });

    // --- Ouvrir modal si erreur serveur ---
    <?php if (!empty($errorMsg)): ?>
        const addTacheModal = new bootstrap.Modal(document.getElementById('addTacheModal'));
        addTacheModal.show();
        errorDiv.textContent = "<?= $errorMsg ?>";
        errorDiv.style.display = "block";
    <?php endif; ?>

    // --- Affichage message succès (toast) ---
    <?php if (!empty($successMsg)): ?>
        // Créer un toast simple Bootstrap
        const toastDiv = document.createElement('div');
        toastDiv.className = 'toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3';
        toastDiv.role = 'alert';
        toastDiv.ariaLive = 'assertive';
        toastDiv.ariaAtomic = 'true';
        toastDiv.innerHTML = `
            <div class="d-flex">
                <div class="toast-body"><?= $successMsg ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>`;
        document.body.appendChild(toastDiv);
        const toast = new bootstrap.Toast(toastDiv, { delay: 3000 });
        toast.show();

        // Optionnel: rafraîchir la page pour voir la nouvelle tâche
        setTimeout(() => { window.location.reload(); }, 1000);
    <?php endif; ?>

});
</script>





<!-- Edit Dosage Modal -->

<?php if ($editTache): ?>
<div class="modal fade show" id="editTacheModal" tabindex="-1" 
     style="display:block; background:rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-light">

            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-edit"></i> Modifier le Dosage</h5>
                <a href="taches.php" class="btn-close"></a>
            </div>

            <form id="editTacheForm" method="POST" action="modifierTache.php">
                <input type="hidden" name="id_dosage" value="<?= $editTache['id_dosage'] ?>">

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type dosage</label>
                            <select class="form-select" name="type_dosage">
                                <option value="Arrosage" <?= $editTache['type_dosage']=='Arrosage'?'selected':'' ?>>Arrosage</option>
                                <option value="Fertilisation" <?= $editTache['type_dosage']=='Fertilisation'?'selected':'' ?>>Fertilisation</option>
                                <option value="Taille" <?= $editTache['type_dosage']=='Taille'?'selected':'' ?>>Taille</option>
                            </select>
                            <div class="error-msg"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Quantité</label>
                            <input type="number" class="form-control" name="quantite" value="<?= $editTache['quantite'] ?>">
                            <div class="error-msg"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Mode dosage</label>
                            <select class="form-select" name="mode_dosage">
                                <option value="Automatique" <?= $editTache['mode_dosage']=='Automatique'?'selected':'' ?>>Automatique</option>
                                <option value="Manuel" <?= $editTache['mode_dosage']=='Manuel'?'selected':'' ?>>Manuel</option>
                                <option value="Semi-automatique" <?= $editTache['mode_dosage']=='Semi-automatique'?'selected':'' ?>>Semi-auto</option>
                            </select>
                            <div class="error-msg"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Date dosage</label>
                            <input type="date" class="form-control" name="date_dosage"
                                   value="<?= $editTache['date_dosage'] ?>">
                            <div class="error-msg"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Dernière exécution</label>
                            <input type="date" class="form-control" name="derniereExecution"
                                   value="<?= $editTache['derniereExecution'] ?>">
                            <div class="error-msg"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Prochaine exécution</label>
                            <input type="date" class="form-control" name="prochaineExecution"
                                   value="<?= $editTache['prochaineExecution'] ?>">
                            <div class="error-msg"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>État</label>
                            <select class="form-select" name="estComplete">
                                <option value="0" <?= $editTache['estComplete']==0?'selected':'' ?>>Non commencé</option>
                                <option value="1" <?= $editTache['estComplete']==1?'selected':'' ?>>En cours</option>
                                <option value="2" <?= $editTache['estComplete']==2?'selected':'' ?>>Complète</option>
                            </select>
                            <div class="error-msg"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Priorité</label>
                            <select class="form-select" name="priorite">
                                <option value="1" <?= $editTache['priorite']==1?'selected':'' ?>>Basse</option>
                                <option value="2" <?= $editTache['priorite']==2?'selected':'' ?>>Moyenne</option>
                                <option value="3" <?= $editTache['priorite']==3?'selected':'' ?>>Haute</option>
                            </select>
                            <div class="error-msg"></div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Plante associée</label>
                            <select class="form-select" name="id_plante">
                                <?php foreach($listePlantes as $pl): ?>
                                    <option value="<?= $pl['id_plante'] ?>"
                                        <?= $pl['id_plante']==$editTache['id_plante']?'selected':'' ?>>
                                        <?= $pl['id_plante'] ?> - <?= htmlspecialchars($pl['nom_plante']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-msg"></div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <a href="taches.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>

                <div id="editTacheError" class="text-danger fw-bold mt-2" style="display:none;"></div>

            </form>

        </div>
    </div>
</div>
<?php endif; ?>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("editTacheForm");
    const errorDiv = document.getElementById("editTacheError");
    const fields = form.querySelectorAll("input[name], select[name]");
    let isSubmitting = false;

    function checkField(input) {
        const msg = input.parentElement.querySelector(".error-msg");
        let error = "";
        const value = input.value.trim();

        if (!value) {
            error = "Ce champ est obligatoire.";
        } else {
            switch (input.name) {

                case "quantite":
                    if (Number(value) <= 0)
                        error = "La quantité doit être > 0.";
                    break;

                case "priorite":
                    if (![1,2,3].includes(Number(value)))
                        error = "Priorité invalide.";
                    break;

                case "estComplete":
                    if (![0,1,2].includes(Number(value)))
                        error = "État invalide.";
                    break;

                case "id_plante":
                    if (Number(value) <= 0)
                        error = "Plante invalide.";
                    break;

                case "prochaineExecution":
                case "derniereExecution":
                    const d1 = form.derniereExecution.value;
                    const d2 = form.prochaineExecution.value;

                    if (d1 && d2 && d2 <= d1)
                        error = "La prochaine exécution doit être après la dernière.";
                    break;
            }
        }

        if (error) {
            msg.textContent = error;
            msg.classList.add("text-danger");
            msg.classList.remove("text-success");
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            return false;
        } else {
            msg.textContent = "Valide ✔";
            msg.classList.add("text-success");
            msg.classList.remove("text-danger");
            input.classList.add("is-valid");
            input.classList.remove("is-invalid");
            return true;
        }
    }

    fields.forEach(input => {
        input.addEventListener("input", () => checkField(input));
        input.addEventListener("change", () => checkField(input));
    });

    form.addEventListener("submit", e => {
        if (isSubmitting) return;

        let valid = true;
        fields.forEach(input => {
            if (!checkField(input)) valid = false;
        });

        if (!valid) {
            e.preventDefault();
            errorDiv.textContent = "Veuillez corriger les erreurs avant de soumettre.";
            errorDiv.style.display = "block";
        } else {
            errorDiv.style.display = "none";
            isSubmitting = true;
        }
    });

    new bootstrap.Modal(document.getElementById("editTacheModal")).show();
});
</script>





        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
