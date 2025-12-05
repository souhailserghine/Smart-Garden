<?php
session_start();
header('Content-Type: application/json');

include_once '../../config.php';
include_once '../../Controller/planteC.php';

try {
    if (!isset($_SESSION['idUtilisateur'])) {
        throw new Exception('Utilisateur non authentifié');
    }
    
    $userId = $_SESSION['idUtilisateur'];
    $planteC = new planteC();
    $plantes = $planteC->listPlantesByUser($userId);
    
    echo json_encode($plantes);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
