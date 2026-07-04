<?php
if (!defined('BASE_PATH')) {
    header('Location: /index.php');
    exit;
}
$pageTitle = 'Bảng điều khiển Biên tập viên';
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
</style>

<!-- Banner Chào Mừng -->
<div class="welcome-banner p-4 mb-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="h3 fw-bold mb-2">Xin chào Biên tập viên, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Biên tập') ?>!</h1>
            <p class="text-slate-300 mb-0 opacity-80" style="font-size: 14px;">Giám sát chất lượng bản thảo, đưa ra phản hồi đánh giá và phát hành các chương truyện đúng lịch xuất bản.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=progress" class="btn btn-light btn-sm fw-bold px-3 py-2" style="border-radius: 8px;">
                <i class="fas fa-chart-line me-1"></i>Xem tiến độ Studio
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
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Chờ đánh giá</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $pendingSubmissions ?></h3>
                    <small class="text-warning text-xs">Bản thảo mới</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-warning" style="width: 48px; height: 48px;">
                    <i class="fas fa-inbox fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Đã Đánh Giá</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $recentReviews ?></h3>
                    <small class="text-muted text-xs">Tổng số review</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-primary" style="width: 48px; height: 48px;">
                    <i class="fas fa-eye fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Đã Phê Duyệt</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $approvedSubmissions ?></h3>
                    <small class="text-success text-xs">Approved</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-success" style="width: 48px; height: 48px;">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card-glow border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-bold text-muted text-uppercase d-block mb-1">Đã Từ Chối</span>
                    <h3 class="fw-bold mb-0 text-slate-800"><?= $rejectedSubmissions ?></h3>
                    <small class="text-danger text-xs">Rejected</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center soft-bg-danger" style="width: 48px; height: 48px;">
                    <i class="fas fa-times-circle fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Trái: Danh sách bản thảo chờ phê duyệt -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-clipboard-check text-primary me-2"></i>Danh sách bản thảo nộp đang chờ duyệt</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light text-muted" style="font-size: 11px;">
                                <th class="ps-3">Tác phẩm</th>
                                <th>Chương nộp</th>
                                <th>Tác giả nộp</th>
                                <th>Thời gian nộp</th>
                                <th class="pe-3 text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pendingList)): ?>
                                <?php foreach ($pendingList as $sub): ?>
                                    <?php 
                                        $cover = !empty($sub['cover_image']) ? BASE_PATH . '/' . ltrim($sub['cover_image'], '/') : BASE_PATH . '/assets/images/default_cover.jpg';
                                    ?>
                                    <tr>
                                        <td class="ps-3 d-flex align-items-center gap-2">
                                            <img src="<?= $cover ?>" alt="Cover" class="rounded shadow-sm" style="width: 32px; height: 44px; object-fit: cover;" onerror="this.src='<?= BASE_PATH ?>/assets/images/default_cover.jpg'">
                                            <div class="fw-bold text-slate-800" style="font-size: 13px;"><?= htmlspecialchars($sub['series_title']) ?></div>
                                        </td>
                                        <td>
                                            <span class="text-slate-800" style="font-size: 12px;">Chương <?= htmlspecialchars($sub['chapter_number']) ?></span>
                                        </td>
                                        <td>
                                            <span class="text-slate-800" style="font-size: 12px;"><?= htmlspecialchars($sub['author_name']) ?></span>
                                        </td>
                                        <td class="text-muted" style="font-size: 11px;">
                                            <?= date('H:i d/m/Y', strtotime($sub['submitted_at'])) ?>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <a href="<?= BASE_PATH ?>/index.php?controller=review&action=create&submission_id=<?= $sub['submission_id'] ?>" class="btn btn-outline-primary btn-xs fw-bold">
                                                <i class="fas fa-edit me-1"></i>Kiểm duyệt
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted small">Hiện tại không có bản thảo nào chờ kiểm duyệt.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Phải: Giám sát tải công việc Studio & Lịch sử lời bình đánh giá -->
    <div class="col-lg-4">
        <!-- Widget 1: Giám sát tải sáng tác -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-tachometer-alt text-danger me-2"></i>Giám sát tải làm việc Studio</h6>
            </div>
            <div class="card-body py-2">
                <div class="list-group list-group-flush">
                    <?php if (!empty($studioWorkload)): ?>
                        <?php foreach ($studioWorkload as $wl): ?>
                            <div class="list-group-item px-0 py-2 border-0 bg-transparent d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-slate-500" style="width: 32px; height: 32px;">
                                        <i class="fas fa-paint-brush"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-800" style="font-size: 12px;"><?= htmlspecialchars($wl['mangaka_name']) ?></div>
                                        <small class="text-muted" style="font-size: 10px;">Vai trò: Mangaka</small>
                                    </div>
                                </div>
                                <span class="badge <?= $wl['active_chapters'] > 1 ? 'bg-danger-soft text-danger' : 'bg-success-soft text-success' ?> text-xs"><?= $wl['active_chapters'] ?> chapter đang vẽ</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted small py-2">Không có dữ liệu studio.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Widget 2: Đánh giá vừa thực hiện -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="m-0 fw-bold text-slate-700"><i class="fas fa-history text-info me-2"></i>Lời bình kiểm duyệt gần đây</h6>
            </div>
            <div class="card-body py-2" style="max-height: 200px; overflow-y: auto;">
                <?php if (!empty($recentReviewList)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentReviewList as $rev): ?>
                            <div class="list-group-item px-0 py-2 border-light bg-transparent">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-success-soft text-success text-xs">Score: <?= $rev['score'] ?></span>
                                    <span class="text-muted" style="font-size: 10px;"><?= date('d/m/Y', strtotime($rev['reviewed_at'])) ?></span>
                                </div>
                                <div class="small text-slate-800 text-truncate" style="font-size: 11px;"><?= htmlspecialchars($rev['comments']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted small py-4">Chưa có đánh giá nào được gửi đi.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
