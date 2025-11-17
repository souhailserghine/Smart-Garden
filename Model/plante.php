<?php 
class plante
{  
    // Attributs
    private $id_plante;
    private $nom_plante;
    private $date_ajout;
    private $niveau_humidite;
    private $besoin_eau;
    private $etat_sante;
    private $idUtilisateur;

    // Constructeur
    public function __construct($id_plante, $nom_plante, $date_ajout, $niveau_humidite, $besoin_eau, $etat_sante, $idUtilisateur)
    {
        $this->id_plante = $id_plante;
        $this->nom_plante = $nom_plante;
        $this->date_ajout = $date_ajout;
        $this->niveau_humidite = $niveau_humidite;
        $this->besoin_eau = $besoin_eau;
        $this->etat_sante = $etat_sante;
        $this->idUtilisateur = $idUtilisateur;
    }
    public function getIdPlante()
    {
        return $this->id_plante;
    }   
    public function getNomPlante()
    {
        return $this->nom_plante;
    }
    public function setNomPlante($nom_plante)
    {
        $this->nom_plante = $nom_plante;
    }
    public function getDateAjout()
    {
        return $this->date_ajout;

    }
    public function setDateAjout($date_ajout)
    {
        $this->date_ajout = $date_ajout;
    }   
    public function getNiveauHumidite()
    {
        return $this->niveau_humidite;
    }
    public function setNiveauHumidite($niveau_humidite)
    {
        $this->niveau_humidite = $niveau_humidite;
    }
    public function getBesoinEau()
    {
        return $this->besoin_eau;
    }
    public function setBesoinEau($besoin_eau)
    {
        $this->besoin_eau = $besoin_eau;
    }
    public function getEtatSante()
    {
        return $this->etat_sante;
    }
    public function setEtatSante($etat_sante)
    {
        $this->etat_sante = $etat_sante;
    }
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }
    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;
    }    

}
?>