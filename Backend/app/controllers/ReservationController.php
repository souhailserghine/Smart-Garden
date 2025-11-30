<?php
// view/Backend/app/controllers/ReservationController.php

class ReservationController
{
    // RÉSERVER UN ÉVÉNEMENT (user_id statique = 999)
   public function addReservation()
{
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    $input = json_decode(file_get_contents('php://input'), true);
    
    $event_id = $input['id_event'] ?? null;
    $user_id = $input['idUtilisateur'] ?? 999;  // ON FORCE 999 SI PAS ENVOYÉ

    if (!$event_id || !is_numeric($event_id)) {
        echo json_encode(["status" => "error", "message" => "ID événement requis"]);
        return;
    }

    require_once __DIR__ . '/../core/Database.php';
    $db = (new Database())->connect();

    try {
        // Vérifie si déjà réservé
        $check = $db->prepare("SELECT id_reservation FROM reservation WHERE id_event = ? AND idUtilisateur = ?");
        $check->execute([$event_id, $user_id]);
        if ($check->fetch()) {
            echo json_encode(["status" => "info", "message" => "Tu as déjà réservé cet événement !"]);
            return;
        }

        // Ajoute la réservation
        $stmt = $db->prepare("INSERT INTO reservation (id_event, idUtilisateur, date_reservation) VALUES (?, ?, NOW())");
        $stmt->execute([$event_id, $user_id]);

        echo json_encode(["status" => "success", "message" => "Réservé avec succès !"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Erreur DB"]);
    }
}

public function getReservedByUser()
{
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $input['idUtilisateur'] ?? 999;

    require_once __DIR__ . '/../core/Database.php';
    $db = (new Database())->connect();

    try {
        $stmt = $db->prepare("SELECT id_event FROM reservation WHERE idUtilisateur = ?");
        $stmt->execute([$user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN); // ← Tableau simple : [10, 15, 21, ...]

        echo json_encode([
            "status" => "success",
            "data"   => array_map('intval', $results)  // ← Force les entiers
        ]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "DB Error"]);
    }
}   // Toutes les réservations (admin)
    public function getAllReservations()
    {
        header('Content-Type: application/json');
        require_once __DIR__ . '/../core/Database.php';
        $db = (new Database())->connect();

        try {
            $sql = "SELECT r.*, e.type_event AS event_title, e.date_event 
                    FROM reservation r 
                    LEFT JOIN evenement e ON r.id_event = e.id_event 
                    ORDER BY r.date_reservation DESC";
            $stmt = $db->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "data" => $data]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // Supprimer une réservation
    public function deleteReservation()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id_reservation'] ?? null;

        if (!$id || !is_numeric($id)) {
            echo json_encode(["status" => "error", "message" => "ID manquant"]);
            return;
        }

        require_once __DIR__ . '/../core/Database.php';
        $db = (new Database())->connect();

        try {
            $stmt = $db->prepare("DELETE FROM reservation WHERE id_reservation = ?");
            $success = $stmt->execute([$id]);
            echo json_encode([
                "status" => $success ? "success" : "error",
                "message" => $success ? "Réservation annulée" : "Erreur suppression"
            ]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
?>