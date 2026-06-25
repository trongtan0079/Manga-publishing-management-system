<?php 
/**
 * @var array $users 
 */
$pageTitle = 'Quản lý Người dùng';
$current_page = 'users';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Tiêu đề trang và Nút thêm mới -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-dark fw-bold">Quản lý Người dùng</h2>
    <a href="/index.php?controller=user&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Thêm Người dùng</a>
</div>

<!-- Bảng hiển thị danh sách người dùng -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Tài khoản</th>
                        <th>Họ và Tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Kiểm tra xem có người dùng nào không -->
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-4"><?= htmlspecialchars($user['user_id']) ?></td>
                                <td><span class="fw-bold"><?= htmlspecialchars($user['username']) ?></span></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                
                                <!-- Hiển thị tên Role -->
                                <td><span class="badge bg-info text-dark"><?= ucfirst(htmlspecialchars($user['role_name'] ?? 'N/A')) ?></span></td>
                                
                                <!-- Hiển thị Trạng thái (Status) với các màu sắc khác nhau -->
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php elseif ($user['status'] === 'inactive'): ?>
                                        <span class="badge bg-secondary">Tạm khóa</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Cấm (Banned)</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Các nút thao tác: Xem, Sửa, Xóa -->
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="/index.php?controller=user&action=show&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="/index.php?controller=user&action=edit&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning text-dark" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <!-- Form xóa người dùng cần sử dụng phương thức POST -->
                                        <form action="/index.php?controller=user&action=delete&id=<?= $user['user_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không? Hành động này không thể hoàn tác.');">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa Người dùng">
                                                <i class="fas fa-trash-alt"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Trường hợp không có dữ liệu -->
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <div class="mb-3"><i class="fas fa-users-slash fa-3x text-light"></i></div>
                                <p class="mb-0">Chưa có người dùng nào. Nhấn <strong>"Thêm Người dùng"</strong> để tạo mới!</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
