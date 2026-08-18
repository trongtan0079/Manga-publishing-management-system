# BÁO CÁO CSRF SECURITY HARDENING TOÀN BỘ PROJECT MANGA PMS

## 1. TỔNG QUAN DỰ ÁN VÀ MỤC TIÊU BẢO MẬT
- **Tên dự án:** Manga Publishing Management System (Manga PMS)
- **Kiến trúc:** PHP 8.x, Vanilla MVC, Apache, MySQL, Dockerized
- **Mục tiêu:** Bổ sung cơ chế chống tấn công Giả mạo yêu cầu từ trang khác (**Cross-Site Request Forgery - CSRF**) cho toàn bộ các luồng thay đổi trạng thái (state-changing actions) trong hệ thống mà không làm thay đổi hay phá vỡ logic nghiệp vụ, RBAC, database schema hoặc luồng tải lên tệp tin đa phương tiện (`multipart/form-data`).

---

## 2. KIẾN TRÚC VÀ CƠ CHẾ CSRF PROTECTION TRIỂN KHAI

### 2.1. Lớp cốt lõi (`core/Csrf.php`)
- **Tạo token ngẫu nhiên:** Sử dụng `bin2hex(random_bytes(32))` tạo chuỗi bảo mật 64 ký tự hex an toàn tuyệt đối về mặt mật mã học.
- **Lưu trữ phiên làm việc:** Token được khởi tạo và duy trì thống nhất trong `$_SESSION['csrf_token']`.
- **So sánh chống Timing Attack:** Sử dụng hàm `hash_equals()` tiêu chuẩn của PHP để chống tấn công phân tích thời gian (timing attacks).
- **Trích xuất thông minh (Smart Extraction):**
  1. Header HTTP `X-CSRF-TOKEN` (cho các lệnh gọi Fetch / AJAX / JSON).
  2. Trường POST `$_POST['csrf_token']` (cho các biểu mẫu HTML thông thường và upload file `multipart/form-data`).
  3. Chỉ đọc `php://input` khi header `Content-Type` là `application/json` (ngăn ngừa triệt để lỗi làm rỗng stream của `multipart/form-data`).
- **Trợ giúp View (`Csrf::field()`):** Tự động sinh ra thẻ `<input type="hidden" name="csrf_token" value="...">` với mã hóa an toàn qua `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.

### 2.2. Tích hợp Entry Point và Base Controller
- **`index.php`:** Nạp `core/Csrf.php` ngay sau khi khởi động phiên làm việc `session_start()`.
- **`controllers/BaseController.php`:**
  - Cung cấp phương thức `$this->validateCsrf()` cho tất cả các Controller con.
  - Phân loại lỗi CSRF (`handleCsrfError()`):
    - **AJAX / JSON Request:** Trả về HTTP Status `403 Forbidden` cùng dữ liệu JSON chuẩn: `{"success": false, "error": "CSRF token mismatch"}`.
    - **HTML Form POST Request:** Thiết lập HTTP Status `403`, gán thông báo lỗi `$_SESSION['error'] = 'Phiên làm việc hết hạn hoặc yêu cầu bảo mật không hợp lệ (CSRF Error).' ` và chuyển hướng an toàn về trang trước (`HTTP_REFERER`) hoặc trang chủ.

---

## 3. THỐNG KÊ CHI TIẾT CÁC THÀNH PHẦN ĐƯỢC BẢO VỆ

### 3.1. Danh sách Controller và Action được gắn `$this->validateCsrf()` (36 Actions / 12 Controllers)
| STT | Controller | Tệp tin | Action POST được bảo vệ |
|---|---|---|---|
| 1 | `AuthController` | `controllers/AuthController.php` | `authenticate()`, `updateProfile()` |
| 2 | `UserController` | `controllers/UserController.php` | `store()`, `update($id)`, `delete($id)` |
| 3 | `SeriesController` | `controllers/SeriesController.php` | `store()`, `submit($id)`, `update($id)`, `delete($id)`, `updateStatus($id)`, `vote($id)`, `updateDossierNotes($id)` |
| 4 | `ChapterController` | `controllers/ChapterController.php` | `store()`, `update($id)`, `delete($id)`, `publish($id)` |
| 5 | `PageController` | `controllers/PageController.php` | `store()`, `update($id)`, `delete($id)` |
| 6 | `PageRegionController` | `controllers/PageRegionController.php` | `store()`, `delete($id)` |
| 7 | `TaskController` | `controllers/TaskController.php` | `store()`, `update($id)`, `delete($id)` |
| 8 | `SubmissionController` | `controllers/SubmissionController.php` | `store()`, `delete($id)` |
| 9 | `ReviewController` | `controllers/ReviewController.php` | `store()`, `save_annotation()`, `delete_annotation()`, `save_submission_annotation()`, `delete_submission_annotation()` |
| 10 | `SeriesRankingController` | `controllers/SeriesRankingController.php` | `store()`, `update($id)`, `delete($id)` |
| 11 | `NotificationController` | `controllers/NotificationController.php` | `markAsRead($id)`, `markAllAsRead()` |
| 12 | `DashboardController` | `controllers/DashboardController.php` | *(Chỉ chứa các action GET báo cáo & thống kê)* |

### 3.2. Danh sách View và Biểu mẫu HTML Form được gắn `<?= Csrf::field() ?>` (46 Forms)
| Nhóm View | Tệp tin | Số lượng Form POST | Chi tiết chức năng |
|---|---|---|---|
| Layout & Auth | `views/auth/login.php` | 1 | Đăng nhập hệ thống (có meta token + input hidden) |
| Shared | `views/shared/profile.php` | 1 | Cập nhật thông tin cá nhân & Avatar |
| Shared | `views/shared/notifications.php` | 2 | Đánh dấu đọc tất cả, đánh dấu đọc từng thông báo |
| Shared | `views/shared/dashboard_notifications.php` | 1 | Đánh dấu đọc thông báo từ Dashboard widget |
| Admin | `views/admin/user_create.php` | 1 | Tạo người dùng mới |
| Admin | `views/admin/user_edit.php` | 1 | Cập nhật thông tin người dùng |
| Admin | `views/admin/users.php` | 1 | Xóa người dùng |
| Mangaka | `views/mangaka/series_create.php` | 1 | Tạo bộ truyện mới kèm tải ảnh bìa |
| Mangaka | `views/mangaka/series_edit.php` | 1 | Sửa thông tin bộ truyện |
| Mangaka | `views/mangaka/series.php` | 1 | Xóa bộ truyện |
| Mangaka | `views/mangaka/series_detail.php` | 2 | Nộp đề xuất bộ truyện, Xóa chapter |
| Mangaka | `views/mangaka/chapter_create.php` | 1 | Tạo chapter mới |
| Mangaka | `views/mangaka/chapter_edit.php` | 1 | Chỉnh sửa chapter |
| Mangaka | `views/mangaka/chapter_detail.php` | 2 | Xóa chapter, Xóa trang truyện |
| Mangaka | `views/mangaka/page_create.php` | 1 | Thêm trang truyện & upload ảnh bản thảo |
| Mangaka | `views/mangaka/page_edit.php` | 1 | Sửa trang truyện |
| Mangaka | `views/mangaka/page_detail.php` | 5 | Xóa trang, Cập nhật ảnh Genko, Xóa phân vùng, Tạo phân vùng, Xóa task |
| Mangaka | `views/mangaka/task_create.php` | 1 | Giao nhiệm vụ cho trợ lý |
| Mangaka | `views/mangaka/task_edit.php` | 1 | Sửa thông tin nhiệm vụ |
| Mangaka | `views/mangaka/task_list.php` | 1 | Xóa nhiệm vụ |
| Mangaka | `views/mangaka/submission_create.php` | 1 | Nộp bản thảo duyệt chương |
| Assistant | `views/assistant/task_list.php` | 1 | Cập nhật trạng thái nhiệm vụ |
| Assistant | `views/assistant/upload_submission.php` | 1 | Nộp bản vẽ hoàn thành nhiệm vụ |
| Editor | `views/editor/submission_list.php` | 1 | Xóa bản thảo nộp |
| Editor | `views/editor/submission_detail.php` | 1 | Xóa bản thảo nộp từ màn hình chi tiết |
| Editor | `views/editor/review_create.php` | 1 | Gửi đánh giá và phê duyệt bản thảo |
| Editor | `views/editor/dossier_detail.php` | 1 | Lưu hồ sơ biện hộ tác phẩm |
| Board | `views/board/rankings.php` | 1 | Xóa xếp hạng bộ truyện |
| Board | `views/board/ranking_edit.php` | 1 | Cập nhật điểm và thứ hạng |
| Board | `views/board/ranking_detail.php` | 1 | Xóa xếp hạng |
| Board | `views/board/ranking_create.php` | 1 | Tạo kỳ xếp hạng mới |
| Board | `views/board/publish_series.php` | 8 | Bỏ phiếu đồng ý/từ chối, Phê duyệt đề xuất, Đổi trạng thái phát hành (ongoing/completed/suspended/canceled), Xuất bản chapter |
| **Tổng cộng** | | **46** | **Tỷ lệ bảo vệ: 100%** |

### 3.3. Danh sách Header Meta Tag và Lệnh gọi Fetch/AJAX được bảo vệ
- **Meta Tag CSRF:**
  - Đã chèn `<meta name="csrf-token" content="...">` vào thẻ `<head>` của:
    1. `views/layouts/header.php` (dùng chung cho toàn bộ hệ thống sau đăng nhập).
    2. `views/auth/login.php` (dành riêng cho trang đăng nhập công khai).
- **Fetch AJAX Endpoints (`views/editor/submission_detail.php`):**
  1. `save_annotation`: Đã bổ sung header `'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''`
  2. `delete_annotation`: Đã bổ sung header `'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''`
  3. `save_submission_annotation`: Đã bổ sung header `'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''`
  4. `delete_submission_annotation`: Đã bổ sung header `'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''`

---

## 4. KẾT QUẢ KIỂM THỬ VÀ ĐÁNH GIÁ AN TOÀN

### 4.1. Kết quả thực thi `tests/test_csrf.php`
Bộ kiểm thử tự động gồm 11 test cases đã vượt qua với tỷ lệ thành công tuyệt đối:
- `[PASS]` 1. Token sinh ra có độ dài 64 ký tự hex (32 bytes cryptographically secure).
- `[PASS]` 2. Token được duy trì nhất quán trong Session của user.
- `[PASS]` 3. `Csrf::field()` sinh đúng thẻ input hidden với token đã mã hóa HTML.
- `[PASS]` 4. `Csrf::validate()` xác thực thành công khi token truyền vào khớp hoàn toàn.
- `[PASS]` 5. `Csrf::validate()` từ chối khi thiếu token (null hoặc rỗng).
- `[PASS]` 6. `Csrf::validate()` từ chối khi token sai hoàn toàn / token rác ngẫu nhiên.
- `[PASS]` 7. `Csrf::validate()` từ chối khi token bị sửa đổi dù chỉ 1 ký tự (tampered).
- `[PASS]` 8. `Csrf::getTokenFromRequest()` nhận diện thành công `X-CSRF-TOKEN` từ HTTP Header.
- `[PASS]` 9. `X-CSRF-TOKEN` header sai bị từ chối xác thực.
- `[PASS]` 10. `Csrf::getTokenFromRequest()` nhận diện thành công form POST parameter `csrf_token`.
- `[PASS]` 11. **Bảo vệ Zero Side-Effects**: Database và Model state không bị thay đổi khi CSRF invalid.

### 4.2. Kết quả kiểm tra cú pháp toàn dự án (`php -l`)
- **Tổng số tệp tin PHP được kiểm tra:** 91 files
- **Số tệp tin có lỗi cú pháp:** 0 files (100% Clean)

---

## 5. KẾT LUẬN
Hệ thống Manga PMS đã hoàn tất nâng cấp **CSRF Security Hardening** toàn diện, tuân thủ nghiêm ngặt các tiêu chuẩn bảo mật OWASP, bảo vệ toàn diện các thao tác nhạy cảm và hoàn toàn không làm gián đoạn trải nghiệm người dùng hoặc các luồng nghiệp vụ hiện tại.
