<?php
require_once 'check_session.php';
include '../../controller/tacheC.php';
include '../../model/tache.php';


$tacheC = new tacheC();

try {
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
    
    $_SESSION['successMsg'] = "Tâche modifiée avec succès !";
} catch (Exception $e) {
    $_SESSION['errorMsg'] = "Erreur : " . $e->getMessage();
}

header("Location: plantes.php");
exit();
?>
