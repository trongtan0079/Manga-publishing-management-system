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
 * Hàm hỗ trợ hiển thị HTML an toàn (chỉ cho phép các thẻ định dạng văn bản cơ bản).
 */
function safeHTML($html) {
    if (empty($html)) return '';
    // Cho phép các thẻ: p, strong, em, u, s, ul, ol, li, br, div, span, hr, h1-h6, i
    return strip_tags($html, '<p><strong><em><u><s><ul><ol><li><br><div><span><hr><h1><h2><h3><h4><h5><h6><i>');
}

/**
 * Hàm hỗ trợ hiển thị Markdown dạng tối giản (in đậm, in nghiêng, gạch ngang, danh sách bullet) hoặc HTML an toàn từ Quill.
 */
function renderMarkdown($text) {
    if (empty($text)) return '';
    
    // Nếu dữ liệu đã có định dạng HTML từ Quill (ví dụ có các thẻ p, strong, em...)
    if (preg_match('/<[a-z][\s\S]*>/i', $text)) {
        return safeHTML($text);
    }
    
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

/**
 * Trích xuất phần mô tả riêng của 1 phân vùng cụ thể từ chuỗi HTML mô tả nhóm (grouped task).
 * HTML gốc chứa nhiều div.region-instruction-card, mỗi div có text "Phân vùng #ID".
 * Hàm này trả về toàn bộ HTML của div card tương ứng với $regionId (giữ nguyên style).
 * Nếu không tìm thấy hoặc HTML không phải nhóm, trả về toàn bộ $fullHtml.
 */
function extractRegionDescription($fullHtml, $regionId) {
    if (empty($fullHtml) || empty($regionId)) return $fullHtml;
    
    // Kiểm tra nhanh xem có phải HTML nhóm không
    if (strpos($fullHtml, 'region-instruction-card') === false) {
        return $fullHtml;
    }
    
    // Dùng DOMDocument để parse HTML
    $dom = new \DOMDocument('1.0', 'UTF-8');
    // Suppress warnings cho HTML không hoàn chỉnh
    @$dom->loadHTML('<?xml encoding="UTF-8"><div>' . $fullHtml . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
    $xpath = new \DOMXPath($dom);
    // Tìm tất cả div có class "region-instruction-card"
    $cards = $xpath->query('//div[contains(@class, "region-instruction-card")]');
    
    foreach ($cards as $card) {
        // Kiểm tra xem card này có chứa text "Phân vùng #<regionId>" không
        $textContent = $card->textContent;
        if (preg_match('/Phân vùng\s*#\s*' . preg_quote($regionId, '/') . '(?!\d)/', $textContent)) {
            // Trả về toàn bộ HTML của card này (giữ nguyên div ngoài)
            return $dom->saveHTML($card);
        }
    }
    
    // Nếu không tìm thấy card cho region này, trả về toàn bộ
    return $fullHtml;
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
    <!-- Quill Rich Text Editor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_PATH ?>/assets/css/style.css?v=1.0.0" rel="stylesheet">
</head>
<body>
