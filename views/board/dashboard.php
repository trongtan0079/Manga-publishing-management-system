<?php 
/**
 * @var int $evaluatedSeries
 * @var string|null $latestPeriod
 * @var string $topRankingSeriesName
 * @var array $top5Series
 * @var array $bottom5Series
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
    <button class="btn btn-success shadow-sm"><i class="fas fa-file-invoice-dollar me-2"></i>Tải Báo cáo</button>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Series đã đánh giá (Kỳ này)</div>
                        <div class="h3 mb-0 fw-bold"><?= $evaluatedSeries ?></div>
                    </div>
                    <div class="stat-icon primary"><i class="fas fa-book"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Kỳ Đánh Giá Hiện Tại</div>
                        <div class="h5 mb-0 fw-bold"><?= $latestPeriod ? date('d/m/Y', strtotime($latestPeriod)) : 'Chưa có' ?></div>
                    </div>
                    <div class="stat-icon info"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Series Hạng 1</div>
                        <div class="h5 mb-0 fw-bold text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($topRankingSeriesName) ?>"><?= htmlspecialchars($topRankingSeriesName) ?></div>
                    </div>
                    <div class="stat-icon warning"><i class="fas fa-trophy"></i></div>
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
                                <span class="text-muted fw-bold"><?= $series['score'] ?> đ</span>
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
                                <span class="text-muted fw-bold"><?= $series['score'] ?> đ</span>
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

<div class="row">
    <div class="col-lg-8">
        <!-- Optional additional charts or tables could go here -->
    </div>
    <div class="col-lg-4">
        <?php require_once __DIR__ . '/../shared/dashboard_notifications.php'; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
