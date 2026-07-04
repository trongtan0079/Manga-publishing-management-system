<?php 
/**
 * View: Giao diện bảng điều khiển cải tiến dành cho Họa sĩ chính (dashboard.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * 
 * @var int $totalSeries Tổng số bộ truyện họa sĩ sở hữu
 * @var int $totalChapters Tổng số chương truyện họa sĩ sở hữu
 * @var int $totalPages Tổng số trang vẽ
 * @var int $totalTasks Tổng số công việc đã giao cho trợ lý
 * @var int $pendingReviews Số lượng bản thảo chờ duyệt
 * @var array $latestRankings Danh sách xếp hạng mới nhất
 * @var \SeriesRanking $rankingModel Thực thể mô hình xếp hạng
 * @var array $mySeries Danh sách Series của tác giả
 * @var array $recentTasks Danh sách công việc sắp đến hạn
 * @var array $recentSubmissions Danh sách bản vẽ trợ lý nộp gần đây
 * @var array $recentActivitiesData Nhật ký hoạt động gần đây
 */
$pageTitle = 'Không gian sáng tác (Mangaka)';
$current_page = 'dashboard';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Cấu hình phông màu violet tùy chỉnh cho Bootstrap
?>
<style>
.bg-violet {
    background-color: #7c3aed !important;
}
.bg-danger-soft {
    background-color: rgba(239, 68, 68, 0.08) !important;
}
.bg-primary-soft {
    background-color: rgba(79, 70, 229, 0.08) !important;
}
.bg-success-soft {
    background-color: rgba(16, 185, 129, 0.08) !important;
}
.bg-info-soft {
    background-color: rgba(14, 165, 233, 0.08) !important;
}
.bg-warning-soft {
    background-color: rgba(245, 158, 11, 0.08) !important;
}
.text-slate-200 {
    color: #cbd5e1 !important;
}
.text-slate-300 {
    color: #94a3b8 !important;
}
.border-slate-700 {
    border-color: #334155 !important;
}
.fs-7 {
    font-size: 0.75rem !important;
}
.z-index-2 {
    z-index: 2;
}
.stat-card-glow:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg) !important;
    transition: all 0.3s ease;
}
.calendar-table th, .calendar-table td {
    padding: 3px 0;
    text-align: center;
    font-size: 11px;
}
.calendar-table td.active-day {
    background-color: #ef4444;
    color: #fff;
    border-radius: 50%;
    font-weight: bold;
}
.timeline-item {
    position: relative;
    padding-left: 20px;
    border-left: 2px solid #e2e8f0;
}
.timeline-item::after {
    content: '';
    position: absolute;
    left: -6px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #10b981;
    border: 2px solid #fff;
}
.timeline-item.warning::after {
    background-color: #f59e0b;
}
.timeline-item.danger::after {
    background-color: #ef4444;
}
.timeline-item.info::after {
    background-color: #0ea5e9;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 fw-bold">Không gian Sáng tác</h2>
        <p class="text-muted small mb-0">Theo dõi tiến độ, nộp bản thảo và chạy phân vùng/tô màu AI trên trang vẽ.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=create" class="btn btn-outline-primary btn-sm px-3"><i class="fas fa-plus me-2"></i>Tạo Series Mới</a>
        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create" class="btn btn-primary btn-sm px-3 shadow-sm"><i class="fas fa-upload me-2"></i>Nộp Bản Thảo Chương</a>
    </div>
</div>

<!-- 1. Banner Chào Mừng (Welcome Banner) -->
<div class="card border-0 mb-4 position-relative overflow-hidden text-white" style="border-radius: var(--radius-md); background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);">
    <div class="row align-items-center g-0">
        <!-- Cột trái: Tiến độ công việc -->
        <div class="col-lg-4 p-4 z-index-2 position-relative">
            <h5 class="text-uppercase text-danger fw-bold fs-7 mb-3" style="letter-spacing: 0.1em;">Tiếp tục công việc</h5>
            <?php if (!empty($mySeries)): ?>
                <?php 
                    $firstSeries = $mySeries[0];
                    $displayTitle = htmlspecialchars($firstSeries['title']);
                ?>
                <h3 class="fw-bold mb-1"><?= $displayTitle ?></h3>
                <p class="text-slate-300 small mb-3">Chương sáng tác hoạt động gần nhất</p>
                <div class="progress bg-secondary mb-3" style="height: 6px; border-radius: 3px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= $firstSeries['series_id'] ?>" class="btn btn-danger btn-sm px-4 fw-bold shadow-sm" style="border-radius: var(--radius-sm);">Tiếp tục ngay <i class="fas fa-chevron-right ms-2"></i></a>
            <?php else: ?>
                <h3 class="fw-bold mb-2">Bắt đầu tác phẩm mới</h3>
                <p class="text-slate-300 small mb-3">Tạo bộ truyện mới để bắt đầu hành trình sáng tác.</p>
                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=create" class="btn btn-danger btn-sm px-4 fw-bold shadow-sm" style="border-radius: var(--radius-sm);">Bắt đầu ngay <i class="fas fa-plus ms-2"></i></a>
            <?php endif; ?>
        </div>
        <!-- Cột giữa: Banner ảnh vẽ của Manga Artist -->
        <div class="col-lg-5 text-center d-none d-lg-block position-relative" style="height: 220px; overflow: hidden;">
            <img src="<?= BASE_PATH ?>/assets/images/manga_banner_artwork.png" alt="Manga Artist" class="img-fluid" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%; height: 100%; object-fit: cover; opacity: 0.85;">
        </div>
        <!-- Cột phải: Trích dẫn truyền cảm hứng -->
        <div class="col-lg-3 p-4 text-center d-none d-lg-flex align-items-center justify-content-center border-start border-slate-700" style="height: 220px;">
            <div>
                <p class="fst-italic text-slate-200 small mb-2">"Mỗi trang truyện là một bước gần hơn đến tác phẩm hoàn hảo."</p>
                <span class="text-danger small fw-bold">— Keep Drawing ✍️</span>
            </div>
        </div>
    </div>
</div>

<!-- 2. Thẻ KPI Stats (5 cột) -->
<div class="row g-3 mb-4">
    <!-- Card 1 -->
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100 py-2 stat-card-glow" style="border-radius: var(--radius); background-color: var(--card-bg);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-danger-soft p-3 rounded-3 me-3 text-danger"><i class="fas fa-book fa-2x"></i></div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-muted text-uppercase mb-1" style="font-size: 11px;">Series</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalSeries ?></div>
                        <small class="text-success text-xs fw-bold"><i class="fas fa-arrow-up me-1"></i>2 series mới</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100 py-2 stat-card-glow" style="border-radius: var(--radius); background-color: var(--card-bg);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-soft p-3 rounded-3 me-3 text-primary"><i class="fas fa-file-alt fa-2x"></i></div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-muted text-uppercase mb-1" style="font-size: 11px;">Chapters</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalChapters ?></div>
                        <small class="text-success text-xs fw-bold"><i class="fas fa-arrow-up me-1"></i>5 chapter mới</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100 py-2 stat-card-glow" style="border-radius: var(--radius); background-color: var(--card-bg);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success-soft p-3 rounded-3 me-3 text-success"><i class="fas fa-images fa-2x"></i></div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-muted text-uppercase mb-1" style="font-size: 11px;">Pages</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalPages ?></div>
                        <small class="text-success text-xs fw-bold"><i class="fas fa-arrow-up me-1"></i>24 trang mới</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100 py-2 stat-card-glow" style="border-radius: var(--radius); background-color: var(--card-bg);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info-soft p-3 rounded-3 me-3 text-info"><i class="fas fa-tasks fa-2x"></i></div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-muted text-uppercase mb-1" style="font-size: 11px;">Tasks</div>
                        <div class="h3 mb-0 fw-bold"><?= $totalTasks ?></div>
                        <small class="text-success text-xs fw-bold"><i class="fas fa-arrow-up me-1"></i>8 task mới</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 5 -->
    <div class="col-md col-12">
        <div class="card border-0 shadow-sm h-100 py-2 stat-card-glow" style="border-radius: var(--radius); background-color: var(--card-bg);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning-soft p-3 rounded-3 me-3 text-warning"><i class="fas fa-clock fa-2x"></i></div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-muted text-uppercase mb-1" style="font-size: 11px;">Chờ duyệt</div>
                        <div class="h3 mb-0 fw-bold"><?= $pendingReviews ?></div>
                        <small class="text-warning text-xs fw-bold"><i class="fas fa-exclamation-triangle me-1"></i>Bản thảo mới</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Khu vực giữa: Series đang vẽ & Task sắp đến hạn -->
<div class="row mb-4">
    <!-- Cột trái: Series đang thực hiện -->
    <div class="col-lg-7 mb-4 mb-lg-0">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md);">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fas fa-folder-open text-primary me-2"></i>Series đang thực hiện</h5>
                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="text-primary text-decoration-none small">Xem tất cả <i class="fas fa-chevron-right ms-1"></i></a>
            </div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <?php if (!empty($mySeries)): ?>
                        <?php foreach (array_slice($mySeries, 0, 4) as $index => $series): ?>
                            <?php 
                                $coverPath = !empty($series['cover_image']) ? BASE_PATH . '/' . ltrim($series['cover_image'], '/') : BASE_PATH . '/assets/images/default_cover.jpg';
                                $progress = ($index % 3 == 0) ? 80 : (($index % 3 == 1) ? 45 : 60);
                                $progressColor = ($index % 3 == 0) ? 'bg-danger' : (($index % 3 == 1) ? 'bg-violet' : 'bg-primary');
                                if ($series['status'] === 'completed') {
                                    $progress = 100;
                                    $progressColor = 'bg-success';
                                }
                            ?>
                            <div class="col">
                                <div class="card h-100 border border-light position-relative" style="border-radius: var(--radius); overflow: hidden;">
                                    <?php if ($series['status'] === 'completed'): ?>
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2 z-index-2">Hoàn thành</span>
                                    <?php endif; ?>
                                    <div style="height: 120px; overflow: hidden; background-color: #f1f5f9;">
                                        <img src="<?= $coverPath ?>" alt="<?= htmlspecialchars($series['title']) ?>" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='<?= BASE_PATH ?>/assets/images/default_cover.jpg'">
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-1 text-truncate">
                                            <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= $series['series_id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($series['title']) ?></a>
                                        </h6>
                                        <p class="text-muted small mb-2">Trạng thái: <?= htmlspecialchars($series['status'] === 'ongoing' ? 'Đang sáng tác' : ($series['status'] === 'completed' ? 'Hoàn thành' : 'Đang lên kế hoạch')) ?></p>
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="progress flex-grow-1 bg-light" style="height: 4px; border-radius: 2px;">
                                                <div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="ms-2 small fw-bold" style="font-size: 11px;"><?= $progress ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 py-5 text-center my-auto">
                            <i class="fas fa-folder-plus fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Chưa có tác phẩm nào đang sáng tác.</h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Task sắp đến hạn -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md);">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fas fa-calendar-alt text-primary me-2"></i>Task sắp đến hạn</h5>
                <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index" class="text-primary text-decoration-none small">Xem tất cả <i class="fas fa-chevron-right ms-1"></i></a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php if (!empty($recentTasks)): ?>
                        <?php foreach ($recentTasks as $task): ?>
                            <?php 
                                $daysDiff = (strtotime($task['due_date']) - time()) / (60 * 60 * 24);
                                $daysDiff = ceil($daysDiff);
                                $badgeClass = 'bg-light text-dark';
                                $badgeLabel = $daysDiff . ' ngày nữa';
                                if ($daysDiff <= 0) {
                                    $badgeClass = 'bg-danger-soft text-danger';
                                    $badgeLabel = 'Hôm nay';
                                } elseif ($daysDiff == 1) {
                                    $badgeClass = 'bg-warning-soft text-warning';
                                    $badgeLabel = 'Ngày mai';
                                }
                            ?>
                            <div class="list-group-item px-0 py-3 border-light bg-transparent">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="fw-bold mb-0" style="font-size: 13px;">Ch.<?= htmlspecialchars($task['chapter_number']) ?> - Trang <?= htmlspecialchars($task['page_number']) ?> - <?= htmlspecialchars($task['title']) ?></h6>
                                        <small class="text-muted">Trợ lý: <strong><?= htmlspecialchars($task['assistant_name'] ?? 'Chưa giao') ?></strong> | Loại: <?= ucfirst(htmlspecialchars($task['task_type'] === 'background' ? 'Vẽ nền' : ($task['task_type'] === 'inking' ? 'Đi nét' : ($task['task_type'] === 'coloring' ? 'Lên màu' : 'Khác')))) ?></small>
                                    </div>
                                    <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                </div>
                                <div class="progress bg-light" style="height: 4px; border-radius: 2px; width: 85%;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5 my-auto">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Không có công việc nào sắp đến hạn.</h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. Hàng cuối: 4 Widget nhỏ (Bản thảo, Lịch, Xếp hạng, Nhật ký) -->
<div class="row">
    <!-- Widget 1: Bản thảo gần đây -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius); overflow: hidden;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold m-0 text-dark"><i class="fas fa-file-upload text-danger me-2"></i>Bản thảo gần đây</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php if (!empty($recentSubmissions)): ?>
                        <?php foreach ($recentSubmissions as $sub): ?>
                            <?php 
                                $subImg = !empty($sub['image_url']) ? BASE_PATH . '/' . ltrim($sub['image_url'], '/') : BASE_PATH . '/assets/images/default_cover.jpg';
                                $subStatusBadge = 'bg-secondary';
                                $subStatusText = 'Chờ duyệt';
                                if ($sub['status'] === 'approved') {
                                    $subStatusBadge = 'bg-success';
                                    $subStatusText = 'Đã duyệt';
                                } elseif ($sub['status'] === 'rejected') {
                                    $subStatusBadge = 'bg-danger';
                                    $subStatusText = 'Cần sửa';
                                }
                            ?>
                            <div class="col-6 mb-2">
                                <div class="border rounded p-1 text-center bg-light" style="height: 100px; overflow: hidden; position: relative;">
                                    <img src="<?= $subImg ?>" alt="Submission" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='<?= BASE_PATH ?>/assets/images/default_cover.jpg'">
                                    <span class="badge <?= $subStatusBadge ?> position-absolute bottom-0 start-0 m-1" style="font-size: 9px; opacity: 0.9;"><?= $subStatusText ?></span>
                                </div>
                                <div class="text-truncate small fw-bold px-1" style="font-size: 11px;">Ch.<?= htmlspecialchars($sub['chapter_number']) ?> - P.<?= htmlspecialchars($sub['page_number']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5 text-muted small">Chưa nộp bản thảo.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Widget 2: Lịch xuất bản động -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius);">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold m-0 text-dark"><i class="fas fa-calendar text-warning me-2"></i>Lịch xuất bản</h6>
            </div>
            <div class="card-body py-2">
                <?php
                    $year = date('Y');
                    $month = date('m');
                    $monthName = date('m / Y');
                    $firstDay = mktime(0, 0, 0, $month, 1, $year);
                    $dayOfWeek = date('w', $firstDay); // 0 (CN) - 6 (T7)
                    if ($dayOfWeek == 0) $dayOfWeek = 7; // Chuyển CN về 7 để dễ canh
                    $daysInMonth = date('t', $firstDay);
                    $today = date('j');
                ?>
                <div class="text-center fw-bold small text-primary mb-2"><?= $monthName ?></div>
                <table class="w-100 calendar-table">
                    <thead>
                        <tr class="text-muted">
                            <th>T2</th><th>T3</th><th>T4</th><th>T5</th><th>T6</th><th>T7</th><th>CN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php 
                            // In ô trống đầu tháng
                            for ($i = 1; $i < $dayOfWeek; $i++) {
                                echo '<td></td>';
                            }
                            
                            // In các ngày trong tháng
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                if (($day + $dayOfWeek - 2) % 7 == 0 && $day != 1) {
                                    echo '</tr><tr>';
                                }
                                $class = ($day == $today) ? 'class="active-day"' : '';
                                echo "<td {$class}>{$day}</td>";
                            }
                            
                            // Điền ô trống cuối tháng
                            $totalCells = $daysInMonth + $dayOfWeek - 1;
                            $rem = $totalCells % 7;
                            if ($rem > 0) {
                                for ($i = $rem; $i < 7; $i++) {
                                    echo '<td></td>';
                                }
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-2 text-center" style="font-size: 11px;">
                    <span class="badge bg-danger-soft text-danger py-1 px-2"><i class="fas fa-circle me-1"></i>Hôm nay: Ngày <?= $today ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Widget 3: Xếp hạng Manga (Mới nhất) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius);">
            <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0 text-dark"><i class="fas fa-chart-line text-success me-2"></i>Xếp hạng Manga</h6>
                <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="text-decoration-none small text-slate-300" style="font-size: 11px;">Xem hết</a>
            </div>
            <div class="card-body px-3 py-2">
                <div class="list-group list-group-flush">
                    <?php if (!empty($latestRankings)): ?>
                        <?php foreach (array_slice($latestRankings, 0, 3) as $index => $ranking): ?>
                            <?php 
                                $prevRanking = $rankingModel->getPreviousRanking($ranking['series_id'], $ranking['period_start_date']);
                                $trendArrow = '<i class="fas fa-minus text-muted"></i>';
                                if ($prevRanking) {
                                    if ($ranking['rank_position'] < $prevRanking['rank_position']) {
                                        $trendArrow = '<i class="fas fa-arrow-up text-success"></i>';
                                    } elseif ($ranking['rank_position'] > $prevRanking['rank_position']) {
                                        $trendArrow = '<i class="fas fa-arrow-down text-danger"></i>';
                                    }
                                }
                            ?>
                            <div class="list-group-item px-0 py-2 border-0 bg-transparent d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-primary me-2" style="width: 15px;">#<?= $ranking['rank_position'] ?></span>
                                    <div class="text-truncate" style="max-width: 120px; font-size: 12px; font-weight: bold;"><?= htmlspecialchars($ranking['series_title']) ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success-soft text-success me-2" style="font-size: 10px;"><?= number_format($ranking['score'], 1) ?></span>
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

    <!-- Widget 4: Hoạt động gần đây (Timeline) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius);">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold m-0 text-dark"><i class="fas fa-history text-info me-2"></i>Hoạt động gần đây</h6>
            </div>
            <div class="card-body px-3 py-2" style="max-height: 240px; overflow-y: auto;">
                <?php if (!empty($recentActivitiesData)): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($recentActivitiesData as $act): ?>
                            <?php 
                                $warnClass = 'info';
                                if (strpos(strtolower($act['content']), 'cảnh báo') !== false || strpos(strtolower($act['content']), 'nguy cơ') !== false) {
                                    $warnClass = 'danger';
                                } elseif (strpos(strtolower($act['content']), 'giao việc') !== false) {
                                    $warnClass = 'warning';
                                }
                            ?>
                            <div class="timeline-item <?= $warnClass ?>">
                                <div class="small fw-bold text-dark" style="font-size: 11px;"><?= htmlspecialchars($act['content']) ?></div>
                                <div class="text-muted" style="font-size: 9px;"><?= date('H:i d/m/Y', strtotime($act['created_at'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted small">Chưa ghi nhận hoạt động.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
