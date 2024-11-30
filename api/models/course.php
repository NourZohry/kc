<?php
class Course {
    private $conn;
    private $table_name = "courses";

    public $course_id;
    public $title;
    public $description;
    public $image_preview;
    public $category_id;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure all fields are returned, even if they're NULL or 0
        return $courses;
    }

    public function getById($course_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE course_id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":course_id", $course_id);
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ensure all fields are returned, even if they're NULL or 0
        return $course;
    }
}
?>
