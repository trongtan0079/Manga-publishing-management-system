<?php
require_once __DIR__ . '/../core/Model.php';

class SeriesRanking extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'series_rankings';
        $this->primaryKey = 'ranking_id';
    }

    /**
     * Lấy lịch sử xếp hạng của một Series
     */
    public function findBySeriesId($seriesId) {
        $sql = "SELECT * FROM {$this->table} WHERE series_id = :series_id ORDER BY period_start_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin chi tiết xếp hạng kèm tên bộ truyện, tác giả, và tên người đánh giá (board member)
     */
    public function findWithDetails($id) {
        $sql = "SELECT sr.*, s.title as series_title, s.mangaka_id, u.full_name as board_member_name 
                FROM {$this->table} sr
                JOIN series s ON sr.series_id = s.series_id
                JOIN users u ON sr.board_member_id = u.user_id
                WHERE sr.ranking_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAllWithSeries() {
        $sql = "SELECT sr.*, s.title as series_title, u.full_name as mangaka_name, b.full_name as board_member_name,
                (SELECT chapter_number FROM chapters WHERE series_id = s.series_id AND status = 'published' ORDER BY chapter_number DESC LIMIT 1) as latest_chapter_number,
                (SELECT title FROM chapters WHERE series_id = s.series_id AND status = 'published' ORDER BY chapter_number DESC LIMIT 1) as latest_chapter_title
                FROM {$this->table} sr
                JOIN series s ON sr.series_id = s.series_id
                JOIN users u ON s.mangaka_id = u.user_id
                JOIN users b ON sr.board_member_id = b.user_id
                ORDER BY sr.period_start_date DESC, sr.rank_position ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByMangakaId($mangakaId) {
        $sql = "SELECT sr.*, s.title as series_title,
                (SELECT chapter_number FROM chapters WHERE series_id = s.series_id AND status = 'published' ORDER BY chapter_number DESC LIMIT 1) as latest_chapter_number,
                (SELECT title FROM chapters WHERE series_id = s.series_id AND status = 'published' ORDER BY chapter_number DESC LIMIT 1) as latest_chapter_title
                FROM {$this->table} sr
                JOIN series s ON sr.series_id = s.series_id
                WHERE s.mangaka_id = :mangaka_id
                ORDER BY sr.period_start_date DESC, sr.rank_position ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mangaka_id', $mangakaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLatestRanking($seriesId) {
        $sql = "SELECT * FROM {$this->table} WHERE series_id = :series_id ORDER BY period_start_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPreviousRanking($seriesId, $currentPeriodStartDate) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE series_id = :series_id AND period_start_date < :current_period 
                ORDER BY period_start_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->bindParam(':current_period', $currentPeriodStartDate);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkDuplicateRanking($seriesId, $periodStartDate) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE series_id = :series_id AND period_start_date = :period_start_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':series_id', $seriesId);
        $stmt->bindParam(':period_start_date', $periodStartDate);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getLatestPeriod() {
        $sql = "SELECT MAX(period_start_date) FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getSeriesCountByPeriod($periodStartDate) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE period_start_date = :period";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':period', $periodStartDate);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getTopSeriesByPeriod($periodStartDate, $limit = 5) {
        $sql = "SELECT sr.*, s.title as series_title 
                FROM {$this->table} sr
                JOIN series s ON sr.series_id = s.series_id
                WHERE sr.period_start_date = :period
                ORDER BY sr.rank_position ASC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':period', $periodStartDate);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBottomSeriesByPeriod($periodStartDate, $limit = 5) {
        $sql = "SELECT sr.*, s.title as series_title 
                FROM {$this->table} sr
                JOIN series s ON sr.series_id = s.series_id
                WHERE sr.period_start_date = :period
                ORDER BY sr.rank_position DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':period', $periodStartDate);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
