<?php
include_once ("../../config.php");
include_once ("../../model/Commentaire.php");

class CommentaireC {
    public function ajouterCommentaire($commentaire) {
        $sql = "INSERT INTO commentaire (contenuCommentaire, dateCommentaire, idPublication, idUtilisateur) 
                VALUES (:contenuCommentaire, :dateCommentaire, :idPublication, :idUtilisateur)";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'contenuCommentaire' => $commentaire->getContenuCommentaire(),
                'dateCommentaire' => $commentaire->getDateCommentaire(),
                'idPublication' => $commentaire->getIdPublication(),
                'idUtilisateur' => $commentaire->getIdUtilisateur()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'ajout du commentaire: " . $e->getMessage());
        }
    }

    public function getCommentairesByPublication($idPublication) {
    $sql = "SELECT c.*, u.nom
            FROM commentaire c 
            JOIN utilisateur u ON c.idUtilisateur = u.idUtilisateur 
            WHERE c.idPublication = :idPublication 
            ORDER BY c.dateCommentaire ASC";
    
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute(['idPublication' => $idPublication]);
        $data = $query->fetchAll();
        
        $commentaires = [];
        foreach ($data as $row) {
            $commentaires[] = new Commentaire(
                $row['idCommentaire'],
                $row['contenuCommentaire'],
                $row['dateCommentaire'],
                $row['idPublication'],
                $row['idUtilisateur'],
                $row['nom'],    // Nom de l'utilisateur
                ''              // Prénom vide
            );
        }
        return $commentaires;
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la récupération des commentaires: " . $e->getMessage());
    }
}

    public function getNombreCommentaires($idPublication) {
        $sql = "SELECT COUNT(*) as nbCommentaires FROM commentaire WHERE idPublication = :idPublication";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['idPublication' => $idPublication]);
            $result = $query->fetch();
            return $result['nbCommentaires'];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function modifierCommentaire($idCommentaire, $nouveauContenu) {
        try {
            $sql = "UPDATE commentaire SET contenuCommentaire = :contenu WHERE idCommentaire = :id";
            $db = config::getConnexion();
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':contenu' => $nouveauContenu,
                ':id' => $idCommentaire
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la modification: " . $e->getMessage());
        }
    }

    public function supprimerCommentaire($idCommentaire) {
        try {
            $sql = "DELETE FROM commentaire WHERE idCommentaire = :id";
            $db = config::getConnexion();
            $stmt = $db->prepare($sql);
            return $stmt->execute([':id' => $idCommentaire]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression: " . $e->getMessage());
        }
    }

    // Méthode de validation des données
    public function validerDonneesCommentaire($data) {
        $errors = [];

        if (!isset($data['idPublication']) || !is_numeric($data['idPublication']) || $data['idPublication'] <= 0) {
            $errors[] = "ID publication invalide";
        }

        if (!isset($data['idUtilisateur']) || !is_numeric($data['idUtilisateur']) || $data['idUtilisateur'] <= 0) {
            $errors[] = "ID utilisateur invalide";
        }

        if (!isset($data['contenuCommentaire']) || empty(trim($data['contenuCommentaire']))) {
            $errors[] = "Le commentaire ne peut pas être vide";
        } elseif (strlen(trim($data['contenuCommentaire'])) > 500) {
            $errors[] = "Le commentaire ne peut pas dépasser 500 caractères";
        }

        return $errors;
    }
}
?>