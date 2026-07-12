# Admin Bug List & Fixes

## BUG-01: Sidebar Dashboard Link
- **Mô tả**: Sidebar trỏ đến `action=index` nhưng DashboardController không có action `index()`.
- **Ảnh hưởng**: Tất cả các vai trò không thể quay về Dashboard từ Sidebar.
- **Sửa**: Thay đổi link sang `action=<role_name>` dựa theo session hiện tại.
- **File**: `views/layouts/sidebar.php`

## BUG-02: Nút "Thêm người dùng mới"
- **Mô tả**: Sử dụng `<button>` không có href, không điều hướng.
- **Sửa**: Đổi thành `<a>` trỏ đến `controller=user&action=create`.
- **File**: `views/admin/dashboard.php`

## BUG-03: Trạng thái banned không được chấp nhận
- **Mô tả**: Validation chỉ chấp nhận `active` và `inactive`, thiếu `banned`.
- **Ảnh hưởng**: Admin không thể tạo/sửa người dùng với trạng thái banned.
- **Sửa**: Thêm `banned` vào whitelist `in_array()` ở cả `store()` và `update()`.
- **File**: `controllers/UserController.php`

## BUG-04: Thiếu import Notification Model
- **Mô tả**: DashboardController sử dụng `new Notification()` nhưng thiếu require_once.
- **Sửa**: Thêm `require_once Notification.php` vào phần import.
- **File**: `controllers/DashboardController.php`

## BUG-05: Đường dẫn cứng (Hardcoded URLs)
- **Mô tả**: Các redirect và href sử dụng `/index.php` cứng, không dùng BASE_PATH.
- **Ảnh hưởng**: Lỗi khi triển khai trong thư mục con (Ví dụ: localhost/manga/).
- **Sửa**: Thay tất cả bằng `BASE_PATH . '/index.php'`.
- **File**: `controllers/UserController.php`, `views/admin/users.php`