<?php

require_once __DIR__ . '/../model/Category.php';

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function getAllCategories()
    {
        header('Content-Type: application/json');
        try {
            $categories = $this->categoryModel->getAll();
            echo json_encode(['status' => 'success', 'data' => $categories]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getCategory($id)
    {
        header('Content-Type: application/json');
        try {
            $category = $this->categoryModel->getById($id);
            if ($category) {
                echo json_encode(['status' => 'success', 'data' => $category]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Category not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function createCategory()
    {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['nom_categorie'])) {
                echo json_encode(['status' => 'error', 'message' => 'Category name is required']);
                return;
            }

            $result = $this->categoryModel->create($data);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Category created successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create category']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateCategory($id)
    {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->categoryModel->update($id, $data);
            
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Category updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update category']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteCategory($id)
    {
        header('Content-Type: application/json');
        try {
            $result = $this->categoryModel->delete($id);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete category']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getCategoriesWithCount()
    {
        header('Content-Type: application/json');
        try {
            $categories = $this->categoryModel->getWithEventCount();
            echo json_encode(['status' => 'success', 'data' => $categories]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
