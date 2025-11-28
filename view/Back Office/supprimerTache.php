<?php
include '../../Controller/tacheC.php';

$tacheC = new tacheC();

if (isset($_GET['id'])) {
    $tacheC->supprimerTache($_GET['id']);
    header('Location: plantes.php');
    exit();
} else {
    echo "Aucun ID fourni.";
}
?>
