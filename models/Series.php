<?php

namespace App\Models;

class Series
{
    public $id;
    public $title;
    public $description;
    public $mangaka_id;
    public $status;
    public $created_at;

    public function __construct() {
    }

    /**
     * Lấy danh sách series cho Editor (bộ truyện đang theo dõi)
     */
    public function getSeriesByEditorId($editorId) {
        $sql = "SELECT * FROM {$this->table} WHERE editor_id = :editor_id AND status != 'planning' ORDER BY series_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':editor_id' => $editorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách series cho Editorial Board (để publish) kèm theo số liệu thống kê
     */
    public function getSeriesForPublishing() {
        $sql = "SELECT s.*, u.full_name as mangaka_name, ed.full_name as editor_name,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id) as total_chapters,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id AND status IN ('approved', 'published')) as finished_chapters,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id AND is_final = 1 AND status IN ('approved', 'published')) as has_final_approved,
                (SELECT rank_position FROM series_rankings WHERE series_id = s.series_id ORDER BY period_start_date DESC, ranking_id DESC LIMIT 1) as latest_rank,
                (SELECT score FROM series_rankings WHERE series_id = s.series_id ORDER BY period_start_date DESC, ranking_id DESC LIMIT 1) as latest_score
                FROM {$this->table} s 
                JOIN users u ON s.mangaka_id = u.user_id 
                LEFT JOIN users ed ON s.editor_id = ed.user_id
                WHERE s.publish_type != 'draft'
                ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách series (hồ sơ) theo Editor kèm theo số liệu
     */
    public function getDossiersByEditorId($editorId) {
        $sql = "SELECT s.*, u.full_name as mangaka_name,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id) as total_chapters,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id AND status IN ('approved', 'published')) as finished_chapters
                FROM {$this->table} s
                JOIN users u ON s.mangaka_id = u.user_id
                WHERE s.editor_id = :editor_id AND s.status != 'planning'
                ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':editor_id' => $editorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countActiveSeries() {
        $sql = "SELECT COUNT(*) FROM series WHERE status IN ('ongoing', 'completed', 'suspended')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
