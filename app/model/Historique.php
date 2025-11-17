<?php
class Historique {
    private $idHistorique;
    private $dateConnexion;
    private $dateDeconnexion;
    private $tache;
    private $duree;
    private $idUtilisateur;
    private $id_dosage;

    public function __construct($idHistorique = null, $dateConnexion = "", $dateDeconnexion = "", $tache = "", $duree = "", $idUtilisateur = null, $id_dosage = null) {
        $this->idHistorique = $idHistorique;
        $this->dateConnexion = $dateConnexion;
        $this->dateDeconnexion = $dateDeconnexion;
        $this->tache = $tache;
        $this->duree = $duree;
        $this->idUtilisateur = $idUtilisateur;
        $this->id_dosage = $id_dosage;
    }

    // === Getters ===
    public function getIdHistorique() { return $this->idHistorique; }
    public function getDateConnexion() { return $this->dateConnexion; }
    public function getDateDeconnexion() { return $this->dateDeconnexion; }
    public function getTache() { return $this->tache; }
    public function getDuree() { return $this->duree; }
    public function getIdUtilisateur() { return $this->idUtilisateur; }
    public function getIdDosage() { return $this->id_dosage; }

    // === Setters ===
    public function setIdHistorique($idHistorique) { $this->idHistorique = $idHistorique; }
    public function setDateConnexion($dateConnexion) { $this->dateConnexion = $dateConnexion; }
    public function setDateDeconnexion($dateDeconnexion) { $this->dateDeconnexion = $dateDeconnexion; }
    public function setTache($tache) { $this->tache = $tache; }
    public function setDuree($duree) { $this->duree = $duree; }
    public function setIdUtilisateur($idUtilisateur) { $this->idUtilisateur = $idUtilisateur; }
    public function setIdDosage($id_dosage) { $this->id_dosage = $id_dosage; }
}
?>
