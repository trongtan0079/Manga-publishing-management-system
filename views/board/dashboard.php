<?php 
/**
 * View: Giao diện bảng điều khiển dành cho Ban Giám đốc / Hội đồng (dashboard.php)
 * Vai trò: Board (Hội đồng/Ban giám đốc)
 * Chức năng: Thống kê số lượng bộ truyện được đánh giá, kỳ đánh giá gần nhất, bộ truyện đứng đầu, và bảng xếp hạng Top 5 bán chạy/yêu thích.
 * 
 * @var int $evaluatedSeries Số lượng truyện đã đánh giá xếp hạng
 * @var string|null $latestPeriod Kỳ đánh giá gần đây nhất
 * @var string $topRankingSeriesName Tên truyện đang dẫn đầu bảng xếp hạng
 * @var array $top5Series Danh sách 5 tác phẩm có điểm số cao nhất
 * @var array $bottom5Series Danh sách 5 tác phẩm cần chú ý (điểm thấp nhất)
 */
$pageTitle = 'Bảng Giám đốc (Board)';
$current_page = 'dashboard';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Báo cáo Ban Giám Đốc</h2>
        <p class="text-muted text-xs mb-0">Theo dõi doanh thu, bảng xếp hạng và toàn cảnh hoạt động xuất bản.</p>
    </div>
    <button class="btn btn-secondary shadow-sm" disabled><i class="fas fa-file-invoice-dollar me-2"></i>Tải Báo cáo (Chưa khả dụng)</button>
</div>

<div class="row g-4 mb-4">
    <!-- Card 1: Total Rankings -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Xếp Hạng</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalRankings) ? (int)$totalRankings : 0 ?></div>
                    </div>
                    <div class="stat-icon info"><i class="fas fa-list-ol"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Evaluated Series -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Manga đã chấm</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($evaluatedSeries) ? (int)$evaluatedSeries : 0 ?></div>
                    </div>
                    <div class="stat-icon primary"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Ungraded Series -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Manga chưa chấm</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($ungradedSeries) ? (int)$ungradedSeries : 0 ?></div>
                    </div>
                    <div class="stat-icon warning"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Top Ranking Series -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Series Hạng 1</div>
                        <div class="h5 mb-0 fw-bold text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($topRankingSeriesName) ?>"><?= htmlspecialchars($topRankingSeriesName) ?></div>
                    </div>
                    <div class="stat-icon success"><i class="fas fa-trophy"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-arrow-up text-success me-2"></i>Top 5 Series (Kỳ hiện tại)</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($top5Series)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($top5Series as $series): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <span class="badge bg-success me-2 rounded-pill">#<?= $series['rank_position'] ?></span>
                                    <strong><?= htmlspecialchars($series['series_title']) ?></strong>
                                </div>
                                <span class="text-muted fw-bold"><?= htmlspecialchars($series['score']) ?> đ</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-arrow-down text-danger me-2"></i>Bottom 5 Series (Kỳ hiện tại)</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($bottom5Series)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($bottom5Series as $series): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <span class="badge bg-danger me-2 rounded-pill">#<?= $series['rank_position'] ?></span>
                                    <strong><?= htmlspecialchars($series['series_title']) ?></strong>
                                </div>
                                <span class="text-muted fw-bold"><?= htmlspecialchars($series['score']) ?> đ</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
