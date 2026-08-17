<?php 
/**
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi gạch đỏ
 * @var array $series Thông tin bộ truyện
 * @var array $chapter Thông tin chapter đang chỉnh sửa
 */
$pageTitle = 'Chỉnh sửa Chapter';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
$isLocked = ($chapter['status'] === 'reviewing_draft' || $chapter['status'] === 'reviewing_final' || $chapter['status'] === 'approved' || $chapter['status'] === 'published');
?>
<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-secondary">&larr; Quay lại Bộ truyện</a>
</div>

<?php require_once __DIR__ . '/../layouts/alerts.php'; ?>

<div class="card border-warning mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Chỉnh sửa Chapter</h5>
    </div>
    <div class="card-body">
        <form action="<?= BASE_PATH ?>/index.php?controller=chapter&action=update&id=<?= $chapter['chapter_id'] ?>" method="POST">
            <?php if ($isLocked): ?>
                <div class="alert alert-warning border-0 py-2.5 px-3 mb-4 d-flex align-items-center gap-2" style="font-size: 0.85rem; border-radius: 8px; background-color: #fffbeb; color: #b45309;">
                    <i class="fas fa-lock fs-6"></i>
                    <div><strong>Lưu ý:</strong> Chương truyện này đã được phê duyệt hoặc xuất bản. Biểu mẫu chỉnh sửa đã bị khóa để bảo toàn dữ liệu.</div>
                </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="chapter_number" class="form-label">Số Chapter <span class="text-danger">*</span></label>
                <input type="number" step="any" min="0" class="form-control" id="chapter_number" name="chapter_number" value="<?= htmlspecialchars($chapter['chapter_number']) ?>" required <?= $isLocked ? 'disabled' : '' ?>>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Tên Chapter</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($chapter['title'] ?? '') ?>" placeholder="Không bắt buộc" <?= $isLocked ? 'disabled' : '' ?>>
            </div>
            
            <div class="mb-3">
                <label class="form-label d-block"><i class="fas fa-info-circle me-1 text-primary"></i> Trạng thái hiện tại</label>
                <?php 
                $cBadge = 'bg-secondary';
                $statusLabels = [
                    'drafting' => 'Phác thảo Kịch bản (Storyboard)',
                    'drawing' => 'Đang vẽ Chi tiết',
                    'reviewing_draft' => 'Chờ duyệt Kịch bản',
                    'reviewing_final' => 'Chờ duyệt Bản vẽ',
                    'approved' => 'Đã duyệt phát hành',
                    'published' => 'Đã xuất bản'
                ];
                $displayStatus = $statusLabels[$chapter['status']] ?? $chapter['status'];
                
                switch ($chapter['status']) {
                    case 'drafting': $cBadge = 'bg-secondary'; break;
                    case 'drawing': $cBadge = 'bg-primary'; break;
                    case 'reviewing_draft': 
                    case 'reviewing_final': $cBadge = 'bg-warning text-dark'; break;
                    case 'approved': $cBadge = 'bg-info text-dark'; break;
                    case 'published': $cBadge = 'bg-success'; break;
                }
                ?>
                <span class="badge <?= $cBadge ?> p-2.5" style="border-radius: 6px; font-size: 0.9rem;"><i class="fas fa-layer-group me-1"></i> <?= htmlspecialchars($displayStatus) ?></span>
                <div class="form-text text-muted mt-2">Trạng thái này được cập nhật tự động trong quá trình nộp bản thảo và phê duyệt của Editor.</div>
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn chót (Due Date)</label>
                <?php 
                $dueDateVal = '';
                if (!empty($chapter['due_date'])) {
                    $dueDateVal = date('Y-m-d\TH:i', strtotime($chapter['due_date']));
                }
                ?>
                <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="<?= $dueDateVal ?>" <?= $isLocked ? 'disabled' : '' ?>>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_final" name="is_final" value="1" <?= (!empty($chapter['is_final']) ? 'checked' : '') ?> <?= $isLocked ? 'disabled' : '' ?>>
                <label class="form-check-label fw-semibold text-danger" for="is_final">
                    <i class="fas fa-flag"></i> Đây là chương cuối cùng của bộ truyện (End Chapter)
                </label>
            </div>
            
            <button type="submit" class="btn btn-warning" <?= $isLocked ? 'disabled' : '' ?>><i class="fas fa-save me-1"></i>Lưu thay đổi</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
