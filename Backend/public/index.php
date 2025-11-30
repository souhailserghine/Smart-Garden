    <?php
    // view/Backend/public/index.php — VERSION FINALE 100% FONCTIONNELLE

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Allow-Headers: Content-Type');

    require_once __DIR__ . '/../app/controllers/EventC.php';
    require_once __DIR__ . '/../app/controllers/CategoryController.php';
    require_once __DIR__ . '/../app/controllers/ReservationController.php';

    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'list':
            $ec = new EventC();
            $events = $ec->listEvents();
            echo json_encode(["status" => "success", "data" => $events]);
            break;

        case 'add':
            $input = json_decode(file_get_contents("php://input"), true);
            $event = new Event(
                null,
                $input['type_event'] ?? '',
                $input['date_event'] ?? '',
                $input['description'] ?? '',
                'active',
                $input['id_categorie'] ?? null
            );
            $ec = new EventC();
            $ec->ajouterEvent($event);
            echo json_encode(["status" => "success", "message" => "Événement ajouté"]);
            break;

        case 'delete':
            $id = $_ gET['id'] ?? null;
            if ($id) {
                $ec = new EventC();
                $ec->supprimerEvent($id);
                echo json_encode(["status" => "success", "message" => "Supprimé"]);
            } else {
                echo json_encode(["status" => "error", "message" => "ID requis"]);
            }
            break;

        case 'categories':
            $cc = new CategoryController();
            $cc->getAllCategories();
            break;

        case 'reserve':
            $rc = new ReservationController();
            $rc->addReservation();
            break;

        case 'mes-reservations':
            $rc = new ReservationController();
            $rc->getReservedByUser();
            break;

        case 'deleteReservation':
            $rc = new ReservationController();
            $rc->deleteReservation();
            break;
            case 'getAllReservations':
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