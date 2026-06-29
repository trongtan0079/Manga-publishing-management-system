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

            <hr class="my-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu thay đổi</button>
                <a href="<?= BASE_PATH ?>/index.php?controller=user&action=show&id=<?= $user['user_id'] ?>" class="btn btn-light border">Xem chi tiết</a>
                <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="btn btn-light border">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
