# BÁO CÁO CHI TIẾT CÔNG NGHỆ SỬ DỤNG TRONG HỆ THỐNG
*Tài liệu kỹ thuật tổng hợp toàn bộ các công nghệ, thư viện, mô hình kiến trúc kèm theo giải thích chi tiết cơ chế hoạt động và file cài đặt cụ thể trong mã nguồn để phục vụ giải trình đồ án.*

---

## 1. CÔNG NGHỆ BACKEND (Phía Máy Chủ)

### 1.1 Ngôn ngữ Lập trình & Phiên bản
*   **PHP (v8.x):** Dự án sử dụng PHP thuần (Native PHP) nhằm tối ưu hóa hiệu năng, giảm thiểu độ trễ máy chủ và đảm bảo sinh viên làm chủ 100% dòng code mà không bị phụ thuộc vào các thư viện/framework cồng kềnh bên ngoài.
*   **Vị trí trong mã nguồn:** Toàn bộ các file `.php` trong thư mục [controllers/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/), [models/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/models/), [core/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/core/).

### 1.2 Kiến trúc Hệ thống (Architecture Pattern)
*   **Mô hình MVC (Model-View-Controller):** Phân tách độc lập tầng dữ liệu (Model), tầng giao diện (View) và tầng điều hướng logic (Controller) giúp dự án dễ bảo trì, mở rộng và kiểm thử độc lập.
    *   **Model (Tương tác CSDL):** Thư mục [models/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/models/)
    *   **Controller (Logic nghiệp vụ):** Thư mục [controllers/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/)
    *   **View (Giao diện hiển thị):** Thư mục [views/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/)
*   **Front Controller Pattern (`index.php`):** Mọi yêu cầu từ trình duyệt đều được định tuyến tập trung qua một file chạy duy nhất ở gốc dự án. Giúp dễ dàng quản lý vòng đời yêu cầu (Request Lifecycle), thiết lập bộ lọc xác thực và cấu hình hệ thống đồng bộ.
    *   **Vị trí:** File [index.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/index.php) ở gốc dự án (phân tích tham số `?controller=...&action=...`).

### 1.3 Tương tác Cơ sở dữ liệu (Database Access)
*   **PDO (PHP Data Objects) Extension:** Thư viện hướng đối tượng để kết nối và thao tác với MySQL, cung cấp cơ chế bảo mật và giao diện truy vấn đồng bộ.
    *   **Vị trí:** File [core/Model.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/core/Model.php) (Khởi tạo kết nối PDO qua cấu hình từ [config/database.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/config/database.php)).
*   **Singleton Connection Sharing (Chia sẻ kết nối tĩnh):** Lớp hạt nhân `core/Model.php` sử dụng thuộc tính tĩnh `$sharedConn` giúp tất cả các Model con dùng chung một kết nối MySQL duy nhất trong suốt vòng đời của 1 Request, giảm thiểu tối đa tải kết nối vào MySQL Server.
    *   **Vị trí:** File [core/Model.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/core/Model.php) (Xem cơ chế gán kết nối tĩnh tại hàm dựng `__construct`).
*   **Prepared Statements & Parameter Binding:** Toàn bộ các câu truy vấn động đều sử dụng tham số hóa kết hợp ràng buộc kiểu dữ liệu (`prepare()`, `execute()`), giúp triệt tiêu hoàn toàn nguy cơ tấn công **SQL Injection**.
    *   **Vị trí:** Các hàm tương tác CSDL trong tất cả các file tại [models/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/models/) (ví dụ: hàm `insert` trong `BoardVote.php`, `update` trong `Chapter.php`).
*   **Database Transactions (Giao dịch an toàn):** Áp dụng cơ chế giao dịch `beginTransaction()`, `commit()`, `rollBack()` tại các nghiệp vụ phức tạp liên quan đến nhiều bảng để đảm bảo tính toàn vẹn dữ liệu (ACID).
    *   **Vị trí:** 
        *   Hàm nộp phiếu bầu và xếp hạng: `store()` trong [controllers/SeriesRankingController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/SeriesRankingController.php)
        *   Hàm phê duyệt/từ chối bản thảo: `approve()` trong [controllers/ReviewController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/ReviewController.php)

### 1.4 Bảo mật & Phân quyền (Security & Access Control)
*   **Role-Based Access Control (RBAC):** Middleware phân quyền người dùng kiểm tra trực tiếp quyền hạn (`admin`, `mangaka`, `assistant`, `editor`, `board`) trước khi thực thi Action của Controller, chặn đứng các hành vi truy cập trực tiếp bất hợp pháp (URL Bypass).
    *   **Vị trí:** File [core/Auth.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/core/Auth.php) (Định nghĩa các hàm `requireLogin()`, `requireRole()`). Các hàm này được gọi ở hàm dựng `__construct` của các Controller.
*   **Mã hóa mật khẩu bằng thuật toán một chiều:** Sử dụng thuật toán `password_hash()` mã hóa bảo mật mật khẩu lưu trong DB, đối chiếu bằng `password_verify()`.
    *   **Vị trí:** 
        *   Khi tạo/cập nhật user: [controllers/UserController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/UserController.php)
        *   Khi đăng nhập: [controllers/AuthController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/AuthController.php)

### 1.5 Xử lý File vật lý & An toàn upload
*   **Xác thực định dạng File (Mime-type & Extension):** Tầng kiểm duyệt tệp tin tải lên kiểm tra cả đuôi mở rộng lẫn kiểu MIME thực tế (ví dụ: `image/png`, `application/pdf`) để loại trừ việc tải lên các mã độc ẩn dưới đuôi ảnh/tài liệu.
    *   **Vị trí:** 
        *   Ảnh bìa, Proposal: Hàm `handleCoverUpload()` và `handleProposalUpload()` trong [controllers/SeriesController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/SeriesController.php)
        *   Submission: Hàm `store()` trong [controllers/SubmissionController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/SubmissionController.php)
*   **Dọn dẹp file vật lý mồ côi (Orphan Cleanup):** Sử dụng hàm `@unlink` của PHP để xóa file vật lý trên ổ đĩa máy chủ khi bản ghi tương ứng bị xóa trong database, tránh rác hệ thống làm phình to bộ nhớ.
    *   **Vị trí:** 
        *   Xóa Chapter: Hàm `delete()` trong [controllers/ChapterController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/ChapterController.php)
        *   Xóa Series: Hàm `delete()` trong [controllers/SeriesController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/SeriesController.php)

### 1.6 Tái cấu trúc & Kế thừa tập trung (Centralized Inheritance Pattern)
*   **BaseController làm lớp cơ sở (Base Class Helper)**: Tận dụng tính kế thừa của lập trình hướng đối tượng (OOP). Các Controller trong hệ thống đều kế thừa từ `BaseController`. Các logic dùng chung như phân quyền truyện (`hasSeriesAccess`), kiểm tra trạng thái khóa của bộ truyện/chương truyện (`isSeriesLocked`, `isChapterLocked`), và tạo thẻ HTML hiển thị trạng thái chuẩn hóa (`getStatusBadge`, `getSeriesStatusBadge`) được định nghĩa tập trung tại đây.
    *   **Lợi ích**: Triệt tiêu hoàn toàn sự trùng lặp mã nguồn (DRY - Don't Repeat Yourself), đảm bảo logic nghiệp vụ được thực thi đồng bộ ở cả Controller (Backend) và View (Frontend).
    *   **Vị trí:** File [BaseController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/BaseController.php).

---

## 2. CÔNG NGHỆ FRONTEND (Phía Trình Duyệt)

### 2.1 Ngôn ngữ & Framework Giao diện
*   **HTML5, CSS3, JavaScript (ES6+):** Xây dựng cấu trúc trang và các tương tác động phía client, vẽ canvas lỗi và gọi API không đồng bộ (AJAX).
    *   **Vị trí:** Các file View trong thư mục [views/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/).
*   **Bootstrap v5 (Grid System, Modals, Cards, Popovers):** Framework xây dựng giao diện responsive nổi tiếng nhất thế giới.
    *   **Vị trí:** Được liên kết CDN trong file [views/layouts/header.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/layouts/header.php) và [views/layouts/footer.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/layouts/footer.php).
*   **Font Awesome v6:** Thư viện cung cấp các Icon vector trực quan trên Sidebar, Dashboard và các nút chức năng.
    *   **Vị trí:** Được liên kết CDN tại [views/layouts/header.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/layouts/header.php).

### 2.2 Tính năng Đánh dấu lỗi Trực quan (Visual Annotation Canvas API)
*   **HTML5 Canvas Drawing (Vẽ khoanh vùng lỗi):** Sử dụng HTML5 Canvas để Editor vẽ khung đỏ trực tiếp lên ảnh truyện.
    *   **Vị trí:** File [views/editor/review_detail.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/editor/review_detail.php) (Mã Javascript bắt sự kiện chuột `mousedown`, `mousemove`, `mouseup` để vẽ khung đỏ trên thẻ canvas).
*   **Responsive Coordinate Scaling System (Hệ tọa độ chuẩn hóa 800x1000):** Quy đổi tọa độ thực tế trên trình duyệt về một lưới ảo kích thước cố định là $800 \times 1000$ pixels để lưu trữ trong DB, khi hiển thị thì quy đổi ngược ra tỉ lệ phần trăm `%` giúp ô vẽ tự động co giãn tương thích theo kích thước thực tế của màn hình thiết bị.
    *   **Lưu tọa độ chuẩn hóa (Backend):** Hàm `save_annotation()` trong [controllers/ReviewController.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/controllers/ReviewController.php)
    *   **Vẽ khung đỏ tỷ lệ phần trăm (Frontend):** 
        *   Giao diện Editor: [views/editor/review_detail.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/editor/review_detail.php)
        *   Giao diện Mangaka: [views/mangaka/page_detail.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/mangaka/page_detail.php)
*   **Bootstrap Popover (Bong bóng hiển thị lỗi khi hover):** Hiển thị chi tiết lỗi cần sửa và tên Editor khi hover chuột vào vùng khoanh đỏ.
    *   **Vị trí:** Javascript khởi tạo `bootstrap.Popover` tại file [views/mangaka/page_detail.php](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/views/mangaka/page_detail.php).
*   **Centralized HTML Badge Rendering (Tải nhãn trạng thái từ Controller)**: Thay thế việc viết các câu lệnh điều kiện `switch-case` lặp đi lặp lại trong mã HTML của các View bằng cách gọi trực tiếp phương thức render nhãn từ Controller (ví dụ: `$this->getStatusBadge(...)`). Giúp cho các tệp View cực kỳ sạch sẽ, dễ bảo trì và tránh lỗi hiển thị không đồng bộ.

---

## 3. CƠ SỞ DỮ LIỆU (Database System)

*   **MySQL DBMS & InnoDB Storage Engine:** Hệ quản trị CSDL quan hệ hỗ trợ ràng buộc vật lý, khóa ngoại và giao dịch an toàn. Sử dụng chuẩn hóa 3NF phân rã dữ liệu thành 10 bảng nhất quán, loại bỏ trùng lặp.
    *   **Vị trí cài đặt:** File SQL [database/manga_workflow.sql](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/database/manga_workflow.sql) ở gốc thư mục dự án.

---

## 4. CÔNG CỤ THIẾT KẾ & QUẢN LÝ (Development & Design Tools)

*   **PlantUML:** Thiết kế các sơ đồ hệ thống (ERD, Class, State Machine...).
    *   **Vị trí:** Các file `.puml` trong thư mục [UML/](file:///d:/XAMPP/htdocs/Manga-publishing-management-system/UML/).
*   **XAMPP Control Panel:** Máy chủ cục bộ Apache & MySQL.
*   **Git (Version Control):** Công cụ quản lý mã nguồn dự án.

