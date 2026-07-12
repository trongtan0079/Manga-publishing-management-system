# Tóm Tắt Module Series, Chapter, Page (Module Summary)

## Mục Đích
Bộ ba module (Series, Chapter, Page) là trái tim của hệ thống Manga Publishing Management System, cho phép các tác giả (Mangaka) tạo ra các bộ truyện mới, xây dựng chương hồi và tải lên các trang truyện. 

## Cấu Trúc MVC Hoàn Chỉnh
- **Controllers:** `SeriesController`, `ChapterController`, `PageController` điều phối toàn bộ luồng request, kiểm tra Role `mangaka` (Guest không có quyền vào) và áp đặt Ownership.
- **Models:** `Series`, `Chapter`, `Page` giao tiếp Database bằng PDO Prepared Statements, tránh SQL Injection triệt để. Có các hàm nghiệp vụ tiện ích như `isChapterNumberExists`, `isPageNumberExists`.
- **Views:** Tách biệt HTML và Logic, dùng chung Layout UI `header`, `sidebar`, `footer`.

## Bảo Mật & Ổn Định
- Validation được thiết kế vừa đủ (không quá khắt khe, không quá lỏng lẻo).
- Image Upload an toàn thông qua kiểm tra MIME type.
- Việc xóa tài nguyên DB đi kèm dọn dẹp ổ cứng (unlink hình ảnh).

Module này đã đạt trạng thái Sẵn Sàng (Production-Ready) cho chức năng của Mangaka.
