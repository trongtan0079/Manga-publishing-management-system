<?php
// database/run_migration.php
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->connect();
    
    if (!$conn) {
        throw new Exception("Không thể kết nối đến cơ sở dữ liệu.");
    }
    
    $sqlFile = __DIR__ . '/create_board_votes_table.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Không tìm thấy file SQL: {$sqlFile}");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Thực thi toàn bộ lệnh SQL trong file
    $conn->exec($sql);
    
    echo "Di trú Database thành công! Đã tạo bảng board_votes và seed dữ liệu mẫu.\n";
} catch (Exception $e) {
    echo "Lỗi khi chạy di trú: " . $e->getMessage() . "\n";
    exit(1);
}
