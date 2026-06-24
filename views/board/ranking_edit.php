<?php 
require_once __DIR__ . '/../../core/Auth.php';
$role = $_SESSION['role_name'];
$pageTitle = 'Sửa Đánh giá Xếp hạng';
$current_page = 'rankings';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Chỉnh sửa Xếp hạng</h2>
        <p class="text-muted text-xs mb-0">Cập nhật thông tin đánh giá cho kỳ hiện tại.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=update&id=<?= $ranking['ranking_id'] ?>" method="POST">
                    
                    <div class="mb-3">
                        <label for="series_id" class="form-label fw-bold">Chọn Bộ Truyện (Series) <span class="text-danger">*</span></label>
                        <select class="form-select" id="series_id" name="series_id" required>
                            <option value="">-- Chọn Series --</option>
                            <?php if (!empty($seriesList)): ?>
                                <?php foreach ($seriesList as $series): ?>
                                    <option value="<?= htmlspecialchars($series['series_id']) ?>" <?= ($series['series_id'] == $ranking['series_id']) ? 'selected' : '' ?>>
                                        ID: <?= htmlspecialchars($series['series_id']) ?> - <?= htmlspecialchars($series['title']) ?> (<?= htmlspecialchars($series['status']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rank_position" class="form-label fw-bold">Hạng (Rank Position) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="rank_position" name="rank_position" min="1" value="<?= htmlspecialchars($ranking['rank_position']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="score" class="form-label fw-bold">Điểm số (Score) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="score" name="score" min="0" value="<?= htmlspecialchars($ranking['score']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="period_start_date" class="form-label fw-bold">Kỳ đánh giá (Ngày bắt đầu) <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="period_start_date" name="period_start_date" value="<?= htmlspecialchars($ranking['period_start_date']) ?>" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning text-white py-2 fw-bold">Cập nhật Đánh giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
