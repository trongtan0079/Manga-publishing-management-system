# Báo Cáo Module Chapter

## 1. Tổng quan
Module Chapter quản lý các chương truyện của một Series. Nó cho phép Mangaka tạo các chapter, đánh số và quản lý trạng thái của chúng (Drafting, Drawing, Reviewing, Approved, Published).

## 2. Các File Chính
- `controllers/ChapterController.php`
- `models/Chapter.php`
- `views/mangaka/chapter_create.php`
- `views/mangaka/chapter_edit.php`
- `views/mangaka/chapter_detail.php`

## 3. Chức Năng (CRUD)
- **Tạo Chapter:** Bắt buộc có `chapter_number` > 0 và `title` hợp lệ (<= 255 ký tự).
- **Xem Chapter:** Xem danh sách chapter trong trang `series_detail.php`, xem chi tiết trang (Pages) bên trong `chapter_detail.php`.
- **Sửa Chapter:** Cập nhật thông tin số chapter, tiêu đề, trạng thái.
- **Xóa Chapter:** Xóa chương truyện và các trang đi kèm (nếu DB hỗ trợ ON DELETE CASCADE hoặc xử lý trong code).

## 4. Quyền Sở Hữu (Ownership) & Bảo Mật
Kiểm tra bảo mật thông qua `checkSeriesOwnership($seriesId)` để đảm bảo:
- Chapter đó thuộc về một Series cụ thể.
- Series đó thuộc quyền sở hữu của Mangaka hiện tại.
Ngăn chặn hoàn toàn việc can thiệp bằng cách sửa `id` hoặc `series_id` trên URL.

## 5. Validation 
- Chống trùng lặp `chapter_number` trong cùng một Series thông qua `isChapterNumberExists`.
- Title có giới hạn độ dài để tránh lỗi DB.
