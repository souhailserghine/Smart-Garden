<?php
require_once __DIR__ . '/../models/Event.php';

class EventController {

    public function __construct() {
        header('Content-Type: application/json');
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    }

  
    public function add() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            echo json_encode(["status" => "error", "message" => "No input data received"]);
            return;
        }

        $requiredFields = ['type_event', 'date_event', 'etat'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || trim($input[$field]) === '') {
                echo json_encode(["status" => "error", "message" => "Field '$field' is required"]);
                return;
            }
        }

        $type = trim($input['type_event']);
        if (strlen($type) < 2 || strlen($type) > 100) {
            echo json_encode(["status" => "error", "message" => "'type_event' must be between 2 and 100 characters"]);
            return;
        }

    
        $date = $input['date_event'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) {
            echo json_encode(["status" => "error", "message" => "'date_event' must be a valid date (YYYY-MM-DD)"]);
            return;
        }
        $today = date('Y-m-d');
        if ($date < $today) {
            echo json_encode(["status" => "error", "message" => "'date_event' must be today or a future date"]);
            return;
        }

       
        $etat = strtolower($input['etat']);
        if (!in_array($etat, ['active', 'inactive'])) {
            echo json_encode(["status" => "error", "message" => "'etat' must be either 'active' or 'inactive'"]);
            return;
        }

        
        $event = new Event();
        $ok = $event->addEvent($type, $date, $etat);

        if ($ok) {
            echo json_encode(["status" => "success", "message" => "Event added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database insert failed"]);
        }
    }

    public function getAll() {
        $event = new Event();
        $data = $event->getAll();
        echo json_encode(["status" => "success", "data" => $data]);
    }

    public function getOne($id) {
        if (!is_numeric($id)) {
            echo json_encode(["status" => "error", "message" => "Invalid ID"]);
            return;
        }
        $event = new Event();
        $data = $event->getById($id);

        if ($data) {
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "error", "message" => "Event not found"]);
        }
    }

    public function update($id) {
        if (!is_numeric($id)) {
            echo json_encode(["status" => "error", "message" => "Invalid ID"]);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) {
            echo json_encode(["status" => "error", "message" => "No input data received"]);
            return;
        }

        
        $requiredFields = ['type_event', 'date_event', 'etat'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || trim($input[$field]) === '') {
                echo json_encode(["status" => "error", "message" => "Field '$field' is required"]);
                return;
            }
        }

       
        $type = trim($input['type_event']);
        if (strlen($type) < 2 || strlen($type) > 100) {
            echo json_encode(["status" => "error", "message" => "'type_event' must be between 2 and 100 characters"]);
            return;
        }

        $date = $input['date_event'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) {
            echo json_encode(["status" => "error", "message" => "'date_event' must be a valid date (YYYY-MM-DD)"]);
            return;
        }
        $today = date('Y-m-d');
        if ($date < $today) {
            echo json_encode(["status" => "error", "message" => "'date_event' must be today or a future date"]);
            return;
        }

      
        $etat = strtolower($input['etat']);
        if (!in_array($etat, ['active', 'inactive'])) {
            echo json_encode(["status" => "error", "message" => "'etat' must be either 'active' or 'inactive'"]);
            return;
        }

        
        $event = new Event();
        $ok = $event->updateEvent($id, $type, $date, $etat);

        if ($ok) {
            echo json_encode(["status"=>"success","message"=>"Event updated successfully"]);
        } else {
            echo json_encode(["status"=>"error","message"=>"Database update failed"]);
        }
    }

    public function delete($id) {
        if (!is_numeric($id)) {
            echo json_encode(["status" => "error", "message" => "Invalid ID"]);
            return;
        }

        $event = new Event();
        $ok = $event->deleteEvent($id);

        if ($ok) {
            echo json_encode(["status"=>"success","message"=>"Event deleted successfully"]);
        } else {
            echo json_encode(["status"=>"error","message"=>"Database delete failed"]);
        }
    }
}
