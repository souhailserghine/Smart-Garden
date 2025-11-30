<?php
// view/Backend/app/controllers/CategoryController.php

include_once __DIR__ . '/../core/config.php';

class CategoryController
{
    // Récupérer toutes les catégories
    public function getAllCategories()
    {
        $db = config::getConnexion();
        try {
            $stmt = $db->query("SELECT * FROM categorie ORDER BY nom_categorie ASC");
            $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "data" => $cats]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // Ajouter une catégorie
    public function addCategory()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $nom = $input['nom'] ?? null;
        $description = $input['description'] ?? '';

        if (!$nom) {
            echo json_encode(["status" => "error", "message" => "Nom requis"]);
            return;
        }

        $db = config::getConnexion();
        try {
            $stmt = $db->prepare("INSERT INTO categorie (nom_categorie, description) VALUES (?, ?)");
            $stmt->execute([$nom, $description]);
            echo json_encode(["status" => "success", "message" => "Catégorie ajoutée"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // Récupérer une catégorie
    public function getOneCategory()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id_categorie'] ?? null;

        if (!$id || !is_numeric($id)) {
            echo json_encode(["status" => "error", "message" => "ID invalide"]);
            return;
        }

        $db = config::getConnexion();
        try {
            $stmt = $db->prepare("SELECT * FROM categorie WHERE id_categorie = ?");
            $stmt->execute([$id]);
            $cat = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cat) {
                echo json_encode(["status" => "success", "data" => $cat]);
            } else {
                echo json_encode(["status" => "error", "message" => "Catégorie introuvable"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // Supprimer une catégorie
    public function deleteCategory()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id_categorie'] ?? null;

        if (!$id || !is_numeric($id)) {
            echo json_encode(["status" => "error", "message" => "ID manquant"]);
            return;
        }

        $db = config::getConnexion();
        try {
            $stmt = $db->prepare("DELETE FROM categorie WHERE id_categorie = ?");
            $success = $stmt->execute([$id]);
            echo json_encode([
                "status" => $success ? "success" : "error",
                "message" => $success ? "Catégorie supprimée" : "Erreur suppression"
            ]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // Mettre à jour une catégorie
    public function updateCategory()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id_categorie'] ?? null;
        $nom = $input['nom'] ?? null;
        $description = $input['description'] ?? null;

        if (!$id || !$nom) {
            echo json_encode(["status" => "error", "message" => "Données manquantes"]);
            return;
        }

        $db = config::getConnexion();
        try {
            $stmt = $db->prepare("UPDATE categorie SET nom_categorie = ?, description = ? WHERE id_categorie = ?");
            $success = $stmt->execute([$nom, $description, $id]);
            echo json_encode([
                "status" => $success ? "success" : "error",
                "message" => $success ? "Catégorie modifiée" : "Erreur modification"
            ]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
?>