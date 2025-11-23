<?php
class Historique {
    private $idHistorique;
    private $dateConnexion;
    private $dateDeconnexion;
    private $tache;
    private $duree;
    private $idUtilisateur;

    public function __construct($idHistorique = null, $dateConnexion = "", $dateDeconnexion = "", $tache = "", $duree = "", $idUtilisateur = null) {
        $this->idHistorique = $idHistorique;
        $this->dateConnexion = $dateConnexion;
        $this->dateDeconnexion = $dateDeconnexion;
        $this->tache = $tache;
        $this->duree = $duree;
        $this->idUtilisateur = $idUtilisateur;
    }

    // === Getters ===
    public function getIdHistorique() { return $this->idHistorique; }
    public function getDateConnexion() { return $this->dateConnexion; }
    public function getDateDeconnexion() { return $this->dateDeconnexion; }
    public function getTache() { return $this->tache; }
    public function getDuree() { return $this->duree; }
    public function getIdUtilisateur() { return $this->idUtilisateur; }

    // === Setters ===
    public function setIdHistorique($idHistorique) { $this->idHistorique = $idHistorique; }
    public function setDateConnexion($dateConnexion) { $this->dateConnexion = $dateConnexion; }
    public function setDateDeconnexion($dateDeconnexion) { $this->dateDeconnexion = $dateDeconnexion; }
    public function setTache($tache) { $this->tache = $tache; }
    public function setDuree($duree) { $this->duree = $duree; }
    public function setIdUtilisateur($idUtilisateur) { $this->idUtilisateur = $idUtilisateur; }
}
?>
