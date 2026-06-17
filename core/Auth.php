<?php
// core/Auth.php

/**
 * Bắt đầu session nếu chưa được khởi tạo
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra xem người dùng đã đăng nhập chưa
 * Nếu chưa, chuyển hướng về trang đăng nhập
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /index.php?controller=auth&action=login');
        exit;
    }
}

/**
 * Kiểm tra xem người dùng có quyền (role) cụ thể hay không
 * Nếu không, hiển thị thông báo lỗi hoặc chuyển hướng
 * 
 * @param string $roleName Tên của role yêu cầu (ví dụ: 'admin', 'mangaka')
 */
function requireRole($roleName) {
    requireLogin();
    if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== $roleName) {
        http_response_code(403);
        die("Access Denied: You do not have the required role ({$roleName}) to access this page.");
    }
}
