<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    if ($pos !== false) {
        $basePath = substr($scriptName, 0, $pos);
    } else {
        $basePath = dirname($scriptName);
    }
    if ($basePath === '/' || $basePath === '\\') $basePath = '';
    define('BASE_PATH', str_replace('\\', '/', $basePath));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Bảng điều khiển' ?> - Nền Tảng Xuất Bản Manga</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_PATH ?>/assets/css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body>
