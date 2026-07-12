<?php
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện bảng điều khiển của Biên tập viên (dashboard.php)
 * Vai trò: Editor (Biên tập viên)
 * Chức năng: Thống kê số bản thảo đang chờ duyệt, các đánh giá gần đây, hiển thị chi tiết danh sách chờ và lịch sử đánh giá.
 * 
 * @var int $pendingSubmissions Số lượng bản thảo đang chờ xét duyệt
 * @var int $recentReviews Số lượng bài đánh giá được thực hiện gần đây
 * @var array $pendingList Danh sách các bản thảo đang chờ duyệt
 * @var array $recentReviewList Danh sách các đánh giá đã thực hiện gần đây
 * @var int $reviewedSubmissions Số lượng bản thảo đang trong quá trình đánh giá
 * @var int $approvedSubmissions Số lượng bản thảo đã phê duyệt
 * @var int $rejectedSubmissions Số lượng bản thảo đã bị từ chối
 */
$pageTitle = 'Góc Biên tập (Editor)';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<?php require_once __DIR__ . '/../layouts/welcome_banner.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Kiểm duyệt Bản thảo</h2>
        <p class="text-muted text-xs mb-0">Theo dõi, phản hồi và duyệt các chương truyện được nộp từ tác giả.</p>
    </div>
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
    <!-- Cột 1: Thống kê tổng số Submissions chờ review -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=review&action=index&status=pending" class="stat-card-link">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Chờ review</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($pendingSubmissions) ? $pendingSubmissions : 0 ?></div>
                        </div>
                        <div class="stat-icon warning" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-inbox"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Cột 2: Thống kê số Reviews đã thực hiện gần đây -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=review&action=index&status=reviewed" class="stat-card-link">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Đã Đánh Giá</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($recentReviews) ? $recentReviews : 0 ?></div>
                        </div>
                        <div class="stat-icon primary" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-eye"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Cột 3: Approved -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=index&status=approved" class="stat-card-link">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Phê Duyệt</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($approvedSubmissions) ? $approvedSubmissions : 0 ?></div>
                        </div>
                        <div class="stat-icon success" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Cột 4: Rejected -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= BASE_PATH ?>/index.php?controller=submission&action=index&status=rejected" class="stat-card-link">
            <div class="card stat-card danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-white text-opacity-75 text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Từ Chối</div>
                            <div class="h2 mb-0 fw-bold text-white"><?= isset($rejectedSubmissions) ? $rejectedSubmissions : 0 ?></div>
                        </div>
                        <div class="stat-icon danger" style="background: rgba(255,255,255,0.15); color: #ffffff;"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row mb-4">
    <!-- Cột 1: Cảnh báo Deadline chương truyện gần nhất -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Cảnh báo Deadline Chương truyện gần nhất</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($upcomingChapters)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tác phẩm</th>
                                    <th>Chương truyện</th>
                                    <th>Tác giả</th>
                                    <th>Hạn nộp bản thảo</th>
                                    <th class="text-end pe-4">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingChapters as $chap): ?>
                                    <?php 
                                        $dueDate = strtotime($chap['due_date']);
                                        $isOverdue = $dueDate < time();
                                        $timeDiff = abs($dueDate - time());
                                        $daysLeft = ceil($timeDiff / (60 * 60 * 24));
                                        
                                        $badgeClass = 'bg-light text-dark border';
                                        $labelText = $daysLeft . ' ngày nữa';
                                        if ($isOverdue) {
                                            $badgeClass = 'bg-danger text-white';
                                            $labelText = 'Trễ ' . $daysLeft . ' ngày';
                                        } elseif ($daysLeft <= 3) {
                                            $badgeClass = 'bg-warning text-dark';
                                            $labelText = 'Còn ' . $daysLeft . ' ngày';
                                        }
                                    ?>
                                    <tr>
                                        <td class="ps-4 font-semibold text-dark"><?= htmlspecialchars($chap['series_title']) ?></td>
                                        <td>
                                            <strong>Ch.<?= htmlspecialchars($chap['chapter_number']) ?></strong>
                                            <span class="text-muted ms-1"><?= htmlspecialchars($chap['chapter_title'] ?? 'Không tên') ?></span>
                                        </td>
                                        <td><span class="text-muted"><?= htmlspecialchars($chap['mangaka_name']) ?></span></td>
                                        <td><?= date('d/m/Y', $dueDate) ?></td>
                                        <td class="text-end pe-4">
                                            <span class="badge <?= $badgeClass ?> px-2 py-1"><?= $labelText ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 px-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background-color: #d1e7dd; color: #0f5132;">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tất cả đều đúng hạn!</h6>
                        <p class="text-muted small mb-0">Không có chương truyện nào sắp đến hạn cần xử lý gấp.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cột 2: Biểu đồ tròn Tỷ lệ Duyệt bài -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-chart-pie text-primary me-2"></i>Thống kê Tỷ lệ Duyệt bài</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                <div style="position: relative; height: 180px; width: 100%;">
                    <canvas id="chartEditorSubmissions" 
                            data-pending="<?= $pendingSubmissions ?>"
                            data-reviewed="<?= $reviewedSubmissions ?>"
                            data-approved="<?= $approvedSubmissions ?>"
                            data-rejected="<?= $rejectedSubmissions ?>"
                            style="max-height: 180px;"></canvas>
                </div>
                <div class="mt-3 text-center text-xs text-muted w-100 px-3">
                    <div class="row g-2">
                        <div class="col-6 text-start"><i class="fas fa-circle me-1" style="color: #ffc107;"></i> Chờ review (<?= $pendingSubmissions ?>)</div>
                        <div class="col-6 text-start"><i class="fas fa-circle me-1" style="color: #0ea5e9;"></i> Đang đánh giá (<?= $reviewedSubmissions ?>)</div>
                        <div class="col-6 text-start"><i class="fas fa-circle me-1" style="color: #198754;"></i> Phê duyệt (<?= $approvedSubmissions ?>)</div>
                        <div class="col-6 text-start"><i class="fas fa-circle me-1" style="color: #dc3545;"></i> Từ chối (<?= $rejectedSubmissions ?>)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Bảng danh sách các bản thảo đang chờ phê duyệt -->
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Danh sách Submissions chờ review</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($pendingList)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Người gửi</th>
                                    <th>Mục tiêu</th>
                                    <th>Ngày nộp</th>
                                    <th class="text-end pe-4">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingList as $sub): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars((string)($sub['sender_name'] ?? 'Không rõ')) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars((string)($sub['series_title'] ?? '')) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($sub['task_id'] !== null): ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 mb-1">Task</span>
                                                <div class="text-xs"><?= htmlspecialchars((string)($sub['task_title'] ?? '')) ?></div>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 mb-1">Chapter</span>
                                                <div class="text-xs">Ch.<?= htmlspecialchars((string)($sub['chapter_number'] ?? '')) ?> - <?= htmlspecialchars((string)($sub['chapter_title'] ?? '')) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($sub['submitted_at'])) ?></small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= BASE_PATH ?>/index.php?controller=review&action=create&submission_id=<?= $sub['submission_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 px-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle mb-3" style="width: 56px; height: 56px;">
                            <i class="fas fa-clipboard-check fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Không có bản thảo chờ review</h6>
                        <p class="text-muted small mb-0">Tất cả các chương truyện của dự án bạn phụ trách đều đã được đánh giá.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row" id="recent-reviews-section">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-history text-primary me-2"></i>Danh sách Reviews gần đây</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentReviewList)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Bản thảo ID</th>
                                    <th>Đánh giá</th>
                                    <th>Điểm</th>
                                    <th>Ngày</th>
                                    <th class="text-end pe-4">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentReviewList as $rev): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">#<?= $rev['submission_id'] ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars((string)($rev['comments'] ?? '')) ?>">
                                                <?= htmlspecialchars((string)($rev['comments'] ?? '')) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($rev['rating']): ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i><?= $rev['rating'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= BASE_PATH ?>/index.php?controller=review&action=show&id=<?= $rev['review_id'] ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 px-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle mb-3" style="width: 56px; height: 56px;">
                            <i class="fas fa-history fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Chưa có đánh giá nào</h6>
                        <p class="text-muted small mb-0">Bạn chưa thực hiện nhận xét hoặc đánh giá nào gần đây.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('chartEditorSubmissions');
        if (canvas) {
            const pending = parseInt(canvas.getAttribute('data-pending') || '0');
            const reviewed = parseInt(canvas.getAttribute('data-reviewed') || '0');
            const approved = parseInt(canvas.getAttribute('data-approved') || '0');
            const rejected = parseInt(canvas.getAttribute('data-rejected') || '0');
            
            const total = pending + reviewed + approved + rejected;
            const dataValues = total === 0 ? [1] : [pending, reviewed, approved, rejected];
            const bgColors = total === 0 ? ['#e2e8f0'] : ['#ffc107', '#0ea5e9', '#198754', '#dc3545'];
            const labels = total === 0 ? ['Chưa có dữ liệu'] : ['Chờ review', 'Đang đánh giá', 'Phê duyệt', 'Từ chối'];
            
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: bgColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
