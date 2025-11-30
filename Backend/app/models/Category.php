<?php

class Event
{
    private $id_event;
    private $type_event;
    private $date_event;
    private $description;
    private $etat;
    private $id_categorie;

    public function __construct($id_event, $type_event, $date_event, $description, $etat, $id_categorie)
    {
        $this->id_event = $id_event;
        $this->type_event = $type_event;
        $this->date_event = $date_event;
        $this->description = $description;
        $this->etat = $etat;
        $this->id_categorie = $id_categorie;
    }

    public function getIdEvent()
    {
        return $this->id_event;
    }

    public function getTypeEvent()
    {
        return $this->type_event;
    }

    public function getDateEvent()
    {
        return $this->date_event;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function getIdCategorie()
    {
        return $this->id_categorie;
    }

    public function setIdEvent($id_event)
    {
        $this->id_event = $id_event;
    }

    public function setTypeEvent($type_event)
    {
        $this->type_event = $type_event;
    }

    public function setDateEvent($date_event)
    {
        $this->date_event = $date_event;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    public function setIdCategorie($id_categorie)
    {
        $this->id_categorie = $id_categorie;
    }
}
?>
