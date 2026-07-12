# Hướng Dẫn Sử Dụng (Walkthrough) - Module Task & Submission

Tài liệu này hướng dẫn cách sử dụng và các bước kiểm thử trực quan trên giao diện đối với Module Task và Submission theo các vai trò người dùng (Roles).

---

## 1. Dành Cho Mangaka (Tác giả)

### 1.1 Khởi Tạo Task Mới
1. Đăng nhập hệ thống bằng tài khoản **Mangaka**.
2. Vào danh sách **Series** -> Chọn **Xem** một bộ truyện -> Chọn **Xem Chapter** -> Chọn **Xem** chi tiết trang truyện (Page).
3. Trong bảng điều khiển **Task Management**, nhấn nút **Create Task**.
4. Điền đầy đủ thông tin:
   - **Tiêu đề công việc**: Ví dụ: "Vẽ nền nhà cổ trang".
   - **Giao cho**: Chọn một Assistant trong danh sách.
   - **Mức độ ưu tiên**: Thấp, Trung bình hoặc Cao.
   - **Hạn chót**: Chọn ngày giờ.
5. Nhấn **Giao Task**. Hệ thống lưu và chuyển hướng lại trang chi tiết Page kèm thông báo thành công.

### 1.2 Chỉnh Sửa Hoặc Xóa Task
- **Sửa**: Tại danh sách Task trên trang truyện, nhấn nút **Edit** -> Thay đổi thông tin (ví dụ đổi Trợ lý hoặc đổi Hạn chót) -> Nhấn **Lưu thay đổi**.
- **Xóa**: Nhấn nút **Delete** bên cạnh Task -> Xác nhận hộp thoại -> Task biến mất khỏi trang truyện (Hệ thống thực hiện xóa qua POST bảo mật).

---

## 2. Dành Cho Assistant (Trợ lý)

### 2.1 Nhận Việc & Cập Nhật Tiến Độ
1. Đăng nhập hệ thống bằng tài khoản **Assistant**.
2. Truy cập trang chủ hoặc menu **My Tasks Dashboard**. Bạn sẽ thấy danh sách toàn bộ các công việc được giao cùng tên bộ truyện, chương, trang và tác giả giao.
3. Ở cột **Cập nhật**, chọn trạng thái từ danh sách thả xuống (`Pending`, `In Progress` hoặc `Completed`) và bấm **Save**.
4. Tiến độ mới được lưu và hiển thị tức thì.

### 2.2 Nộp Bản Vẽ (Upload Submission)
1. Trong menu **Submissions**, nhấn nút **Nộp Bản Thảo Mới**.
2. Chọn đúng **Task** đang làm việc trong dropdown.
3. Nhấp chọn tệp tin tải lên:
   - Hệ thống chấp nhận: `.jpg`, `.jpeg`, `.png`, `.pdf`, `.zip`.
   - Dung lượng tối đa: `20MB`.
4. Nhập lời nhắn/ghi chú gửi cho Mangaka (tùy chọn) -> Nhấn **Nộp Bản Thảo**.
5. Bản thảo sẽ được lưu và hiển thị trong danh sách chờ duyệt với trạng thái `Pending`.

### 2.3 Xóa Bản Thảo Đã Nộp
- Trong trang **Lịch sử nộp Bản thảo của tôi**, nếu bản thảo ở trạng thái `Pending`, Assistant sẽ thấy nút hình thùng rác.
- Nhấp chọn thùng rác -> Xác nhận xóa -> Bản thảo được xóa khỏi cơ sở dữ liệu và tệp tin vật lý tương ứng trên máy chủ bị dọn dẹp sạch sẽ.

---

## 3. Quy Trình Review (Đánh Giá Sản Phẩm)
- **Tác giả duyệt Task**: Mangaka truy cập trang chủ hoặc menu Review. Danh sách bản thảo do Assistant gửi cho các Task của mình sẽ hiển thị. Mangaka nhấn chọn xem chi tiết bản thảo (hệ thống hiển thị hình ảnh xem trước hoặc file PDF/ZIP), viết nhận xét và chọn **Approve** (Task sẽ tự động chuyển sang hoàn thành `Completed`) hoặc **Reject** (Yêu cầu làm lại).
- **Editor duyệt Chapter**: Tantou Editor truy cập menu kiểm duyệt bản thảo, xem tệp tin chapter Mangaka gửi, để lại phản hồi và duyệt hoặc từ chối chương truyện.
