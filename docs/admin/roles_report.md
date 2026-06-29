# Roles Management Report

## Chức năng
Trang Roles Management chỉ hiển thị thông tin (Read Only), bao gồm:
- Danh sách tất cả vai trò trong hệ thống.
- Mô tả chi tiết từng vai trò.
- Số lượng người dùng thuộc mỗi vai trò.
- Summary cards thống kê trực quan.

## Thiết kế
- Route: `?controller=user&action=roles`
- Controller: `UserController::roles()`
- View: `views/admin/roles.php`
- Sidebar: Menu Quản lý Vai trò (Chỉ hiển thị với Admin).

## Giới hạn
- Không cho phép Create, Update, Delete vai trò.
- Vai trò là dữ liệu cố định trong hệ thống.