<?php
class Evenement {
    private $id_event;
    private $type_event;
    private $date_event;
    private $description;
    private $etat;
    private $idUtilisateur;

    public function __construct($id_event = null, $type_event = null, $date_event = null, $description = null, $etat = null, $idUtilisateur = null){
        $this->id_event = $id_event;
        $this->type_event = $type_event;
        $this->date_event = $date_event;
        $this->description = $description;
        $this->etat = $etat;
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getIdEvent(){ return $this->id_event; }
    public function getTypeEvent(){ return $this->type_event; }
    public function getDateEvent(){ return $this->date_event; }
    public function getDescription(){ return $this->description; }
    public function getEtat(){ return $this->etat; }
    public function getIdUtilisateur(){ return $this->idUtilisateur; }

    public function setTypeEvent($type_event){ $this->type_event = $type_event; }
    public function setDateEvent($date_event){ $this->date_event = $date_event; }
    public function setDescription($description){ $this->description = $description; }
    public function setEtat($etat){ $this->etat = $etat; }
    public function setIdUtilisateur($idUtilisateur){ $this->idUtilisateur = $idUtilisateur; }
}
?>
