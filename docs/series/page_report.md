# Báo Cáo Module Page

## 1. Tổng quan
Module Page dùng để quản lý các trang truyện (hình ảnh) bên trong một Chapter. 

## 2. Các File Chính
- `controllers/PageController.php`
- `models/Page.php`
- `views/mangaka/page_create.php`
- `views/mangaka/page_edit.php`
- `views/mangaka/page_detail.php`

## 3. Chức Năng (CRUD)
- **Tạo Page:** Cần có `page_number` (duy nhất trong chapter), trạng thái, và upload ảnh.
- **Xem Page:** Hiển thị chi tiết Page và các hình ảnh. 
- **Sửa Page:** Thay đổi thông tin số trang hoặc thay thế ảnh mới.
- **Xóa Page:** Cho phép xóa dữ liệu Database VÀ xóa file ảnh vật lý (`unlink`) để tránh rác lưu trữ.

## 4. Bảo Mật & Validation
- **Ownership:** Hàm `checkChapterOwnership` đảm bảo Mangaka sở hữu chapter trước khi thao tác các trang bên trong.
- **Image Validation:** 
  - Kích thước không vượt quá 2MB.
  - Kiểm tra mở rộng (extension) hợp lệ.
  - **Kiểm tra MIME Type** bằng `finfo_file` (được bổ sung thêm để tăng độ tin cậy và chống hack file ẩn).
- Xóa file ảnh triệt để khi xóa Page, không sinh rác file hệ thống.
