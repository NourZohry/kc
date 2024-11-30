<?php
require_once 'config/db.php';  // Include the shared database connection
require_once 'models/course.php';  // Include the course model

class CourseController {
    private $db;

    public function __construct() {
        global $pdo;  // Use the global $pdo object from db.php
        $this->db = $pdo;
    }

    public function getCourses() {
        $course = new Course($this->db);
        $courses = $course->getAll();  // Directly fetch all courses from the model
        echo json_encode($courses);
    }

    public function getCourseById($course_id) {
        $course = new Course($this->db);
        $course_data = $course->getById($course_id);  // Fetch course by ID
        if ($course_data) {
            // Return 200 OK if the course is found
            http_response_code(200);
            echo json_encode($course_data);
        } else {
            // Return 404 Not Found if the course does not exist
            http_response_code(404);
            echo json_encode(['error' => 'Course not found']);
        }
    }
}
?>
