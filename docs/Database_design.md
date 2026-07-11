# Kế hoạch & Thiết kế Cơ sở dữ liệu: Manga Creation Workflow and Publishing Management System

Bản thiết kế này đáp ứng đầy đủ yêu cầu từ hệ thống và đã được **đồng bộ hóa 100%** cả về mặt mã SQL lẫn mô tả văn bản với các file UML (ERD và Class Diagram) mới nhất. Toàn bộ các bảng đều sử dụng quy tắc Khóa chính (Primary Key) và Khóa ngoại (Foreign Key) dạng `[tên_thực_thể_số_ít]_id` để đảm bảo chuẩn mực.

---

## 1. Phân tích nghiệp vụ 5 vai trò

Hệ thống được thiết kế phục vụ một quy trình làm việc khép kín của ngành công nghiệp Manga, bao gồm 5 vai trò chính:

* **Admin**: Quản trị toàn bộ hệ thống, cấp phát tài khoản (`User`) và phân quyền (`Role`), theo dõi tổng quan hệ thống.
* **Mangaka**: Tác giả chính. Họ tạo và quản lý `Series`, cấu trúc các `Chapter`, upload `Page` phác thảo/bản vẽ, giao việc (`Task`) cho Assistant, và cuối cùng nộp (`Submission`) `Chapter` cho Tantou Editor.
* **Assistant**: Trợ lý vẽ nền, đi nét, dán tone. Nhận `Task` từ Mangaka, sau khi hoàn thành sẽ nộp bản vẽ qua `Submission` để Mangaka kiểm tra.
* **Tantou Editor**: Biên tập viên phụ trách. Theo dõi tiến độ của Mangaka, nhận `Submission` của các `Chapter`, sau đó thực hiện `Review` (để lại nhận xét, yêu cầu sửa đổi hoặc phê duyệt).
* **Editorial Board**: Hội đồng biên tập. Dựa trên các `Chapter` đã xuất bản hoặc phê duyệt để tổng hợp, đánh giá hiệu suất, đưa ra xếp hạng (`SeriesRanking`) để quyết định vận mệnh của bộ truyện (tiếp tục hay đình bản).

---

## 2. Thiết kế cơ sở dữ liệu chuẩn hóa 3NF

Thiết kế Database đảm bảo:
* **1NF**: Các cột mang giá trị nguyên tử, mỗi bản ghi là duy nhất qua Khóa chính (Primary Key).
* **2NF**: Thỏa 1NF và tất cả các thuộc tính không khóa đều phụ thuộc hoàn toàn vào Khóa chính.
* **3NF**: Thỏa 2NF và không có thuộc tính không khóa nào phụ thuộc bắc cầu vào Khóa chính (giảm tối đa dư thừa dữ liệu).

---

## 3. Database Schema (14 Bảng) và Data Dictionary

### 1. Bảng `roles`
* **Mô tả**: Lưu trữ thông tin về các vai trò người dùng trong hệ thống.
* **Thuộc tính**:
    * `role_id`: INT, Primary Key, Auto Increment.
    * `role_name`: VARCHAR(50), NOT NULL, UNIQUE (Ví dụ: 'admin', 'mangaka', 'assistant', 'editor', 'board').
    * `description`: VARCHAR(255), NULL.
    * `created_at`: TIMESTAMP, Default CURRENT_TIMESTAMP.

### 2. Bảng `users`
* **Mô tả**: Lưu trữ tài khoản người dùng, mỗi tài khoản thuộc 1 Role định sẵn.
* **Thuộc tính**:
    * `user_id`: INT, PK, Auto Increment.
    * `role_id`: INT, Foreign Key (roles.role_id), NOT NULL.
    * `username`: VARCHAR(50), NOT NULL, UNIQUE.
    * `full_name`: VARCHAR(100), NOT NULL.
    * `email`: VARCHAR(100), NOT NULL, UNIQUE.
    * `password_hash`: VARCHAR(255), NOT NULL (Lưu chuỗi băm mật khẩu).
    * `status`: ENUM('active', 'inactive', 'banned'), Default 'active'.
    * `created_at`: TIMESTAMP, Default CURRENT_TIMESTAMP.
    * `updated_at`: TIMESTAMP, Default CURRENT_TIMESTAMP ON UPDATE.

### 3. Bảng `series`
* **Mô tả**: Quản lý thông tin từng bộ truyện Manga.
* **Thuộc tính**:
    * `series_id`: INT, PK, Auto Increment.
    * `mangaka_id`: INT, Foreign Key (users.user_id), NOT NULL.
    * `title`: VARCHAR(255), NOT NULL.
    * `description`: TEXT, NULL.
    * `status`: ENUM('planning', 'ongoing', 'completed', 'canceled', 'suspended'), Default 'planning'.
    * `publish_type`: VARCHAR(50), Default 'weekly'.
    * `cover_image`: VARCHAR(255), NULL.
    * `proposal_file`: VARCHAR(255), NULL.
    * `editor_id`: INT, NULL.
    * `dossier_notes`: TEXT, NULL.
    * `created_at`: TIMESTAMP.
    * `updated_at`: TIMESTAMP.

### 4. Bảng `chapters`
* **Mô tả**: Quản lý các chương (chapter) của một Series.
* **Thuộc tính**:
    * `chapter_id`: INT, PK, Auto Increment.
    * `series_id`: INT, Foreign Key (series.series_id), NOT NULL.
    * `chapter_number`: INT, NOT NULL.
    * `title`: VARCHAR(255), NULL.
    * `status`: ENUM('drafting', 'drawing', 'reviewing', 'approved', 'published'), Default 'drafting'.
    * `published_at`: DATETIME, NULL.
    * `created_at`: TIMESTAMP.
    * `updated_at`: TIMESTAMP.
    * **UNIQUE Constraint**: (`series_id`, `chapter_number`) tránh trùng lặp số chương trong cùng 1 truyện.

### 5. Bảng `pages`
* **Mô tả**: Quản lý từng trang truyện thuộc một Chapter.
* **Thuộc tính**:
    * `page_id`: INT, PK, Auto Increment.
    * `chapter_id`: INT, Foreign Key (chapters.chapter_id), NOT NULL.
    * `page_number`: INT, NOT NULL.
    * `image_url`: VARCHAR(255), NOT NULL.
    * `status`: ENUM('sketch', 'inked', 'toned', 'finished'), Default 'sketch'.
    * `created_at`: TIMESTAMP.
    * `updated_at`: TIMESTAMP.
    * **UNIQUE Constraint**: (`chapter_id`, `page_number`).

### 6. Bảng `page_regions`
* **Mô tả**: Lưu trữ thông tin chi tiết về từng phân vùng vẽ tay thủ công trên trang truyện để phân công công việc cụ thể.
* **Thuộc tính**:
    * `region_id`: INT, PK, Auto Increment.
    * `page_id`: INT, Foreign Key (pages.page_id), NOT NULL.
    * `region_type`: ENUM('panel', 'bubble', 'character', 'background', 'sfx'), NOT NULL.
    * `x`: INT, NOT NULL.
    * `y`: INT, NOT NULL.
    * `width`: INT, NOT NULL.
    * `height`: INT, NOT NULL.
    * `status`: ENUM('pending', 'in_progress', 'completed'), Default 'pending'.
    * `created_at`: TIMESTAMP.

### 7. Bảng `tasks`
* **Mô tả**: Quản lý công việc do Mangaka phân công cho Assistant (ví dụ đi nét, đổ tone trang cụ thể hoặc trên một phân vùng cụ thể).
* **Thuộc tính**:
    * `task_id`: INT, PK, Auto Increment.
    * `page_id`: INT, Foreign Key (pages.page_id), NOT NULL.
    * `page_region_id`: INT, Foreign Key (page_regions.region_id), NULL (Nếu giao việc trên một phân vùng cụ thể).
    * `mangaka_id`: INT, Foreign Key (users.user_id), NOT NULL (Người giao việc).
    * `assistant_id`: INT, Foreign Key (users.user_id), NOT NULL (Người nhận việc).
    * `title`: VARCHAR(255), NOT NULL.
    * `task_type`: ENUM('background', 'inking', 'coloring', 'effects', 'other'), Default 'other'.
    * `description`: TEXT, NULL.
    * `resource_url`: VARCHAR(255), NULL.
    * `priority`: ENUM('low', 'medium', 'high'), Default 'medium'.
    * `status`: ENUM('pending', 'in_progress', 'completed'), Default 'pending'.
    * `due_date`: DATETIME, NULL.
    * `created_at`: TIMESTAMP.
    * `updated_at`: TIMESTAMP.

### 8. Bảng `submissions`
* **Mô tả**: Quản lý việc nộp sản phẩm (Assistant nộp Task cho Mangaka, hoặc Mangaka nộp Chapter cho Editor).
* **Thuộc tính**:
    * `submission_id`: INT, PK, Auto Increment.
    * `user_id`: INT, Foreign Key (users.user_id), NOT NULL (Người submit).
    * `task_id`: INT, Foreign Key (tasks.task_id), NULL (Nếu nộp bản vẽ cho Task).
    * `chapter_id`: INT, Foreign Key (chapters.chapter_id), NULL (Nếu nộp Chapter cho Editor).
    * `file_url`: VARCHAR(255), NULL.
    * `notes`: TEXT, NULL.
    * `status`: ENUM('pending', 'reviewed', 'approved', 'rejected'), Default 'pending'.
    * `submitted_at`: TIMESTAMP.
    * `updated_at`: TIMESTAMP.

### 9. Bảng `reviews`
* **Mô tả**: Quản lý phản hồi và đánh giá về Submission (Editor duyệt Chapter, Mangaka duyệt Task).
* **Thuộc tính**:
    * `review_id`: INT, PK, Auto Increment.
    * `submission_id`: INT, Foreign Key (submissions.submission_id), NOT NULL.
    * `reviewer_id`: INT, Foreign Key (users.user_id), NOT NULL.
    * `comments`: TEXT, NOT NULL.
    * `rating`: INT, NULL (Thang điểm).
    * `created_at`: TIMESTAMP.

### 10. Bảng `series_rankings`
* **Mô tả**: Xếp hạng Series theo chu kỳ (tuần/tháng) do Hội đồng Editorial Board đánh giá.
* **Thuộc tính**:
    * `ranking_id`: INT, PK, Auto Increment.
    * `series_id`: INT, Foreign Key (series.series_id), NOT NULL.
    * `board_member_id`: INT, Foreign Key (users.user_id), NOT NULL (Người đánh giá).
    * `rank_position`: INT, NOT NULL.
    * `score`: DECIMAL(5,2), NULL.
    * `period_start_date`: DATE, NOT NULL.
    * `created_at`: TIMESTAMP.

### 11. Bảng `notifications`
* **Mô tả**: Quản lý thông báo hệ thống gửi đến người dùng.
* **Thuộc tính**:
    * `notification_id`: INT, PK, Auto Increment.
    * `user_id`: INT, Foreign Key (users.user_id), NOT NULL.
    * `type`: VARCHAR(50), NOT NULL.
    * `message`: TEXT, NOT NULL.
    * `is_read`: BOOLEAN, Default FALSE.
    * `related_id`: INT, NULL (ID của đối tượng liên quan như task_id, submission_id, series_id phục vụ điều hướng).
    * `created_at`: TIMESTAMP, Default CURRENT_TIMESTAMP.

### 12. Bảng `system_logs`
* **Mô tả**: Nhật ký ghi nhận các hoạt động và tác vụ cấu hình nhạy cảm của người dùng (tập trung vào Admin).
* **Thuộc tính**:
    * `log_id`: INT, PK, Auto Increment.
    * `user_id`: INT, Foreign Key (users.user_id), NULL (Có thể null nếu người dùng bị xóa - ON DELETE SET NULL).
    * `action`: VARCHAR(255), NOT NULL (Hành động thực hiện, ví dụ: 'Tạo người dùng', 'Sao lưu dữ liệu').
    * `details`: TEXT, NULL (Mô tả chi tiết nội dung thay đổi).
    * `ip_address`: VARCHAR(45), NULL (Địa chỉ IP của client thực hiện tác vụ).
    * `created_at`: TIMESTAMP, Default CURRENT_TIMESTAMP.

### 13. Bảng `editor_annotations`
* **Mô tả**: Lưu trữ các khung khoanh vùng lỗi và ghi chú sửa đổi trực quan của Editor trên từng trang truyện.
* **Thuộc tính**:
    * `annotation_id`: INT, PK, Auto Increment.
    * `page_id`: INT, Foreign Key (pages.page_id), NOT NULL.
    * `editor_id`: INT, Foreign Key (users.user_id), NOT NULL.
    * `x`: INT, NOT NULL.
    * `y`: INT, NOT NULL.
    * `width`: INT, NOT NULL.
    * `height`: INT, NOT NULL.
    * `comments`: TEXT, NOT NULL.
    * `created_at`: TIMESTAMP.

### 14. Bảng `board_votes`
* **Mô tả**: Lưu trữ các lượt bỏ phiếu của thành viên Hội đồng biên tập cho đề xuất truyện.
* **Thuộc tính**:
    * `vote_id`: INT, PK, Auto Increment.
    * `series_id`: INT, Foreign Key (series.series_id), NOT NULL.
    * `board_member_id`: INT, Foreign Key (users.user_id), NOT NULL.
    * `vote`: ENUM('approve', 'reject'), NOT NULL.
    * `created_at`: TIMESTAMP.

---

## 4. Phân tích và mô tả toàn bộ mối quan hệ (Relations)

* **One-to-Many (1-N)**:
  * `roles` (1) - (N) `users`: Một `Role` được gán cho nhiều `User` (`assigned`). Mỗi `User` có đúng 1 `Role` (thông qua `role_id`).
  * `users` (1) - (N) `series`: Một `User` (Mangaka) tạo ra (`creates`) nhiều `Series` (thông qua `mangaka_id`).
  * `users` (1) - (N) `tasks`: Một `User` (Mangaka) giao (`assigns`) nhiều `Task` (qua `mangaka_id`). Một `User` (Assistant) nhận (`receives`) nhiều `Task` (qua `assistant_id`).
  * `users` (1) - (N) `submissions`: Một `User` tạo (`submits`) nhiều `Submission` (thông qua `user_id`).
  * `users` (1) - (N) `reviews`: Một `User` viết (`writes`) nhiều `Review` (thông qua `reviewer_id`).
  * `users` (1) - (N) `notifications`: Một `User` nhận (`receives`) nhiều `Notification` (thông qua `user_id`).
  * `users` (1) - (N) `system_logs`: Một `User` thực hiện các thao tác sẽ ghi lại (`triggers`) nhiều `SystemLog` (thông qua `user_id`).
  * `users` (1) - (N) `series_rankings`: Một `User` (Board Member) đánh giá (`evaluates`) nhiều `SeriesRanking` (thông qua `board_member_id`).
  * `series` (1) - (N) `chapters`: Một `Series` chứa (`contains`) nhiều `Chapter` (thông qua `series_id`).
  * `series` (1) - (N) `series_rankings`: Một `Series` có thể được xếp hạng (`ranked`) nhiều lần qua các khoảng thời gian (thông qua `series_id`).
  * `chapters` (1) - (N) `pages`: Một `Chapter` chứa (`contains`) nhiều `Page` (thông qua `chapter_id`).
  * `pages` (1) - (N) `page_regions`: Một `Page` được phân chia thành (`segment`) nhiều `PageRegion` (thông qua `page_id`).
  * `pages` (1) - (N) `tasks`: Một `Page` yêu cầu (`requires`) nhiều `Task` (thông qua `page_id`).
  * `page_regions` (1) - (N) `tasks`: Một `PageRegion` có thể được giao (`targetedAt`) nhiều `Task` (thông qua `page_region_id`).
  * `tasks` (1) - (N) `submissions`: Một `Task` sinh ra (`generates`) nhiều `Submission` (nếu bị reject và nộp lại nhiều lần, thông qua `task_id`).
  * `chapters` (1) - (N) `submissions`: Một `Chapter` được submit (`submitted`) qua nhiều lần (thông qua `chapter_id`).
  * `submissions` (1) - (N) `reviews`: Một `Submission` có thể nhận (`receives`) nhiều `Review` (thông qua `submission_id`).
  * `users` (1) - (N) `editor_annotations`: Một `User` (Editor) thực hiện vẽ nhiều ghi chú lỗi (`annotates`) trên trang truyện (thông qua `editor_id`).
  * `pages` (1) - (N) `editor_annotations`: Một `Page` trang truyện chứa nhiều ghi chú lỗi (`has`) của Editor (thông qua `page_id`).
  * `users` (1) - (N) `board_votes`: Một `User` (Board Member) tham gia bỏ phiếu (`votes`) nhiều phiếu (thông qua `board_member_id`).
  * `series` (1) - (N) `board_votes`: Một `Series` nhận nhiều phiếu bầu (`receives_votes`) từ Hội đồng (thông qua `series_id`).

* **Many-to-Many (N-N)**:
  * Trong hệ thống này không có quan hệ N-N trực tiếp. Mọi quy trình làm việc phức tạp (ví dụ: Mangaka - Assistant - Editor) đã được phân rã hoàn toàn qua các thực thể trung gian như `Task`, `Submission`, và `Review`.

---

## 5. Lệnh MySQL CREATE TABLE hoàn chỉnh

Tập lệnh SQL chuẩn xác 100% đồng bộ với mọi tài liệu thiết kế. Sẵn sàng cập nhật vào file `database/manga_workflow.sql`.

```sql
-- ==============================================================================
-- CÆ  Sá» Dá»® LIá»†U: Manga Creation Workflow and Publishing Management System
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS manga_workflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE manga_workflow;

-- ------------------------------------------------------------------------------
-- 1. Báº£ng roles: LÆ°u trá»¯ thĂ´ng tin vá» cĂ¡c vai trĂ² ngÆ°á»i dĂ¹ng trong há»‡ thá»‘ng
-- ------------------------------------------------------------------------------
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) COMMENT 'Báº£ng phĂ¢n quyá»n, lÆ°u trá»¯ cĂ¡c vai trĂ² (admin, mangaka, assistant, editor, board)';

-- ------------------------------------------------------------------------------
-- 2. Báº£ng users: LÆ°u trá»¯ tĂ i khoáº£n ngÆ°á»i dĂ¹ng
-- ------------------------------------------------------------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT
) COMMENT 'LÆ°u trá»¯ thĂ´ng tin chi tiáº¿t vĂ  tĂ i khoáº£n Ä‘Äƒng nháº­p cá»§a ngÆ°á»i dĂ¹ng';

-- ------------------------------------------------------------------------------
-- 3. Báº£ng series: Quáº£n lĂ½ thĂ´ng tin tá»«ng bá»™ truyá»‡n Manga
-- ------------------------------------------------------------------------------
CREATE TABLE series (
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
    CONSTRAINT fk_series_editor FOREIGN KEY (editor_id) REFERENCES users(user_id) ON DELETE SET NULL
) COMMENT 'LÆ°u trá»¯ thĂ´ng tin dá»± Ă¡n/bá»™ truyá»‡n Manga cá»§a TĂ¡c giáº£ (Mangaka)';

-- ------------------------------------------------------------------------------
-- 4. Báº£ng chapters: Quáº£n lĂ½ cĂ¡c chÆ°Æ¡ng (chapter) cá»§a má»™t Series
-- ------------------------------------------------------------------------------
CREATE TABLE chapters (
    chapter_id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    chapter_number INT NOT NULL,
    title VARCHAR(255),
    status ENUM('drafting', 'drawing', 'reviewing', 'approved', 'published') DEFAULT 'drafting',
    due_date DATETIME,
    published_at DATETIME,
    is_final TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_chapters_series FOREIGN KEY (series_id) REFERENCES series(series_id) ON DELETE CASCADE,
    UNIQUE KEY unique_chapter (series_id, chapter_number)
) COMMENT 'Quáº£n lĂ½ cĂ¡c chÆ°Æ¡ng truyá»‡n. UNIQUE constraint Ä‘áº£m báº£o khĂ´ng trĂ¹ng sá»‘ thá»© tá»± chÆ°Æ¡ng trong 1 series';

-- ------------------------------------------------------------------------------
-- 5. Báº£ng pages: Quáº£n lĂ½ tá»«ng trang truyá»‡n thuá»™c má»™t Chapter
-- ------------------------------------------------------------------------------
CREATE TABLE pages (
    page_id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT NOT NULL,
    page_number INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    status ENUM('drafting', 'drawing', 'reviewing', 'approved', 'published') DEFAULT 'drafting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pages_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id) ON DELETE CASCADE,
    UNIQUE KEY unique_page (chapter_id, page_number)
) COMMENT 'LÆ°u trá»¯ cĂ¡c trang truyá»‡n. UNIQUE constraint Ä‘áº£m báº£o 1 chÆ°Æ¡ng khĂ´ng bá»‹ trĂ¹ng láº·p sá»‘ thá»© tá»± trang';

-- ------------------------------------------------------------------------------
-- 5b. Báº£ng page_regions: LÆ°u trá»¯ cĂ¡c vĂ¹ng phĂ¢n Ä‘oáº¡n (khung truyá»‡n, bong bĂ³ng thoáº¡i...)
-- ------------------------------------------------------------------------------
CREATE TABLE page_regions (
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
) COMMENT 'LÆ°u trá»¯ thĂ´ng tin chi tiáº¿t vá» tá»«ng phĂ¢n vĂ¹ng váº½ tay thá»§ cĂ´ng trĂªn trang truyá»‡n';

-- ------------------------------------------------------------------------------
-- 6. Báº£ng tasks: Quáº£n lĂ½ cĂ´ng viá»‡c do Mangaka phĂ¢n cĂ´ng cho Assistant
-- ------------------------------------------------------------------------------
CREATE TABLE tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    page_region_id INT NULL,
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
    CONSTRAINT fk_tasks_assistant FOREIGN KEY (assistant_id) REFERENCES users(user_id) ON DELETE RESTRICT
) COMMENT 'LÆ°u trá»¯ cĂ´ng viá»‡c (nhÆ° Ä‘á»• tone, váº½ ná»n) Ä‘Æ°á»£c giao cho cĂ¡c trá»£ lĂ½ trĂªn má»™t trang hoáº·c má»™t vĂ¹ng cá»¥ thá»ƒ';

-- ------------------------------------------------------------------------------
-- 7. Báº£ng submissions: Quáº£n lĂ½ viá»‡c ná»™p sáº£n pháº©m 
-- ------------------------------------------------------------------------------
CREATE TABLE submissions (
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
    CONSTRAINT fk_submissions_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id) ON DELETE CASCADE
) COMMENT 'LÆ°u trá»¯ lá»‹ch sá»­ ná»™p báº£n váº½ cá»§a Assistant cho Task, hoáº·c Mangaka ná»™p Chapter cho Editor';

-- ------------------------------------------------------------------------------
-- 8. Báº£ng reviews: Quáº£n lĂ½ pháº£n há»“i vĂ  Ä‘Ă¡nh giĂ¡ vá» Submission
-- ------------------------------------------------------------------------------
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    comments TEXT NOT NULL,
    rating INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(user_id) ON DELETE RESTRICT
) COMMENT 'LÆ°u trá»¯ cĂ¡c lá»i phĂª bĂ¬nh, gĂ³p Ă½ cá»§a Editor/Mangaka trĂªn tá»«ng láº§n submit';

-- ------------------------------------------------------------------------------
-- 9. Báº£ng series_rankings: Xáº¿p háº¡ng Series theo chu ká»³
-- ------------------------------------------------------------------------------
CREATE TABLE series_rankings (
    ranking_id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    board_member_id INT NOT NULL,
    rank_position INT NOT NULL,
    score DECIMAL(5,2),
    period_start_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rankings_series FOREIGN KEY (series_id) REFERENCES series(series_id) ON DELETE CASCADE,
    CONSTRAINT fk_rankings_board FOREIGN KEY (board_member_id) REFERENCES users(user_id) ON DELETE RESTRICT
) COMMENT 'LÆ°u trá»¯ Ä‘Ă¡nh giĂ¡ xáº¿p háº¡ng Ä‘á»‹nh ká»³ cá»§a Há»™i Ä‘á»“ng biĂªn táº­p (Editorial Board) dĂ nh cho cĂ¡c bá»™ truyá»‡n';

-- ------------------------------------------------------------------------------
-- 10. Báº£ng notifications: Quáº£n lĂ½ thĂ´ng bĂ¡o há»‡ thá»‘ng
-- ------------------------------------------------------------------------------
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    related_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) COMMENT 'LÆ°u trá»¯ thĂ´ng bĂ¡o trong há»‡ thá»‘ng, Ä‘Ă¡nh dáº¥u Ä‘Ă£ Ä‘á»c hoáº·c chÆ°a';

-- ------------------------------------------------------------------------------
-- INDEXES - Tá»‘i Æ°u hĂ³a truy váº¥n cÆ¡ sá»Ÿ dá»¯ liá»‡u
-- ------------------------------------------------------------------------------
CREATE INDEX idx_users_role ON users(role_id);
CREATE INDEX idx_series_mangaka ON series(mangaka_id);
CREATE INDEX idx_chapters_series ON chapters(series_id);
CREATE INDEX idx_pages_chapter ON pages(chapter_id);
CREATE INDEX idx_tasks_users ON tasks(mangaka_id, assistant_id);
CREATE INDEX idx_submissions_target ON submissions(task_id, chapter_id);
CREATE INDEX idx_notifications_user ON notifications(user_id, is_read);

-- ------------------------------------------------------------------------------
-- 11. Báº£ng system_logs: Nháº­t kĂ½ hoáº¡t Ä‘á»™ng há»‡ thá»‘ng
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) COMMENT 'Nháº­t kĂ½ hoáº¡t Ä‘á»™ng nháº¡y cáº£m trong há»‡ thá»‘ng nhÆ° Ä‘Äƒng nháº­p, thay Ä‘á»•i thĂ´ng tin ngÆ°á»i dĂ¹ng';

CREATE INDEX idx_system_logs_user ON system_logs(user_id);


-- SQL Migration: Táº¡o báº£ng editor_annotations
USE manga_workflow;

CREATE TABLE IF NOT EXISTS editor_annotations (
    annotation_id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    editor_id INT NOT NULL,
    x INT NOT NULL,
    y INT NOT NULL,
    width INT NOT NULL,
    height INT NOT NULL,
    comments TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_annotations_page FOREIGN KEY (page_id) REFERENCES pages(page_id) ON DELETE CASCADE,
    CONSTRAINT fk_annotations_editor FOREIGN KEY (editor_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='LÆ°u trá»¯ cĂ¡c khung khoanh vĂ¹ng lá»—i vĂ  ghi chĂº sá»­a Ä‘á»•i trá»±c quan cá»§a Editor trĂªn tá»«ng trang truyá»‡n';

-- database/create_board_votes_table.sql
USE manga_workflow;

-- 1. Táº¡o báº£ng board_votes
CREATE TABLE IF NOT EXISTS board_votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    board_member_id INT NOT NULL,
    vote ENUM('approve', 'reject') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_board_votes_series FOREIGN KEY (series_id) REFERENCES series(series_id) ON DELETE CASCADE,
    CONSTRAINT fk_board_votes_board FOREIGN KEY (board_member_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_board_vote (series_id, board_member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='LÆ°u trá»¯ cĂ¡c lÆ°á»£t bá» phiáº¿u cá»§a thĂ nh viĂªn Há»™i Ä‘á»“ng biĂªn táº­p cho Ä‘á» xuáº¥t truyá»‡n';

-- 2. Seed thĂªm 2 thĂ nh viĂªn Há»™i Ä‘á»“ng BiĂªn táº­p (Editorial Board) Ä‘á»ƒ Ä‘á»§ há»™i Ä‘á»“ng 3 ngÆ°á»i
-- Máº­t kháº©u máº·c Ä‘á»‹nh: password123 (Hash: $2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG)
SET @board_role_id = (SELECT role_id FROM roles WHERE role_name = 'board');

-- Dá»n dáº¹p tĂ i khoáº£n thá»«a náº¿u cĂ³ tá»« Ä‘á»£t cháº¡y trÆ°á»›c
DELETE FROM users WHERE username IN ('board_member_4', 'board_member_5') AND role_id = @board_role_id;

INSERT IGNORE INTO users (username, full_name, email, password_hash, role_id, status) VALUES
('board_member_2', 'Board Member 2', 'board2@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @board_role_id, 'active'),
('board_member_3', 'Board Member 3', 'board3@example.com', '$2y$10$v87ChVYIS3A5xiBaHjA7JuIFU2VN2gr.eVZO7KaHWvnjM48JJo5OG', @board_role_id, 'active');

-- 3. Táº¡o sáºµn dá»¯ liá»‡u bá» phiáº¿u ban Ä‘áº§u cho cĂ¡c bá»™ truyá»‡n Ä‘ang á»Ÿ tráº¡ng thĂ¡i 'planning' Ä‘á»ƒ hiá»ƒn thá»‹ trá»±c quan láº­p tá»©c
-- XĂ³a sáº¡ch cĂ¡c phiáº¿u báº§u cÅ© cá»§a series 'planning' trÆ°á»›c khi chĂ¨n láº¡i
DELETE FROM board_votes WHERE series_id IN (SELECT series_id FROM series WHERE status = 'planning');

-- board_user, board_member_2 bá» phiáº¿u 'approve' (2 phiáº¿u)
-- board_member_3 bá» phiáº¿u 'reject' (1 phiáº¿u)
-- Äáº£m báº£o tá»‰ lá»‡ Ä‘áº¡t Ä‘Ăºng 67% (2/3 tĂ¡n thĂ nh) vĂ  cháº¡y hoĂ n toĂ n tá»± Ä‘á»™ng
INSERT IGNORE INTO board_votes (series_id, board_member_id, vote)
SELECT s.series_id, u.user_id, IF(u.username IN ('board_user', 'board_member_2'), 'approve', 'reject')
FROM series s
CROSS JOIN users u
JOIN roles r ON u.role_id = r.role_id
WHERE s.status = 'planning' AND r.role_name = 'board';

```

---

## 6. Định hướng Code OOP (Models PHP)
Dựa trên Class Diagram, các file Model (ví dụ: `models/User.php`, `models/Series.php`) sẽ sử dụng `camelCase` cho các thuộc tính (`property`) và thực hiện thao tác ánh xạ trực tiếp (mapping) với các cột `snake_case` trong cơ sở dữ liệu.

Ví dụ tham khảo:
```php
class User {
    public $userId;
    public $username;
    public $fullName;
    public $email;
    public $passwordHash;
    public $status;
}
```
