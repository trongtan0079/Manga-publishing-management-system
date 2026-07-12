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

<?php require_once __DIR__ . '/../layouts/welcome_banner.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Báo cáo Ban Giám Đốc</h2>
        <p class="text-muted text-xs mb-0">Theo dõi doanh thu, bảng xếp hạng và toàn cảnh hoạt động xuất bản.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=exportRanking" class="btn btn-success shadow-sm"><i class="fas fa-file-excel me-2"></i>Tải Báo cáo Xếp Hạng (CSV)</a>
</div>

<style>
    .stat-card-link {
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .stat-card-link:hover {
        transform: translateY(-5px);
    }
    .stat-card-link .card {
        border: none !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02), 0 10px 15px rgba(0,0,0,0.03) !important;
    }
    .stat-card-link:hover .card {
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
</style>

<div class="row g-4 mb-4">
    <!-- Card 1: Total Rankings -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="stat-card-link">
            <div class="card stat-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Xếp Hạng</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= isset($totalRankings) ? (int)$totalRankings : 0 ?></div>
                        </div>
                        <div class="stat-icon info" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-list-ol"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 2: Evaluated Series -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="stat-card-link">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Manga đã chấm</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= isset($evaluatedSeries) ? (int)$evaluatedSeries : 0 ?></div>
                        </div>
                        <div class="stat-icon primary" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 3: Ungraded Series -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=create" class="stat-card-link">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Manga chưa chấm</div>
                            <div class="h3 mb-0 fw-bold text-white"><?= isset($ungradedSeries) ? (int)$ungradedSeries : 0 ?></div>
                        </div>
                        <div class="stat-icon warning" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 4: Top Ranking Series -->
    <div class="col-xl-3 col-md-6">
        <?php 
        $topSeriesUrl = BASE_PATH . '/index.php?controller=seriesRanking&action=index';
        if (!empty($top5Series)) {
            $topSeriesUrl = BASE_PATH . '/index.php?controller=series&action=show&id=' . $top5Series[0]['series_id'];
        }
        ?>
        <a href="<?= $topSeriesUrl ?>" class="stat-card-link">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="overflow-hidden flex-grow-1 me-2">
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Series Hạng 1</div>
                            <div class="h5 mb-0 fw-bold text-white text-truncate" title="<?= htmlspecialchars($topRankingSeriesName) ?>"><?= htmlspecialchars($topRankingSeriesName) ?></div>
                        </div>
                        <div class="stat-icon success flex-shrink-0" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-trophy"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>Biểu đồ Điểm số Xếp hạng (Kỳ: <?= htmlspecialchars($latestPeriod ?? 'Chưa có') ?>)</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($chartSeriesData)): ?>
                    <div style="height: 280px; position: relative;">
                        <canvas id="chartSeriesRankings"></canvas>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chart-bar fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                        <p class="mb-0 small">Chưa có dữ liệu xếp hạng kỳ này để hiển thị biểu đồ</p>
                    </div>
                <?php endif; ?>
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

<?php if (!empty($chartSeriesData)): ?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById('chartSeriesRankings');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            
            const labels = <?= json_encode(array_column($chartSeriesData, 'series_title')) ?>;
            const data = <?= json_encode(array_map(function($item) { return (float)$item['score']; }, $chartSeriesData)) ?>;
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Điểm số xếp hạng',
                        data: data,
                        backgroundColor: 'rgba(99, 102, 241, 0.75)', // Indigo style color
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.raw + ' điểm';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + ' đ';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
