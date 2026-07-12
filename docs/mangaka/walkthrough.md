# Hướng Dẫn Kiểm Tra Giao Diện & Kết Quả Thực Hiện (Walkthrough)

Tài liệu này hướng dẫn cách kiểm tra và nghiệm thu các công việc đã thực hiện cho **Module Mangaka – Người 2 (UI Cleanup & Documentation)**.

## 1. Kiểm Tra Việt Hóa Giao Diện (UI Localization)

Đăng nhập với tài khoản người dùng có vai trò **Mangaka** và kiểm tra các màn hình sau:

### A. Quản lý Bộ Truyện (Series)
1. **Danh sách & Tạo mới:** Truy cập menu **Dự án Truyện** (`index.php?controller=series&action=index`). Nhấn **Tạo Truyện Mới** (`action=create`).
   - *Xác minh:* Giao diện hiển thị các nhãn tiếng Việt chuẩn: "Tên Series", "Đường dẫn ảnh bìa (URL)", "Trạng thái", "Mô tả", và nút "Tạo Series".
2. **Chi tiết & Chỉnh sửa:** Nhấn **Xem** hoặc **Sửa** một bộ truyện.
   - *Xác minh:* Màn hình hiển thị "Chưa có ảnh bìa", "Mô tả / Tóm tắt", "Danh sách Chapter", "+ Tạo Chapter mới".

### B. Quản lý Chương (Chapter) & Trang (Page)
1. **Chi tiết Chapter:** Truy cập chi tiết một Chapter (`index.php?controller=chapter&action=show&id=...`).
   - *Xác minh:* Bảng hiển thị các cột "Trang #", "Ảnh thu nhỏ", "Trạng thái", "Cập nhật lần cuối", "Thao tác" và nút "+ Thêm trang".
2. **Quản lý công việc trên Trang (Task):** Truy cập chi tiết một Trang truyện (`index.php?controller=page&action=show&id=...`).
   - *Xác minh:* Khối dưới cùng hiển thị "Quản lý công việc", nút "+ Tạo công việc", tiêu đề các cột bảng Task và nhãn "Hạn chót".

## 2. Kiểm Tra Dọn Dẹp File Thừa (Cleanup Verification)
- 3 file `assign_task.php`, `chapter.php`, `pages.php` trong thư mục `views/mangaka/` đã được xác minh không có liên kết hay include nào sử dụng. Đã được làm sạch và ghi chú xác nhận sẵn sàng xóa khỏi bộ nhớ.

## 3. Kiểm Tra Bộ Tài Liệu Kỹ Thuật (Documentation)
Bộ tài liệu hoàn chỉnh đã được lưu tại thư mục [docs/mangaka/](file:///d:/xampp/htdocs/BTapManga/docs/mangaka/):
- [module_summary.md](file:///d:/xampp/htdocs/BTapManga/docs/mangaka/module_summary.md): Tổng quan cấu trúc module Mangaka.
- [mangaka_report.md](file:///d:/xampp/htdocs/BTapManga/docs/mangaka/mangaka_report.md): Báo cáo chi tiết công việc Người 2.
- [mangaka_bug_list.md](file:///d:/xampp/htdocs/BTapManga/docs/mangaka/mangaka_bug_list.md): Tổng hợp danh sách lỗi & ghi chú cho Người 1.
- [mangaka_verification.md](file:///d:/xampp/htdocs/BTapManga/docs/mangaka/mangaka_verification.md): Bảng xác minh giao diện và ranh giới công việc.
- [mangaka_test_result.md](file:///d:/xampp/htdocs/BTapManga/docs/mangaka/mangaka_test_result.md): Báo cáo kết quả kiểm thử.
- [walkthrough.md](file:///d:/xampp/htdocs/BTapManga/docs/mangaka/walkthrough.md): Hướng dẫn nghiệm thu công việc.
