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
        $event_id = $input['id_event'] ?? null;   // LE NOM QUE TON FRONT ENVOIE
        $user_id = 999; // utilisateur statique

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

    // Récupérer les réservations de l'utilisateur statique
    public function getReservedByUser()
    {
        header('Content-Type: application/json');
        require_once __DIR__ . '/../core/Database.php';
        $db = (new Database())->connect();

        try {
            $stmt = $db->prepare("
                SELECT e.*, r.date_reservation, r.id_reservation
                FROM reservation r
                JOIN evenement e ON r.id_event = e.id_event
                WHERE r.idUtilisateur = 999
                ORDER BY r.date_reservation DESC
            ");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "data" => $data]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // Toutes les réservations (admin)
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