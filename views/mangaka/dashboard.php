<?php 
/**
 * @var int $totalSeries
 * @var int $totalChapters
 * @var int $totalPages
 * @var int $totalTasks
 * @var array $latestRankings
 * @var \SeriesRanking $rankingModel
 */
$pageTitle = 'Không gian sáng tác (Mangaka)';
$current_page = 'dashboard';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Không gian Sáng tác</h2>
        <p class="text-muted text-xs mb-0">Quản lý tác phẩm, nộp bản thảo và theo dõi phản hồi từ Biên tập viên.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-upload me-2"></i>Nộp Bản Thảo Mới</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Series đang quản lý</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalSeries) ? $totalSeries : 0 ?></div>
                    </div>
                    <div class="stat-icon info"><i class="fas fa-book"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Chapter đang thực hiện</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalChapters) ? $totalChapters : 0 ?></div>
                    </div>
                    <div class="stat-icon primary"><i class="fas fa-file-alt"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Bản thảo</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalSubmissions) ? $totalSubmissions : 0 ?></div>
                    </div>
                    <div class="stat-icon success"><i class="fas fa-file-upload"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Bản thảo chờ duyệt</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($pendingReviews) ? $pendingReviews : 0 ?></div>
                    </div>
                    <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Pages</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalPages) ? $totalPages : 0 ?></div>
                    </div>
                    <div class="stat-icon primary"><i class="fas fa-images"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Tổng Tasks</div>
                        <div class="h3 mb-0 fw-bold"><?= isset($totalTasks) ? $totalTasks : 0 ?></div>
                    </div>
                    <div class="stat-icon info"><i class="fas fa-tasks"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-line text-primary me-2"></i>Tổng quan Xếp hạng Mới nhất</h6>
                <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Series</th>
                                <th>Kỳ đánh giá</th>
                                <th>Thứ hạng hiện tại</th>
                                <th>Điểm số mới nhất</th>
                                <th>Biến động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($latestRankings)): ?>
                                <?php foreach ($latestRankings as $ranking): ?>
                                    <?php 
                                        $prevRanking = $rankingModel->getPreviousRanking($ranking['series_id'], $ranking['period_start_date']);
                                        $trendIcon = '<span class="text-secondary fw-bold"><i class="fas fa-minus"></i> ▬ Mới</span>';
                                        
                                        if ($prevRanking) {
                                            if ($ranking['rank_position'] < $prevRanking['rank_position']) {
                                                $trendIcon = '<span class="text-success fw-bold"><i class="fas fa-arrow-up"></i> ▲ Tăng hạng</span>';
                                            } elseif ($ranking['rank_position'] > $prevRanking['rank_position']) {
                                                $trendIcon = '<span class="text-danger fw-bold"><i class="fas fa-arrow-down"></i> ▼ Giảm hạng</span>';
                                            } else {
                                                $trendIcon = '<span class="text-secondary fw-bold"><i class="fas fa-minus"></i> ▬ Không thay đổi</span>';
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td class="ps-4"><strong><?= htmlspecialchars($ranking['series_title']) ?></strong></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars(date('m/Y', strtotime($ranking['period_start_date']))) ?></span></td>
                                        <td><span class="fs-5 fw-bold text-primary">#<?= htmlspecialchars($ranking['rank_position']) ?></span></td>
                                        <td><span class="badge bg-success"><?= htmlspecialchars($ranking['score']) ?></span></td>
                                        <td><?= $trendIcon ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Chưa có xếp hạng nào cho các bộ truyện của bạn.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="m-0 fw-bold"><i class="fas fa-history text-primary me-2"></i>Hoạt động Gần đây</h6>
            </div>
            <div class="card-body text-center py-5">
                <div class="text-muted mb-2"><i class="fas fa-inbox fs-1 opacity-25"></i></div>
                <p class="text-muted mb-0">Chưa có hoạt động gần đây.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <?php require_once __DIR__ . '/../shared/dashboard_notifications.php'; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
