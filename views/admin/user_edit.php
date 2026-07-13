<?php 
/**
 * View: Giao diện chỉnh sửa tài khoản người dùng (user_edit.php)
 * Vai trò: Admin (Quản trị viên)
 * Chức năng: Cho phép quản trị viên chỉnh sửa thông tin cá nhân, cập nhật mật khẩu, vai trò và trạng thái hoạt động của một tài khoản.
 * 
 * @var array $user Thông tin tài khoản người dùng cần sửa đổi
 * @var array $roles Danh sách vai trò để chọn phân quyền
 */
$pageTitle = 'Chỉnh sửa người dùng';
$current_page = 'users';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Chỉnh sửa: <?= htmlspecialchars($user['username']) ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=admin" class="text-decoration-none">Bảng điều khiển</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="text-decoration-none">Người dùng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
            </ol>
        </nav>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white border-bottom pt-3 pb-3">
        <h6 class="m-0 fw-bold"><i class="fas fa-user-edit text-warning me-2"></i>Cập nhật thông tin</h6>
    </div>
    <div class="card-body p-4">
        <form action="<?= BASE_PATH ?>/index.php?controller=user&action=update&id=<?= $user['user_id'] ?>" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label fw-semibold">Tài khoản <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-semibold">Mật khẩu mới</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Để trống nếu không đổi mật khẩu">
                    <div class="form-text">Chỉ nhập nếu muốn thay đổi. Tối thiểu 6 ký tự.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role_id" class="form-label fw-semibold">Vai trò <span class="text-danger">*</span></label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['role_id'] ?>" <?= ($r['role_id'] == $user['role_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(ucfirst($r['role_name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" <?= ($user['status'] === 'active') ? 'selected' : '' ?>>Hoạt động (Active)</option>
                        <option value="inactive" <?= ($user['status'] === 'inactive') ? 'selected' : '' ?>>Tạm khóa (Inactive)</option>
                        <option value="banned" <?= ($user['status'] === 'banned') ? 'selected' : '' ?>>Cấm (Banned)</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3" id="head_board_container" style="display: none;">
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_head_board" name="is_head_board" value="1" <?= ($user['is_head_board'] ?? 0) == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold text-danger" for="is_head_board"><i class="fas fa-crown me-1"></i>Đặt làm Trưởng ban Hội đồng Biên tập (Head of Board)</label>
                    </div>
                    <div class="form-text text-muted">Lưu ý: Chỉ được chọn tối đa một Trưởng ban trong hệ thống. Việc đặt tài khoản này làm Trưởng ban sẽ tự động hạ chức Trưởng ban của các tài khoản khác.</div>
                </div>
            </div>

            <hr class="my-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu thay đổi</button>
                <a href="<?= BASE_PATH ?>/index.php?controller=user&action=show&id=<?= $user['user_id'] ?>" class="btn btn-light border">Xem chi tiết</a>
                <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="btn btn-light border">Hủy</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const headBoardContainer = document.getElementById('head_board_container');
    const isHeadBoardCheckbox = document.getElementById('is_head_board');
    
    function toggleHeadBoard() {
        if (!roleSelect.value) {
            headBoardContainer.style.display = 'none';
            return;
        }
        const selectedOptionText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
        if (selectedOptionText.includes('board') || selectedOptionText.includes('giám đốc') || selectedOptionText.includes('hội đồng')) {
            headBoardContainer.style.display = 'block';
        } else {
            headBoardContainer.style.display = 'none';
            isHeadBoardCheckbox.checked = false;
        }
    }
    
    roleSelect.addEventListener('change', toggleHeadBoard);
    toggleHeadBoard();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
