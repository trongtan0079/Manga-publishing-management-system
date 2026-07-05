<?php 
/**
 * View: Giao diện quản lý danh sách người dùng (users.php)
 * Vai trò: Admin (Quản trị viên)
 * Chức năng: Hiển thị danh sách tất cả các tài khoản người dùng trong hệ thống kèm các chức năng tìm kiếm, sửa, xóa, xem chi tiết.
 * 
 * @var array $users Danh sách thông tin tài khoản người dùng được truyền từ UserController
 * @var int $totalPages Tổng số trang phân trang
 * @var int $page Trang hiện tại
 * @var string $search Từ khóa tìm kiếm
 * @var string $status Trạng thái lọc người dùng
 * @var int $totalUsers Tổng số người dùng
 */
$pageTitle = 'Quản lý Người dùng';
$current_page = 'users';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Tiêu đề trang và Nút thêm mới -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-dark fw-bold d-inline-block align-middle">Quản lý Người dùng</h2>
        <?php if (!empty($status)): ?>
            <span class="badge bg-info text-dark ms-2 align-middle" style="font-size: 0.8rem; padding: 0.35em 0.65em;">
                Đang lọc: <?= $status === 'active' ? 'Hoạt động' : ($status === 'inactive' ? 'Tạm khóa' : 'Bị đình chỉ') ?> (<?= $totalUsers ?>)
            </span>
            <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-sm btn-outline-secondary ms-2 align-middle py-1 px-2" style="font-size: 0.72rem;">
                <i class="fas fa-times me-1"></i>Xóa lọc trạng thái
            </a>
        <?php endif; ?>
        <?php if (!empty($search)): ?>
            <span class="badge bg-light text-dark border ms-2 align-middle" style="font-size: 0.8rem; padding: 0.35em 0.65em;">
                Tìm kiếm: "<?= htmlspecialchars($search) ?>"
            </span>
            <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index<?= !empty($status) ? '&status=' . urlencode($status) : '' ?>" class="btn btn-sm btn-outline-secondary ms-2 align-middle py-1 px-2" style="font-size: 0.72rem;">
                <i class="fas fa-times me-1"></i>Hủy tìm kiếm
            </a>
        <?php endif; ?>
    </div>
    <a href="<?= BASE_PATH ?>/index.php?controller=user&action=create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Thêm Người dùng</a>
</div>

<!-- Bảng hiển thị danh sách người dùng -->
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

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white border-bottom pt-3 pb-3">
        <form action="<?= BASE_PATH ?>/index.php" method="GET" class="row g-3 align-items-center">
            <input type="hidden" name="controller" value="user">
            <input type="hidden" name="action" value="index">
            <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
            
            <div class="col-12 col-md-7 col-lg-8">
                <div class="position-relative">
                    <i class="fas fa-search text-muted position-absolute top-50 translate-middle-y start-0 ms-3" style="pointer-events: none;"></i>
                    <input type="text" class="form-control ps-5 bg-light-subtle" name="search" placeholder="Tìm kiếm theo Tài khoản, Họ tên hoặc Email..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-12 col-md-5 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 text-nowrap"><i class="fas fa-search me-2"></i>Tìm kiếm</button>
                <a href="<?= BASE_PATH ?>/index.php?controller=user&action=index" class="btn btn-secondary flex-grow-1 text-nowrap">Đặt lại</a>
            </div>
        </form>
    </div>
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
                            <tr data-status="<?= htmlspecialchars($user['status']) ?>">
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
                                <td>
                                    <div class="d-flex justify-content-end gap-1 pe-4">
                                        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=show&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="<?= BASE_PATH ?>/index.php?controller=user&action=edit&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-warning text-dark" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <!-- Form xóa người dùng cần sử dụng phương thức POST -->
                                        <form action="<?= BASE_PATH ?>/index.php?controller=user&action=delete&id=<?= $user['user_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không? Hành động này không thể hoàn tác.');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa Người dùng">
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
    <!-- Phần phân trang Backend -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white border-top py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <!-- Nút trang trước -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=user&action=index&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Các số trang -->
                    <?php 
                    // Hiển thị tối đa 5 trang xung quanh trang hiện tại để tránh bị tràn
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    if ($startPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . BASE_PATH . '/index.php?controller=user&action=index&page=1' . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($status) ? '&status=' . urlencode($status) : '') . '">1</a></li>';
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    for ($i = $startPage; $i <= $endPage; $i++): 
                    ?>
                        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=user&action=index&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; 
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . BASE_PATH . '/index.php?controller=user&action=index&page=' . $totalPages . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($status) ? '&status=' . urlencode($status) : '') . '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    
                    <!-- Nút trang sau -->
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= BASE_PATH ?>/index.php?controller=user&action=index&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
