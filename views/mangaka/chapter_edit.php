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
?>

<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= htmlspecialchars($series['series_id']) ?>" class="btn btn-secondary">&larr; Quay lại Bộ truyện</a>
</div>

<div class="card border-warning mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Chỉnh sửa Chapter</h5>
    </div>
    <div class="card-body">
        <form action="<?= BASE_PATH ?>/index.php?controller=chapter&action=update&id=<?= $chapter['chapter_id'] ?>" method="POST">
            
            <div class="mb-3">
                <label for="chapter_number" class="form-label">Số Chapter <span class="text-danger">*</span></label>
                <input type="number" step="any" min="0" class="form-control" id="chapter_number" name="chapter_number" value="<?= htmlspecialchars($chapter['chapter_number']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Tên Chapter</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($chapter['title'] ?? '') ?>" placeholder="Không bắt buộc">
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="drafting" <?= $chapter['status'] === 'drafting' ? 'selected' : '' ?>>Bản nháp (Drafting)</option>
                    <option value="drawing" <?= $chapter['status'] === 'drawing' ? 'selected' : '' ?>>Đang vẽ (Drawing)</option>
                    <option value="reviewing" <?= $chapter['status'] === 'reviewing' ? 'selected' : '' ?>>Đang chờ duyệt (Reviewing)</option>
                    <option value="approved" <?= $chapter['status'] === 'approved' ? 'selected' : '' ?>>Đã duyệt (Approved)</option>
                    <option value="published" <?= $chapter['status'] === 'published' ? 'selected' : '' ?>>Đã xuất bản (Published)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-warning">Lưu thay đổi</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
