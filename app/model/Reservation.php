<?php

require_once __DIR__ . '/../config.php';

class Reservation
{
    protected $db;
    protected $table = 'reservation';

    public function __construct()
    {
        $this->db = config::getConnexion();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_reservation = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (id_event, idUtilisateur, nom, prenom, email, telephone, date_reservation) 
                VALUES (:id_event, :idUtilisateur, :nom, :prenom, :email, :telephone, :date_reservation)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_event' => $data['id_event'] ?? null,
            ':idUtilisateur' => $data['idUtilisateur'] ?? $data['id_user'] ?? null,
            ':nom' => $data['nom'] ?? '',
            ':prenom' => $data['prenom'] ?? '',
            ':email' => $data['email'] ?? '',
            ':telephone' => $data['telephone'] ?? null,
            ':date_reservation' => $data['date_reservation'] ?? date('Y-m-d H:i:s')
        ]);
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id_reservation = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_reservation = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getByEvent($eventId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_event = :event_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE idUtilisateur = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithDetails()
    {
        try {
            $sql = "SELECT 
                        r.*, 
                        e.type_event, 
                        e.date_event, 
                        e.lieu,
                        e.description,
                        e.type_event as event_title,
                        u.nom, 
                        u.email, 
                        u.localisation,
                        u.idUtilisateur
                    FROM {$this->table} r 
                    LEFT JOIN evenement e ON r.id_event = e.id_event 
                    LEFT JOIN utilisateur u ON r.idUtilisateur = u.idUtilisateur
                    ORDER BY r.date_reservation DESC";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log the query result
            error_log("Reservation query returned " . count($result) . " rows");
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error in getAllWithDetails: " . $e->getMessage());
            throw $e;
        }
    }
}
