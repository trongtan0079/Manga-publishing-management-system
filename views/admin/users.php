<?php include __DIR__ . '/../layouts/header.php'; ?>

<!-- Tiêu đề trang và Nút thêm mới -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Users</h2>
    <a href="/index.php?controller=user&action=create" class="btn btn-primary">Create New User</a>
</div>

<!-- Bảng hiển thị danh sách người dùng -->
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Kiểm tra xem có người dùng nào không -->
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        
                        <!-- Hiển thị tên Role -->
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($user['role_name'] ?? 'N/A') ?></span></td>
                        
                        <!-- Hiển thị Trạng thái (Status) với các màu sắc khác nhau -->
                        <td>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php elseif ($user['status'] === 'inactive'): ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Banned</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Các nút thao tác: Xem, Sửa, Xóa -->
                        <td>
                            <a href="/index.php?controller=user&action=show&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-info">View</a>
                            <a href="/index.php?controller=user&action=edit&id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            
                            <!-- Form xóa người dùng cần sử dụng phương thức POST -->
                            <form action="/index.php?controller=user&action=delete&id=<?= $user['user_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Trường hợp không có dữ liệu -->
                <tr>
                    <td colspan="7" class="text-center">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
