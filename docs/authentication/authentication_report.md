# Báo cáo: Authentication & Session Review

## 1. Phạm vi rà soát
- `core/Auth.php`
- `controllers/AuthController.php`
- Các luồng xử lý Login, Logout, Session và Redirect.

## 2. Kết quả đánh giá
Hệ thống xử lý xác thực (Authentication) đã được thiết kế khá chuẩn xác theo mô hình MVC. Tuy nhiên, đã phát hiện và khắc phục một số điểm yếu bảo mật nghiêm trọng.

### 2.1 Luồng Login
- **Hoạt động đúng:** `authenticate()` đã thực hiện kiểm tra `password_verify` an toàn chống lại Rainbow table/Brute Force cơ bản (do dùng Bcrypt mặc định của PHP). Redirect hoạt động mượt mà, chuyển hướng User về Dashboard tương ứng dựa trên Role.
- **Cải tiến:** 
  - Bổ sung kiểm tra `$user['status'] !== 'active'`. Các tài khoản bị khóa (inactive) hiện tại đã bị từ chối đăng nhập.
  - Đã bổ sung `session_regenerate_id(true)` ngay sau khi xác thực thành công. Điều này đóng vai trò then chốt trong việc phòng chống tấn công **Session Fixation**.

### 2.2 Luồng Logout
- **Hoạt động đúng:** `logout()` thực hiện xóa mảng `$_SESSION`, hủy Cookie session hiện tại và gọi `session_destroy()`. Điều này đảm bảo phiên làm việc được chấm dứt hoàn toàn.

### 2.3 Quản lý Session
- Session được khởi tạo an toàn ở các Controller thông qua `session_status() === PHP_SESSION_NONE`.
- Các Controller đều sử dụng `requireLogin()` để đảm bảo phiên làm việc hợp lệ.
- **Cải tiến:** Hỗ trợ thông báo Flash Messages (`$_SESSION['error']`, `$_SESSION['success']`) qua Session hoạt động ổn định và bị hủy ngay sau khi hiển thị (trong Views hoặc Controllers).

## 3. Mức độ hoàn thiện
- **Trạng thái:** Hoàn thành 100% yêu cầu.
- Sẵn sàng hoạt động an toàn trong môi trường Demo.
