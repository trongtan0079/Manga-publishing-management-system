-- database/create_board_votes_table.sql
USE manga_workflow;

-- 1. Tạo bảng board_votes
CREATE TABLE IF NOT EXISTS board_votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    board_member_id INT NOT NULL,
    vote ENUM('approve', 'reject') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_board_votes_series FOREIGN KEY (series_id) REFERENCES series(series_id) ON DELETE CASCADE,
    CONSTRAINT fk_board_votes_board FOREIGN KEY (board_member_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_board_vote (series_id, board_member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lưu trữ các lượt bỏ phiếu của thành viên Hội đồng biên tập cho đề xuất truyện';

-- 2. Seed thêm 2 thành viên Hội đồng Biên tập (Editorial Board) để đủ hội đồng 3 người
-- Mật khẩu mặc định: password123 (Hash: $2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG)
SET @board_role_id = (SELECT role_id FROM roles WHERE role_name = 'board');

-- Dọn dẹp tài khoản thừa nếu có từ đợt chạy trước
DELETE FROM users WHERE username IN ('board_member_4', 'board_member_5') AND role_id = @board_role_id;

INSERT IGNORE INTO users (username, full_name, email, password_hash, role_id, status) VALUES
('board_member_2', 'Board Member 2', 'board2@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @board_role_id, 'active'),
('board_member_3', 'Board Member 3', 'board3@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @board_role_id, 'active');

-- 3. Tạo sẵn dữ liệu bỏ phiếu ban đầu cho các bộ truyện đang ở trạng thái 'planning' để hiển thị trực quan lập tức
-- Xóa sạch các phiếu bầu cũ của series 'planning' trước khi chèn lại
DELETE FROM board_votes WHERE series_id IN (SELECT series_id FROM series WHERE status = 'planning');

-- board_user, board_member_2 bỏ phiếu 'approve' (2 phiếu)
-- board_member_3 bỏ phiếu 'reject' (1 phiếu)
-- Đảm bảo tỉ lệ đạt đúng 67% (2/3 tán thành) và chạy hoàn toàn tự động
INSERT IGNORE INTO board_votes (series_id, board_member_id, vote)
SELECT s.series_id, u.user_id, IF(u.username IN ('board_user', 'board_member_2'), 'approve', 'reject')
FROM series s
CROSS JOIN users u
JOIN roles r ON u.role_id = r.role_id
WHERE s.status = 'planning' AND r.role_name = 'board';
