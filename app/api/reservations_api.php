<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../controller/ReservationController.php';

try {
    $controller = new ReservationController();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'listReservations':
            $controller->getAllReservations();
            break;

        case 'getReservation':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->getReservation($id);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID requis']);
            }
            break;

        case 'createReservation':
            $controller->createReservation();
            break;

        case 'updateReservation':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->updateReservation($id);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID requis']);
            }
            break;

        case 'deleteReservation':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id_reservation'] ?? $_GET['id'] ?? null;
            if ($id) {
                $controller->deleteReservation($id);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de réservation requis']);
            }
            break;

        case 'getByEvent':
            $eventId = $_GET['event_id'] ?? null;
            if ($eventId) {
                $controller->getByEvent($eventId);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Event ID requis']);
            }
            break;

        case 'getByUser':
            $userId = $_GET['user_id'] ?? null;
            if ($userId) {
                $controller->getByUser($userId);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User ID requis']);
            }
            break;

        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Action non valide',
                'available_actions' => [
                    'listReservations',
                    'getReservation',
                    'createReservation',
                    'updateReservation',
                    'deleteReservation',
                    'getByEvent',
                    'getByUser'
                ]
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
