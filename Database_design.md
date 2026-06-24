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

## 3. Database Schema (10 Bảng) và Data Dictionary

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
    * `cover_image`: VARCHAR(255), NULL.
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

### 6. Bảng `tasks`
* **Mô tả**: Quản lý công việc do Mangaka phân công cho Assistant (ví dụ đi nét, đổ tone trang cụ thể).
* **Thuộc tính**:
    * `task_id`: INT, PK, Auto Increment.
    * `page_id`: INT, Foreign Key (pages.page_id), NOT NULL.
    * `mangaka_id`: INT, Foreign Key (users.user_id), NOT NULL (Người giao việc).
    * `assistant_id`: INT, Foreign Key (users.user_id), NOT NULL (Người nhận việc).
    * `title`: VARCHAR(255), NOT NULL.
    * `description`: TEXT, NULL.
    * `priority`: ENUM('low', 'medium', 'high'), Default 'medium'.
    * `status`: ENUM('pending', 'in_progress', 'completed'), Default 'pending'.
    * `due_date`: DATETIME, NULL.
    * `created_at`: TIMESTAMP.
    * `updated_at`: TIMESTAMP.

### 7. Bảng `submissions`
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

### 8. Bảng `reviews`
* **Mô tả**: Quản lý phản hồi và đánh giá về Submission (Editor duyệt Chapter, Mangaka duyệt Task).
* **Thuộc tính**:
    * `review_id`: INT, PK, Auto Increment.
    * `submission_id`: INT, Foreign Key (submissions.submission_id), NOT NULL.
    * `reviewer_id`: INT, Foreign Key (users.user_id), NOT NULL.
    * `comments`: TEXT, NOT NULL.
    * `rating`: INT, NULL (Thang điểm).
    * `created_at`: TIMESTAMP.

### 9. Bảng `series_rankings`
* **Mô tả**: Xếp hạng Series theo chu kỳ (tuần/tháng) do Hội đồng Editorial Board đánh giá.
* **Thuộc tính**:
    * `ranking_id`: INT, PK, Auto Increment.
    * `series_id`: INT, Foreign Key (series.series_id), NOT NULL.
    * `board_member_id`: INT, Foreign Key (users.user_id), NOT NULL (Người đánh giá).
    * `rank_position`: INT, NOT NULL.
    * `score`: DECIMAL(5,2), NULL.
    * `period_start_date`: DATE, NOT NULL.
    * `created_at`: TIMESTAMP.

### 10. Bảng `notifications`
* **Mô tả**: Quản lý thông báo hệ thống gửi đến người dùng.
* **Thuộc tính**:
    * `notification_id`: INT, PK, Auto Increment.
    * `user_id`: INT, Foreign Key (users.user_id), NOT NULL.
    * `type`: VARCHAR(50), NOT NULL.
    * `message`: TEXT, NOT NULL.
    * `is_read`: BOOLEAN, Default FALSE.
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
  * `users` (1) - (N) `series_rankings`: Một `User` (Board Member) đánh giá (`evaluates`) nhiều `SeriesRanking` (thông qua `board_member_id`).
  * `series` (1) - (N) `chapters`: Một `Series` chứa (`contains`) nhiều `Chapter` (thông qua `series_id`).
  * `series` (1) - (N) `series_rankings`: Một `Series` có thể được xếp hạng (`ranked`) nhiều lần qua các khoảng thời gian (thông qua `series_id`).
  * `chapters` (1) - (N) `pages`: Một `Chapter` chứa (`contains`) nhiều `Page` (thông qua `chapter_id`).
  * `pages` (1) - (N) `tasks`: Một `Page` yêu cầu (`requires`) nhiều `Task` (thông qua `page_id`).
  * `tasks` (1) - (N) `submissions`: Một `Task` sinh ra (`generates`) nhiều `Submission` (nếu bị reject và nộp lại nhiều lần, thông qua `task_id`).
  * `chapters` (1) - (N) `submissions`: Một `Chapter` được submit (`submitted`) qua nhiều lần (thông qua `chapter_id`).
  * `submissions` (1) - (N) `reviews`: Một `Submission` có thể nhận (`receives`) nhiều `Review` (thông qua `submission_id`).

* **Many-to-Many (N-N)**:
  * Trong hệ thống này không có quan hệ N-N trực tiếp. Mọi quy trình làm việc phức tạp (ví dụ: Mangaka - Assistant - Editor) đã được phân rã hoàn toàn qua các thực thể trung gian như `Task`, `Submission`, và `Review`.

---

## 5. Lệnh MySQL CREATE TABLE hoàn chỉnh

Tập lệnh SQL chuẩn xác 100% đồng bộ với mọi tài liệu thiết kế. Sẵn sàng cập nhật vào file `database/manga_workflow.sql`.

```sql
CREATE DATABASE IF NOT EXISTS manga_workflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE manga_workflow;

-- 1. roles table
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. users table
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
);

-- 3. series table
CREATE TABLE series (
    series_id INT AUTO_INCREMENT PRIMARY KEY,
    mangaka_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('planning', 'ongoing', 'completed', 'canceled', 'suspended') DEFAULT 'planning',
    cover_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_series_mangaka FOREIGN KEY (mangaka_id) REFERENCES users(user_id) ON DELETE RESTRICT
);

-- 4. chapters table
CREATE TABLE chapters (
    chapter_id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    chapter_number INT NOT NULL,
    title VARCHAR(255),
    status ENUM('drafting', 'drawing', 'reviewing', 'approved', 'published') DEFAULT 'drafting',
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_chapters_series FOREIGN KEY (series_id) REFERENCES series(series_id) ON DELETE CASCADE,
    UNIQUE KEY unique_chapter (series_id, chapter_number)
);

-- 5. pages table
CREATE TABLE pages (
    page_id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT NOT NULL,
    page_number INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    status ENUM('sketch', 'inked', 'toned', 'finished') DEFAULT 'sketch',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pages_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id) ON DELETE CASCADE,
    UNIQUE KEY unique_page (chapter_id, page_number)
);

-- 6. tasks table
CREATE TABLE tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    mangaka_id INT NOT NULL,
    assistant_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    due_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_page FOREIGN KEY (page_id) REFERENCES pages(page_id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_mangaka FOREIGN KEY (mangaka_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_tasks_assistant FOREIGN KEY (assistant_id) REFERENCES users(user_id) ON DELETE RESTRICT
);

-- 7. submissions table
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
);

-- 8. reviews table
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    comments TEXT NOT NULL,
    rating INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(user_id) ON DELETE RESTRICT
);

-- 9. series_rankings table
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
);

-- 10. notifications table
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX idx_users_role ON users(role_id);
CREATE INDEX idx_series_mangaka ON series(mangaka_id);
CREATE INDEX idx_chapters_series ON chapters(series_id);
CREATE INDEX idx_pages_chapter ON pages(chapter_id);
CREATE INDEX idx_tasks_users ON tasks(mangaka_id, assistant_id);
CREATE INDEX idx_submissions_target ON submissions(task_id, chapter_id);
CREATE INDEX idx_notifications_user ON notifications(user_id, is_read);
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
