<?php
if (!defined('BASE_PATH')) {
    header('Location: /index.php');
    exit;
}
$pageTitle = 'Bảng điều khiển Tác giả';
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
    .soft-bg-danger { background: rgba(239, 68, 68, 0.1); color: #ef4848; }
    .soft-bg-info { background: rgba(6, 182, 212, 0.1); color: #06b6d4; }
    
    .calendar-table th {
        text-align: center;
        padding: 5px 0;
        font-size: 11px;
    }
    .calendar-table td {
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        font-weight: 500;
        border-radius: 4px;
        color: #475569;
    }
    .calendar-table td.active-day {
        background: #ef4444;
        color: #fff;
        font-weight: bold;
    }
    .timeline-item {
        border-left: 2px solid #e2e8f0;
        padding-left: 15px;
        position: relative;
        padding-bottom: 12px;
    }
    .timeline-item::before {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #cbd5e1;
        position: absolute;
        left: -6px;
        top: 4px;
    }
    .timeline-item.danger::before { background: #ef4444; }
    .timeline-item.warning::before { background: #f59e0b; }
    .timeline-item.info::before { background: #3b82f6; }
</style>

<!-- Banner Chào Mừng -->
<div class="welcome-banner p-4 mb-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="h3 fw-bold mb-2">Xin chào họa sĩ, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Tác giả') ?>!</h1>
            <p class="text-slate-300 mb-0 opacity-80" style="font-size: 14px;">Hôm nay là một ngày tuyệt vời để tiếp tục hoàn thiện các khung tranh vẽ. Chúc bạn có nhiều cảm hứng sáng tác!</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="btn btn-light btn-sm fw-bold px-3 py-2" style="border-radius: 8px;">
                Quản lý Series truyện
            </a>
        </div>
    </div>
</div>

<!-- 5 KPI hàng ngang -->
<div class="row g-3 mb-4">
    <div class="col-xl col-md-4">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Truyện đang vẽ</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalSeries ?></h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-primary" style="width: 44px; height: 44px;">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Chương chương</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalChapters ?></h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-success" style="width: 44px; height: 44px;">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Tổng số Trang</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalPages ?></h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-warning" style="width: 44px; height: 44px;">
                    <i class="fas fa-images"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Việc đang giao</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $totalTasks ?></h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-danger" style="width: 44px; height: 44px;">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Bản thảo chờ duyệt</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $pendingReviews ?></h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-info" style="width: 44px; height: 44px;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lưới Active Series & Deadline Tasks -->
<div class="row g-4 mb-4">
    <!-- Trái: Dự án truyện đang thực hiện -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-feather text-primary me-2"></i>Dự án Manga đang thực hiện</h6>
            </div>
            <div class="card-body pt-2">
                <div class="row g-3">
                    <?php if (!empty($mySeries)): ?>
                        <?php foreach ($mySeries as $series): ?>
                            <?php 
                                $coverUrl = !empty($series['cover_image']) ? BASE_PATH . '/' . ltrim($series['cover_image'], '/') : BASE_PATH . '/assets/images/default_cover.jpg';
                            ?>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light h-100 d-flex flex-column justify-content-between">
                                    <div class="d-flex gap-3 mb-2">
                                        <img src="<?= $coverUrl ?>" alt="Cover" class="rounded shadow-sm" style="width: 50px; height: 65px; object-fit: cover;" onerror="this.src='<?= BASE_PATH ?>/assets/images/default_cover.jpg'">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-slate-800" style="font-size: 13px;"><?= htmlspecialchars($series['title']) ?></h6>
                                            <span class="badge bg-light text-dark text-xs border"><?= ucfirst(htmlspecialchars($series['status'])) ?></span>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-top">
                                        <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 11px;">
                                            <span>Lịch: <strong><?= ucfirst(htmlspecialchars($series['release_schedule'])) ?></strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0 small">Chưa có dự án truyện nào được gán.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Phải: Nhiệm vụ Trợ lý sắp đến hạn -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-hourglass-half text-danger me-2"></i>Hạn chót công việc Trợ lý</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush px-3">
                    <?php if (!empty($recentTasks)): ?>
                        <?php foreach ($recentTasks as $task): ?>
                            <?php 
                                $daysDiff = (strtotime($task['due_date']) - time()) / 86400;
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
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-slate-800" style="font-size: 13px;">Ch.<?= htmlspecialchars($task['chapter_number']) ?> - Trang <?= htmlspecialchars($task['page_number']) ?> - <?= htmlspecialchars($task['title']) ?></h6>
                                        <small class="text-muted" style="font-size: 11px;">Trợ lý: <strong><?= htmlspecialchars($task['assistant_name'] ?? 'Chưa giao') ?></strong></small>
                                    </div>
                                    <span class="badge <?= $badgeClass ?> text-xs"><?= $badgeLabel ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5 my-auto text-muted small">Không có nhiệm vụ nào gần đây.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row cuối: 4 Widget nhỏ -->
<div class="row g-4">
    <!-- Widget 1: Bản thảo gần đây -->
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-upload text-primary me-2"></i>Bản thảo mới nhận</h6>
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
                                <div class="border rounded p-1 text-center bg-light" style="height: 90px; overflow: hidden; position: relative;">
                                    <img src="<?= $subImg ?>" alt="Submission" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='<?= BASE_PATH ?>/assets/images/default_cover.jpg'">
                                    <span class="badge <?= $subStatusBadge ?> position-absolute bottom-0 start-0 m-1 text-xs" style="opacity: 0.9;"><?= $subStatusText ?></span>
                                </div>
                                <div class="text-truncate small fw-bold mt-1 text-slate-800" style="font-size: 11px;">Ch.<?= htmlspecialchars($sub['chapter_number']) ?> - P.<?= htmlspecialchars($sub['page_number']) ?></div>
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
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-calendar text-warning me-2"></i>Lịch biểu tháng</h6>
            </div>
            <div class="card-body py-2">
                <?php
                    $year = date('Y');
                    $month = date('m');
                    $monthName = date('m / Y');
                    $firstDay = mktime(0, 0, 0, $month, 1, $year);
                    $dayOfWeek = date('w', $firstDay);
                    if ($dayOfWeek == 0) $dayOfWeek = 7;
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
                            for ($i = 1; $i < $dayOfWeek; $i++) {
                                echo '<td></td>';
                            }
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                if (($day + $dayOfWeek - 2) % 7 == 0 && $day != 1) {
                                    echo '</tr><tr>';
                                }
                                $class = ($day == $today) ? 'class="active-day"' : '';
                                echo "<td {$class}>{$day}</td>";
                            }
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
            </div>
        </div>
    </div>

    <!-- Widget 3: Thù lao & Hiệu suất Trợ lý -->
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-coins text-success me-2"></i>Thù lao & Trợ lý</h6>
            </div>
            <div class="card-body py-2">
                <!-- Cost tracker -->
                <div class="p-2 bg-light rounded mb-3 text-center">
                    <span class="text-xs text-muted d-block">Chi phí khoán tháng này</span>
                    <strong class="h6 fw-bold text-primary mb-0"><?= number_format($remunerationCost) ?> đ</strong>
                </div>
                <!-- Leaderboard list -->
                <div class="small fw-bold text-slate-700 mb-2" style="font-size: 11px;">Trợ lý hoàn thành xuất sắc:</div>
                <div class="list-group list-group-flush">
                    <?php if (!empty($assistantsPerf)): ?>
                        <?php foreach ($assistantsPerf as $p): ?>
                            <div class="list-group-item px-0 py-1 bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <span class="text-xs" style="font-size: 11px;"><?= htmlspecialchars($p['assistant_name']) ?></span>
                                <span class="badge bg-success-soft text-success text-xs"><?= $p['completed_tasks'] ?> trang</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted small py-2">Chưa ghi nhận trang vẽ hoàn thành.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Widget 4: Timeline Hoạt động gần đây -->
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-history text-info me-2"></i>Nhật ký hoạt động</h6>
            </div>
            <div class="card-body py-2" style="max-height: 200px; overflow-y: auto;">
                <?php if (!empty($recentActivitiesData)): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($recentActivitiesData as $act): ?>
                            <?php 
                                $warnClass = 'info';
                                if (strpos(strtolower($act['message']), 'cảnh báo') !== false || strpos(strtolower($act['message']), 'nguy cơ') !== false) {
                                    $warnClass = 'danger';
                                } elseif (strpos(strtolower($act['message']), 'giao việc') !== false) {
                                    $warnClass = 'warning';
                                }
                            ?>
                            <div class="timeline-item <?= $warnClass ?>">
                                <div class="small fw-bold text-slate-800" style="font-size: 11px;"><?= htmlspecialchars($act['message']) ?></div>
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
