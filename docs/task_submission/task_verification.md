# Kế Hoạch Nghiệm Thu (Verification Plan) - Module Task & Submission

Tài liệu này vạch ra quy trình và kịch bản kiểm tra để xác minh các chức năng, quyền sở hữu (Ownership), phân quyền (RBAC) và bảo mật của Module Task & Submission hoạt động đúng đắn sau khi sửa lỗi.

---

## 1. Rà Soát Mã Nguồn Tĩnh (Static Code Audit)
- Đảm bảo tuân thủ mô hình kiến trúc MVC của dự án.
- Đảm bảo toàn bộ các câu lệnh SQL trong `models/Task.php` và `models/Submission.php` đều sử dụng **Prepared Statements** thông qua PDO để ngăn chặn SQL Injection.
- Rà soát các tệp tin Controller, Model, View liên quan để đảm bảo không còn chứa các lệnh debug như `var_dump()`, `print_r()`, `die()`, hoặc comment `TODO`.

---

## 2. Kịch Bản Kiểm Thử Chức Năng & Bảo Mật (Test Scenarios)

### Kịch Bản 2.1: Quản Lý Task (Dành Cho Mangaka)
1. **Kiểm tra Tạo Task**:
   - Truy cập vào trang chi tiết một Page thuộc Series của Mangaka hiện tại. Nhấn **Create Task**.
   - Thử gửi form rỗng -> Hệ thống phải chặn và báo lỗi.
   - Thử nhập Tiêu đề > 255 ký tự -> Hệ thống phải báo lỗi.
   - Thử giao cho một `assistant_id` không tồn tại (thay đổi giá trị option qua DevTools) -> Hệ thống phải chặn và báo lỗi.
   - Nhập thông tin hợp lệ -> Task được lưu thành công, hiển thị đúng trên danh sách trang, và gửi thông báo tới Assistant.
2. **Kiểm tra Sửa Task**:
   - Chọn Sửa Task -> Đổi thông tin tiêu đề, độ ưu tiên, hạn chót. Lưu thành công.
   - Nhập một giá trị `due_date` sai định dạng -> Hệ thống báo lỗi và từ chối lưu.
   - Thử đổi ID Task trên URL thành ID của một Task thuộc Mangaka khác -> Hệ thống phải hiển thị thông báo lỗi quyền truy cập và chuyển hướng về Dashboard.
3. **Kiểm tra Xóa Task**:
   - Thử gửi yêu cầu xóa Task bằng GET request -> Hệ thống báo lỗi phương thức yêu cầu không hợp lệ.
   - Bấm nút **Delete** trên giao diện (sử dụng POST) -> Xóa thành công, biến mất khỏi CSDL.

### Kịch Bản 2.2: Cập Nhật Trạng Thái Task (Dành Cho Assistant)
1. **Xem danh sách công việc**:
   - Assistant đăng nhập, truy cập Dashboard -> Danh sách các Task được giao hiển thị chính xác.
2. **Cập nhật tiến độ**:
   - Assistant chọn thay đổi trạng thái thành `In Progress` và nhấn **Save** -> Cập nhật thành công.
   - Thử gửi giá trị trạng thái không hợp lệ (ví dụ: `deleted` hoặc `approved`) -> Hệ thống từ chối cập nhật và báo trạng thái không hợp lệ.
   - Thử gửi yêu cầu cập nhật cho Task của Assistant khác -> Hệ thống chặn và báo lỗi quyền sở hữu.

### Kịch Bản 2.3: Nộp Bản Thảo (Submission)
1. **Kiểm tra Phân quyền nộp**:
   - Mangaka nộp Chapter -> Chọn Chapter thuộc truyện của mình, tải tệp tin lên -> Nộp thành công.
   - Assistant nộp Task -> Chọn Task được giao cho mình, tải tệp tin lên -> Nộp thành công.
   - Thử nộp bản vẽ cho Task của Assistant khác -> Hệ thống chặn và báo lỗi.
   - Thử nộp Chapter không thuộc Series của mình -> Hệ thống chặn và báo lỗi.
2. **Kiểm tra Upload File An Toàn (Upload Security)**:
   - Tải lên tệp tin `.php` nhưng đổi tên thành `.jpg` -> Hệ thống gọi `finfo_file` và `getimagesize()`, phát hiện tệp tin giả mạo và từ chối tải lên.
   - Tải lên tệp tin văn bản `.txt` đổi tên thành `.pdf` -> Hệ thống kiểm tra signature `%PDF` ở đầu tệp, phát hiện giả mạo và từ chối tải lên.
   - Tải lên tệp tin ảnh thật `.png`, `.jpg` -> Hệ thống chấp nhận và lưu trữ thành công vào thư mục `uploads/submissions/`.
   - Tải lên tệp tin ZIP thật chứa nhiều bản vẽ -> Hệ thống kiểm tra chữ ký `PK\x03\x04` đầu tệp và chấp nhận tải lên.

### Kịch Bản 2.4: Xóa Bản Thảo & Dọn Dẹp File Rác
1. **Kiểm tra Xóa bản thảo**:
   - Người nộp truy cập lịch sử nộp bài, chọn xóa bản thảo đang ở trạng thái `pending`.
   - Xác minh: Bản ghi bị xóa khỏi CSDL, đồng thời tệp tin vật lý tương ứng trong thư mục `uploads/submissions/` bị xóa hoàn toàn khỏi đĩa cứng (`unlink`).
   - Thử xóa bản thảo đã được review (Approved/Rejected) -> Hệ thống từ chối xóa và báo lỗi.
   - Thử xóa bản thảo của người khác -> Hệ thống từ chối xóa và báo lỗi quyền sở hữu.
   - Thử gửi yêu cầu xóa bằng GET request -> Hệ thống chặn vì yêu cầu không hợp lệ.
