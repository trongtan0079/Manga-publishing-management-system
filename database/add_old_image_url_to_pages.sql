-- Script SQL cập nhật cấu trúc cơ sở dữ liệu:
-- Bổ sung cột old_image_url vào bảng pages để lưu trữ ảnh cũ phục vụ việc so sánh đối chiếu lỗi.
--
-- LƯU Ý: Nếu cột 'old_image_url' đã tồn tại, MySQL sẽ báo lỗi "#1060 - Duplicate column name".
-- Lỗi này hoàn toàn bình thường, chứng tỏ cơ sở dữ liệu của bạn đã được nâng cấp thành công rồi và không cần chạy lại câu lệnh này nữa.

ALTER TABLE pages ADD COLUMN old_image_url VARCHAR(255) DEFAULT NULL AFTER image_url;
