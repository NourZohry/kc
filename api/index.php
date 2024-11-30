<?php
// Include db.php to establish a connection, handled by CategoryController
require_once 'config/db.php';

header('Content-Type: application/json');
// Allow from any origin
header("Access-Control-Allow-Origin: *");

// Allow specific methods (if needed)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// Allow specific headers (if needed)
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

try {
    // Determine the API endpoint
    $endpoint = $_GET['endpoint'] ?? 'categories';

    // Check if the endpoint corresponds to a Category or Course endpoint
    if ($endpoint === 'categories') {
        // Include the CategoryController
        require_once 'controllers/CategoryController.php';  // Correct the path if necessary

        // Instantiate CategoryController
        $categoryController = new CategoryController();

        // Handle the different operations
        $id = $_GET['id'] ?? null;
        if ($id) {
            $categoryController->getCategoryById($id);
        } else {
            $categoryController->getCategories();
        }
    } elseif ($endpoint === 'courses') {
        // Include the CourseController
        require_once 'controllers/CourseController.php';  // Correct the path if necessary

        // Instantiate CourseController
        $courseController = new CourseController();

        // Handle the different operations
        $id = $_GET['id'] ?? null;
        if ($id) {
            $courseController->getCourseById($id);
        } else {
            $courseController->getCourses();
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
