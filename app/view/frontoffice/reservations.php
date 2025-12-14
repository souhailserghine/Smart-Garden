<?php
/**
 * Reservation API Endpoint
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../controller/ReservationController.php';

$controller = new ReservationController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $controller->getAllReservations();
        break;
    case 'get':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->getReservation($id);
        break;
    case 'create':
        $controller->createReservation();
        break;
    case 'update':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->updateReservation($id);
        break;
    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->deleteReservation($id);
        break;
    case 'by-event':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->getByEvent($id);
        break;
    case 'by-user':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->getByUser($id);
        break;
    default:
        $controller->getAllReservations();
}
