<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include '../../Controller/planteC.php';
include '../../Model/plante.php';

$planteC = new planteC();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userId = $_SESSION['idUtilisateur'];
        
        // Création de l'objet Plante
        $plante = new Plante(
            null,
            $_POST['nom_plante'],
            $_POST['date_ajout'],
            $_POST['niveau_humidite'],
            $_POST['besoin_eau'],
            $_POST['etat_sante'],
            $userId
        );

        // Ajout dans la base
        $result = $planteC->ajouterPlante($plante);

        echo json_encode(['success' => true, 'message' => 'Plante ajoutée avec succès']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
?>
