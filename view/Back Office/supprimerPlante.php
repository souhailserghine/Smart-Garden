<?php
include '../../Controller/planteC.php';

$planteC = new planteC();

if (isset($_GET['id'])) {
    $planteC->supprimerPlante($_GET['id']);
    header('Location: plantes.php');
    exit();
} else {
    echo "Aucun ID fourni.";
}
?>
