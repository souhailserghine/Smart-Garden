<?php
require_once 'check_session.php';
header('Content-Type: application/json');

include_once '../../config.php';
include_once '../../controller/planteC.php';

try {
    if (!isset($_SESSION['idUtilisateur'])) {
        throw new Exception('Utilisateur non authentifié');
    }
    
    $userId = $_SESSION['idUtilisateur'];
    $planteC = new planteC();
    
    // If ID is provided, return single plant
    if (isset($_GET['id'])) {
        $plante = $planteC->getPlanteById($_GET['id']);
        
        if (!$plante) {
            throw new Exception('Plante introuvable');
        }
        
        // Verify the plant belongs to the user
        if ($plante['idUtilisateur'] != $userId) {
            throw new Exception('Accès refusé');
        }
        
        echo json_encode($plante);
    } else {
        // Return all plants for the user
        $plantes = $planteC->listPlantesByUser($userId);
        echo json_encode($plantes);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
