<?php
class Categorie {
    // Attributs privés correspondant à la base de données
    private $id_categorie;
    private $nom_categorie;

    // Constructeur
    public function __construct($nom_categorie = "") {
        $this->nom_categorie = $nom_categorie;
    }

    // Getters
    public function getIdCategorie() {
        return $this->id_categorie;
    }

    public function getNomCategorie() {
        return $this->nom_categorie;
    }

    // Setters
    public function setIdCategorie($id_categorie) {
        $this->id_categorie = (int)$id_categorie;
    }

    public function setNomCategorie($nom_categorie) {
        $this->nom_categorie = $nom_categorie;
    }
}
?>