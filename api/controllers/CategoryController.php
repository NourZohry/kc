<?php
require_once 'config/db.php';  // Include the shared database connection
require_once 'models/category.php';  // Include the course model

class CategoryController {
    private $db;

    public function __construct() {
        global $pdo;  // Use the global $pdo object from db.php
        $this->db = $pdo;
    }

    public function getCategories() {
        $category = new Category($this->db);
        $categories = $category->getAll();  // Directly fetch data from the model
        echo json_encode($categories);
    }

    public function getCategoryById($id) {
        $category = new Category($this->db);
        $category_data = $category->getById($id);  // Directly fetch data from the model
        if ($category_data) {
            // Return 200 OK if the course is found
            http_response_code(200);
            echo json_encode($category_data);
        } else {
            // Return 404 Not Found if the course does not exist
            http_response_code(404);
            echo json_encode(['error' => 'Category not found']);
        }
    }
}
?>