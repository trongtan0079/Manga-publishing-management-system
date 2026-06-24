<?php 
require_once __DIR__ . '/../../core/Auth.php';
$role = $_SESSION['role_name'];
$pageTitle = 'Chi tiết Xếp hạng';
$current_page = 'rankings';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Chi tiết Xếp hạng</h2>
        <p class="text-muted text-xs mb-0">Xem thông tin chi tiết về điểm số và thứ hạng của bộ truyện.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=index" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>Thông tin Xếp hạng</h5>
            </div>
            <div class="card-body p-4">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th class="w-25 bg-light">ID Đánh giá</th>
                            <td><?= htmlspecialchars($ranking['ranking_id']) ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Bộ truyện (Series)</th>
                            <td>
                                <strong><?= htmlspecialchars($series['title']) ?></strong> 
                                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= $series['series_id'] ?>" class="btn btn-sm btn-link text-decoration-none p-0 ms-2">(Xem chi tiết truyện)</a>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Kỳ Đánh giá</th>
                            <td>
                                <span class="badge bg-secondary fs-6"><?= htmlspecialchars(date('d/m/Y', strtotime($ranking['period_start_date']))) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Thứ hạng (Rank)</th>
                            <td>
                                <span class="fs-4 fw-bold text-primary">#<?= htmlspecialchars($ranking['rank_position']) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Điểm số (Score)</th>
                            <td>
                                <span class="fs-5 text-success fw-bold"><?= htmlspecialchars($ranking['score']) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Người Đánh giá</th>
                            <td>ID: <?= htmlspecialchars($ranking['board_member_id']) ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày tạo</th>
                            <td><?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($ranking['created_at']))) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php if ($role === 'board'): ?>
            <div class="card-footer bg-white border-top text-end py-3">
                <a href="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=edit&id=<?= $ranking['ranking_id'] ?>" class="btn btn-warning text-white"><i class="fas fa-edit me-1"></i>Sửa Đánh giá</a>
                <form action="<?= BASE_PATH ?>/index.php?controller=seriesRanking&action=delete&id=<?= $ranking['ranking_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Xóa</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
