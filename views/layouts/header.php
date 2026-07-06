<?php
/**
 * Layout: Phần đầu trang HTML chung (header.php)
 * Chức năng: Khởi động session nếu chưa có, định nghĩa BASE_PATH động, nhúng các thư viện CSS (Bootstrap 5, FontAwesome) và cấu hình thẻ head.
 */
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

/**
 * Hàm hỗ trợ hiển thị Markdown dạng tối giản (in đậm, in nghiêng, gạch ngang, danh sách bullet) an toàn XSS.
 */
function renderMarkdown($text) {
    if (empty($text)) return '';
    $escaped = htmlspecialchars($text);
    
    // Parse Bold: **text** -> <strong>text</strong>
    $escaped = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $escaped);
    // Parse Italic: *text* -> <em>text</em>
    $escaped = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $escaped);
    // Parse Strikethrough: ~~text~~ -> <del>text</del>
    $escaped = preg_replace('/~~(.*?)~~/', '<del>$1</del>', $escaped);
    
    // Parse Bullet Lists: line starting with "- " or "* " -> <li>
    $lines = explode("\n", $escaped);
    $inList = false;
    $result = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (strpos($trimmed, '- ') === 0 || strpos($trimmed, '* ') === 0) {
            if (!$inList) {
                $result[] = '<ul class="mb-2 ps-3">';
                $inList = true;
            }
            $content = substr($trimmed, 2);
            $result[] = '<li>' . $content . '</li>';
        } else {
            if ($inList) {
                $result[] = '</ul>';
                $inList = false;
            }
            $result[] = $line;
        }
    }
    if ($inList) {
        $result[] = '</ul>';
    }
    
    return implode("\n", $result);
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
