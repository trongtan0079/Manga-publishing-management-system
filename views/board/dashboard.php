<?php
require_once __DIR__ . '/../../core/Auth.php';
requireRole('board');
?>
<!DOCTYPE html>
<html>
<head><title>Board Dashboard</title></head>
<body>
    <h1>Welcome to Board Dashboard</h1>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <a href="/index.php?controller=auth&action=logout">Logout</a>
</body>
</html>
