<?php 
/**
 * View: Giao diện tạo đánh giá xếp hạng mới cho bộ truyện (ranking_create.php)
 * Vai trò: Board (Ban biên tập/Hội đồng)
 * Chức năng: Cho phép hội đồng/ban biên tập tạo xếp hạng và nhập điểm cho bộ truyện theo chu kỳ.
 * 
 * @var array $seriesList Danh sách các bộ truyện có trong hệ thống để chọn xếp hạng
 */
require_once __DIR__ . '/../../core/Auth.php';
$role = $_SESSION['role_name'];
$pageTitle = 'Tạo Đánh giá Xếp hạng Mới';
$current_page = 'rankings';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Tạo Đánh giá Mới</h2>
        <p class="text-muted text-xs mb-0">Thêm xếp hạng cho một bộ truyện trong kỳ đánh giá.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=store" method="POST">
                    
                    <div class="mb-3">
                        <label for="series_id" class="form-label fw-bold">Chọn Bộ Truyện (Series) <span class="text-danger">*</span></label>
                        <select class="form-select" id="series_id" name="series_id" required>
                            <option value="">-- Chọn Series --</option>
                            <?php if (!empty($seriesList)): ?>
                                <?php foreach ($seriesList as $series): ?>
                                    <option value="<?= htmlspecialchars($series['series_id']) ?>">
                                        ID: <?= htmlspecialchars($series['series_id']) ?> - <?= htmlspecialchars($series['title']) ?> (<?= htmlspecialchars($series['status']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rank_position" class="form-label fw-bold">Hạng (Rank Position) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="rank_position" name="rank_position" min="1" required placeholder="Ví dụ: 1">
                        </div>
                        <div class="col-md-6">
                            <label for="score" class="form-label fw-bold">Điểm số (Score) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="score" name="score" min="0" required placeholder="Ví dụ: 9.5">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="period_start_date" class="form-label fw-bold">Kỳ đánh giá (Ngày bắt đầu) <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="period_start_date" name="period_start_date" required>
                        <div class="form-text">Chọn ngày đầu tiên của kỳ đánh giá (ví dụ: ngày đầu tháng hoặc ngày đầu tuần).</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">Tạo Đánh giá & Công bố</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
