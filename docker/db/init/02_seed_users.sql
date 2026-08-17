USE manga_workflow;

-- ============================================================
-- ROLES
-- ============================================================

INSERT IGNORE INTO roles (role_name, description) VALUES
('admin', 'Quản trị viên hệ thống'),
('mangaka', 'Tác giả truyện tranh'),
('assistant', 'Trợ lý tác giả'),
('editor', 'Biên tập viên'),
('board', 'Ban giám đốc');

-- ============================================================
-- ROLE IDs
-- ============================================================

SET @admin_role = (
    SELECT role_id FROM roles WHERE role_name = 'admin'
);

SET @mangaka_role = (
    SELECT role_id FROM roles WHERE role_name = 'mangaka'
);

SET @assistant_role = (
    SELECT role_id FROM roles WHERE role_name = 'assistant'
);

SET @editor_role = (
    SELECT role_id FROM roles WHERE role_name = 'editor'
);

SET @board_role = (
    SELECT role_id FROM roles WHERE role_name = 'board'
);

-- ============================================================
-- USERS
-- Default password: password123
-- ============================================================

INSERT IGNORE INTO users
(username, full_name, email, password_hash, role_id, status)
VALUES
(
    'admin_user',
    'System Admin',
    'admin@example.com',
    '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG',
    @admin_role,
    'active'
),
(
    'mangaka_user',
    'Mangaka Author',
    'mangaka@example.com',
    '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG',
    @mangaka_role,
    'active'
),
(
    'assistant_user',
    'Assistant One',
    'assistant@example.com',
    '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG',
    @assistant_role,
    'active'
),
(
    'editor_user',
    'Editor One',
    'editor@example.com',
    '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG',
    @editor_role,
    'active'
),
(
    'board_user',
    'Board Member (Head)',
    'board@example.com',
    '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG',
    @board_role,
    'active'
);

-- ============================================================
-- BOARD MEMBERS
-- ============================================================

INSERT IGNORE INTO users
(username, full_name, email, password_hash, role_id, status)
VALUES
(
    'board_member_2',
    'Board Member 2',
    'board2@example.com',
    '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG',
    @board_role,
    'active'
),
(
    'board_member_3',
    'Board Member 3',
    'board3@example.com',
    '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG',
    @board_role,
    'active'
);

-- ============================================================
-- HEAD BOARD
-- ============================================================

UPDATE users
SET is_head_board = 1
WHERE username = 'board_user';