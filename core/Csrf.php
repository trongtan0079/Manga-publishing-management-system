<?php
// core/Csrf.php

/**
 * Class Csrf
 * 
 * Quản lý và bảo vệ Cross-Site Request Forgery (CSRF) cho toàn bộ hệ thống Manga PMS.
 */
class Csrf
{
    /**
     * Lấy token CSRF từ Session, hoặc tạo mới nếu chưa tồn tại.
     * Token được tạo bằng bin2hex(random_bytes(32)) -> chuỗi 64 ký tự hex ngẫu nhiên an toàn.
     * 
     * @return string
     */
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Tạo thẻ input hidden chứa CSRF Token để nhúng vào các Form HTML.
     * 
     * @return string
     */
    public static function field(): string
    {
        $token = self::getToken();
        $escapedToken = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $escapedToken . '">';
    }

    /**
     * Kiểm tra tính hợp lệ của token CSRF được gửi lên so với token trong Session.
     * Sử dụng hash_equals để chống Timing Attack.
     * 
     * @param mixed $token Token gửi lên từ client
     * @return bool True nếu hợp lệ, False nếu không hợp lệ hoặc thiếu
     */
    public static function validate($token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sessionToken = $_SESSION['csrf_token'] ?? null;

        if (empty($sessionToken) || !is_string($sessionToken)) {
            return false;
        }

        if (empty($token) || !is_string($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Trích xuất CSRF Token từ request hiện tại.
     * Hỗ trợ lấy từ:
     * 1. Header X-CSRF-TOKEN (HTTP_X_CSRF_TOKEN)
     * 2. Form POST body ($_POST['csrf_token'])
     * 3. JSON body (nếu Content-Type là application/json)
     * 
     * @return string|null
     */
    public static function getTokenFromRequest(): ?string
    {
        // 1. Kiểm tra HTTP Header (cho AJAX / Fetch)
        if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            return trim($_SERVER['HTTP_X_CSRF_TOKEN']);
        }

        // 2. Kiểm tra $_POST (cho Form HTML / FormData)
        if (!empty($_POST['csrf_token']) && is_string($_POST['csrf_token'])) {
            return trim($_POST['csrf_token']);
        }

        // 3. Kiểm tra JSON body nếu Content-Type là application/json
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $rawInput = file_get_contents('php://input');
            if (!empty($rawInput)) {
                $data = json_decode($rawInput, true);
                if (is_array($data) && !empty($data['csrf_token']) && is_string($data['csrf_token'])) {
                    return trim($data['csrf_token']);
                }
            }
        }

        return null;
    }
}
