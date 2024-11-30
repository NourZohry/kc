<?php
$servername = "database.cc.localhost"; // MySQL hostname (from docker-compose)
$username = "test_user";              // MySQL username
$password = "test_password";          // MySQL password
$dbname = "course_catalog";           // MySQL database name

try {
    // Create a PDO instance (use 'mysql' as the driver)
    $dsn = "mysql:host=$servername;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
