<?php
require_once '../config.php';

header('Content-Type: application/json');

$capteurId = intval($_GET['capteurId'] ?? 1);
$hours = intval($_GET['hours'] ?? 24);

try {
    $db = config::getConnexion();
    
    // Get sensor data for the last X hours
    $stmt = $db->prepare('SELECT temperature, humidite, timestamp 
                          FROM sensor_data 
                          WHERE id_capteur = ? 
                          AND timestamp > DATE_SUB(NOW(), INTERVAL ? HOUR) 
                          ORDER BY timestamp ASC');
    $stmt->execute([$capteurId, $hours]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
