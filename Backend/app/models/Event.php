<?php
// view/Backend/app/models/Event.php

class Event
{
    private $id_event;
    private $type_event;
    private $date_event;
    private $description;
    private $etat;
    private $id_categorie;

    public function __construct($id_event = null, $type_event = null, $date_event = null, $description = '', $etat = 'active', $id_categorie = null)
    {
        $this->id_event = $id_event;
        $this->type_event = $type_event;
        $this->date_event = $date_event;
        $this->description = $description;
        $this->etat = $etat;
        $this->id_categorie = $id_categorie;
    }

    public function getIdEvent() { return $this->id_event; }
    public function getTypeEvent() { return $this->type_event; }
    public function getDateEvent() { return $this->date_event; }
    public function getDescription() { return $this->description; }
    public function getEtat() { return $this->etat; }
    public function getIdCategorie() { return $this->id_categorie; }

    public function setTypeEvent($v) { $this->type_event = $v; }
    public function setDateEvent($v) { $this->date_event = $v; }
    public function setDescription($v) { $this->description = $v; }
    public function setEtat($v) { $this->etat = $v; }
    public function setIdCategorie($v) { $this->id_categorie = $v; }
}
?>