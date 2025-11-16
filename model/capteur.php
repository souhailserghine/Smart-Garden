<?php
class Capteur {
    // Attributs privés correspondant à la base de données
    private $id_capteur;
    private $etatCapteur;
    private $uniteCapteur;
    private $emplacement;
    private $dateInstallation;
    private $id_categorie;  // Clé étrangère vers categorie
    private $id_plante;     // Clé étrangère vers plante (optionnelle)

    // Constructeur
    public function __construct(
        $etatCapteur = "", 
        $uniteCapteur = "", 
        $emplacement = "", 
        $dateInstallation = null, 
        $id_categorie = null, 
        $id_plante = null
    ) {
        $this->etatCapteur = $etatCapteur;
        $this->uniteCapteur = $uniteCapteur;
        $this->emplacement = $emplacement;
        $this->dateInstallation = $dateInstallation;
        $this->id_categorie = $id_categorie;
        $this->id_plante = $id_plante;
    }

    // Getters
    public function getIdCapteur() {
        return $this->id_capteur;
    }

    public function getEtatCapteur() {
        return $this->etatCapteur;
    }

    public function getUniteCapteur() {
        return $this->uniteCapteur;
    }

    public function getEmplacement() {
        return $this->emplacement;
    }

    public function getDateInstallation() {
        return $this->dateInstallation;
    }

    public function getIdCategorie() {
        return $this->id_categorie;
    }

    public function getIdPlante() {
        return $this->id_plante;
    }

    // Setters
    public function setIdCapteur($id_capteur) {
        $this->id_capteur = (int)$id_capteur;
    }

    public function setEtatCapteur($etatCapteur) {
        $this->etatCapteur = $etatCapteur;
    }

    public function setUniteCapteur($uniteCapteur) {
        $this->uniteCapteur = $uniteCapteur;
    }

    public function setEmplacement($emplacement) {
        $this->emplacement = $emplacement;
    }

    public function setDateInstallation($dateInstallation) {
        $this->dateInstallation = $dateInstallation;
    }

    public function setIdCategorie($id_categorie) {
        $this->id_categorie = $id_categorie;
    }

    public function setIdPlante($id_plante) {
        $this->id_plante = $id_plante;
    }


}
?>