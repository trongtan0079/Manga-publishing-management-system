-- database/seed_users.sql
-- Thêm dữ liệu roles
INSERT INTO roles (role_name, description) VALUES 
('admin', 'Quản trị viên hệ thống'),
('mangaka', 'Tác giả truyện tranh'),
('assistant', 'Trợ lý tác giả'),
('editor', 'Biên tập viên'),
('board', 'Ban giám đốc');

-- Tạo password_hash cho các user (Mật khẩu mặc định: password123)
-- Sử dụng BCRYPT hash của 'password123'
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

-- Lấy role_id tương ứng
SET @admin_role = (SELECT role_id FROM roles WHERE role_name = 'admin');
SET @mangaka_role = (SELECT role_id FROM roles WHERE role_name = 'mangaka');
SET @assistant_role = (SELECT role_id FROM roles WHERE role_name = 'assistant');
SET @editor_role = (SELECT role_id FROM roles WHERE role_name = 'editor');
SET @board_role = (SELECT role_id FROM roles WHERE role_name = 'board');

-- Thêm dữ liệu users
INSERT INTO users (username, full_name, email, password_hash, role_id) VALUES 
('admin_user', 'System Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', @admin_role),
('mangaka_user', 'Mangaka Author', 'mangaka@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', @mangaka_role),
('assistant_user', 'Assistant One', 'assistant@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', @assistant_role),
('editor_user', 'Editor One', 'editor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', @editor_role),
('board_user', 'Board Member', 'board@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', @board_role);
