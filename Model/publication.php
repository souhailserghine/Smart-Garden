<?php
class Publication {
    private $idPublication;
    private $contenuTexte;
    private $datePublication;
    private $nbLikes;
    private $idUtilisateur;
    private $images;
    private $videos;
    
    private $statutModeration;
    private $raisonModeration;
    private $dateModeration;


    public function __construct($contenuTexte, $idUtilisateur, $nbLikes = 0, $images = null, $videos = null) {
        $this->contenuTexte = $contenuTexte;
        $this->idUtilisateur = $idUtilisateur;
        $this->nbLikes = $nbLikes;
        $this->images = $images;
        $this->videos = $videos;
        $this->datePublication = date('Y-m-d H:i:s');
        // INITIALISATION DES NOUVELLES PROPRIÉTÉS
        $this->statutModeration = 'en_attente';
        $this->raisonModeration = null;
        $this->dateModeration = null;
    }

    // Getters
    public function getIdPublication() { return $this->idPublication; }
    public function getContenuTexte() { return $this->contenuTexte; }
    public function getDatePublication() { return $this->datePublication; }
    public function getNbLikes() { return $this->nbLikes; }
    public function getIdUtilisateur() { return $this->idUtilisateur; }
    public function getImages() { return $this->images; }
    public function getVideos() { return $this->videos; }
    
    public function getStatutModeration() { return $this->statutModeration; }
    public function getRaisonModeration() { return $this->raisonModeration; }
    public function getDateModeration() { return $this->dateModeration; }

    // Setters
    public function setIdPublication($idPublication) { $this->idPublication = $idPublication; }
    public function setContenuTexte($contenuTexte) { $this->contenuTexte = $contenuTexte; }
    public function setNbLikes($nbLikes) { $this->nbLikes = $nbLikes; }
    public function setImages($images) { $this->images = $images; }
    public function setVideos($videos) { $this->videos = $videos; }
    
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