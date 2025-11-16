<?php

include 'C:/xampp/htdocs/TacheRayen/config.php';

class CategorieC {

    // Ajouter une catégorie
    public function addCategorie($categorie) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('
                INSERT INTO categorie (nom_categorie) 
                VALUES (:nom)
            ');

            return $req->execute([
                'nom' => $categorie->getNomCategorie()
            ]);

        } catch (Exception $e) {
            error_log('Erreur addCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher toutes les catégories
    public function showCategorie() {
        $db = config::getConnexion();

        try {
            $liste = $db->query('SELECT * FROM categorie ORDER BY id_categorie');
            return $liste->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Erreur showCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher catégories avec nombre de capteurs (JOIN)
    public function showCategorieWithDetails() {
        $db = config::getConnexion();

        try {
            $query = '
                SELECT cat.*, 
                       COUNT(c.id_capteur) AS nombre_capteurs
                FROM categorie cat
                LEFT JOIN capteur c ON cat.id_categorie = c.id_categorie
                GROUP BY cat.id_categorie
                ORDER BY cat.id_categorie
            ';

            $liste = $db->query($query);
            return $liste->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Erreur showCategorieWithDetails: ' . $e->getMessage());
            throw $e;
        }
    }

    // Supprimer une catégorie
    public function deleteCategorie($id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('DELETE FROM categorie WHERE id_categorie = :id');
            $req->execute(['id' => $id]);

            return $req->rowCount(); // nombre de lignes supprimées

        } catch (Exception $e) {
            error_log('Erreur deleteCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Récupérer une catégorie par ID
    public function getCategorie($id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('SELECT * FROM categorie WHERE id_categorie = :id');
            $req->execute(['id' => $id]);

            return $req->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Erreur getCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Modifier une catégorie
    public function updateCategorie($categorie, $id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('
                UPDATE categorie 
                SET nom_categorie = :nom
                WHERE id_categorie = :id
            ');

            $req->execute([
                'id' => $id,
                'nom' => $categorie->getNomCategorie()
            ]);

            return $req->rowCount();

        } catch (Exception $e) {
            error_log('Erreur updateCategorie: ' . $e->getMessage());
            throw $e;
        }
    }
}

?>
