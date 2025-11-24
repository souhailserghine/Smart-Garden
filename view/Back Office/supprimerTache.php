<?php
include '../../Controller/tacheC.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID manquant');
    }

    $id = $_GET['id'];
    $tacheC = new tacheC();
    
    if ($tacheC->supprimerTache($id)) {
        header('Location: plantes.php');
    } else {
        throw new Exception('Erreur lors de la suppression');
    }

} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
?>
