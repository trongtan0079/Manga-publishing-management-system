-- ==============================================================================
-- CƠ SỞ DỮ LIỆU: Manga Creation Workflow and Publishing Management System
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS manga_workflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE manga_workflow;

-- ------------------------------------------------------------------------------
-- 1. Bảng roles: Lưu trữ thông tin về các vai trò người dùng trong hệ thống
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) COMMENT 'Bảng phân quyền, lưu trữ các vai trò (admin, mangaka, assistant, editor, board)';

-- ------------------------------------------------------------------------------
-- 2. Bảng users: Lưu trữ tài khoản người dùng
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    is_head_board TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT,
    INDEX idx_users_role (role_id)
) COMMENT 'Lưu trữ thông tin chi tiết và tài khoản đăng nhập của người dùng';

-- ------------------------------------------------------------------------------
-- 3. Bảng series: Quản lý thông tin từng bộ truyện Manga
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS series (
    series_id INT AUTO_INCREMENT PRIMARY KEY,
    mangaka_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('planning', 'ongoing', 'completed', 'canceled', 'suspended') DEFAULT 'planning',
    publish_type VARCHAR(50) DEFAULT 'weekly',
    cover_image VARCHAR(255),
    proposal_file VARCHAR(255),
    editor_id INT NULL,
    dossier_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_series_mangaka FOREIGN KEY (mangaka_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_series_editor FOREIGN KEY (editor_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_series_mangaka (mangaka_id)
) COMMENT 'Lưu trữ thông tin dự án/bộ truyện Manga của Tác giả (Mangaka)';

-- ------------------------------------------------------------------------------
-- 4. Bảng chapters: Quản lý các chương (chapter) của một Series
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS chapters (
    chapter_id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    chapter_number INT NOT NULL,
    title VARCHAR(255),
    status ENUM('drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting',
    due_date DATETIME,
    published_at DATETIME,
    is_final TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_chapters_series FOREIGN KEY (series_id) REFERENCES series(series_id) ON DELETE CASCADE,
    UNIQUE KEY unique_chapter (series_id, chapter_number),
    INDEX idx_chapters_series (series_id)
) COMMENT 'Quản lý các chương truyện. UNIQUE constraint đảm bảo không trùng số thứ tự chương trong 1 series';

-- ------------------------------------------------------------------------------
-- 5. Bảng pages: Quản lý từng trang truyện thuộc một Chapter
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pages (
    page_id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT NOT NULL,
    page_number INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    old_image_url VARCHAR(255) DEFAULT NULL,
    status ENUM('drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pages_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id) ON DELETE CASCADE,
    UNIQUE KEY unique_page (chapter_id, page_number),
    INDEX idx_pages_chapter (chapter_id)
) COMMENT 'Lưu trữ các trang truyện. UNIQUE constraint đảm bảo 1 chương không bị trùng lặp số thứ tự trang';

-- ------------------------------------------------------------------------------
-- 5b. Bảng page_regions: Lưu trữ các vùng phân đoạn (khung truyện, bong bóng thoại...)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS page_regions (
    region_id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    region_type ENUM('panel', 'bubble', 'character', 'background', 'sfx') NOT NULL,
    x INT NOT NULL,
    y INT NOT NULL,
    width INT NOT NULL,
    height INT NOT NULL,
    status ENUM('pending', 'in_progress', 'submitted', 'completed', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_regions_page FOREIGN KEY (page_id) REFERENCES pages(page_id) ON DELETE CASCADE
) COMMENT 'Lưu trữ thông tin chi tiết về từng phân vùng vẽ tay thủ công trên trang truyện';

-- ------------------------------------------------------------------------------
-- 6. Bảng tasks: Quản lý công việc do Mangaka phân công cho Assistant
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    page_region_id INT NULL,
    grouped_region_ids VARCHAR(255) NULL,
    mangaka_id INT NOT NULL,
    assistant_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    task_type ENUM('background', 'inking', 'coloring', 'effects', 'other') DEFAULT 'other',
    description TEXT,
    resource_url VARCHAR(255) NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'submitted', 'completed', 'rejected') DEFAULT 'pending',
    due_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_page FOREIGN KEY (page_id) REFERENCES pages(page_id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_region FOREIGN KEY (page_region_id) REFERENCES page_regions(region_id) ON DELETE SET NULL,
    CONSTRAINT fk_tasks_mangaka FOREIGN KEY (mangaka_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_tasks_assistant FOREIGN KEY (assistant_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_tasks_users (mangaka_id, assistant_id)
) COMMENT 'Lưu trữ công việc (như đổ tone, vẽ nền) được giao cho các trợ lý trên một trang hoặc một vùng cụ thể';

-- ------------------------------------------------------------------------------
-- 7. Bảng submissions: Quản lý việc nộp sản phẩm 
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS submissions (
    submission_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    task_id INT NULL,
    chapter_id INT NULL,
    file_url VARCHAR(255),
    notes TEXT,
    status ENUM('pending', 'reviewed', 'approved', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_submissions_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_submissions_task FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE,
    CONSTRAINT fk_submissions_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id) ON DELETE CASCADE,
    INDEX idx_submissions_target (task_id, chapter_id)
) COMMENT 'Lưu trữ lịch sử nộp bản vẽ của Assistant cho Task, hoặc Mangaka nộp Chapter cho Editor';

-- ------------------------------------------------------------------------------
-- 8. Bảng reviews: Quản lý phản hồi và đánh giá về Submission
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    comments TEXT NOT NULL,
    rating INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(user_id) ON DELETE RESTRICT
) COMMENT 'Lưu trữ các lời phê bình, góp ý của Editor/Mangaka trên từng lần submit';

-- ------------------------------------------------------------------------------
-- 9. Bảng series_rankings: Xếp hạng Series theo chu kỳ
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS series_rankings (
    ranking_id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    board_member_id INT NOT NULL,
    rank_position INT NOT NULL,
    score DECIMAL(5,2),
    votes INT DEFAULT 0,
    period_start_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rankings_series FOREIGN KEY (series_id) REFERENCES series(series_id) ON DELETE CASCADE,
    CONSTRAINT fk_rankings_board FOREIGN KEY (board_member_id) REFERENCES users(user_id) ON DELETE RESTRICT
) COMMENT 'Lưu trữ đánh giá xếp hạng định kỳ của Hội đồng biên tập (Editorial Board) dành cho các bộ truyện';

-- ------------------------------------------------------------------------------
-- 10. Bảng notifications: Quản lý thông báo hệ thống
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    related_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_notifications_user (user_id, is_read)
) COMMENT 'Lưu trữ thông báo trong hệ thống, đánh dấu đã đọc hoặc chưa';

-- ------------------------------------------------------------------------------
-- 11. Bảng system_logs: Nhật ký hoạt động hệ thống
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_system_logs_user (user_id)
) COMMENT 'Nhật ký hoạt động nhạy cảm trong hệ thống như đăng nhập, thay đổi thông tin người dùng';

-- ------------------------------------------------------------------------------
-- SCHEMA PATCHES / UPGRADES (Đảm bảo đồng bộ cột/kiểu dữ liệu nếu bảng đã tồn tại)
-- ------------------------------------------------------------------------------
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_head_board TINYINT DEFAULT 0 AFTER status;
ALTER TABLE pages ADD COLUMN IF NOT EXISTS old_image_url VARCHAR(255) DEFAULT NULL AFTER image_url;
ALTER TABLE chapters MODIFY COLUMN status ENUM('drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting';
ALTER TABLE pages MODIFY COLUMN status ENUM('drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published') DEFAULT 'drafting';

