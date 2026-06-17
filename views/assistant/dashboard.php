<?php
require_once __DIR__ . '/../../core/Auth.php';
requireRole('assistant');
?>
<!DOCTYPE html>
<html>
<head><title>Assistant Dashboard</title></head>
<body>
    <h1>Welcome to Assistant Dashboard</h1>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <a href="/index.php?controller=auth&action=logout">Logout</a>
</body>
</html>
