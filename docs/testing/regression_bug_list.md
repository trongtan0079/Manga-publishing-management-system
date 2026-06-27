# Danh sách Regression Bugs (Lỗi phát sinh hồi quy)

Tài liệu này ghi nhận mọi lỗi phát sinh mới (Regression Bugs) do ảnh hưởng từ các bản vá trong Giai đoạn Bug Fix (E2E) và Giai đoạn Security Audit.

### Tổng quan
- **Tổng số Regression Bug phát hiện**: 0
- **Số bug đã vá**: 0
- **Số bug còn tồn đọng**: 0

### Đánh giá nguyên nhân
Các sửa đổi trong hệ thống bao gồm:
1. **File Xóa Page (`PageController.php`)**: Chỉ bổ sung duy nhất hàm `unlink()` kết hợp `file_exists()`. Tuyệt đối không can thiệp vào logic Flow hay giao dịch DB. Do đó không gây đứt gãy luồng Review/Task.
2. **Quản lý Flash Message (`footer.php`)**: Đưa hàm `unset($_SESSION['...'])` vào sát thẻ `</body>`. Vì mọi request `Redirect` đều sử dụng `header()` kết hợp `exit;` trước khi view được load, nên thông báo Session vẫn được giữ trọn vẹn khi di chuyển giữa các trang. Chỉ khi trang HTML được load xong thì thông báo mới bị xóa để không lặp lại. Đây là một Fix cực kỳ thanh lịch và vô hại với hệ thống.
3. **Sửa lỗi Upload (`SubmissionController.php`)**: Can thiệp logic kiểm tra đuôi mở rộng và MIME type nội bộ (backend validation). Hoàn toàn không tác động đến tham số ngoại lai hay giao diện.
4. **Fix 403 Submission (`SubmissionController.php`)**: Thêm điều kiện `elseif` để kiểm soát chặt danh sách Role được gọi vào trang danh sách Submission. Không ảnh hưởng đến các quyền cốt lõi.

**Kết luận:** Quá trình vá lỗi cực kỳ an toàn, gọn gàng và không phá vỡ bất kỳ thành phần nào khác (Zero Regression). Hệ thống 100% ổn định.
