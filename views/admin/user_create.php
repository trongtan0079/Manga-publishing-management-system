<?php 
/**
 * View: Giao diện tạo mới tài khoản người dùng (user_create.php)
 * Vai trò: Admin (Quản trị viên)
 * Chức năng: Cho phép quản trị viên nhập thông tin cơ bản và phân quyền vai trò để tạo tài khoản mới.
 * 
 * @var array $roles Danh sách các vai trò (roles) hiện có trong hệ thống để lựa chọn phân quyền
 */
$pageTitle = 'Thêm người dùng mới';
$current_page = 'users';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Thêm người dùng mới</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=admin" class="text-decoration-none">Bảng điều khiển</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="text-decoration-none">Người dùng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
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
        <h6 class="m-0 fw-bold"><i class="fas fa-user-plus text-primary me-2"></i>Thông tin người dùng</h6>
    </div>
    <div class="card-body p-4">
        <form action="<?= BASE_PATH ?>/index.php?controller=user&action=store" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label fw-semibold">Tài khoản <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên tài khoản" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nhập họ và tên đầy đủ" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Nhập địa chỉ email" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Để trống sẽ dùng mật khẩu mặc định">
                    <div class="form-text">Mật khẩu mặc định: <code>password123</code>. Tối thiểu 6 ký tự.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role_id" class="form-label fw-semibold">Vai trò <span class="text-danger">*</span></label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="" disabled selected>-- Chọn vai trò --</option>
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['role_id'] ?>"><?= htmlspecialchars(ucfirst($r['role_name'])) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" selected>Hoạt động (Active)</option>
                        <option value="inactive">Tạm khóa (Inactive)</option>
                        <option value="banned">Cấm (Banned)</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3" id="head_board_container" style="display: none;">
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_head_board" name="is_head_board" value="1">
                        <label class="form-check-label fw-semibold text-danger" for="is_head_board"><i class="fas fa-crown me-1"></i>Đặt làm Trưởng ban Hội đồng Biên tập (Head of Board)</label>
                    </div>
                    <div class="form-text text-muted">Lưu ý: Chỉ được chọn tối đa một Trưởng ban trong hệ thống. Việc đặt tài khoản này làm Trưởng ban sẽ tự động hạ chức Trưởng ban của các tài khoản khác.</div>
                </div>
            </div>

            <hr class="my-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Tạo người dùng</button>
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
