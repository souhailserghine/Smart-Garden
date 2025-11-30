<?php
// controllers/EventC.php - VERSION COMPLÉTÉE

include_once __DIR__ . '/../core/config.php';
include_once __DIR__ . '/../models/Event.php';

class EventC
{
    public function ajouterEvent($event)
    {
        try {
            $db = config::getConnexion();
            $sql = "INSERT INTO evenement 
                    (type_event, date_event, description, etat, id_categorie, lieu, latitude, longitude) 
                    VALUES (:type_event, :date_event, :description, :etat, :id_categorie, :lieu, :latitude, :longitude)";
            $req = $db->prepare($sql);
            $req->execute([
                ':type_event' => $event->getTypeEvent(),
                ':date_event' => $event->getDateEvent(),
                ':description' => $event->getDescription(),
                ':etat' => $event->getEtat(),
                ':id_categorie' => $event->getIdCategorie(),
                ':lieu' => $event->getLieu(),
                ':latitude' => $event->getLatitude(),
                ':longitude' => $event->getLongitude()
            ]);
            return ["status" => "success", "message" => "Événement ajouté avec succès"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Erreur: " . $e->getMessage()];
        }
    }

    public function modifierEvent($data)
    {
        try {
            $db = config::getConnexion();
            $sql = "UPDATE evenement SET 
                    type_event = :type_event, 
                    date_event = :date_event, 
                    description = :description, 
                    id_categorie = :id_categorie,
                    lieu = :lieu,
                    latitude = :latitude,
                    longitude = :longitude
                    WHERE id_event = :id_event";
            
            $req = $db->prepare($sql);
            $req->execute([
                ':type_event' => $data['type_event'],
                ':date_event' => $data['date_event'],
                ':description' => $data['description'],
                ':id_categorie' => $data['id_categorie'],
                ':lieu' => $data['lieu'],
                ':latitude' => $data['latitude'],
                ':longitude' => $data['longitude'],
                ':id_event' => $data['id_event']
            ]);
            
            return ["status" => "success", "message" => "Événement modifié avec succès"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Erreur: " . $e->getMessage()];
        }
    }

    public function listEvents()
    {
        $db = config::getConnexion();
        $sql = "SELECT e.*, c.nom_categorie 
                FROM evenement e 
                LEFT JOIN categorie c ON e.id_categorie = c.id_categorie 
                ORDER BY e.date_event DESC";
        $req = $db->query($sql);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventById($id)
    {
        $db = config::getConnexion();
        $sql = "SELECT e.*, c.nom_categorie 
                FROM evenement e 
                LEFT JOIN categorie c ON e.id_categorie = c.id_categorie 
                WHERE e.id_event = :id";
        $req = $db->prepare($sql);
        $req->execute([':id' => $id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function supprimerEvent($id)
    {
        try {
            $db = config::getConnexion();
            $req = $db->prepare("DELETE FROM evenement WHERE id_event = :id");
            $req->execute([':id' => $id]);
            return ["status" => "success", "message" => "Événement supprimé avec succès"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Erreur: " . $e->getMessage()];
        }
    }

    public function getStats()
    {
        $db = config::getConnexion();
        
        // Total événements
        $sql = "SELECT COUNT(*) as total FROM evenement";
        $req = $db->query($sql);
        $total = $req->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Événements à venir
        $sql = "SELECT COUNT(*) as upcoming FROM evenement WHERE date_event >= CURDATE()";
        $req = $db->query($sql);
        $upcoming = $req->fetch(PDO::FETCH_ASSOC)['upcoming'];
        
        // Événements passés
        $sql = "SELECT COUNT(*) as past FROM evenement WHERE date_event < CURDATE()";
        $req = $db->query($sql);
        $past = $req->fetch(PDO::FETCH_ASSOC)['past'];
        
        return [
            'total' => $total,
            'upcoming' => $upcoming,
            'past' => $past
        ];
    }

    public function getMonthlyStats()
    {
        $db = config::getConnexion();
        $sql = "SELECT 
                    MONTH(date_event) as month, 
                    YEAR(date_event) as year,
                    COUNT(*) as count 
                FROM evenement 
                WHERE YEAR(date_event) = YEAR(CURDATE())
                GROUP BY YEAR(date_event), MONTH(date_event)
                ORDER BY year, month";
        
        $req = $db->query($sql);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>