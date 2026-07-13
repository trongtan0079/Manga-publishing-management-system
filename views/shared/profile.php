<?php

/**
 * Hồ sơ cá nhân - Dùng chung cho tất cả vai trò
 * @var array $user Thông tin user hiện tại (kèm role_name)
 */
$current_page = 'profile';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$roleBadges = [
    'admin'     => 'bg-danger',
    'mangaka'   => 'bg-primary',
    'assistant' => 'bg-info text-dark',
    'editor'    => 'bg-warning text-dark',
    'board'     => 'bg-success',
];
$roleLabels = [
    'admin'     => 'Quản trị viên',
    'mangaka'   => 'Tác giả Manga',
    'assistant' => 'Trợ lý tác giả',
    'editor'    => 'Biên tập viên',
    'board'     => 'Hội đồng biên tập',
];
$roleName = strtolower($user['role_name'] ?? '');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1">Hồ sơ cá nhân</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=<?= htmlspecialchars($roleName) ?>" class="text-decoration-none">Bảng điều khiển</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hồ sơ cá nhân</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Hiển thị thông báo -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row g-4">
    <!-- Cột trái: Thẻ thông tin tổng quan -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 text-center">
            <div class="card-body py-5">
                <!-- Avatar -->
                <div class="position-relative d-inline-block mx-auto mb-3" style="width: 100px; height: 100px;">
                    <img src="<?= getUserAvatarUrl($user['user_id'], $user['full_name']) ?>"
                        alt="Avatar" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover;" id="profileAvatarPreview">
                    <label for="avatar_file" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                        style="width: 30px; height: 30px; border: 2px solid #ffffff; cursor: pointer; transition: all 0.2s;" 
                        title="Đổi ảnh đại diện"
                        onmouseover="this.style.transform='scale(1.15)'; this.style.backgroundColor='#4f46e5';"
                        onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='#6366f1';">
                        <i class="fas fa-pencil-alt" style="font-size: 0.75rem;"></i>
                    </label>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['full_name']) ?></h5>
                <p class="text-muted mb-2" style="font-size: 0.9rem;">@<?= htmlspecialchars($user['username']) ?></p>
                <span class="badge <?= $roleBadges[$roleName] ?? 'bg-secondary' ?> px-3 py-2"><?= htmlspecialchars($roleLabels[$roleName] ?? ucfirst($roleName)) ?></span>

                <hr class="my-4">

                <!-- Thông tin nhanh -->
                <div class="text-start px-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; background: rgba(99,102,241,0.1); flex-shrink: 0;">
                            <i class="fas fa-envelope" style="color: #6366f1; font-size: 0.85rem;"></i>
                        </div>
                        <div style="min-width: 0;">
                            <div class="text-muted" style="font-size: 0.75rem;">Email</div>
                            <div class="fw-semibold text-truncate" style="font-size: 0.9rem;"><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; background: rgba(25,135,84,0.1); flex-shrink: 0;">
                            <i class="fas fa-shield-alt" style="color: #198754; font-size: 0.85rem;"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size: 0.75rem;">Trạng thái</div>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php elseif ($user['status'] === 'inactive'): ?>
                                <span class="badge bg-secondary">Tạm khóa</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Bị cấm</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; background: rgba(13,110,253,0.1); flex-shrink: 0;">
                            <i class="fas fa-calendar-alt" style="color: #0d6efd; font-size: 0.85rem;"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size: 0.75rem;">Ngày tham gia</div>
                            <div class="fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars(date('d/m/Y', strtotime($user['created_at']))) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Form chỉnh sửa -->
    <div class="col-lg-8">
        <!-- Card 1: Thông tin cơ bản -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-user-edit text-primary me-2"></i>Chỉnh sửa thông tin</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_PATH ?>/index.php?controller=auth&action=updateProfile" method="POST" enctype="multipart/form-data">
                    <!-- Username (Read-only) -->
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Tên đăng nhập</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-at text-muted"></i></span>
                            <input type="text" class="form-control bg-light" id="username" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                        </div>
                        <div class="form-text"><i class="fas fa-lock me-1" style="font-size: 0.7rem;"></i>Tên đăng nhập không thể thay đổi.</div>
                    </div>

                    <!-- Họ và tên -->
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                value="<?= htmlspecialchars($user['full_name']) ?>" required maxlength="100" placeholder="Nhập họ và tên">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($user['email']) ?>" required maxlength="100" placeholder="Nhập email">
                        </div>
                    </div>

                    <!-- Ảnh đại diện (Avatar) -->
                    <div class="mb-4">
                        <label for="avatar_file" class="form-label fw-semibold">Ảnh đại diện (Avatar)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-image text-muted"></i></span>
                            <input type="file" class="form-control" id="avatar_file" name="avatar_file" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="form-text"><i class="fas fa-info-circle me-1" style="font-size: 0.7rem;"></i>Hỗ trợ các định dạng: JPG, JPEG, PNG, WEBP. Dung lượng tối đa 2MB.</div>
                    </div>

                    <hr class="my-4">

                    <!-- Đổi mật khẩu (tùy chọn) -->
                    <h6 class="fw-bold mb-3"> Đổi mật khẩu <span class="text-muted fw-normal" style="font-size: 0.8rem;">(để trống nếu không muốn đổi)</span></h6>

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Mật khẩu hiện tại</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Nhập mật khẩu hiện tại">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="new_password" class="form-label fw-semibold">Mật khẩu mới</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Tối thiểu 6 ký tự" minlength="6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label fw-semibold">Xác nhận mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-check-double text-muted"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới">
                            </div>
                        </div>
                    </div>

                    <!-- Nút Submit -->
                    <div class="d-flex justify-content-end">
                        <a href="<?= BASE_PATH ?>/index.php?controller=dashboard&action=<?= htmlspecialchars($roleName) ?>" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i>Hủy
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-save me-1"></i>Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('avatar_file').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileAvatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>