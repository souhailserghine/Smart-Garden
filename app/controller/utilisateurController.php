<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Utilisateur.php';

class UtilisateurC{
    
    public function ajouterUtilisateur($nom, $email, $motDePasse, $localisation, $statut){
        $db = config::getConnexion();
        
        $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);
        $dateInscription = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO utilisateur (nom, email, motDePasse, localisation, dateInscription, statut) 
                VALUES ('$nom', '$email', '$motDePasseHash', '$localisation', '$dateInscription', '$statut')";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function modifierUtilisateur($id, $nom, $email, $localisation, $statut){
        $db = config::getConnexion();
        
        $sql = "UPDATE utilisateur SET nom='$nom', email='$email', localisation='$localisation', statut='$statut' WHERE idUtilisateur=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function supprimerUtilisateur($id){
        $db = config::getConnexion();
        
        $sql = "DELETE FROM utilisateur WHERE idUtilisateur=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    public function changerStatut($id, $statut){
        $db = config::getConnexion();
        
        $sql = "UPDATE utilisateur SET statut='$statut' WHERE idUtilisateur=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function afficher(){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM utilisateur ORDER BY dateInscription DESC";
        
        $result = $db->query($sql);
        $users = $result->fetchAll();
        
        return $users;
    }

    public function afficherParId($id){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM utilisateur WHERE idUtilisateur=$id";
        
        $result = $db->query($sql);
        $user = $result->fetch();
        
        return $user;
    }

    public function rechercher($recherche){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM utilisateur WHERE nom LIKE '%$recherche%' OR email LIKE '%$recherche%' OR localisation LIKE '%$recherche%' ORDER BY nom ASC";
        
        $result = $db->query($sql);
        $users = $result->fetchAll();
        
        return $users;
    }

    public function authentifier($email, $motDePasse){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM utilisateur WHERE email='$email'";
        
        $result = $db->query($sql);
        $user = $result->fetch();
        
        if ($user) {
            if ($user['statut'] == 'bloque') {
                return null;
            }
            
            $passwordMatch = false;
            
            if (password_verify($motDePasse, $user['motDePasse'])) {
                $passwordMatch = true;
            } 
            elseif ($motDePasse === $user['motDePasse']) {
                $passwordMatch = true;
            }
            
            if ($passwordMatch) {
                return $user;
            }
        }
        
        return null;
    }
    
    public function compterUtilisateurs(){
        $db = config::getConnexion();
        
        $sql = "SELECT COUNT(*) as total FROM utilisateur";
        
        $result = $db->query($sql);
        $data = $result->fetch();
        
        return $data['total'];
    }
    
    public function compterParStatut($statut){
        $db = config::getConnexion();
        
        $sql = "SELECT COUNT(*) as total FROM utilisateur WHERE statut='$statut'";
        
        $result = $db->query($sql);
        $data = $result->fetch();
        
        return $data['total'];
    }
    
    public function compterNouveauxAujourdhui(){
        $db = config::getConnexion();
        
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total FROM utilisateur WHERE DATE(dateInscription)='$today'";
        
        $result = $db->query($sql);
        $data = $result->fetch();
        
        return $data['total'];
    }
}
?>
