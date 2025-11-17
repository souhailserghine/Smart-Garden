<?php
include '../../config.php';
//include '../../View/Back Office/list.php';

class planteC {

    public function ajouterPlante($plante) {
        $db = config::getConnexion();

        try {  
            // INSERT corrigé
            $req = $db->prepare('
                INSERT INTO plante (nom_plante, date_ajout, niveau_humidite, besoin_eau, etat_sante)
                VALUES (:nom, :date, :niveau_humidite, :besoin_eau, :etat_sante)
            ');

            $req->execute([
                'nom' => $plante->getNomPlante(),
                'date' => $plante->getDateAjout(),
                'niveau_humidite' => $plante->getNiveauHumidite(),
                'besoin_eau' => $plante->getBesoinEau(),
                'etat_sante' => $plante->getEtatSante()
            ]);
        } catch (Exception $e) {
            echo 'Erreur: '.$e->getMessage();
        }
    }

    public function listPlantes(){
        $db = config::getConnexion();

        try {
            $req = $db->query('SELECT * FROM plante');
            $plantes = $req->fetchAll();
            return $plantes;
        } catch (Exception $e) {
            echo 'Erreur: '.$e->getMessage();
        }
    }

    public function modifierPlante($plante, $id){
    $db = config::getConnexion();

    try {
        $req = $db->prepare('
            UPDATE plante
            SET nom_plante = :nom, 
                date_ajout = :date, 
                niveau_humidite = :niveau_humidite, 
                besoin_eau = :besoin_eau,
                etat_sante = :etat_sante,
                idUtilisateur = :idUtilisateur
            WHERE id_plante = :id
        ');

        $req->execute([
            'nom' => $plante->getNomPlante(),
            'date' => $plante->getDateAjout(),
            'niveau_humidite' => $plante->getNiveauHumidite(),
            'besoin_eau' => $plante->getBesoinEau(),
            'etat_sante' => $plante->getEtatSante(),
            'idUtilisateur' => $plante->getIdUtilisateur(),
            'id' => $id
        ]);

    } catch (Exception $e) {
        echo 'Erreur: '.$e->getMessage();
    }
}


   public function supprimerPlante($id){
    $db = config::getConnexion();

    try {
        $req = $db->prepare('DELETE FROM plante WHERE id_plante = :id');
        $req->execute([
            'id' => $id
        ]);
    } catch (Exception $e) {
        echo 'Erreur: '.$e->getMessage();
    }
}

public function getPlanteById($id){
    $db = config::getConnexion();
    try {
        $req = $db->prepare("SELECT * FROM plante WHERE id_plante = :id");
        $req->bindValue(':id', $id);
        $req->execute();
        return $req->fetch();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

public function listPlantesByUser($idUtilisateur){
    $db = config::getConnexion();
    try {
        $req = $db->prepare('SELECT * FROM plante WHERE idUtilisateur = :idUtilisateur');
        $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll();
    } catch (Exception $e) {
        echo 'Erreur : '.$e->getMessage();
    }
}



}
?>