# Báo Cáo Module Submission

## 1. Tổng quan
Module Submission quản lý việc nộp sản phẩm hoàn thiện trong hệ thống. Assistant nộp bản thảo cho Task đã hoàn thành để Mangaka phê duyệt, và Mangaka nộp bản vẽ Chapter hoàn chỉnh cho Tantou Editor đánh giá.

## 2. Các File Chính
- **Controller**: [SubmissionController.php](file:///d:/xampp/htdocs/management_system/controllers/SubmissionController.php)
- **Model**: [Submission.php](file:///d:/xampp/htdocs/management_system/models/Submission.php)
- **Views**:
  - [upload_submission.php](file:///d:/xampp/htdocs/management_system/views/assistant/upload_submission.php) (Assistant nộp bản vẽ cho Task)
  - [submission_create.php](file:///d:/xampp/htdocs/management_system/views/mangaka/submission_create.php) (Mangaka nộp Chapter cho Editor)
  - [submission_list.php](file:///d:/xampp/htdocs/management_system/views/editor/submission_list.php) (Xem danh sách bản thảo nộp - Editor duyệt, Assistant/Mangaka xem lịch sử)
  - [submission_detail.php](file:///d:/xampp/htdocs/management_system/views/editor/submission_detail.php) (Xem chi tiết bản thảo và thực hiện hành động liên quan)

## 3. Chức Năng Cốt Lõi
- **Upload Bản Thảo**: Assistant tải tệp tin lên hệ thống liên kết với một Task chưa hoàn chỉnh. Mangaka tải tệp tin liên kết với một Chapter thuộc Series của mình.
- **Xóa Bản Thảo**: Người nộp có quyền xóa bản thảo của mình nếu nó vẫn đang ở trạng thái `pending` (chờ đánh giá). Hành động xóa yêu cầu phương thức `POST` và thực hiện xóa tệp tin vật lý khỏi đĩa cứng của máy chủ để tránh rác dung lượng.
- **Xem Chi Tiết & Tải về**: Cho phép xem trước hình ảnh trực tiếp hoặc tải xuống tệp PDF/ZIP gốc để phục vụ đánh giá.

## 4. Bảo Mật Upload & Tệp Tin Giả Mạo
Hệ thống áp dụng các kiểm tra bảo mật cực kỳ nghiêm ngặt đối với tệp tin tải lên:
- **Kiểm tra MIME thật**: Sử dụng `finfo_file()` với chế độ `FILEINFO_MIME_TYPE` để lấy MIME type thực tế của nội dung tệp tin tải lên, đối chiếu với Whitelist MIME. Tránh việc người dùng đổi tên đuôi file nguy hại (ví dụ: đổi `.php` thành `.jpg`).
- **Xác thực ảnh thật**: Với các định dạng ảnh (`jpg`, `jpeg`, `png`), hệ thống gọi hàm `getimagesize()` để đảm bảo tệp tin thực sự chứa cấu trúc dữ liệu ảnh hợp lệ.
- **Xác thực PDF Signature**: Các tệp tin PDF được kiểm tra signature bắt đầu bằng `%PDF`.
- **Xác thực ZIP Signature**: Các tệp tin ZIP được kiểm tra signature bắt đầu bằng `PK\x03\x04`.

## 5. Quyền Sở Hữu (Ownership) & Phân quyền
- **Phân quyền nộp bài**: Assistant chỉ được nộp bản thảo cho Task được giao đích danh cho mình. Mangaka chỉ được nộp Chapter cho Series do chính mình sáng tác.
- **Xem bản thảo**: Editor xem tất cả bản thảo pending. Assistant chỉ được xem bản thảo của họ. Mangaka chỉ được xem bản thảo của họ hoặc bản thảo do Assistant nộp cho Task thuộc truyện của Mangaka.
- **Xóa bản thảo**: Chỉ cho phép người nộp xóa bản thảo của họ khi ở trạng thái `pending`. Bắt buộc kiểm tra quyền sở hữu bằng cách so sánh `user_id` trong bản ghi với `$_SESSION['user_id']`.
- **Bảo vệ bằng POST**: Chuyển đổi toàn bộ thao tác xóa từ `GET` sang `POST` để ngăn chặn các cuộc tấn công CSRF.
