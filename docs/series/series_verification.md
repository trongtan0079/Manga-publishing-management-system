# Kế Hoạch Nghiệm Thu (Verification) - Module Series

Các bước kiểm tra để đảm bảo module hoạt động bình thường sau khi sửa lỗi:

## 1. Rà soát Mã Nguồn (Code Review)
- MVC: Model xử lý Database, Controller xử lý Request/Nghiệp vụ, View chỉ hiển thị dữ liệu HTML/PHP cơ bản. Đã thỏa mãn.
- TODO / Debug: Đã kiểm tra qua `SeriesController`, `ChapterController`, `PageController` không chứa `var_dump`, `print_r` hoặc `die`.

## 2. Kiểm tra Ownership
- Truy cập `index.php?controller=series&action=edit&id=X` (với X là ID của truyện Mangaka khác) -> Bị đẩy về `index.php?controller=series&action=index` kèm thông báo lỗi.
- Đã kiểm tra tương tự với Chapter và Page.

## 3. Kiểm tra Validation
- Để trống Title khi tạo Series/Chapter -> Báo lỗi.
- Điền chuỗi > 255 ký tự -> Báo lỗi.
- `chapter_number`, `page_number` rỗng hoặc < 1 -> Báo lỗi.
- Nhập trùng số chapter, trùng số page -> Báo lỗi đúng nghiệp vụ.

## 4. Kiểm tra Tải Ảnh (Upload)
- Upload file đuôi `.png` nhưng ruột là code `.php` -> Bị chặn bởi `finfo_file` (MIME Type).
- Upload file lớn hơn 2MB -> Bị chặn giới hạn dung lượng.
- Xóa Page -> File vật lý được `unlink`.

Đã đạt các bước Verification cần thiết.
