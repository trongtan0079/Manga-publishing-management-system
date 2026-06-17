<?php
// Hiển thị tất cả lỗi để dễ dàng debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Kiểm tra kết nối Database và Models</h1>";

try {
    // 1. Kiểm tra kết nối Database
    echo "<h2>1. Khởi tạo Database</h2>";
    require_once __DIR__ . '/config/database.php';
    $db = new Database();
    $conn = $db->connect();
    
    if ($conn) {
        echo "<p style='color: green;'>Kết nối Database thành công!</p>";
    } else {
        echo "<p style='color: red;'>Kết nối Database thất bại!</p>";
    }

    // 2. Kiểm tra Model User
    echo "<h2>2. Kiểm tra User Model</h2>";
    require_once __DIR__ . '/models/User.php';
    $userModel = new User();
    
    $users = $userModel->findAll();
    echo "<p style='color: green;'>Require file User.php và gọi findAll() thành công.</p>";
    echo "Kết quả User::findAll(): ";
    echo "<pre>";
    print_r($users); // Sẽ trả về mảng rỗng nếu chưa có data, quan trọng là không bị lỗi SQL
    echo "</pre>";

    // 3. Kiểm tra Model Series
    echo "<h2>3. Kiểm tra Series Model</h2>";
    require_once __DIR__ . '/models/Series.php';
    $seriesModel = new Series();
    
    $series = $seriesModel->findAll();
    echo "<p style='color: green;'>Require file Series.php và gọi findAll() thành công.</p>";
    echo "Kết quả Series::findAll(): ";
    echo "<pre>";
    print_r($series);
    echo "</pre>";

    echo "<h2 style='color: blue;'>=> Toàn bộ cấu trúc Base Model và các Entity Models đã hoạt động tốt, không có lỗi require_once hay PDO!</h2>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Có lỗi xảy ra: " . $e->getMessage() . "</p>";
}
