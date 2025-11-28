<?php
include '../../Controller/tacheC.php';
include '../../Model/tache.php';

$tacheC = new tacheC();

$t = new tache(
    $_POST['id_dosage'],
    $_POST['type_dosage'],
    $_POST['quantite'],
    $_POST['mode_dosage'],
    $_POST['date_dosage'],
    $_POST['derniereExecution'],
    $_POST['prochaineExecution'],
    $_POST['estComplete'],
    $_POST['priorite'],
    $_POST['id_plante']
);

$tacheC->modifierTache($t, $_POST['id_dosage']);

header("Location: plantes.php");
exit();
?>
