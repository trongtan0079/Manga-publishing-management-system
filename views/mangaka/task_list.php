<?php 
/**
 * View: Giao diện quản lý danh sách công việc đã giao cho trợ lý (task_list.php)
 * Vai trò: Mangaka (Họa sĩ chính)
 * Chức năng: Hiển thị danh sách tất cả các công việc (Task) do Mangaka giao cho các Trợ lý.
 * 
 * @var array $tasks Danh sách các công việc đã giao
 */
$pageTitle = 'Danh sách Công việc đã giao';
$current_page = 'tasks';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-dark fw-bold">Danh sách Công việc đã giao</h2>
        <p class="text-muted text-xs mb-0">Theo dõi tiến độ, thời hạn và trạng thái xử lý trang vẽ của các trợ lý.</p>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=mangaka" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom border-light">
        <h5 class="card-title mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Danh sách Công việc</h5>
        
        <!-- Bộ lọc trạng thái nhanh -->
        <div class="d-flex align-items-center gap-2">
            <?php
            $currentStatus = $_GET['status'] ?? '';
            ?>
            <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index" class="btn btn-xs <?= $currentStatus === '' ? 'btn-primary' : 'btn-outline-secondary' ?>" style="font-size: 11px;">Tất cả</a>
            <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index&status=pending" class="btn btn-xs <?= $currentStatus === 'pending' ? 'btn-primary' : 'btn-outline-secondary' ?>" style="font-size: 11px;">Chờ làm</a>
            <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index&status=in_progress" class="btn btn-xs <?= $currentStatus === 'in_progress' ? 'btn-primary' : 'btn-outline-secondary' ?>" style="font-size: 11px;">Đang làm</a>
            <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index&status=submitted" class="btn btn-xs <?= $currentStatus === 'submitted' ? 'btn-primary' : 'btn-outline-secondary' ?>" style="font-size: 11px;">Đã nộp</a>
            <a href="<?= BASE_PATH ?>/index.php?controller=task&action=index&status=completed" class="btn btn-xs <?= $currentStatus === 'completed' ? 'btn-primary' : 'btn-outline-secondary' ?>" style="font-size: 11px;">Hoàn thành</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Ngữ cảnh truyện</th>
                        <th>Nhiệm vụ phân công</th>
                        <th>Người thực hiện</th>
                        <th>Độ ưu tiên</th>
                        <th>Hạn chót</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tasks)): ?>
                        <?php foreach ($tasks as $task): 
                            $isOverdue = false;
                            if ($task['status'] !== 'completed' && !empty($task['due_date'])) {
                                if (strtotime($task['due_date']) < time()) {
                                    $isOverdue = true;
                                }
                            }
                            $dueTime = !empty($task['due_date']) ? date('d/m/Y H:i', strtotime($task['due_date'])) : 'Không có';
                            $dueClass = $isOverdue ? 'text-danger fw-bold' : '';
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $task['page_id'] ?>&highlight_region=<?= $task['page_region_id'] ?>" class="text-decoration-none text-dark hover-primary-text" title="Xem chi tiết phân trang & phân vùng">
                                        <strong><?= htmlspecialchars($task['series_title']) ?></strong><br>
                                        <small class="text-muted">Chương <?= htmlspecialchars($task['chapter_number']) ?> - Trang <?= htmlspecialchars($task['page_number']) ?></small>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $typeLabel = 'Khác';
                                    $typeBadge = 'bg-secondary';
                                    switch ($task['task_type'] ?? 'other') {
                                        case 'background': $typeLabel = 'Vẽ nền'; $typeBadge = 'bg-dark'; break;
                                        case 'inking': $typeLabel = 'Đi nét'; $typeBadge = 'bg-secondary'; break;
                                        case 'coloring': $typeLabel = 'Lên màu'; $typeBadge = 'bg-success'; break;
                                        case 'effects': $typeLabel = 'Hiệu ứng'; $typeBadge = 'bg-info text-dark'; break;
                                        case 'other': $typeLabel = 'Khác'; $typeBadge = 'bg-secondary'; break;
                                    }
                                    ?>
                                    <span class="badge <?= $typeBadge ?> mb-1"><?= $typeLabel ?></span><br>
                                    <strong><?= htmlspecialchars($task['title']) ?></strong>
                                </td>
                                <td>
                                    <span class="fw-medium text-slate-700">
                                        <i class="fas fa-user-circle me-1 text-muted"></i><?= htmlspecialchars($task['assistant_name'] ?? 'Chưa có trợ lý') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $pColor = 'secondary';
                                    $pLabel = 'Thường';
                                    if ($task['priority'] === 'high') {
                                        $pColor = 'danger';
                                        $pLabel = 'Cao';
                                    } elseif ($task['priority'] === 'medium') {
                                        $pColor = 'warning';
                                        $pLabel = 'Trung bình';
                                    } else {
                                        $pColor = 'info';
                                        $pLabel = 'Thấp';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $pColor ?>-soft text-<?= $pColor ?> border-0 fw-bold"><?= $pLabel ?></span>
                                </td>
                                <td class="<?= $dueClass ?>"><?= $dueTime ?></td>
                                <td>
                                    <?php
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
                                        case 'completed':
                                            $statusBadge = '<span class="badge bg-success">Hoàn thành</span>';
                                            break;
                                    }
                                    if ($isOverdue) {
                                        $statusBadge = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Trễ hạn</span>';
                                    }
                                    echo $statusBadge;
                                    ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= $task['page_id'] ?>&highlight_region=<?= $task['page_region_id'] ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết trang">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_PATH ?>/index.php?controller=task&action=edit&id=<?= $task['task_id'] ?>" class="btn btn-sm btn-outline-warning" title="Sửa công việc">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= BASE_PATH ?>/index.php?controller=task&action=delete&id=<?= $task['task_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa công việc">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-tasks fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Không tìm thấy công việc nào phù hợp.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.hover-primary-text {
    transition: color 0.15s ease-in-out;
}
.hover-primary-text:hover, .hover-primary-text:hover small, .hover-primary-text:hover strong {
    color: var(--primary, #6366f1) !important;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
