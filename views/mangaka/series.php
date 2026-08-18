<?php 
if (!defined('BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strpos($scriptName, '/views/');
    $projectUrl = ($pos !== false) ? substr($scriptName, 0, $pos) : '';
    header('Location: ' . $projectUrl . '/index.php');
    exit;
}
/**
 * View: Giao diện quản lý danh sách dự án truyện (series.php)
 * Vai trò: Mangaka (Họa sĩ chính), Editor, Board, Admin
 * Chức năng: Hiển thị toàn bộ danh sách các bộ truyện (Series) kèm tìm kiếm, lọc trạng thái và phân trang.
 * 
 * @var array $seriesList Danh sách các bộ truyện
 * @var int $totalSeries Tổng số bộ truyện
 * @var int $totalPages Tổng số trang
 * @var int $page Trang hiện tại
 * @var string $search Từ khóa tìm kiếm
 * @var string $status Trạng thái lọc
 */
$pageTitle = 'Dự án Truyện';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$search = $search ?? '';
$status = $status ?? '';
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$totalSeries = $totalSeries ?? count($seriesList ?? []);

$statusLabels = [
    'planning'  => 'Kế hoạch (Nháp / Chờ duyệt)',
    'ongoing'   => 'Đang phát hành',
    'completed' => 'Hoàn thành',
    'suspended' => 'Tạm ngưng',
    'canceled'  => 'Đã hủy / Từ chối'
];
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Tiêu đề trang và Nút thêm mới -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0 text-dark fw-bold d-inline-block align-middle">Dự án Truyện</h2>
        <?php if (!empty($status)): ?>
            <span class="badge bg-info text-dark ms-2 align-middle" style="font-size: 0.8rem; padding: 0.35em 0.65em;">
                Đang lọc: <?= htmlspecialchars($statusLabels[$status] ?? $status) ?> (<?= $totalSeries ?>)
            </span>
            <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-sm btn-outline-secondary ms-2 align-middle py-1 px-2" style="font-size: 0.72rem;">
                <i class="fas fa-times me-1"></i>Xóa lọc trạng thái
            </a>
        <?php endif; ?>
        <?php if (!empty($search)): ?>
            <span class="badge bg-light text-dark border ms-2 align-middle" style="font-size: 0.8rem; padding: 0.35em 0.65em;">
                Tìm kiếm: "<?= htmlspecialchars($search) ?>"
            </span>
            <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index<?= !empty($status) ? '&status=' . urlencode($status) : '' ?>" class="btn btn-sm btn-outline-secondary ms-2 align-middle py-1 px-2" style="font-size: 0.72rem;">
                <i class="fas fa-times me-1"></i>Hủy tìm kiếm
            </a>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=<?= htmlspecialchars($_SESSION['role_name'] ?? 'mangaka') ?>" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Quay lại Bảng điều khiển
        </a>
        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka'): ?>
        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tạo Truyện Mới</a>
        <?php endif; ?>
    </div>
</div>

<!-- Khối tìm kiếm và lọc danh sách -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white border-bottom pt-3 pb-3">
        <form action="<?= BASE_PATH ?>/index.php" method="GET" class="row g-3 align-items-center">
            <input type="hidden" name="controller" value="series">
            <input type="hidden" name="action" value="index">
            
            <div class="col-12 col-md-5 col-lg-6">
                <div class="position-relative">
                    <i class="fas fa-search text-muted position-absolute top-50 translate-middle-y start-0 ms-3" style="pointer-events: none;"></i>
                    <input type="text" class="form-control ps-5 bg-light-subtle" name="search" placeholder="Tìm kiếm theo Tên truyện hoặc Mô tả..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            
            <div class="col-12 col-md-4 col-lg-3">
                <select name="status" class="form-select bg-light-subtle">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="planning" <?= ($status === 'planning') ? 'selected' : '' ?>>Kế hoạch (Planning)</option>
                    <option value="ongoing" <?= ($status === 'ongoing') ? 'selected' : '' ?>>Đang phát hành (Ongoing)</option>
                    <option value="completed" <?= ($status === 'completed') ? 'selected' : '' ?>>Hoàn thành (Completed)</option>
                    <option value="suspended" <?= ($status === 'suspended') ? 'selected' : '' ?>>Tạm ngưng (Suspended)</option>
                    <option value="canceled" <?= ($status === 'canceled') ? 'selected' : '' ?>>Đã hủy / Từ chối (Canceled)</option>
                </select>
            </div>

            <div class="col-12 col-md-3 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 text-nowrap"><i class="fas fa-filter me-2"></i>Lọc</button>
                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=index" class="btn btn-secondary flex-grow-1 text-nowrap">Đặt lại</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 80px;">ID</th>
                        <th>Tên Truyện</th>
                        <th style="width: 200px;">Trạng thái</th>
                        <th style="width: 170px;">Ngày tạo</th>
                        <th class="text-end pe-4" style="width: 200px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($seriesList)): ?>
                        <?php foreach ($seriesList as $series): ?>
                            <tr class="clickable-row" data-href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= $series['series_id'] ?>">
                                <td class="ps-4"><?= htmlspecialchars($series['series_id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($series['cover_image'])): 
                                            $coverUrl = $series['cover_image'];
                                            $resolvedCover = (strpos($coverUrl, 'http') === 0) ? $coverUrl : BASE_PATH . '/' . ltrim($coverUrl, '/');
                                        ?>
                                            <img src="<?= htmlspecialchars($resolvedCover) ?>" alt="Cover" width="40" height="60" class="me-3 object-fit-cover rounded shadow-sm flex-shrink-0">
                                        <?php else: ?>
                                            <div class="me-3 rounded bg-light border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 60px; color: #94a3b8;">
                                                <i class="fas fa-book"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong class="d-block text-dark mb-1"><?= htmlspecialchars($series['title']) ?></strong>
                                            <?php if (!empty($series['mangaka_name']) && isset($_SESSION['role_name']) && $_SESSION['role_name'] !== 'mangaka'): ?>
                                                <small class="text-muted"><i class="fas fa-user-edit me-1"></i><?= htmlspecialchars($series['mangaka_name']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?= $this->getSeriesStatusBadge($series) ?>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($series['created_at']))) ?></td>
                                
                                <!-- Actions -->
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="<?= BASE_PATH ?>/index.php?controller=series&action=show&id=<?= $series['series_id'] ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka'): ?>
                                            <?php if (!$this->isSeriesLocked($series)): ?>
                                                <a href="<?= BASE_PATH ?>/index.php?controller=series&action=edit&id=<?= $series['series_id'] ?>" class="btn btn-sm btn-warning text-dark" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                            <?php endif; ?>
                                            <!-- Form Xóa (dùng POST để bảo mật) -->
                                            <?php if ($series['status'] === 'planning'): ?>
                                                <form action="<?= BASE_PATH ?>/index.php?controller=series&action=delete&id=<?= $series['series_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bộ truyện này không? Hành động này không thể hoàn tác.');">
                                                    <?= Csrf::field() ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa Truyện">
                                                        <i class="fas fa-trash-alt"></i> Xóa
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-0">
                                <div class="empty-state-wrapper text-center py-5">
                                    <div class="mb-3" style="color: #cbd5e1;"><i class="fas fa-folder-open fa-3x"></i></div>
                                    <?php if (!empty($search) || !empty($status)): ?>
                                        <h6 class="fw-bold text-slate-700">Không tìm thấy bộ truyện phù hợp</h6>
                                        <p class="text-muted small mb-0">Không có kết quả nào khớp với bộ lọc hoặc từ khóa tìm kiếm của bạn.</p>
                                    <?php else: ?>
                                        <h6 class="fw-bold text-slate-700">Chưa có dự án truyện nào</h6>
                                        <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'mangaka'): ?>
                                            <p class="text-muted small mb-0">Danh sách hiện đang trống. Nhấn <strong>"Tạo Truyện Mới"</strong> để bắt đầu!</p>
                                        <?php else: ?>
                                            <p class="text-muted small mb-0">Hiện tại chưa có bộ truyện nào trong danh sách.</p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Phân trang (Pagination) -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white border-top py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <!-- Nút trang trước -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=series&action=index&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Các số trang -->
                    <?php 
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    if ($startPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=series&action=index&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=series&action=index&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=series&action=index&page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>"><?= $totalPages ?></a>
                        </li>
                    <?php endif; ?>

                    <!-- Nút trang sau -->
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=series&action=index&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const clickableRows = document.querySelectorAll(".clickable-row");
    clickableRows.forEach(row => {
        row.addEventListener("click", function(e) {
            // Đảm bảo không kích hoạt chuyển trang nếu click trúng nút, dropdown hoặc link con
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('select') || e.target.closest('form') || e.target.closest('input')) {
                return;
            }
            const href = this.getAttribute("data-href");
            if (href) {
                window.location.href = href;
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
