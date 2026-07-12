# Bug List & Fixes
1. **Lỗi HTML Form Nested**: Thẻ `<form>` nằm lồng trong thẻ `<a>` ở danh sách thông báo. Đã sửa đổi thẻ `<a>` thành thẻ `<div>` trong file `dashboard_notifications.php`.
2. **Lỗi URL Tampering (Ranking)**: Chỉnh sửa ép kiểu `$id = (int)$id;` và xác nhận bản ghi tồn tại trong Database trước khi thực hiện cập nhật.
3. **Thiếu Validation (Ranking)**: Đã thêm kiểm tra `series_id` tồn tại, `rank_position >= 1`, và `score` (0-100) ở hàm `store` và `update`.
4. **Thiếu Method Check (Notification)**: Đã giới hạn các hàm `markAsRead` và `markAllAsRead` chỉ chấp nhận phương thức POST.