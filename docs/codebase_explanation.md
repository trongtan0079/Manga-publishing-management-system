# TÀI LIỆU HƯỚNG DẪN ĐỌC HIỂU & GIẢI THÍCH TOÀN BỘ MÃ NGUỒN (CODEBASE EXPLANATION)
*Tài liệu hướng dẫn chi tiết về sơ đồ cấu trúc file và chức năng cụ thể của từng Class/File trong dự án*

---

## 1. MÔ HÌNH MVC TỔNG QUAN
Dự án được xây dựng theo chuẩn mô hình **MVC (Model - View - Controller)**. Dưới đây là cách dòng dữ liệu di chuyển qua các thư mục chính:

```
                  ┌──────────────┐
                  │   Browser    │◀──────────────┐
                  └──────┬───────┘               │
                         │ (Request URL)         │
                         ▼                       │ (HTML / CSS / Response)
                  ┌──────────────┐               │
                  │  index.php   │               │
                  └──────┬───────┘               │
                         │ (Routing)             │
                         ▼                       │
                  ┌──────────────┐               │
                  │ Controllers  ├───────────────┤ (Render View)
                  └──────┬───────┴────────┐      │
                         │                │      │
      (Read/Write Data)  │                │      │
                         ▼                ▼      │
                  ┌──────────────┐  ┌────────────┴─┐
                  │    Models    │  │    Views     │
                  └──────────────┘  └──────────────┘
```

---

## 2. CHI TIẾT CÁC THƯ MỤC & FILE CỐT LÕI (Core & Config)

### 2.1. Thư mục `config/` (Cấu hình)
* **`database.php`**: Khai báo Class `Database` chứa cấu hình máy chủ cơ sở dữ liệu MySQL (Host, DB Name, Username, Password). Phương thức `connect()` khởi tạo kết nối thông qua PDO với chế độ xử lý lỗi ngoại lệ (`PDO::ERRMODE_EXCEPTION`) và tự động cấu hình bộ mã tiếng Việt `utf8mb4`.

### 2.2. Thư mục `core/` (Hạt nhân hệ thống)
* **`Model.php`**: Lớp cha (Base Model) của tất cả các Class trong thư mục `models/`. 
  * Thiết lập một thuộc tính tĩnh `$sharedConn` (Singleton-like pattern) để các model con chia sẻ chung 1 kết nối database trong cùng 1 request, giảm tải cho MySQL.
  * Cung cấp sẵn các hàm thao tác CRUD dùng chung: `findAll()` (lấy tất cả), `findById($id)` (tìm theo ID), `insert($data)` (thêm mới bằng tự động sinh câu lệnh bindValue), `update($id, $data)` (cập nhật động), `delete($id)` (xóa theo khóa chính).
* **`Auth.php`**: Chứa các hàm kiểm tra trạng thái phiên làm việc (Session):
  * `requireLogin()`: Ép buộc người dùng quay về trang đăng nhập nếu chưa đăng nhập.
  * `requireRole($roleName)`: Kiểm tra vai trò của người dùng trong Session có khớp với `$roleName` được yêu cầu hay không. Nếu không, lập tức từ chối truy cập và trả về mã lỗi HTTP 403 (Access Denied).

---

## 3. CHI TIẾT CÁC CONTROLLERS (`controllers/`)
Đóng vai trò điều phối luồng nghiệp vụ. Toàn bộ các controller đều kế thừa từ `BaseController`.

* **`BaseController.php`**: Lớp cha của các controller. Trong hàm dựng `__construct()`, nếu phát hiện người dùng đã đăng nhập, nó sẽ tự động truy vấn số lượng thông báo chưa đọc (`unreadCount`) và danh sách thông báo mới nhất (`latestNotifications`) để hiển thị thống kê lên thanh Header/Sidebar dùng chung của mọi trang giao diện.
* **`AuthController.php`**: Quản lý quy trình đăng nhập (`login()`), đăng xuất (`logout()`) và xem/cập nhật hồ sơ cá nhân (`profile()`). Khi Mangaka sửa họ tên, controller này cập nhật lại `$_SESSION['full_name']` để navbar phản hồi ngay lập tức.
* **`DashboardController.php`**: Chứa trang tổng quan của cả 5 vai trò:
  * `admin()`: Xem biểu đồ phân bố tài khoản, số lượng task và submission.
  * `mangaka()`: Xem tổng số truyện, số chương và biến động bảng xếp hạng.
  * `assistant()`: Thống kê task cần làm và bảng tính thù lao theo từng tháng.
  * `editor()`: Thống kê số chương chờ duyệt, số đánh giá đã thực hiện.
  * `board()`: Xem báo cáo xếp hạng Manga đứng đầu, top 5, bottom 5 trong kỳ.
  * `progress()`: Giao diện theo dõi tiến độ thời gian thực của các Series dành riêng cho Editor.
* **`SeriesController.php`**: Quản lý vòng đời bộ truyện. Chứa cơ chế kiểm tra quyền sở hữu (`checkOwnership`) để ngăn Mangaka sửa truyện của người khác. Riêng action duyệt truyện `publish()` bắt buộc phân quyền cho Editorial Board.
* **`ChapterController.php`**: Quản lý các chương truyện của series. Khi xóa chương, tích hợp quét xóa vật lý toàn bộ ảnh các trang vẽ và tệp tin bản thảo nộp lên.
* **`PageController.php`**: Quản lý các trang vẽ trong chương. Tích hợp nút kích hoạt AI phân đoạn tự động (`runAI`). Khi xóa trang vẽ, dọn dẹp sạch file ảnh trên máy chủ.
* **`PageRegionController.php`**: Quản lý các vùng phân đoạn đã nhận diện (Khung hình, bóng thoại, nhân vật, bối cảnh, SFX). Tích hợp thuật toán AI mô phỏng phân đoạn lưới thông minh (Smart Grid Subdivision) dựa trên hạt giống `page_id`, công cụ tự vẽ vùng thủ công (click-and-drag) và chức năng xóa phân vùng lệch để tăng tính thực tế.
* **`TaskController.php`**: Quản lý việc Mangaka giao việc trên trang/vùng cho Assistant. Có validation bắt buộc nhập, kiểm tra tài khoản assistant hợp lệ và còn hoạt động (active), đồng thời tự động cập nhật trạng thái phân vùng liên quan thành `in_progress` khi giao việc.
* **`SubmissionController.php`**: Quản lý việc Assistant nộp bản thảo task hoặc Mangaka nộp bản thảo Chapter. Chứa cơ chế kiểm tra chữ ký file nhị phân (Binary Magic Bytes) chống file giả mạo, chặn nộp đè bài khi task/chapter đã hoàn thành. Tích hợp giao diện làm mờ (dimming) và highlight phân vùng nổi bật dành riêng cho Trợ lý.
* **`ReviewController.php`**: Quản lý việc Editor/Mangaka đánh giá bản thảo. Khi duyệt (Approve) bản thảo của Assistant, tự động đồng bộ chuỗi trạng thái: Cập nhật Task thành `completed`, cập nhật Phân vùng liên quan thành `completed`, đồng thời tự động chuyển trạng thái của cả Trang truyện thành `approved` nếu tất cả phân vùng của trang đó đã hoàn thành.
* **`SeriesRankingController.php`**: Cho phép Editorial Board nhập điểm bình chọn và xếp hạng Manga. Nếu bộ truyện xếp hạng kém (Hạng >= 5 hoặc điểm < 50), tự động gửi thông báo cảnh báo nguy cơ hủy dự án.
* **`NotificationController.php`**: Quản lý việc hiển thị thông báo cá nhân, đánh dấu đã đọc (`markAsRead()`), hoặc đọc tất cả (`markAllAsRead()`).
* **`UserController.php`**: Cho phép Admin CRUD tài khoản người dùng, bắt buộc kiểm tra điền đầy đủ dữ liệu (username, full_name), quản lý danh sách vai trò hệ thống.

---

## 4. CHI TIẾT CÁC MODELS (`models/`)
Các Model ánh xạ trực tiếp với cấu trúc bảng trong MySQL và chịu trách nhiệm truy xuất dữ liệu:

* **`User.php`**: Thao tác trên bảng `users`. Chứa hàm kiểm tra đăng nhập (`verifyLogin`), tìm người dùng theo vai trò (`findByRoleName`).
* **`Series.php`**: Thao tác trên bảng `series`. Chứa hàm tìm truyện theo tác giả, tìm truyện kèm tên tác giả phục vụ trang quản trị.
* **`Chapter.php`**: Thao tác trên bảng `chapters`.
* **`Page.php`**: Thao tác trên bảng `pages`. Đảm bảo số trang trong chương không bị trùng lặp.
* **`PageRegion.php`**: Thao tác trên bảng `page_regions`.
* **`Task.php`**: Thao tác trên bảng `tasks`. Tìm các task chưa xong của Assistant, tìm task theo trang vẽ.
* **`Submission.php`**: Thao tác trên bảng `submissions`.
* **`Review.php`**: Thao tác trên bảng `reviews`.
* **`SeriesRanking.php`**: Thao tác trên bảng `series_rankings`. Tính toán top 5, bottom 5 truyện theo kỳ đánh giá gần nhất.
* **`Notification.php`**: Thao tác trên bảng `notifications`. Tính số thông báo chưa đọc, tự động lưu thông báo mới.

---

## 5. CHI TIẾT CẤU TRÚC GIAO DIỆN (`views/`)
Giao diện được tổ chức khoa học theo phân mục thư mục con:

* **`views/layouts/`** (Khung layout cố định):
  * `header.php`: Khai báo HTML, nạp CSS Tailwind/Bootstrap 5, các thư viện FontAwesome.
  * `navbar.php`: Thanh tiêu đề đầu trang, chứa biểu tượng thông báo và lối tắt hồ sơ.
  * `sidebar.php`: Thanh menu bên điều hướng động dựa theo vai trò người dùng trong Session.
  * `footer.php`: Kết thúc thẻ HTML và nạp các file JS Bootstrap.
* **`views/admin/`**: Chứa Dashboard hệ thống, form CRUD người dùng của Admin.
* **`views/mangaka/`**: Chứa form tạo/sửa Series, chi tiết Chapter/Page, khu vực giao việc và duyệt bản thảo của tác giả.
* **`views/assistant/`**: Chứa Dashboard hiển thị danh sách nhiệm vụ được giao và bảng thù lao sản phẩm của trợ lý.
* **`views/editor/`**: Chứa Dashboard tiến độ thời gian thực, giao diện đánh giá bản thảo và ghi nhận xét của biên tập viên.
* **`views/board/`**: Chứa Dashboard biểu đồ thống kê thứ hạng truyện, form duyệt xuất bản Series và form nhập xếp hạng bình chọn.
* **`views/shared/`**: Chứa trang xem thông báo cá nhân, trang đăng nhập của hệ thống.

---

## 6. LƯU Ý KHI GIẢI THÍCH MÃ NGUỒN CHO HỘI ĐỒNG
Khi giải thích mã nguồn trước giảng viên, bạn nên:
1. **Bắt đầu từ `index.php`**: Giải thích rằng đây là nơi đón nhận toàn bộ yêu cầu, kiểm tra Session người dùng thông qua `Auth.php` rồi mới chuyển tiếp việc khởi tạo Controller tương ứng.
2. **Đi tiếp tới Controller**: Chọn 1 hành động ví dụ (như Duyệt bản thảo trong `ReviewController` hoặc Giao việc trong `TaskController`), giải thích cách nó xác thực dữ liệu đầu vào.
3. **Mô tả cách Model hoạt động**: Nêu rõ các Model kế thừa `core/Model.php` giúp tái sử dụng mã nguồn CRUD tốt và tối ưu hóa kết nối qua thuộc tính tĩnh `$sharedConn`.
4. **Kết thúc ở View**: Chỉ ra cách các View nhận biến truyền từ Controller để hiển thị dữ liệu trực quan bằng HTML/Bootstrap.
