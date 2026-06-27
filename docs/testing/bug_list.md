# Danh sách Bug (Bug List)

Tổng hợp toàn bộ các bug đã phát hiện và khắc phục trong quá trình kiểm thử E2E và Security Testing của hệ thống Manga Publishing Management System.

---

### Bug 01: Quản lý File Rác Khi Xóa Page
- **ID**: BUG-001
- **Module**: Page Management (`PageController.php`)
- **Mức độ**: Medium
- **Mô tả**: Khi Mangaka xóa một `Page` (Trang truyện), hệ thống chỉ xóa bản ghi trong Database mà không xóa file vật lý tương ứng trong thư mục `/uploads/pages/`, gây lãng phí dung lượng lưu trữ dài hạn.
- **Các bước tái hiện (Steps to Reproduce)**:
  1. Mangaka đăng nhập và vào một Chapter.
  2. Tạo Page mới, tải lên file ảnh `page_01.png`.
  3. Nhấn "Xóa" Page vừa tạo.
  4. Mở thư mục `uploads/pages/` trên server, file `page_01.png` vẫn còn tồn tại.
- **Nguyên nhân**: Thiếu logic gọi hàm `unlink()` trước khi gọi hàm Model xóa dòng dữ liệu Database.
- **Cách khắc phục**: Đã thêm đoạn code kiểm tra `file_exists($filePath)` và thực thi `unlink($filePath)` ngay trước khi xóa Database.
- **Trạng thái**: **Verified Fixed** (Đã kiểm thử hồi quy thành công).

---

### Bug 02: Lỗi Validation MIME Type quá cứng nhắc (UX Upload)
- **ID**: BUG-002
- **Module**: Submission Management (`SubmissionController.php`)
- **Mức độ**: Low
- **Mô tả**: Nếu người dùng nộp một file ảnh thực chất là `image/jpeg` nhưng vô tình bị đổi tên đuôi thành `.png` (không khớp MIME Type thực tế), hệ thống sẽ báo lỗi và từ chối lưu file. Điều này gây trải nghiệm kém cho Assistant/Mangaka.
- **Các bước tái hiện (Steps to Reproduce)**:
  1. Đăng nhập với role Assistant.
  2. Tại form Upload Submission, chọn file `test.jpeg` đã bị đổi tên thành `test.png`.
  3. Bấm Submit.
  4. Hệ thống báo lỗi: "Nội dung file không hợp lệ (MIME type mismatch)."
- **Nguyên nhân**: Code kiểm tra đuôi mở rộng và MIME type, nhưng không linh hoạt xử lý trong trường hợp MIME Type vẫn nằm trong danh sách an toàn (`allowedMimes`).
- **Cách khắc phục**: Đã sửa lại hàm validation: Khi MIME Type hợp lệ nhưng đuôi file sai, hệ thống tự động đổi đuôi file (`$ext = $correctExt`) cho đúng với nội dung thực tế và chấp nhận lưu file (Auto-correct extension).
- **Trạng thái**: **Verified Fixed**

---

### Bug 03: Flash Message tồn tại qua nhiều trang
- **ID**: BUG-003
- **Module**: Giao diện chung (Views / Layouts)
- **Mức độ**: Low
- **Mô tả**: Các thông báo (Alert) thành công hay lỗi lấy từ `$_SESSION['success']` hoặc `$_SESSION['error']` thỉnh thoảng vẫn tồn tại sau khi Refresh (F5) hoặc điều hướng (Back) nếu một số View quên không thực thi `unset()`.
- **Các bước tái hiện (Steps to Reproduce)**:
  1. Thực hiện tạo mới Series thành công (nhận Flash Message thành công).
  2. Nhấn nút Back trên trình duyệt hoặc F5 trang.
  3. Thông báo thành công cũ vẫn hiện lên lần nữa.
- **Nguyên nhân**: Không có cơ chế xóa Session rác tập trung sau khi render. Các view tự xử lý lẻ tẻ nên hay bị sót.
- **Cách khắc phục**: Chèn các lệnh `unset($_SESSION['...'])` vào cuối file `views/layouts/footer.php` (ngay trước thẻ đóng `</body>`). Do đây là điểm cuối cùng của mọi giao diện, nó bảo đảm Message chỉ hiện đúng 1 lần rồi biến mất.
- **Trạng thái**: **Verified Fixed**

---

### Bug 04: Lỗi phân quyền ngầm định tại danh sách Submission
- **ID**: BUG-004
- **Module**: Submission Management (`SubmissionController.php`)
- **Mức độ**: Medium
- **Mô tả**: Hàm `index()` của `SubmissionController` chỉ kiểm tra nhánh `if ($role === 'editor') { ... } else { ... }`. Điều này vô tình cho phép Role `Board` và `Admin` lọt vào nhánh `else` (xem danh sách của user). Dù dữ liệu trả về rỗng, nhưng nó vi phạm chuẩn Access Denied của Ma trận phân quyền.
- **Các bước tái hiện (Steps to Reproduce)**:
  1. Đăng nhập bằng tài khoản `Board` hoặc `Admin`.
  2. Truy cập URL: `http://localhost:8000/index.php?controller=submission&action=index`
  3. Trang hiển thị danh sách rỗng thay vì báo lỗi 403 Forbidden.
- **Nguyên nhân**: Thiếu kiểm tra Role chặn cứng ở nhánh `else`.
- **Cách khắc phục**: Đổi `else` thành `elseif ($role === 'mangaka' || $role === 'assistant')` và bổ sung nhánh `else` cuối cùng để bắn lỗi HTTP 403 Forbidden kèm Redirect.
- **Trạng thái**: **Verified Fixed**
