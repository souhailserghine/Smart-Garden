<?php
session_start();
include '../../Controller/planteC.php';
include '../../Model/plante.php';

$planteC = new planteC();
$userId = $_SESSION['idUtilisateur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $planteData = $planteC->getPlanteById($_POST['id_plante']);
        
        if (!$planteData) {
            die("Plante introuvable !");
        }

        // Vérifier que la plante appartient à l'utilisateur
        if ($planteData['idUtilisateur'] != $userId) {
            die("Accès refusé !");
        }

        $plante = new Plante(
            $_POST['id_plante'],
            $_POST['nom_plante'],
            $_POST['date_ajout'],
            $_POST['niveau_humidite'],
            $_POST['besoin_eau'],
            $_POST['etat_sante'],
            $userId
        );

        $ok = $planteC->modifierPlante($plante, $_POST['id_plante']);
        if ($ok) {
            header('Location: plantes.php');
            exit;
        } else {
            echo "<p style='color:red;'>Modification échouée</p>";
        }
    } catch (Exception $e) {
        die("Erreur: " . $e->getMessage());
    }
}
?>
