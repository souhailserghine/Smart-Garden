<?php
require_once __DIR__ . '/EvenementController.php';

$controller = new EvenementController();
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

switch($action){
    case 'add': $controller->add(); break;
    case 'getAll': $controller->getAll(); break;
    case 'info': $controller->info(); break;
    case 'getOne': if($id) $controller->getOne($id); break;
    case 'update': if($id) $controller->update($id); break;
    case 'delete': if($id) $controller->delete($id); break;
    default: echo json_encode(['status'=>'success','message'=>'API working']);
}
?>
