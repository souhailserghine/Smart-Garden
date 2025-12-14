<?php
require_once 'check_session.php';
include '../../controller/tacheC.php';
include '../../model/tache.php';

$tacheController = new tacheC();

// Récupération des champs et conversion types
$type_dosage = $_POST['type_dosage'];
$quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
$mode_dosage = $_POST['mode_dosage'];
$date_dosage = $_POST['date_dosage'];
$derniereExecution = $_POST['derniereExecution'];
$prochaineExecution = $_POST['prochaineExecution'];
$estComplete = isset($_POST['estComplete']) ? (int)$_POST['estComplete'] : 0;
$priorite = isset($_POST['priorite']) ? (int)$_POST['priorite'] : 1;
$id_plante = isset($_POST['id_plante']) ? (int)$_POST['id_plante'] : 0;

// Création de l’objet tâche (10 arguments, ID tache = null)
$tache = new tache(
    null,               // id_tache
    $type_dosage,       // string
    $quantite,          // float
    $mode_dosage,       // string
    $date_dosage,       // date
    $derniereExecution, // date
    $prochaineExecution,// date
    $estComplete,       // int
    $priorite,          // int
    $id_plante          // int
);


// Ajout en base
try {
    $tacheController->ajouterTache($tache);
    $_SESSION['successMsg'] = "La tâche a été ajoutée avec succès";
} catch (Exception $e) {
    $_SESSION['errorMsg'] = "Erreur : " . $e->getMessage();
}

// Redirection
header("Location: plantes.php");
exit();
