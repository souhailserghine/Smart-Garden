<?php
class Commentaire {
    private $idCommentaire;
    private $contenuCommentaire;
    private $dateCommentaire;
    private $idPublication;
    private $idUtilisateur;
    private $nom;
    private $prenom;
    // NOUVELLES PROPRIÉTÉS POUR LA MODÉRATION
    private $statutModeration;
    private $raisonModeration;
    private $dateModeration;

    public function __construct($idCommentaire, $contenuCommentaire, $dateCommentaire, $idPublication, $idUtilisateur, $nom = null, $prenom = null) {
        $this->idCommentaire = $idCommentaire;
        $this->contenuCommentaire = $contenuCommentaire;
        $this->dateCommentaire = $dateCommentaire;
        $this->idPublication = $idPublication;
        $this->idUtilisateur = $idUtilisateur;
        $this->nom = $nom;
        $this->prenom = $prenom;
        // INITIALISATION DES NOUVELLES PROPRIÉTÉS
        $this->statutModeration = 'en_attente';
        $this->raisonModeration = null;
        $this->dateModeration = null;
    }

    // Getters
    public function getIdCommentaire() { return $this->idCommentaire; }
    public function getContenuCommentaire() { return $this->contenuCommentaire; }
    public function getDateCommentaire() { return $this->dateCommentaire; }
    public function getIdPublication() { return $this->idPublication; }
    public function getIdUtilisateur() { return $this->idUtilisateur; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    // NOUVEAUX GETTERS
    public function getStatutModeration() { return $this->statutModeration; }
    public function getRaisonModeration() { return $this->raisonModeration; }
    public function getDateModeration() { return $this->dateModeration; }

    // NOUVEAUX SETTERS
    public function setStatutModeration($statutModeration) { $this->statutModeration = $statutModeration; }
    public function setRaisonModeration($raisonModeration) { $this->raisonModeration = $raisonModeration; }
    public function setDateModeration($dateModeration) { $this->dateModeration = $dateModeration; }

    // MÉTHODES POUR LA MODÉRATION
    public function approuver() {
        $this->statutModeration = 'approuve';
        $this->raisonModeration = null;
        $this->dateModeration = date('Y-m-d H:i:s');
    }

    public function rejeter($raison) {
        $this->statutModeration = 'rejete';
        $this->raisonModeration = $raison;
        $this->dateModeration = date('Y-m-d H:i:s');
    }

    public function estEnAttente() {
        return $this->statutModeration === 'en_attente';
    }

    public function estApprouve() {
        return $this->statutModeration === 'approuve';
    }

    public function estRejete() {
        return $this->statutModeration === 'rejete';
    }
}
?>