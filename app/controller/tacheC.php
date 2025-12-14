<?php
require_once __DIR__ . '/../config.php';

class tacheC {

   
    public function ajouterTache($tache) {
        $db = config::getConnexion();

        
        try { 
            $req = $db->prepare('
                INSERT INTO tache (type_dosage, quantite, mode_dosage, date_dosage, derniereExecution, prochaineExecution, estComplete, priorite, id_plante)
                VALUES (:type, :qte, :mode_d, :date_d, :derniere_e, :prochaine_e, :complete, :priorite_t, :id_plante_t)
            ');

            $req->execute([
                'type' => $tache->getTypeDosage(),
                'qte' => $tache->getQuantite(),
                'mode_d' => $tache->getModeDosage(),
                'date_d' => $tache->getDateDosage(),
                'derniere_e' => $tache->getDerniereExecution(),
                'prochaine_e' => $tache->getProchaineExecution(),
                'complete' => $tache->getEstComplete(),
                'priorite_t' => $tache->getPriorite(),
                'id_plante_t' => $tache->getIdPlante() 
            ]);
        } catch (Exception $e) {
            echo 'Erreur lors de l\'ajout de la tâche: '.$e->getMessage();
        }
    }

    
    public function listTaches(){
        $db = config::getConnexion();

        try {
            $req = $db->query('SELECT * FROM tache');
            $taches = $req->fetchAll(PDO::FETCH_ASSOC); // Utilise FETCH_ASSOC pour retourner un tableau associatif
            return $taches;
        } catch (Exception $e) {
            echo 'Erreur lors de la liste des tâches: '.$e->getMessage();
        }
    }

  
    public function modifierTache($tache, $id){
        $db = config::getConnexion();

        try {
            $req = $db->prepare('
                UPDATE tache
                SET type_dosage = :type, 
                    quantite = :qte, 
                    mode_dosage = :mode_d, 
                    date_dosage = :date_d, 
                    derniereExecution = :derniere_e,
                    prochaineExecution = :prochaine_e,
                    estComplete = :complete,
                    priorite = :priorite_t,
                    id_plante = :id_plante_t
                WHERE id_dosage = :id
            ');

            $req->execute([
                'type' => $tache->getTypeDosage(),
                'qte' => $tache->getQuantite(),
                'mode_d' => $tache->getModeDosage(),
                'date_d' => $tache->getDateDosage(),
                'derniere_e' => $tache->getDerniereExecution(),
                'prochaine_e' => $tache->getProchaineExecution(),
                'complete' => $tache->getEstComplete(),
                'priorite_t' => $tache->getPriorite(),
                'id_plante_t' => $tache->getIdPlante(),
                'id' => $id
            ]);

            return ($req->rowCount() > 0); 

        } catch (Exception $e) {
            echo 'Erreur lors de la modification de la tâche: '.$e->getMessage();
            return false;
        }
    }

 
    public function supprimerTache($id){
        $db = config::getConnexion();

        try {
            $req = $db->prepare('DELETE FROM tache WHERE id_dosage = :id');
            $req->execute([
                'id' => $id
            ]);
        } catch (Exception $e) {
            echo 'Erreur lors de la suppression de la tâche: '.$e->getMessage();
        }
    }

   
    public function getTacheById($id){
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM tache WHERE id_dosage = :id");
            $req->bindValue(':id', $id, PDO::PARAM_INT);
            $req->execute();
            return $req->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Erreur lors de la récupération de la tâche : " . $e->getMessage();
        }
    }

    
    public function listTachesByPlante($idPlante){
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM tache WHERE id_plante = :idPlante ORDER BY prochaineExecution ASC');
            $req->bindValue(':idPlante', $idPlante, PDO::PARAM_INT);
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur lors de la liste des tâches par plante : '.$e->getMessage();
        }
    }


public function getTachesByPlante($id_plante){
    $db = config::getConnexion();
    try {
        $query = $db->prepare("SELECT * FROM tache WHERE id_plante = :id_plante");
        $query->bindValue(':id_plante', $id_plante, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo 'Erreur : '.$e->getMessage();
        return [];
    }
}
public function getTachesByUser($id_user){
    $db = config::getConnexion();
    try {
        $query = $db->prepare("
            SELECT t.*, p.nom_plante 
            FROM tache t
            INNER JOIN plante p ON t.id_plante = p.id_plante
            WHERE p.id_user = :id_user
            ORDER BY t.prochaineExecution ASC
        ");
        $query->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo 'Erreur : '.$e->getMessage();
        return [];
    }
}

}
/*public function listTachesAvecPlante() {
    $db = config::getConnexion();

    try {
        $req = $db->query('
            SELECT t.*, p.nom_plante, p.type_plante
            FROM tache t
            JOIN plante p ON t.id_plante = p.id_plante
            ORDER BY t.prochaineExecution ASC
        ');

        return $req->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        echo 'Erreur lors de la jointure : ' . $e->getMessage();
    }
}
*/
?>