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

    public function addPublication($publication) {
    try {
        $db = config::getConnexion();
        
        // MODIFIER LA REQUÊTE POUR INCLURE datePublication
        $sql = "INSERT INTO publication (contenuTexte, datePublication, idUtilisateur, nbLikes, images, videos) 
                VALUES (:contenuTexte, :datePublication, :idUtilisateur, :nbLikes, :images, :videos)";
        
        $query = $db->prepare($sql);
        
        // MODIFIER L'EXECUTION POUR INCLURE LA DATE
        $query->execute([
            'contenuTexte' => $publication->getContenuTexte(),
            'datePublication' => $publication->getDatePublication(), // AJOUTER CETTE LIGNE
            'idUtilisateur' => $publication->getIdUtilisateur(),
            'nbLikes' => $publication->getNbLikes(),
            'images' => $publication->getImages(),
            'videos' => $publication->getVideos()
        ]);
        
        return true;
    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
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

    public function listePublicationsTrieesParLikes() {
    try {
        $sql = "SELECT * FROM publication ORDER BY nbLikes DESC, datePublication DESC";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    } catch (PDOException $e) {
        die('Erreur: ' . $e->getMessage());
    }
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