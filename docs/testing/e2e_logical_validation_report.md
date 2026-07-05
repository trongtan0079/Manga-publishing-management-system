# Báo cáo Kiểm thử Nghiệp vụ & An toàn Hệ thống (E2E Logical Validation & Security Report)

Báo cáo này ghi nhận kết quả kiểm thử chuỗi nghiệp vụ khép kín (End-to-End) trên hệ thống Manga Publishing Management System sau khi đã hoàn tất các bản vá lỗi bảo mật logic và kiểm chuẩn dữ liệu (validation).

---

## 1. Môi trường kiểm thử
- **Đường dẫn ứng dụng**: `http://localhost:8080/index.php` (hoặc `http://localhost/Manga-publishing-management-system/` trên Apache)
- **Database**: Cổng `3307` (manga_workflow)
- **Công cụ thực hiện**: Browser Subagent (Kiểm thử tự động hóa hành vi người dùng)
- **Tài khoản test mặc định**:
  - **Mangaka**: `mangaka_user` / `password123`
  - **Trợ lý (Assistant)**: `assistant_user` / `password123`
  - **Biên tập viên (Editor)**: `editor_user` / `password123`
  - **Ban giám đốc (Board)**: `board_user` / `password123`

---

## 2. Kịch bản E2E & Kết quả thực tế

### Kịch bản 1: Tạo dự án & Phân phối công việc (Mangaka)
- **Hành động**: 
  - Mangaka đăng nhập, tạo bộ truyện mới **"Test Series"**.
  - Tạo chương truyện mới **"Ch. 1 - Test Chapter"**.
  - Tải lên trang truyện mới (Page 1) và thực thi **"Quét AI phân đoạn vùng"**.
  - Nhận diện thành công các phân vùng nhân vật, khung thoại.
  - Tạo một Task **"Background inking"** tại phân vùng số 4, giao cho **Assistant One**.
- **Kết quả**: Thành công 100%. Lịch sử thông báo gửi đến Assistant được ghi nhận thành công trong DB.

### Kịch bản 2: Hoàn thành nhiệm vụ & Nộp bản thảo (Assistant)
- **Hành động**:
  - Trợ lý (`assistant_user`) đăng nhập, truy cập bảng điều khiển nhiệm vụ.
  - Chọn nhiệm vụ **"Background inking"** và tải lên tệp nộp bài.
- **Kết quả**: Thành công 100%. Trạng thái Task chuyển sang chờ duyệt, gửi thông báo tự động đến Mangaka.

### Kịch bản 3: Duyệt sản phẩm trợ lý & Nộp chương (Mangaka)
- **Hành động**:
  - Mangaka đăng nhập lại, vào xem bản thảo nhiệm vụ của Assistant.
  - Click **"Đánh giá & Phê duyệt"**, cho điểm **8/10** và nhấn Approve.
  - Sau đó, vào phần nộp chương truyện, tải lên tệp ZIP toàn bộ chương truyện gửi cho Biên tập viên.
- **Kết quả**: Thành công. Trạng thái Task tự động đổi sang `completed`, phân vùng tương ứng trên trang đổi sang `completed`. Chương truyện đổi sang trạng thái `reviewing`.

### Kịch bản 4: Biên tập viên thẩm định chương truyện (Editor)
- **Hành động**:
  - Editor (`editor_user`) đăng nhập, kiểm tra danh sách bản thảo chương truyện chờ duyệt.
  - Mở chi tiết chương của "Test Series".
  - Chấm điểm **9/10**, viết nhận xét và chọn Approve.
- **Kết quả**: Thành công. Trạng thái Chapter chuyển sang `approved` và Mangaka nhận được thông báo chúc mừng.

### Kịch bản 5: Ban giám đốc duyệt xuất bản (Board)
- **Hành động**:
  - Board Member (`board_user`) đăng nhập, chuyển đổi trạng thái của bộ truyện "Test Series" từ **Kế hoạch (Planning)** sang **Đang xuất bản (Ongoing)** và chọn lịch phát hành là Hàng tuần.
- **Kết quả**: Thành công. Trạng thái dự án thay đổi ngay lập tức trên dashboard.

---

## 3. Rà soát & Khắc phục lỗi bảo mật logic người dùng đã tồn tại
Trong quá trình kiểm thử, chúng tôi cũng đã cấu hình hiển thị thông báo lỗi trên giao diện quản trị Admin khi thêm/sửa thông tin người dùng:
- **Tình huống**: Admin cố tình tạo hoặc cập nhật tài khoản trùng tên đăng nhập (`username`) hoặc email đã có trong hệ thống.
- **Cách xử lý**:
  - Bổ sung hiển thị thẻ Alert hiển thị lỗi của Session (`$_SESSION['error']`) trực tiếp tại các file giao diện:
    - [user_create.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/admin/user_create.php#L29-L43)
    - [user_edit.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/admin/user_edit.php#L29-L43)
    - [users.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/admin/users.php#L21-L35)
- **Kết quả kiểm thử**: Khi Admin nhập trùng `username` hoặc `email`, trang tự động tải lại và hiển thị cảnh báo đỏ mô tả chính xác lỗi (ví dụ: `Lỗi: Username 'mangaka_user' đã tồn tại trong hệ thống!`) thay vì hiển thị trang trắng hay crash dữ liệu.
