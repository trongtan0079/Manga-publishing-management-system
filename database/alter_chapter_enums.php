<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->connect();
    
    // Add new ENUMs
    $sql1 = "ALTER TABLE chapters MODIFY COLUMN status ENUM('drafting', 'drawing', 'reviewing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting'";
    $conn->exec($sql1);
    echo "Altered chapters table enum successfully.\n";

    // Update existing
    $sql2 = "UPDATE chapters SET status = 'reviewing_final' WHERE status = 'reviewing'";
    $stmt = $conn->prepare($sql2);
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "Updated {$count} chapters from reviewing to reviewing_final.\n";
    
    // Clean up
    $sql3 = "ALTER TABLE chapters MODIFY COLUMN status ENUM('drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting'";
    $conn->exec($sql3);
    echo "Cleaned up chapters table enum successfully.\n";
    
    // Do the same for pages
    $sql4 = "ALTER TABLE pages MODIFY COLUMN status ENUM('drafting', 'drawing', 'reviewing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting'";
    $conn->exec($sql4);
    echo "Altered pages table enum successfully.\n";

    $sql5 = "UPDATE pages SET status = 'reviewing_final' WHERE status = 'reviewing'";
    $stmt5 = $conn->prepare($sql5);
    $stmt5->execute();
    $count5 = $stmt5->rowCount();
    echo "Updated {$count5} pages from reviewing to reviewing_final.\n";
    
    $sql6 = "ALTER TABLE pages MODIFY COLUMN status ENUM('drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting'";
    $conn->exec($sql6);
    echo "Cleaned up pages table enum successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
