# Báo cáo: Role, Permission & Ownership Review

## 1. Cơ chế phân quyền chung (Roles)
Hệ thống sử dụng cơ chế kiểm tra Session cứng (`requireRole($roleName)`) tại Controller Constructor để cấm hoàn toàn các Role trái phép xâm nhập vào chức năng không thuộc thẩm quyền.
- **Admin**: `UserController`, `DashboardController::admin()`
- **Mangaka**: `SeriesController`, `ChapterController`, `PageController`, `TaskController::create/store/delete`
- **Assistant**: `TaskController::index`
- **Editor**: `ReviewController`, `DashboardController::editor()`
- **Board**: `DashboardController::board()`, `SeriesRankingController`

Hệ thống hoạt động đúng theo đặc tả yêu cầu, không phát hiện lỗ hổng cho phép Role A truy cập Dashboard hoặc Controller của Role B.

## 2. Quyền sở hữu dữ liệu (Data Ownership)
Việc phân quyền theo Role là chưa đủ nếu người dùng có thể can thiệp vào ID của người khác (VD: Mangaka A sửa truyện của Mangaka B). Hệ thống đã cài đặt rất tốt các lớp kiểm tra (Ownership Checks):

- **Series, Chapter, Page:** Các Controller đều kiểm tra ngược từ ID được cấp về `mangaka_id` trong bảng Series. `checkSeriesOwnership()` và `checkChapterOwnership()` hoạt động rất nghiêm ngặt.
- **Tasks:**
  - *Mangaka*: Chỉ được tạo/sửa/xóa Task trên các Page thuộc quyền sở hữu của mình.
  - *Assistant*: Chỉ được cập nhật trạng thái (update status) của các Task do đích danh mình được giao (`task['assistant_id'] == $_SESSION['user_id']`). Không thể sửa Tiêu đề, Hạn chót, v.v.
- **Submissions:**
  - Logic phân quyền để xem (Show) rất thông minh khi `Submission.php` join với các bảng liên quan để định danh `mangaka_id`. Mangaka có thể xem các bản thảo của Assistant nếu Task đó do Mangaka tạo ra.
  - Chỉ người nộp (`user_id`) mới được xóa Submission và chỉ khi nó đang ở trạng thái `pending`.
- **Notifications:** Kiểm tra chính xác `notification['user_id'] == $_SESSION['user_id']` trước khi markAsRead.
- **Reviews:** Quyền Review được giới hạn cho Editor (với Chapters) và Mangaka (với Tasks của chính mình).

## 3. Kết luận về Phân quyền & Quyền sở hữu
- 100% tuân thủ thiết kế và bảo vệ toàn vẹn dữ liệu.
- Mọi chức năng `update` và `delete` đều chỉ thực hiện sau khi so sánh ID phiên đăng nhập với `mangaka_id`, `assistant_id`, hoặc `user_id` chủ quản.
- Không cần sửa đổi thêm phần Permission và Ownership vì chúng đã quá tốt và an toàn.
