<?php
require_once __DIR__ . '/../config/database.php';
try {
    $db = new Database();
    $conn = $db->connect();
    
    $conn->exec("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'in_progress', 'submitted', 'completed', 'rejected') DEFAULT 'pending'");
    echo "Altered tasks table.\n";
    
    $conn->exec("ALTER TABLE page_regions MODIFY COLUMN status ENUM('pending', 'in_progress', 'submitted', 'completed', 'rejected') DEFAULT 'pending'");
    echo "Altered page_regions table.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
