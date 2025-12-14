<?php

require_once __DIR__ . '/../config.php';

class Category
{
    protected $db;
    protected $table = 'categorie';

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
        $sql = "SELECT * FROM {$this->table} WHERE id_categorie = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (nom_categorie) VALUES (:nom_categorie)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nom_categorie' => $data['nom_categorie']]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET nom_categorie = :nom_categorie WHERE id_categorie = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom_categorie' => $data['nom_categorie'],
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_categorie = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getWithEventCount()
    {
        $sql = "SELECT c.*, COUNT(e.id_event) as event_count 
                FROM {$this->table} c 
                LEFT JOIN evenement e ON c.id_categorie = e.id_categorie 
                GROUP BY c.id_categorie";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
