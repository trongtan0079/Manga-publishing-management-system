# Walkthrough: Authentication, User & Permission Module

## 1. Công việc đã thực hiện

Theo yêu cầu từ bạn (ưu tiên sửa lỗi trực tiếp mà không thay đổi kiến trúc), tôi đã thực hiện một cuộc rà soát và khắc phục các vấn đề liên quan đến Auth, Quản lý User và Phân quyền:

### A. Authentication & Session
- **AuthController.php:**
  - Cập nhật hàm `authenticate` để chặn những tài khoản có trạng thái khóa (inactive) đăng nhập.
  - Cập nhật hàm `authenticate` thêm `session_regenerate_id(true)` ngay khi người dùng nhập đúng Password. Điều này vá lỗi bảo mật nghiêm trọng **Session Fixation**.
- Đã xác minh luồng `logout()` xử lý xóa sạch session an toàn, các thông báo lỗi hiển thị chính xác.

### B. User Management
- **UserController.php:**
  - Bổ sung hàm `filter_var` để đảm bảo hệ thống không nhận chuỗi Email định dạng sai khi Create/Update user.
  - Bổ sung bước tra cứu DB để xác minh `role_id` từ phía Client thực sự là Role tồn tại.
  - Cập nhật điều kiện lọc trạng thái (chỉ nhận `active` / `inactive`).
  - Thiết lập độ dài mật khẩu an toàn (>= 6 ký tự) nếu Admin có cung cấp mật khẩu.
  - Bảo vệ Admin bằng cách cấm họ xóa (`delete`) chính tài khoản của bản thân.

### C. Permission & Data Ownership
- Tiến hành rà soát kỹ lưỡng toàn bộ cơ chế phân quyền (dựa trên Role) và cấp quyền sở hữu dữ liệu ở các module chính:
  - **Series, Chapter, Page**: Chỉ Mangaka của Series mới có quyền tạo, sửa, xóa dữ liệu tương ứng (sử dụng `checkSeriesOwnership`, `checkChapterOwnership`, `checkPageOwnership`).
  - **Task**: Mangaka chỉ được giao Task liên quan đến Page của mình. Assistant chỉ được update trạng thái các Task được giao cụ thể cho mình.
  - **Submission**: Người dùng chỉ được sửa/xóa bản thảo pending do chính mình upload. Mangaka có thể review (gián tiếp xem) bản thảo do Assistant upload cho Task của họ.
  - **Review**: Editor chỉ Review Chapter. Mangaka chỉ Review Task.
- Cơ chế `requireRole()` hoạt động đúng theo đặc tả và không có lỗ hổng rò rỉ dữ liệu chéo (Cross-tenant data access).

### D. Kiến trúc MVC & Bảo vệ cơ bản
- Controller tuân thủ luật: không chèn HTML.
- View tuân thủ luật: không trực tiếp Query DB và đều dùng `htmlspecialchars()` để chống XSS.
- Model sử dụng `Prepared Statements` (với PDO) đầy đủ trên mọi tính năng chống hoàn toàn SQL Injection.

## 2. Kết quả (Deliverables)
Toàn bộ các tài liệu đánh giá và báo cáo đã được tôi xuất thành file đầy đủ. Bạn có thể nhấn vào các liên kết sau để xem chi tiết:
- [authentication_report.md](file:///C:/Users/Admin/.gemini/antigravity-ide/brain/dbe38186-1c16-4a81-a089-3ca9decd5a80/authentication_report.md)
- [user_management_report.md](file:///C:/Users/Admin/.gemini/antigravity-ide/brain/dbe38186-1c16-4a81-a089-3ca9decd5a80/user_management_report.md)
- [permission_report.md](file:///C:/Users/Admin/.gemini/antigravity-ide/brain/dbe38186-1c16-4a81-a089-3ca9decd5a80/permission_report.md)
- [authentication_bug_list.md](file:///C:/Users/Admin/.gemini/antigravity-ide/brain/dbe38186-1c16-4a81-a089-3ca9decd5a80/authentication_bug_list.md)

## 3. Khuyến nghị Verification
Các bản sửa lỗi đã có hiệu lực ngay lập tức. Để xác thực, bạn có thể kiểm tra nhanh bằng cách:
1. Sửa trạng thái một tài khoản sang `inactive` rồi thử dùng tài khoản đó đăng nhập. Hệ thống sẽ từ chối.
2. Thử xóa tài khoản Admin đang login hiện tại. Hệ thống sẽ báo "Bạn không thể tự xóa chính mình".
3. Thử đổi mật khẩu cho user nhưng nhập 3 ký tự (Ví dụ: `123`). Hệ thống sẽ báo lỗi yêu cầu từ 6 ký tự trở lên.
