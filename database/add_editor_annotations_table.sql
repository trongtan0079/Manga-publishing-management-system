-- SQL Migration: Tạo bảng editor_annotations
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lưu trữ các khung khoanh vùng lỗi và ghi chú sửa đổi trực quan của Editor trên từng trang truyện';
