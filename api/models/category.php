<?php
class Category {
    private $conn;
    private $table_name = "categories";

    public $id;
    public $name;
    public $description;
    public $parent_id;
    public $count_of_courses;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "
            SELECT 
                c.*, 
                COALESCE((SELECT COUNT(*) FROM courses WHERE category_id = c.id), 0) AS count_of_courses
            FROM " . $this->table_name . " c
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Optionally, rename the 'parent' field to 'parent_id' and ensure all fields are included
        foreach ($categories as &$category) {
            $category['parent_id'] = $category['parent'];
            unset($category['parent']);
        }
        

        return $categories;
    }

    public function getById($id) {
        $query = "
        SELECT 
            c.*, 
            COUNT(co.course_id) AS count_of_courses
        FROM " . $this->table_name . " c
        LEFT JOIN courses co ON co.category_id = c.id
        WHERE c.id = :id
        GROUP BY c.id
        LIMIT 1
    ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        // Rename 'parent' to 'parent_id'
        // if (isset($category['parent'])) {
            $category['parent_id'] = $category['parent'];
            unset($category['parent']);
        // }

        return $category;
    }
}

?>
