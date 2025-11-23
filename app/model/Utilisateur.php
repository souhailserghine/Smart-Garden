<?php
class Utilisateur {
    private $idUtilisateur;
    private $nom;
    private $email;
    private $motDePasse;
    private $localisation;
    private $dateInscription;
    private $role;
    private $statut;
    private $verified;
    
    public function __construct($idUtilisateur = null, $nom = "", $email = "", $motDePasse = "", $localisation = "", $dateInscription = "", $role = "", $statut = "actif", $verified = 0) {
        $this->idUtilisateur = $idUtilisateur;
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->localisation = $localisation;
        $this->dateInscription = $dateInscription;
        $this->role = $role;
        $this->statut = $statut;
        $this->verified = $verified;
    }

    // === Getters ===
    public function getIdUtilisateur() { return $this->idUtilisateur; }
    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getMotDePasse() { return $this->motDePasse; }
    public function getLocalisation() { return $this->localisation; }
    public function getDateInscription() { return $this->dateInscription; }
    public function getRole() { return $this->role; }
    public function getStatut() { return $this->statut; }
    public function getVerified() { return $this->verified; }

    // === Setters ===
    public function setIdUtilisateur($idUtilisateur) { $this->idUtilisateur = $idUtilisateur; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setEmail($email) { $this->email = $email; }
    public function setMotDePasse($motDePasse) { $this->motDePasse = $motDePasse; }
    public function setLocalisation($localisation) { $this->localisation = $localisation; }
    public function setDateInscription($dateInscription) { $this->dateInscription = $dateInscription; }
    public function setRole($role) { $this->role = $role; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setVerified($verified) { $this->verified = $verified; }

}
?>
