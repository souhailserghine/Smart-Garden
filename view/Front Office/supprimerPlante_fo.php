<?php
session_start();
include '../../Controller/planteC.php';

$planteC = new planteC();
$userId = $_SESSION['idUtilisateur'];

if (isset($_GET['id'])) {
    try {
        $planteData = $planteC->getPlanteById($_GET['id']);
        
        if (!$planteData) {
            die("Plante introuvable !");
        }

        // Vérifier que la plante appartient à l'utilisateur
        if ($planteData['idUtilisateur'] != $userId) {
            die("Accès refusé !");
        }

        $ok = $planteC->supprimerPlante($_GET['id']);
        if ($ok) {
            header('Location: plantes.php');
            exit;
        } else {
            die("Suppression échouée");
        }
    } catch (Exception $e) {
        die("Erreur: " . $e->getMessage());
    }
} else {
    die("ID manquant");
}
?>
