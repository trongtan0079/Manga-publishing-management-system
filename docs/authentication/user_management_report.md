# Báo cáo: User Management Review

## 1. Phạm vi rà soát
- `controllers/UserController.php`
- Các thao tác: Tạo (Create), Xem (Read/Show/Index), Sửa (Update), Xóa (Delete) người dùng.

## 2. Các lỗi và thiếu sót được phát hiện
- **Validation Email:** Hệ thống chưa kiểm tra định dạng hợp lệ của Email khi Admin tạo mới hoặc cập nhật người dùng.
- **Validation Role:** Hệ thống không kiểm tra `role_id` do Form truyền lên có thực sự tồn tại trong Database hay không (nguy cơ chèn ID ảo).
- **Validation Password:** Mật khẩu khi tạo mới hoặc cập nhật không có quy định độ dài tối thiểu.
- **Rủi ro Xóa:** Admin có thể vô tình hoặc cố ý xóa chính tài khoản mà mình đang đăng nhập, dẫn đến lỗi phiên làm việc hoặc kẹt hệ thống.

## 3. Các thay đổi và khắc phục đã thực hiện
Đã thực hiện cập nhật mã nguồn trực tiếp vào `UserController.php`:
- **Bảo mật Email:** Thêm `filter_var($email, FILTER_VALIDATE_EMAIL)` vào hàm `store()` và `update()`.
- **Bảo mật Role & Status:** 
  - Thêm kiểm tra `$this->roleModel->findById($role_id)` để đảm bảo Role là hợp lệ.
  - Fix cứng `status` chỉ nhận `active` hoặc `inactive` qua `in_array`.
- **Bảo mật Mật khẩu:** Thêm điều kiện `strlen($_POST['password']) < 6` để yêu cầu mật khẩu tối thiểu 6 ký tự.
- **An toàn Hệ thống (Self-deletion):** Thêm chốt chặn `if ($id == $_SESSION['user_id'])` trong hàm `delete()` để cấm Admin xóa chính mình.

## 4. Đánh giá MVC & Tổng kết
- Controller `UserController` hoàn toàn tuân thủ MVC: Không chứa bất kỳ mã HTML nào, gọi thẳng đến Models để thao tác dữ liệu, điều hướng qua Views.
- Các Views được thiết kế dùng `htmlspecialchars` an toàn chống XSS.
- **Mức độ hoàn thiện:** 100%. User Management Module đã đáp ứng đầy đủ yêu cầu quản lý chặt chẽ và an toàn.
