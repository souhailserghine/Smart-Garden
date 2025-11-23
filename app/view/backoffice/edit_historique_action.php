<?php
require_once '../../controller/historiqueController.php';

$id = intval($_POST['id']);
$dateConnexion = $_POST['dateConnexion'];
$dateDeconnexion = $_POST['dateDeconnexion'];
$tache = $_POST['tache'];
$duree = floatval($_POST['duree']);
$idUtilisateur = intval($_POST['idUtilisateur']);

// Convert datetime-local format to MySQL datetime format
$dateConnexion = date('Y-m-d H:i:s', strtotime($dateConnexion));
if (!empty($dateDeconnexion)) {
    $dateDeconnexion = date('Y-m-d H:i:s', strtotime($dateDeconnexion));
} else {
    $dateDeconnexion = '0000-00-00 00:00:00';
}

$controller = new HistoriqueC();
$result = $controller->modifierHistorique($id, $dateConnexion, $dateDeconnexion, $tache, $duree, $idUtilisateur);

header("Location: utilisateurs.php?historique=" . $idUtilisateur);
exit();
?>
