<?php 
/**
 * @var int $pendingSubmissions
 * @var int $recentReviews
 * @var array $pendingList
 * @var array $recentReviewList
 */
$pageTitle = 'Góc Biên tập (Editor)';
$current_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Kiểm duyệt Bản thảo</h2>
        <p class="text-muted text-xs mb-0">Theo dõi, phản hồi và duyệt các chương truyện được nộp từ tác giả.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Cột 1: Thống kê tổng số Submissions chờ review -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Chờ review</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= isset($pendingSubmissions) ? $pendingSubmissions : 0 ?></div>
                    </div>
                    <div class="stat-icon warning"><i class="fas fa-inbox"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột 2: Thống kê số Reviews đã thực hiện gần đây -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Đã Đánh Giá</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= isset($recentReviews) ? $recentReviews : 0 ?></div>
                    </div>
                    <div class="stat-icon primary"><i class="fas fa-eye"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột 3: Approved -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Phê Duyệt</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= isset($approvedSubmissions) ? $approvedSubmissions : 0 ?></div>
                    </div>
                    <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột 4: Rejected -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-muted text-uppercase mb-2">Từ Chối</div>
                        <div class="h3 mb-0 fw-bold text-dark"><?= isset($rejectedSubmissions) ? $rejectedSubmissions : 0 ?></div>
                    </div>
                    <div class="stat-icon danger"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Bảng danh sách các bản thảo đang chờ phê duyệt -->
    <div class="col-lg-12">
        <div class="card">
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
                    <div class="text-center py-5">
                        <p class="text-muted mb-0">Chưa có dữ liệu</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
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
                    <div class="text-center py-5">
                        <p class="text-muted mb-0">Chưa có dữ liệu</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <?php require_once __DIR__ . '/../shared/dashboard_notifications.php'; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
