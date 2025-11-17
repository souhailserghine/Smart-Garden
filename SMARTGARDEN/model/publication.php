<?php
class Publication {
    private $id;
    private $titre;
    private $contenu;
    private $auteur_id;
    private $date_publication;

    public function __construct($titre, $contenu, $auteur_id) {
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->auteur_id = $auteur_id;
        $this->date_publication = date('Y-m-d H:i:s');
    }

    // Getters 
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getContenu() { return $this->contenu; }
    public function getAuteurId() { return $this->auteur_id; }
    public function getDatePublication() { return $this->date_publication; }

    // Setters 
    public function setId($id) { $this->id = $id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
}
?>