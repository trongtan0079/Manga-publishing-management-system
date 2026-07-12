# Admin Module Walkthrough

## File đã chỉnh sửa
1. `views/layouts/sidebar.php` - Sửa Dashboard link + thêm menu Vai trò.
2. `views/admin/dashboard.php` - Viết lại hoàn toàn với stat cards và biểu đồ Chart.js.
3. `views/admin/user_create.php` - Đồng nhất giao diện + BASE_PATH.
4. `views/admin/user_edit.php` - Đồng nhất giao diện + BASE_PATH.
5. `views/admin/user_detail.php` - Đồng nhất giao diện + BASE_PATH.
6. `views/admin/users.php` - Dùng BASE_PATH cho tất cả liên kết.
7. `controllers/UserController.php` - Thêm trạng thái banned + action roles() + BASE_PATH.
8. `controllers/DashboardController.php` - Thêm stats + import Notification Model.

## File mới
1. `views/admin/roles.php` - Trang Roles Management (Read Only).

## Luồng kiểm thử
1. Đăng nhập Admin -> Dashboard hiển thị đầy đủ thông số + biểu đồ.
2. Click Thêm người dùng mới -> Form tạo user.
3. Tạo user với trạng thái banned -> Lưu thành công.
4. Sidebar -> Quản lý Vai trò -> Hiển thị bảng danh sách vai trò.
5. Đăng nhập vai trò khác -> Sidebar Dashboard link hoạt động chính xác.