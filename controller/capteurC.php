<?php

include 'C:/xampp/htdocs/TacheRayen/config.php';

class CapteurC {

    // Ajouter un capteur
    public function addCapteur($capteur) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('INSERT INTO capteur (etatCapteur, uniteCapteur, emplacement, dateInstallation, id_categorie, id_plante) 
                                 VALUES (:etat, :unit, :emp, :date, :id_cat, :id_plante)');

            $result = $req->execute([
                'etat' => $capteur->getEtatCapteur(),
                'unit' => $capteur->getUniteCapteur(),
                'emp' => $capteur->getEmplacement(),
                'date' => $capteur->getDateInstallation(),
                'id_cat' => $capteur->getIdCategorie(),
                'id_plante' => $capteur->getIdPlante()
            ]);

            return $result;

        } catch (Exception $e) {
            error_log('Erreur addCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher tous les capteurs
    public function showCapteur() {
        $db = config::getConnexion();

        try {
            $liste = $db->query('SELECT * FROM capteur ORDER BY id_capteur');
            return $liste->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur showCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher tous les capteurs avec leurs catégories et plantes (JOIN)
    public function showCapteurWithDetails() {
        $db = config::getConnexion();

        try {
            $query = 'SELECT c.*, 
                             cat.nom_categorie, 
                             p.nom_plante 
                      FROM capteur c
                      LEFT JOIN categorie cat ON c.id_categorie = cat.id_categorie
                      LEFT JOIN plante p ON c.id_plante = p.id_plante
                      ORDER BY c.id_capteur';
            
            $liste = $db->query($query);
            return $liste->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur showCapteurWithDetails: ' . $e->getMessage());
            throw $e;
        }
    }

    // Afficher les capteurs filtrés par catégorie
    public function showCapteurByCategorie($id_categorie) {
        $db = config::getConnexion();

        try {
            $query = 'SELECT c.*, 
                             cat.nom_categorie, 
                             p.nom_plante 
                      FROM capteur c
                      LEFT JOIN categorie cat ON c.id_categorie = cat.id_categorie
                      LEFT JOIN plante p ON c.id_plante = p.id_plante
                      WHERE c.id_categorie = :id_categorie
                      ORDER BY c.id_capteur';
            
            $req = $db->prepare($query);
            $req->execute(['id_categorie' => $id_categorie]);
            return $req->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur showCapteurByCategorie: ' . $e->getMessage());
            throw $e;
        }
    }

    // Supprimer un capteur
    public function deleteCapteur($id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('DELETE FROM capteur WHERE id_capteur = :id');
            $result = $req->execute(['id' => $id]);
            
            return $req->rowCount();
            
        } catch (Exception $e) {
            error_log('Erreur deleteCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Récupérer un capteur par ID
    public function getCapteur($id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('SELECT * FROM capteur WHERE id_capteur = :id');
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) {
            error_log('Erreur getCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Modifier un capteur
    public function updateCapteur($capteur, $id) {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('UPDATE capteur 
                                 SET etatCapteur = :etat, 
                                     uniteCapteur = :unit, 
                                     emplacement = :emp, 
                                     dateInstallation = :date,
                                     id_categorie = :id_cat,
                                     id_plante = :id_plante
                                 WHERE id_capteur = :id');
            
            $result = $req->execute([
                'id' => $id,
                'etat' => $capteur->getEtatCapteur(),
                'unit' => $capteur->getUniteCapteur(),
                'emp' => $capteur->getEmplacement(),
                'date' => $capteur->getDateInstallation(),
                'id_cat' => $capteur->getIdCategorie(),
                'id_plante' => $capteur->getIdPlante()
            ]);

            return $req->rowCount();
            
        } catch (Exception $e) {
            error_log('Erreur updateCapteur: ' . $e->getMessage());
            throw $e;
        }
    }

    // Récupérer toutes les catégories
    public function getAllCategories() {
        $db = config::getConnexion();

        try {
            $req = $db->query('SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie');
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getAllCategories: ' . $e->getMessage());
            throw $e;
        }
    }

    // Récupérer toutes les plantes
    public function getAllPlantes() {
        $db = config::getConnexion();

        try {
            $req = $db->query('SELECT id_plante, nom_plante FROM plante ORDER BY nom_plante');
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getAllPlantes: ' . $e->getMessage());
            throw $e;
        }
    }

}

?>