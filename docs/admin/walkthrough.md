# Admin Module Walkthrough

## File dã ch?nh s?a
1. `views/layouts/sidebar.php` - S?a Dashboard link + thêm menu Vai trò.
2. `views/admin/dashboard.php` - Vi?t l?i hoàn toàn v?i stat cards và Chart.js.
3. `views/admin/user_create.php` - Layout th?ng nh?t + BASE_PATH.
4. `views/admin/user_edit.php` - Layout th?ng nh?t + BASE_PATH.
5. `views/admin/user_detail.php` - Layout th?ng nh?t + BASE_PATH.
6. `views/admin/users.php` - BASE_PATH cho t?t c? link.
7. `controllers/UserController.php` - Thêm banned status + action roles() + BASE_PATH.
8. `controllers/DashboardController.php` - Thêm stats + import Notification.

## File m?i
1. `views/admin/roles.php` - Trang Roles Management (Read Only).

## Lu?ng ki?m th?
1. Ðang nh?p Admin -> Dashboard hi?n th? d?y d? stats + charts.
2. Click Thêm ngu?i dùng m?i -> Form t?o user.
3. T?o user v?i status banned -> Luu thành công.
4. Sidebar -> Qu?n lý Vai trò -> Hi?n th? b?ng roles.
5. Ðang nh?p role khác -> Sidebar Dashboard link dúng.
