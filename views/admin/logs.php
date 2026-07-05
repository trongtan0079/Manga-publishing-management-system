<?php
if (!defined('BASE_PATH')) {
    header('Location: /index.php');
    exit;
}
/**
 * View: Giao diện xem Nhật ký hoạt động hệ thống (logs.php)
 * Vai trò: Admin
 * Chức năng: Hiển thị danh sách lịch sử thao tác của các thành viên trong hệ thống (CRUD, login, backup...)
 * 
 * @var array $logs Danh sách nhật ký hoạt động
 * @var int $page Trang hiện tại
 * @var int $totalPages Tổng số trang
 * @var int $totalLogs Tổng số dòng logs
 */
$pageTitle = 'Nhật ký Hoạt động';
$current_page = 'logs';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Tiêu đề trang -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-dark fw-bold">Nhật ký Hoạt động Hệ thống</h2>
        <p class="text-muted text-xs mb-0">Theo dõi toàn bộ các tác vụ cấu hình, chỉnh sửa và đăng nhập trên hệ thống theo thời gian thực.</p>
    </div>
    <span class="badge bg-secondary-subtle text-secondary border px-3 py-2"><i class="fas fa-eye me-1"></i>Chỉ xem</span>
</div>

<!-- Thống kê Logs -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold"><i class="fas fa-history text-primary me-2"></i>Lịch sử hoạt động (Tổng số: <?= $totalLogs ?> bản ghi)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 80px;">Log ID</th>
                        <th style="width: 220px;">Người thực hiện</th>
                        <th style="width: 180px;">Hành động</th>
                        <th>Chi tiết tác vụ</th>
                        <th style="width: 140px;">Địa chỉ IP</th>
                        <th class="pe-4 text-end" style="width: 160px;">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#<?= htmlspecialchars($log['log_id']) ?></td>
                                <td>
                                    <?php if ($log['user_id']): ?>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($log['full_name']) ?></div>
                                        <div class="small text-muted">@<?= htmlspecialchars($log['username']) ?> (<?= ucfirst(htmlspecialchars($log['role_name'] ?? '')) ?>)</div>
                                    <?php else: ?>
                                        <span class="text-muted small">Hệ thống / Đã ẩn danh</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        // Tô màu cho các tác vụ nhạy cảm
                                        $actionName = $log['action'];
                                        $badgeClass = 'bg-secondary';
                                        if (strpos($actionName, 'Tạo') !== false) {
                                            $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                        } elseif (strpos($actionName, 'Xóa') !== false) {
                                            $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                        } elseif (strpos($actionName, 'Cập nhật') !== false || strpos($actionName, 'Khóa') !== false) {
                                            $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                        } elseif (strpos($actionName, 'Sao lưu') !== false) {
                                            $badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                                        }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-2 py-1" style="font-size: 0.75rem;"><?= htmlspecialchars($actionName) ?></span>
                                </td>
                                <td class="text-muted" style="max-width: 350px; font-size: 0.85rem; word-break: break-word;">
                                    <?= htmlspecialchars($log['details']) ?>
                                </td>
                                <td>
                                    <code class="text-xs text-secondary"><i class="fas fa-network-wired me-1"></i><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></code>
                                </td>
                                <td class="pe-4 text-end text-muted" style="font-size: 0.82rem;">
                                    <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <div class="mb-3"><i class="fas fa-history fa-3x text-light"></i></div>
                                <p class="mb-0">Hệ thống chưa ghi nhận bất kỳ nhật ký hoạt động nào.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Phân trang -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white border-top py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=logs&page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php 
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    if ($startPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . BASE_PATH . '/index.php?controller=dashboard&action=logs&page=1">1</a></li>';
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    for ($i = $startPage; $i <= $endPage; $i++): 
                    ?>
                        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=logs&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; 
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . BASE_PATH . '/index.php?controller=dashboard&action=logs&page=' . $totalPages . '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=logs&page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
