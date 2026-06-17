<?php
// test_auth.php
require_once __DIR__ . '/controllers/AuthController.php';

// Bắt đầu session cho mục đích test (nếu chưa có)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authController = new AuthController();

echo "<h1>Test Auth Module</h1>";

// 1. Kiểm tra session hiện tại
echo "<h2>1. Current Session State</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// 2. Kiểm tra getCurrentUser()
echo "<h2>2. Get Current User</h2>";
$currentUser = $authController->getCurrentUser();
if ($currentUser) {
    echo "<p>User is logged in:</p>";
    echo "<pre>";
    print_r($currentUser);
    echo "</pre>";
} else {
    echo "<p>No user is currently logged in.</p>";
}

// 3. Test form giả lập POST
echo "<h2>3. Simulate Login / Logout</h2>";
echo '
<form method="POST" action="/index.php?controller=auth&action=authenticate">
    <input type="text" name="login_id" placeholder="Username or Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
';
echo '<br>';
echo '<a href="/index.php?controller=auth&action=logout">Logout</a>';

echo "<p>To test this properly, make sure index.php is routing correctly, or replace the action URL above with your test script if not using full routing yet.</p>";

// Lấy danh sách users có sẵn để test
require_once __DIR__ . '/models/User.php';
$userModel = new User();
// Do đây là hàm test nên ta dùng query trực tiếp để show danh sách users
try {
    $stmt = $userModel->getConnection()->query("SELECT username, email FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($users) {
        echo "<h3>Available Users in DB:</h3>";
        echo "<ul>";
        foreach ($users as $u) {
            echo "<li>" . htmlspecialchars($u['username']) . " / " . htmlspecialchars($u['email']) . " (password depends on what was set in DB, often 'password123' if seeded)</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "Could not fetch users: " . $e->getMessage();
}

?>
