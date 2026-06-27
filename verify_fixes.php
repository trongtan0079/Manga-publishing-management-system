<?php
$cookieFile = __DIR__ . '/cookie.txt';
$baseUrl = 'http://localhost:8000';
$dummyImg = 'C:\Users\Ngan\.gemini\antigravity-ide\brain\45eeb796-8df4-42d2-98f0-322b7c5b06a4\dummy_manga_page_1782549513576.png';

function request($url, $postData = null, $files = null, $method = 'POST') {
    global $cookieFile;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects so we can read headers
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    if ($postData !== null || $files !== null) {
        if ($method === 'POST') curl_setopt($ch, CURLOPT_POST, true);
        else curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        $data = [];
        if ($postData) {
            foreach ($postData as $k => $v) $data[$k] = $v;
        }
        if ($files) {
            foreach ($files as $k => $file) {
                $data[$k] = new CURLFile($file['path'], $file['mime'], $file['name']);
            }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $headerStr = substr($response, 0, $headerSize);
    $bodyStr = substr($response, $headerSize);
    
    return ['code' => $httpCode, 'headers' => $headerStr, 'body' => $bodyStr];
}

function requestGet($url) {
    global $cookieFile;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

echo "=== Verification 1: Delete Page ===\n";
// Login as Mangaka
request("$baseUrl/index.php?controller=auth&action=logout"); 
request("$baseUrl/index.php?controller=auth&action=authenticate", [
    'login_id' => 'mangaka_user',
    'password' => 'password123'
]);

// Create Page
$res = request("$baseUrl/index.php?controller=page&action=store", [
    'chapter_id' => 1,
    'page_number' => 9999,
    'status' => 'drafting'
], [
    'image' => ['path' => $dummyImg, 'mime' => 'image/png', 'name' => 'test_delete.png']
]);
// Get page id
$html = requestGet("$baseUrl/index.php?controller=chapter&action=show&id=1");
preg_match_all('/controller=page&action=show&id=(\d+)/', $html, $matches);
$pageId = end($matches[1]);
echo "Created Page ID: $pageId\n";

// Get file path from DB
require_once 'c:\xampp\htdocs\manga\config\database.php';
$db = (new Database())->connect();
$stmt = $db->prepare("SELECT image_url FROM pages WHERE page_id = ?");
$stmt->execute([$pageId]);
$imageUrl = $stmt->fetchColumn();
$physicalPath = __DIR__ . '/../../../../../../xampp/htdocs/manga/' . ltrim($imageUrl, '/');
echo "File created: " . (file_exists($physicalPath) ? "YES" : "NO") . "\n";

// Delete Page
request("$baseUrl/index.php?controller=page&action=delete&id=$pageId", ['_method' => 'POST']);

// Verify DB
$stmt->execute([$pageId]);
$existsInDb = $stmt->fetchColumn() !== false;
echo "Exists in DB: " . ($existsInDb ? "YES" : "NO") . "\n";
echo "File exists on disk: " . (file_exists($physicalPath) ? "YES" : "NO") . "\n";


echo "\n=== Verification 2: Flash Message ===\n";
// Create a success flash message by doing some action
$res = request("$baseUrl/index.php?controller=series&action=store", [
    'title' => 'Flash Test Series',
    'synopsis' => 'Test',
    'status' => 'planning'
]);
// Go to series list (first load)
$html1 = requestGet("$baseUrl/index.php?controller=series&action=index");
$hasFlash1 = strpos($html1, 'Đã tạo truyện mới thành công') !== false;
echo "Flash message on 1st load: " . ($hasFlash1 ? "YES" : "NO") . "\n";

// Refresh page
$html2 = requestGet("$baseUrl/index.php?controller=series&action=index");
$hasFlash2 = strpos($html2, 'Đã tạo truyện mới thành công') !== false;
echo "Flash message on 2nd load: " . ($hasFlash2 ? "YES" : "NO") . "\n";


echo "\n=== Verification 3: UX Upload ===\n";
// Login as Assistant
request("$baseUrl/index.php?controller=auth&action=logout"); 
request("$baseUrl/index.php?controller=auth&action=authenticate", [
    'login_id' => 'assistant_user',
    'password' => 'password123'
]);

// Upload dummyImg (which is actually image/jpeg) but name it .png
$res = request("$baseUrl/index.php?controller=submission&action=store", [
    'task_id' => 1,
    'notes' => 'Test mismatched mime'
], [
    'file' => ['path' => $dummyImg, 'mime' => 'image/png', 'name' => 'mismatched.png']
]);
// Find submission in DB
$stmt = $db->query("SELECT file_url FROM submissions ORDER BY submission_id DESC LIMIT 1");
$fileUrl = $stmt->fetchColumn();
echo "Stored File URL: " . $fileUrl . "\n";
$ext = pathinfo($fileUrl, PATHINFO_EXTENSION);
echo "Auto-corrected extension: " . $ext . "\n";
