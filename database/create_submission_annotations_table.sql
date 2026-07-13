-- SQL Migration: Tạo bảng submission_annotations
USE manga_workflow;

CREATE TABLE IF NOT EXISTS submission_annotations (
    annotation_id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    user_id INT NOT NULL, -- Tác giả (Mangaka) thực hiện ghi chú lỗi
    x INT NOT NULL,
    y INT NOT NULL,
    width INT NOT NULL,
    height INT NOT NULL,
    comments TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sub_annotations_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE,
    CONSTRAINT fk_sub_annotations_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lưu trữ các khung lỗi trực quan của Tác giả trên bản vẽ nộp của Trợ lý';
