<?php 
/**
 * @var array $user 
 */
$pageTitle = 'Chi tiết người dùng';
$current_page = 'users';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Chi tiết: <?= htmlspecialchars($user['username']) ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=admin" class="text-decoration-none">Bảng điều khiển</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="text-decoration-none">Người dùng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=edit&id=<?= $user['user_id'] ?>" class="btn btn-warning btn-sm text-dark"><i class="fas fa-edit me-1"></i>Sửa</a>
        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-id-card text-info me-2"></i>Thông tin người dùng</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        <tr>
                            <th class="ps-4 text-muted" style="width: 200px;">User ID</th>
                            <td class="fw-bold">#<?= htmlspecialchars($user['user_id']) ?></td>
                        </tr>
                        <tr>
                            <th class="ps-4 text-muted">Tài khoản</th>
                            <td class="fw-bold"><?= htmlspecialchars($user['username']) ?></td>
                        </tr>
                        <tr>
                            <th class="ps-4 text-muted">Họ và tên</th>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                        </tr>
                        <tr>
                            <th class="ps-4 text-muted">Email</th>
                            <td><a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($user['email']) ?></a></td>
                        </tr>
                        <tr>
                            <th class="ps-4 text-muted">Vai trò</th>
                            <td>
                                <?php
                                    $roleBadgeColors = [
                                        'admin' => 'bg-danger',
                                        'mangaka' => 'bg-primary',
                                        'assistant' => 'bg-info text-dark',
                                        'editor' => 'bg-warning text-dark',
                                        'board' => 'bg-success',
                                    ];
                                    $roleName = $user['role_name'] ?? 'N/A';
                                    $badgeColor = $roleBadgeColors[strtolower($roleName)] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgeColor ?>"><?= htmlspecialchars(ucfirst($roleName)) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-4 text-muted">Trạng thái</th>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php elseif ($user['status'] === 'inactive'): ?>
                                    <span class="badge bg-secondary">Tạm khóa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Cấm (Banned)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-4 text-muted">Ngày tạo</th>
                            <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th class="ps-4 text-muted">Cập nhật lần cuối</th>
                            <td><?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-4">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=6366f1&color=fff&size=96" alt="Avatar" class="rounded-circle mb-3 shadow-sm" width="96" height="96">
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['full_name']) ?></h5>
                <p class="text-muted mb-3">@<?= htmlspecialchars($user['username']) ?></p>
                <div class="d-grid gap-2">
                    <a href="<?= BASE_PATH ?>/index.php?controller=user&action=edit&id=<?= $user['user_id'] ?>" class="btn btn-warning btn-sm text-dark"><i class="fas fa-edit me-1"></i>Chỉnh sửa</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
