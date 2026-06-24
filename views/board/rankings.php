<?php 
require_once __DIR__ . '/../../core/Auth.php';
$role = $_SESSION['role_name'];
$pageTitle = 'Quản lý Xếp hạng (Rankings)';
$current_page = 'rankings';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Xếp hạng Series</h2>
        <p class="text-muted text-xs mb-0">Danh sách xếp hạng các bộ truyện theo kỳ đánh giá.</p>
    </div>
    <?php if ($role === 'board'): ?>
    <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tạo Ranking Mới</a>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kỳ Đánh Giá</th>
                        <th>Hạng</th>
                        <th>Series</th>
                        <th>Mangaka</th>
                        <th>Điểm Số</th>
                        <th>Ngày Tạo</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rankings)): ?>
                        <?php foreach ($rankings as $ranking): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-secondary"><?= htmlspecialchars(date('m/Y', strtotime($ranking['period_start_date']))) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary fs-5">#<?= htmlspecialchars($ranking['rank_position']) ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($ranking['series_title']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($ranking['mangaka_name'] ?? '') ?></td>
                                <td>
                                    <span class="badge bg-success"><?= htmlspecialchars($ranking['score']) ?></span>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ranking['created_at']))) ?></td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=show&id=<?= $ranking['ranking_id'] ?>" class="btn btn-sm btn-info text-white" title="Chi tiết"><i class="fas fa-eye"></i></a>
                                        <?php if ($role === 'board'): ?>
                                        <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=edit&id=<?= $ranking['ranking_id'] ?>" class="btn btn-sm btn-warning text-white" title="Sửa"><i class="fas fa-edit"></i></a>
                                        <form action="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=delete&id=<?= $ranking['ranking_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fs-1 mb-3 opacity-25"></i>
                                <p class="mb-0">Chưa có dữ liệu xếp hạng nào.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
