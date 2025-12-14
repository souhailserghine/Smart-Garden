<?php
// Debug script to test reservations
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';

try {
    $db = config::getConnexion();
    
    // Test 1: Check if reservation table exists
    echo json_encode([
        'test' => 'Database Connection',
        'status' => 'Connected to database successfully'
    ]) . "\n\n";
    
    // Test 2: Get raw reservation data
    $sql = "SELECT * FROM reservation";
    $stmt = $db->query($sql);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'test' => 'Raw Reservations',
        'count' => count($reservations),
        'data' => $reservations
    ], JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 3: Get reservations with event details
    $sql = "SELECT r.*, e.type_event, e.date_event, e.lieu, e.type_event as event_title
            FROM reservation r 
            LEFT JOIN evenement e ON r.id_event = e.id_event 
            ORDER BY r.date_reservation DESC";
    $stmt = $db->query($sql);
    $reservationsWithEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'test' => 'Reservations with Events',
        'count' => count($reservationsWithEvents),
        'data' => $reservationsWithEvents
    ], JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 4: Get reservations with full details (events + users)
    $sql = "SELECT 
                r.*, 
                e.type_event, 
                e.date_event, 
                e.lieu,
                e.description,
                e.type_event as event_title,
                u.nom, 
                u.email, 
                u.localisation,
                u.idUtilisateur
            FROM reservation r 
            LEFT JOIN evenement e ON r.id_event = e.id_event 
            LEFT JOIN utilisateur u ON r.idUtilisateur = u.idUtilisateur
            ORDER BY r.date_reservation DESC";
    $stmt = $db->query($sql);
    $fullDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'test' => 'Full Details (Events + Users)',
        'count' => count($fullDetails),
        'data' => $fullDetails
    ], JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 5: Check table structure
    $sql = "DESCRIBE reservation";
    $stmt = $db->query($sql);
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'test' => 'Reservation Table Structure',
        'columns' => $structure
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
