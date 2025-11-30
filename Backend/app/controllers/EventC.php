<?php

include_once __DIR__ . '/../core/config.php';
include_once __DIR__ . '/../models/Event.php';

class EventC
{
    public function ajouterEvent($event)
    {
        $db = config::getConnexion();
        $sql = "INSERT INTO evenement (type_event, date_event, description, etat, id_categorie) 
                VALUES (:type_event, :date_event, :description, :etat, :id_categorie)";
        $req = $db->prepare($sql);
        $req->execute([
            ':type_event' => $event->getTypeEvent(),
            ':date_event' => $event->getDateEvent(),
            ':description' => $event->getDescription(),
            ':etat' => $event->getEtat(),
            ':id_categorie' => $event->getIdCategorie()
        ]);
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

    public function supprimerEvent($id)
    {
        $db = config::getConnexion();
        $req = $db->prepare("DELETE FROM evenement WHERE id_event = :id");
        $req->execute([':id' => $id]);
    }
}
?>