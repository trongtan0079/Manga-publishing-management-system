# Admin Bug List & Fixes

## BUG-01: Sidebar Dashboard Link
- **Mô t?**: Sidebar tr? d?n `action=index` nhung DashboardController không có action `index()`.
- **?nh hu?ng**: T?t c? role không th? quay v? Dashboard t? Sidebar.
- **S?a**: Thay d?i link sang `action=<role_name>` d?a theo session hi?n t?i.
- **File**: `views/layouts/sidebar.php`

## BUG-02: Nút "Thêm ngu?i dùng m?i"
- **Mô t?**: S? d?ng `<button>` không có href, không di?u hu?ng.
- **S?a**: Ð?i thành `<a>` tr? d?n `controller=user&action=create`.
- **File**: `views/admin/dashboard.php`

## BUG-03: Status banned không du?c ch?p nh?n
- **Mô t?**: Validation ch? accept `active` và `inactive`, thi?u `banned`.
- **?nh hu?ng**: Admin không th? t?o/s?a user v?i tr?ng thái banned.
- **S?a**: Thêm `banned` vào whitelist `in_array()` ? c? `store()` và `update()`.
- **File**: `controllers/UserController.php`

## BUG-04: Thi?u require Notification Model
- **Mô t?**: DashboardController s? d?ng `new Notification()` nhung thi?u require_once.
- **S?a**: Thêm `require_once Notification.php` vào ph?n import.
- **File**: `controllers/DashboardController.php`

## BUG-05: Hardcoded URLs
- **Mô t?**: Các redirect và href s? d?ng `/index.php` c?ng, không dùng BASE_PATH.
- **?nh hu?ng**: L?i khi deploy trong thu m?c con (VD: localhost/manga/).
- **S?a**: Thay t?t c? b?ng `BASE_PATH . '/index.php'`.
- **File**: `controllers/UserController.php`, `views/admin/users.php`
