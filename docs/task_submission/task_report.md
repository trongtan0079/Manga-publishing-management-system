# Báo Cáo Module Task

## 1. Tổng quan
Module Task chịu trách nhiệm quản lý công việc sáng tác của studio. Mangaka sở hữu bộ truyện có quyền tạo và giao Task cho các Assistant trên từng trang truyện (Page) cụ thể (ví dụ: vẽ nền, tô bóng, dán tone, vẽ hiệu ứng...). Assistant được giao Task sẽ theo dõi công việc của mình trên Dashboard cá nhân và cập nhật tiến độ công việc.

## 2. Các File Chính
- **Controller**: [TaskController.php](file:///d:/xampp/htdocs/management_system/controllers/TaskController.php)
- **Model**: [Task.php](file:///d:/xampp/htdocs/management_system/models/Task.php)
- **Views**:
  - [task_create.php](file:///d:/xampp/htdocs/management_system/views/mangaka/task_create.php) (Mangaka tạo Task mới)
  - [task_edit.php](file:///d:/xampp/htdocs/management_system/views/mangaka/task_edit.php) (Mangaka chỉnh sửa Task)
  - [task_list.php](file:///d:/xampp/htdocs/management_system/views/assistant/task_list.php) (Assistant xem danh sách và cập nhật trạng thái Task)
  - [page_detail.php](file:///d:/xampp/htdocs/management_system/views/mangaka/page_detail.php) (Hiển thị danh sách Task trên trang của Mangaka)

## 3. Chức Năng Cốt Lõi
- **Tạo Task**: Mangaka chọn một trang truyện (Page), chọn một Assistant từ danh sách, nhập tiêu đề, mô tả, mức độ ưu tiên và hạn chót (tùy chọn) để tạo công việc mới.
- **Chỉnh sửa Task**: Mangaka chỉnh sửa các thông tin của Task đã giao bao gồm tiêu đề, mô tả, người thực hiện, độ ưu tiên, hạn chót và trạng thái.
- **Xóa Task**: Mangaka xóa Task khi công việc không còn cần thiết (chỉ hỗ trợ phương thức POST để bảo mật).
- **Cập nhật Tiến độ**: Assistant cập nhật trạng thái của Task được giao (`pending` -> `in_progress` -> `completed`).

## 4. Quyền Sở Hữu (Ownership) & Bảo mật
- **Kiểm soát Quyền Tác giả**: Sử dụng hàm `checkPageOwnership($pageId)` để xác định trang truyện thuộc về Chapter và Series do Mangaka đang đăng nhập sở hữu. Điều này ngăn chặn việc Mangaka tự tiện giao Task cho truyện của người khác.
- **ID & URL Tampering**: Bất kỳ tham số ID nào truyền qua URL (`?id=...` hoặc `?page_id=...`) đều được ép kiểu `intval()` và xác thực quyền sở hữu nghiêm ngặt trước khi hiển thị form hoặc xử lý DB.
- **Giới hạn Assistant**: Assistant chỉ có quyền cập nhật trạng thái (`status`) của các Task được giao đích danh cho họ, không thể chỉnh sửa thông tin khác của Task.
- **Chặn Guest & các Role khác**: Guest, Editor và Board hoàn toàn không thể truy cập vào các chức năng quản lý Task.
