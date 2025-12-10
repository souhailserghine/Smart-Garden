<?php
include  ("../../config.php");
include  '../../model/publication.php';


class PublicationC {
    public function ajouterLike($idPublication) {
    $sql = "UPDATE publication SET nbLikes = nbLikes + 1 WHERE idPublication = :idPublication";
    $db = config::getConnexion();
    $req = $db->prepare($sql);
    $req->bindValue(':idPublication', $idPublication);
    return $req->execute();
}

    public function addPublication($publication){
        $db = config::getConnexion();
        try {
            $req = $db->prepare("
                INSERT INTO publication (contenuTexte, datePublication, nbLikes, idUtilisateur)
                VALUES (:contenuTexte, NOW(), :nbLikes, :idUtilisateur)
            ");

            $req->execute([
                'contenuTexte' => $publication->getContenuTexte(),
                'nbLikes'      => $publication->getNbLikes(),
                'idUtilisateur'=> $publication->getIdUtilisateur()
            ]);

            return $db->lastInsertId();

        } catch(Exception $e){
            error_log("Erreur: " . $e->getMessage());
            return false;
        }
    }

    public function deletePublication($id){
        $db = config::getConnexion();
        try {
            $req = $db->prepare("DELETE FROM publication WHERE idPublication = ?");
            return $req->execute([$id]);
        } catch(Exception $e){
            error_log("Erreur: " . $e->getMessage());
            return false;
        }
    }

    public function updatePublication($id, $publication){
        $db = config::getConnexion();
        try {
            $req = $db->prepare("
                UPDATE publication 
                SET contenuTexte = ?, nbLikes = ?
                WHERE idPublication = ?
            ");

            return $req->execute([
                $publication->getContenuTexte(),
                $publication->getNbLikes(),
                $id
            ]);

        } catch(Exception $e){
            error_log("Erreur: " . $e->getMessage());
            return false;
        }
    }

    public function listePublications(){
        $db = config::getConnexion();
        $req = $db->query("SELECT * FROM publication ORDER BY datePublication DESC");
        return $req->fetchAll();
    }

    public function getPublication($id){
        $db = config::getConnexion();
        $req = $db->prepare("SELECT * FROM publication WHERE idPublication = ?");
        $req->execute([$id]);
        $data = $req->fetch();

        if ($data) {
            $publication = new Publication(
                $data['contenuTexte'],
                $data['idUtilisateur'],
                $data['nbLikes']
            );

            $publication->setIdPublication($data['idPublication']);
            return $publication;
        }
        return null;
    }
}
?>