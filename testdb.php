<?php

// Script kiểm thử kết nối cơ sở dữ liệu MySQL qua PDO cổng 3307
try {
    $pdo = new PDO(
        "mysql:host=localhost;port=3307;dbname=manga_workflow;charset=utf8mb4",
        "root",
        ""
    );

    echo "Connected successfully to manga_workflow at port 3307!";
} catch (PDOException $e) {
    // Hiển thị thông báo nếu kết nối thất bại
    echo "Connection failed: " . $e->getMessage();
}

