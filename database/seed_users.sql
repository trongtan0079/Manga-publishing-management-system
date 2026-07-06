-- database/seed_users.sql
-- Thêm dữ liệu roles (Sử dụng IGNORE để tránh lỗi khi chạy lại file nhiều lần)
INSERT IGNORE INTO roles (role_name, description) VALUES 
('admin', 'Quản trị viên hệ thống'),
('mangaka', 'Tác giả truyện tranh'),
('assistant', 'Trợ lý tác giả'),
('editor', 'Biên tập viên'),
('board', 'Ban giám đốc');

-- Tạo password_hash cho các user (Mật khẩu mặc định: password123)
-- Sử dụng BCRYPT hash của 'password123'
-- Hash: $2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG

-- Lấy role_id tương ứng
SET @admin_role = (SELECT role_id FROM roles WHERE role_name = 'admin');
SET @mangaka_role = (SELECT role_id FROM roles WHERE role_name = 'mangaka');
SET @assistant_role = (SELECT role_id FROM roles WHERE role_name = 'assistant');
SET @editor_role = (SELECT role_id FROM roles WHERE role_name = 'editor');
SET @board_role = (SELECT role_id FROM roles WHERE role_name = 'board');

-- Thêm dữ liệu users (Sử dụng IGNORE để tránh trùng lặp UNIQUE username/email)
INSERT IGNORE INTO users (username, full_name, email, password_hash, role_id) VALUES 
('admin_user', 'System Admin', 'admin@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @admin_role),
('mangaka_user', 'Mangaka Author', 'mangaka@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @mangaka_role),
('assistant_user', 'Assistant One', 'assistant@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @assistant_role),
('editor_user', 'Editor One', 'editor@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @editor_role),
('board_user', 'Board Member', 'board@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @board_role);
