<?php 
/**
 * Quản lý Vai trò (Chỉ đọc) - Hiển thị thông tin và số lượng người dùng của từng vai trò trong hệ thống.
 * @var array $rolesWithCount Danh sách các vai trò kèm theo số lượng thành viên tương ứng
 */
$pageTitle = 'Quản lý Vai trò';
$current_page = 'roles';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$roleDescriptions = [
    'admin'     => 'Quản trị viên hệ thống, quản lý toàn bộ người dùng và cấu hình.',
    'mangaka'   => 'Tác giả manga, tạo và quản lý dự án truyện, chương truyện.',
    'assistant' => 'Trợ lý tác giả, thực hiện các công việc (task) được giao.',
    'editor'    => 'Biên tập viên, phê duyệt hoặc từ chối bản thảo chương truyện.',
    'board'     => 'Hội đồng đánh giá, xếp hạng và chấm điểm các tác phẩm manga.',
];

$roleIcons = [
    'admin'     => 'fa-user-shield text-danger',
    'mangaka'   => 'fa-paint-brush text-primary',
    'assistant' => 'fa-hands-helping text-info',
    'editor'    => 'fa-pen-fancy text-warning',
    'board'     => 'fa-gavel text-success',
];

$roleBadges = [
    'admin'     => 'bg-danger',
    'mangaka'   => 'bg-primary',
    'assistant' => 'bg-info text-dark',
    'editor'    => 'bg-warning text-dark',
    'board'     => 'bg-success',
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Quản lý Vai trò</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=admin" class="text-decoration-none">Bảng điều khiển</a></li>
                <li class="breadcrumb-item active" aria-current="page">Vai trò</li>
            </ol>
        </nav>
    </div>
    <span class="badge bg-secondary-subtle text-secondary border px-3 py-2"><i class="fas fa-lock me-1"></i>Chỉ xem</span>
</div>

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white border-bottom pt-3 pb-3">
        <h6 class="m-0 fw-bold"><i class="fas fa-user-tag text-primary me-2"></i>Danh sách vai trò trong hệ thống</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 60px;">ID</th>
                        <th>Vai trò</th>
                        <th>Mô tả</th>
                        <th class="text-center" style="width: 120px;">Số User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rolesWithCount)): ?>
                        <?php foreach ($rolesWithCount as $r): ?>
                            <?php $roleName = strtolower($r['role_name']); ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#<?= htmlspecialchars($r['role_id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(99,102,241,0.08);">
                                            <i class="fas <?= $roleIcons[$roleName] ?? 'fa-user text-secondary' ?>"></i>
                                        </div>
                                        <span class="badge <?= $roleBadges[$roleName] ?? 'bg-secondary' ?> px-3 py-2"><?= htmlspecialchars(ucfirst($r['role_name'])) ?></span>
                                    </div>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($roleDescriptions[$roleName] ?? ($r['description'] ?? 'Không có mô tả')) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-3 py-2" style="font-size: 0.9rem;"><?= (int)$r['user_count'] ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="fas fa-user-tag fa-3x text-light mb-3 d-block"></i>
                                <p class="mb-0">Chưa có vai trò nào trong hệ thống.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4">
    <?php if (!empty($rolesWithCount)): ?>
        <?php foreach ($rolesWithCount as $r): ?>
            <?php $roleName = strtolower($r['role_name']); ?>
            <div class="col-xl col-md-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: rgba(99,102,241,0.08);">
                            <i class="fas <?= $roleIcons[$roleName] ?? 'fa-user text-secondary' ?> fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-1"><?= (int)$r['user_count'] ?></h5>
                        <p class="text-muted mb-0 text-xs text-uppercase fw-bold"><?= htmlspecialchars(ucfirst($r['role_name'])) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
