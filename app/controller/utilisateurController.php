<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Utilisateur.php';

class UtilisateurC{
    
    public function ajouterUtilisateur($nom, $email, $motDePasse, $localisation, $statut, $verified = 0){
        $db = config::getConnexion();
        
        $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);
        $dateInscription = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO utilisateur (nom, email, motDePasse, localisation, dateInscription, statut, verified) 
                VALUES ('$nom', '$email', '$motDePasseHash', '$localisation', '$dateInscription', '$statut', $verified)";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        }
        return false;
    }

    public function modifierUtilisateur($id, $nom, $email, $localisation, $statut){
        $db = config::getConnexion();
        
        $sql = "UPDATE utilisateur SET nom='$nom', email='$email', localisation='$localisation', statut='$statut' WHERE idUtilisateur=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        }
        return false;
    }

    public function supprimerUtilisateur($id){
        $db = config::getConnexion();
        
        $sql = "DELETE FROM utilisateur WHERE idUtilisateur=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        }
        return false;
    }
    
    public function changerStatut($id, $statut){
        $db = config::getConnexion();
        
        $sql = "UPDATE utilisateur SET statut='$statut' WHERE idUtilisateur=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        }
        return false;
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
    
    public function trouverParEmail($email){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM utilisateur WHERE email='$email'";
        
        $result = $db->query($sql);
        $user = $result->fetch();
        
        return $user;
    }
    
    public function sauvegarderTokenReset($email, $tokenHash, $expirySeconds = 3600){
        $db = config::getConnexion();
        
        $expires = date('Y-m-d H:i:s', time() + $expirySeconds);
        $now = date('Y-m-d H:i:s');
        
        $sql = "UPDATE utilisateur SET reset_token_hash='$tokenHash', reset_expires='$expires', reset_requested_at='$now' WHERE email='$email'";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        }
        return false;
    }
    
    public function verifierTokenReset($email, $tokenHash){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM utilisateur WHERE email='$email' AND reset_token_hash='$tokenHash'";
        
        $result = $db->query($sql);
        $user = $result->fetch();
        
        if ($user && $user['reset_expires']) {
            if (strtotime($user['reset_expires']) > time()) {
                return $user;
            }
        }
        
        return null;
    }
    
    public function mettreAJourMotDePasse($id, $nouveauMotDePasse){
        $db = config::getConnexion();
        
        $motDePasseHash = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);
        
        $sql = "UPDATE utilisateur SET motDePasse='$motDePasseHash', reset_token_hash=NULL, reset_expires=NULL, reset_requested_at=NULL WHERE idUtilisateur=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        }
        return false;
    }

    public function registerFace() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = $_POST['email'];
            $faceData = $_POST['face'];

            $ch = curl_init('http://localhost:5000/register');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username'=>$email,'face'=>$faceData]));
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if($result['success']){
                $_SESSION['message'] = "Face registration successful!";
                header("Location: sign-in.php");
            } else {
                $_SESSION['error'] = $result['message'];
                header("Location: sign-in.php?error=face_registration");
            }
        }
    }

    public function loginFace() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $faceData = $_POST['face'];

            $ch = curl_init('http://localhost:5000/login');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['face'=>$faceData]));
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if($result['success']){
                $user = $this->trouverParEmail($result['email']);
                if($user){
                    session_start();
                    $_SESSION['user_id'] = $user['idUtilisateur'];
                    $_SESSION['user_name'] = $user['nom'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    header("Location: index.php");
                } else {
                    header("Location: sign-in.php?error=invalid");
                }
            } else {
                header("Location: sign-in.php?error=face_not_recognized");
            }
        }
    }
    
    public function verifierEmail($email) {
        $db = config::getConnexion();
        
        $sql = "UPDATE utilisateur SET verified = 1 WHERE email='$email'";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        }
        return false;
    }
    
    public function estVerifie($email) {
        $db = config::getConnexion();
        
        $sql = "SELECT verified FROM utilisateur WHERE email='$email'";
        
        $result = $db->query($sql);
        $data = $result->fetch();
        
        return $data && $data['verified'] == 1;
    }
        public function verifierCompte($email){
            $db = config::getConnexion();
            
            $sql = $db->prepare("UPDATE utilisateur SET verified = 1 WHERE email = :mail");
            $sql->bindValue(':mail', $email);
            $result = $sql->execute();
            
            if ($result) {
                return true;
            }
            return false;
        }


}
?>
