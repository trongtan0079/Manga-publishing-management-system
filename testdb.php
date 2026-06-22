<?php

try {
    $pdo = new PDO(
        "mysql:host=localhost;port=3307;dbname=manga_workflow;charset=utf8mb4",
        "root",
        ""
    );

    echo "Connected successfully to manga_workflow at port 3307!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
