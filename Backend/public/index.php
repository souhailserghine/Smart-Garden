<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../app/controllers/EventC.php';
require_once __DIR__ . '/../app/controllers/CategoryController.php';
require_once __DIR__ . '/../app/controllers/ReservationController.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'listEvents':
        $ec = new EventC();
        $events = $ec->listEvents();
        echo json_encode(["status" => "success", "data" => $events]);
        break;

    case 'addEvent':
        $input = json_decode(file_get_contents("php://input"), true);
        $event = new Event(
            null,
            $input['type_event'] ?? '',
            $input['date_event'] ?? '',
            $input['description'] ?? '',
            'active',
            $input['id_categorie'] ?? null,
            $input['lieu'] ?? '',
            $input['latitude'] ?? null,
            $input['longitude'] ?? null
        );
        $ec = new EventC();
        $result = $ec->ajouterEvent($event);
        echo json_encode($result);
        break;

    case 'deleteEvent':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $ec = new EventC();
            $result = $ec->supprimerEvent($id);
            echo json_encode($result);
        } else {
            echo json_encode(["status" => "error", "message" => "ID requis"]);
        }
        break;

case 'listCategories':
    $cc = new CategoryController();
    $cc->getAllCategories(); // This already echoes JSON, so don't wrap it
    break;

    case 'reserveEvent':
        $rc = new ReservationController();
        $rc->addReservation();
        break;

    case 'myReservations':
        $rc = new ReservationController();
        $rc->getReservedByUser();
        break;

    case 'deleteReservation':
        $rc = new ReservationController();
        $rc->deleteReservation();
        break;

    case 'listReservations':
        $rc = new ReservationController();
        $rc->getAllReservations();
        break;

    case 'updateReservation':
        $rc = new ReservationController();
        $rc->updateReservation();
        break;

    default:
        echo json_encode(["status" => "ok", "message" => "API prête"]);
}
?>
