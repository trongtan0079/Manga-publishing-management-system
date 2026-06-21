<?php

namespace App\Controllers;

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/Series.php';

use Chapter;
use Series;
use PDOException;

class ChapterController
{
    private $chapterModel;
    private $seriesModel;
    private $allowedStatuses = ['draft', 'published', 'scheduled'];

    public function __construct() {
        requireRole('mangaka');
        
        $this->chapterModel = new Chapter();
        $this->seriesModel = new Series();
    }

    private function checkSeriesOwnership($seriesId) {
        $series = $this->seriesModel->findById($seriesId);
        if (!$series) {
            $_SESSION['error'] = "Không tìm thấy bộ truyện.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        if ($series['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên bộ truyện này.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }
        return $series;
    }

    public function index() {
        $seriesId = $_GET['series_id'] ?? null;
        if ($seriesId) {
            header('Location: /index.php?controller=series&action=show&id=' . $seriesId);
            exit;
        }
        header('Location: /index.php?controller=series&action=index');
        exit;
    }

    public function create() {
        $seriesId = $_GET['series_id'] ?? null;
        if (!$seriesId) {
            $_SESSION['error'] = "Thiếu thông tin bộ truyện.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        $series = $this->checkSeriesOwnership($seriesId);
        require_once __DIR__ . '/../views/mangaka/chapter_create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $seriesId = $_POST['series_id'] ?? null;
            if (!$seriesId) {
                $_SESSION['error'] = "Thiếu thông tin bộ truyện.";
                header('Location: /index.php?controller=series&action=index');
                exit;
            }

            $series = $this->checkSeriesOwnership($seriesId);

            $chapterNumber = trim($_POST['chapter_number'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $status = $_POST['status'] ?? 'draft';

            // Validation
            if ($chapterNumber === '' || !is_numeric($chapterNumber) || $chapterNumber <= 0) {
                $_SESSION['error'] = "Chapter Number bắt buộc và phải lớn hơn 0.";
                header("Location: /index.php?controller=chapter&action=create&series_id={$seriesId}");
                exit;
            }

            if ($this->chapterModel->isChapterNumberExists($seriesId, $chapterNumber)) {
                $_SESSION['error'] = "Chapter Number {$chapterNumber} đã tồn tại trong bộ truyện này.";
                header("Location: /index.php?controller=chapter&action=create&series_id={$seriesId}");
                exit;
            }

            if (!in_array($status, $this->allowedStatuses)) {
                $_SESSION['error'] = "Trạng thái chapter không hợp lệ!";
                header("Location: /index.php?controller=chapter&action=create&series_id={$seriesId}");
                exit;
            }

            $data = [
                'series_id' => $seriesId,
                'chapter_number' => $chapterNumber,
                'title' => $title,
                'status' => $status
            ];

            try {
                $this->chapterModel->insert($data);
                $_SESSION['success'] = "Tạo chapter {$chapterNumber} thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi tạo chapter: " . $e->getMessage();
            }
            
            header("Location: /index.php?controller=series&action=show&id={$seriesId}");
            exit;
        }
    }

    public function edit($id) {
        $chapter = $this->chapterModel->findById($id);
        if (!$chapter) {
            $_SESSION['error'] = "Không tìm thấy chapter.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        $series = $this->checkSeriesOwnership($chapter['series_id']);
        require_once __DIR__ . '/../views/mangaka/chapter_edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chapter = $this->chapterModel->findById($id);
            if (!$chapter) {
                $_SESSION['error'] = "Không tìm thấy chapter.";
                header('Location: /index.php?controller=series&action=index');
                exit;
            }

            $seriesId = $chapter['series_id'];
            $series = $this->checkSeriesOwnership($seriesId);

            $chapterNumber = trim($_POST['chapter_number'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $status = $_POST['status'] ?? 'draft';

            // Validation
            if ($chapterNumber === '' || !is_numeric($chapterNumber) || $chapterNumber <= 0) {
                $_SESSION['error'] = "Chapter Number bắt buộc và phải lớn hơn 0.";
                header("Location: /index.php?controller=chapter&action=edit&id={$id}");
                exit;
            }

            if ($this->chapterModel->isChapterNumberExists($seriesId, $chapterNumber, $id)) {
                $_SESSION['error'] = "Chapter Number {$chapterNumber} đã tồn tại trong bộ truyện này.";
                header("Location: /index.php?controller=chapter&action=edit&id={$id}");
                exit;
            }

            if (!in_array($status, $this->allowedStatuses)) {
                $_SESSION['error'] = "Trạng thái chapter không hợp lệ!";
                header("Location: /index.php?controller=chapter&action=edit&id={$id}");
                exit;
            }

            $data = [
                'chapter_number' => $chapterNumber,
                'title' => $title,
                'status' => $status
            ];

            try {
                $this->chapterModel->update($id, $data);
                $_SESSION['success'] = "Cập nhật chapter {$chapterNumber} thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi cập nhật chapter: " . $e->getMessage();
            }
            
            header("Location: /index.php?controller=series&action=show&id={$seriesId}");
            exit;
        }
    }

    public function show($id) {
        $chapter = $this->chapterModel->findById($id);
        if (!$chapter) {
            $_SESSION['error'] = "Không tìm thấy chapter.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        $series = $this->checkSeriesOwnership($chapter['series_id']);
        require_once __DIR__ . '/../views/mangaka/chapter_detail.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chapter = $this->chapterModel->findById($id);
            if (!$chapter) {
                $_SESSION['error'] = "Không tìm thấy chapter.";
                header('Location: /index.php?controller=series&action=index');
                exit;
            }

            $seriesId = $chapter['series_id'];
            $this->checkSeriesOwnership($seriesId);

            try {
                $this->chapterModel->delete($id);
                $_SESSION['success'] = "Đã xóa chapter thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Không thể xóa chapter vì dữ liệu đang liên kết.";
            }
            
            header("Location: /index.php?controller=series&action=show&id={$seriesId}");
            exit;
        }
    }
}
