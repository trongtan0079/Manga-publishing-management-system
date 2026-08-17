USE manga_workflow;

CREATE TABLE IF NOT EXISTS board_votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    board_member_id INT NOT NULL,
    vote ENUM('approve', 'reject') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_board_votes_series
        FOREIGN KEY (series_id)
        REFERENCES series(series_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_board_votes_board
        FOREIGN KEY (board_member_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    UNIQUE KEY unique_board_vote
        (series_id, board_member_id)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci
COMMENT='Editorial board voting records';