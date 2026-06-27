# Danh Sách Lỗi Đã Khắc Phục (Bug List) - Module Task & Submission

Dưới đây là danh sách các lỗi bảo mật, lỗi logic và lỗ hổng thiết kế đã được phát hiện và khắc phục hoàn toàn trong quá trình rà soát mã nguồn hệ thống.

---

### Bug 01: Lỗ Hổng CSRF & Xóa Dữ Liệu Bằng GET Request (Submission)
- **Mức độ**: High
- **Mô tả**: Hành động xóa Submission (`SubmissionController@delete`) được kích hoạt thông qua thẻ liên kết `<a>` (GET Request). Kẻ tấn công có thể thực hiện tấn công CSRF (Cross-Site Request Forgery) bằng cách chèn một đường dẫn xóa vào thẻ ảnh hoặc trang web bên ngoài để lừa người dùng xóa bản thảo của họ một cách vô thức.
- **Cách khắc phục**:
  - Chuyển đổi phương thức xử lý trong `SubmissionController::delete()` sang kiểm tra POST Request: `if ($_SERVER['REQUEST_METHOD'] !== 'POST') { ... }`.
  - Thay đổi giao diện xóa trong [submission_list.php](file:///d:/xampp/htdocs/management_system/views/editor/submission_list.php) và [submission_detail.php](file:///d:/xampp/htdocs/management_system/views/editor/submission_detail.php) thành thẻ `<form>` ẩn sử dụng phương thức `POST` và có hộp thoại xác nhận.

---

### Bug 02: Lỗ Hổng File Upload Giả Mạo & Bypass MIME Type
- **Mức độ**: High
- **Mô tả**: Trước đây hệ thống chỉ kiểm tra đuôi mở rộng file và kiểu MIME do trình duyệt gửi lên thông qua biến `$_FILES['file']['type']` (vốn dễ dàng bị giả mạo bằng các công cụ Proxy như Burp Suite). Người dùng xấu có thể đổi tên tệp tin `.php` thành `.jpg` và tải mã độc lên máy chủ.
- **Cách khắc phục**:
  - Triển khai hàm `finfo_file()` đọc trực tiếp nội dung nhị phân để lấy MIME thật của tệp.
  - Sử dụng hàm `getimagesize()` để đảm bảo tệp ảnh (`jpg`, `jpeg`, `png`) thực sự có dữ liệu điểm ảnh hợp lệ.
  - Đọc chữ ký đầu tệp (Header signatures) của PDF (`%PDF`) và ZIP (`PK\x03\x04`) để đảm bảo không tải lên tệp tin rác giả mạo.

---

### Bug 03: Giao Task Cho Trợ Lý Không Hợp Lệ (Invalid Assistant Assignment)
- **Mức độ**: Medium
- **Mô tả**: Khi Mangaka tạo hoặc sửa Task, họ có thể sửa đổi dữ liệu POST để truyền `assistant_id` là ID của một người dùng không tồn tại, hoặc người dùng có vai trò khác (`admin`, `editor`), hoặc trợ lý đã bị vô hiệu hóa (`inactive`). Hệ thống trước đó không hề kiểm tra tính hợp lệ của người nhận việc.
- **Cách khắc phục**:
  - Bổ sung xác thực trong `TaskController::store()` và `update()`: Tìm người dùng theo ID bằng `getUserByIdWithRole($assistantId)`, đảm bảo người đó tồn tại, có `role_name` là `'assistant'` và `status` hoạt động là `'active'`.

---

### Bug 04: Thiếu Validation Toàn Diện Cho Dữ Liệu Đầu Vào của Task
- **Mức độ**: Medium
- **Mô tả**: Khi Mangaka lưu Task mới hoặc cập nhật thông tin Task, các trường như `title` có thể bị gửi rỗng hoặc vượt quá giới hạn cột CSDL (255 ký tự). Các tham số `priority`, `status` và `due_date` có thể bị gửi giá trị giả mạo nằm ngoài whitelist và không đúng định dạng ngày tháng.
- **Cách khắc phục**:
  - Bổ sung kiểm tra `title`: không được rỗng, giới hạn `mb_strlen($title) <= 255`.
  - Whitelist dữ liệu `priority`: `['low', 'medium', 'high']`.
  - Whitelist dữ liệu `status`: `['pending', 'in_progress', 'completed']`.
  - Kiểm tra `due_date` bằng `strtotime($dueDate) !== false` để đảm bảo định dạng ngày giờ hợp lệ trước khi ghi vào Database.

---

### Bug 05: Lỗi URL / ID Tampering trên tham số Query String
- **Mức độ**: High
- **Mô tả**: Người dùng có thể thay đổi các tham số `id`, `page_id` trên URL để trỏ tới các ID giả mạo, ID âm, hoặc ID của người khác nhằm thực hiện tấn công rò rỉ dữ liệu hoặc thay đổi dữ liệu trái phép.
- **Cách khắc phục**:
  - Ép kiểu dữ liệu bằng `intval()` đối với toàn bộ tham số ID nhận từ URL/POST.
  - Kiểm tra ID có lớn hơn 0 hay không.
  - Kiểm tra sự tồn tại của thực thể trong CSDL ngay đầu Action. Nếu không tồn tại hoặc không thuộc quyền sở hữu (khớp `mangaka_id`/`assistant_id`), lập tức báo lỗi và chuyển hướng về trang an toàn thay vì thực thi tiếp.
