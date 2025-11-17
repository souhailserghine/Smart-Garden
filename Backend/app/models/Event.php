<?php
require_once __DIR__ . '/../core/Database.php';

class Event {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function addEvent($type, $date, $etat) {
        $sql = "INSERT INTO events (type_event, date_event, etat) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$type, $date, $etat]);
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM events");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM events WHERE event_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateEvent($id, $type, $date, $etat) {
        $sql = "UPDATE events SET type_event = ?, date_event = ?, etat = ? WHERE event_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$type, $date, $etat, $id]);
    }

    public function deleteEvent($id) {
        $stmt = $this->conn->prepare("DELETE FROM events WHERE event_id = ?");
        return $stmt->execute([$id]);
    }
}
