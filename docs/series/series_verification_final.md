# Báo Cáo Xác Nhận Cuối Cùng (Final Verification) - Module Series, Chapter, Page

## 1. Rà Soát Tính Năng (Functional Verification)

### Series
- **Tạo/Sửa/Xóa:** Hoạt động đúng yêu cầu.
- **Validation Title:** Bắt buộc nhập, độ dài tối đa 255 ký tự để chống lỗi DB.
- **Ownership:** Hệ thống kiểm tra ID người dùng trên từng thao tác để giới hạn quyền. Mangaka chỉ được phép tác động lên Series của chính mình.
- **Role Permission:** Role `mangaka` được hardcode trong controller, tự động từ chối Role khác.

### Chapter
- **Tạo/Sửa/Xóa:** Hoạt động đúng yêu cầu.
- **Validation Title & Number:** Chapter Number bắt buộc > 0. Title giới hạn 255 ký tự. Đảm bảo Chapter Number không được trùng trong cùng một bộ truyện.
- **Ownership:** Controller truy ngược Series của Chapter, và kiểm tra quyền tác giả của Series đó. Bảo mật cao, chặn can thiệp URL.

### Page
- **Upload và Cập Nhật Ảnh:** Luồng xử lý qua biến `$_FILES`, lưu thư mục `/uploads/pages/`.
- **MIME Type Validation:** Tính năng `finfo_file` đã được kích hoạt và hoạt động tốt. Ngăn chặn triệt để lỗ hổng giả mạo file thực thi `.php` ngụy trang dưới định dạng ảnh `.jpg`.
- **Dọn Rác Hệ Thống (Unlink):** Khi gọi hành động `delete` trang, hệ thống tự động dò tìm vị trí tệp tin và chạy hàm `unlink()`. Không xuất hiện ảnh rác sau khi xóa.

## 2. Xác Nhận Bảo Mật (Security Verification)
- **Guest:** Chặn tại cửa bằng `requireLogin()` (tích hợp trong `requireRole()`).
- **Assistant / Editor / Board:** Chặn bằng mã trạng thái HTTP 403 Access Denied. Không thể tạo, sửa hay xóa thông qua URL bypass.
- **Tampering (Sửa tham số):** Nếu cố tình sửa biến `?id=X` trên URL, hàm `checkOwnership` (hoặc tương đương) lập tức văng `$_SESSION['error']` và đẩy về trang danh sách.

## 3. Chống Phân Mảnh Chức Năng Khác (Regression)
- Những chỉnh sửa validation hoàn toàn độc lập, **không ảnh hưởng** đến các Model/Controller khác như Task, Submission, Review, Notification.
- Dashboard vẫn gọi Data bình thường.

## Kết Luận Cuối
Quy trình Verification đạt trạng thái hoàn mỹ.
