<?php
if (!defined('BASE_PATH')) {
    header('Location: /index.php');
    exit;
}
$pageTitle = 'Báo cáo Ban Giám đốc';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    .welcome-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px);
        background-size: 20px 20px;
        color: #fff;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }
    .stat-card-glow {
        transition: all 0.3s ease;
        border-radius: 14px;
        background: #ffffff;
    }
    .stat-card-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    .soft-bg-primary { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
    .soft-bg-success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .soft-bg-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .soft-bg-info { background: rgba(6, 182, 212, 0.1); color: #06b6d4; }
</style>

<!-- Banner Chào Mừng -->
<div class="welcome-banner p-4 mb-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="h3 fw-bold mb-2">Báo cáo Ban Giám Đốc (Board)</h1>
            <p class="text-slate-300 mb-0 opacity-80" style="font-size: 14px;">Giám sát bảng xếp hạng điểm số, phê duyệt định hướng xuất bản và theo dõi doanh thu/tiến độ phát hành truyện.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="btn btn-light btn-sm fw-bold px-3 py-2" style="border-radius: 8px;">
                Chi tiết Bảng Xếp Hạng
            </a>
        </div>
    </div>
</div>

<!-- 4 KPI Stats -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Tổng Xếp Hạng</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalRankings ?></h3>
                    <small class="text-muted text-xs">Bản ghi đánh giá</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-info" style="width: 48px; height: 48px;">
                    <i class="fas fa-list-ol fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Manga đã chấm</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $evaluatedSeries ?></h3>
                    <small class="text-success text-xs">Đã chấm điểm</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-primary" style="width: 48px; height: 48px;">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Manga chưa chấm</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $ungradedSeries ?></h3>
                    <small class="text-warning text-xs">Đang chờ kỳ xếp hạng</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-warning" style="width: 48px; height: 48px;">
                    <i class="fas fa-hourglass-half fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Series Dẫn Đầu</span>
                    <h5 class="fw-bold mb-0 text-slate-800 text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($topRankingSeriesName) ?>"><?= htmlspecialchars($topRankingSeriesName) ?></h5>
                    <small class="text-success text-xs"><i class="fas fa-trophy me-1 text-warning"></i>Hạng 1</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-success" style="width: 48px; height: 48px;">
                    <i class="fas fa-trophy fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Trái: So sánh Top 5 và Bottom 5 -->
    <div class="col-lg-8">
        <div class="row g-4">
            <!-- Top 5 -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-arrow-up text-success me-2"></i>Top 5 Series Thịnh hành</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush px-3">
                            <?php if (!empty($top5Series)): ?>
                                <?php foreach ($top5Series as $index => $series): ?>
                                    <?php 
                                        $prevRanking = $rankingModel->getPreviousRanking($series['series_id'], $series['period_start_date']);
                                        $trendArrow = '<i class="fas fa-minus text-muted"></i>';
                                        if ($prevRanking) {
                                            if ($series['rank_position'] < $prevRanking['rank_position']) {
                                                $trendArrow = '<i class="fas fa-arrow-up text-success"></i>';
                                            } elseif ($series['rank_position'] > $prevRanking['rank_position']) {
                                                $trendArrow = '<i class="fas fa-arrow-down text-danger"></i>';
                                            }
                                        }
                                    ?>
                                    <div class="list-group-item px-0 py-2 border-0 bg-transparent d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold text-primary me-2" style="width: 15px;">#<?= $series['rank_position'] ?></span>
                                            <div class="text-truncate text-slate-800" style="max-width: 150px; font-size: 13px; font-weight: bold;"><?= htmlspecialchars($series['series_title']) ?></div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success-soft text-success text-xs"><?= number_format($series['score'], 1) ?></span>
                                            <?= $trendArrow ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted small">Chưa xếp hạng.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom 5 -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-arrow-down text-danger me-2"></i>Dự án thành tích thấp (Chú ý)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush px-3">
                            <?php if (!empty($bottom5Series)): ?>
                                <?php foreach ($bottom5Series as $series): ?>
                                    <?php 
                                        $prevRanking = $rankingModel->getPreviousRanking($series['series_id'], $series['period_start_date']);
                                        $trendArrow = '<i class="fas fa-minus text-muted"></i>';
                                        if ($prevRanking) {
                                            if ($series['rank_position'] < $prevRanking['rank_position']) {
                                                $trendArrow = '<i class="fas fa-arrow-up text-success"></i>';
                                            } elseif ($series['rank_position'] > $prevRanking['rank_position']) {
                                                $trendArrow = '<i class="fas fa-arrow-down text-danger"></i>';
                                            }
                                        }
                                    ?>
                                    <div class="list-group-item px-0 py-2 border-0 bg-transparent d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold text-danger me-2" style="width: 15px;">#<?= $series['rank_position'] ?></span>
                                            <div class="text-truncate text-slate-800" style="max-width: 150px; font-size: 13px; font-weight: bold;"><?= htmlspecialchars($series['series_title']) ?></div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-danger-soft text-danger text-xs"><?= number_format($series['score'], 1) ?></span>
                                            <?= $trendArrow ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted small">Không có dữ liệu truyện thành tích thấp.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phải: Lịch xuất bản tuần này -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-print text-primary me-2"></i>Lịch xuất bản tuần này</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush px-3">
                    <?php if (!empty($scheduledReleases)): ?>
                        <?php foreach ($scheduledReleases as $release): ?>
                            <div class="list-group-item px-0 py-2 border-light bg-transparent">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-slate-800" style="font-size: 12px;"><?= htmlspecialchars($release['series_title']) ?></span>
                                    <span class="badge bg-primary-soft text-primary text-xs">Ch.<?= htmlspecialchars($release['chapter_number']) ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center" style="font-size: 11px;">
                                    <span class="text-muted"><i class="far fa-clock me-1"></i><?= date('d/m/Y', strtotime($release['updated_at'])) ?></span>
                                    <span class="text-success fw-bold"><i class="fas fa-check me-1"></i>Sẵn sàng</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted small">Không có chapter nào có lịch xuất bản trong tuần này.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
