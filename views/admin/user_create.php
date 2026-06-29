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

            <hr class="my-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Tạo người dùng</button>
                <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="btn btn-light border">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
