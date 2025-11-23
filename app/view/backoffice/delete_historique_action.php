<?php
require_once '../../controller/historiqueController.php';

$id = intval($_POST['id']);
$idUtilisateur = intval($_POST['userId']);

$controller = new HistoriqueC();
$result = $controller->supprimerHistorique($id);

header("Location: utilisateurs.php?historique=" . $idUtilisateur);
exit();
?>
