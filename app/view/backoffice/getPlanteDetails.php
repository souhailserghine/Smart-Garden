<?php
/**
 * API Endpoint to get plant details
 * Following MVC pattern using Controller
 * 
 * Usage: GET /getPlanteDetails.php?id=1
 * Response: JSON with plant details
 */

require_once '../../controller/planteC.php';

// Get plant ID from request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'ID de plante invalide'
    ]);
    exit;
}

// Use Controller to get plant details (MVC pattern)
$planteController = new planteC();
$plante = $planteController->getPlanteById($id);

if ($plante) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'plante' => $plante
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Plante non trouvée'
    ]);
}
?>
