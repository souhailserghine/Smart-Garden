<?php
/**
 * Category API Endpoint
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../controller/CategoryController.php';

$controller = new CategoryController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $controller->getAllCategories();
        break;
    case 'get':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->getCategory($id);
        break;
    case 'create':
        $controller->createCategory();
        break;
    case 'update':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->updateCategory($id);
        break;
    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) $controller->deleteCategory($id);
        break;
    case 'with-count':
        $controller->getCategoriesWithCount();
        break;
    default:
        $controller->getAllCategories();
}
