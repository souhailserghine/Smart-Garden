<?php
// view/Backend/app/controllers/ReservationController.php

// ON UTILISE LA MÊME CONFIG QUE TOUT LE RESTE DU PROJET
include_once __DIR__ . '/../core/config.php';

class ReservationController
{
    // RÉSERVER UN ÉVÉNEMENT (user_id statique = 999)
    public function addReservation()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');

        $input = json_decode(file_get_contents('php://input'), true);

        $event_id = $input['id_event'] ?? null;
        $user_id  = $input['idUtilisateur'] ?? 999; // fallback

        if (!$event_id || !is_numeric($event_id)) {
            echo json_encode(["status" => "error", "message" => "ID événement requis"]);
            return;
        }

        try {
            $db = config::getConnexion();

            // Vérifier si déjà réservé
            $check = $db->prepare("SELECT id_reservation FROM reservation WHERE id_event = ? AND idUtilisateur = ?");
            $check->execute([$event_id, $user_id]);
            if ($check->fetch()) {
                echo json_encode(["status" => "info", "message" => "Tu as déjà réservé cet événement !"]);
                return;
            }

            $stmt = $db->prepare("INSERT INTO reservation (id_event, idUtilisateur, date_reservation) VALUES (?, ?, NOW())");
            $stmt->execute([$event_id, $user_id]);

            echo json_encode(["status" => "success", "message" => "Réservé avec succès !"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Erreur DB : " . $e->getMessage()]);
        }
    }

    // Récupérer les événements réservés par un user
    public function getReservedByUser()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $input = json_decode(file_get_contents('php://input'), true);
        $user_id = $input['idUtilisateur'] ?? 999;

        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("SELECT id_event FROM reservation WHERE idUtilisateur = ?");
            $stmt->execute([$user_id]);
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

            echo json_encode([
                "status" => "success",
                "data"   => array_map('intval', $results)
            ]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "DB Error"]);
        }
    }

    // ADMIN : Toutes les réservations
    public function getAllReservations()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        try {
            $db = config::getConnexion();
            $sql = "SELECT r.*, e.type_event AS event_title, e.date_event 
                    FROM reservation r 
                    LEFT JOIN evenement e ON r.id_event = e.id_event 
                    ORDER BY r.date_reservation DESC";
            $stmt = $db->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["status" => "success", "data" => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // ADMIN : Modifier une réservation
    public function updateReservation()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $input = json_decode(file_get_contents('php://input'), true);

        $id_reservation   = $input['id_reservation'] ?? null;
        $idUtilisateur    = $input['idUtilisateur'] ?? null;
        $date_reservation = $input['date_reservation'] ?? null;

        if (!$id_reservation || !$idUtilisateur || !$date_reservation) {
            echo json_encode(["status" => "error", "message" => "Données manquantes"]);
            return;
        }

        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("UPDATE reservation SET idUtilisateur = ?, date_reservation = ? WHERE id_reservation = ?");
            $success = $stmt->execute([$idUtilisateur, $date_reservation, $id_reservation]);

            echo json_encode([
                "status"  => $success ? "success" : "error",
                "message" => $success ? "Réservation mise à jour" : "Échec de la mise à jour"
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // ADMIN : Supprimer une réservation
    public function deleteReservation()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id_reservation'] ?? null;

        if (!$id || !is_numeric($id)) {
            echo json_encode(["status" => "error", "message" => "ID manquant"]);
            return;
        }

        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("DELETE FROM reservation WHERE id_reservation = ?");
            $success = $stmt->execute([$id]);

            echo json_encode([
                "status"  => $success ? "success" : "error",
                "message" => $success ? "Réservation supprimée" : "Erreur suppression"
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}