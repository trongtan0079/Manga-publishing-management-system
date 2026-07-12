# Báo Cáo Lỗi (Bug List) - Module Series, Chapter, Page

Trong quá trình rà soát và hoàn thiện, các điểm yếu/lỗ hổng (bug/vulnerabilities) sau đã được phát hiện và xử lý:

## 1. Lỗi thiếu Validation chiều dài Title (Series & Chapter)
- **Tình trạng cũ:** Chỉ kiểm tra rỗng, không giới hạn độ dài có thể gây lỗi Database `Data too long for column`.
- **Xử lý:** Bổ sung `mb_strlen($title) > 255` để báo lỗi nếu vượt quá giới hạn.

## 2. Lỗ hổng giả mạo đuôi file (File Upload - Page)
- **Tình trạng cũ:** Hàm `handleImageUpload` chỉ kiểm tra đuôi file (`$extension = pathinfo(...)`). Kẻ gian có thể đổi tên file `.php` thành `.jpg` và upload lên server.
- **Xử lý:** Bổ sung việc kiểm tra MIME Type thực tế của nội dung file thông qua `finfo_file($finfo, $file['tmp_name'])` (chỉ cho phép `image/jpeg`, `image/png`, `image/webp`).

## 3. Kiểm tra lại Ownership 
- **Tình trạng cũ:** Code hiện tại đã có cấu trúc `checkOwnership`, `checkSeriesOwnership`, `checkChapterOwnership`.
- **Xử lý:** Đã rà soát và xác nhận các hàm này hoạt động tốt, chặn đứng việc đổi ID trên URL để sửa dữ liệu của tác giả khác. Không cần chỉnh sửa thêm.

## 4. Quản lý File Rác (Xóa Page)
- **Tình trạng cũ:** Quá trình `delete` Page đã có hàm `unlink($filePath)` để xóa file ảnh cũ khi người dùng nhấn nút Xóa. Tuy nhiên, khi "Sửa" Page và đổi ảnh, ảnh cũ không bị xóa (yêu cầu nghiệp vụ giữ lại - không xem là bug).
- **Xử lý:** Đảm bảo hàm `unlink` không phát sinh lỗi (kiểm tra `file_exists`).
