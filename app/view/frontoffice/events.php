<?php
/**
 * Event API Endpoint
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../controller/EventController.php';

$controller = new EventController();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $controller->listEvents();
        break;
    case 'get':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->getEvent($id);
        break;
    case 'create':
        $controller->createEvent();
        break;
    case 'update':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->updateEvent($id);
        break;
    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->deleteEvent($id);
        break;
    case 'stats':
        $controller->getStats();
        break;
    case 'search':
        $controller->searchEvents();
        break;
    default:
        $controller->listEvents();
}
