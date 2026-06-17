<?php
require_once __DIR__ . '/../../core/Auth.php';
requireRole('editor');
?>
<!DOCTYPE html>
<html>
<head><title>Editor Dashboard</title></head>
<body>
    <h1>Welcome to Editor Dashboard</h1>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <a href="/index.php?controller=auth&action=logout">Logout</a>
</body>
</html>
