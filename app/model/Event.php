<?php

require_once __DIR__ . '/../config.php';

class Event
{
    protected $db;
    protected $table = 'evenement';

    public function __construct()
    {
        $this->db = config::getConnexion();
    }

    public function getAllWithCategory()
    {
        $sql = "SELECT e.*, c.nom_categorie 
                FROM {$this->table} e 
                LEFT JOIN categorie c ON e.id_categorie = c.id_categorie 
                ORDER BY e.date_event DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdWithCategory($id)
    {
        $sql = "SELECT e.*, c.nom_categorie 
                FROM {$this->table} e 
                LEFT JOIN categorie c ON e.id_categorie = c.id_categorie 
                WHERE e.id_event = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (type_event, date_event, description, etat, id_categorie, lieu, latitude, longitude) 
                VALUES (:type_event, :date_event, :description, :etat, :id_categorie, :lieu, :latitude, :longitude)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':type_event' => $data['type_event'],
            ':date_event' => $data['date_event'],
            ':description' => $data['description'],
            ':etat' => $data['etat'] ?? 'active',
            ':id_categorie' => $data['id_categorie'] ?? null,
            ':lieu' => $data['lieu'] ?? null,
            ':latitude' => $data['latitude'] ?? null,
            ':longitude' => $data['longitude'] ?? null
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                type_event = :type_event, 
                date_event = :date_event, 
                description = :description, 
                id_categorie = :id_categorie,
                lieu = :lieu,
                latitude = :latitude,
                longitude = :longitude
                WHERE id_event = :id_event";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':type_event' => $data['type_event'],
            ':date_event' => $data['date_event'],
            ':description' => $data['description'],
            ':id_categorie' => $data['id_categorie'] ?? null,
            ':lieu' => $data['lieu'] ?? null,
            ':latitude' => $data['latitude'] ?? null,
            ':longitude' => $data['longitude'] ?? null,
            ':id_event' => $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_event = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY date_event DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_event = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStats()
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN etat = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN etat = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN date_event >= CURDATE() THEN 1 ELSE 0 END) as upcoming
                FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function search($filters = [])
    {
        $sql = "SELECT e.*, c.nom_categorie 
                FROM {$this->table} e 
                LEFT JOIN categorie c ON e.id_categorie = c.id_categorie 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (e.type_event LIKE :search OR e.description LIKE :search OR e.lieu LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category'])) {
            $sql .= " AND e.id_categorie = :category";
            $params[':category'] = $filters['category'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND e.etat = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['date_start'])) {
            $sql .= " AND e.date_event >= :date_start";
            $params[':date_start'] = $filters['date_start'];
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND e.date_event <= :date_end";
            $params[':date_end'] = $filters['date_end'];
        }

        $sql .= " ORDER BY e.date_event DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
