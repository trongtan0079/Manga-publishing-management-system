# Báo Cáo Module Series

## 1. Tổng quan
Module Series chịu trách nhiệm quản lý các bộ truyện của Mangaka. Nó cho phép người dùng có quyền Mangaka tạo, xem, chỉnh sửa và xóa bộ truyện.

## 2. Các File Chính
- `controllers/SeriesController.php`
- `models/Series.php`
- `views/mangaka/series.php`
- `views/mangaka/series_create.php`
- `views/mangaka/series_edit.php`
- `views/mangaka/series_detail.php`

## 3. Chức Năng (CRUD)
- **Tạo Series:** Khởi tạo bộ truyện mới với trạng thái mặc định, yêu cầu nhập Title (bắt buộc, <= 255 ký tự).
- **Xem Series:** Liệt kê các Series của Mangaka thông qua `findByMangakaId`. Xem chi tiết Series và các chapter liên kết.
- **Sửa Series:** Cập nhật thông tin Series (Title, Status, Description, Cover Image). 
- **Xóa Series:** Xóa bộ truyện (chỉ xóa khi không còn dữ liệu liên kết hoặc bị ràng buộc FK).

## 4. Quyền Sở Hữu (Ownership)
Sử dụng hàm `checkOwnership($series, $id)` để đảm bảo chỉ Mangaka sở hữu (khớp `mangaka_id` với `$_SESSION['user_id']`) mới được phép thao tác sửa, xóa, hoặc xem chi tiết bộ truyện. Guest và các role khác không có quyền truy cập (thông qua `requireRole('mangaka')`).
