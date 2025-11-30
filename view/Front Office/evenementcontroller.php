<?php
require_once __DIR__ . '/../models/Evenement.php';

class EvenementController {

    private function connectDB() {
        try {
            $db = new PDO('mysql:host=localhost;dbname=smartgarden;charset=utf8', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Ajouter un nouvel événement
    public function add() {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) {
            echo json_encode(["status"=>"error","message"=>"No input data received"]);
            return;
        }

        $requiredFields = ['type_event','date_event','etat'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || trim($input[$field]) === '') {
                echo json_encode(["status"=>"error","message"=>"Field '$field' is required"]);
                return;
            }
        }

        $type = trim($input['type_event']);
        $date = $input['date_event'];
        $etat = strtolower($input['etat']);

        // Validations
        if (strlen($type) < 2 || strlen($type) > 100) {
            echo json_encode(["status"=>"error","message"=>"'type_event' must be between 2 and 100 characters"]);
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) {
            echo json_encode(["status"=>"error","message"=>"'date_event' must be a valid date (YYYY-MM-DD)"]);
            return;
        }

        $today = date('Y-m-d');
        if ($date < $today) {
            echo json_encode(["status"=>"error","message"=>"'date_event' must be today or a future date"]);
            return;
        }

        if (!in_array($etat, ['active','inactive'])) {
            echo json_encode(["status"=>"error","message"=>"'etat' must be either 'active' or 'inactive'"]);
            return;
        }

        try {
            $db = $this->connectDB();
            $stmt = $db->prepare("INSERT INTO evenement (type_event, date_event, etat) VALUES (:type, :date, :etat)");
            $stmt->execute([
                'type' => $type,
                'date' => $date,
                'etat' => $etat
            ]);
            echo json_encode(["status"=>"success","message"=>"Event added successfully"]);
        } catch (Exception $e) {
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }

    // Lister tous les événements
    public function getAll() {
        try {
            $db = $this->connectDB();
            $stmt = $db->query("SELECT * FROM evenement ORDER BY date_event ASC");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status"=>"success","data"=>$events]);
        } catch (Exception $e) {
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }

    // Obtenir un événement par ID
    public function getOne($id) {
        if (!is_numeric($id)) {
            echo json_encode(["status"=>"error","message"=>"Invalid ID"]);
            return;
        }

        try {
            $db = $this->connectDB();
            $stmt = $db->prepare("SELECT * FROM evenement WHERE id_event = :id");
            $stmt->execute(['id'=>$id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                echo json_encode(["status"=>"success","data"=>$event]);
            } else {
                echo json_encode(["status"=>"error","message"=>"Event not found"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }

    // Mettre à jour un événement
    public function update($id) {
        if (!is_numeric($id)) {
            echo json_encode(["status"=>"error","message"=>"Invalid ID"]);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) {
            echo json_encode(["status"=>"error","message"=>"No input data received"]);
            return;
        }

        $requiredFields = ['type_event','date_event','etat'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || trim($input[$field]) === '') {
                echo json_encode(["status"=>"error","message"=>"Field '$field' is required"]);
                return;
            }
        }

        $type = trim($input['type_event']);
        $date = $input['date_event'];
        $etat = strtolower($input['etat']);

        // Validations
        if (strlen($type) < 2 || strlen($type) > 100) {
            echo json_encode(["status"=>"error","message"=>"'type_event' must be between 2 and 100 characters"]);
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) {
            echo json_encode(["status"=>"error","message"=>"'date_event' must be a valid date (YYYY-MM-DD)"]);
            return;
        }

        $today = date('Y-m-d');
        if ($date < $today) {
            echo json_encode(["status"=>"error","message"=>"'date_event' must be today or a future date"]);
            return;
        }

        if (!in_array($etat, ['active','inactive'])) {
            echo json_encode(["status"=>"error","message"=>"'etat' must be either 'active' or 'inactive'"]);
            return;
        }

        try {
            $db = $this->connectDB();
            $stmt = $db->prepare("
                UPDATE evenement 
                SET type_event = :type, date_event = :date, etat = :etat 
                WHERE id_event = :id
            ");
            $stmt->execute([
                'type' => $type,
                'date' => $date,
                'etat' => $etat,
                'id' => $id
            ]);
            echo json_encode(["status"=>"success","message"=>"Event updated successfully"]);
        } catch (Exception $e) {
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }

    // Supprimer un événement
    public function delete($id) {
        if (!is_numeric($id)) {
            echo json_encode(["status"=>"error","message"=>"Invalid ID"]);
            return;
        }

        try {
            $db = $this->connectDB();
            $stmt = $db->prepare("DELETE FROM evenement WHERE id_event = :id");
            $stmt->execute(['id'=>$id]);
            echo json_encode(["status"=>"success","message"=>"Event deleted successfully"]);
        } catch (Exception $e) {
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }
}
?>
