<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

header('Content-Type: application/json');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

set_exception_handler(function($e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit;
});

require_once __DIR__ . '/../app/controllers/EventController.php';

$controller = new EventController();

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; 
}


switch($action) {
    case 'add':
        $controller->add();
        break;

    case 'getAll':
        $controller->getAll();
        break;

    case 'getOne':
        if ($id) {
            $controller->getOne($id);
        } else {
            echo json_encode(["status" => "error", "message" => "ID missing"]);
        }
        break;

    case 'update':
        if ($id) {
            $controller->update($id);
        } else {
            echo json_encode(["status" => "error", "message" => "ID missing"]);
        }
        break;

    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            echo json_encode(["status" => "error", "message" => "ID missing"]);
        }
        break;

    default:
        echo json_encode(["status" => "success", "message" => "API working"]);
}
