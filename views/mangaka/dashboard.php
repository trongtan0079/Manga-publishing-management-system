<?php 
/**
 * View: Giao diện bảng điều khiển dành cho Họa sĩ chính (dashboard.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * Chức năng: Thống kê số lượng tác phẩm, chương truyện, số trang vẽ đã tạo, tổng số công việc và bảng xếp hạng xếp hạng truyện mới nhất của họa sĩ.
 * 
 * @var int $totalSeries Tổng số bộ truyện họa sĩ sở hữu
 * @var int $totalChapters Tổng số chương truyện họa sĩ sở hữu
 * @var int $totalPages Tổng số trang vẽ
 * @var int $totalTasks Tổng số công việc đã giao cho trợ lý
 * @var array $latestRankings Danh sách xếp hạng mới nhất của các bộ truyện thuộc họa sĩ
 * @var \SeriesRanking $rankingModel Thực thể mô hình xếp hạng dùng để lấy thêm thông tin
 */
$pageTitle = 'Không gian sáng tác (Mangaka)';
$current_page = 'dashboard';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<?php require_once __DIR__ . '/../layouts/welcome_banner.php'; ?>

<?php 
// Kiểm tra xem tác giả này có bộ truyện nào bị xếp hạng thấp có nguy cơ bị hủy hay không
$hasWarningSeries = false;
$warningSeriesList = [];
if (!empty($latestRankings)) {
    foreach ($latestRankings as $r) {
        if ($r['rank_position'] >= 5 && $r['score'] < 50) {
            $hasWarningSeries = true;
            $warningSeriesList[] = $r['series_title'] ?? ('Series #' . $r['series_id']);
        }
    }
}
?>

<?php if ($hasWarningSeries): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; border-left: 5px solid #dc3545; background-color: #fdf2f2; border-top: 1px solid #fca5a5; border-right: 1px solid #fca5a5; border-bottom: 1px solid #fca5a5;">
        <i class="fas fa-exclamation-triangle me-3 text-danger fs-4"></i>
        <div>
            <strong class="text-danger d-block mb-1" style="font-size: 0.92rem;"><i class="fas fa-radiation"></i> Cảnh báo Hiệu Năng Tác Phẩm!</strong>
            <span class="text-muted" style="font-size: 0.82rem; font-weight: 500; line-height: 1.5; display: inline-block;">
                Tác phẩm của bạn: <strong class="text-dark"><?= implode(', ', array_map('htmlspecialchars', $warningSeriesList)) ?></strong> hiện đang có thứ hạng thấp (Hạng >= 5) và điểm số bình chọn dưới trung bình (Điểm < 50). Có nguy cơ cao bị Hội đồng Biên tập xem xét đình bản (Hủy dự án). Vui lòng liên hệ Editor phụ trách để xây dựng hồ sơ biện hộ hoặc cải thiện chất lượng kịch bản ở chương tiếp theo.
            </span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Không gian Sáng tác</h2>
        <p class="text-muted text-xs mb-0">Quản lý tác phẩm, nộp bản thảo và theo dõi phản hồi từ Biên tập viên.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-upload me-2"></i>Nộp Bản Thảo Mới</a>
</div>

<style>
    .stat-card-link {
        text-decoration: none !important;
        display: block;
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
    <!-- Series đang quản lý -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="stat-card-link">
            <div class="card stat-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Series đang quản lý</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($totalSeries) ? $totalSeries : 0 ?></div>
                        </div>
                        <div class="stat-icon info" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-book"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- Chapter đang thực hiện -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=chapter&action=index" class="stat-card-link">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng số Chapter</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($totalChapters) ? $totalChapters : 0 ?></div>
                        </div>
                        <div class="stat-icon primary" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-file-alt"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- Tổng bản thảo -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=index" class="stat-card-link">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Bản thảo</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($totalSubmissions) ? $totalSubmissions : 0 ?></div>
                        </div>
                        <div class="stat-icon success" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-file-upload"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- Duyệt bài Trợ lý -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=review&action=index" class="stat-card-link">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Duyệt bài Trợ lý</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($pendingReviews) ? $pendingReviews : 0 ?></div>
                        </div>
                        <div class="stat-icon warning" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- Tổng Pages -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=index" class="stat-card-link">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Pages</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($totalPages) ? $totalPages : 0 ?></div>
                        </div>
                        <div class="stat-icon primary" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-images"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- Tổng Tasks -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index" class="stat-card-link">
            <div class="card stat-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tổng Tasks</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($totalTasks) ? $totalTasks : 0 ?></div>
                        </div>
                        <div class="stat-icon info" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-tasks"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Hàng 1: Xếp hạng Manga -->
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
                                <th>Thứ hạng</th>
                                <th>Điểm số</th>
                                <th>Biến động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($latestRankings)): ?>
                                <?php foreach ($latestRankings as $ranking): ?>
                                    <?php 
                                        $prevRanking = $rankingModel->getPreviousRanking($ranking['series_id'], $ranking['period_start_date']);
                                        $trendIcon = '<span class="text-secondary fw-bold" style="font-size: 0.8rem;"><i class="fas fa-minus"></i> Mới</span>';
                                        
                                        if ($prevRanking) {
                                            if ($ranking['rank_position'] < $prevRanking['rank_position']) {
                                                $trendIcon = '<span class="text-success fw-bold" style="font-size: 0.8rem;"><i class="fas fa-arrow-up"></i> ▲ Tăng</span>';
                                            } elseif ($ranking['rank_position'] > $prevRanking['rank_position']) {
                                                $trendIcon = '<span class="text-danger fw-bold" style="font-size: 0.8rem;"><i class="fas fa-arrow-down"></i> ▼ Giảm</span>';
                                            } else {
                                                $trendIcon = '<span class="text-secondary fw-bold" style="font-size: 0.8rem;"><i class="fas fa-minus"></i> ▬</span>';
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td class="ps-4"><strong><?= htmlspecialchars($ranking['series_title']) ?></strong></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars(date('m/Y', strtotime($ranking['period_start_date']))) ?></span></td>
                                        <td><span class="fw-bold text-primary">#<?= htmlspecialchars($ranking['rank_position']) ?></span></td>
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

<!-- Hàng 2: Tiến độ Công việc Trợ lý -->
<div class="row mb-4" id="tasks-progress-section">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="m-0 fw-bold"><i class="fas fa-tasks text-primary me-2"></i>Tiến độ Công việc Trợ lý</h6>
                <div class="d-flex align-items-center gap-2">
                    <select id="series-task-filter" class="form-select form-select-sm border-light-subtle shadow-sm" style="width: auto; min-width: 140px; font-size: 0.8rem;" <?= empty($mySeriesList) ? 'disabled' : '' ?>>
                        <option value="all">Tất cả dự án</option>
                        <?php if (!empty($mySeriesList)): ?>
                            <?php foreach ($mySeriesList as $ser): ?>
                                <option value="<?= $ser['series_id'] ?>"><?= htmlspecialchars($ser['title']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span id="task-completed-badge" class="badge bg-primary-soft text-primary fw-bold" style="font-size: 0.75rem; padding: 6px 12px; border-radius: 20px; background: rgba(79, 70, 229, 0.08);">
                        Hoàn thành: 0/0
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                        <span class="text-xs fw-semibold text-muted" style="font-size: 0.8rem;">Hiệu suất hoàn thành</span>
                        <span id="task-completion-rate-label" class="text-xs fw-bold text-slate-700" style="font-size: 0.8rem;">0%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px; background-color: #f1f5f9;">
                        <div id="task-progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%; border-radius: 4px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- Tasks Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead>
                            <tr class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <th>Công việc</th>
                                <th>Trợ lý</th>
                                <th>Hạn chót</th>
                                <th class="text-end">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody id="task-table-body">
                            <?php if (!empty($allTasks)): ?>
                                <?php foreach ($allTasks as $task): 
                                    $statusBadge = '';
                                    switch ($task['status']) {
                                        case 'pending':
                                            $statusBadge = '<span class="badge bg-secondary">Chờ làm</span>';
                                            break;
                                        case 'in_progress':
                                            $statusBadge = '<span class="badge bg-primary">Đang làm</span>';
                                            break;
                                        case 'submitted':
                                            $statusBadge = '<span class="badge bg-warning text-dark">Đã nộp</span>';
                                            break;
                                        case 'rejected':
                                            $statusBadge = '<span class="badge bg-danger">Yêu cầu sửa</span>';
                                            break;
                                        case 'completed':
                                            $statusBadge = '<span class="badge bg-success">Hoàn thành</span>';
                                            break;
                                    }
                                    
                                    // Check if overdue
                                    $isOverdue = false;
                                    if ($task['status'] !== 'completed' && !empty($task['due_date'])) {
                                        if (strtotime($task['due_date']) < time()) {
                                            $isOverdue = true;
                                            $statusBadge = '<span class="badge bg-danger">Trễ hạn</span>';
                                        }
                                    }

                                    $dueTime = !empty($task['due_date']) ? date('d/m/Y', strtotime($task['due_date'])) : 'Không có';
                                    $dueClass = $isOverdue ? 'text-danger fw-bold' : '';
                                ?>
                                    <tr class="task-row" data-series-id="<?= $task['series_id'] ?>" data-status="<?= $task['status'] ?>">
                                        <td>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 140px;" title="<?= htmlspecialchars($task['title']) ?>">
                                                <?= htmlspecialchars($task['title']) ?>
                                            </div>
                                            <div class="text-xs text-muted" style="font-size: 0.72rem;">
                                                <?= htmlspecialchars($task['series_title']) ?> - P.<?= htmlspecialchars($task['page_number']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-slate-700"><?= htmlspecialchars($task['assistant_name'] ?? 'Chưa giao') ?></span>
                                        </td>
                                        <td class="<?= $dueClass ?>"><?= $dueTime ?></td>
                                        <td class="text-end"><?= $statusBadge ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!-- Hàng trống khi không có kết quả -->
                            <tr id="no-tasks-row" style="display: none;">
                                <td colspan="4" class="text-center py-4 text-muted">Chưa giao công việc nào cho trợ lý trong dự án này.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="m-0 fw-bold"><i class="fas fa-history text-primary me-2"></i>Hoạt động Gần đây</h6>
            </div>
            <div class="card-body py-4">
                <?php if (!empty($recentActivities)): ?>
                    <div class="timeline-container" style="position: relative; padding-left: 30px;">
                        <!-- Timeline vertical line -->
                        <div style="position: absolute; top: 0; bottom: 0; left: 15px; width: 2px; background-color: #f1f5f9; pointer-events: none;"></div>
                        
                        <?php foreach ($recentActivities as $activity): 
                            $icon = 'fa-info-circle text-primary';
                            $bgColor = 'rgba(14, 165, 233, 0.1)';
                            
                            switch ($activity['type']) {
                                case 'task_assigned':
                                    $icon = 'fa-tasks text-info';
                                    $bgColor = 'rgba(14, 165, 233, 0.1)';
                                    break;
                                case 'submission_submitted':
                                    $icon = 'fa-arrow-up text-primary';
                                    $bgColor = 'rgba(79, 70, 229, 0.1)';
                                    break;
                                case 'chapter_submitted':
                                    $icon = 'fa-file-upload text-indigo';
                                    $bgColor = 'rgba(99, 102, 241, 0.1)';
                                    break;
                                case 'review_created':
                                    $icon = 'fa-comment-alt text-purple';
                                    $bgColor = 'rgba(139, 92, 246, 0.1)';
                                    break;
                                case 'submission_approved':
                                    $icon = 'fa-check-circle text-success';
                                    $bgColor = 'rgba(16, 185, 129, 0.1)';
                                    break;
                                case 'submission_rejected':
                                    $icon = 'fa-times-circle text-danger';
                                    $bgColor = 'rgba(239, 68, 68, 0.1)';
                                    break;
                                case 'ranking_published':
                                    $icon = 'fa-trophy text-warning';
                                    $bgColor = 'rgba(245, 158, 11, 0.1)';
                                    break;
                            }
                        ?>
                            <div class="timeline-item mb-4 position-relative" style="min-height: 40px;">
                                <!-- Timeline icon -->
                                <div style="position: absolute; left: -30px; top: 4px; width: 32px; height: 32px; background: <?= $bgColor ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1;">
                                    <i class="fas <?= $icon ?> fs-6"></i>
                                </div>
                                <div class="ms-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($activity['message']) ?></span>
                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="far fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?></small>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #64748b;">
                                        <?php if ($activity['is_read']): ?>
                                            <span class="badge bg-light text-muted border">Đã đọc</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-primary border">Mới</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="text-muted mb-2"><i class="fas fa-inbox fs-1 opacity-25"></i></div>
                        <p class="text-muted mb-0">Chưa có hoạt động gần đây.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterSelect = document.getElementById("series-task-filter");
    if (!filterSelect) return;

    const taskRows = document.querySelectorAll(".task-row");
    const noTasksRow = document.getElementById("no-tasks-row");
    const completedBadge = document.getElementById("task-completed-badge");
    const completionRateLabel = document.getElementById("task-completion-rate-label");
    const progressBar = document.getElementById("task-progress-bar");

    function updateFilter() {
        const selectedValue = filterSelect.value;
        let total = 0;
        let completed = 0;
        let visibleCount = 0;

        taskRows.forEach(row => {
            const seriesId = row.getAttribute("data-series-id");
            const status = row.getAttribute("data-status");
            const matches = (selectedValue === "all" || seriesId === selectedValue);

            if (matches) {
                total++;
                if (status === "completed") {
                    completed++;
                }

                // Show only up to 5 matching rows
                if (visibleCount < 5) {
                    row.style.display = "table-row";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            } else {
                row.style.display = "none";
            }
        });

        // Update stats badges & progress bars
        completedBadge.textContent = "Hoàn thành: " + completed + "/" + total;
        const rate = total > 0 ? Math.round((completed / total) * 100) : 0;
        completionRateLabel.textContent = rate + "%";
        progressBar.style.width = rate + "%";
        progressBar.setAttribute("aria-valuenow", rate);

        // Show/hide no tasks alert row
        if (total === 0) {
            noTasksRow.style.display = "table-row";
        } else {
            noTasksRow.style.display = "none";
        }
    }

    filterSelect.addEventListener("change", updateFilter);
    
    // Initial run
    updateFilter();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
