<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Historique.php';

class HistoriqueC{
    
    public function ajouterHistorique($dateConnexion, $dateDeconnexion, $tache, $duree, $idUtilisateur, $id_dosage){
        $db = config::getConnexion();
        
        $sql = "INSERT INTO historique (dateConnexion, dateDeconnexion, tache, duree, idUtilisateur, id_dosage) 
                VALUES ('$dateConnexion', '$dateDeconnexion', '$tache', '$duree', '$idUtilisateur', '$id_dosage')";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function modifierHistorique($id, $dateConnexion, $dateDeconnexion, $tache, $duree, $idUtilisateur, $id_dosage){
        $db = config::getConnexion();
        
        $sql = "UPDATE historique SET dateConnexion='$dateConnexion', dateDeconnexion='$dateDeconnexion', 
                tache='$tache', duree='$duree', idUtilisateur='$idUtilisateur', id_dosage='$id_dosage' 
                WHERE idHistorique=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function supprimerHistorique($id){
        $db = config::getConnexion();
        
        $sql = "DELETE FROM historique WHERE idHistorique=$id";
        
        $result = $db->query($sql);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function afficher(){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM historique ORDER BY dateConnexion DESC";
        
        $result = $db->query($sql);
        $historiques = $result->fetchAll();
        
        return $historiques;
    }

    public function afficherParId($id){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM historique WHERE idHistorique=$id";
        
        $result = $db->query($sql);
        $historique = $result->fetch();
        
        return $historique;
    }

    public function afficherParUtilisateur($idUtilisateur){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM historique WHERE idUtilisateur=$idUtilisateur ORDER BY dateConnexion DESC";
        
        $result = $db->query($sql);
        $historiques = $result->fetchAll();
        
        return $historiques;
    }

    public function afficherParTache($tache){
        $db = config::getConnexion();
        
        $sql = "SELECT * FROM historique WHERE tache='$tache' ORDER BY dateConnexion DESC";
        
        $result = $db->query($sql);
        $historiques = $result->fetchAll();
        
        return $historiques;
    }

    public function calculerDuree($dateConnexion, $dateDeconnexion){
        if ($dateConnexion && $dateDeconnexion) {
            $debut = strtotime($dateConnexion);
            $fin = strtotime($dateDeconnexion);
            $duree = ($fin - $debut) / 60;
            return $duree;
        }
        return 0;
    }

    public function afficherStatistiques($idUtilisateur){
        $db = config::getConnexion();
        
        $sql = "SELECT tache, COUNT(*) as nombre, SUM(duree) as duree_totale 
                FROM historique WHERE idUtilisateur=$idUtilisateur GROUP BY tache";
        
        $result = $db->query($sql);
        $stats = $result->fetchAll();
        
        return $stats;
    }
    
    public function compterHistoriques(){
        $db = config::getConnexion();
        
        $sql = "SELECT COUNT(*) as total FROM historique";
        
        $result = $db->query($sql);
        $data = $result->fetch();
        
        return $data['total'];
    }
    
    public function compterParTache($tache){
        $db = config::getConnexion();
        
        $sql = "SELECT COUNT(*) as total FROM historique WHERE tache='$tache'";
        
        $result = $db->query($sql);
        $data = $result->fetch();
        
        return $data['total'];
    }
    
    public function compterAujourdhui(){
        $db = config::getConnexion();
        
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total FROM historique WHERE DATE(dateConnexion)='$today'";
        
        $result = $db->query($sql);
        $data = $result->fetch();
        
        return $data['total'];
    }
}
?>